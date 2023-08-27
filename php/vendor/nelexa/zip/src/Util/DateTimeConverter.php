<?php

namespace PhpZip\Util;

/**
 * Convert unix timestamp values to DOS date/time values and vice versa.
 *
 * The DOS date/time format is a bitmask:
 *
 * 24                16                 8                 0
 * +-+-+-+-+-+-+-+-+ +-+-+-+-+-+-+-+-+ +-+-+-+-+-+-+-+-+ +-+-+-+-+-+-+-+-+
 * |Y|Y|Y|Y|Y|Y|Y|M| |M|M|M|D|D|D|D|D| |h|h|h|h|h|m|m|m| |m|m|m|s|s|s|s|s|
 * +-+-+-+-+-+-+-+-+ +-+-+-+-+-+-+-+-+ +-+-+-+-+-+-+-+-+ +-+-+-+-+-+-+-+-+
 * \___________/\________/\_________/ \________/\____________/\_________/
 * year        month       day      hour       minute        second
 *
 * The year is stored as an offset from 1980.
 * Seconds are stored in two-second increments.
 * (So if the "second" value is 15, it actually represents 30 seconds.)
 *
 * @see https://docs.microsoft.com/ru-ru/windows/win32/api/winbase/nf-winbase-filetimetodosdatetime?redirectedfrom=MSDN
 *
 * @author Ne-Lexa alexey@nelexa.ru
 * @license MIT
 *
 * @internal
 */
class DateTimeConverter
{
    /**
     * Smallest supported DOS date/time value in a ZIP file,
     * which is January 1st, 1980 AD 00:00:00 local time.
     */
    const MIN_DOS_TIME = 0x210000; // (1 << 21) | (1 << 16)

    /**
     * Largest supported DOS date/time value in a ZIP file,
     * which is December 31st, 2107 AD 23:59:58 local time.
     */
    const MAX_DOS_TIME = 0xff9fbf7d; // ((2107 - 1980) << 25) | (12 << 21) | (31 << 16) | (23 << 11) | (59 << 5) | (58 >> 1);

    /**
     * Convert a 32 bit integer DOS date/time value to a UNIX timestamp value.
     *
     * @param int $dosTime Dos date/time
     *
     * @return int Unix timestamp
     */
    public static function msDosToUnix($dosTime)
    {
        if ($dosTime <= self::MIN_DOS_TIME) {
            $dosTime = 0;
        } elseif ($dosTime > self::MAX_DOS_TIME) {
            $dosTime = self::MAX_DOS_TIME;
        }
//        date_default_timezone_set('UTC');
        return mktime(
            (($dosTime >> 11) & 0x1f),         // hours
            (($dosTime >> 5) & 0x3f),          // minutes
            (($dosTime << 1) & 0x3e),          // seconds
            (($dosTime >> 21) & 0x0f),         // month
            (($dosTime >> 16) & 0x1f),         // day
            ((($dosTime >> 25) & 0x7f) + 1980) // year
        );
    }

    /**
     * Converts a UNIX timestamp value to a DOS date/time value.
     *
     * @param int $unixTimestamp the number of seconds since midnight, January 1st,
     *                           1970 AD UTC
     *
     * @return int a DOS date/time value reflecting the local time zone and
     *             rounded down to even seconds
     *             and is in between DateTimeConverter::MIN_DOS_TIME and DateTimeConverter::MAX_DOS_TIME
     */
    public static function unixToMsDos($unixTimestamp)
    {
        if ($unixTimestamp < 0) {
            throw new \InvalidArgumentException('Negative unix timestamp: ' . $unixTimestamp);
        }

        $date = getdate($unixTimestamp);
        $dosTime = (
            (($date['year'] - 1980) << 25) |
            ($date['mon'] << 21) |
            ($date['mday'] << 16) |
            ($date['hours'] << 11) |
            ($date['minutes'] << 5) |
            ($date['seconds'] >> 1)
        );

        if ($dosTime <= self::MIN_DOS_TIME) {
            $dosTime = 0;
        }

        return $dosTime;
    }
}
