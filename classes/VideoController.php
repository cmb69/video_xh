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

class VideoController extends Controller
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $options;

    public function __construct($name, $options = '')
    {
        parent::__construct();
        $this->name = $name;
        $this->options = $this->model->getOptions(html_entity_decode($options, ENT_QUOTES, 'UTF-8'));
    }

    public function defaultAction()
    {
        global $sl;

        $files = $this->model->videoFiles($this->name);
        if (!empty($files)) {
            $view = new View('video');
            $view->attributes = new HtmlString($this->videoAttributes());
            $sources = [];
            foreach ($files as $filename => $type) {
                $url = $this->model->normalizedUrl(CMSIMPLE_URL . $filename);
                $sources[] = (object) ['url' => $url, 'type' => $type];
            }
            $view->sources = $sources;
            $view->track = $this->model->subtitleFile($this->name);
            $view->langCode = $sl;
            $filename = array_keys($files)[0];
            $view->filename = $filename;
            $view->downloadLink = new HtmlString($this->downloadLink($filename));
            $view->render();
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
            . ($poster ? ' poster="' . $poster . '"' : '')
            . ' ' . $this->resizeStyle();
        return $attributes;
    }

    /**
     * @return string
     */
    private function resizeStyle()
    {
        switch ($this->options['resize']) {
            case 'full':
                $style = 'style="width:100%"';
                break;
            case 'shrink':
                $style = 'style="max-width:100%"';
                break;
            default:
                $style = '';
        }
        return $style;
    }

    /**
     * @param string $filename
     * @return string
     */
    private function downloadLink($filename)
    {
        $style = $this->resizeStyle();
        $basename = basename($filename);
        $download = sprintf($this->lang['label_download'], $basename);
        $poster = $this->model->posterFile($this->name);
        if ($poster) {
            $link = "<img src=\"$poster\" alt=\"$download\" title=\"$download\" $style>";
        } else {
            $link = $download;
        }
        return $link;
    }
}
