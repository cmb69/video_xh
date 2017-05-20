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

class Model
{
    /**
     * @var string
     */
    private $videoFolder;

    /**
     * @var array
     */
    private $config;

    /**
     * @param array $folder
     * @param array $config
     */
    public function __construct($folder, $config)
    {
        $this->videoFolder = $folder;
        $this->config = $config;
    }

    /**
     * @param string $url
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
     * @return array
     */
    private function types()
    {
        return array('webm' => 'webm', 'mp4' => 'mp4', 'ogv' => 'ogg');
    }

    /**
     * @return array
     */
    private function extensions()
    {
        return array_keys($this->types());
    }

    /**
     * @return array
     */
    public function availableVideos()
    {
        $dirHandle = opendir($this->videoFolder);
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
     * @param string $name
     * @return array
     */
    public function videoFiles($name)
    {
        $dirname = $this->videoFolder;
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
     * @param string $name
     * @return string
     */
    public function posterFile($name)
    {
        $filename = $this->videoFolder . $name . '.jpg';
        return file_exists($filename) ? $filename : false;
    }

    /**
     * @param string $name
     * @return string
     */
    public function subtitleFile($name)
    {
        global $sl;

        $dirname = $this->videoFolder;
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
     * @param string $query
     * @return array
     */
    public function getOptions($query)
    {
        $validOptions = array(
            'autoplay', 'controls', 'height', 'loop', 'preload',
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
