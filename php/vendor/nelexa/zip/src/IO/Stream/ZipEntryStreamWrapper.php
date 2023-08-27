<?php

namespace PhpZip\IO\Stream;

use PhpZip\Exception\ZipException;
use PhpZip\Model\ZipEntry;

/**
 * The class provides stream reuse functionality.
 *
 * Stream will not be closed at {@see fclose}.
 *
 * @see https://www.php.net/streamwrapper
 */
final class ZipEntryStreamWrapper
{
    /** @var string the registered protocol */
    const PROTOCOL = 'zipentry';

    /** @var resource */
    public $context;

    /** @var resource */
    private $fp;

    /**
     * @return bool
     */
    public static function register()
    {
        $protocol = self::PROTOCOL;

        if (!\in_array($protocol, stream_get_wrappers(), true)) {
            if (!stream_wrapper_register($protocol, self::class)) {
                throw new \RuntimeException("Failed to register '{$protocol}://' protocol");
            }

            return true;
        }

        return false;
    }

    public static function unregister()
    {
        stream_wrapper_unregister(self::PROTOCOL);
    }

    /**
     * @param ZipEntry $entry
     *
     * @return resource
     */
    public static function wrap(ZipEntry $entry)
    {
        self::register();

        $context = stream_context_create(
            [
                self::PROTOCOL => [
                    'entry' => $entry,
                ],
            ]
        );

        $uri = self::PROTOCOL . '://' . $entry->getName();
        $fp = fopen($uri, 'r+b', false, $context);

        if ($fp === false) {
            throw new \RuntimeException('Error open ' . $uri);
        }

        return $fp;
    }

    /**
     * Opens file or URL.
     *
     * This method is called immediately after the wrapper is
     * initialized (f.e. by {@see fopen()} and {@see file_get_contents()}).
     *
     * @param string $path        specifies the URL that was passed to
     *                            the original function
     * @param string $mode        the mode used to open the file, as detailed
     *                            for {@see fopen()}
     * @param int    $options     Holds additional flags set by the streams
     *                            API. It can hold one or more of the
     *                            following values OR'd together.
     * @param string $opened_path if the path is opened successfully, and
     *                            STREAM_USE_PATH is set in options,
     *                            opened_path should be set to the
     *                            full path of the file/resource that
     *                            was actually opened
     *
     * @throws ZipException
     *
     * @return bool
     *
     * @see https://www.php.net/streamwrapper.stream-open
     */
    public function stream_open($path, $mode, $options, &$opened_path)
    {
        if ($this->context === null) {
            throw new \RuntimeException('stream context is null');
        }
        $streamOptions = stream_context_get_options($this->context);

        if (!isset($streamOptions[self::PROTOCOL]['entry'])) {
            throw new \RuntimeException('no stream option ["' . self::PROTOCOL . '"]["entry"]');
        }
        $zipEntry = $streamOptions[self::PROTOCOL]['entry'];

        if (!$zipEntry instanceof ZipEntry) {
            throw new \RuntimeException('invalid stream context');
        }

        $zipData = $zipEntry->getData();

        if ($zipData === null) {
            throw new ZipException(sprintf('No data for zip entry "%s"', $zipEntry->getName()));
        }
        $this->fp = $zipData->getDataAsStream();

        return $this->fp !== false;
    }

    /**
     * Read from stream.
     *
     * This method is called in response to {@see fread()} and {@see fgets()}.
     *
     * Note: Remember to update the read/write position of the stream
     * (by the number of bytes that were successfully read).
     *
     * @param int $count how many bytes of data from the current
     *                   position should be returned
     *
     * @return false|string If there are less than count bytes available,
     *                      return as many as are available. If no more data
     *                      is available, return either FALSE or
     *                      an empty string.
     *
     * @see https://www.php.net/streamwrapper.stream-read
     */
    public function stream_read($count)
    {
        return fread($this->fp, $count);
    }

