#!/usr/bin/make -f
# Makefile for lcs-squidguard

all:

install:

# install lcs squidguard conf sudo
	install -d $(DESTDIR)/etc/lcs/sudo.d
	cp -a etc/sudo.d/* $(DESTDIR)/etc/lcs/sudo.d/
                
# install lcs squidguard conf
	install -d $(DESTDIR)/var/lib/lcs/squidguard
	cp -a conf/* $(DESTDIR)/var/lib/lcs/squidguard/

# install lcs squidguard cron
	install -d $(DESTDIR)/etc/cron.weekly
	install -m 644 cron/* $(DESTDIR)/etc/cron.weekly/

# install lcs squidguard scripts
	install -d $(DESTDIR)/usr/share/lcs/scripts
	install -m 750 -o root -g root scripts/* $(DESTDIR)/usr/share/lcs/scripts/

# install lcs squidguard sbin
	install -d $(DESTDIR)/usr/sbin
	install -m 0700 -o root -g root sbin/* $(DESTDIR)/usr/sbin/

# install lcs squidguard doc-html
	install -d $(DESTDIR)/usr/share/doc/lcs/squidguard
	cp -a doc-html/* $(DESTDIR)/usr/share/doc/lcs/squidguard/

# install lcs squidguard lib
	install -d $(DESTDIR)/var/lib
	cp -a lib/* $(DESTDIR)/var/lib/

# install lcs squidguard www
	install -d $(DESTDIR)/var/www
	cp -a www/* $(DESTDIR)/var/www/

clean:


.PHONY: all install clean

