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

use Video\Infra\Response;
use Video\Infra\VideoFinder;
use Video\Infra\View;

class ShowCallBuilder
{
    /** @var string */
    private $pluginFolder;

    /** @var array<string,string> */
    private $config;

    /** @var array<string,string> */
    private $lang;

    /** @var VideoFinder */
    private $videoFinder;

    /**
     * @param array<string,string> $config
     * @param array<string,string> $lang
     */
    public function __construct(string $pluginFolder, array $config, array $lang, VideoFinder $videoFinder)
    {
        $this->pluginFolder = $pluginFolder;
        $this->config = $config;
        $this->lang = $lang;
        $this->videoFinder = $videoFinder;
    }

    public function __invoke(): Response
    {
        $view = new View("{$this->pluginFolder}views/", $this->lang);
        $output = $view->render('call-builder', [
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
        ]);
        return new Response($output, $this->renderScript("{$this->pluginFolder}video.min.js"));
    }

    /** @return list<array{id:string,label:string,selected:string}> */
    private function preloadOptions(): array
    {
        $options = [];
        foreach (array('auto', 'metadata', 'none') as $id) {
            $label = $this->lang["preload_{$id}"];
            $selected = $id === $this->config['default_preload'] ? 'selected' : '';
            $options[] = compact('id', 'label', 'selected');
        }
        return $options;
    }

    private function renderScript(string $filename): string
    {
        return sprintf('<script type="text/javascript" src="%s"></script>', XH_hsc($filename));
    }
}
