#!/usr/bin/make -f

all:

install:
	# install conf files
	install -d $(DESTDIR)/etc/lcs/squirrelmail
	install -m 644 conf/* $(DESTDIR)/etc/lcs/squirrelmail/
	
	# install main conf script
	install -d $(DESTDIR)/usr/share/lcs/scripts
	install -m 755 scripts/* $(DESTDIR)/usr/share/lcs/scripts/
	
	# install doc
	install -d $(DESTDIR)/usr/share/doc/lcs/squirrelmail/html
	install -m 644 doc-html/* $(DESTDIR)/usr/share/doc/lcs/squirrelmail/html/
	
	# install sbin
	install -d $(DESTDIR)/usr/sbin
	install -m 755 sbin/* $(DESTDIR)/usr/sbin/

clean:


.PHONY: all install clean

