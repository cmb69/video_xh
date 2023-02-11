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
use Video\Dic;

/**
 * @var array<array<string>> $pth
 * @var array<array<string>> $plugin_cf
 * @var array<array<string>> $plugin_tx
 * @var string $admin
 * @var PageDataRouter $pd_router
 * @var string $o
 */

XH_registerStandardPluginMenuItems(true);

if ($plugin_cf['video']['show_tab']) {
    $pd_router->add_tab(
        $plugin_tx['video']['label_pdtab'],
        "{$pth['folder']['plugins']}video/video_view.php"
    );
}

if (XH_wantsPluginAdministration('video')) {
    $o .= print_plugin_admin('on');
    switch ($admin) {
        case '':
            $o .= Dic::makeInfoController()->defaultAction()->process();
            break;
        case 'plugin_main':
            $o .= Dic::makeCallBuilderController()->defaultAction()->process();
            break;
        default:
            $o .= plugin_admin_common();
    }
}
