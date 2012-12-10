<?php

// $Id$

/**
 * Front-end of Video_XH.
 *
 * Copyright (c) 2012 Christoph M. Becker (see license.txt)
 */


if (!defined('CMSIMPLE_XH_VERSION')) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}


define('VIDEO_VERSION', '1beta6');


/**
 * Fully qualified absolute URL to CMSimple's index.php.
 */
define('VIDEO_URL', 'http'
    . (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 's' : '')
    . '://' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT']
    . preg_replace('/index.php$/', '', $_SERVER['PHP_SELF']));


/**
 * Returns the fully qualified absolute URL of $url
 * in canonical form (i.e. with ./ and ../ resolved).
 *
 * @param string $url  A relative URL.
 * @return string
 */
function video_canonical_url($url) {
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
 */
function Video_folder()
{
    global $pth, $plugin_cf;

    $pcf = $plugin_cf['video'];
    return !empty($pcf['folder_video'])
	? rtrim($pth['folder']['base'] . $pcf['folder_video'], '/') .'/'
	: isset($pth['folder']['media'])
	    ? $pth['folder']['media']
	    : $pth['folder']['downloads'];
}

/**
 * Includes the necessary JS and CSS to the <head>.
 *
 * @global string $hjs
 * @return void
 */
function video_hjs() {
    global $hjs, $pth, $plugin_cf;

    $pcf = $plugin_cf['video'];
    $lib = $pth['folder']['plugins'].'video/lib/';
    if (!empty($pcf['skin'])) {
	$hjs .= '<link rel="stylesheet" href="'.$lib.$pcf['skin'].'.css" type="text/css">'."\n";
    }
    if ($pcf['use_cdn']) {
	$hjs .= '<link rel="stylesheet" href="http://vjs.zencdn.net/c/video-js.css" type="text/css">'."\n"
		.'<script type="text/javascript" src="http://vjs.zencdn.net/c/video.js"></script>'."\n";
    } else {
	$hjs .= '<link rel="stylesheet" href="'.$lib.'video-js.min.css" type="text/css">'."\n"
		.'<script type="text/javascript" src="'.$lib.'video.min.js"></script>'."\n"
		.'<script type="text/javascript">VideoJS.options.flash.swf = \''.$lib.'video-js.swf\'</script>'."\n";
    }
    $order = $pcf['prefer_flash'] ? '\'flash\', \'html5\'' : '\'html5\', \'flash\'';
    $hjs .= '<script type="text/javascript" src="' . $pth['folder']['plugins'] . 'video/autosize.js"></script>';
    $hjs .= '<script type="text/javascript">VideoJS.options.techOrder = ['.$order.']</script>'."\n";
}


/**
 * Returns all options.
 *
 * Defaults are taken from $plugin_cf['video']['default_*'].
 * Those will be overridden with the options in $query.
 *
 * @param string $query  The options given like a query string.
 * @param array $validOpts  The valid options.
 * @return array
 */
function Video_getOpt($query, $validOpts)
{
    global $plugin_cf;

    $query = html_entity_decode($query, ENT_QUOTES, 'UTF-8');
    parse_str($query, $opts);

    $res = array();
    foreach ($validOpts as $key) {
	$res[$key] = isset($opts[$key])
	    ? ($opts[$key] === '' ? true : $opts[$key])
	    : $plugin_cf['video']["default_$key"];
    }

    return $res;
}


/**
 * Returns the <video> element to embed the video.
 *
 * @access public
 * @param string $name  Name of the video file without extension.
 * @param string $options  The options in form of a query string.
 * @return string  The (X)HTML.
 */
function video($name, $options = '')
{
    global $pth, $plugin_cf, $plugin_tx;
    static $run = 0;

    $pcf = $plugin_cf['video'];
    $ptx = $plugin_tx['video'];
    if (!$run) {
	video_hjs();
    }
    $run++;
    $dn = Video_folder();

    $keys = array('controls', 'preload', 'autoplay', 'loop', 'width', 'height');
    $opts = Video_getOpt($options, $keys);

    $fn = $dn . $name . '.jpg';
    $class = 'vjs-' . (!empty($pcf['skin']) ? $pcf['skin'] : 'default') . '-skin';
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
    $types = array('webm' => 'webm', 'mp4' => 'mp4', 'ogv' => 'ogg');
    foreach ($types as $ext => $type) {
	$fn = $dn . $name . '.' . $ext;
	if (file_exists($fn)) {
	    $o .= tag('source'
		. (' src="' . video_canonical_url($fn) . '"')
		. (' type="video/' . $type . '"')
	    );
	}
    }
    $o .= '</video>';
    $o .= '<script type="text/javascript">VideoJS("video_' . $run
	. '").ready(function(){video.autosize(this,' . $pcf['auto_resize'] . ')})</script>';
    return $o;
}

?>
