#!/bin/bash
#
##### Script de sauvegarde de l'annuaire lcs #####
#
# Auteur pour le se3 : Stephane Boireau (Bernay/Pont-Audemer (27))
# Adapte et simplifie pour Lcs par David Gloux (50)
#
# Derniere modif: 05/05/2008

# Chemin des sauvegardes:

dossier_svg="/var/backups/ldap"

#Couleurs
COLTITRE="\033[1;35m"   # Rose
COLTXT="\033[0;37m"     # Gris
COLCMD="\033[1;37m"     # Blanc

echo -e "$COLTITRE"
echo "************************************"
echo "* Sauvegarde de l'annuaire  *"
echo "************************************"
echo -e "$COLCMD\c"

mkdir -p "$dossier_svg"
mkdir -p "$dossier_svg/svg_hebdo"

ladate=$(date +"%Y.%m.%d-%H.%M.%S");
jour=$(date +%a)
semaine=$(date +%-V)
semainesauv=$((semaine-1))

if [ ! -e "$dossier_svg/svg_hebdo/num_semaine.txt" ]; then
touch "$dossier_svg/svg_hebdo/num_semaine.txt"
echo $semaine > "$dossier_svg/svg_hebdo/num_semaine.txt"
fi

if [ $semaine != $(cat "$dossier_svg/svg_hebdo/num_semaine.txt") ]; then
	# C'est une nouvelle semaine qui commence... on met de cote la sauvegarde de la semaine precedente.
	echo $semaine > "$dossier_svg/svg_hebdo/num_semaine.txt"
	# On supprime la sauvegarde de l'annee precedente si elle existe
	if [ -d "$dossier_svg/svg_hebdo/semaine_${semainesauv}" ]; then
	rm -fr $dossier_svg/svg_hebdo/semaine_${semainesauv}
	fi
	# et on (re)cree le dossier
	mkdir -p $dossier_svg/svg_hebdo/semaine_${semainesauv}
	liste=($(ls $dossier_svg/DB_CONFIG.*))
	if [ ${#liste[*]} -gt 0 ]; then
	      # On recupere la premiere sauvegarde de la semaine
	      cp -a ${liste[0]} $dossier_svg/svg_hebdo/semaine_${semainesauv}
	fi
	
	liste=($(ls $dossier_svg/ldap.*.ldif))	
	if [ ${#liste[*]} -gt 0 ]; then
		# On recupere la premiere sauvegarde de la semaine
		cp -a ${liste[0]} $dossier_svg/svg_hebdo/semaine_${semainesauv}
	fi
	if [ -e $dossier_svg/ldap.se3sav.tar.gz ]; then
	        cp -a $dossier_svg/ldap.se3sav.tar.gz $dossier_svg/svg_hebdo/semaine_${semaine}
	fi
fi

ldapsearch -xLLL -D $(grep ^rootdn /etc/ldap/slapd.conf|tr "\t" " " | sed -e "s/ \{2,\}/ /g" | sed -e "s/'//g"  | sed -e 's/"//g' | cut -d" " -f2) -w $(cat /etc/ldap.secret) > "$dossier_svg/ldap.$jour.ldif"
cp -f /var/lib/ldap/DB_CONFIG $dossier_svg/DB_CONFIG.$jour

chown -R root:root "$dossier_svg"
chmod -R 700 "$dossier_svg"

echo ""
echo -e "$COLTITRE"
echo "***********"
echo "* Termine *"
echo "***********"
echo -e "$COLTXT"