    /**
     * Seeks to specific location in a stream.
     *
     * This method is called in response to {@see fseek()}.
     * The read/write position of the stream should be updated according
     * to the offset and whence.
     *
     * @param int $offset the stream offset to seek to
     * @param int $whence Possible values:
     *                    {@see \SEEK_SET} - Set position equal to offset bytes.
     *                    {@see \SEEK_CUR} - Set position to current location plus offset.
     *                    {@see \SEEK_END} - Set position to end-of-file plus offset.
     *
     * @return bool return TRUE if the position was updated, FALSE otherwise
     *
     * @see https://www.php.net/streamwrapper.stream-seek
     */
    public function stream_seek($offset, $whence = \SEEK_SET)
    {
        return fseek($this->fp, $offset, $whence) === 0;
    }

    /**
     * Retrieve the current position of a stream.
     *
     * This method is called in response to {@see fseek()} to determine
     * the current position.
     *
     * @return int should return the current position of the stream
     *
     * @see https://www.php.net/streamwrapper.stream-tell
     */
    public function stream_tell()
    {
        $pos = ftell($this->fp);

        if ($pos === false) {
            throw new \RuntimeException('Cannot get stream position.');
        }

        return $pos;
    }

    /**
     * Tests for end-of-file on a file pointer.
     *
     * This method is called in response to {@see feof()}.
     *
     * @return bool should return TRUE if the read/write position is at
     *              the end of the stream and if no more data is available
     *              to be read, or FALSE otherwise
     *
     * @see https://www.php.net/streamwrapper.stream-eof
     */
    public function stream_eof()
    {
        return feof($this->fp);
    }

    /**
     * Retrieve information about a file resource.
     *
     * This method is called in response to {@see fstat()}.
     *
     * @return array
     *
     * @see https://www.php.net/streamwrapper.stream-stat
     * @see https://www.php.net/stat
     * @see https://www.php.net/fstat
     */
    public function stream_stat()
    {
        return fstat($this->fp);
    }

    /**
     * Flushes the output.
     *
     * This method is called in response to {@see fflush()} and when the
     * stream is being closed while any unflushed data has been written to
     * it before.
     * If you have cached data in your stream but not yet stored it into
     * the underlying storage, you should do so now.
     *
     * @return bool should return TRUE if the cached data was successfully
     *              stored (or if there was no data to store), or FALSE
     *              if the data could not be stored
     *
     * @see https://www.php.net/streamwrapper.stream-flush
     */
    public function stream_flush()
    {
        return fflush($this->fp);
    }

    /**
     * Truncate stream.
     *
     * Will respond to truncation, e.g., through {@see ftruncate()}.
     *
     * @param int $new_size the new size
     *
     * @return bool returns TRUE on success or FALSE on failure
     *
     * @see https://www.php.net/streamwrapper.stream-truncate
     */
    public function stream_truncate($new_size)
    {
        return ftruncate($this->fp, (int) $new_size);
    }

    /**
     * Write to stream.
     *
     * This method is called in response to {@see fwrite().}
     *
     * Note: Remember to update the current position of the stream by
     * number of bytes that were successfully written.
     *
     * @param string $data should be stored into the underlying stream
     *
     * @return int should return the number of bytes that were successfully stored, or 0 if none could be stored
     *
     * @see https://www.php.net/streamwrapper.stream-write
     */
    public function stream_write($data)
    {
        $bytes = fwrite($this->fp, $data);

        return $bytes === false ? 0 : $bytes;
    }

    /**
     * Retrieve the underlaying resource.
     *
     * This method is called in response to {@see stream_select()}.
     *
     * @param int $cast_as can be {@see STREAM_CAST_FOR_SELECT} when {@see stream_select()}
     *                     is callingstream_cast() or {@see STREAM_CAST_AS_STREAM} when
     *                     stream_cast() is called for other uses
     *
     * @return resource
     */
    public function stream_cast($cast_as)
    {
        return $this->fp;
    }

    /**
     * Close a resource.
     *
     * This method is called in response to {@see fclose()}.
     * All resources that were locked, or allocated, by the wrapper should be released.
     *
     * @see https://www.php.net/streamwrapper.stream-close
     */
    public function stream_close()
    {
    }
}
