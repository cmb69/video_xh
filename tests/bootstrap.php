<?php

require_once '../../cmsimple/functions.php';

require_once '../plib/classes/Request.php';
require_once '../plib/classes/Response.php';
require_once '../plib/classes/SystemChecker.php';
require_once '../plib/classes/Url.php';
require_once '../plib/classes/View.php';
require_once '../plib/classes/FakeRequest.php';
require_once '../plib/classes/FakeSystemChecker.php';

require_once './classes/model/Video.php';
require_once './classes/model/VideoFinder.php';
require_once './classes/Dic.php';
require_once './classes/ShowCallBuilder.php';
require_once './classes/ShowInfo.php';
require_once './classes/ShowVideo.php';

const CMSIMPLE_URL = "http://example.com/index.php";
const CMSIMPLE_XH_VERSION = "CMSimple_XH 1.8";
const VIDEO_VERSION = "2.2";
