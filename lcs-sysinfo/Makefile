#!/usr/bin/make -f
# Makefile for lcs-sysinfo

all:

install:

# install phpsysinfo
	install -d $(DESTDIR)/usr/share/phpsysinfo
	cp -R phpsysinfo/* $(DESTDIR)/usr/share/phpsysinfo
	chmod -R 755 $(DESTDIR)/usr/share/phpsysinfo	

# install doc
	install -d $(DESTDIR)/usr/share/doc/lcs/sysinfo/html
	cp -a doc-html/* $(DESTDIR)/usr/share/doc/lcs/sysinfo/html/

# install sbin
	install -d $(DESTDIR)/usr/sbin
	install -m 755 sbin/* $(DESTDIR)/usr/sbin/

clean:

.PHONY: all install clean