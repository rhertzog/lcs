#!/usr/bin/make -f

all:

install:
	# install conf files
	install -d $(DESTDIR)/etc
	install -d $(DESTDIR)/etc/apache2
	install -d $(DESTDIR)/etc/apache2/lcs-main
	install -d $(DESTDIR)/etc/lcs
	#install -d $(DESTDIR)/etc/lcs/jabber
	install -d $(DESTDIR)/etc/lcs/sudo.d
	cp -a conf/* $(DESTDIR)/etc/

	# install web-gui
	install -d $(DESTDIR)/usr/share/lcs/jabber
	cp -a lcs/* $(DESTDIR)/usr/share/lcs

	# install doc
	install -d $(DESTDIR)/usr/share/doc/lcs/jabber/html
	install -m 644 doc-html/* $(DESTDIR)/usr/share/doc/lcs/jabber/html/
	# Lcs menu et log
	install -d $(DESTDIR)/var
	cp -a var/* $(DESTDIR)/var	
	# install sbin
	install -d $(DESTDIR)/usr/share/lcs/sbin
	install -m 755 sbin/* $(DESTDIR)/usr/share/lcs/sbin/

clean:


.PHONY: all install clean
