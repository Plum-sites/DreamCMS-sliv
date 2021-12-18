<?php

namespace App;

use Config;

class SCEncryptor {
    public static function decryptString($val){
        return self::aes_decrypt($val, self::getAescryptKey());
    }

    public static function encryptString($val){
        return self::aes_encrypt($val, self::getAescryptKey());
    }

    protected static function aes_encrypt($val, $key)
    {
        return openssl_encrypt($val, 'aes-128-ecb', $key, 0);
    }

    protected static function aes_decrypt($val, $key)
    {
        return openssl_decrypt($val, 'aes-128-ecb', $key, 0);
    }

    public static function getAescryptKey()
    {
        if(!Config::has('app.aeskey'))
            throw new \Exception('The .env value AES_KEY has to be set!');

        return substr(hash('sha256', Config::get('app.aeskey')), 0, 16);
    }
}