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

class CallBuilderControllerTest extends TestCase
{
    public function testIt(): void
    {
        $model = $this->createStub(Model::class);
        $model->method('availableVideos')->willReturn([]);
        $subject = new CallBuilderController(
            "./",
            XH_includeVar("./config/config.php", "plugin_cf")['video'],
            XH_includeVar("./languages/en.php", "plugin_tx")['video'],
            $model
        );
        $response = $subject->defaultAction();
        $expected = new Response(
            <<<'HTML'

            <h1>Video – Call Builder</h1>
            <script type="text/x-template" id="video_call_builder">
              <form id="video_call_builder">
                <p>
                  <label for="video_name">Video</label>
                  <select id="video_name">
                  </select>
                </p>
                <p>
                  <label for="video_title">Title</label>
                  <input id="video_title" type="text" value="">
                </p>
                <p>
                  <label for="video_description">Description</label>
                  <textarea id="video_description"></textarea>
                </p>
                <p>
                  <label for="video_preload">Preload</label>
                  <select id="video_preload">
                    <option value="auto" selected>Auto</option>
                    <option value="metadata" >Metadata</option>
                    <option value="none" >None</option>
                  </select>
                </p>
                <p>
                  <label for="video_autoplay">Autoplay</label>
                  <input id="video_autoplay" type="checkbox" >
                </p>
                <p>
                  <label for="video_loop">Loop</label>
                  <input id="video_loop" type="checkbox" >
                </p>
                <p>
                  <label for="video_controls">Controls</label>
                  <input id="video_controls" type="checkbox" checked>
                </p>
                <p>
                  <label for="video_width">Width</label>
                  <input id="video_width" type="text" value="512">
                </p>
                <p>
                  <label for="video_height">Height</label>
                  <input id="video_height" type="text" value="288">
                </p>
                <p>
                  <label for="video_class">CSS Class</label>
                  <input id="video_class" type="text" value="video_video">
                </p>
                <p>
                  <textarea id="video_call"></textarea>
                </p>
              </form>
            </script>

            HTML,
            '<script type="text/javascript" src="./video.min.js"></script>'
        );

        $this->assertEquals($expected, $response);
    }
}
