#!/usr/bin/make -f

all:

install:
	# install conf files
	install -d $(DESTDIR)/etc
	install -d $(DESTDIR)/etc/fail2ban
	install -d $(DESTDIR)/etc/fail2ban/filter.d
	install -d $(DESTDIR)/etc/cron.weekly
	cp -a conf/* $(DESTDIR)/etc/
	chmod 755  $(DESTDIR)/etc/cron.weekly/lcs-save-sql
        chmod 755  $(DESTDIR)/etc/cron.weekly/lcs-save-ldap
	# install sbin
	install -d $(DESTDIR)/usr/sbin
	install -m 755 sbin/* $(DESTDIR)/usr/sbin/

clean:


.PHONY: all install clean
