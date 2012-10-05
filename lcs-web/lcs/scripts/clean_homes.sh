#!/bin/bash

# for the moment only for restaure corrects rights and move obsoletes home files



# on teste si slapd tourne
ldap_status()
{
testannu=$(getent passwd admin)
if [ ! -z "$testannu" ]; then
	echo "Service Annuaire Ok"
else
	echo "ERREUR : Service Annuaire local ou distant HS !!!"
	exit 1
fi 

}

LADATE=$(date +%d-%m-%Y)
DEST=/home/admin/Trash_users/_Trash_$LADATE

cpt=0
#     cd /home
ldap_status

for USER in `ls /home`
do
    if  [ "$USER" != "superviseur" ]; then
      
	USEROK=$(getent passwd $USER) 
	if [ ! -z "$USEROK" ]; then
		echo "Modification des droits de /home/$USER"
	        chown -R $USER /home/$USER/Maildir 
	   [ -e /home/$USER/Documents ] && chown -R $USER /home/$USER/Documents 
	   [ -e /home/$USER/public_html ] && chown -R $USER /home/$USER/public_html
	else
	  if [ "$cpt" = "0" ]; then
		mkdir -p /home/admin/Trash_users
		chown admin:admins /home/admin/Trash_users
		mkdir -p ${DEST}
		cpt=1
	  fi
	  if [ ! -z "$(echo "$USER" | grep -i "bcdi")" ]; then
	  echo "conservation dossier bcdi"
	  else
	    echo "$USER n'est pas dans l'annuaire "
	    echo "deplacement de /home/$USER vers $DEST/"
	    mv /home/$USER $DEST/
	    chown -R admin:lcs-users $DEST
	  fi
# 	  
	fi
    fi
done





