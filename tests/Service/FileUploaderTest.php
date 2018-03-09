<?php

namespace App\Tests\Service;

use PHPUnit\Framework\TestCase;
use App\Service\FileUploader;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Filesystem\Filesystem;

class FileUploaderTest extends TestCase
{
    private static $targetDir = '/tmp/file-uploader-test';

    /** @var FileUploader */
    private $service;

    /** @var Filesystem */
    private $fs;

    protected function setUp()
    {
        $this->service = new FileUploader(self::$targetDir);

        $this->fs = new Filesystem();
        $this->fs->mkdir(self::$targetDir);
    }

    protected function tearDown()
    {
        $this->fs->remove(self::$targetDir);
    }

    public function testGetTargetDir()
    {
        $this->assertEquals(self::$targetDir, $this->service->getTargetDir());
    }

    public function testUpload()
    {
        $filePath = self::$targetDir . '/uploaded.txt';
        $fileContents = 'Test OK!';
        $uploadSubDir = '/uploaded';

        $this->fs->dumpFile($filePath, $fileContents);

        $uploadedFile = new UploadedFile($filePath, 'test.txt', null, null, null, true);

        $fileName = $this->service->upload($uploadSubDir, $uploadedFile);
        $filePath = $this->service->getTargetDir() . $uploadSubDir . '/' . $fileName;

        $this->assertTrue($this->fs->exists($filePath), 'Uploaded file exists');
        $this->assertEquals($fileContents, file_get_contents($filePath), 'Uploaded file content is good');
    }
}