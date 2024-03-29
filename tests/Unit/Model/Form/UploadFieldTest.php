<?php

namespace FormRelay\Core\Tests\Unit\Model\Form;

use FormRelay\Core\Model\File\FileInterface;
use FormRelay\Core\Model\Form\UploadField;

class UploadFieldTest extends AbstractFieldTest
{
    const FIELD_CLASS =  UploadField::class;

    protected function createField(...$arguments)
    {
        $file = $this->createMock(FileInterface::class);
        $file->method('getName')->willReturn($arguments[0] ?? 'name1');
        $file->method('getPublicUrl')->willReturn($arguments[1] ?? 'url1');
        $file->method('getRelativePath')->willReturn($arguments[2] ?? 'path1');
        $file->method('getMimeType')->willReturn($arguments[3] ?? 'type1');
        return new UploadField($file);
    }

    /** @test */
    public function init()
    {
        $this->subject = $this->createField();
        $this->assertEquals('name1', $this->subject->getFileName());
        $this->assertEquals('url1', $this->subject->getPublicUrl());
        $this->assertEquals('path1', $this->subject->getRelativePath());
        $this->assertEquals('type1', $this->subject->getMimeType());
    }

    public function castToStringProvider(): array
    {
        return [
            [['name1', 'url1', 'path1', 'type1'], 'url1'],
            [['name2', 'url2', 'path2', 'type2'], 'url2'],
        ];
    }

    public function packProvider(): array
    {
        return [
            [
                ['name1', 'url1', 'path1', 'type1'],
                [
                    'fileName' => 'name1',
                    'publicUrl' => 'url1',
                    'relativePath' => 'path1',
                    'mimeType' => 'type1',
                ]
            ],
        ];
    }
}
