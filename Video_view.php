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

use Video\Model;

/** @return string */
function Video_view()
{
    global $pth, $plugin_cf, $plugin_tx;

    ob_start();
    $controller = new Video\CallBuilderController(
        "{$pth['folder']['plugins']}video/",
        $plugin_cf['video'],
        $plugin_tx['video'],
        new Model($pth['folder']['media'], $plugin_cf['video'])
    );
    $controller->defaultAction();
    return (string) ob_get_clean();
}
