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
use ApprovalTests\Approvals;
use PHPUnit\Framework\TestCase;
use Plib\FakeRequest;
use Plib\View;
use Video\Value\Video;
use Video\Infra\VideoFinder;
use Video\Logic\OptionParser;

class ShowVideoTest extends TestCase
{
    private const OPTIONS = "title=My%20Video&description=This%20is%20a%20nice%20one%20%26%20it%27s%20short";

    /** @var ShowVideo */
    private $sut;

    /** @var VideoFinder&MockObject */
    private $videoFinder;

    public function setUp(): void
    {
        $this->videoFinder = $this->createStub(VideoFinder::class);
        $this->sut = new ShowVideo(
            new OptionParser(XH_includeVar("./config/config.php", "plugin_cf")['video']),
            $this->videoFinder,
            new View("./views/", XH_includeVar("./languages/en.php", "plugin_tx")['video'])
        );
    }

    public function testRendersVideoWithPoster(): void
    {
        $this->videoFinder->method('find')->willReturn(new Video(
            [
                './userfiles/media/my_video.mp4' => "mp4",
                './userfiles/media/my_video.webm' => "webm",
            ],
            "./userfiles/media/my_video.jpg",
            null,
            1674668829
        ));
        $response = ($this->sut)(new FakeRequest(["language" => "en"]), "my_video", self::OPTIONS);
        Approvals::verifyHtml($response->output());
    }

    public function testRendersVideoWithoutPoster(): void
    {
        $this->videoFinder->method('find')->willReturn(new Video(
            [
                './userfiles/media/my_video.mp4' => "mp4",
                './userfiles/media/my_video.webm' => "webm",
            ],
            null,
            null,
            1674668829
        ));
        $response = ($this->sut)(new FakeRequest(["language" => "en"]), "my_video", self::OPTIONS);
        Approvals::verifyHtml($response->output());
    }

    public function testReportsMissingVideo(): void
    {
        $response = ($this->sut)(new FakeRequest(["language" => "en"]), "no_video", self::OPTIONS);
        Approvals::verifyHtml($response->output());
    }
}
