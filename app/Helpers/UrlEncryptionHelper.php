<?php

namespace App\Helpers;

class UrlEncryptionHelper
{
    private static $key = "MAL_979805";

    /**
     * Encrypt URL parameter (matching Drupal's encrypt_url function)
     */
    public static function encryptUrl($string)
    {
        if (empty($string)) {
            return '';
        }

        $key = self::$key;
        $result = '';
        
        for ($i = 0; $i < strlen($string); $i++) {
            $char = substr($string, $i, 1);
            $keychar = substr($key, ($i % strlen($key)) - 1, 1);
            $char = chr(ord($char) + ord($keychar));
            $result .= $char;
        }

        return urlencode(base64_encode($result));
    }

    /**
     * Decrypt URL parameter (matching Drupal's decrypt_url function)
     * @param string $string The encrypted string (can be URL-encoded or already decoded)
     * @param bool $isUrlEncoded Whether the string is URL-encoded (default: true)
     */
    public static function decryptUrl($string, $isUrlEncoded = true)
    {
        if (empty($string)) {
            return '';
        }

        $key = self::$key;
        $result = '';
        
        // Match Drupal: base64_decode(urldecode($string))
        // Use rawurldecode() to preserve + characters (doesn't convert + to space like urldecode() does)
        if ($isUrlEncoded) {
            $string = base64_decode(rawurldecode($string));
        } else {
            // If already decoded by Laravel, we still need to handle + properly
            // Re-encode + as %2B then use rawurldecode to get it back as +
            $string = str_replace('+', '%2B', $string);
            $string = base64_decode(rawurldecode($string));
        }

        for ($i = 0; $i < strlen($string); $i++) {
            $char = substr($string, $i, 1);
            $keychar = substr($key, ($i % strlen($key)) - 1, 1);
            $char = chr(ord($char) - ord($keychar));
            $result .= $char;
        }

        return $result;
    }
}

