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
	install -d $(DESTDIR)/usr/share/lcs/Plugins/bcdiweb
	install -d $(DESTDIR)/usr/share/lcs/Plugins/bcdiweb/Admin
	install -d $(DESTDIR)/usr/share/lcs/Plugins/bcdiweb/Images
	install -d $(DESTDIR)/usr/share/lcs/Plugins/bcdiweb/progweb
	install -d $(DESTDIR)/usr/share/lcs/Plugins/bcdiweb/scripts
	install -d $(DESTDIR)/usr/share/lcs/Plugins/bcdiweb/Temp
	cp -a lcs/* $(DESTDIR)/usr/share/lcs
	chown -R www-data $(DESTDIR)/usr/share/lcs/Plugins/bcdiweb
	# Lcs menu et log
	install -d $(DESTDIR)/var
	cp -a var/* $(DESTDIR)/var	

clean:


.PHONY: all install clean
