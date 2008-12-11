#!/bin/bash
# squidGuard.sh version du 28/03/2008
ARG="$@"

case $ARG in
   se3_internet)
		echo "Mise en place du fichier squidguard.conf pour se3-internet"
		# Acquisition de ipse3 dans lcs_db.params
        	IPSE3=`mysql -se "SELECT value FROM lcs_db.params WHERE name='se3Ip';"`
		# Acquisition du basedn
		BASEDN=`mysql -se "SELECT value FROM lcs_db.params WHERE name='ldap_base_dn';"`
		# Acquisition du server_ldap
		LDAP_SERVER=`mysql -se "SELECT value FROM lcs_db.params WHERE name='ldap_server';"`
		#Acquisition ip du LCS
		iplan=`ifconfig eth0|grep inet | cut -f2 -d\:| cut -f1 -d\ `
		# Remplacement des parametres generiques dans le fichier de conf		
		cp /etc/lcs/squidguard/squidGuard.conf.internet  /etc/squid/squidGuard.tmp
		sed -i 's/#IPSE3#/'"$IPSE3"'/g' /etc/squid/squidGuard.tmp
		sed -i 's/#ServerLdap#/'"$LDAP_SERVER"'/g' /etc/squid/squidGuard.tmp
		sed -i 's/#basedn#/'"$BASEDN"'/g' /etc/squid/squidGuard.tmp
		sed -i 's/#IPLCS#/'"$iplan"'/g' /etc/squid/squidGuard.tmp
		mv /etc/squid/squidGuard.conf  /etc/squid/squidGuard.lcs
		mv /etc/squid/squidGuard.tmp  /etc/squid/squidGuard.conf
		#mise en place des listes blanches
		cp -a /etc/lcs/squidguard/lcs_wl /var/lib/squidguard/db/whitelists/lcs
		/usr/bin/squidGuard -C whitelists/lcs/domains
       		/usr/bin/squidGuard -C whitelists/lcs/urls
		chown proxy.www-data /var/lib/squidguard/db/whitelists -R
        	chmod g+x /var/lib/squidguard/db/whitelists
       		chmod g+x /var/lib/squidguard/db/whitelists/*
       		chmod g+w /var/lib/squidguard/db/whitelists -R
       		squid -k reconfigure
	   	# Inscription dans la base lcs_db.params
	  	 /usr/bin/mysql -e "INSERT INTO lcs_db.params (id, name, value, srv_id, descr, cat) VALUES ('', 'se3-internet', '1', '0', 'Type de conf squidGuard', '0');"			
   ;;
   lcs)
       echo "Modification base lcs a partir des fichiers diff"
       /usr/bin/squidGuard -u
   ;;
   lcs_db)
       echo "Reconstruction base lcs urls.db et domains.db"
       /usr/bin/squidGuard -C blacklists/lcs/domains
       /usr/bin/squidGuard -C blacklists/lcs/urls
       chown proxy:www-data /var/lib/squidguard/db/blacklists/lcs/*
       chmod 664 /var/lib/squidguard/db/blacklists/lcs/*
   ;;
   raz_db)
      echo "RAZ urls, domains urls.db domains.db lcs"
      rm /var/lib/squidguard/db/blacklists/lcs/urls
      rm /var/lib/squidguard/db/blacklists/lcs/domains
      echo "# base urls lcs" > /var/lib/squidguard/db/blacklists/lcs/urls
      echo "# base domains lcs" > /var/lib/squidguard/db/blacklists/lcs/domains
      /usr/bin/squidGuard -C blacklists/lcs/urls
      /usr/bin/squidGuard -C blacklists/lcs/domains
      chown proxy:www-data /var/lib/squidguard/db/blacklists/lcs/*
      chmod 664 /var/lib/squidguard/db/blacklists/lcs/*
   ;;
   bl_lcs)
       echo "Liste noire sur Proxy acad�mique + liste noire LCS sur le LCS"
       cat /etc/squid/squidGuard.conf | sed -e "s/!ads !aggressive !audio-video !drugs !gambling !hacking !porn !violence !warez any/any/g" > /etc/squid/squidGuard.conf.tmp
       mv /etc/squid/squidGuard.conf.tmp /etc/squid/squidGuard.conf
   ;;
   bl_full)
       echo "Listes noires LCS + Nationale sur Proxy LCS"
       RES=`grep '!ads' /etc/squid/squidGuard.conf`
       if [ "x$RES" = "x" ]; then      
         cat /etc/squid/squidGuard.conf | sed -e "s/any/!ads !aggressive !audio-video !drugs !gambling !hacking !porn !violence !warez any/g" > /etc/squid/squidGuard.conf.tmp
         mv /etc/squid/squidGuard.conf.tmp /etc/squid/squidGuard.conf
       fi		     
   ;;
   webmailOn)
       echo "Liste noire webmail valid�e"
       RES=`grep '!webmail' /etc/squid/squidGuard.conf`
       if [ "x$RES" = "x" ]; then
         cat /etc/squid/squidGuard.conf | sed -e "s/pass /pass !webmail /g" > /etc/squid/squidGuard.conf.tmp
         mv /etc/squid/squidGuard.conf.tmp /etc/squid/squidGuard.conf
       fi	      
   ;;
   webmailOff)
       echo "Liste noire webmail d�valid�e"
       cat /etc/squid/squidGuard.conf | sed -e "s/!webmail //g" > /etc/squid/squidGuard.conf.tmp
       mv /etc/squid/squidGuard.conf.tmp /etc/squid/squidGuard.conf
   ;;
   forumsOn)
       echo "Liste noire forums valid�e"
       RES=`grep '!forums' /etc/squid/squidGuard.conf`
       if [ "x$RES" = "x" ]; then  
         cat /etc/squid/squidGuard.conf | sed -e "s/pass /pass !forums /g" > /etc/squid/squidGuard.conf.tmp
         mv /etc/squid/squidGuard.conf.tmp /etc/squid/squidGuard.conf
       fi	 
   ;;
   forumsOff)
       echo "Liste noire forums d�valid�e"
       cat /etc/squid/squidGuard.conf | sed -e "s/!forums //g" > /etc/squid/squidGuard.conf.tmp
       mv /etc/squid/squidGuard.conf.tmp /etc/squid/squidGuard.conf
   ;;
   reload)
       /etc/init.d/squid reload
   ;;
   status)
   	RES=`grep '!webmail' /etc/squid/squidGuard.conf`
	if [ "x$RES" = "x" ]; then
	  RET="webmailOff"
	else
	  RET="webmailOn"
	fi    
    	RES=`grep '!forums' /etc/squid/squidGuard.conf`
	if [ "x$RES" = "x" ]; then
	  RET="$RET forumsOff"
	else
	  RET="$RET forumsOn"
	fi       
   	RES=`grep '!ads' /etc/squid/squidGuard.conf`
	if [ "x$RES" = "x" ]; then
	  echo "$RET bl_lcs"
	else
	  echo "$RET bl_full"
	fi    
   ;;
   help)
   	# Aide en ligne
	echo "$0 option";
	echo "lcs : Reconstruction des bases de donnees squidGuard a partir des nouveaux fichiers diff"
	echo "lcs_db : Reconstruction des bases de donnees squidGuard LCS a partir des fichiers lcs/domains et lcs/urls"
	echo "raz_db : Efface les bases de donnees squidGurad LCS"
	echo "bl_lcs : Liste noire sur Proxy academique + liste noire LCS sur le LCS"
	echo "bl_full : Listes noires totalement gerees sur le LCS"
	echo "webmailOn : Filtrage des webmail via la liste noire webmail"
	echo "webmailOff : Pas de filtage des webmail"
	echo "forumsOn : Filtrage des forums via la liste noire forums"
	echo "forumsOff : Pas de filtage des forums"        
	echo "reload : Recharge la configuration squidGuard"
	echo "status: Retourne le status des configurations squidGuard"
	echo "se3_internet : Met en place le fichier squidGuard.conf adapt� au dispositif se3-internet"
   ;;
   *)
       echo "usage: $0 (lcs|lcs_db|raz_db|bl_lcs|bl_full|webmailOn|webmailOff|forumsOn|forumsOff|reload|status|help)"
   ;;
esac