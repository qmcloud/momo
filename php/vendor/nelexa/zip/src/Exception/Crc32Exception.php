<?php

namespace PhpZip\Exception;

/**
 * Thrown to indicate a CRC32 mismatch between the declared value in the
 * Central File Header and the Data Descriptor or between the declared value
 * and the computed value from the decompressed data.
 *
 * The exception detail message is the name of the ZIP entry.
 *
 * @author Ne-Lexa alexey@nelexa.ru
 * @license MIT
 */
class Crc32Exception extends ZipException
{
    /**
     * Expected crc.
     *
     * @var int
     */
    private $expectedCrc;

    /**
     * Actual crc.
     *
     * @var int
     */
    private $actualCrc;

    /**
     * Crc32Exception constructor.
     *
     * @param string $name
     * @param int    $expected
     * @param int    $actual
     */
    public function __construct($name, $expected, $actual)
    {
        parent::__construct(
            sprintf(
                '%s (expected CRC32 value 0x%x, but is actually 0x%x)',
                $name,
                $expected,
                $actual
            )
        );
        $this->expectedCrc = $expected;
        $this->actualCrc = $actual;
    }

    /**
     * Returns expected crc.
     *
     * @return int
     */
    public function getExpectedCrc()
    {
        return $this->expectedCrc;
    }

    /**
     * Returns actual crc.
     *
     * @return int
     */
    public function getActualCrc()
    {
        return $this->actualCrc;
    }
}
