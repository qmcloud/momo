<?php

namespace PhpZip\Model\Data;

use PhpZip\Model\ZipData;
use PhpZip\Model\ZipEntry;
use PhpZip\ZipFile;

/**
 * The class contains a streaming resource with new content added to the ZIP archive.
 */
class ZipNewData implements ZipData
{
    /**
     * A static variable allows closing the stream in the destructor
     * only if it is its sole holder.
     *
     * @var array<int, int> array of resource ids and the number of class clones
     */
    private static $guardClonedStream = [];

    /** @var ZipEntry */
    private $zipEntry;

    /** @var resource */
    private $stream;

    /**
     * ZipStringData constructor.
     *
     * @param ZipEntry        $zipEntry
     * @param string|resource $data
     */
    public function __construct(ZipEntry $zipEntry, $data)
    {
        $this->zipEntry = $zipEntry;

        if (\is_string($data)) {
            $zipEntry->setUncompressedSize(\strlen($data));

            if (!($handle = fopen('php://temp', 'w+b'))) {
                // @codeCoverageIgnoreStart
                throw new \RuntimeException('A temporary resource cannot be opened for writing.');
                // @codeCoverageIgnoreEnd
            }
            fwrite($handle, $data);
            rewind($handle);
            $this->stream = $handle;
        } elseif (\is_resource($data)) {
            $this->stream = $data;
        }

        $resourceId = (int) $this->stream;
        self::$guardClonedStream[$resourceId] =
            isset(self::$guardClonedStream[$resourceId]) ?
                self::$guardClonedStream[$resourceId] + 1 :
                0;
    }

    /**
     * @return resource returns stream data
     */
    public function getDataAsStream()
    {
        if (!\is_resource($this->stream)) {
            throw new \LogicException(sprintf('Resource has been closed (entry=%s).', $this->zipEntry->getName()));
        }

        return $this->stream;
    }

    /**
     * @return string returns data as string
     */
    public function getDataAsString()
    {
        $stream = $this->getDataAsStream();
        $pos = ftell($stream);

        try {
            rewind($stream);

            return stream_get_contents($stream);
        } finally {
            fseek($stream, $pos);
        }
    }

    /**
     * @param resource $outStream
     */
    public function copyDataToStream($outStream)
    {
        $stream = $this->getDataAsStream();
        rewind($stream);
        stream_copy_to_stream($stream, $outStream);
    }

    /**
     * @see https://php.net/manual/en/language.oop5.cloning.php
     */
    public function __clone()
    {
        $resourceId = (int) $this->stream;
        self::$guardClonedStream[$resourceId] =
            isset(self::$guardClonedStream[$resourceId]) ?
                self::$guardClonedStream[$resourceId] + 1 :
                1;
    }

    /**
     * The stream will be closed when closing the zip archive.
     *
     * The method implements protection against closing the stream of the cloned object.
     *
     * @see ZipFile::close()
     */
    public function __destruct()
    {
        $resourceId = (int) $this->stream;

        if (isset(self::$guardClonedStream[$resourceId]) && self::$guardClonedStream[$resourceId] > 0) {
            self::$guardClonedStream[$resourceId]--;

            return;
        }

        if (\is_resource($this->stream)) {
            fclose($this->stream);
        }
    }
}
