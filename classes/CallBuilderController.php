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

use stdClass;

class CallBuilderController extends Controller
{
    /**
     * @return void
     */
    public function defaultAction()
    {
        global $pth, $plugin_tx;

        $this->addScript("{$this->pluginFolder}video.min.js");
        $view = new View("{$pth['folder']['plugins']}video/views/", $plugin_tx['video']);
        $view->render('call-builder', [
            "videos" => $this->model->availableVideos(),
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
    }

    /**
     * @return array<stdClass>
     */
    private function preloadOptions()
    {
        $options = [];
        foreach (array('auto', 'metadata', 'none') as $id) {
            $label = $this->lang["preload_{$id}"];
            $selected = $id === $this->config['default_preload'] ? 'selected' : '';
            $options[] = (object) compact('id', 'label', 'selected');
        }
        return $options;
    }

    /**
     * @param string $filename
     * @return void
     */
    private function addScript($filename)
    {
        global $bjs;

        $bjs .= sprintf('<script type="text/javascript" src="%s"></script>', XH_hsc($filename));
    }
}
