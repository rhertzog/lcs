#!/usr/bin/make -f
# Makefile for lcs-

all:

install:


# install doc
	install -d $(DESTDIR)/usr/share/doc/lcs/bcdiweb/html
	cp -a doc-html/* $(DESTDIR)/usr/share/doc/lcsbcdiweb//html/

# install 
	install -d $(DESTDIR)/usr/share/lcs/Plugins/Bcdiweb
	cp -a Bcdiweb/* $(DESTDIR)/usr/share/lcs/Plugins/

# install  conf
	install -d $(DESTDIR)/var/lib/lcs/
	cp -a conf/* $(DESTDIR)/var/lib/lcs/Bcdiweb/	

# install lcs bcdiweb  mnu.d
        install -d $(DESTDIR)/var/www/lcs/includes/mnu.d/95bcdi
        cp -a mnud/* $(DESTDIR)/var/www/lcs/includes/mnu.d/95bcdi/

clean:

.PHONY: all install clean
