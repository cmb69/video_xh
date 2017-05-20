<?php

/**
 * Copyright 2012-2017 Christoph M. Becker
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

namespace Video;

use PHPUnit_Framework_TestCase;
use org\bovigo\vfs\vfsStreamWrapper;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStream;

class ModelTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    private $mediaFolder;

    /**
     * @var Model
     */
    private $subject;

    protected function setUp()
    {
        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot(new vfsStreamDirectory('test'));
        $this->mediaFolder = vfsStream::url('test') . '/userfiles/media/';
        $config = array(
            'default_preload' => 'auto',
            'default_autoplay' => '0',
            'default_loop' => '0',
            'default_controls' => '1',
            'default_width' => '512',
            'default_height' => '288',
            'default_resize' => 'no'
        );
        $this->subject = new Model($this->mediaFolder, $config);
        mkdir($this->mediaFolder, 0777, true);
        file_put_contents($this->mediaFolder . 'movie.avi', '');
        file_put_contents($this->mediaFolder . 'movie.jpg', '');
        file_put_contents($this->mediaFolder . 'movie.mp4', '');
        file_put_contents($this->mediaFolder . 'movie.vtt', '');
        file_put_contents($this->mediaFolder . 'movie.webm', '');
        file_put_contents($this->mediaFolder . 'movie', '');
    }

    public function testNormalizedUrl()
    {
        $url = 'http://example.com/foo/./../bar/./baz/index.html';
        $expected = 'http://example.com/bar/baz/index.html';
        $actual = $this->subject->normalizedUrl($url);
        $this->assertEquals($expected, $actual);
    }

    public function testAvailableVideos()
    {
        $expected = array('movie');
        $actual = $this->subject->availableVideos();
        $this->assertEquals($expected, $actual);
    }

    public function testVideoFiles()
    {
        $expected = array(
            $this->mediaFolder . 'movie.webm' => 'webm',
            $this->mediaFolder . 'movie.mp4' => 'mp4'
        );
        $actual = $this->subject->videoFiles('movie');
        $this->assertEquals($expected, $actual);
    }

    public function testNoVideoFiles()
    {
        $actual = $this->subject->videoFiles('foo');
        $this->assertEmpty($actual);
    }

    public function testPosterFile()
    {
        $expected = $this->mediaFolder . 'movie.jpg';
        $actual = $this->subject->posterFile('movie');
        $this->assertEquals($expected, $actual);
    }

    public function testNoPosterFile()
    {
        $actual = $this->subject->posterFile('foo');
        $this->assertFalse($actual);
    }

    public function testSubtitleFile()
    {
        $expected = $this->mediaFolder . 'movie.vtt';
        $actual = $this->subject->subtitleFile('movie');
        $this->assertEquals($expected, $actual);
    }

    public function testNoSubtitleFile()
    {
        $actual = $this->subject->subtitleFile('foo');
        $this->assertFalse($actual);
    }

    /**
     * @return array
     */
    public function dataForGetOptions()
    {
        return array(
            array(
                '',
                array(
                    'autoplay' => '0',
                    'controls' => '1',
                    'height' => '288',
                    'loop' => '0',
                    'preload' => 'auto',
                    'resize' => 'no',
                    'width' => '512'
                )
            ),
            array(
                'autoplay=1&controls=0&height=360&loop=1'
                    . '&preload=metadata&resize=full&width=640',
                array(
                    'autoplay' => '1',
                    'controls' => '0',
                    'height' => '360',
                    'loop' => '1',
                    'preload' => 'metadata',
                    'resize' => 'full',
                    'width' => '640'
                )
            )
        );
    }

    /**
     * @param string $query
     * @param array $expected
     * @dataProvider dataForGetOptions
     */
    public function testGetOptions($query, $expected)
    {
        $actual = $this->subject->getOptions($query);
        $this->assertEquals($expected, $actual);
    }
}
