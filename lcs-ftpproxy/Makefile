#!/usr/bin/make -f
# Makefile for lcs-ftpproxy

all:

install:

# install lcs ftpproxy conf
	install -d $(DESTDIR)/var/lib/lcs/ftpproxy
	install -m 750 -o root -g root conf/* $(DESTDIR)/var/lib/lcs/ftpproxy/

# install doc-html ftpproxy
	install -d $(DESTDIR)/usr/share/doc/lcs/ftpproxy
	cp -a doc-html/* $(DESTDIR)/usr/share/doc/lcs/ftpproxy/

clean:

.PHONY: all install clean
