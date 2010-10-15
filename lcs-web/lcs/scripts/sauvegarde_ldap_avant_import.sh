#!/bin/bash

# $Id$
# Script destine a effectuer une sauvegarde de l'annuaire LDAP avant de proceder a un nouvel import
# Auteur: Stephane Boireau (27)
# Modifications pour LCS : Jean-Luc Chretien (50)
# Derniere modification: 16/09/2010

dossier_svg="/root/save/sauvegarde_ldap_avant_import"

if [ "$1" = "--help" -o "$1" = "-h" ]; then
        echo "Script destine a effectuer une sauvegarde de l'annuaire LDAP vers"
		echo "   $dossier_svg"
		echo "avant de proceder à un nouvel import."
        echo ""
        echo "Usage : pas d'option"
        exit
fi

mkdir -p $dossier_svg
date=$(date +%Y%m%d-%H%M%S)

BASEDN=$(cat /etc/ldap/ldap.conf | grep "^BASE" | tr "\t" " " | sed -e "s/ \{2,\}/ /g" | cut -d" " -f2)
ROOTDN=$(cat /etc/ldap/slapd.conf | grep "^rootdn" | tr "\t" " " | cut -d'"' -f2)
PASSDN=$(cat /etc/ldap.secret)



echo "Erreur lors de la sauvegarde de precaution effectuee avant import.
Le $date" > /tmp/erreur_svg_prealable_ldap_${date}.txt
# Le fichier d erreur est genere quoi qu il arrive, mais il n est expedie qu en cas de probleme de sauvegarde
/usr/bin/ldapsearch -xLLL -D $ROOTDN -w $PASSDN > $dossier_svg/ldap_${date}.ldif || mail root -s "Erreur sauvegarde LDAP" < /tmp/erreur_svg_prealable_ldap_${date}.txt
rm -f /tmp/erreur_svg_prealable_ldap_${date}.txt
