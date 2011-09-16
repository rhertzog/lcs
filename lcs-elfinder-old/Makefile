#!/usr/bin/make -f
# Makefile for lcs-elfinder

all:

install:

# install lcs elfinder conf
	install -d $(DESTDIR)/var/lib/lcs/elfinder
	cp -a conf/* $(DESTDIR)/var/lib/lcs/elfinder/

# install doc
	install -d $(DESTDIR)/usr/share/doc/lcs/elfinder/html
	cp -a doc-html/* $(DESTDIR)/usr/share/doc/lcs/elfinder/html/

# install elfinder
	install -d $(DESTDIR)/usr/share/lcs/elfinder
	cp -a elfinder/* $(DESTDIR)/usr/share/lcs/elfinder/
clean:

.PHONY: all install clean