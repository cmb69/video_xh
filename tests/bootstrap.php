<?php

require_once '../../cmsimple/functions.php';

require_once '../plib/classes/SystemChecker.php';
require_once '../plib/classes/FakeSystemChecker.php';

require_once './classes/value/Video.php';
require_once './classes/infra/Response.php';
require_once './classes/infra/Url.php';
require_once './classes/infra/VideoFinder.php';
require_once './classes/infra/View.php';
require_once './classes/logic/OptionParser.php';

require_once './classes/ShowCallBuilder.php';
require_once './classes/ShowInfo.php';
require_once './classes/ShowVideo.php';

const CMSIMPLE_URL = "http://example.com/index.php";
const CMSIMPLE_XH_VERSION = "CMSimple_XH 1.8";
const VIDEO_VERSION = "2.1-dev";
