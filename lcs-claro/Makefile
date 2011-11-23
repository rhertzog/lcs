#!/usr/bin/make -f
# Makefile for lcs-claro

all:

install:


# install doc
	install -d $(DESTDIR)/usr/share/doc/lcs/claro/html
	cp -a doc-html/* $(DESTDIR)/usr/share/doc/lcs/claro/html/

# install Claroline
	install -d $(DESTDIR)/usr/share/lcs/Plugins/Claro
	cp -a Claro/* $(DESTDIR)/usr/share/lcs/Plugins/Claro/

# install Claroline conf
	install -d $(DESTDIR)/var/lib/lcs/Claro
	cp -a conf/* $(DESTDIR)/var/lib/lcs/Claro/	

clean:

.PHONY: all install clean
