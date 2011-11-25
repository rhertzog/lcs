#!/usr/bin/make -f
# Makefile for lcs-lightsquid

all:

install:

# install conf files
	install -d $(DESTDIR)/etc
	install -d $(DESTDIR)/etc/apache2
	install -d $(DESTDIR)/etc/apache2/lcs-main
	cp -a conf/* $(DESTDIR)/etc/

# install lcs lightsquid cron.hourly
	install -d $(DESTDIR)/etc/cron.hourly
	install -m 0755 cron.hourly/* $(DESTDIR)/etc/cron.hourly/

# install lightsquid 
	install -d $(DESTDIR)/usr/share/lcs/lightsquid
	cp -a lightsquid/.htaccess $(DESTDIR)/usr/share/lcs/lightsquid/
	cp -a lightsquid/* $(DESTDIR)/usr/share/lcs/lightsquid/

# install lightsquid menu
	install -d $(DESTDIR)/var/www/lcs/includes/mnu.d
	cp -a mnu.d/* $(DESTDIR)/var/www/lcs/includes/mnu.d/

# install doc
	install -d $(DESTDIR)/usr/share/doc/lcs/lightsquid/html
	install -m 0644 doc/html/* $(DESTDIR)/usr/share/doc/lcs/lightsquid/html/
	install -d $(DESTDIR)/usr/share/doc/lcs/lightsquid/txt
	install -m 0644 doc/txt/* $(DESTDIR)/usr/share/doc/lcs/lightsquid/txt/

clean:

.PHONY: all install clean

