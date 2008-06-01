#!/bin/bash
# ===================================================================
# Projet LCS : Linux Communication Server
# par Misterphi le 28/01/2008
# gestion des modules par interface web
# scripts d'apt-get sudoïfié
# paramètres passés :	$1=source du paquet; 
#				$2= action (install/remove);
#				$3= nom du paquet
# ====================================================================

# on positionne les drapeaux indiquant le début de l'opération. flip nécessaire pour la page auto refresh 
echo "<? \$flip=0;\$verrou=1;?>" > /usr/share/lcs/Modules/flag.php

# La source ne peut être qu'un ftp du crdp
if [ "$1" = "deb ftp://debian.crdp.ac-caen.fr/debian sarge Lcs" -o "$1" = "deb ftp://debian.crdp.ac-caen.fr/debian sarge LcsXP" -o "$1" = "deb ftp://debian.crdp.ac-caen.fr/debian sarge LcsTesting"  -o "$1" = "deb ftp://debian.crdp.ac-caen.fr/debian etch Lcs"   -o "$1" = "deb ftp://debian.crdp.ac-caen.fr/debian etch LcsXP"  -o "$1" = "deb ftp://debian.crdp.ac-caen.fr/debian etch LcsTesting" ] ; then 

# Par sécurité, on modifie le sources.list pour n'installer que des paquets Crdp-Caen ou sûrs !!!
if [ "$2" = "install" ];then
mv /etc/apt/sources.list /etc/apt/sources.tmp
echo "deb http://ftp.fr.debian.org/debian/ etch main" > /etc/apt/sources.list
echo "deb-src http://ftp.fr.debian.org/debian/ etch main" >> /etc/apt/sources.list
echo "deb http://security.debian.org/ etch/updates main" >> /etc/apt/sources.list
echo "deb-src http://security.debian.org/ etch/updates main" >> /etc/apt/sources.list
echo $1 >> /etc/apt/sources.list
fi

#on simule une cli dans la page web
echo " <TABLE WIDTH=70% BORDER=2 ALIGN=CENTER><TR><TD WIDTH=50% ALIGN=TOP BGCOLOR='#000000'><FONT FACE='Arial, sans-serif'COLOR='#FFFFFF'><FONT SIZE=2>" >> /tmp/ecran_install_$3.html 
echo "$2 $3" | sed -e "s/$/<BR>/g" >> /tmp/ecran_install_$3.html 

#on initialise dpkg au cas où un processus apt aurait foiré précédemment
dpkg --configure -a

#on lance le processus
apt-get update | sed -e "s/$/<BR>/g" >>  /tmp/ecran_install_$3.html 
apt-get --force-yes $2 -y $3 | sed -e "s/$/<BR>/g" >>  /tmp/ecran_install_$3.html

#on teste l'état du paquet, et on affiche le résultat
vartest=`dpkg -l $3 | grep ii | wc -l`
	if [ $vartest -eq 0 ]; then
	if [ "$2" = "install" ];then
		echo "<FONT color='#FF0000'>Le paquet $3 n'est <B>PAS OPERATIONNEL</B></FONT>" | sed -e "s/$/<BR><BR>/g" >>  /tmp/ecran_install_$3.html
		else echo "<BR><FONT color='#FF0000'>Le paquet $3 est <B>DESINSTALLE</B> </FONT>" | sed -e "s/$/<BR><BR>/g" >>  /tmp/ecran_install_$3.html
		fi
		else echo "<FONT color='#33FF33'> Le paquet $3 est <B>OPERATIONNEL</B> </FONT>" | sed -e "s/$/<BR><BR>/g" >>  /tmp/ecran_install_$3.html
	fi

#pour le fun, on affiche une invite 	
echo "www-data@Lcs:/$<blink><B>_</B></blink>" | sed -e "s/$/<BR>/g" >>  /tmp/ecran_install_$3.html
echo "</FONT></FONT></TD></TR></TABLE>" >> /tmp/ecran_install_$3.html 

#on restaure le sourceslist d'origine
if [ "$2" = "install" ];then
mv  /etc/apt/sources.tmp /etc/apt/sources.list
fi

#on initialise dpkg si le processus a foiré
dpkg --configure -a

#pour que le script puisse effacer le fichier temporaire
chown www-data:www-data /tmp/ecran_install_$3.html
fi

#on positionne les drapeaux indiquant la FIN de l'opération, flip pour arrêter l'auto refresh
echo "<? \$flip=1;\$verrou=0;?>" > /usr/share/lcs/Modules/flag.php

# on at la raz de $flip  en cas de pb sur la page autorefresh
at now+1minutes  < /usr/share/lcs/Modules/jobinit.sh

## THE END
