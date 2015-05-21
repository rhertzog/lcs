#!/usr/bin/make -f
# Makefile for lcs-cdt

all:

install:


# install doc
	install -d $(DESTDIR)/usr/share/doc/lcs/cdt/html
	cp -a doc-html/* $(DESTDIR)/usr/share/doc/lcs/cdt/html/

# install Cdt
	install -d $(DESTDIR)/usr/share/lcs/Plugins/Cdt
	cp -a Cdt/* $(DESTDIR)/usr/share/lcs/Plugins/Cdt/

# install Cdt conf
	install -d $(DESTDIR)/var/lib/lcs/Cdt
	cp -a conf/* $(DESTDIR)/var/lib/lcs/Cdt/	

# install Cdt sudo
	install -d $(DESTDIR)/etc/lcs/sudo.d
	cp -a etc/* $(DESTDIR)/etc/lcs/sudo.d/

# install Cdt script
	install -d $(DESTDIR)/usr/share/lcs/scripts
	cp -a lcs/* $(DESTDIR)/usr/share/lcs/scripts/

clean:

.PHONY: all install clean
