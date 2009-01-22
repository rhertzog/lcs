#!/bin/bash
# Creation du repertoire de l'utilisateur mkhdir.sh 22/01/2009
# $1 : login
# $2 : group eleves ou profs
# $3 : passwd

# Decodage du mot de passe
PASSWD=`/usr/bin/python /var/www/lcs/includes/decode.py $3 | perl -ne '/(.*)/;@result=split(/\|/,$1);print $result[0];'`

cd /home

# Si le repertoire de l'utilisateur n'existe pas
if [ ! -d $1 ]; then
	mkdir $1
	chown root:lcs-users $1
	chmod 750 $1 
	cp -r /etc/skel/* /home/$1/
	chown -R $1:www-data /home/$1/Maildir
	chmod -R 700 /home/$1/Maildir

        chown $1:lcs-users /home/$1/Documents
        chmod 770 /home/$1/Documents

        chown -R $1:lcs-users /home/$1/public_html
        chmod 770 /home/$1/public_html
        chmod 664 /home/$1/public_html/index.html	
	# si eleve
	if [ $2 = "eleves" ]; then
		chown -R root:root /home/$1/public_html
                chmod 755 /home/$1/public_html
		# Creation bdd eleve
		/usr/share/lcs/sbin/mysqlDbInit.pl $1 $PASSWD $2	
	else
		# si profs ou administratifs
		/usr/share/lcs/sbin/mysqlDbInit.pl $1 $PASSWD $2	
	fi
else 	
        # Si le repertoire de l'utilisateur existe
        chown root:lcs-users $1
	chmod 750 $1	
	if [ ! -d $1/public_html ]; then
	        # ReCreation du rep public_html
		cp -r /etc/skel/public_html /home/$1/
                chown -R $1:lcs-users /home/$1/public_html
                chmod 770 /home/$1/public_html
                chmod 664 /home/$1/public_html/index.html
		# si eleve
	        if [ $2 = "eleves" ]; then
		    chown -R root:root /home/$1/public_html
                    chmod 755 /home/$1/public_html
		    # Creation bdd eleve si elle n'existe pas
                    if [ `mysql -s -e "SELECT Db FROM mysql.db WHERE User='$1'" | wc -l` == 0 ]; then
                        /usr/share/lcs/sbin/mysqlDbInit.pl $1 $PASSWD $2
		    fi	
		else
		    # si profs ou administratifs
		    # Creation bdd prof ou administratif si elle n'existe pas
		    if [ `mysql -s -e "SELECT Db FROM mysql.db WHERE User='$1'" | wc -l` == 0 ]; then
                        /usr/share/lcs/sbin/mysqlDbInit.pl $1 $PASSWD $2
		    fi	
        	fi
        fi
	if [ ! -d $1/Documents ]; then
            # Recreation du rep Documents
            mkdir  /home/$1/Documents
            chown $1:lcs-users /home/$1/Documents
            chmod 770 /home/$1/Documents
        fi
        if [ ! -d $1/Maidir ]; then
            cp -r /etc/skel/Maildir /home/$1/
            chown -R $1:www-data /home/$1/Maildir
	    chmod -R 700 /home/$1/Maildir
        fi
fi
exit 0
