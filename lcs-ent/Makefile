#!/usr/bin/make -f
# Makefile for lcs-ent

all:

install:

# install lcs ent conf
	install -d $(DESTDIR)/var/lib/lcs/ent
	cp -a conf/* $(DESTDIR)/var/lib/lcs/ent/

# install lcs-ent doc
	install -d $(DESTDIR)/usr/share/doc/lcs/ent/html
	cp -a doc-html/* $(DESTDIR)/usr/share/doc/lcs/ent/html/

clean:


.PHONY: all install clean

