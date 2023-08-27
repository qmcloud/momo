<?php

namespace PhpZip\Model;

use PhpZip\Constants\ZipCompressionMethod;
use PhpZip\Constants\ZipEncryptionMethod;
use PhpZip\Constants\ZipPlatform;
use PhpZip\Util\FileAttribUtil;
use PhpZip\Util\FilesUtil;

/**
 * Zip info.
 *
 * @author Ne-Lexa alexey@nelexa.ru
 * @license MIT
 *
 * @deprecated Use ZipEntry
 */
class ZipInfo
{
    /** @var ZipEntry */
    private $entry;

    /**
     * ZipInfo constructor.
     *
     * @param ZipEntry $entry
     */
    public function __construct(ZipEntry $entry)
    {
        $this->entry = $entry;
    }

    /**
     * @param ZipEntry $entry
     *
     * @return string
     *
     * @deprecated Use {@see ZipPlatform::getPlatformName()}
     */
    public static function getPlatformName(ZipEntry $entry)
    {
        return ZipPlatform::getPlatformName($entry->getExtractedOS());
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->entry->getName();
    }

    /**
     * @return bool
     */
    public function isFolder()
    {
        return $this->entry->isDirectory();
    }

    /**
     * @return int
     */
    public function getSize()
    {
        return $this->entry->getUncompressedSize();
    }

    /**
     * @return int
     */
    public function getCompressedSize()
    {
        return $this->entry->getCompressedSize();
    }

    /**
     * @return int
     */
    public function getMtime()
    {
        return $this->entry->getMTime()->getTimestamp();
    }

    /**
     * @return int|null
     */
    public function getCtime()
    {
        $ctime = $this->entry->getCTime();

        return $ctime === null ? null : $ctime->getTimestamp();
    }

    /**
     * @return int|null
     */
    public function getAtime()
    {
        $atime = $this->entry->getATime();

        return $atime === null ? null : $atime->getTimestamp();
    }

    /**
     * @return string
     */
    public function getAttributes()
    {
        $externalAttributes = $this->entry->getExternalAttributes();

        if ($this->entry->getCreatedOS() === ZipPlatform::OS_UNIX) {
            $permission = (($externalAttributes >> 16) & 0xFFFF);

            return FileAttribUtil::getUnixMode($permission);
        }

        return FileAttribUtil::getDosMode($externalAttributes);
    }

    /**
     * @return bool
     */
    public function isEncrypted()
    {
        return $this->entry->isEncrypted();
    }

    /**
     * @return string|null
     */
    public function getComment()
    {
        return $this->entry->getComment();
    }

    /**
     * @return int
     */
    public function getCrc()
    {
        return $this->entry->getCrc();
    }

    /**
     * @return string
     *
     * @deprecated use \PhpZip\Model\ZipInfo::getMethodName()
     */
    public function getMethod()
    {
        return $this->getMethodName();
    }

    /**
     * @return string
     */
    public function getMethodName()
    {
        return ZipCompressionMethod::getCompressionMethodName($this->entry->getCompressionMethod());
    }

    /**
     * @return string
     */
    public function getEncryptionMethodName()
    {
        return ZipEncryptionMethod::getEncryptionMethodName($this->entry->getEncryptionMethod());
    }

    /**
     * @return string
     */
    public function getPlatform()
    {
        return ZipPlatform::getPlatformName($this->entry->getExtractedOS());
    }

    /**
     * @return int
     */
    public function getVersion()
    {
        return $this->entry->getExtractVersion();
    }

    /**
     * @return int|null
     */
    public function getEncryptionMethod()
    {
        $encryptionMethod = $this->entry->getEncryptionMethod();

        return $encryptionMethod === ZipEncryptionMethod::NONE ? null : $encryptionMethod;
    }

    /**
     * @return int|null
     */
    public function getCompressionLevel()
    {
        return $this->entry->getCompressionLevel();
    }

    /**
     * @return int
     */
    public function getCompressionMethod()
    {
        return $this->entry->getCompressionMethod();
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'name' => $this->getName(),
            'folder' => $this->isFolder(),
            'size' => $this->getSize(),
            'compressed_size' => $this->getCompressedSize(),
            'modified' => $this->getMtime(),
            'created' => $this->getCtime(),
            'accessed' => $this->getAtime(),
            'attributes' => $this->getAttributes(),
            'encrypted' => $this->isEncrypted(),
            'encryption_method' => $this->getEncryptionMethod(),
            'encryption_method_name' => $this->getEncryptionMethodName(),
            'comment' => $this->getComment(),
            'crc' => $this->getCrc(),
            'method_name' => $this->getMethodName(),
            'compression_method' => $this->getCompressionMethod(),
            'platform' => $this->getPlatform(),
            'version' => $this->getVersion(),
        ];
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $ctime = $this->entry->getCTime();
        $atime = $this->entry->getATime();
        $comment = $this->getComment();

        return __CLASS__ . ' {'
            . 'Name="' . $this->getName() . '", '
            . ($this->isFolder() ? 'Folder, ' : '')
            . 'Size="' . FilesUtil::humanSize($this->getSize()) . '"'
            . ', Compressed size="' . FilesUtil::humanSize($this->getCompressedSize()) . '"'
            . ', Modified time="' . $this->entry->getMTime()->format(\DATE_W3C) . '", '
            . ($ctime !== null ? 'Created time="' . $ctime->format(\DATE_W3C) . '", ' : '')
            . ($atime !== null ? 'Accessed time="' . $atime->format(\DATE_W3C) . '", ' : '')
            . ($this->isEncrypted() ? 'Encrypted, ' : '')
            . ($comment !== null ? 'Comment="' . $comment . '", ' : '')
            . (!empty($this->crc) ? 'Crc=0x' . dechex($this->crc) . ', ' : '')
            . 'Method name="' . $this->getMethodName() . '", '
            . 'Attributes="' . $this->getAttributes() . '", '
            . 'Platform="' . $this->getPlatform() . '", '
            . 'Version=' . $this->getVersion()
            . '}';
    }
}
