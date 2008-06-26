#!/bin/bash
# apache2-ssl-cert.sh <FQN> <DOMAIN> <COUNTRYNAME> <PROVINCENAME> <LOCALITYNAME> <ORGANIZATIONNAME> <ORGANIZATIONALUNITNAME>
IN_CONFIG_PATH="/etc/lcs/apache2ssl"

if [ "$7" = "" ];
then
        echo "Usage : apache2-ssl-cert.sh <FQN> <DOMAIN> <COUNTRYNAME> <PROVINCENAME> <LOCALITYNAME> <ORGANIZATIONNAME> <ORGANIZATIONALUNITNAME>"
        exit 0
fi

FQN="$1"
DOMAIN="$2"
COUNTRYNAME="$3"
PROVINCENAME="$4"
LOCALITYNAME="$5"
ORGANIZATIONNAME="$6"
ORGANIZATIONALUNITNAME="$7"

cat $IN_CONFIG_PATH/openssl.lcs.in \
        |sed -e "s!#FQN#!$FQN!g" \
        |sed -e "s!#DOMAIN#!$DOMAIN!g" \
        |sed -e "s!#COUNTRYNAME#!$COUNTRYNAME!g" \
        |sed -e "s!#PROVINCENAME#!$PROVINCENAME!g" \
        |sed -e "s!#LOCALITYNAME#!$LOCALITYNAME!g" \
        |sed -e "s!#ORGANIZATIONNAME#!$ORGANIZATIONNAME!g" \
        |sed -e "s!#ORGANIZATIONALUNITNAME#!$ORGANIZATIONALUNITNAME!g" \
        > $IN_CONFIG_PATH/openssl.lcs

openssl req  -config $IN_CONFIG_PATH/openssl.lcs -x509 -nodes -days 365 -newkey rsa:1024 -out /etc/apache2/ssl/server.crt -keyout /etc/apache2/ssl/server.key
chown root:root /etc/apache2/ssl
chmod 440 /etc/apache2/ssl/server.key

exit 0