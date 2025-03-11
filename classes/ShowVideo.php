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

use Plib\Response;
use Video\Value\Video;
use Video\Infra\Url;
use Video\Infra\View;
use Video\Infra\VideoFinder;
use Video\Logic\OptionParser;

class ShowVideo
{
    /** @var string */
    private $pluginFolder;

    /** @var array<string,string> */
    private $lang;

    /** @var string */
    private $sl;

    /** @var OptionParser */
    private $optionParser;

    /** @var VideoFinder */
    private $videoFinder;

    /** @param array<string,string> $lang */
    public function __construct(
        string $pluginFolder,
        array $lang,
        string $sl,
        OptionParser $optionParser,
        VideoFinder $videoFinder
    ) {
        $this->pluginFolder = $pluginFolder;
        $this->lang = $lang;
        $this->sl = $sl;
        $this->optionParser = $optionParser;
        $this->videoFinder = $videoFinder;
    }

    public function __invoke(string $name, string $options = ''): Response
    {
        $options = $this->optionParser->parse(html_entity_decode($options, ENT_QUOTES, 'UTF-8'));
        $video = $this->videoFinder->find($name);
        if ($video !== null) {
            $filename = $video->filename();
            $sources = [];
            foreach ($video->sources() as $url => $type) {
                $sources[] = ['url' => $url, 'type' => $type];
            }
            $view = new View("{$this->pluginFolder}views/", $this->lang);
            $data = [
                "className" => $options['class'],
                "attributes" => $this->videoAttributes($video, $options),
                "sources" => $sources,
                "track" => $video->subtitle(),
                "langCode" => $this->sl,
                "contentUrl" => new Url($filename),
                "filename" => $filename,
                "downloadLink" => $this->downloadLink($video, $options, $filename),
                "title" => $options['title'],
                "description" => $options['description'],
                "uploadDate" => date('c', $video->date()),
            ];
            $poster = $video->poster();
            if ($poster) {
                $data["thumbnailUrl"] = new Url($poster);
            }
            return Response::create($view->render('video', $data));
        } else {
            return Response::create(XH_message('fail', $this->lang['error_missing'], $name));
        }
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
        $download = sprintf($this->lang['label_download'], $basename);
        $poster = $video->poster();
        if ($poster) {
            $link = "<img src=\"$poster\" alt=\"$download\" title=\"$download\" class=\"{$options['class']}\">";
        } else {
            $link = $download;
        }
        return $link;
    }
}
