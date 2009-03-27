#!/usr/bin/make -f
# Makefile for lcs-Cdt

all:

install:


# install doc
	install -d $(DESTDIR)/usr/share/doc/lcs/Cdt/html
	cp -a doc-html/* $(DESTDIR)/usr/share/doc/lcs/Cdt/html/

# install Cdt
	install -d $(DESTDIR)/usr/share/lcs/Plugins/Cdt
	cp -a Cdt/* $(DESTDIR)/usr/share/lcs/Plugins/Cdt

# install Cdt conf
	install -d $(DESTDIR)/var/lib/lcs/Cdt
	cp -a conf/* $(DESTDIR)/var/lib/lcs/Cdt/	

clean:

.PHONY: all install clean
