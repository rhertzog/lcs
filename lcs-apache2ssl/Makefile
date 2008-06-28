#!/usr/bin/make -f
# Makefile for lcs-apache2ssl

all:

install:

# install lcs web conf
	install -d $(DESTDIR)/var/lib/lcs/apache2ssl
	cp -a conf/* $(DESTDIR)/var/lib/lcs/apache2ssl/

# install apache2 conf
	mkdir -p $(DESTDIR)/etc/apache2
	cp -R apache/* $(DESTDIR)/etc/apache2/

# install etc lcs 
	install -d $(DESTDIR)/etc/lcs
	cp -a lcs/* $(DESTDIR)/etc/lcs
# install doc
	install -d $(DESTDIR)/usr/share/doc/lcs/apache2ss/html
	install -m 644 doc-html/* $(DESTDIR)/usr/share/doc/lcs/apache2ss/html/

# install main conf script
	install -d $(DESTDIR)/usr/share/lcs/scripts
	install -m 755 scripts/* $(DESTDIR)/usr/share/lcs/scripts/

# install lcs apache2ssl www
	install -d $(DESTDIR)/var/www
	cp -a www/* $(DESTDIR)/var/www/

clean:


.PHONY: all install clean

