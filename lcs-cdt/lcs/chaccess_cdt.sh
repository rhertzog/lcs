#!/bin/bash
#script sudo pour ajouter/enlever le droit d'ecriture 
#$1 : type de droit a appliquer (Writable/NoWritable)
#$2 : fichier concerne (
#$3 : login  
#$4 : nom du repertoire ( uniquement pour creation  nouveau repetoire)
# 03/03/2014

if [ "$1" == "Writable" -o "$1" == "NoWritable" ] && [ "$2" == "conf" -o "$2" == "data" -o "$2" == "creneau" -o "$2" == "vac"  -o "$2" == "jsonfiles"  -o "$2" == "MesDocsCdt" ] ; then
	path_config=/usr/share/lcs/Plugins/Cdt/Includes
	path_json=/usr/share/lcs/Plugins/Cdt/json_files
	if [ "$1" == "Writable" ] ; then
		#autorise modif fichiers config
		if [ "$2" == "conf" ]; then
            		chmod 770 $path_config/config.inc.php
            		#creation fichier temporaire pour sed
            		touch $path_config/config.tmp.php
			chgrp www-data $path_config/config.tmp.php
			chmod 770 $path_config/config.tmp.php
			exit 0
		elif [ "$2" == "data" ]; then 
			chmod 770 $path_config/data.inc.php
			exit 0
		elif [ "$2" == "creneau" ]; then 
			chmod 770 $path_config/creneau.inc.php
			exit 0
		elif [ "$2" == "vac" ]; then 
			chmod 770 $path_config/vac.inc.php
			exit 0
		#modif json
		elif [ "$2" == "jsonfiles" ]; then
             	chmod 770 $path_json/
             	if [ "$3" != "root" -a "$3" != ""  ]; then
			is_valid_login=`ldapsearch -xLLL uid=$3 uid | grep -c "uid=$3,ou=People"`
			if [ -e  $path_json/$3.json -a $is_valid_login = 1 ]; then
			chmod 770 $path_json/$3.json
			fi
		fi
		exit 0
		#modif rep perso
		elif [ "$2" == "MesDocsCdt" -a "$3" != "root" -a "$3" != "" ]; then
			#validite du login prof
			is_valid_login=`ldapsearch -xLLL uid=$3 uid | grep -c "uid=$3,ou=People"`
			is_in_profs=`getent group profs | grep "$3" | wc -l`
			if [ $is_valid_login = 1 -a $is_in_profs = 1 ]; then
				#validite du rep 
				monrep=`echo $4 | sed -e "s/\//_/g"`
				# a minima le login doit etre dans acces.log
				is_in_acces=`cat /var/log/lcs/acces.log | grep -c $3`
				if [ -d /home/$3/public_html/$monrep -a $is_in_acces > 0 ]; then 
				chown $3:lcs-users /home/$3/public_html/$4
                		chmod 770 /home/$3/public_html/$4
				exit 0
				else 
				exit 1
				fi
			else
			exit 1
			fi
		fi
	
	#fin ajout droit ecriture
	#debut retrait droit ecriture
	elif [ "$1" == "NoWritable" ] ; then
		#modif fichiers config
                if [ "$2" == "conf" ]; then
                        chmod 750 $path_config/config.inc.php
                        #suppression fichier temporaire
                        rm -f $path_config/config.tmp.php
						exit 0
		elif [ "$2" == "data" ]; then
                        chmod 750 $path_config/data.inc.php
                        exit 0
                elif [ "$2" == "creneau" ]; then
                        chmod 750 $path_config/creneau.inc.php
                        exit 0
                elif [ "$2" == "vac" ]; then
                        chmod 750 $path_config/vac.inc.php
                        exit 0
                #modif json
                elif [ "$2" == "jsonfiles" ]; then
                        chown -R root:www-data $path_json
			chmod -R 750 $path_json/
                        exit 0
		fi
        fi

else
#Pb params
exit 1
fi
exit 0
