#!/bin/bash
# Ouverture/Fermeture de l'espace web perso 19/03/2014
# $1 : login
# $2 : 1 = Ouverture, 0 = Fermeture

if [ -z $1 ]; then
	echo "ERR : Empty login."
	exit 1
fi

if [[ ! $1 =~ ^[a-z2-9._-]+$ ]]; then
        echo "ERR : Improper login."
        exit 1
fi

if [ $1 = root ]; then
	echo "ERR : Invalid login root."
	exit 1
fi

COUNTLOGIN=`ldapsearch -xLLL uid="$1" | grep -c "uid:"`

if [ $COUNTLOGIN = 0 ]; then
	echo "ERR : Unknow login in LDAP."
	exit 1
fi

# Si $2 1 Ouverture de l'espace web
if [ $2 == "1" ]; then
	if [ -d /home/$1/public_html ]; then
    		chown -R $1:lcs-users /home/$1/public_html
    		chmod 770 /home/$1/public_html
	fi
elif [ $2 == "0" ]; then 
	if [ -d /home/$1/public_html ]; then
    		# Fermeture de l'espace web en ecriture
    		chown -R root:root /home/$1/public_html
    		chmod 755 /home/$1/public_html
    		mv /home/$1/public_html/index.html /home/$1/public_html/index.html.sav
    		cp /etc/skel/public_html/index.html /home/$1/public_html/
    		chmod 664 /home/$1/public_html/index.html
	fi
else
	echo "ERR : Unknow action"
	exit 1
fi

exit 0
