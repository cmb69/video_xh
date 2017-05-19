<?php

/**
 * Front-end of Video_XH.
 *
 * PHP versions 4 and 5
 *
 * @category  CMSimple_XH
 * @package   Video
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2012-2016 Christoph M. Becker <http://3-magi.net/>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link      http://3-magi.net/?CMSimple_XH/Video_XH
 */

/*
 * Prevent direct access.
 */
if (!defined('CMSIMPLE_XH_VERSION')) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}

/**
 * The model class.
 */
require_once $pth['folder']['plugin_classes'] . 'Model.php';

/**
 * The version number.
 */
define('VIDEO_VERSION', '@VIDEO_VERSION@');

/**
 * Fully qualified absolute URL to CMSimple's index.php.
 */
define(
    'VIDEO_URL',
    'http'
    . (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 's' : '')
    . '://' . $_SERVER['HTTP_HOST'] . preg_replace('/index\.php$/', '', $sn)
);

/**
 * Returns a string with XHTML style empty elements converted to HTML according
 * to the config option xhtml_endtags.
 *
 * @param string $xhtml An XHTML string.
 *
 * @return (X)HTML.
 *
 * @global array The configuration of the core.
 */
function Video_xhtml($xhtml)
{
    global $cf;

    if ($cf['xhtml']['endtags'] == 'true') {
        return $xhtml;
    } else {
        return str_replace(' />', '>', $xhtml);
    }
}

/**
 * Includes the required JavaScript in the head element.
 *
 * @return void
 *
 * @global array The paths of system files and folders.
 *
 * @staticvar bool Whether this function has already been called.
 */
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
 * Includes the necessary JS and CSS in the head element.
 *
 * @return void
 *
 * @global string The current language code.
 * @global string The document fragment to insert in the head element.
 * @global array  The paths of system files and folders.
 * @global array  The configuration of the plugins.
 *
 * @staticvar bool Whether this function has already been called.
 */
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
    $hjs .= Video_xhtml($o);
}

/**
 * Returns a style attribute according to the resize mode.
 *
 * @param string $resizeMode A resize mode ("no", "shrink" or "full").
 *
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
 * Returns a link for downloading the video.
 *
 * @param string $videoname A video name.
 * @param string $filename  A file path.
 * @param string $style     A style attribute.
 *
 * @return string (X)HTML.
 *
 * @global array  The localization of the plugins.
 * @global object The video model.
 */
function Video_downloadLink($videoname, $filename, $style)
{
    global $plugin_tx, $_Video;

    $basename = basename($filename);
    $download = sprintf($plugin_tx['video']['label_download'], $basename);
    $poster = $_Video->posterFile($videoname);
    if ($poster) {
        $link = tag(
            "img src=\"$poster\" alt=\"$download\" title=\"$download\" $style"
        );
    } else {
        $link = $download;
    }
    return $link;
}

/**
 * Returns the attributes for a video element.
 *
 * @param string $name    A video name.
 * @param array  $options Video options.
 *
 * @return string
 *
 * @global array  The configuration of the plugins.
 * @global object The video model.
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
 * Returns a JSON string containing the current localization.
 *
 * @return string
 * 
 * @global array The localization of the plugins.
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
 * Returns the player language keys.
 *
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
 * Returns the video element to embed the video.
 *
 * @param string $name    Name of the video file without extension.
 * @param string $options The options in form of a query string.
 *
 * @return string (X)HTML.
 *
 * @global array  The localization of the plugins.
 * @global string The current language.
 * @global object The video model.
 *
 * @staticvar int The number of times this function has been called.
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
            $url = $_Video->normalizedUrl(VIDEO_URL . $filename);
            $o .= <<<EOT
    <source src="$url" type="video/$type" />

EOT;
        }
        $filename = $_Video->subtitleFile($name);
        if ($filename) {
            $o .= <<<EOT
    <track src="$filename" srclang="$sl" label="$ptx[subtitle_label]" />

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
        $o = '<div class="cmsimplecore_warning">'
            . sprintf($ptx['error_missing'], $name) . '</div>';
    }
    return Video_xhtml($o);
}

/**
 * The model object.
 */
$_Video = new Video_Model($pth['folder'], $plugin_cf['video']);

/*
 * Handle auto_hjs config option.
 */
if ($plugin_cf['video']['auto_hjs']) {
    Video_hjs();
}

?>
