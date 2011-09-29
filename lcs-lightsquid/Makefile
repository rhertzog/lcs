#!/usr/bin/make -f
# Makefile for lcs-lightsquid

all:

install:

# install conf files
	install -d $(DESTDIR)/etc
	install -d $(DESTDIR)/etc/apache2
	install -d $(DESTDIR)/etc/apache2/lcs-main
	cp -a conf/* $(DESTDIR)/etc/

# install lcs lightsquid cron.daily
	install -d $(DESTDIR)/etc/cron.daily
	install -m 0644 cron.daily/* $(DESTDIR)/etc/cron.daily/

# install lightsquid 
	install -d $(DESTDIR)/usr/share/share/lcs/lightsquid
	install -m 0644 lightsquid $(DESTDIR)/usr/share/doc/lcs/lightsquid/html

# install doc
	install -d $(DESTDIR)/usr/share/doc/lcs/lightsquid/html
	install -m 0644 doc/html/* $(DESTDIR)/usr/share/doc/lcs/lightsquid/html

clean:


.PHONY: all install clean

