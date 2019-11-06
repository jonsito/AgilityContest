#!/usr/bin/php -f
<?php

define ("PRIVATE_KEY","/etc/AgilityContest/AgilityContest.key");
define ("PUBLIC_KEY","/etc/AgilityContest/AgilityContest_puk.pem");
define ("LICENSES","/etc/AgilityContest/Licencias.txt");
define ("LOGOS","/var/www/html/AgilityContest/agility/images/logos");

class SymmetricCipher {

    const HASH_ALGO = 'sha256';
    const METHOD = 'aes-256-ctr';

    /**
     * Encrypts (but does not authenticate) a message
     *
     * @param string $message - plaintext message
     * @param string $key - encryption key (raw binary expected)
     * @param boolean $encode - set to TRUE to return a base64-encoded
     * @return string (raw binary)
     */
    public static function unsecure_encrypt($message, $key, $encode = false)  {
        $nonceSize = openssl_cipher_iv_length(self::METHOD);
        $nonce = openssl_random_pseudo_bytes($nonceSize);
        $ciphertext = openssl_encrypt( $message, self::METHOD, $key,OPENSSL_RAW_DATA, $nonce );
        // Now let's pack the IV and the ciphertext together
        // Naively, we can just concatenate
        if ($encode) { return base64_encode($nonce.$ciphertext); }
        return $nonce.$ciphertext;
    }

    /**
     * Decrypts (but does not verify) a message
     *
     * @param string $message - ciphertext message
     * @param string $key - encryption key (raw binary expected)
     * @param boolean $encoded - are we expecting an encoded string?
     * @return string
     * @throws Exception
     */
    public static function unsecure_decrypt($message, $key, $encoded = false) {
        if ($encoded) {
            $message = base64_decode($message, true);
            if ($message === false) { throw new Exception('Encryption failure'); }
        }
        $nonceSize = openssl_cipher_iv_length(self::METHOD);
        $nonce = mb_substr($message, 0, $nonceSize, '8bit');
        $ciphertext = mb_substr($message, $nonceSize, null, '8bit');
        $plaintext = openssl_decrypt( $ciphertext,self::METHOD, $key,OPENSSL_RAW_DATA, $nonce );
        return $plaintext;
    }

    /**
     * Encrypts then MACs a message
     *
     * @param string $message - plaintext message
     * @param string $key - encryption key (raw binary expected)
     * @param boolean $encode - set to TRUE to return a base64-encoded string
     * @return string (raw binary)
     */
    public static function encrypt($message, $key, $encode = false) {
        list($encKey, $authKey) = self::splitKeys($key);
        // Pass to UnsafeCrypto::encrypt
        $ciphertext = self::unsecure_encrypt($message, $encKey);
        // Calculate a MAC of the IV and ciphertext
        $mac = hash_hmac(self::HASH_ALGO, $ciphertext, $authKey, true);
        if ($encode) { return base64_encode($mac.$ciphertext); }
        // Prepend MAC to the ciphertext and return to caller
        return $mac.$ciphertext;
    }

    /**
     * Decrypts a message (after verifying integrity)
     *
     * @param string $message - ciphertext message
     * @param string $key - encryption key (raw binary expected)
     * @param boolean $encoded - are we expecting an encoded string?
     * @return string (raw binary)
     * @throws Exception
     */
    public static function decrypt($message, $key, $encoded = false) {
        list($encKey, $authKey) = self::splitKeys($key);
        if ($encoded) {
            $message = base64_decode($message, true);
            if ($message === false) { throw new Exception('Decryption failure: base64_decode'); }
        }
        // Hash Size -- in case HASH_ALGO is changed
        $hs = mb_strlen(hash(self::HASH_ALGO, '', true), '8bit');
        $mac = mb_substr($message, 0, $hs, '8bit');
        $ciphertext = mb_substr($message, $hs, null, '8bit');
        $calculated = hash_hmac(self::HASH_ALGO, $ciphertext, $authKey,true );
        if (!self::hashEquals($mac, $calculated)) { throw new Exception('Decryption failure: hash missmatch'); }
        // Pass to UnsafeCrypto::decrypt
        $plaintext = self::unsecure_decrypt($ciphertext, $encKey);
        return $plaintext;
    }

    /**
     * Splits a key into two separate keys; one for encryption
     * and the other for authenticaiton
     *
     * @param string $masterKey (raw binary)
     * @return array (two raw binary strings)
     */
    protected static function splitKeys($masterKey)  {
        // You really want to implement HKDF here instead!
        return [
            hash_hmac(self::HASH_ALGO, 'ENCRYPTION', $masterKey, true),
            hash_hmac(self::HASH_ALGO, 'AUTHENTICATION', $masterKey, true)
        ];
    }

