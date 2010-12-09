#!/usr/bin/make -f
# Makefile for lcs-zonep

all:

install:
# install /etc/lcs/sudo.d/lcs-zonep
	install -d $(DESTDIR)/etc/lcs/sudo.d
	install -m 644 etc/lcs/sudo.d/lcs-zonep $(DESTDIR)/etc/lcs/sudo.d

# create /usr/share/lcs/lcs-web-hooks.d/
	install -d $(DESTDIR)/usr/share/lcs/lcs-web-hooks.d/post_auth
	install -m 644 usr/share/lcs/lcs-web-hooks.d/post_auth/update_zonep_config.php $(DESTDIR)/usr/share/lcs/lcs-web-hooks.d/post_auth

# install /usr/bin/lcs-zonep-update-credentials
	install -d $(DESTDIR)/usr/bin
	install -m 755 bin/lcs-zonep-update-credentials $(DESTDIR)/usr/bin

clean:

.PHONY: all install clean
