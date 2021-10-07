<?php

namespace FormRelay\Core\Model\Form;

use FormRelay\Core\Model\File\FileInterface;

class UploadField implements FieldInterface
{
    /** @var string */
    protected $fileName = '';

    /** @var string */
    protected $publicUrl = '';

    /** @var string */
    protected $relativePath = '';

    /** @var string */
    protected $mimeType = '';

    public function __construct(FileInterface $file = null)
    {
        if ($file) {
            $this->fileName = $file->getName();
            $this->publicUrl = $file->getPublicUrl();
            $this->relativePath = $file->getRelativePath();
            $this->mimeType = $file->getMimeType();
        }
    }

    public function setFileName(string $fileName)
    {
        $this->fileName = $fileName;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function setPublicUrl(string $publicUrl)
    {
        $this->publicUrl = $publicUrl;
    }

    public function getPublicUrl(): string
    {
        return $this->publicUrl;
    }

    public function setRelativePath(string $relativePath)
    {
        return $this->relativePath = $relativePath;
    }

    public function getRelativePath(): string
    {
        return $this->relativePath;
    }

    public function setMimeType(string $mimeType)
    {
        return $this->mimeType = $mimeType;
    }

    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    public function __toString(): string
    {
        return $this->getPublicUrl();
    }

    public function pack(): array
    {
        return [
            'fileName' => $this->getFileName(),
            'publicUrl' => $this->getPublicUrl(),
            'relativePath' => $this->getRelativePath(),
            'mimeType' => $this->getMimeType(),
        ];
    }

    public static function unpack(array $packed): FieldInterface
    {
        $field = new static();
        $field->setFileName($packed['fileName']);
        $field->setPublicUrl($packed['publicUrl']);
        $field->setRelativePath($packed['relativePath']);
        $field->setMimeType($packed['mimeType']);
        return $field;
    }
}
