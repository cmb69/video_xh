.PHONY: build
build:
	svn export -q . video/
	rm video/video.komodoproject
	sed -i "s/%VIDEO_VERSION%/${VERSION}/g" video/index.php video/version.nfo

.PHONY: sniff
sniff:
	phpcs.bat *.php

.PHONY: compat
compat:
	pci.bat -d .

.PHONY: doc
doc:
	phpdoc.bat -f index.php,admin.php -t doc/ -dc CMSimple_XH -dn Video_XH
