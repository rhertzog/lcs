#!/usr/bin/make -f

all:

install:
	# install conf files
	install -d $(DESTDIR)/etc
	install -d $(DESTDIR)/etc/spamassassin
	cp -a conf/* $(DESTDIR)/etc/

	# install lcs conf files
	install -d $(DESTDIR)/usr/share/lcs/sbin
	install -m 755 lcs/sbin/* $(DESTDIR)/usr/share/lcs/sbin
	# install doc
	install -d $(DESTDIR)/usr/share/doc/lcs/anti-spam/html
	install -m 644 doc/html/* $(DESTDIR)/usr/share/doc/lcs/anti-spam/html/



clean:


.PHONY: all install clean