    /**
     * Compare two strings without leaking timing information
     *
     * @param string $a
     * @param string $b
     * @ref https://paragonie.com/b/WS1DLx6BnpsdaVQW
     * @return boolean
     */
    protected static function hashEquals($a, $b) {
        if (function_exists('hash_equals')) {
            return hash_equals($a, $b);
        }
        $nonce = openssl_random_pseudo_bytes(32);
        return hash_hmac(self::HASH_ALGO, $a, $nonce) === hash_hmac(self::HASH_ALGO, $b, $nonce);
    }
}

class Cipher {

	// a note on RSA encryption:
	// maximun data leng is fixed by RSA sign process to
	// (numberofbits/8) - 11 ( when padding is used
	// we are using 8192 bits RSA KEY, so have a maximum of 1013 bytes ( 1368 when base64 encoded )
	// so divide original data in chunks of 1000bytes before encoding
	// @See: https://www.php.net/manual/es/function.openssl-private-encrypt.php
	
	//Block size for encryption block cipher
	private $ENCRYPT_BLOCK_SIZE = 1000;// this for 8192 bit key for example, leaving some room
	//Block size for decryption block cipher
	private $DECRYPT_BLOCK_SIZE = 1024;// this again for 8192 bit key

    /**
     * @param $data data to encrypt
     * @param $privKeyFile private key file
     * @param string $uniqueID 32 bytes raw (not base64 encoded) symmetric key
     * @param string $serial license serial number
     * @return string|null
     */
	function encrypt($data,$privKeyFile,$uniqueID="",$serial="") {
		$fp=fopen ($privKeyFile,"rb"); $priv_key=fread ($fp,8192); fclose($fp);
		$key=openssl_get_privatekey($priv_key);
		if (!$key)	logAndDie("encrypt({$serial}): Cannot load private key");

		// perform rsa encryption
		$text="";
		$chunks=str_split($data, $this->ENCRYPT_BLOCK_SIZE);
		foreach($chunks as $chunk) {
			$partialEncrypted = '';
			openssl_private_encrypt($chunk,$partialEncrypted,$key,OPENSSL_PKCS1_PADDING);
			if (empty($partialEncrypted)){
				openssl_free_key($key);
                logAndDie("openssl_private_encrypt({$serial}) error");
			}
		    $text.=$partialEncrypted;
		}
		openssl_free_key($key);

		// now perform symetric encryption with provided uniqueID and return base64 encoded text;
        $result=null;
        if ($uniqueID!=="") {
            $result=SymmetricCipher::encrypt($text,$uniqueID,true);
        } else {
            $result=base64_encode($text);
        }
        if (!$result) die("SymmetricCipher::encrypt({$serial}) error");
        return $result;
	}

    /**
     * @param $data encoded data
     * @param string $uniqueID 32 bytes raw key ( not base64 encoded )
     * @param string $serial License serial number
     * @return string
     */
	function decrypt($data,$uniqueID="",$serial="") {

	    // perform symmetric decryption if uniqueID is not null
        $text=$data;
        if ($uniqueID!=="") {
            try {
                $text=SymmetricCipher::decrypt($data,$uniqueID,true); // data is base64 encoded
                $text=base64_encode($text);
            } catch (Exception $e) {
                syslog(LOG_ERR, $e->getMessage());
                logAndDie("SymmetricCipher::decrypt({$serial}) error");
            }
        }

		// load rsa public key
		$fp=fopen (PUBLIC_KEY,"rb"); $pub_key=fread ($fp,8192); fclose($fp);
		$key=openssl_get_publickey($pub_key);
		if (!$key) echo "decrypt({$serial}): Cannot get public key";

        // divide data in chunks
		$chunks = str_split(base64_decode($text), $this->DECRYPT_BLOCK_SIZE);
		// decrypt data
		$decrypted="";
		foreach($chunks as $chunk) {
			$partial = '';
			//be sure to match padding
			$decryptionOK = openssl_public_decrypt($chunk, $partial, $key, OPENSSL_PKCS1_PADDING);
			if($decryptionOK === false){//here also processed errors in decryption. If too big this will be false
				openssl_free_key($key);
                logAndDie("RSA decrypt({$serial}) failed ".openssl_error_string().PHP_EOL);
			}
			$decrypted .= $partial;
		}
		openssl_free_key($key);
		return $decrypted;
		// echo "Decrypted Data: ".var_dump(json_decode($decrypted,true));
	}
}

