<?php

/**
 * Testing the video model.
 *
 * PHP version 5
 *
 * @category  Testing
 * @package   Video
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2012-2013 Christoph M. Becker <http://3-magi.net/>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @version   SVN: $Id$
 * @link      http://3-magi.net/?CMSimple_XH/Video_XH
 */

 /**
  * The file system mock objects.
  */
require_once 'vfsStream/vfsStream.php';

/**
 * The class under test.
 */
require './classes/Model.php';

/**
 * A test case for the video model.
 *
 * @category Testing
 * @package  Video
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Video_XH
 */
class ModelTest extends PHPUnit_Framework_TestCase
{
    protected $baseFolder;

    protected $mediaFolder;

    protected $model;

    public function setUp()
    {
        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot(new vfsStreamDirectory('test'));
        $this->baseFolder = vfsStream::url('test') . '/';
        $this->mediaFolder = $this->baseFolder . 'userfiles/media/';
        $folders = array(
            'base' => $this->baseFolder,
            'media' => $this->mediaFolder,
            'downloads' => $this->baseFolder . 'userfiles/downloads/',
            'plugins' => $this->baseFolder . 'plugins/'
        );
        $config = array(
            'folder_video' => '',
            'default_preload' => 'auto',
            'default_autoplay' => '0',
            'default_loop' => '0',
            'default_controls' => '1',
            'default_centered' => '1',
            'default_width' => '512',
            'default_height' => '288',
            'default_resize' => 'no'
        );
        $this->model = new Video_Model($folders, $config);
        mkdir($folders['media'], 0777, true);
        file_put_contents($this->mediaFolder . 'movie.avi', '');
        file_put_contents($this->mediaFolder . 'movie.jpg', '');
        file_put_contents($this->mediaFolder . 'movie.mp4', '');
        file_put_contents($this->mediaFolder . 'movie.vtt', '');
        file_put_contents($this->mediaFolder . 'movie.webm', '');
    }

    public function testNormalizedUrl()
    {
        $url = 'http://example.com/foo/./../bar/./baz/index.html';
        $expected = 'http://example.com/bar/baz/index.html';
        $actual = $this->model->normalizedUrl($url);
        $this->assertEquals($expected, $actual);
    }

    public function testVideoFolder()
    {
        $expected = $this->baseFolder . 'userfiles/media/';
        $actual = $this->model->videoFolder();
        $this->assertEquals($expected, $actual);
    }

    public function testNonStandardVideoFolder()
    {
        $folders = array('base' => './');
        $config = array('folder_video' => 'foo');
        $expected = './foo/';
        $model = new Video_Model($folders, $config);
        $actual = $model->videoFolder();
        $this->assertEquals($expected, $actual);
    }

    public function testBCVideoFolder()
    {
        $folders = array('downloads' => './downloads/');
        $config = array('folder_video' => '');
        $expected = './downloads/';
        $model = new Video_Model($folders, $config);
        $actual = $model->videoFolder();
        $this->assertEquals($expected, $actual);
    }

    public function testAvailableVideos()
    {
        $expected = array('movie');
        $actual = $this->model->availableVideos();
        $this->assertEquals($expected, $actual);
    }

    public function testVideoFiles()
    {
        $expected = array(
            $this->mediaFolder . 'movie.webm' => 'webm',
            $this->mediaFolder . 'movie.mp4' => 'mp4'
        );
        $actual = $this->model->videoFiles('movie');
        $this->assertEquals($expected, $actual);
    }

    public function testNoVideoFiles()
    {
        $actual = $this->model->videoFiles('foo');
        $this->assertEmpty($actual);
    }

    public function testPosterFile()
    {
        $expected = $this->mediaFolder . 'movie.jpg';
        $actual = $this->model->posterFile('movie');
        $this->assertEquals($expected, $actual);
    }

    public function testNoPosterFile()
    {
        $actual = $this->model->posterFile('foo');
        $this->assertFalse($actual);
    }

    public function testSubtitleFile()
    {
        $expected = $this->mediaFolder . 'movie.vtt';
        $actual = $this->model->subtitleFile('movie');
        $this->assertEquals($expected, $actual);
    }

    public function testNoSubtitleFile()
    {
        $actual = $this->model->subtitleFile('foo');
        $this->assertFalse($actual);
    }

    public function dataForGetOptions()
    {
        return array(
            array(
                '',
                array(
                    'autoplay' => '0',
                    'centered' => '1',
                    'controls' => '1',
                    'height' => '288',
                    'loop' => '0',
                    'preload' => 'auto',
                    'resize' => 'no',
                    'width' => '512'
                )
            ),
            array(
                'autoplay=1&centered=0&controls=0&height=360&loop=1'
                    . '&preload=metadata&resize=full&width=640',
                array(
                    'autoplay' => '1',
                    'centered' => '0',
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
     * @dataProvider dataForGetOptions
     */
    public function testGetOptions($query, $expected)
    {
        $actual = $this->model->getOptions($query);
        $this->assertEquals($expected, $actual);
    }
}


?>
