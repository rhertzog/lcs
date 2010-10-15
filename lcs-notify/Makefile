#!/usr/bin/make -f

all:

install:
	# install lcs sbin files
	install -d $(DESTDIR)/sbin
	install -m 755 sbin/* $(DESTDIR)/sbin
	install -d $(DESTDIR)/var/lib/lcs
	install -d $(DESTDIR)/var/lib/lcs/lcs-notify
	install -m 755 var/lib/lcs/lcs-notify/* $(DESTDIR)/var/lib/lcs/lcs-notify
	
	# install doc
	#install -d $(DESTDIR)/usr/share/doc/lcs/notify/html
	#install -m 644 doc/html/* $(DESTDIR)/usr/share/doc/lcs/notify/html/



clean:


.PHONY: all install clean
