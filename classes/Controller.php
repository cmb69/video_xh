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

class Controller
{
    /**
     * @var string
     */
    protected $pluginFolder;

    /**
     * @var array<string>
     */
    protected $config;

    /**
     * @var array<string>
     */
    protected $lang;

    /**
     * @var Model
     */
    protected $model;

    public function __construct()
    {
        global $pth, $plugin_cf, $plugin_tx;

        $this->pluginFolder = "{$pth['folder']['plugins']}video/";
        $this->config = $plugin_cf['video'];
        $this->lang = $plugin_tx['video'];
        $this->model = new Model($pth['folder']['media'], $this->config);
    }

    /**
     * @param string $filename
     * @return void
     */
    protected function addScript($filename)
    {
        global $bjs;

        $bjs .= sprintf('<script type="text/javascript" src="%s"></script>', XH_hsc($filename));
    }
}
