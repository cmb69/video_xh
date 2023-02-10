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

use PHPUnit\Framework\TestCase;
use function XH_includeVar;

class OptionParserTest extends TestCase
{
    /** @var OptionParser */
    private $sut;

    public function setUp(): void
    {
        $this->sut = new OptionParser(XH_includeVar("./config/config.php", 'plugin_cf')['video']);
    }

    /**
     * @param array<string> $expected
     * @dataProvider dataForParsesOptions
     */
    public function testParsesOptions(string $query, array $expected): void
    {
        $actual = $this->sut->parse($query);
        $this->assertEquals($expected, $actual);
    }

    /** @return array<array{string,array<string>}> */
    public function dataForParsesOptions(): array
    {
        return array(
            array(
                '',
                array(
                    'autoplay' => '0',
                    'controls' => '1',
                    'description' => '',
                    'height' => '288',
                    'loop' => '0',
                    'preload' => 'auto',
                    'class' => 'video_video',
                    'title' => '',
                    'width' => '512'
                )
            ),
            array(
                'autoplay=1&controls=0&description=blah%20blah&height=360&loop=1'
                    . '&preload=metadata&class=video_video&title=foo&width=640',
                array(
                    'autoplay' => '1',
                    'controls' => '0',
                    'description' => 'blah blah',
                    'height' => '360',
                    'loop' => '1',
                    'preload' => 'metadata',
                    'class' => 'video_video',
                    'title' => 'foo',
                    'width' => '640'
                )
            )
        );
    }
}
