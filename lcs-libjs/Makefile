#!/usr/bin/make -f

all:

install:
	# install lcs libjs files
	install -d $(DESTDIR)/usr/share/lcs/libjs
	cp -a libjs/* $(DESTDIR)/usr/share/lcs/libjs

	install -d $(DESTDIR)/etc
	cp -a conf/* $(DESTDIR)/etc

clean:


.PHONY: all install clean
