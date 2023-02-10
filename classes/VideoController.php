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

    /** @var array<string> */
    private $lang;

    /** @var string */
    private $sl;

    /** @var Model */
    private $model;

    /**
     * @var string
     */
    private $name;

    /**
     * @var array<mixed>
     */
    private $options;

    /**
     * @param array<string> $lang
     */
    public function __construct(
        string $pluginFolder,
        array $lang,
        string $sl,
        Model $model,
        string $name,
        string $options = ''
    ) {
        $this->pluginFolder = $pluginFolder;
        $this->lang = $lang;
        $this->sl = $sl;
        $this->model = $model;
        $this->name = $name;
        $this->options = $this->model->getOptions(html_entity_decode($options, ENT_QUOTES, 'UTF-8'));
    }

    public function defaultAction(): Response
    {
        $files = $this->model->videoFiles($this->name);
        if (!empty($files)) {
            $filename = key($files);
            $sources = [];
            foreach ($files as $url => $type) {
                $sources[] = (object) ['url' => $url, 'type' => $type];
            }
            $view = new View("{$this->pluginFolder}views/", $this->lang);
            $data = [
                "className" => $this->options['class'],
                "attributes" => new HtmlString($this->videoAttributes()),
                "sources" => $sources,
                "track" => $this->model->subtitleFile($this->name),
                "langCode" => $this->sl,
                "contentUrl" => $this->model->normalizedUrl(CMSIMPLE_URL . $filename),
                "filename" => $filename,
                "downloadLink" => new HtmlString($this->downloadLink($filename)),
                "title" => $this->options['title'],
                "description" => $this->options['description'],
                "uploadDate" => date('c', $this->model->uploadDate($filename)),
            ];
            $poster = $this->model->posterFile($this->name);
            if ($poster) {
                $data["thumbnailUrl"] = $this->model->normalizedUrl(CMSIMPLE_URL . $poster);
            }
            return new Response($view->render('video', $data));
        } else {
            return new Response(XH_message('fail', $this->lang['error_missing'], $this->name));
        }
    }

    private function videoAttributes(): string
    {
        $poster = $this->model->posterFile($this->name);
        $attributes = 'class=""'
            . (!empty($this->options['controls']) ? ' controls="controls"' : '')
            . (!empty($this->options['autoplay']) ? ' autoplay="autoplay"' : '')
            . (!empty($this->options['loop']) ? ' loop="loop"' : '')
            . ' preload="' . $this->options['preload'] . '"'
            . ' width="' . $this->options['width'] . '"'
            . ' height="' . $this->options['height'] . '"'
            . ($poster ? ' poster="' . $poster . '"' : '');
        return $attributes;
    }

    private function downloadLink(string $filename): string
    {
        $basename = basename($filename);
        $download = sprintf($this->lang['label_download'], $basename);
        $poster = $this->model->posterFile($this->name);
        if ($poster) {
            $link = "<img src=\"$poster\" alt=\"$download\" title=\"$download\" class=\"{$this->options['class']}\">";
        } else {
            $link = $download;
        }
        return $link;
    }
}
