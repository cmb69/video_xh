<?php

namespace Video;

use ApprovalTests\Approvals;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Plib\FakeRequest;
use Plib\View;
use Video\Model\Video;
use Video\Model\VideoFinder;

class ShowVideoTest extends TestCase
{
    private const OPTIONS = "title=My%20Video&description=This%20is%20a%20nice%20one%20%26%20it%27s%20short";

    /** @var array<string,string> */
    private $conf;

    /** @var VideoFinder&MockObject */
    private $videoFinder;

    private View $view;

    protected function setUp(): void
    {
        $this->conf = XH_includeVar("./config/config.php", "plugin_cf")['video'];
        $this->videoFinder = $this->createStub(VideoFinder::class);
        $this->view = new View("./views/", XH_includeVar("./languages/en.php", "plugin_tx")['video']);
    }

    private function sut(): ShowVideo
    {
        return new ShowVideo($this->conf, $this->videoFinder, $this->view);
    }

    public function testRendersVideoWithPoster(): void
    {
        $this->videoFinder->method('find')->willReturn($this->myVideo());
        $response = $this->sut()(new FakeRequest(["language" => "en"]), "my_video", self::OPTIONS);
        Approvals::verifyHtml($response->output());
    }

    public function testRendersVideoWithoutPoster(): void
    {
        $this->videoFinder->method('find')->willReturn($this->myVideo(false));
        $response = $this->sut()(new FakeRequest(["language" => "en"]), "my_video", self::OPTIONS);
        Approvals::verifyHtml($response->output());
    }

    public function testReportsMissingVideo(): void
    {
        $response = $this->sut()(new FakeRequest(["language" => "en"]), "no_video", self::OPTIONS);
        Approvals::verifyHtml($response->output());
    }

    public function testRendersEmptyMetaName(): void
    {
        $this->videoFinder->method('find')->willReturn($this->myVideo());
        $response = $this->sut()(new FakeRequest(), "my_video");
        $this->assertStringContainsString('<meta itemprop="name" content="">', $response->output());
    }

    private function myVideo(bool $poster = true): Video
    {
        return new Video(
            [
                './userfiles/media/my_video.mp4' => "video/mp4",
                './userfiles/media/my_video.webm' => "video/webm",
            ],
            $poster ? "./userfiles/media/my_video.jpg" : null,
            null,
            1674668829
        );
    }
}
