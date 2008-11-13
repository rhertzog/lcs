#!/usr/bin/make -f
# Makefile for lcs-monlcs

all:

install:

# install lcs monlcs conf
	install -d $(DESTDIR)/var/lib/lcs/monlcs
	cp -a conf/* $(DESTDIR)/var/lib/lcs/monlcs/

# install doc-html
	install -d $(DESTDIR)/usr/share/doc/lcs/monlcs/html
	install -m 755 doc-html/* $(DESTDIR)/usr/share/doc/lcs/monlcs/html/

# install lcs monlcs www
	install -d $(DESTDIR)/var/www
	cp -a www/* $(DESTDIR)/var/www/

clean:


.PHONY: all install clean

