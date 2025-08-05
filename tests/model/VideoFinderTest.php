<?php

namespace Video\Model;

use PHPUnit\Framework\TestCase;
use org\bovigo\vfs\vfsStreamWrapper;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStream;

class VideoFinderTest extends TestCase
{
    /** @var string */
    private $mediaFolder;

    /** @var VideoFinder */
    private $subject;

    protected function setUp(): void
    {
        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot(new vfsStreamDirectory('test'));
        $this->mediaFolder = vfsStream::url('test') . '/userfiles/media/';
        $this->subject = new VideoFinder($this->mediaFolder);
        mkdir($this->mediaFolder, 0777, true);
        touch($this->mediaFolder . 'movie.avi');
        touch($this->mediaFolder . 'movie.jpg');
        touch($this->mediaFolder . 'movie.mp4');
        touch($this->mediaFolder . 'movie.vtt');
        touch($this->mediaFolder . 'movie.webm');
        touch($this->mediaFolder . 'movie');
    }

    public function testAvailableVideos(): void
    {
        $expected = array('movie');
        $actual = $this->subject->availableVideos();
        $this->assertEquals($expected, $actual);
    }

    public function testFindsVideo(): void
    {
        $sources = [
            "{$this->mediaFolder}movie.webm" => "video/webm",
            "{$this->mediaFolder}movie.mp4" => "video/mp4"
        ];
        $video = $this->subject->find("movie", "en");
        $this->assertEquals($sources, $video->sources());
        $this->assertEquals("{$this->mediaFolder}movie.jpg", $video->poster());
        $this->assertEquals("{$this->mediaFolder}movie.vtt", $video->subtitle());
    }

    public function testFindsNoVideo(): void
    {
        $video = $this->subject->find("does_not_exist", "en");
        $this->assertNull($video);
    }

    public function testFindsVideoWithoutPoster(): void
    {
        $sources = [
            "{$this->mediaFolder}movie.webm" => "video/webm",
            "{$this->mediaFolder}movie.mp4" => "video/mp4"
        ];
        unlink($this->mediaFolder . 'movie.jpg');
        $video = $this->subject->find("movie", "en");
        $this->assertEquals($sources, $video->sources());
        $this->assertNull($video->poster());
        $this->assertEquals("{$this->mediaFolder}movie.vtt", $video->subtitle());
    }

    public function testFindsVideoWithoutSubtitle(): void
    {
        $sources = [
            "{$this->mediaFolder}movie.webm" => "video/webm",
            "{$this->mediaFolder}movie.mp4" => "video/mp4"
        ];
        unlink($this->mediaFolder . 'movie.vtt');
        $video = $this->subject->find("movie", "en");
        $this->assertEquals($sources, $video->sources());
        $this->assertEquals("{$this->mediaFolder}movie.jpg", $video->poster());
        $this->assertNull($video->subtitle());
    }
}
