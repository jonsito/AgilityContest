#!/usr/bin/php -f
<?php

define ("PRIVATE_KEY","/etc/AgilityContest/AgilityContest.key");
define ("PUBLIC_KEY","/etc/AgilityContest/AgilityContest_puk.pem");
define ("LICENSES","/etc/AgilityContest/Licencias.txt");
define ("BLACKLIST","/etc/AgilityContest/blacklist.txt");
define ("LOGOS","/var/www/html/AgilityContest/agility/images/logos");

require_once("/var/www/html/AgilityContest/agility/server/auth/SymmetricCipher.php");

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
if ($argc == 1) {  // generate black list
    // read black list from file. data is json encoded
    $data=file(BLACKLIST,FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $bl=array();
    foreach ($data as $item) $bl[]=json_decode($item,true);
    // ok. now ready to crypt
    $cipher=new Cipher();
    $result=$cipher->encrypt(json_encode($data),PRIVATE_KEY,"","00000000");
    if (!$result) logAndDie("BlackList encryption failed");

    // now try to decrypt to make sure data is ok
    $decrypted=$cipher->decrypt($result,"","00000000");
    if (!$decrypted) logAndDie("Crypt(): Cannot unencrypt blacklist data");
    $ddata=json_decode($decrypted,true);
    if (!is_array($ddata)) logAndDie("Crypt(): unencrypted data has invalid blacklist contents");

    // fine. so echo result
    echo $result;
    return 0;
}

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
        if (!is_array($ddata)) logAndDie("Crypt(): unencrypted data has no valid license contents");

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
