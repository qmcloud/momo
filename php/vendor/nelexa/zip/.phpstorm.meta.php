<?php

namespace PHPSTORM_META {

    registerArgumentsSet(
        "bool",
        true,
        false
    );

    registerArgumentsSet(
        "compression_methods",
        \PhpZip\Constants\ZipCompressionMethod::STORED,
        \PhpZip\Constants\ZipCompressionMethod::DEFLATED,
        \PhpZip\Constants\ZipCompressionMethod::BZIP2
    );
    expectedArguments(\PhpZip\ZipFile::addFile(), 2, argumentsSet("compression_methods"));
    expectedArguments(\PhpZip\ZipFile::addFromStream(), 2, argumentsSet("compression_methods"));
    expectedArguments(\PhpZip\ZipFile::addFromString(), 2, argumentsSet("compression_methods"));
    expectedArguments(\PhpZip\ZipFile::addDir(), 2, argumentsSet("compression_methods"));
    expectedArguments(\PhpZip\ZipFile::addDirRecursive(), 2, argumentsSet("compression_methods"));
    expectedArguments(\PhpZip\ZipFile::addFilesFromIterator(), 2, argumentsSet("compression_methods"));
    expectedArguments(\PhpZip\ZipFile::addFilesFromIterator(), 2, argumentsSet("compression_methods"));
    expectedArguments(\PhpZip\ZipFile::addFilesFromGlob(), 3, argumentsSet("compression_methods"));
    expectedArguments(\PhpZip\ZipFile::addFilesFromGlobRecursive(), 3, argumentsSet("compression_methods"));
    expectedArguments(\PhpZip\ZipFile::addFilesFromRegex(), 3, argumentsSet("compression_methods"));
    expectedArguments(\PhpZip\ZipFile::addFilesFromRegexRecursive(), 3, argumentsSet("compression_methods"));
    expectedArguments(\PhpZip\ZipFile::setCompressionMethodEntry(), 1, argumentsSet("compression_methods"));
    expectedArguments(\PhpZip\Model\ZipEntry::setCompressionMethod(), 0, argumentsSet("compression_methods"));
    expectedArguments(\PhpZip\Model\ZipEntry::setMethod(), 0, argumentsSet("compression_methods"));

    registerArgumentsSet(
        'compression_levels',
        \PhpZip\Constants\ZipCompressionLevel::MAXIMUM,
        \PhpZip\Constants\ZipCompressionLevel::NORMAL,
        \PhpZip\Constants\ZipCompressionLevel::FAST,
        \PhpZip\Constants\ZipCompressionLevel::SUPER_FAST
    );
    expectedArguments(\PhpZip\ZipFile::setCompressionLevel(), 0, argumentsSet("compression_levels"));
    expectedArguments(\PhpZip\ZipFile::setCompressionLevelEntry(), 1, argumentsSet("compression_levels"));
    expectedArguments(\PhpZip\Model\ZipEntry::setCompressionLevel(), 0, argumentsSet("compression_levels"));

    registerArgumentsSet(
        'encryption_methods',
        \PhpZip\Constants\ZipEncryptionMethod::WINZIP_AES_256,
        \PhpZip\Constants\ZipEncryptionMethod::WINZIP_AES_192,
        \PhpZip\Constants\ZipEncryptionMethod::WINZIP_AES_128,
        \PhpZip\Constants\ZipEncryptionMethod::PKWARE
    );
    expectedArguments(\PhpZip\ZipFile::setPassword(), 1, argumentsSet("encryption_methods"));
    expectedArguments(\PhpZip\ZipFile::setPasswordEntry(), 2, argumentsSet("encryption_methods"));
    expectedArguments(\PhpZip\Model\ZipEntry::setEncryptionMethod(), 0, argumentsSet("encryption_methods"));
    expectedArguments(\PhpZip\Model\ZipEntry::setPassword(), 1, argumentsSet("encryption_methods"));

    registerArgumentsSet(
        'zip_mime_types',
        null,
        'application/zip',
        'application/vnd.android.package-archive',
        'application/java-archive'
    );
    expectedArguments(\PhpZip\ZipFile::outputAsAttachment(), 1, argumentsSet("zip_mime_types"));
    expectedArguments(\PhpZip\ZipFile::outputAsAttachment(), 2, argumentsSet("bool"));

    expectedArguments(\PhpZip\ZipFile::outputAsResponse(), 2, argumentsSet("zip_mime_types"));
    expectedArguments(\PhpZip\ZipFile::outputAsResponse(), 3, argumentsSet("bool"));

