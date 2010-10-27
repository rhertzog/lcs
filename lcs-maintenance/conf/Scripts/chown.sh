#!/bin/sh
module=`echo $1 | tr [:upper:] [:lower:]`
chown -R root:root /usr/share/doc/lcs/$module
chown -R root:root /var/lib/lcs/$1
chown -R www-data:www-data /usr/share/lcs/Plugins/$1
chmod 750 /usr/share/lcs/Plugins/$1