#!/usr/bin/make -f
# Makefile for gestEtab-monitor

all:

install:

# install conf
	install -d $(DESTDIR)/etc/gestEtab
	install -m 0660 conf/* $(DESTDIR)/etc/gestEtab

# install sbin
	install -d $(DESTDIR)/usr/sbin
	install -m 0750 sbin/* $(DESTDIR)/usr/sbin

# install lib
	install -d $(DESTDIR)/usr/share/gestEtab
	install -m 0644 lib/* $(DESTDIR)/usr/share/gestEtab

clean:


.PHONY: all install clean