#!/usr/bin/make -f
# Makefile for lcs-#MODUL#

all:

install:


# install doc
	install -d $(DESTDIR)/usr/share/doc/lcs/#MODUL#/html
	cp -a doc-html/* $(DESTDIR)/usr/share/doc/lcs/#MODUL#/html/

# install #MODUL#
	install -d $(DESTDIR)/usr/share/lcs/Plugins/#MODUL#
	cp -a #MODUL#/* $(DESTDIR)/usr/share/lcs/Plugins/#MODUL#

# install #MODUL# conf
	install -d $(DESTDIR)/var/lib/lcs/#MODUL#
	cp -a conf/* $(DESTDIR)/var/lib/lcs/#MODUL#/	

clean:

.PHONY: all install clean
