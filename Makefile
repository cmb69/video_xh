.PHONY: build
build:
	svn export -q . video/
	sed -i "s/%VIDEO_VERSION%/${VERSION}/g" video/index.php video/version.nfo