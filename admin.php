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
    $view->checks = (new Video\SystemCheckService)->getChecks();
    return (string) $view;
}

XH_registerStandardPluginMenuItems(true);

if (XH_wantsPluginAdministration('video')) {
    $o .= print_plugin_admin('on');
    switch ($admin) {
        case '':
            $o .= Video_aboutView();
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
