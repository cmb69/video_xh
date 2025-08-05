<?php

namespace Video;

use ApprovalTests\Approvals;
use PHPUnit\Framework\TestCase;
use Plib\FakeRequest;
use Plib\View;
use Video\Model\VideoFinder;

class ShowCallBuilderTest extends TestCase
{
    public function testRendersCallBuilder(): void
    {
        $videoFinder = $this->createStub(VideoFinder::class);
        $videoFinder->method('availableVideos')->willReturn([]);
        $subject = new ShowCallBuilder(
            "./",
            XH_includeVar("./config/config.php", "plugin_cf")['video'],
            $videoFinder,
            new View("./views/", XH_includeVar("./languages/en.php", "plugin_tx")['video'])
        );
        $response = $subject(true, new FakeRequest());
        $this->assertSame("Video – Call Builder", $response->title());
        Approvals::verifyHtml($response->output());
    }
}
