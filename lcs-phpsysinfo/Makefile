#!/usr/bin/make -f
# Makefile for lcs-apache2ssl

all:

install:

# install lcs phpsysinfo conf
	install -d $(DESTDIR)/var/lib/lcs/phpsysinfo
	install -m 755 conf/* $(DESTDIR)/var/lib/lcs/phpsysinfo/
        chown -R www-data:www-data $(DESTDIR)/var/lib/lcs/phpsysinfo/
# install doc
	install -d $(DESTDIR)/usr/share/doc/lcs/phpsysinfo/html
	install -m 644 doc-html/* $(DESTDIR)/usr/share/doc/lcs/phpsysinfo/html/

# install sbin
	install -d $(DESTDIR)/usr/sbin
	install -m 755 sbin/* $(DESTDIR)/usr/sbin/

clean:


.PHONY: all install clean

