#!/usr/bin/make -f
# Makefile for lcs-web

all:

install:

# install lcs conf
	install -d $(DESTDIR)/var/lib/lcs/dns
	install -m 0660 conf/* $(DESTDIR)/var/lib/lcs/dns

# install sbin
	install -d $(DESTDIR)/usr/sbin
	install -m 0750 sbin/* $(DESTDIR)/usr/sbin

# install doc
	install -d $(DESTDIR)/usr/share/doc/lcs/dns/html
	install -m 0644 doc/html/* $(DESTDIR)/usr/share/doc/lcs/dns/html



clean:


.PHONY: all install clean

