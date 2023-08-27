<?php

namespace PhpZip\Model\Data;

use PhpZip\Exception\Crc32Exception;
use PhpZip\Exception\ZipException;
use PhpZip\IO\ZipReader;
use PhpZip\Model\ZipData;
use PhpZip\Model\ZipEntry;

/**
 * Class ZipFileData.
 */
class ZipSourceFileData implements ZipData
{
    /** @var ZipReader */
    private $zipReader;

    /** @var resource|null */
    private $stream;

    /** @var ZipEntry */
    private $sourceEntry;

    /** @var int */
    private $offset;

    /** @var int */
    private $uncompressedSize;

    /** @var int */
    private $compressedSize;

    /**
     * ZipFileData constructor.
     *
     * @param ZipReader $zipReader
     * @param ZipEntry  $zipEntry
     * @param int       $offsetData
     */
    public function __construct(ZipReader $zipReader, ZipEntry $zipEntry, $offsetData)
    {
        $this->zipReader = $zipReader;
        $this->offset = $offsetData;
        $this->sourceEntry = $zipEntry;
        $this->compressedSize = $zipEntry->getCompressedSize();
        $this->uncompressedSize = $zipEntry->getUncompressedSize();
    }

    /**
     * @param ZipEntry $entry
     *
     * @return bool
     */
    public function hasRecompressData(ZipEntry $entry)
    {
        return $this->sourceEntry->getCompressionLevel() !== $entry->getCompressionLevel() ||
            $this->sourceEntry->getCompressionMethod() !== $entry->getCompressionMethod() ||
            $this->sourceEntry->isEncrypted() !== $entry->isEncrypted() ||
            $this->sourceEntry->getEncryptionMethod() !== $entry->getEncryptionMethod() ||
            $this->sourceEntry->getPassword() !== $entry->getPassword() ||
            $this->sourceEntry->getCompressedSize() !== $entry->getCompressedSize() ||
            $this->sourceEntry->getUncompressedSize() !== $entry->getUncompressedSize() ||
            $this->sourceEntry->getCrc() !== $entry->getCrc();
    }

    /**
     * @throws ZipException
     *
     * @return resource returns stream data
     */
    public function getDataAsStream()
    {
        if (!\is_resource($this->stream)) {
            $this->stream = $this->zipReader->getEntryStream($this);
        }

        return $this->stream;
    }

    /**
     * @throws ZipException
     *
     * @return string returns data as string
     */
    public function getDataAsString()
    {
        $autoClosable = $this->stream === null;

        $stream = $this->getDataAsStream();
        $pos = ftell($stream);

        try {
            rewind($stream);

            return stream_get_contents($stream);
        } finally {
            if ($autoClosable) {
                fclose($stream);
                $this->stream = null;
            } else {
                fseek($stream, $pos);
            }
        }
    }

    /**
     * @param resource $outputStream Output stream
     *
     * @throws ZipException
     * @throws Crc32Exception
     */
    public function copyDataToStream($outputStream)
    {
        if (\is_resource($this->stream)) {
            rewind($this->stream);
            stream_copy_to_stream($this->stream, $outputStream);
        } else {
            $this->zipReader->copyUncompressedDataToStream($this, $outputStream);
        }
    }

    /**
     * @param resource $outputStream Output stream
     */
    public function copyCompressedDataToStream($outputStream)
    {
        $this->zipReader->copyCompressedDataToStream($this, $outputStream);
    }

    /**
     * @return ZipEntry
     */
    public function getSourceEntry()
    {
        return $this->sourceEntry;
    }

    /**
     * @return int
     */
    public function getCompressedSize()
    {
        return $this->compressedSize;
    }

    /**
     * @return int
     */
    public function getUncompressedSize()
    {
        return $this->uncompressedSize;
    }

    /**
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * {@inheritdoc}
     */
    public function __destruct()
    {
        if (\is_resource($this->stream)) {
            fclose($this->stream);
        }
    }
}
