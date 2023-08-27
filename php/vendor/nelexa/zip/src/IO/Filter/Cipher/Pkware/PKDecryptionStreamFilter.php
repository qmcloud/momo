<?php

namespace PhpZip\IO\Filter\Cipher\Pkware;

use PhpZip\Exception\ZipException;
use PhpZip\Model\ZipEntry;

/**
 * Decryption PKWARE Traditional Encryption.
 */
class PKDecryptionStreamFilter extends \php_user_filter
{
    const FILTER_NAME = 'phpzip.decryption.pkware';

    /** @var int */
    private $checkByte = 0;

    /** @var int */
    private $readLength = 0;

    /** @var int */
    private $size = 0;

    /** @var bool */
    private $readHeader = false;

    /** @var PKCryptContext */
    private $context;

    /**
     * @return bool
     */
    public static function register()
    {
        return stream_filter_register(self::FILTER_NAME, __CLASS__);
    }

    /**
     * @see https://php.net/manual/en/php-user-filter.oncreate.php
     *
     * @return bool
     */
    public function onCreate()
    {
        if (!isset($this->params['entry'])) {
            return false;
        }

        if (!($this->params['entry'] instanceof ZipEntry)) {
            throw new \RuntimeException('ZipEntry expected');
        }
        /** @var ZipEntry $entry */
        $entry = $this->params['entry'];
        $password = $entry->getPassword();

        if ($password === null) {
            return false;
        }

        $this->size = $entry->getCompressedSize();

        // init context
        $this->context = new PKCryptContext($password);

        // init check byte
        if ($entry->isDataDescriptorEnabled()) {
            $this->checkByte = ($entry->getDosTime() >> 8) & 0xff;
        } else {
            $this->checkByte = ($entry->getCrc() >> 24) & 0xff;
        }

        $this->readLength = 0;
        $this->readHeader = false;

        return true;
    }

    /**
     * Decryption filter.
     *
     * @param resource $in
     * @param resource $out
     * @param int      $consumed
     * @param bool     $closing
     *
     * @throws ZipException
     *
     * @return int
     *
     * @todo USE FFI in php 7.4
     */
    public function filter($in, $out, &$consumed, $closing)
    {
        while ($bucket = stream_bucket_make_writeable($in)) {
            $buffer = $bucket->data;
            $this->readLength += $bucket->datalen;

            if ($this->readLength > $this->size) {
                $buffer = substr($buffer, 0, $this->size - $this->readLength);
            }

            if (!$this->readHeader) {
                $header = substr($buffer, 0, PKCryptContext::STD_DEC_HDR_SIZE);
                $this->context->checkHeader($header, $this->checkByte);

                $buffer = substr($buffer, PKCryptContext::STD_DEC_HDR_SIZE);
                $this->readHeader = true;
            }

            $bucket->data = $this->context->decryptString($buffer);

            $consumed += $bucket->datalen;
            stream_bucket_append($out, $bucket);
        }

        return \PSFS_PASS_ON;
    }
}
