<?php

namespace PhpZip\Constants;

/**
 * Compression levels for Deflate and BZIP2.
 *
 * {@see https://pkware.cachefly.net/webdocs/casestudies/APPNOTE.TXT} Section 4.4.4:
 *
 * For Methods 8 and 9 - Deflating
 * -------------------------------
 * Bit 2  Bit 1
 * 0      0    Normal (-en) compression option was used.
 * 0      1    Maximum (-exx/-ex) compression option was used.
 * 1      0    Fast (-ef) compression option was used.
 * 1      1    Super Fast (-es) compression option was used.
 *
 * Different programs encode compression level information in different ways:
 *
 * Deflate Compress Level  pkzip              zip      7z, WinRAR  WinZip
 * ----------------------  ----------------   -------  ----------  ------
 * Super Fast compression  1                                       1
 * Fast compression        2                  1, 2
 * Normal Compression      3 - 8 (5 default)  3 - 7    1 - 9
 * Maximum compression     9                  8, 9                 9
 */
interface ZipCompressionLevel
{
    /** @var int Compression level for super fast compression. */
    const SUPER_FAST = 1;

    /** @var int compression level for fast compression */
    const FAST = 2;

    /** @var int compression level for normal compression */
    const NORMAL = 5;

    /** @var int compression level for maximum compression */
    const MAXIMUM = 9;

    /**
     * @var int int Minimum compression level
     *
     * @internal
     */
    const LEVEL_MIN = self::SUPER_FAST;

    /**
     * @var int int Maximum compression level
     *
     * @internal
     */
    const LEVEL_MAX = self::MAXIMUM;
}
