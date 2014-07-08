#!/bin/bash

chown -R root:root /usr/share/doc/lcs/roundcube
chown -R root:root /var/lib/lcs/roundcube
chown -R root:www-data /usr/share/lcs/roundcube
chmod -R 750 /usr/share/lcs/roundcube
chmod -R 770 /usr/share/lcs/roundcube/temp
if [ -d /var/log/lcs/roundcube ]; then
	chown -R www-data:www-data /var/log/lcs/roundcube
fi