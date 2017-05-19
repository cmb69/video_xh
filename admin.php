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

/**
 * @return string
 */
function Video_aboutView()
{
    global $pth;

    $view = new Video\View('info');
    $view->logo = "{$pth['folder']['plugins']}video/video.png";
    $view->version = VIDEO_VERSION;
    return (string) $view;
}

/**
 * @return string
 */
function Video_systemCheckView()
{
    global $pth, $tx, $plugin_tx;

    $phpVersion = '5.4.0';
    $ptx = $plugin_tx['video'];
    $imgdir = $pth['folder']['plugins'] . 'video/images/';
    $ok = '<img src="' . $imgdir . 'ok.png" alt="ok">';
    $warn = '<img src="' . $imgdir . 'warn.png" alt="warning">';
    $fail = '<img src="' . $imgdir . 'fail.png" alt="failure">';
    $o = '<h4>' . $ptx['syscheck_title'] . '</h4>' . PHP_EOL
        . (version_compare(PHP_VERSION, $phpVersion) >= 0 ? $ok : $fail)
        . '&nbsp;&nbsp;' . sprintf($ptx['syscheck_phpversion'], $phpVersion)
        . '<br>' . PHP_EOL;
    foreach (array('json') as $ext) {
        $o .= (extension_loaded($ext) ? $ok : $fail)
            . '&nbsp;&nbsp;' . sprintf($ptx['syscheck_extension'], $ext)
            . '<br>' . PHP_EOL;
    }
    $o .= (strtoupper($tx['meta']['codepage']) == 'UTF-8' ? $ok : $warn)
        . '&nbsp;&nbsp;' . $ptx['syscheck_encoding']
        . '<br>' . '<br>' . PHP_EOL;
    foreach (array('config/', 'css/', 'languages/') as $folder) {
        $folders[] = $pth['folder']['plugins'] . 'video/' . $folder;
    }
    foreach ($folders as $folder) {
        $o .= (is_writable($folder) ? $ok : $warn)
            . '&nbsp;&nbsp;' . sprintf($ptx['syscheck_writable'], $folder)
            . '<br>' . PHP_EOL;
    }
    return $o;
}

XH_registerStandardPluginMenuItems(true);

if (XH_wantsPluginAdministration('video')) {
    $o .= print_plugin_admin('on');
    switch ($admin) {
        case '':
            $o .= Video_aboutView() . '<hr>' . Video_systemCheckView();
            break;
        case 'plugin_main':
            ob_start();
            (new Video\CallBuilderController)->defaultAction();
            $o .= ob_get_clean();
            break;
        default:
            $o .= plugin_admin_common($action, $admin, $plugin);
    }
}

$temp = json_encode(array_values($_Video->availableVideos()));
Video_includeJs();
$hjs .= <<<EOT
<script type="text/javascript">/* <![CDATA[ */
VIDEO.availableVideos = $temp;
/* ]]> */</script>

EOT;

