<?php

/**
 * Front-end of Video_XH.
 *
 * PHP versions 4 and 5
 *
 * @category CMSimple_XH
 * @package  Video_XH
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @version  SVN: $Id$
 * @link     http://3-magi.net/?CMSimple_XH/Video_XH
 */

/*
 * Prevent direct access.
 */
if (!defined('CMSIMPLE_XH_VERSION')) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}

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
 * Returns a string with all (X)HTML entities decoded.
 *
 * Provides a simplified fallback for PHP 4, which should be sufficient for our
 * needs.
 *
 * @param string $string A string.
 *
 * @return string
 */
function Video_entitiyDecoded($string)
{
    if (version_compare(phpversion(), '5', '>=')) {
        return html_entity_decode($string, ENT_QUOTES, 'UTF-8');
    } else {
        $replacePairs = array(
            '&amp;' => '&',
            '&quot;' => '"',
            '&apos;' => '\'',
            '&lt;' => '<',
            '&gt;' => '>'
        );
        return strtr($string, $replacePairs);
    }
}

/**
 * Returns the fully qualified absolute URL of $url
 * in canonical form (i.e. with ./ and ../ resolved).
 *
 * @param string $url A relative URL.
 *
 * @return string
 */
function Video_canonicalUrl($url)
{
    $parts = explode('/', VIDEO_URL . $url);
    $i = count($parts) - 1;
    while ($i >= 0) {
        switch ($parts[$i]) {
        case '.':
            array_splice($parts, $i, 1);
            break;
        case '..':
            array_splice($parts, $i - 1, 2);
            $i--;
            break;
        }
        $i--;
    }
    return implode('/', $parts);
}

/**
 * Returns the relative path to the video folder.
 *
 * @return string
 *
 * @global array The paths of system files and folders.
 * @global array The configuration of the plugins.
 */
function Video_folder()
{
    global $pth, $plugin_cf;

    $pcf = $plugin_cf['video'];
    if (!empty($pcf['folder_video'])) {
        $folder = rtrim($pth['folder']['base'] . $pcf['folder_video'], '/') . '/';
    } elseif (isset($pth['folder']['media'])) {
        $folder = $pth['folder']['media'];
    } else {
        $folder = $pth['folder']['downloads'];
    }
    return $folder;
}

/**
 * Returns a map of filenames to types.
 *
 * @param string $name Name of the video file without extension.
 *
 * @return array
 */
function Video_files($name)
{
    $types = array('webm' => 'webm', 'mp4' => 'mp4', 'ogv' => 'ogg');
    $dirname = Video_folder();
    $files = array();
    foreach ($types as $ext => $type) {
        $fn = $dirname . $name . '.' . $ext;
        if (file_exists($fn)) {
            $files[$fn] = $ext;
        }
    }
    return $files;
}

/**
 * Returns the path of an appropriate subtitle file; <var>false</var> otherwise.
 *
 * @param string $name A video name.
 *
 * @return string
 *
 * @global string The current language.
 */
function Video_subtitleFile($name)
{
    global $sl;

    $dirname = Video_folder();
    $filename = $dirname . $name . '.vtt';
    $suffixes = array("_$sl.vtt", "_$sl.srt", '.vtt', '.srt');
    foreach ($suffixes as $suffix) {
        $filename = $dirname . $name . $suffix;
        if (file_exists($filename)) {
            return $filename;
        }
    }
    return false;
}

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
 * @global string The document fragment to insert in the head element.
 * @global array  The paths of system files and folders.
 * @global array  The configuration of the plugins.
 */
