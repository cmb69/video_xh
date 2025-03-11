<?php

/**
 * Copyright 2023 Christoph M. Becker
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

use Plib\SystemChecker;
use Video\Infra\VideoFinder;
use Video\Logic\OptionParser;

class Dic
{
    public static function makeShowVideo(): ShowVideo
    {
        global $pth, $sl, $plugin_cf, $plugin_tx;

        return new ShowVideo(
            "{$pth['folder']['plugins']}video/",
            $plugin_tx["video"],
            $sl,
            new OptionParser($plugin_cf['video']),
            self::makeVideoFinder()
        );
    }

    public static function makeShowInfo(): ShowInfo
    {
        global $pth, $plugin_tx;

        return new ShowInfo(
            "{$pth['folder']['plugins']}video/",
            $plugin_tx['video'],
            new SystemChecker()
        );
    }

    public static function makeShowCallBuilder(): ShowCallBuilder
    {
        global $pth, $plugin_cf, $plugin_tx;

        return new ShowCallBuilder(
            "{$pth['folder']['plugins']}video/",
            $plugin_cf['video'],
            $plugin_tx['video'],
            self::makeVideoFinder()
        );
    }

    private static function makeVideoFinder(): VideoFinder
    {
        global $pth, $sl;

        return new VideoFinder($pth['folder']['media'], $sl);
    }
}
