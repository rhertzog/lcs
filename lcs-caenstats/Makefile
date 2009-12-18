#!/usr/bin/make -f

all:

install:
	# install conf files
	#install -d $(DESTDIR)/etc/lcs/
	#install -m 644 conf/* $(DESTDIR)/etc/lcs/
                     
	# install home superviseur
	install -d $(DESTDIR)/home/superviseur
	cp -a  home/* $(DESTDIR)/home
                                         
clean:
                                        
.PHONY: all install clean
