#!/usr/bin/make -f
# Makefile for lcs-casmrt

all:

install:

# install doc
	install -d $(DESTDIR)/usr/share/doc/lcs/casmrt/html
	cp -a doc-html/* $(DESTDIR)/usr/share/doc/lcs/casmrt/html/

# install casmrt
	install -d $(DESTDIR)/usr/share/lcs/casmrt
	cp -a casmrt/* $(DESTDIR)/usr/share/lcs/casmrt/

clean:

.PHONY: all install clean
