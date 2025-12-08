<?php

namespace App\Helpers;

class AuthEncryptionHelper
{
    /**
     * Decrypt AES-256-CBC encrypted data (matching Drupal's decrypt function)
     */
    public static function decrypt($data)
    {
        if (empty($data)) {
            return '';
        }

        $encryptedString = base64_decode($data);
        $cipher = 'aes-256-cbc';
        $secret = config('services.hrms.secret', '');
        $iv = config('services.hrms.iv', '');

        if (empty($secret) || empty($iv)) {
            throw new \Exception('HRMS encryption secret or IV not configured');
        }

        $decryptedString = openssl_decrypt($encryptedString, $cipher, $secret, OPENSSL_RAW_DATA, $iv);

        return $decryptedString;
    }

    /**
     * Validate checksum using HMAC-SHA256 (matching Drupal's checksum_validation function)
     */
    public static function checksumValidation($data)
    {
        $hmacSecret = config('services.hrms.hmac_secret', '');
        
        if (empty($hmacSecret)) {
            throw new \Exception('HMAC secret not configured');
        }

        return hash_hmac('sha256', mb_convert_encoding($data, "UTF-8"), $hmacSecret);
    }
}

