<?php

namespace PhpZip\IO\Filter\Cipher\WinZipAes;

use PhpZip\Exception\RuntimeException;
use PhpZip\Model\Extra\Fields\WinZipAesExtraField;
use PhpZip\Model\ZipEntry;

/**
 * Encrypt WinZip AES stream.
 */
class WinZipAesEncryptionStreamFilter extends \php_user_filter
{
    const FILTER_NAME = 'phpzip.encryption.winzipaes';

    /** @var string */
    private $buffer;

    /** @var int */
    private $remaining = 0;

    /** @var ZipEntry */
    private $entry;

    /** @var int */
    private $size;

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

        $this->size = (int) $this->params['size'];
        $this->context = null;
        $this->buffer = '';

        return true;
    }

    /**
     * @param resource $in
     * @param resource $out
     * @param int      $consumed
     * @param bool     $closing
     *
     * @return int
     */
    public function filter($in, $out, &$consumed, $closing)
    {
        while ($bucket = stream_bucket_make_writeable($in)) {
            $this->buffer .= $bucket->data;
            $this->remaining += $bucket->datalen;

            if ($this->remaining > $this->size) {
                $this->buffer = substr($this->buffer, 0, $this->size - $this->remaining);
                $this->remaining = $this->size;
            }

            $encryptionText = '';

            // write header
            if ($this->context === null) {
                /**
                 * @var WinZipAesExtraField|null $winZipExtra
                 */
                $winZipExtra = $this->entry->getExtraField(WinZipAesExtraField::HEADER_ID);

                if ($winZipExtra === null) {
                    throw new RuntimeException('$winZipExtra is null');
                }
                $saltSize = $winZipExtra->getSaltSize();

                try {
                    $salt = random_bytes($saltSize);
                } catch (\Exception $e) {
                    throw new \RuntimeException('Oops, our server is bust and cannot generate any random data.', 1, $e);
                }
                $password = $this->entry->getPassword();

                if ($password === null) {
                    throw new RuntimeException('$password is null');
                }
                $this->context = new WinZipAesContext(
                    $winZipExtra->getEncryptionStrength(),
                    $password,
                    $salt
                );

                $encryptionText .= $salt . $this->context->getPasswordVerifier();
            }

            // encrypt data
            $offset = 0;
            $len = \strlen($this->buffer);
            $remaining = $this->remaining - $this->size;

            if ($remaining >= WinZipAesContext::BLOCK_SIZE && $len < WinZipAesContext::BLOCK_SIZE) {
                return \PSFS_FEED_ME;
            }
            $limit = max($len, $remaining);

            if ($remaining > $limit && ($limit % WinZipAesContext::BLOCK_SIZE) !== 0) {
                $limit -= ($limit % WinZipAesContext::BLOCK_SIZE);
            }

            while ($offset < $limit) {
                $this->context->updateIv();
                $length = min(WinZipAesContext::BLOCK_SIZE, $limit - $offset);
                $encryptionText .= $this->context->encrypt(
                    substr($this->buffer, 0, $length)
                );
                $offset += $length;
                $this->buffer = substr($this->buffer, $length);
            }

            if ($remaining === 0) {
                $encryptionText .= $this->context->getHmac();
            }

            $bucket->data = $encryptionText;
            $consumed += $bucket->datalen;

            stream_bucket_append($out, $bucket);
        }

        return \PSFS_PASS_ON;
    }
}
