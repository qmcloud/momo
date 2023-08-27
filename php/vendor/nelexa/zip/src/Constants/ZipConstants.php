<?php

namespace PhpZip\Constants;

/**
 * Zip Constants.
 *
 * @author Ne-Lexa alexey@nelexa.ru
 * @license MIT
 */
interface ZipConstants
{
    /** @var int End Of Central Directory Record signature. */
    const END_CD = 0x06054B50; // "PK\005\006"

    /** @var int Zip64 End Of Central Directory Record. */
    const ZIP64_END_CD = 0x06064B50; // "PK\006\006"

    /** @var int Zip64 End Of Central Directory Locator. */
    const ZIP64_END_CD_LOC = 0x07064B50; // "PK\006\007"

    /** @var int Central File Header signature. */
    const CENTRAL_FILE_HEADER = 0x02014B50; // "PK\001\002"

    /** @var int Local File Header signature. */
    const LOCAL_FILE_HEADER = 0x04034B50; // "PK\003\004"

    /** @var int Data Descriptor signature. */
    const DATA_DESCRIPTOR = 0x08074B50; // "PK\007\008"

    /**
     * @var int value stored in four-byte size and similar fields
     *          if ZIP64 extensions are used
     */
    const ZIP64_MAGIC = 0xFFFFFFFF;

    /**
     * Local File Header signature      4
     * Version Needed To Extract        2
     * General Purpose Bit Flags        2
     * Compression Method               2
     * Last Mod File Time               2
     * Last Mod File Date               2
     * CRC-32                           4
     * Compressed Size                  4
     * Uncompressed Size                4.
     *
     * @var int Local File Header filename position
     */
    const LFH_FILENAME_LENGTH_POS = 26;

    /**
     * The minimum length of the Local File Header record.
     *
     * local file header signature      4
     * version needed to extract        2
     * general purpose bit flag         2
     * compression method               2
     * last mod file time               2
     * last mod file date               2
     * crc-32                           4
     * compressed size                  4
     * uncompressed size                4
     * file name length                 2
     * extra field length               2
     */
    const LFH_FILENAME_POS = 30;

    /** @var int the length of the Zip64 End Of Central Directory Locator */
    const ZIP64_END_CD_LOC_LEN = 20;

    /** @var int the minimum length of the End Of Central Directory Record */
    const END_CD_MIN_LEN = 22;

    /**
     * The minimum length of the Zip64 End Of Central Directory Record.
     *
     * zip64 end of central dir
     * signature                        4
     * size of zip64 end of central
     * directory record                 8
     * version made by                  2
     * version needed to extract        2
     * number of this disk              4
     * number of the disk with the
     * start of the central directory   4
     * total number of entries in the
     * central directory on this disk   8
     * total number of entries in
     * the central directory            8
     * size of the central directory    8
     * offset of start of central
     * directory with respect to
     * the starting disk number         8
     *
     * @var int ZIP64 End Of Central Directory length
     */
    const ZIP64_END_OF_CD_LEN = 56;
}
