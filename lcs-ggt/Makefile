#!/usr/bin/make -f
# Makefile for lcs-ggt

all:

install:


# install doc
	install -d $(DESTDIR)/usr/share/doc/lcs/ggt/html
	cp -a doc-html/* $(DESTDIR)/usr/share/doc/lcs/ggt/html/

# install 
	install -d $(DESTDIR)/usr/share/lcs/Plugins/Ggt
	cp -a Ggt/* $(DESTDIR)/usr/share/lcs/Plugins/Ggt

# install  conf
	install -d $(DESTDIR)/var/lib/lcs/Ggt
	cp -a conf/* $(DESTDIR)/var/lib/lcs/Ggt/	

clean:

.PHONY: all install clean
