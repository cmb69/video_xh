.PHONY: build
build:
	svn export -q . video/
	rm video/video.komodoproject
	sed -i "s/%VIDEO_VERSION%/${VERSION}/g" video/index.php video/version.nfo

.PHONY: sniff
sniff:
	phpcs.bat *.php
