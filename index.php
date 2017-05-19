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

function Video_hjs()
{
    global $sl, $hjs, $pth, $plugin_cf;
    static $again = false;

    if ($again) {
        return;
    }
    $again = true;
    $pcf = $plugin_cf['video'];
    $playerLang = Video_playerLang();
    $lib = $pth['folder']['plugins'] . 'video/lib/';
    $o = '';
    if (!empty($pcf['skin'])) {
        $css = "$lib$pcf[skin].css";
    } elseif ($pcf['use_cdn']) {
        $css = 'http://vjs.zencdn.net/4.12.15/video-js.css';
    } else {
        $css = "${lib}video-js.css";
    }
    $o .= <<<EOT
<link rel="stylesheet" href="$css" type="text/css">

EOT;
    if ($pcf['use_cdn']) {
        $o .= <<<EOT
<script type="text/javascript" src="http://vjs.zencdn.net/4.12.15/video.js"></script>
<script type="text/javascript">
    videojs.addLanguage('$sl', $playerLang);
</script>

EOT;
    } else {
        $o .= <<<EOT
<script type="text/javascript" src="${lib}video.js"></script>
<script type="text/javascript">
    videojs.options.flash.swf = "${lib}video-js.swf";
    videojs.addLanguage('$sl', $playerLang);
</script>

EOT;
    }
    Video_includeJs();
    $order = $pcf['prefer_flash'] ? '"flash", "html5"' : '"html5", "flash"';
    $o .= <<<EOT
<script type="text/javascript">
    videojs.options.techOrder = [$order];
</script>

EOT;
    $hjs .= $o;
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
    global $plugin_cf, $_Video;

    $pcf = $plugin_cf['video'];
    $class = !empty($pcf['skin']) ? $pcf['skin'] : 'default';
    $class = "vjs-$class-skin";
    if ($options['centered']) {
        $class .= ' vjs-big-play-centered';
    }
    $poster = $_Video->posterFile($name);
    $attributes = 'class="video-js ' . $class . '"'
        . (!empty($options['controls']) ? ' controls="controls"' : '')
        . (!empty($options['autoplay']) ? ' autoplay="autoplay"' : '')
        . (!empty($options['loop']) ? ' loop="loop"' : '')
        . ' preload="' . $options['preload'] . '"'
        . ($poster ? ' poster="' . $poster . '"' : '')
        . ' ' . Video_resizeStyle($options['resize']);
    return $attributes;
}

/**
 * @return string
 */
function Video_playerLang()
{
    global $plugin_tx;

    if (function_exists('json_encode')) {
        $playerLang = array();
        foreach (Video_playerLanguageKeys() as $key => $text) {
            $playerLang[$text] = $plugin_tx['video'][$key];
        }
        return json_encode($playerLang);
    } else {
        return '{}';
    }
}

/**
 * @return array
 */
function Video_playerLanguageKeys()
{
    return array(
        'player_play' => 'Play',
        'player_pause' => 'Pause',
        'player_current_time' => 'Current Time',
        'player_duration_time' => 'Duration Time',
        'player_remaining_time' => 'Remaining Time',
        'player_stream_type' => 'Stream Type',
        'player_live' => 'LIVE',
        'player_loaded' => 'Loaded',
        'player_progress' => 'Progress',
        'player_fullscreen' => 'Fullscreen',
        'player_non_fullscreen' => 'Non-Fullscreen',
        'player_mute' => 'Mute',
        'player_unmuted' => 'Unmuted',
        'player_playback_rate' => 'Playback Rate',
        'player_subtitles' => 'Subtitles',
        'player_subtitles_off' => 'subtitles off',
        'player_captions' => 'Captions',
        'player_captions_off' => 'captions off',
        'player_chapters' => 'Chapters',
        'player_abort_playback_user' => 'You aborted the video playback',
        'player_network_error' =>
            'A network error caused the video download to fail part-way.',
        'player_cant_load' =>
            'The video could not be loaded, either because the server or network'
            . ' failed or because the format is not supported.',
        'player_abort_playback' =>
            'The video playback was aborted due to a corruption problem or'
            . ' because the video used features your browser did not support.',
        'player_incompatible' => 'No compatible source was found for this video.'
    );
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
        Video_hjs();
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
<script type="text/javascript">
VIDEO.initPlayer("video_$run", $opts[width], $opts[height], "$opts[resize]");
</script>

EOT;
    } else {
        $o = XH_message('fail', $ptx['error_missing'], $name);
    }
    return $o;
}

$_Video = new Video\Model($pth['folder'], $plugin_cf['video']);

if ($plugin_cf['video']['auto_hjs']) {
    Video_hjs();
}
