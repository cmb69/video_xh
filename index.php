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


define('VIDEO_VERSION', '1beta2');


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
    foreach (array('mp4', 'webm', 'ogg') as $type) {
	$fn = $dn.$name.'.'.$type;
	if (file_exists($fn)) {
	    $o .= tag('source src="'.$fn.'" type="video/'.$type.'"')."\n";
	}
    }
    $o .= '</video>'."\n";
    $o .= '<script type="text/javascript">VideoJS(\'video_'.$run.'\')</script>'."\n";
    return $o;
}

?>
