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
 *
 * @todo: Use better definition.
 */
define(
    'VIDEO_URL', 'http'
    . (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 's' : '')
    . '://' . $_SERVER['SERVER_NAME']
    . ($_SERVER['SERVER_PORT'] < 1024 ? '' : ':' . $_SERVER['SERVER_PORT'])
    . preg_replace('/index.php$/', '', $_SERVER['PHP_SELF'])
);

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
 * Includes the necessary JS and CSS in the head element.
 *
 * @return void
 *
 * @global string The document fragment to insert in the head element.
 * @global array  The paths of system files and folders.
 * @global array  The configuration of the plugins.
 *
 * @todo Remove CDN facility for better compat?
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
    if (!empty($pcf['skin'])) {
        $hjs .= <<<EOT
<link rel="stylesheet" href="$lib$pcf[skin].css" type="text/css">

EOT;
    }
    if ($pcf['use_cdn']) {
        $hjs .= <<<EOT
<link rel="stylesheet" href="http://vjs.zencdn.net/c/video-js.css" type="text/css">
<script type="text/javascript" src="http://vjs.zencdn.net/c/video.js"></script>

EOT;
    } else {
        $hjs .= <<<EOT
<link rel="stylesheet" href="${lib}video-js.min.css" type="text/css">
<script type="text/javascript" src="${lib}video.js"></script>
<script type="text/javascript">
    videojs.options.flash.swf = "${lib}video-js.swf";
</script>

EOT;
    }
    $autosizePath = $pth['folder']['plugins'] . 'video/autosize.js';
    $order = $pcf['prefer_flash'] ? '"flash", "html5"' : '"html5", "flash"';
    $hjs .= <<<EOT
<script type="text/javascript" src="$autosizePath"></script>
<script type="text/javascript">
    videojs.options.techOrder = [$order];
</script>

EOT;
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

    $query = html_entity_decode($query, ENT_QUOTES, 'UTF-8'); // TODO: PHP 4!
    parse_str($query, $opts);

    $res = array();
    foreach ($validOpts as $key) {
        // FIXME: no nested ternary op!
        $res[$key] = isset($opts[$key])
            ? ($opts[$key] === '' ? true : $opts[$key])
            : $plugin_cf['video']["default_$key"];
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
 */
function video($name, $options = '')
{
    global $pth, $plugin_cf, $plugin_tx;
    static $run = 0;

    $pcf = $plugin_cf['video'];
    $ptx = $plugin_tx['video'];
    if (!$run) {
        Video_hjs();
    }
    $run++;
    $files = Video_files($name);

    if (!empty($files)) {
        $dn = Video_folder();

        $keys = array(
            'controls', 'preload', 'autoplay', 'loop', 'width', 'height', 'resize'
        );
        $opts = Video_getOpt($options, $keys);

        $fn = $dn . $name . '.jpg';
        $class = 'vjs-' . (!empty($pcf['skin']) ? $pcf['skin'] : 'default')
            . '-skin';
        $o = '<noscript>' . $ptx['message_no_js'] . '</noscript>'
            . '<video id="video_' . $run . '" class="video-js ' . $class . '"'
            . (!empty($opts['controls']) ? ' controls="controls"' : '')
            . (!empty($opts['autoplay']) ? ' autoplay="autoplay"' : '')
            . (!empty($opts['loop']) ? ' loop="loop"' : '')
            . (' preload="' . $opts['preload'] . '"')
            . (' width="' . $opts['width'] . '"')
            . (' height="' . $opts['height'] . '"')
            . (file_exists($fn) ? ' poster="' . $fn . '"' : '')
            . '>';
        foreach ($files as $fn => $type) {
            $o .= tag(
                'source src="' . Video_canonicalUrl($fn) . '"'
                . ' type="video/' . $type . '"'
            );
        }
        $o .= '</video>';
        $o .= <<<EOT
<script type="text/javascript">
    videojs("video_$run", {}, function () {
	video.autosize("video_$run", "$opts[resize]");
    });
</script>

EOT;
    } else {
        $o = '<div class="cmsimplecore_warning">'
            . sprintf($ptx['error_missing'], $name) . '</div>';
    }
    return $o;
}

/*
 * Handle auto_hjs config option.
 */
if ($plugin_cf['video']['auto_hjs']) {
    Video_hjs();
}

?>
