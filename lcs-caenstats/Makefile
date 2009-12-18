#!/usr/bin/make -f

all:

install:
        # install conf files
        #install -d $(DESTDIR)/etc/lcs/
        #install -m 644 conf/* $(DESTDIR)/etc/lcs/
                       
        # install home superviseur
        install -d $(DESTDIR)/home/superviseur
        install -m 750 home/* $(DESTDIR)/home
                                          
clean:
                                        
.PHONY: all install clean
