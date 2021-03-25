<?php

namespace FormRelay\Core\Model\Form;

use FormRelay\Core\Model\File\FileInterface;

class UploadField implements FieldInterface
{
    /** @var FileInterface */
    protected $file;

    /** @var string */
    protected $fileName;

    public function __construct(FileInterface $file)
    {
        $this->file = $file;
        $this->fileName = $file->getName();
    }

    public function getFile(): FileInterface
    {
        return $this->file;
    }

    public function __call($name, $arguments)
    {
        if (method_exists($this->file, $name)) {
            return $this->file->$name(...$arguments);
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

    public function getPublicUrl(): string
    {
        $this->file->getPublicUrl();
    }

    public function getRelativePath(): string
    {
        return $this->file->getRelativePath();
    }

    public function getMimeType(): string
    {
        return $this->file->getMimeType();
    }

    public function __toString(): string
    {
        return $this->getPublicUrl();
    }
}
