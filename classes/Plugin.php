<?php

/**
 * Copyright 2012-2023 Christoph M. Becker
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
    const VERSION = '2.0-dev';

    /** @return void */
    public function run()
    {
        if (defined("XH_ADM") && XH_ADM) {
            XH_registerStandardPluginMenuItems(true);
            $this->registerTab();
            if (XH_wantsPluginAdministration('video')) {
                $this->handleAdministration();
            }
        }
    }

    /** @return void */
    private function registerTab()
    {
        global $pd_router, $pth, $plugin_cf;

        if ($plugin_cf['video']['show_tab']) {
            $pd_router->add_tab('Video', "{$pth['folder']['plugins']}video/Video_view.php");
        }
    }

    /** @return void */
    private function handleAdministration()
    {
        global $o, $admin, $pth, $plugin_cf, $plugin_tx, $sl;

        $o .= print_plugin_admin('on');
        switch ($admin) {
            case '':
                ob_start();
                (new InfoController("{$pth['folder']['plugins']}video/", $plugin_tx['video']))->defaultAction();
                $o .= ob_get_clean();
                break;
            case 'plugin_main':
                ob_start();
                $controller = new CallBuilderController(
                    "{$pth['folder']['plugins']}video/",
                    $plugin_cf['video'],
                    $plugin_tx['video'],
                    new Model($pth['folder']['media'], $plugin_cf['video'], $sl)
                );
                $controller->defaultAction();
                $o .= ob_get_clean();
                break;
            default:
                $o .= plugin_admin_common();
        }
    }
}
