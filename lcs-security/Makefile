#!/usr/bin/make -f

all:

install:
	# install conf files
	install -d $(DESTDIR)/etc
	install -d $(DESTDIR)/etc/fail2ban
	install -d $(DESTDIR)/etc/fail2ban/filter.d
	install -d $(DESTDIR)/etc/cron.d
	install -d $(DESTDIR)/etc/cron.weekly
	install -d $(DESTDIR)/usr/share/lcs/scripts
        # copy and chmod
	cp -a conf/* $(DESTDIR)/etc/
	cp -a scripts/* $(DESTDIR)/usr/share/lcs/scripts
	chmod 644  $(DESTDIR)/etc/cron.d/lcs-save-ldap
	chmod 755  $(DESTDIR)/etc/cron.weekly/lcs-save-sql
	# install sbin
	install -d $(DESTDIR)/usr/sbin
	install -m 755 sbin/* $(DESTDIR)/usr/sbin/

clean:


.PHONY: all install clean