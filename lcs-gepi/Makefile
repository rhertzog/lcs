#!/usr/bin/make -f
# Makefile for lcs-Gepi

all:

install:


# install doc
	install -d $(DESTDIR)/usr/share/doc/lcs/Gepi/html
	cp -a doc-html/* $(DESTDIR)/usr/share/doc/lcs/Gepi/html/

# install Gepi
	install -d $(DESTDIR)/usr/share/lcs/Plugins/Gepi
	cp -a Gepi/* $(DESTDIR)/usr/share/lcs/Plugins/Gepi

# install Gepi conf
	install -d $(DESTDIR)/var/lib/lcs/Gepi
	cp -a conf/* $(DESTDIR)/var/lib/lcs/Gepi/	

clean:

.PHONY: all install clean
