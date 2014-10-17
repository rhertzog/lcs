#!/usr/bin/make -f
# Makefile for lcs-certmanager

all:

install:

# install lcs certmanager conf
	install -d $(DESTDIR)/var/lib/lcs/certmanager
	cp -a conf/* $(DESTDIR)/var/lib/lcs/certmanager
		
# install lcs certmanager etc
	install -d $(DESTDIR)/etc
	cp -a etc/* $(DESTDIR)/etc/

# install lcs certmanager sbin
	install -d $(DESTDIR)/usr/sbin
	install -m 0700 -o root -g root sbin/* $(DESTDIR)/usr/sbin/
	
# install lcs certmanager www
	install -d $(DESTDIR)/var/www
	cp -a www/* $(DESTDIR)/var/www/	

# install lcs certmanager certmanager	
	install -d $(DESTDIR)/usr/share/lcs/certmanager
	install -m 0770 -o root -g www-data $(DESTDIR)/usr/share/lcs/certmanager

	
# install lcs certmanager doc-html
	install -d $(DESTDIR)/usr/share/doc/lcs/certmanager/html
	cp -a doc-html/* $(DESTDIR)/usr/share/doc/lcs/certmanager/html/

clean:


.PHONY: all install clean

