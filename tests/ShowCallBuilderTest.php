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

use ApprovalTests\Approvals;
use PHPUnit\Framework\TestCase;
use Plib\View;
use Video\Model\VideoFinder;

class ShowCallBuilderTest extends TestCase
{
    public function testRendersCallBuilder(): void
    {
        $videoFinder = $this->createStub(VideoFinder::class);
        $videoFinder->method('availableVideos')->willReturn([]);
        $subject = new ShowCallBuilder(
            "./",
            XH_includeVar("./config/config.php", "plugin_cf")['video'],
            $videoFinder,
            new View("./views/", XH_includeVar("./languages/en.php", "plugin_tx")['video'])
        );

        $response = $subject();

        Approvals::verifyHtml($response->output());
    }
}
