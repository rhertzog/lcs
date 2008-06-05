#!/usr/bin/make -f
# Makefile for lcs-web

all:

install:

# install lcs web conf
	install -d $(DESTDIR)/var/lib/lcs/web
	cp -a conf/* $(DESTDIR)/var/lib/lcs/web/

# install lcs web cron
	install -d $(DESTDIR)/etc/cron.d
	install -m 644 cron/* $(DESTDIR)/etc/cron.d/

# install doc
	install -d $(DESTDIR)/usr/share/doc/lcs
	cp -a doc/* $(DESTDIR)/usr/share/doc/lcs/

# install lcs web etc
	install -d $(DESTDIR)/etc
	cp -a etc/* $(DESTDIR)/etc/

# install lcs web lcs 
	install -d $(DESTDIR)/usr/share/lcs
	cp -a lcs/* $(DESTDIR)/usr/share/lcs/

# install lcs web lib
	install -d $(DESTDIR)/usr/lib/cgi-bin $(DESTDIR)/usr/share/perl5
	install -m 755 lib/cgi-bin/gep2.cgi $(DESTDIR)/usr/lib/cgi-bin/
	install -m 644 lib/perl5/LcSe.pm $(DESTDIR)/usr/share/perl5/

# install sbin
	install -d $(DESTDIR)/usr/sbin
	install -m 0755 sbin/* $(DESTDIR)/usr/sbin/

# install lcs web skel
	install -d $(DESTDIR)/etc/skel
	cp -a skel/* $(DESTDIR)/etc/skel/

# install lcs web www
	install -d $(DESTDIR)/var/www
	cp -a www/* $(DESTDIR)/var/www/


clean:


.PHONY: all install clean

