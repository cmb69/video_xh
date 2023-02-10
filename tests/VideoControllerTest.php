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

class VideoControllerTest extends TestCase
{
    public function testRendersVideoWithPoster(): void
    {
        $model = $this->createStub(Model::class);
        $model->method('getOptions')->willReturn([
            'title' => "My Video",
            'description' => "This is a nice one & it's short",
            'preload' => "auto",
            'autoplay' => "0",
            'loop' => "0",
            'controls' => "1",
            'width' => "512",
            'height' => "288",
            'class' => "video_video",
        ]);
        $model->method('videoFiles')->willReturn([
            'my_video.mp4' => "mp4",
            'my_video.webm' => "webm",
        ]);
        $model->method('posterFile')->willReturn("my_video.jpg");
        $model->method('uploadDate')->willReturn(1674668829);
        $model->method('normalizedUrl')->willReturn("http://example.com/userfiles/media/my_video.mp4");
        $subject = new VideoController(
            "./",
            XH_includeVar("./languages/en.php", "plugin_tx")['video'],
            "en",
            $model,
            "my_video",
            ""
        );

        $response = $subject->defaultAction();

        Approvals::verifyString($response->representation());
    }

    public function testRendersVideoWithoutPoster(): void
    {
        $model = $this->createStub(Model::class);
        $model->method('getOptions')->willReturn([
            'title' => "My Video",
            'description' => "This is a nice one & it's short",
            'preload' => "auto",
            'autoplay' => "0",
            'loop' => "0",
            'controls' => "1",
            'width' => "512",
            'height' => "288",
            'class' => "video_video",
        ]);
        $model->method('videoFiles')->willReturn([
            'my_video.mp4' => "mp4",
            'my_video.webm' => "webm",
        ]);
        $model->method('uploadDate')->willReturn(1674668829);
        $model->method('normalizedUrl')->willReturn("http://example.com/userfiles/media/my_video.mp4");
        $subject = new VideoController(
            "./",
            XH_includeVar("./languages/en.php", "plugin_tx")['video'],
            "en",
            $model,
            "my_video",
            ""
        );

        $response = $subject->defaultAction();

        Approvals::verifyString($response->representation());
    }

    public function testReportsMissingVideo(): void
    {
        $model = $this->createStub(Model::class);
        $subject = new VideoController(
            "./",
            XH_includeVar("./languages/en.php", "plugin_tx")['video'],
            "en",
            $model,
            "no_video",
            ""
        );

        $response = $subject->defaultAction();

        Approvals::verifyString($response->representation());
    }
}
