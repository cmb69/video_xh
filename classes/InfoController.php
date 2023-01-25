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

class InfoController
{
    /** @var string $pluginFolder */
    private $pluginFolder;

    /** @var array<string> */
    private $lang;

    /** @var SystemChecker */
    private $systemChecker;

    /**
     * @param string $pluginFolder
     * @param array<string> $lang
     */
    public function __construct($pluginFolder, array $lang, SystemChecker $systemChecker)
    {
        $this->pluginFolder = $pluginFolder;
        $this->lang = $lang;
        $this->systemChecker = $systemChecker;
    }

    /** @return void */
    public function defaultAction()
    {
        $view = new View("{$this->pluginFolder}views/", $this->lang);
        $view->render('info', [
            "version" => Plugin::VERSION,
            "checks" => (new SystemCheckService($this->pluginFolder, $this->lang, $this->systemChecker))->getChecks(),
        ]);
    }
}
