#!/usr/bin/make -f
# Makefile for lcs-helpdesk

all:

install:

# install doc
	install -d $(DESTDIR)/usr/share/doc/lcs/helpdesk/html
	cp -a doc-html/* $(DESTDIR)/usr/share/doc/lcs/helpdesk/html/

# install helpdesk
	install -d $(DESTDIR)/usr/share/lcs/helpdesk
	cp -a helpdesk/* $(DESTDIR)/usr/share/lcs/helpdesk/

clean:

.PHONY: all install clean
