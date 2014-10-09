#!/bin/sh
chown -R root:root /usr/share/doc/lcs/$1
chown -R root:root /var/lib/lcs/$1
chown -R root:www-data /usr/share/lcs/Plugins/$1
chmod 750 /usr/share/lcs/Plugins/$1
chmod 770 /usr/share/lcs/Plugins/$1/wakka.config.php
