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

        $this->assertEquals(
            new Response(<<<'HTML'

                <div itemprop="video" itemscope itemtype="http://schema.org/VideoObject">
                  <meta itemprop="name" content="My Video">
                  <meta itemprop="description" content="This is a nice one &amp; it's short">
                  <meta itemprop="contentURL" content="http://example.com/userfiles/media/my_video.mp4">
                  <meta itemprop="thumbnailUrl" content="http://example.com/userfiles/media/my_video.mp4">
                  <meta itemprop="uploadDate" content="2023-01-25T17:47:09+00:00">
                  <video class="video_video" class="" controls="controls" preload="auto" width="512" height="288" poster="my_video.jpg">
                    <source src="my_video.mp4" type="video/mp4">
                    <source src="my_video.webm" type="video/webm">
                    <a href="my_video.mp4"><img src="my_video.jpg" alt="Download my_video.mp4 video" title="Download my_video.mp4 video" class="video_video"></a>
                  </video>
                </div>

                HTML
            ),
            $response
        );
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

        $this->assertEquals(
            new Response(<<<'HTML'

                <div itemprop="video" itemscope itemtype="http://schema.org/VideoObject">
                  <meta itemprop="name" content="My Video">
                  <meta itemprop="description" content="This is a nice one &amp; it's short">
                  <meta itemprop="contentURL" content="http://example.com/userfiles/media/my_video.mp4">
                  <meta itemprop="uploadDate" content="2023-01-25T17:47:09+00:00">
                  <video class="video_video" class="" controls="controls" preload="auto" width="512" height="288">
                    <source src="my_video.mp4" type="video/mp4">
                    <source src="my_video.webm" type="video/webm">
                    <a href="my_video.mp4">Download my_video.mp4 video</a>
                  </video>
                </div>

                HTML
            ),
            $response
        );
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

        $this->assertEquals(
            new Response('<p class="xh_fail">Video &quot;no_video&quot; missing!</p>'),
            $response
        );
    }
}
