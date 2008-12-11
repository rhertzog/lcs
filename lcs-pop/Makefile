#!/usr/bin/make -f

all:

install:
	# install main conf script
	install -d $(DESTDIR)/usr/share/lcs/scripts
	install -m 755 scripts/* $(DESTDIR)/usr/share/lcs/scripts/
	
	# install sbin
	install -d $(DESTDIR)/usr/sbin
	install -m 755 sbin/* $(DESTDIR)/usr/sbin/

clean:


.PHONY: all install clean

