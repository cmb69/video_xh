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
use Video\VideoController;

const VIDEO_VERSION = '2.0-dev';

/**
 * @param string $name
 * @param string $options
 * @return string
 */
function video($name, $options = '')
{
    global $pth, $sl, $plugin_cf, $plugin_tx;

    $controller = new VideoController(
        "{$pth['folder']['plugins']}video/",
        $plugin_tx["video"],
        $sl,
        new Model($pth['folder']['media'], $plugin_cf['video'], $sl),
        $name,
        $options
    );
    return $controller->defaultAction()->process();
}
