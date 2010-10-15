#!/usr/bin/make -f
# Makefile for lcs-clientftp

all:

install:

# install lcs clientftp conf
	install -d $(DESTDIR)/var/lib/lcs/clientftp
	cp -a conf/* $(DESTDIR)/var/lib/lcs/clientftp/

# install doc
	install -d $(DESTDIR)/usr/share/doc/lcs/clientftp/html
	cp -a doc-html/* $(DESTDIR)/usr/share/doc/lcs/clientftp/html/

# install clientftp
	install -d $(DESTDIR)/usr/share/lcs/clientftp
	cp -a clientftp/* $(DESTDIR)/usr/share/lcs/clientftp

clean:

.PHONY: all install clean