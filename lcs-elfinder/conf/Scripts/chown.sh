#!/bin/bash

chown -R root:root /usr/share/doc/lcs/$1
chown -R root:root /var/lib/lcs/$1
chown -R root:www-data /usr/share/lcs/$1
chmod -R 750 /usr/share/lcs/$1
