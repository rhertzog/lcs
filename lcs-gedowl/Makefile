#!/usr/bin/make -f
# Makefile for lcs-Gedowl

all:

install:


# install doc
	install -d $(DESTDIR)/usr/share/doc/lcs/Gedowl/html
	cp -a doc-html/* $(DESTDIR)/usr/share/doc/lcs/Gedowl/html/

# install Gedowl
	install -d $(DESTDIR)/usr/share/lcs/Plugins/Gedowl
	cp -a Gedowl/* $(DESTDIR)/usr/share/lcs/Plugins/Gedowl

# install Gedowl conf
	install -d $(DESTDIR)/var/lib/lcs/Gedowl
	cp -a conf/* $(DESTDIR)/var/lib/lcs/Gedowl/	

# install Gedowl sudo
	install -d $(DESTDIR)/etc/lcs/sudo.d
	cp -a etc/* $(DESTDIR)/etc/lcs/sudo.d/

# install Gedowl script
	install -d $(DESTDIR)/usr/share/lcs/scripts
	cp -a lcs/* $(DESTDIR)/usr/share/lcs/scripts/

clean:

.PHONY: all install clean
