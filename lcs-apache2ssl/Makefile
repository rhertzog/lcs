#!/usr/bin/make -f
# Makefile for lcs-apache2ssl

all:

install:

# install apache2 conf
	mkdir -p $(DESTDIR)/etc/apache2
	cp -R apache/* $(DESTDIR)/etc/apache2/

# install doc
	install -d $(DESTDIR)/usr/share/doc/lcs/apache2ssl/html
	install -m 644 doc-html/* $(DESTDIR)/usr/share/doc/lcs/apache2ssl/html/

clean:

.PHONY: all install clean

