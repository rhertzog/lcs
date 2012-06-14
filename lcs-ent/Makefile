#!/usr/bin/make -f
# Makefile for lcs-ent

all:

install:

# install lcs ent conf
	install -d $(DESTDIR)/var/lib/lcs/ent
	cp -a conf/* $(DESTDIR)/var/lib/lcs/ent/
# install sbin
	install -d $(DESTDIR)/usr/sbin
	install -m 0750 sbin/* $(DESTDIR)/usr/sbin
# install lcs-ent doc
	install -d $(DESTDIR)/usr/share/doc/lcs/ent/html
	cp -a doc-html/* $(DESTDIR)/usr/share/doc/lcs/ent/html/
# install www 70ent menu
	install -d $(DESTDIR)/var/www/lcs/includes/mnu.d/
	cp -a www/* $(DESTDIR)/var/www/lcs/includes/mnu.d/
# install lcs ent
	install -d $(DESTDIR)/usr/share/lcs
	cp -a lcs/* $(DESTDIR)/usr/share/lcs/
# install etc conf 
	install -d $(DESTDIR)/etc/apache2/lcs-main
	install -m 644 etc/apache2/lcs-main/* $(DESTDIR)/etc/apache2/lcs-main
clean:


.PHONY: all install clean

