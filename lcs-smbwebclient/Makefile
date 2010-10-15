#!/usr/bin/make -f
# Makefile for lcs-smbwebclient

all:

install:

# install lcs smbwebclient conf
	install -d $(DESTDIR)/var/lib/lcs/smbwebclient
	cp -a conf/* $(DESTDIR)/var/lib/lcs/smbwebclient/

# install doc
	install -d $(DESTDIR)/usr/share/doc/lcs/smbwebclient/html
	cp -a doc-html/* $(DESTDIR)/usr/share/doc/lcs/smbwebclient/html/

# install smbwebclient
	install -d $(DESTDIR)/usr/share/lcs/smbwebclient
	cp -a smbwebclient/* $(DESTDIR)/usr/share/lcs/smbwebclient

clean:

.PHONY: all install clean