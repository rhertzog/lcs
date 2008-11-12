#!/usr/bin/make -f
# Makefile for lcs-spip

all:

install:

# install lcs spip conf
	install -d $(DESTDIR)/var/lib/lcs/spip
	cp -a conf/* $(DESTDIR)/var/lib/lcs/spip/

# install doc
	install -d $(DESTDIR)/usr/share/doc/lcs/spip/html
	cp -a doc/* $(DESTDIR)/usr/share/doc/lcs/spip/html/

# install spip
	install -d $(DESTDIR)/usr/share/lcs/spip
	cp -a spip/* $(DESTDIR)/usr/share/lcs/spip

clean:

.PHONY: all install clean