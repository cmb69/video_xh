<?php

/**
 * Testing the video model.
 *
 * PHP version 5
 *
 * @category  Testing
 * @package   Video
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2012-2015 Christoph M. Becker <http://3-magi.net/>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link      http://3-magi.net/?CMSimple_XH/Video_XH
 */

require_once './vendor/autoload.php';

require './classes/Model.php';

use org\bovigo\vfs\vfsStreamWrapper;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStream;

/**
 * Testing the video model.
 *
 * @category Testing
 * @package  Video
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Video_XH
 */
class ModelTest extends PHPUnit_Framework_TestCase
{
    /**
     * The base folder.
     *
     * @var string
     */
    protected $baseFolder;

    /**
     * The media folder.
     *
     * @var string
     */
    protected $mediaFolder;

    /**
     * The test subject.
     *
     * @var Video_Model
     */
    protected $subject;

    /**
     * Sets up the test fixture.
     *
     * @return void
     */
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
        $this->subject = new Video_Model($folders, $config);
        mkdir($folders['media'], 0777, true);
        file_put_contents($this->mediaFolder . 'movie.avi', '');
        file_put_contents($this->mediaFolder . 'movie.jpg', '');
        file_put_contents($this->mediaFolder . 'movie.mp4', '');
        file_put_contents($this->mediaFolder . 'movie.vtt', '');
        file_put_contents($this->mediaFolder . 'movie.webm', '');
    }

    /**
     * Tests ::normalizedUrl().
     *
     * @return void
     */
    public function testNormalizedUrl()
    {
        $url = 'http://example.com/foo/./../bar/./baz/index.html';
        $expected = 'http://example.com/bar/baz/index.html';
        $actual = $this->subject->normalizedUrl($url);
        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests ::videoFolder().
     *
     * @return void
     */
    public function testVideoFolder()
    {
        $expected = $this->baseFolder . 'userfiles/media/';
        $actual = $this->subject->videoFolder();
        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests a non standard video folder.
     *
     * @return void
     */
    public function testNonStandardVideoFolder()
    {
        $folders = array('base' => './');
        $config = array('folder_video' => 'foo');
        $expected = './foo/';
        $subject = new Video_Model($folders, $config);
        $actual = $subject->videoFolder();
        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests a backward compatible video folder.
     *
     * @return void
     */
    public function testBCVideoFolder()
    {
        $folders = array('downloads' => './downloads/');
        $config = array('folder_video' => '');
        $expected = './downloads/';
        $subject = new Video_Model($folders, $config);
        $actual = $subject->videoFolder();
        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests ::availableVideos().
     *
     * @return void
     */
    public function testAvailableVideos()
    {
        $expected = array('movie');
        $actual = $this->subject->availableVideos();
        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests ::videoFiles().
     *
     * @return void
     */
    public function testVideoFiles()
    {
        $expected = array(
            $this->mediaFolder . 'movie.webm' => 'webm',
            $this->mediaFolder . 'movie.mp4' => 'mp4'
        );
        $actual = $this->subject->videoFiles('movie');
        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests no video files.
     *
     * @return void
     */
    public function testNoVideoFiles()
    {
        $actual = $this->subject->videoFiles('foo');
        $this->assertEmpty($actual);
    }

    /**
     * Tests ::posterFile().
     *
     * @return void
     */
    public function testPosterFile()
    {
        $expected = $this->mediaFolder . 'movie.jpg';
        $actual = $this->subject->posterFile('movie');
        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests no poster file.
     *
     * @return void
     */
    public function testNoPosterFile()
    {
        $actual = $this->subject->posterFile('foo');
        $this->assertFalse($actual);
    }

    /**
     * Tests ::subtitleFile().
     *
     * @return void
     */
    public function testSubtitleFile()
    {
        $expected = $this->mediaFolder . 'movie.vtt';
        $actual = $this->subject->subtitleFile('movie');
        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests no subtitle file.
     *
     * @return void
     */
    public function testNoSubtitleFile()
    {
        $actual = $this->subject->subtitleFile('foo');
        $this->assertFalse($actual);
    }

    /**
     * Returns data for ::testGetOptions().
     *
     * @return void
     */
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
     * Tests ::getOptions().
     *
     * @param string $query    A query string.
     * @param array  $expected An array of expected options.
     *
     * @return void
     *
     * @dataProvider dataForGetOptions
     */
    public function testGetOptions($query, $expected)
    {
        $actual = $this->subject->getOptions($query);
        $this->assertEquals($expected, $actual);
    }
}

?>
