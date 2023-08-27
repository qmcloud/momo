<?php

namespace PhpZip\Constants;

/**
 * Class ZipPlatform.
 */
final class ZipPlatform
{
    /** @var int MS-DOS OS */
    const OS_DOS = 0;

    /** @var int Unix OS */
    const OS_UNIX = 3;

    /** MacOS platform */
    const OS_MAC_OSX = 19;

    /** @var array Zip Platforms */
    private static $platforms = [
        self::OS_DOS => 'MS-DOS',
        1 => 'Amiga',
        2 => 'OpenVMS',
        self::OS_UNIX => 'Unix',
        4 => 'VM/CMS',
        5 => 'Atari ST',
        6 => 'HPFS (OS/2, NT 3.x)',
        7 => 'Macintosh',
        8 => 'Z-System',
        9 => 'CP/M',
        10 => 'Windows NTFS or TOPS-20',
        11 => 'MVS or NTFS',
        12 => 'VSE or SMS/QDOS',
        13 => 'Acorn RISC OS',
        14 => 'VFAT',
        15 => 'alternate MVS',
        16 => 'BeOS',
        17 => 'Tandem',
        18 => 'OS/400',
        self::OS_MAC_OSX => 'OS/X (Darwin)',
        30 => 'AtheOS/Syllable',
    ];

    /**
     * @param int $platform
     *
     * @return string
     */
    public static function getPlatformName($platform)
    {
        return isset(self::$platforms[$platform]) ? self::$platforms[$platform] : 'Unknown';
    }
}
