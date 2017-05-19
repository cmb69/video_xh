<?php

/**
 * The video model.
 *
 * PHP versions 4 and 5
 *
 * @category  CMSimple_XH
 * @package   Video
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2012-2017 Christoph M. Becker <http://3-magi.net/>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link      http://3-magi.net/?CMSimple_XH/Video_XH
 */

namespace Video;

/**
 * The video model class.
 *
 * @category CMSimple_XH
 * @package  Video
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Video_XH
 */
class Model
{
    /**
     * The folder paths.
     *
     * @var array
     */
    private $folders;

    /**
     * The configuration options.
     *
     * @var array
     */
    private $config;

    /**
     * Initializes a new model object.
     *
     * @param array $folders An array of folder paths.
     * @param array $config  Configuration options.
     *
     * @return void
     */
    public function __construct($folders, $config)
    {
        $this->folders = $folders;
        $this->config = $config;
    }

    /**
     * Returns a URL in normalized form (i.e. with ./ and ../ resolved).
     *
     * @param string $url An absolute URL.
     *
     * @return string
     */
    public function normalizedUrl($url)
    {
        $parts = explode('/', $url);
        $i = 0;
        while ($i < count($parts)) {
            switch ($parts[$i]) {
            case '.':
                array_splice($parts, $i, 1);
                break;
            case '..':
                array_splice($parts, $i - 1, 2);
                $i--;
                break;
            default:
                $i++;
            }
        }
        return implode('/', $parts);
    }

    /**
     * Returns an associative array (file extension => MIME type) of the
     * recognized video formats.
     *
     * @return array
     */
    private function types()
    {
        return array('webm' => 'webm', 'mp4' => 'mp4', 'ogv' => 'ogg');
    }

    /**
     * Returns an array of recognized video file extensions.
     *
     * @return array
     */
    private function extensions()
    {
        return array_keys($this->types());
    }

    /**
     * Returns the relative path to the video folder.
     *
     * @return string
     */
    public function videoFolder()
    {
        if (!empty($this->config['folder_video'])) {
            $folder = $this->folders['base'] . $this->config['folder_video'];
            $folder = rtrim($folder, '/') . '/';
        } elseif (isset($this->folders['media'])) {
            $folder = $this->folders['media'];
        } else {
            $folder = $this->folders['downloads'];
        }
        return $folder;
    }

    /**
     * Returns all recognized videos in the video folder.
     *
     * @return array
     */
    public function availableVideos()
    {
        $dirHandle = opendir($this->videoFolder());
        $videos = array();
        if ($dirHandle) {
            while (($file = readdir($dirHandle)) !== false) {
                $pathinfo = pathinfo($file);
                if (!isset($pathinfo['extension'])) {
                    continue;
                }
                $basename = $pathinfo['basename'];
                $extension = $pathinfo['extension'];
                if (in_array($extension, $this->extensions())) {
                    $name = substr($basename, 0, -(strlen($extension) + 1));
                    $videos[] = $name;
                }
            }
        }
        sort($videos);
        $videos = array_unique($videos);
        return $videos;
    }

    /**
     * Returns a map of filenames to types.
     *
     * @param string $name Name of the video file without extension.
     *
     * @return array
     */
    public function videoFiles($name)
    {
        $dirname = $this->videoFolder();
        $files = array();
        foreach (array_keys($this->types()) as $extension) {
            $filename = $dirname . $name . '.' . $extension;
            if (file_exists($filename)) {
                $files[$filename] = $extension;
            }
        }
        return $files;
    }

    /**
     * Returns the filename of the poster; <var>false</var> if no poster is
     * available.
     *
     * @param string $name A video name.
     *
     * @return string
     */
    public function posterFile($name)
    {
        $filename = $this->videoFolder() . $name . '.jpg';
        return file_exists($filename) ? $filename : false;
    }

    /**
     * Returns the path of an appropriate subtitle file; <var>false</var> otherwise.
     *
     * @param string $name A video name.
     *
     * @return string
     *
     * @global string The current language.
     */
    public function subtitleFile($name)
    {
        global $sl;

        $dirname = $this->videoFolder();
        $suffixes = array("_$sl.vtt", "_$sl.srt", '.vtt', '.srt');
        foreach ($suffixes as $suffix) {
            $filename = $dirname . $name . $suffix;
            if (file_exists($filename)) {
                return $filename;
            }
        }
        return false;
    }

    /**
     * Returns all options.
     *
     * Defaults are taken from $plugin_cf['video']['default_*'].
     * Those will be overridden with the options in $query.
     *
     * @param string $query The options given like a query string.
     *
     * @return array
     */
    public function getOptions($query)
    {
        $validOptions = array(
            'autoplay', 'centered', 'controls', 'height', 'loop', 'preload',
            'resize', 'width'
        );
        parse_str($query, $options);
        $res = array();
        foreach ($validOptions as $key) {
            if (isset($options[$key])) {
                $res[$key] = ($options[$key] === '') ? true : $options[$key];
            } else {
                $res[$key] = $this->config["default_$key"];
            }
        }
        return $res;
    }

}

?>
