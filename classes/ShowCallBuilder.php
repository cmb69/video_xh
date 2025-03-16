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
use Plib\View;
use Video\Model\VideoFinder;

class ShowCallBuilder
{
    /** @var string */
    private $pluginFolder;

    /** @var array<string,string> */
    private $config;

    /** @var VideoFinder */
    private $videoFinder;

    /** @var View */
    private $view;

    /** @param array<string,string> $config */
    public function __construct(string $pluginFolder, array $config, VideoFinder $videoFinder, View $view)
    {
        $this->pluginFolder = $pluginFolder;
        $this->config = $config;
        $this->videoFinder = $videoFinder;
        $this->view = $view;
    }

    public function __invoke(bool $showTitle): Response
    {
        $output = $this->view->render('call-builder', [
            "videos" => $this->videoFinder->availableVideos(),
            "title" => $this->config['default_title'],
            "description" => $this->config['default_description'],
            "preloadOptions" => $this->preloadOptions(),
            "autoplay" => $this->config['default_autoplay'] ? 'checked' : '',
            "loop" => $this->config['default_loop'] ? 'checked' : '',
            "controls" => $this->config['default_controls'] ? 'checked' : '',
            "width" => $this->config['default_width'],
            "height" => $this->config['default_height'],
            "className" => $this->config['default_class'],
            "script" => "{$this->pluginFolder}video.min.js",
            "show_title" => $showTitle,
        ]);
        $response =  Response::create($output);
        if ($showTitle) {
            $response = $response->withTitle($this->view->esc("Video – ") . $this->view->text('menu_main'));
        }
        return $response;
    }

    /** @return list<array{id:string,label:string,selected:string}> */
    private function preloadOptions(): array
    {
        $options = [];
        foreach (array('auto', 'metadata', 'none') as $id) {
            $label = $this->view->plain("preload_{$id}");
            $selected = $id === $this->config['default_preload'] ? 'selected' : '';
            $options[] = compact('id', 'label', 'selected');
        }
        return $options;
    }
}
