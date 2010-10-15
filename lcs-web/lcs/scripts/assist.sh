#!/bin/sh

## $Id$ ##
#
##### Permet de creer un compte d'assistance pour l'interface web pdt 1 heure #####
#

if [ "$1" = "--help" -o "$1" = "-h" ]
then
	echo "Script permettant de creer un compte pour les services d'assistance academique."
	echo "Le compte permet l'acces complet al'interface web, il est detruit apres une heure."
	echo "Usage : pas d'option"
	exit
fi	

# On cree le compte avec un pass aleatoire
getent passwd assist >/dev/null && ADM=1
PASS=`date | md5sum | cut -c 3-9`

[ "$ADM" = "1" ] && echo "Le compte assist existe deja " && exit 1
UIDPOLICY=`echo "SELECT value FROM params WHERE name='uidPolicy'" | mysql -h localhost lcs_db -N`
echo "UPDATE params SET value='4' WHERE name='uidPolicy'" | mysql -h localhost lcs_db
/usr/share/lcs/sbin/userAdd.pl t assis $PASS 00000000 M Administratifs
echo "UPDATE params SET value=\"$UIDPOLICY\" WHERE name='uidPolicy'" | mysql -h localhost lcs_db
echo "compte administrateur temporaire cree"
echo "login: assist"
echo "passw: $PASS"
echo "ce compte expirera dans une heure"

### On adapte les droit pour LCS 2
chown root\:lcs-users /home/assist
chmod 750 /home/assist

# Le compte expirera dans une heure
echo  "/usr/share/lcs/sbin/userDel.pl assist" | at now+1 hour
# Mise en place des droits se3_is_admin et lcs_is_admin
peopleRdn=`mysql lcs_db -B -N -e "select value from params where name='peopleRdn'"`
ldap_base_dn=`mysql lcs_db -B -N -e "select value from params where name='ldap_base_dn'"`
rightsRdn=`mysql lcs_db -B -N -e "select value from params where name='rightsRdn'"`
cDn="uid=assist,$peopleRdn,$ldap_base_dn";
pDn="cn=se3_is_admin,$rightsRdn,$ldap_base_dn";
/usr/share/lcs/sbin/groupAddEntry.pl "$cDn" "$pDn"
pDn="cn=lcs_is_admin,$rightsRdn,$ldap_base_dn";
/usr/share/lcs/sbin/groupAddEntry.pl "$cDn" "$pDn"
