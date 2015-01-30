#!/usr/bin/make -f
# Makefile for lcs-spip

all:

install:

# install lcs spip cron.daily task update security screen
	install -d $(DESTDIR)/etc/cron.daily
	install -m 755 cron.daily/* $(DESTDIR)/etc/cron.daily/
	
# install lcs spip cron.weekly task clean spip tmp
	install -d $(DESTDIR)/etc/cron.weekly
	install -m 755 cron.weekly/* $(DESTDIR)/etc/cron.weekly/

# install lcs spip conf
	install -d $(DESTDIR)/var/lib/lcs/spip
	cp -a conf/* $(DESTDIR)/var/lib/lcs/spip/

# install doc
	install -d $(DESTDIR)/usr/share/doc/lcs/spip/html
	cp -a doc-html/* $(DESTDIR)/usr/share/doc/lcs/spip/html/

# install spip
	install -d $(DESTDIR)/usr/share/lcs/spip
	cp -a spip/* $(DESTDIR)/usr/share/lcs/spip

clean:

.PHONY: all install clean