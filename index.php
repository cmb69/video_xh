<?php

/**
 * Front-end of Video_XH.
 *
 * Copyright (c) 2012 Christoph M. Becker (see license.txt)
 */


if (!defined('CMSIMPLE_XH_VERSION')) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}


define('VIDEO_VERSION', '1beta4');


/**
 * Fully qualified absolute URL to CMSimple's index.php.
 */
define('VIDEO_URL', 'http://'
    . (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 's' : '')
    . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT']
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
 * Includes the necessary JS and CSS to the <head>.
 *
 * @global string $hjs
 * @return void
 */
function video_hjs() {
    global $hjs, $pth, $plugin_cf;

    $pcf = $plugin_cf['video'];
    $lib = $pth['folder']['plugins'].'video/lib/';
    if ($pcf['use_cdn']) {
	$hjs .= '<link rel="stylesheet" href="http://vjs.zencdn.net/c/video-js.css" type="text/css">'."\n"
		.'<script type="text/javascript" src="http://vjs.zencdn.net/c/video.js"></script>'."\n";
    } else {
	$hjs .= '<link rel="stylesheet" href="'.$lib.'video-js.min.css" type="text/css">'."\n"
		.'<script type="text/javascript" src="'.$lib.'video.min.js"></script>'."\n"
		.'<script type="text/javascript">VideoJS.options.flash.swf = \''.$lib.'video-js.swf\'</script>'."\n";
    }
    $order = $pcf['prefer_flash'] ? '\'flash\', \'html5\'' : '\'html5\', \'flash\'';
    $hjs .= '<script type="text/javascript">VideoJS.options.techOrder = ['.$order.']</script>'."\n";
}


/**
 * Returns the <video> element to embed the video.
 *
 * @access public
 * @param string $name  Name of the video file without extension.
 * @param int $width  The width of the video.
 * @param int $height  The height of the video.
 * @return string  The (X)HTML.
 */
function video($name, $width = NULL, $height = NULL) {
    global $pth, $plugin_cf, $plugin_tx;
    static $run = 0;

    $pcf = $plugin_cf['video'];
    $ptx = $plugin_tx['video'];
    if (!$run) {video_hjs();}
    $run++;
    $dn = !empty($pcf['folder_video']) ? rtrim($pth['folder']['base'].$pcf['folder_video'], '/').'/'
	    : isset($pth['folder']['media']) ? $pth['folder']['media'] : $pth['folder']['downloads'];
    if (!isset($width)) {$width = $pcf['default_width'];}
    if (!isset($height)) {$height = $pcf['default_height'];}
    $fn = $dn.$name.'.jpg';
    $poster = file_exists($fn) ? ' poster="'.$fn.'"' : '';
    $o = '<noscript>'.$ptx['message_no_js'].'</noscript>'."\n"
	    .'<video id="video_'.$run.'" class="video-js vjs-default-skin" controls="controls"'
	    .' preload="'.$pcf['preload'].'" width="'.$width.'" height="'.$height.'"'.$poster.'>'."\n";
    foreach (array('webm' => 'webm', 'mp4' => 'mp4', 'ogv' => 'ogg') as $ext => $type) {
	$fn = $dn.$name.'.'.$ext;
	if (file_exists($fn)) {
	    $o .= tag('source src="' . video_canonical_url($fn) . '" type="video/'.$type.'"')."\n";
	}
    }
    $o .= '</video>'."\n";
    $o .= '<script type="text/javascript">VideoJS(\'video_'.$run.'\')</script>'."\n";
    return $o;
}

?>
