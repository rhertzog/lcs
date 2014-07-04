#!/bin/bash
# Creation du repertoire de l'utilisateur mkhdir.sh 04/04/2014
# $1 : login
# $2 : Groupe d'appartenance
# $3 : Crypt passwd 

if [ "$#" != "3" ]; then
        echo "ERR : Invalid number of arguments"
        exit 1
fi

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

if [ $2 != "eleves" ] && [ $2 != "profs" ] && [ $2 != "administratifs" ] && [ $2 != "nogroup" ]; then
	echo "ERR : Unknow group."
	exit 1
fi

# get params
WWWPATH="/var/www"
if [ -e $WWWPATH/lcs/includes/config.inc.php ]; then
  dbhost=`cat $WWWPATH/lcs/includes/config.inc.php | grep "HOSTAUTH=" | cut -d = -f 2 |cut -d \" -f 2`
  dbname=`cat $WWWPATH/lcs/includes/config.inc.php | grep "DBAUTH=" | cut -d = -f 2 |cut -d \" -f 2`
  dbuser=`cat $WWWPATH/lcs/includes/config.inc.php | grep "USERAUTH=" | cut -d = -f 2 |cut -d \" -f 2`
  dbpass=`cat $WWWPATH/lcs/includes/config.inc.php | grep "PASSAUTH=" | cut -d = -f 2 |cut -d \" -f 2`
else
  echo "Missing config"
  exit 1
fi

BASEDN=`echo "SELECT value FROM params WHERE name='ldap_base_dn'" | mysql -h $dbhost $dbname -u $dbuser -p$dbpass -N`
if [ -z "$BASEDN" ]; then
        echo "Impossible d'acceder au parametre BASEDN"
        exit 1
fi

PEOPLERDN=`echo "SELECT value FROM params WHERE name='peopleRdn'" | mysql -h $dbhost $dbname -u $dbuser -p$dbpass -N`
if [ -z "$PEOPLERDN" ]; then
        echo "Impossible d'acceder au parametre PEOPLERDN"
        exit 1
fi

# Decrypt passwd
PASSWD=`/usr/bin/python /usr/share/lcs/privatekey/decode.py $3 | perl -ne '/(.*)/;@result=split(/\|/,$1);print $result[0];'`

if [ -z $PASSWD ]; then
        echo "ERR : Empty password"
        exit 1
fi

WHOAMI=`ldapwhoami -x -D "uid=$1,$PEOPLERDN,$BASEDN" -w $PASSWD | grep $1 | wc -l`
if [ $WHOAMI = 0 ]; then
	echo "ERR : Invalid passwd."
	exit 1
fi

# Verify if this is the good group 
if [ $2 = "eleves" ] || [ $2 = "profs" ] || [ $2 = "administratifs" ]; then
	ISGROUP=`getent group $2 | grep $1 | wc -l`
	if [ $ISGROUP = 0 ]; then
		echo "ERR invalid group $2 for $1"
		exit 1
	fi
elif [ $2 = "nogroup" ] && [ $1 != "admin" ]; then
	echo "ERR invalid group $2 for $1"
	exit 1
fi 

GRP=$2
cd /home

# Si le repertoire de l'utilisateur n'existe pas
if [ ! -d $1 ]; then
	mkdir $1
	chown root:lcs-users $1
	chmod 750 $1 
	cp -r /etc/skel/* /home/$1/
	chown -R $1:lcs-users /home/$1/Maildir
	chmod -R 700 /home/$1/Maildir
	chown $1:lcs-users /home/$1/Documents
	chmod 770 /home/$1/Documents

	# Fixation droits sur repertoire Profile si il existe
	if [ -d $1/Profile ]; then
		chown www-data:lcs-users /home/$1/Profile
        chmod 750 /home/$1/Profile
	fi

        chown -R $1:lcs-users /home/$1/public_html
        chmod 770 /home/$1/public_html
        chmod 664 /home/$1/public_html/index.html	
	# si eleve
	if [ $GRP = "eleves" ]; then
		chown -R root:root /home/$1/public_html
		chmod 755 /home/$1/public_html
		# Creation bdd eleve
		/usr/share/lcs/sbin/mysqlDbInit.pl $1 $PASSWD $GRP	
	else
		# si profs ou administratifs
		/usr/share/lcs/sbin/mysqlDbInit.pl $1 $PASSWD $GRP	
	fi
else 	
	# Si le repertoire de l'utilisateur existe
	chown root:lcs-users $1
	chmod 750 $1	
	if [ ! -d $1/public_html ]; then
		# Recreation du rep public_html
		cp -r /etc/skel/public_html /home/$1/
		chown -R $1:lcs-users /home/$1/public_html
		chmod 770 /home/$1/public_html
		chmod 664 /home/$1/public_html/index.html
		# si eleve
		if [ $GRP = "eleves" ]; then
		    chown -R root:root /home/$1/public_html
			chmod 755 /home/$1/public_html
		    # Creation bdd eleve si elle n'existe pas
			if [ `mysql -s -e "SELECT Db FROM mysql.db WHERE User='$1'" | wc -l` == 0 ]; then
				/usr/share/lcs/sbin/mysqlDbInit.pl $1 $PASSWD $GRP
		    fi	
		else
		    # si profs ou administratifs
		    # Creation bdd prof ou administratif si elle n'existe pas
		    if [ `mysql -s -e "SELECT Db FROM mysql.db WHERE User='$1'" | wc -l` == 0 ]; then
                        /usr/share/lcs/sbin/mysqlDbInit.pl $1 $PASSWD $GRP
		    fi	
        fi
    fi
	if [ ! -d $1/Documents ]; then
		# Recreation du rep Documents
		mkdir  /home/$1/Documents
		chown $1:lcs-users /home/$1/Documents
		chmod 770 /home/$1/Documents
	fi
	if [ ! -d $1/Maildir ]; then
		# Recreation Maildir
		cp -r /etc/skel/Maildir /home/$1/
		chown -R $1:lcs-users /home/$1/Maildir
		chmod -R 700 /home/$1/Maildir
	fi
	if [ ! -d $1/Profile ]; then
		mkdir /home/$1/Profile
		chown www-data:lcs-users /home/$1/Profile
        chmod 750 /home/$1/Profile
	fi
fi
exit 0
