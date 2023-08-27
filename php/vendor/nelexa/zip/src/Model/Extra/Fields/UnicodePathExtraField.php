<?php

namespace PhpZip\Model\Extra\Fields;

/**
 * Info-ZIP Unicode Path Extra Field (0x7075):
 * ==========================================.
 *
 * Stores the UTF-8 version of the file name field as stored in the
 * local header and central directory header. (Last Revision 20070912)
 *
 * Value         Size        Description
 * -----         ----        -----------
 * (UPath) 0x7075        Short       tag for this extra block type ("up")
 * TSize         Short       total data size for this block
 * Version       1 byte      version of this extra field, currently 1
 * NameCRC32     4 bytes     File Name Field CRC32 Checksum
 * UnicodeName   Variable    UTF-8 version of the entry File Name
 *
 * Currently Version is set to the number 1.  If there is a need
 * to change this field, the version will be incremented.  Changes
 * may not be backward compatible so this extra field should not be
 * used if the version is not recognized.
 *
 * The NameCRC32 is the standard zip CRC32 checksum of the File Name
 * field in the header.  This is used to verify that the header
 * File Name field has not changed since the Unicode Path extra field
 * was created.  This can happen if a utility renames the File Name but
 * does not update the UTF-8 path extra field.  If the CRC check fails,
 * this UTF-8 Path Extra Field should be ignored and the File Name field
 * in the header should be used instead.
 *
 * The UnicodeName is the UTF-8 version of the contents of the File Name
 * field in the header.  As UnicodeName is defined to be UTF-8, no UTF-8
 * byte order mark (BOM) is used.  The length of this field is determined
 * by subtracting the size of the previous fields from TSize.  If both
 * the File Name and Comment fields are UTF-8, the new General Purpose
 * Bit Flag, bit 11 (Language encoding flag (EFS)), can be used to
 * indicate that both the header File Name and Comment fields are UTF-8
 * and, in this case, the Unicode Path and Unicode Comment extra fields
 * are not needed and should not be created.  Note that, for backward
 * compatibility, bit 11 should only be used if the native character set
 * of the paths and comments being zipped up are already in UTF-8. It is
 * expected that the same file name storage method, either general
 * purpose bit 11 or extra fields, be used in both the Local and Central
 * Directory Header for a file.
 *
 * @see https://pkware.cachefly.net/webdocs/casestudies/APPNOTE.TXT section 4.6.9
 */
class UnicodePathExtraField extends AbstractUnicodeExtraField
{
    const HEADER_ID = 0x7075;

    /**
     * Returns the Header ID (type) of this Extra Field.
     * The Header ID is an unsigned short integer (two bytes)
     * which must be constant during the life cycle of this object.
     *
     * @return int
     */
    public function getHeaderId()
    {
        return self::HEADER_ID;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf(
            '0x%04x UnicodePath: "%s"',
            self::HEADER_ID,
            $this->getUnicodeValue()
        );
    }
}
