#!/bin/bash

# $Id$
# Script destiné à effectuer une sauvegarde de l'annuaire LDAP avant de procéder à un nouvel import
# Auteur: Stephane Boireau (27)
# Dernière modification: 08/03/2007

WWWPATH="/var/www"
dossier_svg="/root/save/sauvegarde_ldap_avant_import"


if [ "$1" = "--help" -o "$1" = "-h" ]; then
        echo "Script destiné à effectuer une sauvegarde de l'annuaire LDAP vers"
		echo "   $dossier_svg"
		echo "avant de procéder à un nouvel import."
        echo ""
        echo "Usage : pas d'option"
        exit
fi

mkdir -p $dossier_svg
date=$(date +%Y%m%d-%H%M%S)
#
# Récupération des paramètres de connexion à la base
#
if [ -e $WWWPATH/lcs/includes/config.inc.php ]; then
  HOSTAUTH=`cat $WWWPATH/lcs/includes/config.inc.php | grep "HOSTAUTH=" | cut -d = -f 2 |cut -d \" -f 2`
  DBAUTH=`cat $WWWPATH/lcs/includes/config.inc.php | grep "DBAUTH=" | cut -d = -f 2 |cut -d \" -f 2`
  USERAUTH=`cat $WWWPATH/lcs/includes/config.inc.php | grep "USERAUTH=" | cut -d = -f 2 |cut -d \" -f 2`
  PASSAUTH=`cat $WWWPATH/lcs/includes/config.inc.php | grep "PASSAUTH=" | cut -d = -f 2 |cut -d \" -f 2`
else
  echo "Fichier de conf inaccessible"
  exit 1
fi
#

MYSQLCNX="mysql -h $HOSTAUTH $DBAUTH -u $USERAUTH -p$PASSAUTH -N"
PARAMLDAP=(`echo "select value from lcs_db.params  where name='ldap_server' or name='ldap_base_dn' or name='adminRdn' or name='adminPw'"|$MYSQLCNX`)
DOMAIN=`hostname -d`

echo "${PARAMLDAP[0]} ${PARAMLDAP[1]} ${PARAMLDAP[2]} ${PARAMLDAP[3]} $DOMAIN";

echo "Erreur lors de la sauvegarde de précaution effectuée avant import.
Le $date" > /tmp/erreur_svg_prealable_ldap_${date}.txt
# Le fichier d erreur est généré quoi qu il arrive, mais il n est expédié qu en cas de problème de sauvegarde
/usr/bin/ldapsearch -xLLL -h ${PARAMLDAP[0]} -D  ${PARAMLDAP[2]},${PARAMLDAP[1]} -w ${PARAMLDAP[3]} > $dossier_svg/ldap_${date}.ldif || mail admin@$DOMAIN -s "Erreur sauvegarde LDAP" < /tmp/erreur_svg_prealable_ldap_${date}.txt
rm -f /tmp/erreur_svg_prealable_ldap_${date}.txt
