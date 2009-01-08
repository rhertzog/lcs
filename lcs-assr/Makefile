#!/usr/bin/make -f

all:

install:
	# install conf files
	install -d $(DESTDIR)/etc
	cp -a conf/* $(DESTDIR)/etc/

	# install sbin
	install -d $(DESTDIR)/usr/share/lcs/sbin
	install -m 755 sbin/* $(DESTDIR)/usr/share/lcs/sbin/

clean:


.PHONY: all install clean
