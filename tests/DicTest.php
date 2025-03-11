<?php

namespace Video;

use PHPUnit\Framework\TestCase;

class DicTest extends TestCase
{
    public function testMakesShowVideo(): void
    {
        global $pth, $plugin_cf, $plugin_tx;

        $pth = ["folder" => ["media" => "", "plugins" => ""]];
        $plugin_cf = ["video" => []];
        $plugin_tx = ["video" => []];
        $this->isInstanceOf(ShowVideo::class, Dic::makeShowVideo());
    }

    public function testMakesShowInfo(): void
    {
        global $pth, $plugin_tx;

        $pth = ["folder" => ["plugins" => ""]];
        $plugin_tx = ["video" => []];
        $this->isInstanceOf(ShowInfo::class, Dic::makeShowInfo());
    }

    public function testMakesShowCallBuilder(): void
    {
        global $pth, $plugin_cf, $plugin_tx;

        $pth = ["folder" => ["media" => "", "plugins" => ""]];
        $plugin_cf = ["video" => []];
        $plugin_tx = ["video" => []];
        $this->isInstanceOf(ShowCallBuilder::class, Dic::makeShowCallBuilder());
    }
}