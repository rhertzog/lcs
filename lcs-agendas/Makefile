#!/usr/bin/make -f
# Makefile for lcs-Agendas

all:

install:


# install doc
	install -d $(DESTDIR)/usr/share/doc/lcs/Agendas/html
	cp -a doc-html/* $(DESTDIR)/usr/share/doc/lcs/Agendas/html/

# install Agendas
	install -d $(DESTDIR)/usr/share/lcs/Plugins/Agendas
	cp -a Agendas/* $(DESTDIR)/usr/share/lcs/Plugins/Agendas

# install Agendas conf
	install -d $(DESTDIR)/var/lib/lcs/Agendas
	cp -a conf/* $(DESTDIR)/var/lib/lcs/Agendas/	

clean:

.PHONY: all install clean
