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

/*
 * Prevent direct access.
 */
if (!defined('CMSIMPLE_XH_VERSION')) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}

define('VIDEO_VERSION', '@VIDEO_VERSION@');

function Video_includeJs()
{
    global $pth, $hjs;
    static $again = false;

    if (!$again) {
        $jsPath = $pth['folder']['plugins'] . 'video/video.js';
        $hjs .= "<script type=\"text/javascript\" src=\"$jsPath\"></script>"
            . PHP_EOL;
        $again = true;
    }
}

/**
 * @param string $resizeMode
 * @return string
 */
function Video_resizeStyle($resizeMode)
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
function Video_downloadLink($videoname, $filename, $style)
{
    global $plugin_tx, $_Video;

    $basename = basename($filename);
    $download = sprintf($plugin_tx['video']['label_download'], $basename);
    $poster = $_Video->posterFile($videoname);
    if ($poster) {
        $link = "<img src=\"$poster\" alt=\"$download\" title=\"$download\" $style>";
    } else {
        $link = $download;
    }
    return $link;
}

/**
 * @param string $name
 * @param array $options
 * @return string
 */
function Video_videoAttributes($name, $options)
{
    global $_Video;

    $poster = $_Video->posterFile($name);
    $attributes = 'class=""'
        . (!empty($options['controls']) ? ' controls="controls"' : '')
        . (!empty($options['autoplay']) ? ' autoplay="autoplay"' : '')
        . (!empty($options['loop']) ? ' loop="loop"' : '')
        . ' preload="' . $options['preload'] . '"'
        . ' width="' . $options['width'] . '"'
        . ' height="' . $options['height'] . '"'
        . ($poster ? ' poster="' . $poster . '"' : '')
        . ' ' . Video_resizeStyle($options['resize']);
    return $attributes;
}

/**
 * @param string $name
 * @param string $options
 * @return string
 */
function video($name, $options = '')
{
    global $plugin_tx, $sl, $_Video;
    static $run = 0;

    $ptx = $plugin_tx['video'];
    if (!$run) {
        Video_includeJs();
    }
    $run++;
    $files = $_Video->videoFiles($name);

    if (!empty($files)) {
        $opts = $_Video->getOptions(html_entity_decode($options, ENT_QUOTES, 'UTF-8'));
        $attributes = Video_videoAttributes($name, $opts);
        $o = <<<EOT
<!-- Video_XH: $name -->
<video id="video_$run" $attributes>

EOT;
        foreach ($files as $filename => $type) {
            $url = $_Video->normalizedUrl(CMSIMPLE_URL . $filename);
            $o .= <<<EOT
    <source src="$url" type="video/$type">

EOT;
        }
        $filename = $_Video->subtitleFile($name);
        if ($filename) {
            $o .= <<<EOT
    <track src="$filename" srclang="$sl" label="$ptx[subtitle_label]">

EOT;
        }
        $filenames = array_keys($files);
        $filename = $filenames[0];
        $style = Video_resizeStyle($opts['resize']);
        $link = Video_downloadLink($name, $filename, $style);
        $o .= <<<EOT
    <a href="$filename">$link</a>

EOT;
        $o .= <<<EOT
</video>

EOT;
    } else {
        $o = XH_message('fail', $ptx['error_missing'], $name);
    }
    return $o;
}

$_Video = new Video\Model($pth['folder'], $plugin_cf['video']);

if ($plugin_cf['video']['auto_hjs']) {
    Video_includeJs();
}
