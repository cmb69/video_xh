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

namespace Video\Model;

class VideoFinder
{
    private const TYPES = array('webm' => 'webm', 'mp4' => 'mp4', 'ogv' => 'ogg');

    /** @var string */
    private $videoFolder;

    public function __construct(string $folder)
    {
        $this->videoFolder = $folder;
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
                if (in_array($extension, array_keys(self::TYPES))) {
                    $name = substr($basename, 0, -(strlen($extension) + 1));
                    $videos[] = $name;
                }
            }
        }
        sort($videos);
        $videos = array_unique($videos);
        return $videos;
    }

    public function find(string $name, string $language): ?Video
    {
        $sources = $this->videoFiles($name);
        if (empty($sources)) {
            return null;
        }
        return new Video(
            $sources,
            $this->posterFile($name),
            $this->subtitleFile($name, $language),
            $this->uploadDate(key($sources))
        );
    }

    /** @return array<string,string> */
    private function videoFiles(string $name): array
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

    private function posterFile(string $name): ?string
    {
        $filename = $this->videoFolder . $name . '.jpg';
        return file_exists($filename) ? $filename : null;
    }

    private function subtitleFile(string $name, string $language): ?string
    {
        $dirname = $this->videoFolder;
        $suffixes = array("_{$language}.vtt", "_{$language}.srt", '.vtt', '.srt');
        foreach ($suffixes as $suffix) {
            $filename = $dirname . $name . $suffix;
            if (file_exists($filename)) {
                return $filename;
            }
        }
        return null;
    }

    private function uploadDate(string $filename): int
    {
        return (int) filectime($filename);
    }
}
