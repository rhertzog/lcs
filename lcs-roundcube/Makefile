#!/usr/bin/make -f
# Makefile for lcs-roundcube

all:

install:

# install lcs roundcube conf
	install -d $(DESTDIR)/var/lib/lcs/roundcube
	cp -a conf/* $(DESTDIR)/var/lib/lcs/roundcube/

# install doc
	install -d $(DESTDIR)/usr/share/doc/lcs/roundcube/html
	cp -a doc-html/* $(DESTDIR)/usr/share/doc/lcs/roundcube/html/

# install roundcube
	install -d $(DESTDIR)/usr/share/lcs/roundcube
	cp -a roundcube/* $(DESTDIR)/usr/share/lcs/roundcube/

# install etc conf 
	install -d $(DESTDIR)/etc/apache2/lcs-main
	install -m 644 etc/apache2/lcs-main/* $(DESTDIR)/etc/apache2/lcs-main

# install sbin
	install -d $(DESTDIR)/usr/sbin
	install -m 0700 -o root -g root sbin/* $(DESTDIR)/usr/sbin/	

clean:

.PHONY: all install clean