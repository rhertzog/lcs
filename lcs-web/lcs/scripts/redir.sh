#!/bin/bash
# creation d un fichier forward
# $1 : adresse mail perso (=aucune pour supprimer la redirection)
# $2 : login
# $3 : adresse mail locale
# 03/03/2013

#verif nb params
if [ $# -lt 2  -o $# -gt 3 ]; then
	exit 1
fi

#verif  params
if [[ ! $2 =~ ^[a-z0-9._-]+$ ]] || [ -z $1 ] || [ -z $2 ] || [ $2 = root ]; then
	exit 1
fi

#pas de params exotiques
is_valid_login=`ldapsearch -xLLL uid="$2" uid | grep -c "uid=$2,ou=People"`

#creation du .forward
if [ "$1" != "aucune" ]; then
	if  [ $is_valid_login = 1 ] && [ -d /home/$2 ] && [[ $1 =~ ^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}$ ]] ; then
    	echo $1 > /home/$2/.forward
    	chown $2:lcs-users /home/$2/.forward
    	chmod 660 /home/$2/.forward
  	else
	exit 1
	fi
fi

#suppression du .forward	
if [ "$1" == "aucune" ] ; then
	if [ $is_valid_login = 1 ] && [  -e /home/$2/.forward ]; then
	rm -f /home/$2/.forward
	else
	exit 1
	fi
fi

#ajout adresse locale pour garder une copie du mail
if [  "$3" != "" ]; then
	if [[ $3 =~ ^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}$ ]] && [ $is_valid_login = 1 ] && [ -e /home/$2/.forward ] ; then
    	echo $3 >> /home/$2/.forward
   	else
	exit 1
	fi
fi
