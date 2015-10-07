#!/usr/bin/make -f

all:

install:
	# install conf files
	install -d $(DESTDIR)/etc
	cp -a conf/* $(DESTDIR)/etc/
	chmod 644  $(DESTDIR)/etc/cron.d/lcs-save-ldap
	chmod 755  $(DESTDIR)/etc/cron.weekly/lcs-save-sql
	# install lcs scripts
	install -d $(DESTDIR)/usr/share/lcs/scripts
	install -m 755 scripts/* $(DESTDIR)/usr/share/lcs/scripts 
	# install var
	install -d $(DESTDIR)/var
	cp -a var/*  $(DESTDIR)/var/	
	# install sbin
	install -d $(DESTDIR)/usr/sbin
	install -m 755 sbin/* $(DESTDIR)/usr/sbin/
	# install home RSSH
	install -d $(DESTDIR)/usr/share/lcs-security/
	cp -a home/* $(DESTDIR)/usr/share/lcs-security/
	# install default .bashrc
	cp -a root/.bashrc $(DESTDIR)/usr/share/lcs-security/
	# install doc
	install -d $(DESTDIR)/usr/share/doc/lcs/security/html
	cp -a doc-html/* $(DESTDIR)/usr/share/doc/lcs/security/html/
	
clean:


.PHONY: all install clean
