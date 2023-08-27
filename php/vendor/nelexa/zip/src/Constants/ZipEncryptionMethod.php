<?php

namespace PhpZip\Constants;

use PhpZip\Exception\InvalidArgumentException;

/**
 * Class ZipEncryptionMethod.
 */
final class ZipEncryptionMethod
{
    const NONE = -1;

    /** @var int Traditional PKWARE encryption. */
    const PKWARE = 0;

    /** @var int WinZip AES-256 */
    const WINZIP_AES_256 = 1;

    /** @var int WinZip AES-128 */
    const WINZIP_AES_128 = 2;

    /** @var int WinZip AES-192 */
    const WINZIP_AES_192 = 3;

    /** @var array<int, string> */
    private static $ENCRYPTION_METHODS = [
        self::NONE => 'no encryption',
        self::PKWARE => 'Traditional PKWARE encryption',
        self::WINZIP_AES_128 => 'WinZip AES-128',
        self::WINZIP_AES_192 => 'WinZip AES-192',
        self::WINZIP_AES_256 => 'WinZip AES-256',
    ];

    /**
     * @param int $value
     *
     * @return string
     */
    public static function getEncryptionMethodName($value)
    {
        $value = (int) $value;

        return isset(self::$ENCRYPTION_METHODS[$value]) ?
            self::$ENCRYPTION_METHODS[$value] :
            'Unknown Encryption Method';
    }

    /**
     * @param int $encryptionMethod
     *
     * @return bool
     */
    public static function hasEncryptionMethod($encryptionMethod)
    {
        return isset(self::$ENCRYPTION_METHODS[$encryptionMethod]);
    }

    /**
     * @param int $encryptionMethod
     *
     * @return bool
     */
    public static function isWinZipAesMethod($encryptionMethod)
    {
        return \in_array(
            (int) $encryptionMethod,
            [
                self::WINZIP_AES_256,
                self::WINZIP_AES_192,
                self::WINZIP_AES_128,
            ],
            true
        );
    }

    /**
     * @param int $encryptionMethod
     *
     * @throws InvalidArgumentException
     */
    public static function checkSupport($encryptionMethod)
    {
        $encryptionMethod = (int) $encryptionMethod;

        if (!self::hasEncryptionMethod($encryptionMethod)) {
            throw new InvalidArgumentException(sprintf(
                'Encryption method %d is not supported.',
                $encryptionMethod
            ));
        }
    }
}
