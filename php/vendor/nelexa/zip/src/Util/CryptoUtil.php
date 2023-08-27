<?php

namespace PhpZip\Util;

use PhpZip\Exception\RuntimeException;

/**
 * Crypto Utils.
 *
 * @internal
 */
class CryptoUtil
{
    /**
     * Returns random bytes.
     *
     * @param int $length
     *
     * @throws \Exception
     *
     * @return string
     *
     * @deprecated Use random_bytes()
     */
    final public static function randomBytes($length)
    {
        return random_bytes($length);
    }

    /**
     * Decrypt AES-CTR.
     *
     * @param string $data Encrypted data
     * @param string $key  Aes key
     * @param string $iv   Aes IV
     *
     * @return string Raw data
     */
    public static function decryptAesCtr($data, $key, $iv)
    {
        if (\extension_loaded('openssl')) {
            $numBits = \strlen($key) * 8;
            /** @noinspection PhpComposerExtensionStubsInspection */
            return openssl_decrypt($data, 'AES-' . $numBits . '-CTR', $key, \OPENSSL_RAW_DATA, $iv);
        }

        if (\extension_loaded('mcrypt')) {
            return mcrypt_decrypt(\MCRYPT_RIJNDAEL_128, $key, $data, 'ctr', $iv);
        }

        throw new RuntimeException('Extension openssl or mcrypt not loaded');
    }

    /**
     * Encrypt AES-CTR.
     *
     * @param string $data Raw data
     * @param string $key  Aes key
     * @param string $iv   Aes IV
     *
     * @return string Encrypted data
     */
    public static function encryptAesCtr($data, $key, $iv)
    {
        if (\extension_loaded('openssl')) {
            $numBits = \strlen($key) * 8;
            /** @noinspection PhpComposerExtensionStubsInspection */
            return openssl_encrypt($data, 'AES-' . $numBits . '-CTR', $key, \OPENSSL_RAW_DATA, $iv);
        }

        if (\extension_loaded('mcrypt')) {
            return mcrypt_encrypt(\MCRYPT_RIJNDAEL_128, $key, $data, 'ctr', $iv);
        }

        throw new RuntimeException('Extension openssl or mcrypt not loaded');
    }
}
