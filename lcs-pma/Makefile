#!/usr/bin/make -f
# Makefile for lcs-pma

all:

install:

# install lcs pma conf
	install -d $(DESTDIR)/var/lib/lcs/pma
	cp -a conf/* $(DESTDIR)/var/lib/lcs/pma/

# install doc
	install -d $(DESTDIR)/usr/share/doc/lcs/pma/html
	cp -a doc-html/* $(DESTDIR)/usr/share/doc/lcs/pma/html/

# install pma
	install -d $(DESTDIR)/usr/share/lcs/pma
	cp -a pma/* $(DESTDIR)/usr/share/lcs/pma

clean:

.PHONY: all install clean