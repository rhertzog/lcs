#!/bin/sh
module=`echo $1 | tr [:upper:] [:lower:]`
chown -R root:root /usr/share/doc/lcs/$module
chown -R root:root /var/lib/lcs/$1
chown -R root:www-data /usr/share/lcs/Plugins/$1
chmod -R 750 /usr/share/lcs/Plugins/$1
chmod -R 770 /usr/share/lcs/Plugins/$1/images/
