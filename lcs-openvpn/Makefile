#!/usr/bin/make -f

all:

install:
	# install conf files
	install -d $(DESTDIR)/etc
	install -d $(DESTDIR)/etc/apache2
	install -d $(DESTDIR)/etc/apache2/lcs-main
	install -d $(DESTDIR)/etc/lcs
	install -d $(DESTDIR)/etc/lcs/openvpn
	install -d $(DESTDIR)/etc/lcs/sudo.d
	cp -a conf/* $(DESTDIR)/etc/

	# install web-gui
	install -d $(DESTDIR)/usr/share/lcs/openvpn
	install -d $(DESTDIR)/usr/share/lcs/openvpn/configs
	install -d $(DESTDIR)/usr/share/lcs/openvpn/downloads
	install -d $(DESTDIR)/usr/share/lcs/openvpn/img
	install -d $(DESTDIR)/usr/share/lcs/openvpn/include
	install -d $(DESTDIR)/usr/share/lcs/openvpn/keys
	install -d $(DESTDIR)/usr/share/lcs/openvpn/plugins
	install -d $(DESTDIR)/usr/share/lcs/openvpn/lcs-conf
	install -d $(DESTDIR)/usr/share/lcs/openvpn/templates
	install -d $(DESTDIR)/usr/share/lcs/openvpn/templates_c
	cp -a lcs/* $(DESTDIR)/usr/share/lcs
	# install doc
	install -d $(DESTDIR)/usr/share/doc/lcs/openvpn/html
	install -m 644 doc-html/* $(DESTDIR)/usr/share/doc/lcs/openvpn/html/
	# Lcs menu et log
	install -d $(DESTDIR)/var
	cp -a var/* $(DESTDIR)/var	
	# install sbin
	install -d $(DESTDIR)/usr/share/lcs/sbin
	install -m 755 sbin/* $(DESTDIR)/usr/share/lcs/sbin/

clean:


.PHONY: all install clean
