#!/usr/bin/make -f

all:

install:
	# install conf files
	install -d $(DESTDIR)/etc/lcs/courier
	install -m 644 courier/* $(DESTDIR)/etc/lcs/courier/
	install -d $(DESTDIR)/etc/lcs/pam.d
	install -m 644 pam.d/* $(DESTDIR)/etc/lcs/pam.d/
	
	# install main conf script
	install -d $(DESTDIR)/usr/share/lcs/scripts
	install -m 755 scripts/* $(DESTDIR)/usr/share/lcs/scripts/
	
	# install doc
	install -d $(DESTDIR)/usr/share/doc/lcs/imap/html
	install -m 644 doc-html/* $(DESTDIR)/usr/share/doc/lcs/imap/html/
	
	# install sbin
	install -d $(DESTDIR)/usr/sbin
	install -m 755 sbin/* $(DESTDIR)/usr/sbin/

	# install lcs imap www
	install -d $(DESTDIR)/var/www
	cp -a www/* $(DESTDIR)/var/www/
clean:


.PHONY: all install clean

