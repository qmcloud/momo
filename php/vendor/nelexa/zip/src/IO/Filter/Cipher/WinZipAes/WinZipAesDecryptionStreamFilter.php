<?php

namespace PhpZip\IO\Filter\Cipher\WinZipAes;

use PhpZip\Exception\RuntimeException;
use PhpZip\Exception\ZipAuthenticationException;
use PhpZip\Model\Extra\Fields\WinZipAesExtraField;
use PhpZip\Model\ZipEntry;

/**
 * Decrypt WinZip AES stream.
 */
class WinZipAesDecryptionStreamFilter extends \php_user_filter
{
    const FILTER_NAME = 'phpzip.decryption.winzipaes';

    /** @var string */
    private $buffer;

    /** @var string */
    private $authenticationCode;

    /** @var int */
    private $encBlockPosition = 0;

    /** @var int */
    private $encBlockLength = 0;

    /** @var int */
    private $readLength = 0;

    /** @var ZipEntry */
    private $entry;

    /** @var WinZipAesContext|null */
    private $context;

    /**
     * @return bool
     */
    public static function register()
    {
        return stream_filter_register(self::FILTER_NAME, __CLASS__);
    }

    /**
     * @return bool
     *
     * @noinspection DuplicatedCode
     */
    public function onCreate()
    {
        if (!isset($this->params['entry'])) {
            return false;
        }

        if (!($this->params['entry'] instanceof ZipEntry)) {
            throw new \RuntimeException('ZipEntry expected');
        }
        $this->entry = $this->params['entry'];

        if (
            $this->entry->getPassword() === null ||
            !$this->entry->isEncrypted() ||
            !$this->entry->hasExtraField(WinZipAesExtraField::HEADER_ID)
        ) {
            return false;
        }

        $this->buffer = '';

        return true;
    }

    /**
     * @param resource $in
     * @param resource $out
     * @param int      $consumed
     * @param bool     $closing
     *
     * @throws ZipAuthenticationException
     *
     * @return int
     */
    public function filter($in, $out, &$consumed, $closing)
    {
        while ($bucket = stream_bucket_make_writeable($in)) {
            $this->buffer .= $bucket->data;
            $this->readLength += $bucket->datalen;

            if ($this->readLength > $this->entry->getCompressedSize()) {
                $this->buffer = substr($this->buffer, 0, $this->entry->getCompressedSize() - $this->readLength);
            }

            // read header
            if ($this->context === null) {
                /**
                 * @var WinZipAesExtraField|null $winZipExtra
                 */
                $winZipExtra = $this->entry->getExtraField(WinZipAesExtraField::HEADER_ID);

                if ($winZipExtra === null) {
                    throw new RuntimeException('$winZipExtra is null');
                }
                $saltSize = $winZipExtra->getSaltSize();
                $headerSize = $saltSize + WinZipAesContext::PASSWORD_VERIFIER_SIZE;

                if (\strlen($this->buffer) < $headerSize) {
                    return \PSFS_FEED_ME;
                }

                $salt = substr($this->buffer, 0, $saltSize);
                $passwordVerifier = substr($this->buffer, $saltSize, WinZipAesContext::PASSWORD_VERIFIER_SIZE);
                $password = $this->entry->getPassword();

                if ($password === null) {
                    throw new RuntimeException('$password is null');
                }
                $this->context = new WinZipAesContext($winZipExtra->getEncryptionStrength(), $password, $salt);
                unset($password);

                // Verify password.
                if ($passwordVerifier !== $this->context->getPasswordVerifier()) {
                    throw new ZipAuthenticationException('Invalid password');
                }

                $this->encBlockPosition = 0;
                $this->encBlockLength = $this->entry->getCompressedSize() - $headerSize - WinZipAesContext::FOOTER_SIZE;

                $this->buffer = substr($this->buffer, $headerSize);
            }

            // encrypt data
            $plainText = '';
            $offset = 0;
            $len = \strlen($this->buffer);
            $remaining = $this->encBlockLength - $this->encBlockPosition;

            if ($remaining >= WinZipAesContext::BLOCK_SIZE && $len < WinZipAesContext::BLOCK_SIZE) {
                return \PSFS_FEED_ME;
            }
            $limit = min($len, $remaining);

            if ($remaining > $limit && ($limit % WinZipAesContext::BLOCK_SIZE) !== 0) {
                $limit -= ($limit % WinZipAesContext::BLOCK_SIZE);
            }

            while ($offset < $limit) {
                $this->context->updateIv();
                $length = min(WinZipAesContext::BLOCK_SIZE, $limit - $offset);
                $data = substr($this->buffer, 0, $length);
                $plainText .= $this->context->decryption($data);
                $offset += $length;
                $this->buffer = substr($this->buffer, $length);
            }
            $this->encBlockPosition += $offset;

            if (
                $this->encBlockPosition === $this->encBlockLength &&
                \strlen($this->buffer) === WinZipAesContext::FOOTER_SIZE
            ) {
                $this->authenticationCode = $this->buffer;
                $this->buffer = '';
            }

            $bucket->data = $plainText;
            $consumed += $bucket->datalen;
            stream_bucket_append($out, $bucket);
        }

        return \PSFS_PASS_ON;
    }

    /**
     * @see http://php.net/manual/en/php-user-filter.onclose.php
     *
     * @throws ZipAuthenticationException
     */
    public function onClose()
    {
        $this->buffer = '';

        if ($this->context !== null) {
            $this->context->checkAuthCode($this->authenticationCode);
        }
    }
}
