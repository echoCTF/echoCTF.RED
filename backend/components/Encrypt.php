<?php

namespace app\components;

use Yii;

/**
 * Handle Flag related operations
 * @method encrypt
 */
class Flag
{
  // Helper function to encrypt the code
  public static function encrypt($code, $secretKey)
  {
    // Encrypt the data with the secret key using AES
    $encrypted = openssl_encrypt($code, 'AES-128-ECB', $secretKey, OPENSSL_RAW_DATA);

    // Generate MD5 hash of the hexadecimal representation of the encrypted data
    return md5(strtoupper(bin2hex($encrypted)));
  }
}
