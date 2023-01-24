<?php

/**
 * Copyright 2012-2017 Christoph M. Becker
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

class CallBuilderController extends Controller
{
    /**
     * @return void
     */
    public function defaultAction()
    {
        $this->addScript("{$this->pluginFolder}video.min.js");
        $view = new View('call-builder');
        $view->videos = $this->model->availableVideos();
        $view->title = $this->config['default_title'];
        $view->description = $this->config['default_description'];
        $view->preloadOptions = $this->preloadOptions();
        $view->autoplay = $this->config['default_autoplay'] ? 'checked' : '';
        $view->loop = $this->config['default_loop'] ? 'checked' : '';
        $view->controls = $this->config['default_controls'] ? 'checked' : '';
        $view->width = $this->config['default_width'];
        $view->height = $this->config['default_height'];
        $view->className = $this->config['default_class'];
        $view->render();
    }

    /**
     * @return array
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
}
