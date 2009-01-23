#!/bin/bash
grep -v 'alias_maps' /etc/postfix/main.cf > /etc/postfix/main.cf.lcssav
echo "alias_maps = hash:/etc/aliases, ldap:/etc/postfix/ldap-aliases.cf, ldap:/etc/postfix/mailing_list.cf" >> /etc/postfix/main.cf.lcssav
mv  /etc/postfix/main.cf.lcssav  /etc/postfix/main.cf
echo "$1" >> /etc/postfix/mailing_list.cf

/etc/init.d/postfix reload