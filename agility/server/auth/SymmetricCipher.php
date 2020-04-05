<?php
/**
 * SymmetricCipher
 * Created by PhpStorm.
 * User: jantonio
 * Date: 9/03/18
 * Time: 17:37
 *
 * Simple class to handle symmetric data encryption/decryption
 * https://stackoverflow.com/questions/9262109/simplest-two-way-encryption-using-php
 */
class SymmetricCipher {

    const HASH_ALGO = 'sha256';
    const METHOD = 'aes-256-cbc';

    /**
     * Encrypts (but does not authenticate) a message
     *
     * @param string $message - plaintext message
     * @param string $key - encryption key (raw binary expected)
     * @param boolean $encode - set to TRUE to return a base64-encoded
     * @return string (raw binary)
     */
    public static function unsecure_encrypt($message, $key)  {
        $nonceSize = openssl_cipher_iv_length(self::METHOD);
        $iv = openssl_random_pseudo_bytes($nonceSize); // create initialization vector
        $ciphertext = openssl_encrypt( $message, self::METHOD, $key,OPENSSL_RAW_DATA, $iv );
        // Now let's pack the IV and the ciphertext together
        // Naively, we can just concatenate
        return $iv.$ciphertext;
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
        // a partir de la clave simetrica, genero dos claves: encriptacion y firma
        list($encKey, $authKey) = self::splitKeys($key);
        // Pass to UnsafeCrypto::encrypt, that returns IV+EncryptedMsg
        $ciphertext = self::unsecure_encrypt($message, $encKey); // encripto con la clave de encriptacion
        // Calculate a MAC of the IV and ciphertext
        $mac = hash_hmac(self::HASH_ALGO, $ciphertext, $authKey, true); // genero la firma
        // Prepend MAC to the ciphertext and return $mac.$iv.$ciphertext to caller
        if ($encode) { return base64_encode($mac.$ciphertext); }
        return $mac.$ciphertext;
    }

    /**
     * Decrypts (but does not verify) a message
     *
     * @param string $message - ciphertext message
     * @param string $key - encryption key (raw binary expected)
     * @return string
     * @throws Exception
     */
    public static function unsecure_decrypt($message, $key) {
        // retrieve initialization vector.
        $ivSize = openssl_cipher_iv_length(self::METHOD);
        $iv = mb_substr($message, 0, $ivSize, '8bit');
        $ciphertext = mb_substr($message, $ivSize, null, '8bit');
        $plaintext = openssl_decrypt( $ciphertext,self::METHOD, $key,OPENSSL_RAW_DATA, $iv );
        return $plaintext;
    }

    /**
     * Decrypts a message (after verifying integrity)
     *
     * @param string $message - ciphertext message (mac.iv.ciphertex)
     * @param string $key - encryption key (raw binary expected)
     * @param boolean $encoded - are we expecting a base64 encoded string?
     * @return string (raw binary)
     * @throws Exception
     */
    public static function decrypt($message, $key, $encoded = false) {
        if ($encoded) { // en cao necesario hacemos un base64_decode
            $message = base64_decode($message, true);
            if ($message === false) { throw new Exception('Decryption failure: base64_decode'); }
        }
        // a partir de la clave simetrica, generamos dos claves: encriptacion y firma
        list($encKey, $authKey) = self::splitKeys($key);
        // evaluate length and extract mac hash from message
        $hs = mb_strlen(hash(self::HASH_ALGO, '', true), '8bit'); // get hash size
        $mac = mb_substr($message, 0, $hs, '8bit'); // mac
        $ciphertext = mb_substr($message, $hs, null, '8bit'); // get iv.ciphertext
        // eval message hash and compare against provided
        $calculated = hash_hmac(self::HASH_ALGO, $ciphertext, $authKey,true );
        if (!self::hashEquals($mac, $calculated)) { throw new Exception('Decryption failure: check hash'); }
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