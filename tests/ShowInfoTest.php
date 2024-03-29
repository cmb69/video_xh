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
use ApprovalTests\Approvals;

use Video\Infra\SystemChecker;

class ShowInfoTest extends TestCase
{
    public function testRendersPluginInfo(): void
    {
        $systemCheckerStub = $this->createStub(SystemChecker::class);
        $systemCheckerStub->method('checkPhpVersion')->willReturn(true);
        $systemCheckerStub->method('checkXhVersion')->willReturn(true);
        $systemCheckerStub->method('checkWritability')->willReturn(true);

        $subject = new ShowInfo(
            "./",
            XH_includeVar("./languages/en.php", "plugin_tx")['video'],
            $systemCheckerStub
        );
        $response = $subject();
        Approvals::verifyHtml($response->output());
        $this->assertEquals("", $response->bjs());
    }
}
