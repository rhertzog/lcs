#!/bin/bash
# action.sh ordonnancement d'une action sur le serveur LcSe3
# Version du 27/02/2014

#$1 action : halt, reboot, settime, update, synchro_mdp
#$2 crypt passwd for synchro_mdp

# get params
WWWPATH="/var/www"
if [ -e $WWWPATH/lcs/includes/config.inc.php ] && [ $1 = "settime" -o $1 = "synchro_mdp" ]; then
  		dbhost=`cat $WWWPATH/lcs/includes/config.inc.php | grep "HOSTAUTH=" | cut -d = -f 2 |cut -d \" -f 2`
  		dbname=`cat $WWWPATH/lcs/includes/config.inc.php | grep "DBAUTH=" | cut -d = -f 2 |cut -d \" -f 2`
  		dbuser=`cat $WWWPATH/lcs/includes/config.inc.php | grep "USERAUTH=" | cut -d = -f 2 |cut -d \" -f 2`
  		dbpass=`cat $WWWPATH/lcs/includes/config.inc.php | grep "PASSAUTH=" | cut -d = -f 2 |cut -d \" -f 2`
else
	echo "Missing config"
  	exit 1
fi
	
case $1 in
  halt)
    /sbin/shutdown -h now &
    exit 0;;
  reboot)
    /sbin/shutdown -r now &   
    exit 0;;  
  settime)
	NTPSER=`echo "select value from lcs_db.params where name='ntpserv'" | mysql -h $dbhost $dbname -u $dbuser -p$dbpass -N`
    /usr/sbin/ntpdate -s "$NTPSER" &
    exit 0;;  
  update)
    apt-get update
    apt-get -y dist-upgrade &
    exit 0;;  
  synchro_mdp)
    KEYPRIV=`grep key_priv /var/www/lcs/includes/private_key.inc.php | cut -d '"' -f 2`
    PASSWD=`echo "$2" | openssl aes-256-cbc -a -d -salt -pass pass:"$KEYPRIV"`
    # Verify passwd validity    
    if [ -z $PASSWD ]; then
        echo "ERR: Empty password."
        exit 1
    fi
	 BASEDN=`echo "SELECT value FROM params WHERE name='ldap_base_dn'" | mysql -h $dbhost $dbname -u $dbuser -p$dbpass -N`
	 if [ -z "$BASEDN" ]; then
        echo "Impossible d'acceder au parametre BASEDN."
        exit 1
	 fi
	 PEOPLERDN=`echo "SELECT value FROM params WHERE name='peopleRdn'" | mysql -h $dbhost $dbname -u $dbuser -p$dbpass -N`
	 if [ -z "$PEOPLERDN" ]; then
        echo "Impossible d'acceder au parametre PEOPLERDN."
        exit 1
	 fi    
	 WHOAMI=`ldapwhoami -x -D "uid=admin,$PEOPLERDN,$BASEDN" -w $PASSWD | grep admin | wc -l`
	 if [ $WHOAMI = 0 ]; then
		echo "ERR : Invalid passwd."
		exit 1
	 fi
    # Change passwd for setup page
    /usr/bin/htpasswd  -bc /var/www/setup/.htpasswd admin "$PASSWD"
    chown root:www-data /var/www/setup/.htpasswd
    chmod 640 /var/www/setup/.htpasswd  
    exit 0;;  
  *)
	echo "ERR : Command unknow !"
  	exit 1;;
esac
