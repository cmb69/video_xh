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
    /** @var array<string> */
    private $lang;

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
     * @param string $name
     * @param string $options
     */
    public function __construct(array $lang, Model $model, $name, $options = '')
    {
        $this->lang = $lang;
        $this->model = $model;
        $this->name = $name;
        $this->options = $this->model->getOptions(html_entity_decode($options, ENT_QUOTES, 'UTF-8'));
    }

    /** @return void */
    public function defaultAction()
    {
        global $sl, $pth, $plugin_tx;

        $files = $this->model->videoFiles($this->name);
        if (!empty($files)) {
            $filename = key($files);
            $sources = [];
            foreach ($files as $url => $type) {
                $sources[] = (object) ['url' => $url, 'type' => $type];
            }
            $view = new View("{$pth['folder']['plugins']}video/views/", $plugin_tx['video']);
            $data = [
                "className" => $this->options['class'],
                "attributes" => new HtmlString($this->videoAttributes()),
                "sources" => $sources,
                "track" => $this->model->subtitleFile($this->name),
                "langCode" => $sl,
                "contentUrl" => $this->model->normalizedUrl(CMSIMPLE_URL . $filename),
                "filename" => $filename,
                "downloadLink" => new HtmlString($this->downloadLink($filename)),
                "title" => $this->options['title'],
                "description" => $this->options['description'],
                "uploadDate" => date('c', (int) filectime($filename)),
            ];
            $poster = $this->model->posterFile($this->name);
            if ($poster) {
                $data["thumbnailUrl"] = $this->model->normalizedUrl(CMSIMPLE_URL . $poster);
            }
            $view->render('video', $data);
        } else {
            echo XH_message('fail', $this->lang['error_missing'], $this->name);
        }
    }

    /**
     * @return string
     */
    private function videoAttributes()
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

    /**
     * @param string $filename
     * @return string
     */
    private function downloadLink($filename)
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
