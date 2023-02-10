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

class VideoController
{
    /** @var string */
    private $pluginFolder;

    /** @var array<string,string> */
    private $lang;

    /** @var string */
    private $sl;

    /** @var Model */
    private $model;

    /** @param array<string,string> $lang */
    public function __construct(
        string $pluginFolder,
        array $lang,
        string $sl,
        Model $model
    ) {
        $this->pluginFolder = $pluginFolder;
        $this->lang = $lang;
        $this->sl = $sl;
        $this->model = $model;
    }

    public function defaultAction(string $name, string $options = ''): Response
    {
        $options = $this->model->getOptions(html_entity_decode($options, ENT_QUOTES, 'UTF-8'));
        $files = $this->model->videoFiles($name);
        if (!empty($files)) {
            $filename = key($files);
            $sources = [];
            foreach ($files as $url => $type) {
                $sources[] = ['url' => $url, 'type' => $type];
            }
            $view = new View("{$this->pluginFolder}views/", $this->lang);
            $data = [
                "className" => $options['class'],
                "attributes" => $this->videoAttributes($name, $options),
                "sources" => $sources,
                "track" => $this->model->subtitleFile($name),
                "langCode" => $this->sl,
                "contentUrl" => $this->model->normalizedUrl(CMSIMPLE_URL . $filename),
                "filename" => $filename,
                "downloadLink" => $this->downloadLink($name, $options, $filename),
                "title" => $options['title'],
                "description" => $options['description'],
                "uploadDate" => date('c', $this->model->uploadDate($filename)),
            ];
            $poster = $this->model->posterFile($name);
            if ($poster) {
                $data["thumbnailUrl"] = $this->model->normalizedUrl(CMSIMPLE_URL . $poster);
            }
            return new Response($view->render('video', $data));
        } else {
            return new Response(XH_message('fail', $this->lang['error_missing'], $name));
        }
    }

    /** @param array<string,string|true> $options */
    private function videoAttributes(string $name, array $options): string
    {
        $poster = $this->model->posterFile($name);
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
    private function downloadLink(string $name, array $options, string $filename): string
    {
        $basename = basename($filename);
        $download = sprintf($this->lang['label_download'], $basename);
        $poster = $this->model->posterFile($name);
        if ($poster) {
            $link = "<img src=\"$poster\" alt=\"$download\" title=\"$download\" class=\"{$options['class']}\">";
        } else {
            $link = $download;
        }
        return $link;
    }
}
