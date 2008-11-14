#!/usr/bin/make -f
# Makefile for lcs-squid

all:

install:

# install lcs squid conf
	install -d $(DESTDIR)/var/lib/lcs/squid
	cp -a conf/* $(DESTDIR)/var/lib/lcs/squid/
	install -d $(DESTDIR)/etc/default
	install -m 750 -o root -g root conf/default/iptables $(DESTDIR)/etc/default/
	install -d $(DESTDIR)/etc/init.d/
	install -m 750 -o root -g root conf/init.d/iptables $(DESTDIR)/etc/init.d/

# install scripts squid
	install -d $(DESTDIR)/usr/share/lcs/scripts
	install -m 750 -o root -g root scripts/* $(DESTDIR)/usr/share/lcs/scripts/

# install doc-html squid
	install -d $(DESTDIR)/usr/share/doc/lcs/squid
	cp -a doc-html/* $(DESTDIR)/usr/share/doc/lcs/squid/


clean:


.PHONY: all install clean

