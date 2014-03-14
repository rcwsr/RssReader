<?php

namespace Rss;

/**
 * Class Crypto
 *
 * This is a class I previously created for a university assignment. It allows for encrypting and decrypting of a string
 *
 * @package Rss
 */
class Crypto
{
    private $iv;
    private $secret;

    /**
     * Constructs the crypto class.
     * @param $secret Pre-defined secret string. Only use one per app.
     */
    public function __construct($secret)
    {
        $this->secret = $secret;
        $iv_size = mcrypt_get_iv_size(MCRYPT_TWOFISH, MCRYPT_MODE_ECB);
        $this->iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
    }

    /**
     * Encrypts a string and returns in base64.
     * @param $string String to encrypt.
     * @return string Encrypted base64 string.
     */
    public function encrypt($string)
    {
        $encrypted_string = mcrypt_encrypt(MCRYPT_TWOFISH, $this->secret, $string, MCRYPT_MODE_ECB, $this->iv);
        $base64_string = base64_encode($encrypted_string);
        return trim($base64_string);
    }

    /**
     * Decrypts a base64 string.
     * @param $string Base64 string to decrypt.
     * @return string Decrypted string.
     */
    public function decrypt($string)
    {
        $string = base64_decode($string);
        $decrypted_string = mcrypt_decrypt(MCRYPT_TWOFISH, $this->secret, $string, MCRYPT_MODE_ECB, $this->iv);
        return trim($decrypted_string);
    }
}