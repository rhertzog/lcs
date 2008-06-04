#!/bin/bash


# GrosQuicK :
# test if include is in place on main.cf
RESULT=`cat /etc/postfix/main.cf | egrep "mailing_list.cf"`
if   [ "$RESULT" = "" ] ; then
	echo "alias_maps = ldap:/etc/postfix/mailing_list.cf" >> /etc/postfix/main.cf
fi
echo "$1" >> /etc/postfix/mailing_list.cf

/etc/init.d/postfix reload
