<?php

namespace Video;

use ApprovalTests\Approvals;
use PHPUnit\Framework\TestCase;
use Plib\FakeSystemChecker;
use Plib\View;

class ShowInfoTest extends TestCase
{
    public function testRendersPluginInfo(): void
    {
        $subject = new ShowInfo(
            "./",
            new FakeSystemChecker(true),
            new View("./views/", XH_includeVar("./languages/en.php", "plugin_tx")['video'])
        );
        $response = $subject();
        Approvals::verifyHtml($response->output());
    }
}
