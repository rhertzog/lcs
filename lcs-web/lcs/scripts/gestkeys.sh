#!/bin/bash
# manage lcs auth keys gestkeys.sh 04/03/2014
# $1 : open or close


if [ "$1" = "open" ]; then
	chown  -R www-data:www-data /usr/share/lcs/privatekey
	chmod 640 /usr/share/lcs/privatekey/*
	rm -f /usr/share/lcs/privatekey/*.pyc
	exit 0
elif [ "$1" = "close" ]; then
	chown -R root:www-data /usr/share/lcs/privatekey
	chmod 640 /usr/share/lcs/privatekey/*
	cp -a /usr/share/lcs/privatekey/public_key.js /var/www/lcs/public_key.js
	exit 0
else
	echo "ERR : Invalid argument."
	exit 1
fi	
