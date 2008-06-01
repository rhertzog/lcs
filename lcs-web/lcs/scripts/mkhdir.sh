#!/bin/bash
# Creation du repertoire de l'utilisateur mkhdir.sh 03/06/2005
# $1 : login
# $2 : group eleves ou profs
# $3 : passwd

# Decodage du mot de passe
PASSWD=`/usr/bin/python /var/www/lcs/includes/decode.py $3 | perl -ne '/(.*)/;@result=split(/\|/,$1);print $result[0];'`

cd /home

# Si le repertoire de l'utilisateur n'existe pas
if [ ! -d $1 ]; then
	mkdir $1
	chown $1:www-data $1
	chmod 770 $1 
	cp -a /etc/skel/* /home/$1/
	chown -R $1:www-data /home/$1/Maildir
	chmod -R 700 /home/$1/Maildir
	chmod 770 /home/$1/public_html
	chmod 660 /home/$1/public_html/index.html
			
	# si eleve
	if [ $2 = "eleves" ]; then
		chown -R root:root /home/$1/public_html
		# Creation bdd eleve
		/usr/share/lcs/sbin/mysqlDbInit.pl $1 $PASSWD $2	
	else
		# si profs ou administratifs
		chown -R $1:www-data /home/$1/public_html
		/usr/share/lcs/sbin/mysqlDbInit.pl $1 $PASSWD $2	
	fi;
else 	
        # Si le repertoire de l'utilisateur existe
        chown $1:www-data $1
	chmod 770 $1	
	if [ ! -d $1/bin ]; then
	        # Creation du rep bin
		cp -a /etc/skel/bin /home/$1/
	fi;
	if [ ! -d $1/usr ]; then
	        # Creation du rep usr
		cp -a /etc/skel/usr /home/$1/
	fi;
	if [ ! -d $1/lib ]; then
	        # Creation du rep lib
		cp -a /etc/skel/lib /home/$1/
	fi;
	if [ ! -d $1/public_html ]; then
	        # Creation du rep public_html
		cp -a /etc/skel/public_html /home/$1/
                chmod 770 /home/$1/public_html
		chmod 660 /home/$1/public_html/index.html
		# si eleve
	        if [ $2 = "eleves" ]; then
		     chown -R root:root /home/$1/public_html
		     # Creation bdd eleve si elle n'existe pas
                     if [ `mysql -s -e "SELECT Db FROM mysql.db WHERE User='$1'" | wc -l` == 0 ]; then
		         /usr/share/lcs/sbin/mysqlDbInit.pl $1 $PASSWD $2
		     fi	
		else
		     # si profs ou administratifs
		     chown -R $1:www-data /home/$1/public_html
		     # Creation bdd prof ou administratif si elle n'existe pas
		     if [ `mysql -s -e "SELECT Db FROM mysql.db WHERE User='$1'" | wc -l` == 0 ]; then
			/usr/sbin/mysqlDbInit.pl $1 $PASSWD $2
		     fi								       
		     /usr/share/lcs/sbin/mysqlDbInit.pl $1 $PASSWD $2	
        	fi;
        fi;
fi;
exit 0
