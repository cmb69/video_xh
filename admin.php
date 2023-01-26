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

use XH\PageDataRouter;
use Video\CallBuilderController;
use Video\InfoController;
use Video\Model;
use Video\SystemCheckService;

/**
 * @var array<array<string>> $pth
 * @var array<array<string>> $plugin_cf
 * @var array<array<string>> $plugin_tx
 * @var string $sl
 * @var string $admin
 * @var PageDataRouter $pd_router
 * @var string $o
 */

XH_registerStandardPluginMenuItems(true);

if ($plugin_cf['video']['show_tab']) {
    $pd_router->add_tab('Video', "{$pth['folder']['plugins']}video/Video_view.php");
}

if (XH_wantsPluginAdministration('video')) {
    $o .= print_plugin_admin('on');
    switch ($admin) {
        case '':
            $temp = new InfoController(
                "{$pth['folder']['plugins']}video/",
                $plugin_tx['video'],
                new SystemCheckService()
            );
            $o .= $temp->defaultAction()->process();
            break;
        case 'plugin_main':
            $temp = new CallBuilderController(
                "{$pth['folder']['plugins']}video/",
                $plugin_cf['video'],
                $plugin_tx['video'],
                new Model($pth['folder']['media'], $plugin_cf['video'], $sl)
            );
            $o .= $temp->defaultAction()->process();
            break;
        default:
            $o .= plugin_admin_common();
    }
}
