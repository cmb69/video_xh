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
            "checks" => $this->getChecks(),
        ]);
    }

    /**
     * @return object[]
     */
    private function getChecks()
    {
        return array(
            $this->checkPhpVersion('5.4.0'),
            $this->checkXhVersion('1.7.0'),
            $this->checkWritability("{$this->pluginFolder}css/"),
            $this->checkWritability("{$this->pluginFolder}config"),
            $this->checkWritability("{$this->pluginFolder}languages/")
        );
    }

    /**
     * @param string $version
     * @return object
     */
    private function checkPhpVersion($version)
    {
        $state = $this->systemChecker->checkPhpVersion($version) ? 'success' : 'fail';
        $label = sprintf($this->lang['syscheck_phpversion'], $version);
        $stateLabel = $this->lang["syscheck_$state"];
        return (object) compact('state', 'label', 'stateLabel');
    }

    /**
     * @param string $version
     * @return object
     */
    private function checkXhVersion($version)
    {
        $state = $this->systemChecker->checkXhVersion($version) ? 'success' : 'fail';
        $label = sprintf($this->lang['syscheck_xhversion'], $version);
        $stateLabel = $this->lang["syscheck_$state"];
        return (object) compact('state', 'label', 'stateLabel');
    }

    /**
     * @param string $folder
     * @return object
     */
    private function checkWritability($folder)
    {
        $state = $this->systemChecker->checkWritability($folder) ? 'success' : 'warning';
        $label = sprintf($this->lang['syscheck_writable'], $folder);
        $stateLabel = $this->lang["syscheck_$state"];
        return (object) compact('state', 'label', 'stateLabel');
    }
}
