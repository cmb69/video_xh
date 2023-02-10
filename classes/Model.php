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

namespace Video;

class Model
{
    const TYPES = array('webm' => 'webm', 'mp4' => 'mp4', 'ogv' => 'ogg');

    /** @var string */
    private $videoFolder;

    /** @var array<string> */
    private $config;

    /** @var string $sl */
    private $sl;

    /** @param array<string> $config */
    public function __construct(string $folder, array $config, string $sl)
    {
        $this->videoFolder = $folder;
        $this->config = $config;
        $this->sl = $sl;
    }

    public function normalizedUrl(string $url): string
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

    /** @return array<string> */
    private function extensions(): array
    {
        return array_keys(self::TYPES);
    }

    /** @return array<string> */
    public function availableVideos(): array
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

    /** @return array<string> */
    public function videoFiles(string $name): array
    {
        $dirname = $this->videoFolder;
        $files = array();
        foreach (array_keys(self::TYPES) as $extension) {
            $filename = $dirname . $name . '.' . $extension;
            if (file_exists($filename)) {
                $files[$filename] = $extension;
            }
        }
        return $files;
    }

    public function posterFile(string $name): ?string
    {
        $filename = $this->videoFolder . $name . '.jpg';
        return file_exists($filename) ? $filename : null;
    }

    public function subtitleFile(string $name): ?string
    {
        $dirname = $this->videoFolder;
        $suffixes = array("_{$this->sl}.vtt", "_{$this->sl}.srt", '.vtt', '.srt');
        foreach ($suffixes as $suffix) {
            $filename = $dirname . $name . $suffix;
            if (file_exists($filename)) {
                return $filename;
            }
        }
        return null;
    }

    public function uploadDate(string $filename): int
    {
        return (int) filectime($filename);
    }

    /** @return array<mixed> */
    public function getOptions(string $query): array
    {
        $validOptions = array(
            'autoplay', 'class', 'controls', 'description', 'height', 'loop', 'preload',
            'title', 'width'
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
