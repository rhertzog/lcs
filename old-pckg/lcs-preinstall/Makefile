#!/usr/bin/make -f
# Makefile for lcs-preinstall

all:

install:

# install lcs conf
	install -d $(DESTDIR)/var/lib/lcs/preinstall
	cp -a conf/* $(DESTDIR)/var/lib/lcs/preinstall

# install lcs etc conf default
	install -d $(DESTDIR)/etc/default
	install -m 644 etc/default/* $(DESTDIR)/etc/default/

# install lcs etc conf init.d
	install -d $(DESTDIR)/etc/init.d
	install -m 755 etc/init.d/* $(DESTDIR)/etc/init.d/

# install lcs etc conf sudo.d
	install -d $(DESTDIR)/etc/lcs
	install -d $(DESTDIR)/etc/lcs/sudo.d
	
# install sbin
	install -d $(DESTDIR)/usr/sbin
	install -m 0750 sbin/* $(DESTDIR)/usr/sbin

# install doc
	install -d $(DESTDIR)/usr/share/doc/lcs/preinstall/html
	install -m 0644 doc/html/* $(DESTDIR)/usr/share/doc/lcs/preinstall/html

clean:


.PHONY: all install clean

