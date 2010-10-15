#!/usr/bin/make -f
# Makefile for lcs-sacoche

all:

install:


# install doc
	install -d $(DESTDIR)/usr/share/doc/lcs/sacoche/html
	cp -a doc-html/* $(DESTDIR)/usr/share/doc/lcs/sacoche/html/

# install Sacoche
	install -d $(DESTDIR)/usr/share/lcs/Plugins/Sacoche
	cp -a Sacoche/* $(DESTDIR)/usr/share/lcs/Plugins/Sacoche

# install Sacoche conf
	install -d $(DESTDIR)/var/lib/lcs/Sacoche
	cp -a conf/* $(DESTDIR)/var/lib/lcs/Sacoche/	

clean:

.PHONY: all install clean
