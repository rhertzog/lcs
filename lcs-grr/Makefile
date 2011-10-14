#!/usr/bin/make -f
# Makefile for lcs-Grr

all:

install:


# install doc
	install -d $(DESTDIR)/usr/share/doc/lcs/grr/html
	cp -a doc-html/* $(DESTDIR)/usr/share/doc/lcs/grr/html/

# install Grr
	install -d $(DESTDIR)/usr/share/lcs/Plugins/Grr
	cp -a Grr/* $(DESTDIR)/usr/share/lcs/Plugins/Grr

# install Grr conf
	install -d $(DESTDIR)/var/lib/lcs/Grr
	cp -a conf/* $(DESTDIR)/var/lib/lcs/Grr/	

clean:

.PHONY: all install clean
