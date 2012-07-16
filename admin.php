<?php

// $Id$

/**
 * Back-end of Video_XH.
 *
 * Copyright (c) 2012 Christoph M. Becker (see license.txt)
 */


if (!defined('CMSIMPLE_XH_VERSION')) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}


/**
 * Returns the version information view.
 *
 * @return string  The (X)HTML.
 */
function video_version() {
    global $pth;

    return '<h1><a href="http://3-magi.net/?CMSimple_XH/Video_XH">Video_XH</a></h1>'."\n"
	    .tag('img class="video_plugin_icon" src="'.$pth['folder']['plugins'].'video/video.png" alt="Plugin icon"')."\n"
	    .'<p style="margin-top: 1em">Version: '.VIDEO_VERSION.'</p>'."\n"
	    .'<p>Copyright &copy; 2012 <a href="http://3-magi.net/">Christoph M. Becker</a></p>'."\n"
	    .'<p>Video_XH is powered by <a href="http://videojs.com">Video.js</a></p>'."\n"
	    .'<p class="video_license">This program is free software: you can redistribute it and/or modify'
	    .' it under the terms of the GNU General Public License as published by'
	    .' the Free Software Foundation, either version 3 of the License, or'
	    .' (at your option) any later version.</p>'."\n"
	    .'<p class="video_license">This program is distributed in the hope that it will be useful,'
	    .' but WITHOUT ANY WARRANTY; without even the implied warranty of'
	    .' MERCHAN&shy;TABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the'
	    .' GNU General Public License for more details.</p>'."\n"
	    .'<p class="video_license">You should have received a copy of the GNU General Public License'
	    .' along with this program.  If not, see'
	    .' <a href="http://www.gnu.org/licenses/">http://www.gnu.org/licenses/</a>.</p>'."\n";
}


/**
 * Returns the requirements information view.
 *
 * @return string  The (X)HTML.
 */
function video_system_check() { // RELEASE-TODO
    global $pth, $tx, $plugin_tx;

    define('VIDEO_PHP_VERSION', '4.0.7');
    $ptx = $plugin_tx['video'];
    $imgdir = $pth['folder']['plugins'].'video/images/';
    $ok = tag('img src="'.$imgdir.'ok.png" alt="ok"');
    $warn = tag('img src="'.$imgdir.'warn.png" alt="warning"');
    $fail = tag('img src="'.$imgdir.'fail.png" alt="failure"');
    $o = '<h4>'.$ptx['syscheck_title'].'</h4>'
	    .(version_compare(PHP_VERSION, VIDEO_PHP_VERSION) >= 0 ? $ok : $fail)
	    .'&nbsp;&nbsp;'.sprintf($ptx['syscheck_phpversion'], VIDEO_PHP_VERSION)
	    .tag('br')."\n";
    foreach (array() as $ext) {
	$o .= (extension_loaded($ext) ? $ok : $fail)
		.'&nbsp;&nbsp;'.sprintf($ptx['syscheck_extension'], $ext).tag('br')."\n";
    }
    $o .= (!get_magic_quotes_runtime() ? $ok : $fail)
	    .'&nbsp;&nbsp;'.$ptx['syscheck_magic_quotes'].tag('br').tag('br')."\n";
    $o .= (strtoupper($tx['meta']['codepage']) == 'UTF-8' ? $ok : $warn)
	    .'&nbsp;&nbsp;'.$ptx['syscheck_encoding'].tag('br').tag('br')."\n";
    foreach (array('config/', 'css/', 'languages/') as $folder) {
	$folders[] = $pth['folder']['plugins'].'video/'.$folder;
    }
    foreach ($folders as $folder) {
	$o .= (is_writable($folder) ? $ok : $warn)
		.'&nbsp;&nbsp;'.sprintf($ptx['syscheck_writable'], $folder).tag('br')."\n";
    }
    return $o;
}


/**
 * Returns all recognized videos in the video folder.
 *
 * @return array
 */
function Video_availVideos()
{
    $files = glob(Video_folder() . '*.???');
    $vids = array();
    foreach ($files as $file) {
	$pp = pathinfo($file);
	if (in_array($pp['extension'], array('mp4', 'webm', 'ogv'))) {
	    $name = substr($pp['basename'], 0, -(strlen($pp['extension']) + 1));
	    $vids[$name] = $name;
	}
    }
    return array_unique($vids);
}


/**
 * Returns a selectbox.
 *
 * @param string $id  The id of the selectbox.
 * @param array $items  The options (key->value, value->html).
 * @param string $default  The selected option.
 * @return string  The (X)HTML.
 */
function Video_selectbox($id, $items, $default = null)
{
    $o = '<select id="'. $id . '">';
    foreach ($items as $key => $val) {
	$sel = isset($default) && $key == $default ? ' selected="selected"' : ''; 
	$o .= '<option value="' . htmlspecialchars($key, ENT_COMPAT, 'UTF-8')
	    . '"' . $sel . '>' . htmlspecialchars($val, ENT_COMPAT, 'UTF-8')
	    . '</option>';
    }
    $o .= '</select>';
    return $o;
}


/**
 * Returns the "call builder".
 *
 * @return string  The (X)HTML.
 */
function Video_adminMain()
{
    global $pth, $plugin_cf, $plugin_tx;
    
    $pcf = $plugin_cf['video'];
    $ptx = $plugin_tx['video'];
    $o = '<div id="video_call_builder">'
	. '<label for="video_name">' . $ptx['label_name'] . '</label>'
	. Video_selectbox('video_name', Video_availVideos()) . tag('br');
    $items = array();
    foreach (array('auto', 'metadata', 'none') as $key) {
	$items[$key] = $ptx['preload_' . $key];
    }
    $o .= '<label for="video_preload">' . $ptx['label_preload'] . '</label>'
	. Video_selectbox('video_preload', $items, $pcf['default_preload']) . tag('br');
    foreach (array('autoplay', 'loop', 'controls') as $key) {
	$id = 'video_' . $key;
	$check = $pcf['default_' . $key] ? ' checked="checked"' : '';
	$o .= '<label for="' . $id . '">' . $ptx['label_' . $key] . '</label>'
	    . tag('input id="' . $id . '" type="checkbox"' . $check) . tag('br');
    }
    foreach (array('width', 'height') as $key) {
	$id = 'video_' . $key;
	$o .= '<label for="' . $id . '">' . $ptx['label_' . $key] . '</label>'
	    . tag('input id="' . $id . '" type="text" value="' . $pcf['default_' . $key]. '"') . tag('br');
    }
    $o .= tag('hr')
	. '<label for="video_call">' . $ptx['label_call'] . '</label>'
	. tag('input id="video_call" type="text" readonly="readonly"') . tag('br')
	. '</div>'
	. '<script type="text/javascript" src="' . $pth['folder']['plugins']
	    . 'video/admin.js"></script>';
    return $o;
}


/**
 * Handle the plugin administration.
 */
if (isset($video) && $video == 'true') {
    $o .= print_plugin_admin('on');
    switch ($admin) {
	case '':
	    $o .= video_version().tag('hr').video_system_check();
	    break;
	case 'plugin_main':
	    $o .= Video_adminMain();
	    break;
	default:
	    $o .= plugin_admin_common($action, $admin, $plugin);
    }
}

?>
