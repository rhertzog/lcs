#!/usr/bin/make -f
# Makefile for lcs-maintenance

all:

install:


# install doc
	install -d $(DESTDIR)/usr/share/doc/lcs/maintenance/html
	cp -a doc-html/* $(DESTDIR)/usr/share/doc/lcs/maintenance/html/

# install Maintenance
	install -d $(DESTDIR)/usr/share/lcs/Plugins/Maintenance
	cp -a Maintenance/* $(DESTDIR)/usr/share/lcs/Plugins/Maintenance

# install Maintenance conf
	install -d $(DESTDIR)/var/lib/lcs/Maintenance
	cp -a conf/* $(DESTDIR)/var/lib/lcs/Maintenance/	

clean:

.PHONY: all install clean
