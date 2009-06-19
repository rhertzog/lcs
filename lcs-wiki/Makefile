#!/usr/bin/make -f
# Makefile for lcs-Wiki

all:

install:


# install doc
	install -d $(DESTDIR)/usr/share/doc/lcs/Wiki/html
	cp -a doc-html/* $(DESTDIR)/usr/share/doc/lcs/Wiki/html/

# install Wiki
	install -d $(DESTDIR)/usr/share/lcs/Plugins/Wiki
	cp -a Wiki/* $(DESTDIR)/usr/share/lcs/Plugins/Wiki

# install Wiki conf
	install -d $(DESTDIR)/var/lib/lcs/Wiki
	cp -a conf/* $(DESTDIR)/var/lib/lcs/Wiki/	

clean:

.PHONY: all install clean
