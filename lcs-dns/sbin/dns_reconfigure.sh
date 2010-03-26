#!/bin/bash
# Configure service dns sur LCS
# jLCF <(°_-)/> on 29 Mai. 2008
#
IN_CONFIG_PATH="/var/lib/lcs/dns"

if [ -z "$5" ];
then
        echo "Usage: dns_reconfigure.sh <DNS_PRIMARY> <DNS_SECONDARY> <IPADDR> <HOSTNAME> <DOMAIN>"
        exit 0
fi

DNS_PRIMARY="$1"
DNS_SECONDARY="$2"
IPADDR0="$3"
HOSTNAME="$4"
DOMAIN="$5"
LCSFULLDOMAIN=$HOSTNAME.$DOMAIN

if [ -n "$DNS_PRIMARY" ] || [ -n "$DNS_SECONDARY" ];then
    cp $IN_CONFIG_PATH/named.conf.options.in $IN_CONFIG_PATH/named.conf.options
    if [ -n "$DNS_PRIMARY" ];then
        sed -i s/#DNS_PRIMARY#/$DNS_PRIMARY\;/g $IN_CONFIG_PATH/named.conf.options
    fi
    if [ -n "$DNS_SECONDARY" ];then
        sed -i s/#DNS_SECONDARY#/$DNS_SECONDARY\;/g $IN_CONFIG_PATH/named.conf.options
    fi
    mv $IN_CONFIG_PATH/named.conf.options /etc/bind/named.conf.options
fi
LCSNET1=`echo $IPADDR0 | cut -d '.' -f 1`
LCSNET2=`echo $IPADDR0 | cut -d '.' -f 2`
LCSNET3=`echo $IPADDR0 | cut -d '.' -f 3`
LCSNET="$LCSNET1.$LCSNET2.$LCSNET3"
LCSNETREV="$LCSNET3.$LCSNET2.$LCSNET1"
LCSMACHINE=`echo $IPADDR0 | cut -d '.' -f 4`
#
# Traitement de named.conf.local
#
cp $IN_CONFIG_PATH/named.conf.local.in $IN_CONFIG_PATH/named.conf.local
sed -i s/#LCSDOMAIN#/$DOMAIN/g $IN_CONFIG_PATH/named.conf.local
sed -i s/#LCSNETREV#/$LCSNETREV/g $IN_CONFIG_PATH/named.conf.local
mv $IN_CONFIG_PATH/named.conf.local /etc/bind/named.conf.local
#
# Recherche des n° serial actuels
#
# localnet.db
if [ -e /etc/bind/localnet.db ]; then
    SERIALDB=`grep Serial /etc/bind/localnet.db | cut -d ";" -f 1 | cut -d " " -f 2`
    if [ -n $SERIALDB ]; then
        let SERIALDB+=1
    else
        SERIALDB="1"
    fi
else
    SERIALDB="1"
fi
# localnet.rev
if [ -e /etc/bind/localnet.rev ]; then
    SERIALREV=`grep Serial /etc/bind/localnet.rev | cut -d ";" -f 1 | cut -d " " -f 2`
    if [ -n $SERIALREV ]; then
        let SERIALREV+=1
    else
        SERIALREV="1"
    fi
else
    SERIALREV="1"
fi
#
# Traitement de localnet.db
#
cp $IN_CONFIG_PATH/localnet.db.in $IN_CONFIG_PATH/localnet.db
sed -i s/#SERIAL#/$SERIALDB/g $IN_CONFIG_PATH/localnet.db
sed -i s/#LCSDOMAIN#/$DOMAIN/g $IN_CONFIG_PATH/localnet.db
sed -i s/#LCSFULLDOMAIN#/$LCSFULLDOMAIN/g $IN_CONFIG_PATH/localnet.db
sed -i s/#LCSNET#/$LCSNET/g $IN_CONFIG_PATH/localnet.db
sed -i s/#LCSHOSTNAME#/$HOSTNAME/g $IN_CONFIG_PATH/localnet.db
sed -i s/"machine$LCSMACHINE\tIN"/"$HOSTNAME\tIN"/g $IN_CONFIG_PATH/localnet.db
mv $IN_CONFIG_PATH/localnet.db /etc/bind/localnet.db
#
# Traitement de localnet.rev
#
cp $IN_CONFIG_PATH/localnet.rev.in $IN_CONFIG_PATH/localnet.rev
sed -i s/#SERIAL#/$SERIALREV/g $IN_CONFIG_PATH/localnet.rev
sed -i s/#LCSFULLDOMAIN#/$LCSFULLDOMAIN/g $IN_CONFIG_PATH/localnet.rev
sed -i s/#LCSDOMAIN#/$DOMAIN/g $IN_CONFIG_PATH/localnet.rev
sed -i s/machine$LCSMACHINE.$DOMAIN/$LCSFULLDOMAIN/g $IN_CONFIG_PATH/localnet.rev
mv $IN_CONFIG_PATH/localnet.rev /etc/bind/localnet.rev
#
# Traitement de resolv.conf
#
rm /etc/resolv.conf
echo "search $5" > /etc/resolv.conf
echo "nameserver localhost" >> /etc/resolv.conf
# Disable after test
#echo "nameserver $DNS_PRIMARY" >> /etc/resolv.conf
invoke-rc.d bind9 force-reload  > /dev/null 2>&1
