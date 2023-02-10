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

namespace Video\Value;

class Video
{
    /** @var array<string,string> */
    private $sources;

    /** @var string|null */
    private $poster;

    /** @var string|null */
    private $subtitle;

    /** @var int */
    private $date;

    /** @param array<string,string> $sources */
    public function __construct(array $sources, ?string $poster, ?string $subtitle, int $date)
    {
        $this->sources = $sources;
        $this->poster = $poster;
        $this->subtitle = $subtitle;
        $this->date = $date;
    }

    public function filename(): string
    {
        assert(!empty($this->sources));
        return key($this->sources);
    }

    /** @return array<string,string> */
    public function sources(): array
    {
        return $this->sources;
    }

    public function poster(): ?string
    {
        return $this->poster;
    }

    public function subtitle(): ?string
    {
        return $this->subtitle;
    }

    public function date(): int
    {
        return $this->date;
    }
}
