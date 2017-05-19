<?php

/**
 * Back-end of Video_XH.
 *
 * PHP versions 4 and 5
 *
 * @category  CMSimple_XH
 * @package   Video
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2012-2017 Christoph M. Becker <http://3-magi.net/>
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
 * Returns the about view.
 *
 * @return string (X)HTML.
 *
 * @global array The paths of system files and folders.
 * @global array The localization of the plugins.
 */
function Video_aboutView()
{
    global $pth, $plugin_tx;

    $iconPath = $pth['folder']['plugins'] . 'video/video.png';
    $version = VIDEO_VERSION;
    $o = <<<EOT
<h1>Video</h1>
<img class="video_plugin_icon" width="128" height="128" src="$iconPath"
     alt="{$plugin_tx['video']['alt_logo']}" />
<p style="margin-top:1em">Version: $version</p>
<p>Copyright &copy; 2012-2017
    <a href="http://3-magi.net/">Christoph M. Becker</a></p>
<p>Video_XH is powered by <a href="http://videojs.com">Video.js</a></p>
<p class="video_license">
    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.</p>
<p class="video_license">
    This program is distributed in the hope that it will be useful,
    but <em>without any warranty</em>; without even the implied warranty of
    <em>merchantability</em> or <em>fitness for a particular purpose</em>.
    See the GNU General Public License for more details.</p>
<p class="video_license">
    You should have received a copy of the GNU General Public License
    along with this program.  If not, see
    <a href="http://www.gnu.org/licenses/">http://www.gnu.org/licenses/</a></p>

EOT;
    return $o;
}

/**
 * Returns the system check view.
 *
 * @return string (X)HTML.
 *
 * @global array The paths of system files and folders.
 * @global array The localization of the core.
 * @global array The localization of the plugins.
 */
function Video_systemCheckView()
{
    global $pth, $tx, $plugin_tx;

    $phpVersion = '5.4.0';
    $ptx = $plugin_tx['video'];
    $imgdir = $pth['folder']['plugins'] . 'video/images/';
    $ok = tag('img src="' . $imgdir . 'ok.png" alt="ok"');
    $warn = tag('img src="' . $imgdir . 'warn.png" alt="warning"');
    $fail = tag('img src="' . $imgdir . 'fail.png" alt="failure"');
    $o = '<h4>' . $ptx['syscheck_title'] . '</h4>' . PHP_EOL
        . (version_compare(PHP_VERSION, $phpVersion) >= 0 ? $ok : $fail)
        . '&nbsp;&nbsp;' . sprintf($ptx['syscheck_phpversion'], $phpVersion)
        . tag('br') . PHP_EOL;
    foreach (array('json') as $ext) {
        $o .= (extension_loaded($ext) ? $ok : $fail)
            . '&nbsp;&nbsp;' . sprintf($ptx['syscheck_extension'], $ext)
            . tag('br') . PHP_EOL;
    }
    $o .= (!get_magic_quotes_runtime() ? $ok : $fail)
        . '&nbsp;&nbsp;' . $ptx['syscheck_magic_quotes']
        . tag('br') . tag('br') . PHP_EOL;
    $o .= (strtoupper($tx['meta']['codepage']) == 'UTF-8' ? $ok : $warn)
        . '&nbsp;&nbsp;' . $ptx['syscheck_encoding']
        . tag('br') . tag('br') . PHP_EOL;
    foreach (array('config/', 'css/', 'languages/') as $folder) {
        $folders[] = $pth['folder']['plugins'] . 'video/' . $folder;
    }
    foreach ($folders as $folder) {
        $o .= (is_writable($folder) ? $ok : $warn)
            . '&nbsp;&nbsp;' . sprintf($ptx['syscheck_writable'], $folder)
            . tag('br') . PHP_EOL;
    }
    return $o;
}

/**
 * Returns all available skins.
 *
 * @return array
 *
 * @global array The paths of system files and folders.
 */
function Video_availableSkins()
{
    global $pth;

    $skinPath = $pth['folder']['plugins'] . 'video/lib/';
    $skins = array();
    $dirHandle = opendir($skinPath);
    if ($dirHandle !== false) {
        while (($entry = readdir($dirHandle)) !== false) {
            if ($entry != 'video-js.css'
                && pathinfo($entry, PATHINFO_EXTENSION) == 'css'
            ) {
                $skins[] = basename($entry, '.css');
            }
        }
    }
    natcasesort($skins);
    array_unshift($skins, '');
    return $skins;
}

/**
 * Returns an associative array of preload options.
 *
 * @return array
 *
 * @global array The localization of the plugins.
 */
function Video_preloadOptions()
{
    global $plugin_tx;

    $ptx = $plugin_tx['video'];
    $options = array();
    foreach (array('auto', 'metadata', 'none') as $key) {
        $options[$key] = $ptx['preload_' . $key];
    }
    return $options;
}

