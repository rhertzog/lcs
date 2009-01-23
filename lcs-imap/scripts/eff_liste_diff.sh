#!/bin/bash
FILE="/etc/postfix/mailing_list.cf"
echo "# mailing list desactivated" >$FILE
grep -v 'alias_maps' /etc/postfix/main.cf > /etc/postfix/main.cf.lcssav
echo "alias_maps = hash:/etc/aliases, ldap:/etc/postfix/ldap-aliases.cf" >> /etc/postfix/main.cf.lcssav
mv  /etc/postfix/main.cf.lcssav  /etc/postfix/main.cf

/etc/init.d/postfix reload