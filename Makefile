PHPDOC=phpdoc.bat
JSDOC=/usr/local/jsdoc-3.1.1/jsdoc

PHPSOURCES=index.php admin.php
JSSOURCES=autosize.js admin.js
RICFILES=README CHANGELOG

EMPTY=
SPACE=$(EMPTY) $(EMPTY)
COMMA=,

.PHONY: build
build:
	svn export -q . video/
	rm video/video.komodoproject
	sed -i "s/%VIDEO_VERSION%/${VERSION}/g" video/index.php video/version.nfo

.PHONY: sniff
sniff:
	phpcs.bat $(PHPSOURCES)

.PHONY: compat
compat:
	pci.bat -d .

.PHONY: doc
doc: doc/php/index.html doc/js/index.html

doc/php/index.html: $(PHPSOURCES) $(RICFILES)
	$(PHPDOC) -f $(subst $(SPACE),$(COMMA),$(PHPSOURCES) $(RICFILES)) -t doc/php/ -dc CMSimple_XH -dn Video_XH


doc/js/index.html: $(JSSOURCES)
	$(JSDOC) -d doc/js/ $(JSSOURCES)
