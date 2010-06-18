#!/bin/sh
chown -R root:root /usr/share/doc/lcs/$1
chown -R root:root /var/lib/lcs/$1
chown -R 33:33 /usr/share/lcs/$1
chmod 750 /usr/share/lcs/$1