/**
 * Returns an associative array of resize options.
 *
 * @return array
 *
 * @global array The localization of the plugins.
 */
function Video_resizeOptions()
{
    global $plugin_tx;

    $ptx = $plugin_tx['video'];
    $options = array();
    foreach (array('no', 'shrink', 'full') as $key) {
        $options[$key] = $ptx['resize_' . $key];
    }
    return $options;
}

/**
 * Returns a selectbox.
 *
 * @param string $id      The id of the selectbox.
 * @param array  $items   The options (key->value, value->html).
 * @param string $default The selected option.
 *
 * @return string (X)HTML.
 */
function Video_selectbox($id, $items, $default = null)
{
    $o = '<select id="'. $id . '">';
    foreach ($items as $key => $val) {
        $sel = isset($default) && $key == $default
            ? ' selected="selected"'
            : '';
        $o .= '<option value="' . htmlspecialchars($key, ENT_COMPAT, 'UTF-8')
            . '"' . $sel . '>' . htmlspecialchars($val, ENT_COMPAT, 'UTF-8')
            . '</option>';
    }
    $o .= '</select>';
    return $o;
}

/**
 * Returns the view of a single field (incl. its label).
 *
 * @param string $label A label.
 * @param string $field A field marked up as (X)HTML.
 *
 * @return (X)HTML.
 */
function Video_builderField($label, $field)
{
    $o = <<<EOT
    <p><label><span>$label</span>$field</label></p>

EOT;
    return $o;
}

/**
 * Returns the "call builder".
 *
 * @return string (X)HTML.
 *
 * @global array  The configuration of the plugins.
 * @global array  The localization of the plugins.
 * @global object The video model.
 */
function Video_adminMain()
{
    global $plugin_cf, $plugin_tx, $_Video;

    $pcf = $plugin_cf['video'];
    $ptx = $plugin_tx['video'];
    Video_includeJs();
    $o = '<!-- Video_XH: call builder -->' . PHP_EOL
        . '<h1>Video &ndash; ' . $ptx['menu_main'] . '</h1>' . PHP_EOL
        . '<div id="video_call_builder">' . PHP_EOL;
    $videos = $_Video->availableVideos();
    $videos = array_combine($videos, $videos);
    $field = Video_selectbox('video_name', $videos);
    $o .= Video_builderField($ptx['label_name'], $field);
    $field = Video_selectbox(
        'video_preload', Video_preloadOptions(), $pcf['default_preload']
    );
    $o .= Video_builderField($ptx['label_preload'], $field);
    foreach (array('autoplay', 'loop', 'controls', 'centered') as $key) {
        $id = 'video_' . $key;
        $check = $pcf['default_' . $key] ? ' checked="checked"' : '';
        $field = tag("input id=\"$id\" type=\"checkbox\"$check");
        $o .= Video_builderField($ptx["label_$key"], $field);
    }
    foreach (array('width', 'height') as $key) {
        $id = 'video_' . $key;
        $defaultKey = "default_$key";
        $field = tag("input id=\"$id\" type=\"text\" value=\"$pcf[$defaultKey]\"");
        $o .= Video_builderField($ptx["label_$key"], $field);
    }
    $field = Video_selectbox(
        'video_resize', Video_resizeOptions(), $pcf['default_resize']
    );
    $o .= Video_builderField($ptx['label_resize'], $field);
    $o .= '    <p><textarea id="video_call" readonly="readonly"></textarea></p>'
        . PHP_EOL;
    $o .= <<<EOT
</div>
<script type="text/javascript">/* <![CDATA[ */
VIDEO.initCallBuilder();
/* ]]> */</script>
EOT;
    return $o;
}

/*
 * Register plugin menu items.
 */
if (function_exists('XH_registerStandardPluginMenuItems')) {
    XH_registerStandardPluginMenuItems(true);
}

/*
 * Handle the plugin administration.
 */
if (function_exists('XH_wantsPluginAdministration')
    && XH_wantsPluginAdministration('video')
    || isset($video) && $video == 'true'
) {
    $o .= print_plugin_admin('on');
    switch ($admin) {
    case '':
        $o .= Video_aboutView() . tag('hr') . Video_systemCheckView();
        break;
    case 'plugin_main':
        $o .= Video_adminMain();
        break;
    default:
        $o .= plugin_admin_common($action, $admin, $plugin);
    }
}

/*
 * Pass the available videos to JavaScript for use in an editor.
 */
$temp = json_encode(array_values($_Video->availableVideos()));
Video_includeJs();
$hjs .= <<<EOT
<script type="text/javascript">/* <![CDATA[ */
VIDEO.availableVideos = $temp;
/* ]]> */</script>

EOT;

?>
