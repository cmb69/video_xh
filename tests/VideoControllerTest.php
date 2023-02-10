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
    /** @var VideoController */
    private $sut;

    /** @var Model&MockObject */
    private $model;

    public function setUp(): void
    {
        $this->model = $this->createStub(Model::class);
        $this->model->method('getOptions')->willReturn([
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
        $this->model->method('uploadDate')->willReturn(1674668829);
        $this->model->method('normalizedUrl')->willReturn("http://example.com/userfiles/media/my_video.mp4");
        $this->sut = new VideoController(
            "./",
            XH_includeVar("./languages/en.php", "plugin_tx")['video'],
            "en",
            $this->model
        );
    }

    public function testRendersVideoWithPoster(): void
    {
        $this->model->method('videoFiles')->willReturn([
            'my_video.mp4' => "mp4",
            'my_video.webm' => "webm",
        ]);
        $this->model->method('posterFile')->willReturn("my_video.jpg");
        $response = $this->sut->defaultAction("my_video", "");
        Approvals::verifyString($response->representation());
    }

    public function testRendersVideoWithoutPoster(): void
    {
        $this->model->method('videoFiles')->willReturn([
            'my_video.mp4' => "mp4",
            'my_video.webm' => "webm",
        ]);
        $response = $this->sut->defaultAction("my_video", "");
        Approvals::verifyString($response->representation());
    }

    public function testReportsMissingVideo(): void
    {
        $response = $this->sut->defaultAction("no_video", "");
        Approvals::verifyString($response->representation());
    }
}
