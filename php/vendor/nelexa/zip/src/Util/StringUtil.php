<?php

namespace PhpZip\Util;

/**
 * String Util.
 *
 * @internal
 */
final class StringUtil
{
    /**
     * @param string $haystack
     * @param string $needle
     *
     * @return bool
     */
    public static function endsWith($haystack, $needle)
    {
        $length = \strlen($needle);

        if ($length === 0) {
            return true;
        }

        return substr($haystack, -$length) === $needle;
    }

    /**
     * @param string $string
     *
     * @return bool
     */
    public static function isBinary($string)
    {
        return strpos($string, "\0") !== false;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public static function isASCII($name)
    {
        return preg_match('~[^\x20-\x7e]~', (string) $name) === 0;
    }
}
