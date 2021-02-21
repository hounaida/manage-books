<?php

namespace App\Tests\Unit\Services;

use App\Service\FileUploader;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\String\UnicodeString;

class FileUploaderTest extends KernelTestCase
{
    private $file;
    private $slugger;

    public function setUp(): void
    {
        parent::setUp();
        $this->file = $this->getMockBuilder(File::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->slugger = $this->getMockBuilder(SluggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
    }

    public function testUpload(): void
    {
        $target = __DIR__.'/../../Images/';

        $this->file
            ->expects($this->once())
            ->method('getFilename')
            ->willReturn('Ibiza-bohemia')
            ;
       $this->slugger
            ->expects($this->once())
            ->method('slug')
            ->with('Ibiza-bohemia')
           ->willReturn(new UnicodeString('ibiza-bohemia'))
            ;
        $this->file
            ->expects($this->once())
            ->method('guessExtension')
            ->willReturn('jpg')
        ;
        $this->file
            ->expects($this->once())
            ->method('move')
            ->with($target)
        ;

        $fileUploader = new FileUploader($target, $this->slugger);
        $fileUploader->upload($this->file);
     }
}
