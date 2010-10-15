#!/usr/bin/make -f
# Makefile for lcs-Biilan2

all:

install:


# install doc
	install -d $(DESTDIR)/usr/share/doc/lcs/Biilan2/html
	cp -a doc-html/* $(DESTDIR)/usr/share/doc/lcs/Biilan2/html/

# install Biilan2
	install -d $(DESTDIR)/usr/share/lcs/Plugins/Biilan2
	cp -a Biilan2/* $(DESTDIR)/usr/share/lcs/Plugins/Biilan2

# install Biilan2 conf
	install -d $(DESTDIR)/var/lib/lcs/Biilan2
	cp -a conf/* $(DESTDIR)/var/lib/lcs/Biilan2/	

clean:

.PHONY: all install clean