    registerArgumentsSet(
        'dos_charset',
        \PhpZip\Constants\DosCodePage::CP_LATIN_US,
        \PhpZip\Constants\DosCodePage::CP_GREEK,
        \PhpZip\Constants\DosCodePage::CP_BALT_RIM,
        \PhpZip\Constants\DosCodePage::CP_LATIN1,
        \PhpZip\Constants\DosCodePage::CP_LATIN2,
        \PhpZip\Constants\DosCodePage::CP_CYRILLIC,
        \PhpZip\Constants\DosCodePage::CP_TURKISH,
        \PhpZip\Constants\DosCodePage::CP_PORTUGUESE,
        \PhpZip\Constants\DosCodePage::CP_ICELANDIC,
        \PhpZip\Constants\DosCodePage::CP_HEBREW,
        \PhpZip\Constants\DosCodePage::CP_CANADA,
        \PhpZip\Constants\DosCodePage::CP_ARABIC,
        \PhpZip\Constants\DosCodePage::CP_NORDIC,
        \PhpZip\Constants\DosCodePage::CP_CYRILLIC_RUSSIAN,
        \PhpZip\Constants\DosCodePage::CP_GREEK2,
        \PhpZip\Constants\DosCodePage::CP_THAI
    );
    expectedArguments(\PhpZip\Model\ZipEntry::setCharset(), 0, argumentsSet('dos_charset'));
    expectedArguments(\PhpZip\Constants\DosCodePage::toUTF8(), 1, argumentsSet('dos_charset'));
    expectedArguments(\PhpZip\Constants\DosCodePage::fromUTF8(), 1, argumentsSet('dos_charset'));

    registerArgumentsSet(
        "zip_os",
        \PhpZip\Constants\ZipPlatform::OS_UNIX,
        \PhpZip\Constants\ZipPlatform::OS_DOS,
        \PhpZip\Constants\ZipPlatform::OS_MAC_OSX
    );
    expectedArguments(\PhpZip\Model\ZipEntry::setCreatedOS(), 0, argumentsSet('zip_os'));
    expectedArguments(\PhpZip\Model\ZipEntry::setExtractedOS(), 0, argumentsSet('zip_os'));
    expectedArguments(\PhpZip\Model\ZipEntry::setPlatform(), 0, argumentsSet('zip_os'));

    registerArgumentsSet(
        "zip_gpbf",
        \PhpZip\Constants\GeneralPurposeBitFlag::ENCRYPTION |
        \PhpZip\Constants\GeneralPurposeBitFlag::DATA_DESCRIPTOR |
        \PhpZip\Constants\GeneralPurposeBitFlag::COMPRESSION_FLAG1 |
        \PhpZip\Constants\GeneralPurposeBitFlag::COMPRESSION_FLAG2 |
        \PhpZip\Constants\GeneralPurposeBitFlag::UTF8
    );
    expectedArguments(\PhpZip\Model\ZipEntry::setGeneralPurposeBitFlags(), 0, argumentsSet('zip_gpbf'));

    registerArgumentsSet(
        "winzip_aes_vendor_version",
        \PhpZip\Model\Extra\Fields\WinZipAesExtraField::VERSION_AE1,
        \PhpZip\Model\Extra\Fields\WinZipAesExtraField::VERSION_AE2
    );
    registerArgumentsSet(
        "winzip_aes_key_strength",
        \PhpZip\Model\Extra\Fields\WinZipAesExtraField::KEY_STRENGTH_256BIT,
        \PhpZip\Model\Extra\Fields\WinZipAesExtraField::KEY_STRENGTH_128BIT,
        \PhpZip\Model\Extra\Fields\WinZipAesExtraField::KEY_STRENGTH_192BIT
    );
    expectedArguments(\PhpZip\Model\Extra\Fields\WinZipAesExtraField::__construct(), 0, argumentsSet('winzip_aes_vendor_version'));
    expectedArguments(\PhpZip\Model\Extra\Fields\WinZipAesExtraField::__construct(), 1, argumentsSet('winzip_aes_key_strength'));
    expectedArguments(\PhpZip\Model\Extra\Fields\WinZipAesExtraField::__construct(), 2, argumentsSet('compression_methods'));
    expectedArguments(\PhpZip\Model\Extra\Fields\WinZipAesExtraField::setVendorVersion(), 0, argumentsSet('winzip_aes_vendor_version'));
    expectedArguments(\PhpZip\Model\Extra\Fields\WinZipAesExtraField::setKeyStrength(), 0, argumentsSet('winzip_aes_key_strength'));
    expectedArguments(\PhpZip\Model\Extra\Fields\WinZipAesExtraField::setCompressionMethod(), 0, argumentsSet('compression_methods'));
}