function Video_hjs()
{
    global $hjs, $pth, $plugin_cf;
    static $again = false;

    if ($again) {
        return;
    }
    $again = true;
    $pcf = $plugin_cf['video'];
    $lib = $pth['folder']['plugins'] . 'video/lib/';
    $o = '';
    if (!empty($pcf['skin'])) {
        $css = "$lib$pcf[skin].css";
    } elseif ($pcf['use_cdn']) {
        $css = 'http://vjs.zencdn.net/4.3/video-js.css';
    } else {
        $css = "${lib}video-js.css";
    }
    $o .= <<<EOT
<link rel="stylesheet" href="$css" type="text/css">

EOT;
    if ($pcf['use_cdn']) {
        $o .= <<<EOT
<script type="text/javascript" src="http://vjs.zencdn.net/4.3/video.js"></script>

EOT;
    } else {
        $o .= <<<EOT
<script type="text/javascript" src="${lib}video.js"></script>
<script type="text/javascript">
    videojs.options.flash.swf = "${lib}video-js.swf";
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
 * Returns all options.
 *
 * Defaults are taken from $plugin_cf['video']['default_*'].
 * Those will be overridden with the options in $query.
 *
 * @param string $query     The options given like a query string.
 * @param array  $validOpts The valid options.
 *
 * @return array
 *
 * @global array The configuration of the plugins.
 */
function Video_getOpt($query, $validOpts)
{
    global $plugin_cf;

    $query = Video_entitiyDecoded($query);
    parse_str($query, $opts);

    $res = array();
    foreach ($validOpts as $key) {
        if (isset($opts[$key])) {
            $res[$key] = ($opts[$key] === '') ? true : $opts[$key];
        } else {
            $res[$key] = $plugin_cf['video']["default_$key"];
        }
    }
    return $res;
}

/**
 * Returns the video element to embed the video.
 *
 * @param string $name    Name of the video file without extension.
 * @param string $options The options in form of a query string.
 *
 * @return string (X)HTML.
 *
 * @global array The paths of system files and folders.
 * @global array The configuration of the plugins.
 * @global array The localization of the plugins.
 * @global string The current language. *
 */
function video($name, $options = '')
{
    global $pth, $plugin_cf, $plugin_tx, $sl;
    static $run = 0;

    $pcf = $plugin_cf['video'];
    $ptx = $plugin_tx['video'];
    if (!$run) {
        Video_hjs();
    }
    $run++;
    $files = Video_files($name);

    if (!empty($files)) {
        $keys = array(
            'align', 'autoplay', 'centered', 'controls', 'height', 'loop',
            'preload', 'resize', 'width',

        );
        $opts = Video_getOpt($options, $keys);
        $class = !empty($pcf['skin']) ? $pcf['skin'] : 'default';
        $class = "vjs-$class-skin";
        if ($opts['centered']) {
            $class .= ' vjs-big-play-centered';
        }
        $controls = !empty($opts['controls']) ? ' controls="controls"' : '';
        $autoplay = !empty($opts['autoplay']) ? ' autoplay="autoplay"' : '';
        $loop = !empty($opts['loop']) ? ' loop="loop"' : '';
        $filename = Video_folder() . $name . '.jpg';
        $poster = file_exists($filename) ? 'poster="' . $filename . '"' : '';
        switch ($opts['resize']) {
        case 'full':
            $style = 'style="width:100%"';
            break;
        case 'shrink':
            $style = 'style="max-width:100%';
            break;
        default:
            $style = '';
        }
        $o = <<<EOT
<!-- Video_XH: $name -->
<video id="video_$run" class="video-js $class"$controls$autoplay$loop
       preload="$opts[preload]" $poster $style>

EOT;
        foreach ($files as $filename => $type) {
            $url = Video_canonicalUrl($filename);
            $o .= <<<EOT
    <source src="$url" type="video/$type" />

EOT;
        }
        $filename = Video_subtitleFile($name);
        if ($filename) {
            $o .= <<<EOT
    <track src="$filename" srclang="$sl" label="$ptx[subtitle_label]" />

EOT;
        }
        $o .= <<<EOT
</video>
<script type="text/javascript">/* <![CDATA[ */
(function () {
    var video = document.getElementById("video_$run");
    video.width = "$opts[width]";
    video.height = "$opts[height]";
    videojs("video_$run", {}, function () {
        VIDEO.initPlayer("video_$run", "$opts[align]", "$opts[resize]");
    });
})();
/* ]]> */</script>
<noscript><p class="video_noscript">$ptx[message_no_js]</p></noscript>

EOT;
    } else {
        $o = '<div class="cmsimplecore_warning">'
            . sprintf($ptx['error_missing'], $name) . '</div>';
    }
    return Video_xhtml($o);
}

/*
 * Handle auto_hjs config option.
 */
if ($plugin_cf['video']['auto_hjs']) {
    Video_hjs();
}

?>
