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
     * @return string
     */
    public function defaultAction()
    {
        Video_includeJs();
        $view = new View('call-builder');
        $videos = $this->model->availableVideos();
        $videos = array_combine($videos, $videos);
        $field = $this->selectbox('video_name', $videos);
        $view->nameSelect = new HtmlString($field);
        $field = $this->selectbox('video_preload', $this->preloadOptions(), $this->config['default_preload']);
        $view->preloadSelect = new HtmlString($field);
        foreach (array('autoplay', 'loop', 'controls') as $key) {
            $id = 'video_' . $key;
            $check = $this->config['default_' . $key] ? ' checked="checked"' : '';
            $field = "<input id=\"$id\" type=\"checkbox\"$check>";
            $view->{"{$key}Input"} = new HtmlString($field);
        }
        foreach (array('width', 'height') as $key) {
            $id = 'video_' . $key;
            $defaultKey = "default_$key";
            $field = "<input id=\"$id\" type=\"text\" value=\"{$this->config[$defaultKey]}\">";
            $view->{"{$key}Input"} = new HtmlString($field);
        }
        $field = $this->selectbox('video_resize', $this->resizeOptions(), $this->config['default_resize']);
        $view->resizeSelect = new HtmlString($field);
        $view->render();
    }

    /**
     * @param string $id
     * @param array $items
     * @param string $default
     * @return string
     */
    private function selectbox($id, $items, $default = null)
    {
        $o = '<select id="'. $id . '">';
        foreach ($items as $key => $val) {
            $sel = isset($default) && $key == $default
                ? ' selected="selected"'
                : '';
            $o .= '<option value="' . XH_hsc($key)
                . '"' . $sel . '>' . XH_hsc($val)
                . '</option>';
        }
        $o .= '</select>';
        return $o;
    }

    /**
     * @return array
     */
    private function preloadOptions()
    {
        $options = array();
        foreach (array('auto', 'metadata', 'none') as $key) {
            $options[$key] = $this->lang['preload_' . $key];
        }
        return $options;
    }

    /**
     * @return array
     */
    private function resizeOptions()
    {
        $options = array();
        foreach (array('no', 'shrink', 'full') as $key) {
            $options[$key] = $this->lang['resize_' . $key];
        }
        return $options;
    }
}
