#!/bin/bash
# ===================================================================
# Projet LCS : Linux Communication Server
# par Misterphi le 06/11/2010
# gestion des modules par interface web
# scripts d'apt-get sudoifie
# paramètres passés :	$1=source du paquet; 
#				$2= action (install/remove);
#				$3= nom du paquet
# ====================================================================

# on positionne les drapeaux indiquant le début de l'opération. flip nécessaire pour la page auto refresh 
echo "<? \$flip=0;\$verrou=1;?>" > /usr/share/lcs/Modules/flag.php

#on recherche un depot similaire a $1
Depot=`grep \`echo $1 | cut -d"/" -f3\` /etc/apt/sources.list`
if [ -n "$Depot" ] || [ "$2" = "remove  --purge" ] ;then

	if [ "$2" = "install" ]; then
  cp /etc/apt/sources.list /etc/apt/sources.tmp
  sed -i "s#$Depot#$1#g" /etc/apt/sources.list
   fi

   #on simule une cli dans la page web
   echo " <TABLE WIDTH=70% BORDER=2 ALIGN=CENTER><TR><TD WIDTH=50% ALIGN=TOP BGCOLOR='#000000'><FONT FACE='Arial, sans-serif'COLOR='#FFFFFF'><FONT SIZE=2>" >> /tmp/ecran_install_$3.html 
   echo "$2 $3" | sed -e "s/$/<BR>/g" >> /tmp/ecran_install_$3.html 

   #on initialise dpkg au cas où un processus apt aurait foiré précédemment
   dpkg --configure -a

   #on lance le processus
   apt-get update | sed -e "s/$/<BR>/g" >>  /tmp/ecran_install_$3.html 
   apt-get $2 -y $3 | sed -e "s/$/<BR>/g" >>  /tmp/ecran_install_$3.html

   #on teste l'état du paquet, et on affiche le résultat
   vartest=`dpkg -l $3 | grep '\(^ii \|^hi \)' | wc -l`
   varhold=`dpkg -l $3 | grep '^hi' | wc -l`
   if [ $vartest -eq 0 ]; then
       if [ "$2" = "install" ];then
           echo "<FONT color='#FF0000'>Le paquet $3 n'est <B>PAS OPERATIONNEL</B></FONT>" | sed -e "s/$/<BR><BR>/g" >>  /tmp/ecran_install_$3.html
       else echo "<BR><FONT color='#FF0000'>Le paquet $3 est <B>DESINSTALLE</B> </FONT>" | sed -e "s/$/<BR><BR>/g" >>  /tmp/ecran_install_$3.html
       fi
   else echo "<FONT color='#33FF33'> Le paquet $3 est <B>OPERATIONNEL</B> </FONT>" | sed -e "s/$/<BR>/g" >>  /tmp/ecran_install_$3.html
   if [ $varhold -eq 1 ]; then
	   if [ "$2" = "install" ];then
	   echo "<FONT color='#FF6600'>Mais la mise &#224; jour est interdite</FONT>" | sed -e "s/$/<BR>/g" >>  /tmp/ecran_install_$3.html
   	   fi
   	   if [ "$2" = "remove  --purge" ];then
	   echo "<FONT color='#FF6600'>La d&#233;sinstallation est interdite</FONT>" | sed -e "s/$/<BR>/g" >>  /tmp/ecran_install_$3.html
   	   fi
   	 fi  
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
else echo "<BR><FONT color='#FF0000'>D&eacute;p&ocirc;t $1 non trouv&eacute; dans la liste des d&eacute;p&ocirc;ts ; veuillez contacter l'administrateur du r&eacute;seau.</FONT>" | sed -e "s/$/<BR><BR>/g" >> /tmp/ecran_install_$3.html
fi
#on positionne les drapeaux indiquant la FIN de l'opération, flip pour arrêter l'auto refresh
echo "<? \$flip=1;\$verrou=0;?>" > /usr/share/lcs/Modules/flag.php

# on at la raz de $flip  en cas de pb sur la page autorefresh
at now+1minutes  < /usr/share/lcs/Modules/jobinit.sh

## THE END