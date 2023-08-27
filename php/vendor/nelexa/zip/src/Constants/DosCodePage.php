<?php

/** @noinspection PhpComposerExtensionStubsInspection */

namespace PhpZip\Constants;

/**
 * Class DosCodePage.
 */
final class DosCodePage
{
    const CP_LATIN_US = 'cp437';

    const CP_GREEK = 'cp737';

    const CP_BALT_RIM = 'cp775';

    const CP_LATIN1 = 'cp850';

    const CP_LATIN2 = 'cp852';

    const CP_CYRILLIC = 'cp855';

    const CP_TURKISH = 'cp857';

    const CP_PORTUGUESE = 'cp860';

    const CP_ICELANDIC = 'cp861';

    const CP_HEBREW = 'cp862';

    const CP_CANADA = 'cp863';

    const CP_ARABIC = 'cp864';

    const CP_NORDIC = 'cp865';

    const CP_CYRILLIC_RUSSIAN = 'cp866';

    const CP_GREEK2 = 'cp869';

    const CP_THAI = 'cp874';

    /** @var string[] */
    private static $CP_CHARSETS = [
        self::CP_LATIN_US,
        self::CP_GREEK,
        self::CP_BALT_RIM,
        self::CP_LATIN1,
        self::CP_LATIN2,
        self::CP_CYRILLIC,
        self::CP_TURKISH,
        self::CP_PORTUGUESE,
        self::CP_ICELANDIC,
        self::CP_HEBREW,
        self::CP_CANADA,
        self::CP_ARABIC,
        self::CP_NORDIC,
        self::CP_CYRILLIC_RUSSIAN,
        self::CP_GREEK2,
        self::CP_THAI,
    ];

    /**
     * @param string $str
     * @param string $sourceEncoding
     *
     * @return string
     */
    public static function toUTF8($str, $sourceEncoding)
    {
        $s = iconv($sourceEncoding, 'UTF-8', $str);

        if ($s === false) {
            return $str;
        }

        return $s;
    }

    /**
     * @param string $str
     * @param string $destEncoding
     *
     * @return string
     */
    public static function fromUTF8($str, $destEncoding)
    {
        $s = iconv('UTF-8', $destEncoding, $str);

        if ($s === false) {
            return $str;
        }

        return $s;
    }

    /**
     * @return string[]
     */
    public static function getCodePages()
    {
        return self::$CP_CHARSETS;
    }
}
