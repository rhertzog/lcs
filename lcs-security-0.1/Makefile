#!/usr/bin/make -f

all:

install:
	# install conf files
	install -d $(DESTDIR)/etc
	install -d $(DESTDIR)/etc/fail2ban
	install -d $(DESTDIR)/etc/fail2ban/filter.d
	cp -a conf/* $(DESTDIR)/etc/

	# install sbin
	install -d $(DESTDIR)/usr/sbin
	install -m 755 sbin/* $(DESTDIR)/usr/sbin/

clean:


.PHONY: all install clean
