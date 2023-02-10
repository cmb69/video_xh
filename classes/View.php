<?php

/**
 * Copyright 2017-2023 Christoph M. Becker
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

class View
{
    /** @var string */
    private $templateFolder;

    /** @var array<string> */
    private $lang;

    /**
     * @param string $templateFolder
     * @param array<string> $lang
     */
    public function __construct($templateFolder, array $lang)
    {
        $this->templateFolder = $templateFolder;
        $this->lang = $lang;
    }

    /**
     * @param string $key
     * @param scalar $args
     * @return string
     */
    public function text($key, ...$args)
    {
        return $this->escape(sprintf($this->lang[$key], ...$args));
    }

    /**
     * @param string $_template
     * @param array<mixed> $_data
     * @return string
     */
    public function render($_template, array $_data)
    {
        extract($_data);
        ob_start();
        include "{$this->templateFolder}{$_template}.php";
        return (string) ob_get_clean();
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    public function escape($value)
    {
        if ($value instanceof HtmlString) {
            return $value;
        } else {
            return XH_hsc($value);
        }
    }
}
