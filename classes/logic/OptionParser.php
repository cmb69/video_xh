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

namespace Video\Logic;

class OptionParser
{
    /** @var array<string,string> */
    private $config;

    /** @param array<string,string> $config */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /** @return array<string,string|true> */
    public function parse(string $query): array
    {
        $validOptions = array(
            'autoplay', 'class', 'controls', 'description', 'height', 'loop', 'preload',
            'title', 'width'
        );
        parse_str($query, $options);
        $res = array();
        foreach ($validOptions as $key) {
            if (isset($options[$key])) {
                assert(is_string($options[$key])); // @todo actually handle this
                $res[$key] = ($options[$key] === '') ? true : $options[$key];
            } else {
                $res[$key] = $this->config["default_$key"];
            }
        }
        return $res;
    }
}
