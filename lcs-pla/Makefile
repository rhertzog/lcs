#!/usr/bin/make -f
# Makefile for lcs-pla

all:

install:

# install lcs pla conf
	install -d $(DESTDIR)/var/lib/lcs/pla
	cp -a conf/* $(DESTDIR)/var/lib/lcs/pla/

# install doc
	install -d $(DESTDIR)/usr/share/doc/lcs/pla/html
	cp -a doc-html/* $(DESTDIR)/usr/share/doc/lcs/pla/html/

# install pla
	install -d $(DESTDIR)/usr/share/lcs/pla
	cp -a pla/* $(DESTDIR)/usr/share/lcs/pla

clean:

.PHONY: all install clean