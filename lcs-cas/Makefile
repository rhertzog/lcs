#!/usr/bin/make -f
# Makefile for lcs-cas

all:

install:

# install lcs cas logrotate
	install -d $(DESTDIR)/etc/logrotate.d
	install -m 644 logrotate/* $(DESTDIR)/etc/logrotate.d/

# install lcs cas conf
	install -d $(DESTDIR)/var/lib/lcs/cas
	install -m 0660 conf/* $(DESTDIR)/var/lib/lcs/cas

# install lcs cas cron.weekly
	install -d $(DESTDIR)/etc/cron.weekly
	install -m 755 cron.weekly/* $(DESTDIR)/etc/cron.weekly/

# install lcs cas sbin
	install -d $(DESTDIR)/usr/sbin
	install -m 0700 -o root -g root sbin/* $(DESTDIR)/usr/sbin/

# install lcs cas doc-html
	install -d $(DESTDIR)/usr/share/doc/lcs/cas/html
	cp -a doc-html/* $(DESTDIR)/usr/share/doc/lcs/cas/html/

clean:


.PHONY: all install clean

