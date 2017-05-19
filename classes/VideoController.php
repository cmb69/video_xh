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
    private $name;

    private $options;

    public function __construct($name, $options = '')
    {
        parent::__construct();
        $this->name = $name;
        $this->options = $options;
    }

    public function defaultAction()
    {
        global $sl;
        static $run = 0;
    
        if (!$run) {
            Video_includeJs();
        }
        $run++;
        $files = $this->model->videoFiles($this->name);
    
        if (!empty($files)) {
            $opts = $this->model->getOptions(html_entity_decode($this->options, ENT_QUOTES, 'UTF-8'));
            $attributes = $this->videoAttributes($this->name, $opts);
            $o = <<<EOT
<!-- Video_XH: $this->name -->
<video id="video_$run" $attributes>

EOT;
            foreach ($files as $filename => $type) {
                $url = $this->model->normalizedUrl(CMSIMPLE_URL . $filename);
                $o .= <<<EOT
    <source src="$url" type="video/$type">

EOT;
            }
            $filename = $this->model->subtitleFile($this->name);
            if ($filename) {
                $o .= <<<EOT
    <track src="$filename" srclang="$sl" label="{$this->lang['subtitle_label']}">

EOT;
            }
            $filenames = array_keys($files);
            $filename = $filenames[0];
            $style = $this->resizeStyle($opts['resize']);
            $link = $this->downloadLink($this->name, $filename, $style);
            $o .= <<<EOT
    <a href="$filename">$link</a>

EOT;
            $o .= <<<EOT
</video>

EOT;
        } else {
            $o = XH_message('fail', $this->lang['error_missing'], $this->name);
        }
        echo $o;
    }

    /**
     * @param string $name
     * @param array $options
     * @return string
     */
    private function videoAttributes($name, $options)
    {
        $poster = $this->model->posterFile($name);
        $attributes = 'class=""'
            . (!empty($options['controls']) ? ' controls="controls"' : '')
            . (!empty($options['autoplay']) ? ' autoplay="autoplay"' : '')
            . (!empty($options['loop']) ? ' loop="loop"' : '')
            . ' preload="' . $options['preload'] . '"'
            . ' width="' . $options['width'] . '"'
            . ' height="' . $options['height'] . '"'
            . ($poster ? ' poster="' . $poster . '"' : '')
            . ' ' . $this->resizeStyle($options['resize']);
        return $attributes;
    }

    /**
     * @param string $resizeMode
     * @return string
     */
    private function resizeStyle($resizeMode)
    {
        switch ($resizeMode) {
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
     * @param string $videoname
     * @param string $filename
     * @param string $style
     * @return string
     */
    private function downloadLink($videoname, $filename, $style)
    {
        $basename = basename($filename);
        $download = sprintf($this->lang['label_download'], $basename);
        $poster = $this->model->posterFile($videoname);
        if ($poster) {
            $link = "<img src=\"$poster\" alt=\"$download\" title=\"$download\" $style>";
        } else {
            $link = $download;
        }
        return $link;
    }
}
