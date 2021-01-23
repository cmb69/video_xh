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

namespace Video;

class Plugin
{
    const VERSION = '1.1';

    public function run()
    {
        if (XH_ADM) {
            XH_registerStandardPluginMenuItems(true);
            $this->registerTab();
            if (XH_wantsPluginAdministration('video')) {
                $this->handleAdministration();
            }
        }
    }

    private function registerTab()
    {
        global $pd_router, $pth, $plugin_cf;

        if ($plugin_cf['video']['show_tab']) {
            $pd_router->add_tab('Video', "{$pth['folder']['plugins']}video/Video_view.php");
        }
    }

    private function handleAdministration()
    {
        global $o, $admin, $action;

        $o .= print_plugin_admin('on');
        switch ($admin) {
            case '':
                ob_start();
                (new InfoController)->defaultAction();
                $o .= ob_get_clean();
                break;
            case 'plugin_main':
                ob_start();
                (new CallBuilderController)->defaultAction();
                $o .= ob_get_clean();
                break;
            default:
                $o .= plugin_admin_common($action, $admin, 'video');
        }
    }
}