// compose logo file name based in club name, instead (old) club ID
function composeLogoName($name) {
        // Remove all (back)slashes from name
        $logo = str_replace('\\', '', $name);
        $logo = str_replace('/', '', $logo);
        // convert to lowercase
        // Remove all characters that are not the separator, a-z, 0-9, or whitespace
        $logo = preg_replace('![^'.preg_quote('-').'a-z0-_9\s]+!', '', strtolower($logo));
        // Replace all separator characters and whitespace by a single separator
        $logo = preg_replace('!['.preg_quote('-').'\s]+!u', '_', $logo);
        $logo="$logo.png";
        fwrite(STDERR,"Looking for logo {$logo}".PHP_EOL);
        return $logo;
}

function showLogo($data) {
    $img=imagecreatefromstring(base64_decode($data));
    imagepng($img,"/tmp/kk.png");
    system("eog /tmp/kk.png");
}

function logAndDie($msg) {
    syslog(LOG_ERR,$msg);
    die($msg);
}

// activate logging
openlog("AgilityContest", LOG_PID | LOG_PERROR, LOG_LOCAL0);
// invocation: getLicense email uniqueID activationKey
if ($argc == 5) { // encrypt
    $serial = $argv[1]; // who is requesting the license
    $email= $argv[2];
    $uniqueID = base64_decode($argv[3],true);
    $activationKey = $argv[4];
    syslog(LOG_INFO,"License request: serial:{$serial} email:{$email} ID:{$argv[3]} AK:{$activationKey}");
    // read license file
    $licenses = file(LICENSES,FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if (!$licenses) logAndDie("Cannot open licenses file");

    // PENDING: track and log request

    // iterate on each line until license found
    $diemsg="No license found for {$email}";
    foreach ( $licenses as $lic) {
        $data=json_decode($lic,true);
        if ($data['email']!==$email) continue;
        if ($data['status']==="cancelled") {
		$diemsg="License for {email} is cancelled";
		continue;
	}
        if ( strcmp( $data['expires'] , date("Ymd") ) <0 ) {
		$diemsg="License for {$email} is expired";
		continue;
	}
        if ($data['activationkey']!==$activationKey) {
		$diemsg="Activation key for {$email} does not match";
		continue;
	}

        // license is ok. prepare it
        unset($data['status']);
        unset($data['activationkey']);
	// add logo
	if ($data['image']!=='') $logo=$data['image'];
	else $logo=composeLogoName($data['club']);
        if(!file_exists(LOGOS."/{$logo}")) $logo="agilitycontest.png"; // check for file not found
        $data['image']=base64_encode(file_get_contents(LOGOS."/{$logo}"));

        // ok. now ready to crypt
        $cipher=new Cipher();
        $result=$cipher->encrypt(json_encode($data),PRIVATE_KEY,$uniqueID,$data['serial']);
        if (!$result) logAndDie("License generation failed");

        // now try to decrypt to make sure data is ok
        $decrypted=$cipher->decrypt($result,$uniqueID,$serial);
        if (!$decrypted) logAndDie("Crypt(): Cannot unencrypt resulting data");
        $ddata=json_decode($decrypted,true);
        if (!is_array($ddata)) logAndDie("Crypt(): unencrypted data has novalid license contents");

        // fine. so echo result
        echo $result;
        return 0;
    }
    // arriving here means license not found
    logAndDie($diemsg);

} elseif ($argc == 3) { // decrypt
    $uniqueID=base64_decode($argv[1],true);
    $file=$argv[2];
    // load base64 encoded encrypted file
    $fp=fopen ($file,"rb");
    $data=""; while (!feof($fp)) { $data .= fread($fp, 8192); };
    fclose($fp);
    // ok. now ready to crypt
    $cipher=new Cipher();
    $result=$cipher->decrypt($data,$uniqueID,"");
    if (!$result) logAndDie("License decryption failed");
    $data=json_decode($result,true);
    if (!is_array($data)) logAndDie("Invalid license contents");
    showLogo($data['image']);
    return 0;
} else {
    fwrite(STDERR,"Usage: ".PHP_EOL);
    fwrite(STDERR,"    (encrypt) {$argv[0]} serial email uniqueID activationKey".PHP_EOL);
    fwrite(STDERR,"    (decrypt) {$argv[0]} uniqueID encfile".PHP_EOL);
    fwrite(STDERR,"Use '' for uniqueID when not used".PHP_EOL);
}


?>
