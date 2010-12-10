#!/bin/bash
#
# cahngement d'ip sur lcs2
#
#

OLDIP=`ifconfig eth0| grep "inet adr"| cut -d ":" -f 2 | cut -f 1 -d " "`
OLDGATEWAY=`cat /etc/network/interfaces | grep gateway| awk {'print $2'}`
OLDNETWORK=`mysql -Nse "select value from lcs_db.params where name='network';"`
OLDBROADCAST=`mysql -Nse "select value from lcs_db.params where name='broadcast';"`

echo "Ce script permet de changer l'ip du LCS"
echo "ATTENTION ce script est valide seulement pour un masque de sous reseau"
echo " à 255.255.255.0   (/24)."
echo "pour continuer appuyer sur entree, pour quitter ctrl + c :"
read

echo "Indiquez la nouvelle ip du LCS : "
read NEWIP
echo "Indiquez la nouvelle passerelle :"
read NEWGATEWAY
echo "Indiquer le serveur DNS PRIMAIRE si pas de changement laissez vide :"
read PRIDNS
echo "Indiquer le serveur DNS SECONDAIRE si pas de changement laissez vide :"
read SECDNS
echo "l'adresse IP $OLDIP sera remplace par $NEWIP"
echo "la passerelle sera $NEWGATEWAY"
echo "Etes vous sur ? (N/o) :"
read VALIDE

if [ -z $VALIDE ]; then
	VALIDE=n
fi
if [ $VALIDE = "n" ]; then
        exit 1;
fi

NEWPREFIX=`echo $NEWIP | cut -f 1-3 -d "."`
NEWNET=`echo "$NEWPREFIX".0`
NEWBROADCAST=`echo "$NEWPREFIX".255`
DOMAIN=`hostname -d`

echo "modification de /etc"
for i in `grep -rsl "$OLDIP" /etc/*`
do 
	sed -i'.changeip' "s/$OLDIP/$NEWIP/g" $i
done

sed -i'' "s/$OLDGATEWAY/$NEWGATEWAY/g" /etc/network/interfaces
sed -i'' "s/$OLDGATEWAY/$NEWGATEWAY/g" /etc/lcs/lcs.conf
sed -i'' "s/$OLDNETWORK/$NEWNET/g" /etc/network/interfaces
sed -i'' "s/$OLDNETWORK/$NEWNET/g" /etc/lcs/lcs.conf
sed -i'' "s/$OLDNETWORK/$NEWNET/g" /etc/squid/squid.conf
sed -i'' "s/$OLDBROADCAST/$NEWBROADCAST/g" /etc/network/interfaces
sed -i'' "s/$OLDBROADCAST/$NEWBROADCAST/g" /etc/lcs/lcs.conf

echo "modification du dns :"
if [ -z $PRIDNS ]; then
        PRIDNS=`mysql -Nse "select value from lcs_db.params where name='dns1';"`
else
	mysql -e "UPDATE lcs_db.params SET   value='$PRIDNS' WHERE name='dns1';"
fi
if [ -z $SECDNS ]; then
        SECDNS=`mysql -Nse "select value from lcs_db.params where name='dns2';"`
else
	mysql -e "UPDATE lcs_db.params SET   value='$SECDNS' WHERE name='dns2';" 
fi

/usr/sbin/dns_reconfigure.sh $PRIDNS $SECDNS  $NEWIP $HOSTNAME $DOMAIN


echo "mise a jour de la base de données"
mysql -e "UPDATE lcs_db.params SET   value='$NEWIP' WHERE name='iplcs';"
mysql -e "UPDATE lcs_db.params SET   value='$NEWIP' WHERE name='mtaip';"
mysql -e "UPDATE lcs_db.params SET   value='$NEWNET' WHERE name='network';"
mysql -e "UPDATE lcs_db.params SET   value='$NEWNET/24' WHERE name='vlansc';"
mysql -e "UPDATE lcs_db.params SET   value='$NEWGATEWAY' WHERE name='gateway';"
mysql -e "UPDATE lcs_db.params SET   value='$NEWBROADCAST' WHERE name='broadcast';"

LDAPIP=`mysql -Nse "select value from lcs_db.params where name='ldap_server';"`
if [ "$LDAPIP" = "$OLDIP" ]; then
	mysql -e "UPDATE lcs_db.params SET   value='$NEWIP' WHERE name='ldap_server';"
fi

LDAPMASTERIP=`mysql -Nse "select value from lcs_db.params where name='ldap_master_server';"`
if [ "$LDAPMASTERIP" = "$OLDIP" ]; then
	mysql -e "UPDATE lcs_db.params SET   value='$NEWIP' WHERE name='ldap_master_server';"
fi


echo "changement de l'ip termine, pour mettre en place le changement rebooter le LCS"
echo "si votre annuaire est sur SE3 relancer ldaplcs2se3.sh"
