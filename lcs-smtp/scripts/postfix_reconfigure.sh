#!/bin/bash
# Make main.cf / ldap-aliases.cf 
# GrosQuicK  Mon, 3 Dec 2007
# Modify by jLCF <(°_-)/> on 6 Nov. 2008
#
IN_CONFIG_PATH="/etc/lcs/postfix"

if [ "$7" = "" ];
then
        echo "Usage: postfix_reconfigure.sh <FQDN> <DOMAIN> <LOCAL_DOMAIN> <NETWORK> <LDAP_SERVER> <BASEDN> <RELAYHOST> <XEN_NETWORK>"
        exit 0
fi

FQHN="$1"
DOMAIN="$2"
LOCAL_DOMAIN="$3"
LAN_NETWORK="$4"
LDAP_SERVER="$5"
BASE_DN="$6"
RELAY_HOST="$7"
XEN_NETWORK="$8"

if [ -e /etc/postfix/main.cf ]; 
then
        mv /etc/postfix/main.cf /etc/postfix/main.cf.lcssav
fi
cat $IN_CONFIG_PATH/main_lcslis.cf \
        |sed -e "s!#FQDN#!$FQHN!g" \
        |sed -e "s!#DOMAIN#!$DOMAIN!g" \
        |sed -e "s!#LOCAL_DOMAIN#!$LOCAL_DOMAIN!g" \
        |sed -e "s!#LAN_NETWORK#!$LAN_NETWORK!g" \
        |sed -e "s!#RELAY_HOST#!$RELAY_HOST!g" \
        |sed -e "s!#XEN_NETWORK#!$XEN_NETWORK!g" \
        >/etc/postfix/main.cf
sed -i s/,,/,/g  /etc/postfix/main.cf
#set the mailname
echo "$DOMAIN" > /etc/mailname

# Add alias map with ldap search filter on mail attribute
# if not exist (must be in main_lcslis.cf sample file)
RESULT=`cat /etc/postfix/main.cf | egrep "ldap-aliases.cf"`
if   [ "$RESULT" = "" ] ; then
	echo "alias_maps = hash:/etc/aliases, ldap:/etc/postfix/ldap-aliases.cf" >> /etc/postfix/main.cf
fi
# test if include for mailing list aliases is in place on old main.cf
# and put it if necessary
if [ -e /etc/postfix/main.cf.lcssav ]; then
    RESULT=`cat /etc/postfix/main.cf.lcssav | grep "mailing_list.cf"`
    if   [ "$RESULT" != "" ] ; then
	echo "# Include mainling list configuration" >> /etc/postfix/main.cf
	echo "alias_maps = ldap:/etc/postfix/mailing_list.cf" >> /etc/postfix/main.cf
    fi
fi
# ldap-aliases map
echo "server_host = $LDAP_SERVER" > /etc/postfix/ldap-aliases.cf
echo "search_base = $BASE_DN" >> /etc/postfix/ldap-aliases.cf
echo "query_filter = (mail=%s@$DOMAIN)" >>  /etc/postfix/ldap-aliases.cf
echo "result_attribute = uid" >> /etc/postfix/ldap-aliases.cf

/etc/init.d/postfix restart >/dev/null
