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


clean:


.PHONY: all install clean
