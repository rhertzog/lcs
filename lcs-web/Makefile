#!/usr/bin/make -f
# Makefile for lcs-web
# A TERMINER 01/06/08 jLCF <(-_°)/> !!!!
all:

install:

# install lcs web conf
	install -d $(DESTDIR)/var/lib/lcs/web
	install -m 0750 conf/* $(DESTDIR)/var/lib/lcs/web

# install lcs web cron
	install -d $(DESTDIR)/etc/cron.d
	install -m 644 cron/* $(DESTDIR)/etc/cron.d

# install doc
	install -d $(DESTDIR)/usr/share/doc/lcs
	install -m 0750 doc/* $(DESTDIR)/usr/share/doc/lcs

# install lcs web etc
	install -d $(DESTDIR)/etc
	install -m 0750 etc/* $(DESTDIR)/etc

# install lcs web lcs 
	install -d $(DESTDIR)/usr/share/lcs
	install -m 0750 lcs/* $(DESTDIR)/usr/share/lcs

# install lcs web lib
	install -d $(DESTDIR)/usr/lib
	install -m 0750 lib/* $(DESTDIR)/usr/lib

# install sbin
	install -d $(DESTDIR)/usr/sbin
	install -m 0750 sbin/* $(DESTDIR)/usr/sbin

# install lcs web www
	install -d $(DESTDIR)/var/www
	install -m 0750 www/* $(DESTDIR)/var/www


clean:


.PHONY: all install clean

