#!/usr/bin/make -f
# Makefile for lcs-phpsysinfo

all:

install:

# install lcs phpsysinfo conf
	install -d $(DESTDIR)/var/lib/lcs/phpsysinfo
	cp -a conf/* $(DESTDIR)/var/lib/lcs/phpsysinfo/

# install doc
	install -d $(DESTDIR)/usr/share/doc/lcs/phpsysinfo/html
	install -m 644 doc-html/* $(DESTDIR)/usr/share/doc/lcs/phpsysinfo/html/

# install sbin
	install -d $(DESTDIR)/usr/sbin
	install -m 755 sbin/* $(DESTDIR)/usr/sbin/

clean:


.PHONY: all install clean

