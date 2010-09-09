#!/usr/bin/make -f
# Makefile for lcs-desktop

all:

install:

# install desktop
	install -d $(DESTDIR)/us/share/lcs/desktop
	cp -R desktop/* $(DESTDIR)/usr/share/lcs/desktop/
	chmod -R 755 $(DESTDIR)/usr/share/lcs/desktop	

	

# install doc
	install -d $(DESTDIR)/usr/share/doc/lcs/desktop/html
	cp -a doc-html/* $(DESTDIR)/usr/share/doc/lcs/desktop/html/

clean:

.PHONY: all install clean