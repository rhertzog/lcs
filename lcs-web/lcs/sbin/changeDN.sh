#!/bin/bash

# LCS
# Script de changement de DN ldap
# 04/01/2008 Simon CAVEY <simon.cavey@crdp.ac-caen.fr>
# 

old_dn=`cat /etc/ldap/ldap.conf |grep BASE | cut -f 2 -d " "`


if  [ $# == 0 ]; then
	clear
	echo "####################################"
	echo "Changement de DN"
	echo ""
	echo "vous n avez pas passe d argument au script"
	echo "le nouveau DN pour ldap va vous etre demande"
	echo "vous pouvez aussi utiliser la commande : "
	echo "changeDN.sh \"<nouveau DN>\" "
	echo ""
	echo "le DN actuel est : $old_dn"
	echo -n "donnez le nouveau DN :"	
	read new_dn
else 
	new_dn=$1
fi

echo "Export ldap"
slapcat -b $old_dn -l exportldap_changedn.ldif

echo "generation nouvel annuaire"
sed -i.ex-dn s/"$old_dn"/"$new_dn"/g exportldap_changedn.ldif

echo "Suppression ancien annuaire"
mv /var/lib/ldap/DB_CONFIG /tmp
rm /var/lib/ldap/*
mv /tmp/DB_CONFIG /var/lib/ldap/                               

## changement de DN dans etc
liste=`grep -irl "$old_dn" /etc/*`
for var in $liste ; do 
		sed -i.ex-dn s/"$old_dn"/"$new_dn"/g $var;
done

echo "importation nouvel annuaire"
slapadd -l exportldap_changedn.ldif


## changement de DN dans /usr/share/lcs/
liste=`grep -irl "$old_dn" /usr/share/lcs/*`
for var in $liste ; do 
		sed -i.ex-dn s/"$old_dn"/"$new_dn"/g $var;
done

## changement de DN dans mysql
mysql -e "UPDATE lcs_db.params SET value='$new_dn' WHERE name='ldap_base_dn';"

## redemarrage des services
for i in apache slapd courier-authdaemon courier-imap courier-imap-ssl
do
	/etc/init.d/$i restart >>/dev/null
done

