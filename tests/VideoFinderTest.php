<?php

/**
 * Copyright 2012-2023 Christoph M. Becker
 *
 * This file is part of Video_XH.
 *
 * Video_XH is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Video_XH is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Video_XH.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Video\Infra;

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
            "{$this->mediaFolder}movie.webm" => "webm",
            "{$this->mediaFolder}movie.mp4" => "mp4"
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
            "{$this->mediaFolder}movie.webm" => "webm",
            "{$this->mediaFolder}movie.mp4" => "mp4"
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
            "{$this->mediaFolder}movie.webm" => "webm",
            "{$this->mediaFolder}movie.mp4" => "mp4"
        ];
        unlink($this->mediaFolder . 'movie.vtt');
        $video = $this->subject->find("movie", "en");
        $this->assertEquals($sources, $video->sources());
        $this->assertEquals("{$this->mediaFolder}movie.jpg", $video->poster());
        $this->assertNull($video->subtitle());
    }
}
