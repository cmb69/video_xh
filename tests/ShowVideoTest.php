<?php

namespace Video;

use function XH_includeVar;
use ApprovalTests\Approvals;
use PHPUnit\Framework\TestCase;
use Plib\FakeRequest;
use Plib\View;
use Video\Model\Video;
use Video\Model\VideoFinder;

class ShowVideoTest extends TestCase
{
    private const OPTIONS = "title=My%20Video&description=This%20is%20a%20nice%20one%20%26%20it%27s%20short";

    /** @var ShowVideo */
    private $sut;

    /** @var VideoFinder&MockObject */
    private $videoFinder;

    public function setUp(): void
    {
        $this->videoFinder = $this->createStub(VideoFinder::class);
        $this->sut = new ShowVideo(
            XH_includeVar("./config/config.php", "plugin_cf")['video'],
            $this->videoFinder,
            new View("./views/", XH_includeVar("./languages/en.php", "plugin_tx")['video'])
        );
    }

    public function testRendersVideoWithPoster(): void
    {
        $this->videoFinder->method('find')->willReturn(new Video(
            [
                './userfiles/media/my_video.mp4' => "video/mp4",
                './userfiles/media/my_video.webm' => "video/webm",
            ],
            "./userfiles/media/my_video.jpg",
            null,
            1674668829
        ));
        $response = ($this->sut)(new FakeRequest(["language" => "en"]), "my_video", self::OPTIONS);
        Approvals::verifyHtml($response->output());
    }

    public function testRendersVideoWithoutPoster(): void
    {
        $this->videoFinder->method('find')->willReturn(new Video(
            [
                './userfiles/media/my_video.mp4' => "video/mp4",
                './userfiles/media/my_video.webm' => "video/webm",
            ],
            null,
            null,
            1674668829
        ));
        $response = ($this->sut)(new FakeRequest(["language" => "en"]), "my_video", self::OPTIONS);
        Approvals::verifyHtml($response->output());
    }

    public function testReportsMissingVideo(): void
    {
        $response = ($this->sut)(new FakeRequest(["language" => "en"]), "no_video", self::OPTIONS);
        Approvals::verifyHtml($response->output());
    }
}
