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

use function XH_includeVar;
use PHPUnit\Framework\TestCase;

class InfoControllerTest extends TestCase
{
    public function testRendersPluginInfo(): void
    {
        $systemCheckerStub = $this->createStub(SystemCheckService::class);
        $systemCheckerStub->method('checkPhpVersion')->willReturn(true);
        $systemCheckerStub->method('checkXhVersion')->willReturn(true);
        $systemCheckerStub->method('checkWritability')->willReturn(true);
        $subject = new InfoController(
            "./",
            XH_includeVar("./languages/en.php", "plugin_tx")['video'],
            $systemCheckerStub
        );
        $this->expectOutputString(
            <<<HTML

            <h1>Video 2.0-dev</h1>
            <div class="video_syscheck">
                <h2>System check</h2>
                <p class="xh_success">Checking that PHP version ≥ 5.4.0 … okay</p>
                <p class="xh_success">Checking that CMSimple_XH version ≥ 1.7.0 … okay</p>
                <p class="xh_success">Checking that './css/' is writable … okay</p>
                <p class="xh_success">Checking that './config' is writable … okay</p>
                <p class="xh_success">Checking that './languages/' is writable … okay</p>
            </div>

            HTML
        );
        $subject->defaultAction();
    }
}
