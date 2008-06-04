#!/bin/bash
FILE="/etc/postfix/mailing_list.cf"
echo "# mailing list desactivated" >$FILE
# remove mailing_list aliases on main.cf
mv /etc/postfix/main.cf /etc/postfix/main.cf.lcssav
sed '/mailing_list.cf/d' /etc/postfix/main.cf.lcssav > /etc/postfix/main.cf

/etc/init.d/postfix restart
