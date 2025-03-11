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

use Plib\Response;
use Plib\SystemChecker;
use Video\Infra\View;

class ShowInfo
{
    /** @var string $pluginFolder */
    private $pluginFolder;

    /** @var array<string,string> */
    private $lang;

    /** @var SystemChecker */
    private $systemChecker;

    /** @param array<string,string> $lang */
    public function __construct(string $pluginFolder, array $lang, SystemChecker $systemChecker)
    {
        $this->pluginFolder = $pluginFolder;
        $this->lang = $lang;
        $this->systemChecker = $systemChecker;
    }

    public function __invoke(): Response
    {
        $view = new View("{$this->pluginFolder}views/", $this->lang);
        $output = $view->render('info', [
            "version" => VIDEO_VERSION,
            "checks" => [
                $this->checkPhpVersion('7.1.0'),
                $this->checkXhVersion('1.7.0'),
                $this->checkPlibVersion('1.1.0'),
                $this->checkWritability("{$this->pluginFolder}css/"),
                $this->checkWritability("{$this->pluginFolder}config"),
                $this->checkWritability("{$this->pluginFolder}languages/")
            ],
        ]);
        return Response::create($output);
    }

    /** @return array{class:string,label:string,stateLabel:string} */
    private function checkPhpVersion(string $version): array
    {
        $state = $this->systemChecker->checkVersion(PHP_VERSION, $version) ? 'success' : 'fail';
        return [
            'class' => "xh_$state",
            'label' => sprintf($this->lang['syscheck_phpversion'], $version),
            'stateLabel' => $this->lang["syscheck_$state"],
        ];
    }

    /** @return array{class:string,label:string,stateLabel:string} */
    private function checkXhVersion(string $version): array
    {
        $state = $this->systemChecker->checkVersion(CMSIMPLE_XH_VERSION, "CMSimple_XH $version") ? 'success' : 'fail';
        return [
            'class' => "xh_$state",
            'label' => sprintf($this->lang['syscheck_xhversion'], $version),
            'stateLabel' => $this->lang["syscheck_$state"],
        ];
    }

    /** @return array{class:string,label:string,stateLabel:string} */
    private function checkPlibVersion(string $version)
    {
        $state = $this->systemChecker->checkPlugin("plib", $version) ? 'success' : 'fail';
        return [
            'class' => "xh_$state",
            'label' => sprintf($this->lang['syscheck_plibversion'], $version),
            'stateLabel' => $this->lang["syscheck_$state"],
        ];
    }

    /** @return array{class:string,label:string,stateLabel:string} */
    private function checkWritability(string $folder): array
    {
        $state = $this->systemChecker->checkWritability($folder) ? 'success' : 'warning';
        return [
            'class' => "xh_$state",
            'label' => sprintf($this->lang['syscheck_writable'], $folder),
            'stateLabel' => $this->lang["syscheck_$state"],
        ];
    }
}
