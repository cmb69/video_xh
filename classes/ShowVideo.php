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

use Plib\Request;
use Plib\Response;
use Plib\View;
use Video\Value\Video;
use Video\Infra\VideoFinder;

class ShowVideo
{
    /** @var array<string,string> */
    private $conf;

    /** @var VideoFinder */
    private $videoFinder;

    /** @var View */
    private $view;

    /** @param array<string,string> $conf */
    public function __construct(
        array $conf,
        VideoFinder $videoFinder,
        View $view
    ) {
        $this->conf = $conf;
        $this->videoFinder = $videoFinder;
        $this->view = $view;
    }

    public function __invoke(Request $request, string $name, string $options = ''): Response
    {
        $options = $this->parseOptions(html_entity_decode($options, ENT_QUOTES, 'UTF-8'));
        $video = $this->videoFinder->find($name, $request->language());
        if ($video !== null) {
            $filename = $video->filename();
            $sources = [];
            foreach ($video->sources() as $url => $type) {
                $sources[] = ['url' => $url, 'type' => $type];
            }
            $data = [
                "className" => $options['class'],
                "attributes" => $this->videoAttributes($video, $options),
                "sources" => $sources,
                "track" => $video->subtitle(),
                "langCode" => $request->language(),
                "contentUrl" => $request->url()->path($filename)->absolute(),
                "filename" => $filename,
                "downloadLink" => $this->downloadLink($video, $options, $filename),
                "title" => $options['title'],
                "description" => $options['description'],
                "uploadDate" => date('c', $video->date()),
            ];
            $poster = $video->poster();
            if ($poster) {
                $data["thumbnailUrl"] = $request->url()->path($poster)->absolute();
            }
            return Response::create($this->view->render('video', $data));
        } else {
            return Response::create($this->view->message('fail', 'error_missing', $name));
        }
    }

    /** @return array<string,string|true> */
    private function parseOptions(string $query): array
    {
        $validOptions = [
            'autoplay', 'class', 'controls', 'description', 'height', 'loop', 'preload',
            'title', 'width'
        ];
        parse_str($query, $options);
        $res = array();
        foreach ($validOptions as $key) {
            if (isset($options[$key])) {
                assert(is_string($options[$key])); // @todo actually handle this
                $res[$key] = ($options[$key] === '') ? true : $options[$key];
            } else {
                $res[$key] = $this->conf["default_$key"];
            }
        }
        return $res;
    }

    /** @param array<string,string|true> $options */
    private function videoAttributes(Video $video, array $options): string
    {
        $poster = $video->poster();
        $attributes = 'class=""'
            . (!empty($options['controls']) ? ' controls="controls"' : '')
            . (!empty($options['autoplay']) ? ' autoplay="autoplay"' : '')
            . (!empty($options['loop']) ? ' loop="loop"' : '')
            . ' preload="' . $options['preload'] . '"'
            . ' width="' . $options['width'] . '"'
            . ' height="' . $options['height'] . '"'
            . ($poster ? ' poster="' . $poster . '"' : '');
        return $attributes;
    }

    /** @param array<string,string|true> $options */
    private function downloadLink(Video $video, array $options, string $filename): string
    {
        $basename = basename($filename);
        $download = $this->view->text('label_download', $basename);
        $poster = $video->poster();
        if ($poster) {
            $link = "<img src=\"$poster\" alt=\"$download\" title=\"$download\" class=\"{$options['class']}\">";
        } else {
            $link = $download;
        }
        return $link;
    }
}
