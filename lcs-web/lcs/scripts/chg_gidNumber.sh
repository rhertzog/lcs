#!/bin/sh

if [ "$1" = "--help" -o "$1" = "-h" ]; then
	echo "Script permettant de changer le gidNumber des membres de la branche People et Trash si elle existe"
	echo ""
	echo "Usage : chg_gidNumber.sh gid1 gid2"
	echo "Exemple : chg_gidNumber.sh 1000 5005"
	exit
fi

BASEDN=$(cat /etc/ldap/ldap.conf | grep "^BASE" | tr "\t" " " | sed -e "s/ \{2,\}/ /g" | cut -d" " -f2)
ROOTDN=$(cat /etc/ldap/slapd.conf | grep "^rootdn" | tr "\t" " " | cut -d'"' -f2)
PASSDN=$(cat /etc/ldap.secret)

if [ -z $1 ] || [ -z $2 ]; then
	echo "Parametres invalides"
	exit 1
fi

gid1=$1
gid2=$2



ladate=$(date +"%Y.%m.%d-%H.%M.%S");

tmp=/root/chg_gidNumber.$ladate/
mkdir -p $tmp
chmod 700 $tmp

ldapsearch -xLLL -b ou=People,$BASEDN gidNumber=$gid1 uid | grep "^uid: " | sed -e "s/^uid: //" | while read uid
do
        echo "Modification gidNumber de $uid" | tee -a $tmp/chg_gidNumber_comptes.log
        echo "dn: uid=$uid,ou=People,$BASEDN
changetype: modify
replace: gidNumber
gidNumber: $gid2
" > $tmp/${uid}_modif.ldif
        ldapmodify -x -D $ROOTDN -w $PASSDN -f $tmp/${uid}_modif.ldif | tee -a $tmp/chg_gidNumber_comptes.log
done     
if [ ! -z $(ldapsearch -xLLL ou=Trash) ]; then
echo "Trash"
ldapsearch -xLLL -b ou=Trash,$BASEDN gidNumber=$gid1 uid | grep "^uid: " | sed -e "s/^uid: //" | while read uid
do
        echo "Modification gidNumber de $uid" | tee -a $tmp/chg_gidNumber_comptes.log
        echo "dn: uid=$uid,ou=Trash,$BASEDN
changetype: modify
replace: gidNumber
gidNumber: $gid2
" > $tmp/${uid}_modif.ldif
        ldapmodify -x -D $ROOTDN -w $PASSDN -f $tmp/${uid}_modif.ldif | tee -a $tmp/chg_gidNumber_comptes.log
done
fi

rm -Rf $tmp/*.ldif
