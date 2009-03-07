#!/usr/bin/make -f

all:

install:
	# install conf files
	install -d $(DESTDIR)/etc
	install -d $(DESTDIR)/etc/apache2
	install -d $(DESTDIR)/etc/apache2/lcs-main
	install -d $(DESTDIR)/etc/cron.d
	cp -a conf/* $(DESTDIR)/etc
	# install web
	install -d $(DESTDIR)/usr/share/lcs/bcdiweb
	install -d $(DESTDIR)/usr/share/lcs/bcdiweb/Admin
	install -d $(DESTDIR)/usr/share/lcs/bcdiweb/Images
	install -d $(DESTDIR)/usr/share/lcs/bcdiweb/progweb
	install -d $(DESTDIR)/usr/share/lcs/bcdiweb/scripts
	install -d $(DESTDIR)/usr/share/lcs/bcdiweb/Temp
	cp -a lcs/* $(DESTDIR)/usr/share/lcs
	chown -R www-data $(DESTDIR)/usr/share/lcs/bcdiweb
	# Lcs menu et log
	install -d $(DESTDIR)/var
	cp -a var/* $(DESTDIR)/var	

clean:


.PHONY: all install clean
