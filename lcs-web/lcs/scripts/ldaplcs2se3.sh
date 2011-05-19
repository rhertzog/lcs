#!/bin/bash
# Connection d'un LCS sur l'annuaire ldap SE3
# Olivier lecluse, Jean-Luc Chretien, Franck Molle
# 19/05/2011

# recuperation des params bdd

WWWPATH="/var/www"
if [ -e $WWWPATH/lcs/includes/config.inc.php ]; then
  dbhost=`cat $WWWPATH/lcs/includes/config.inc.php | grep "HOSTAUTH=" | cut -d = -f 2 |cut -d \" -f 2`
  dbname=`cat $WWWPATH/lcs/includes/config.inc.php | grep "DBAUTH=" | cut -d = -f 2 |cut -d \" -f 2`
  dbuser=`cat $WWWPATH/lcs/includes/config.inc.php | grep "USERAUTH=" | cut -d = -f 2 |cut -d \" -f 2`
  dbpass=`cat $WWWPATH/lcs/includes/config.inc.php | grep "PASSAUTH=" | cut -d = -f 2 |cut -d \" -f 2`
else
  echo "Fichier de conf inaccessible"
  exit 1
fi

# Nettoyage preliminaire

if [ -e /tmp/test.ldif ]; then
        rm /tmp/test.ldif
fi
if [ -e /tmp/addse3.ldif ]; then
        rm /tmp/addse3.ldif
fi

# Recuperation des params ldap

clear
echo "Ce script va déporter l'annuaire du Lcs sur le SE3"
echo "Il est important que l'annuaire du Lcs ait ete parametre en concordance avec celui du SE3 (meme baseDN, meme pass..."
echo ""
echo "Entrez l'adresse du SE3"
read LDAPIP

OLDLDAPIP=`echo "SELECT value FROM params WHERE name='ldap_server'" | mysql -h $dbhost $dbname -u $dbuser -p$dbpass -N`
if [ -z "$OLDLDAPIP" ]; then
        echo "Impossible d'accéder au paramètre LDAPIP"
        exit 1
fi
BASEDN=`echo "SELECT value FROM params WHERE name='ldap_base_dn'" | mysql -h $dbhost $dbname -u $dbuser -p$dbpass -N`
if [ -z "$BASEDN" ]; then
        echo "Impossible d'accéder au paramètre BASEDN"
        exit 1
fi
ADMINRDN=`echo "SELECT value FROM params WHERE name='adminRdn'" | mysql -h $dbhost $dbname -u $dbuser -p$dbpass -N`
if [ -z "$ADMINRDN" ]; then
        echo "Impossible d'accéder au paramètre ADMINRDN"
        exit 1
fi
ADMINPW=`echo "SELECT value FROM params WHERE name='adminPw'" | mysql -h $dbhost $dbname -u $dbuser -p$dbpass -N`
if [ -z "$ADMINPW" ]; then
        echo "Impossible d'accéder au paramètre ADMINPW"
        exit 1
fi
PEOPLERDN=`echo "SELECT value FROM params WHERE name='peopleRdn'" | mysql -h $dbhost $dbname -u $dbuser -p$dbpass -N`
if [ -z "$PEOPLERDN" ]; then
        echo "Impossible d'accéder au paramètre PEOPLERDN"
        exit 1
fi
GROUPSRDN=`echo "SELECT value FROM params WHERE name='groupsRdn'" | mysql -h $dbhost $dbname -u $dbuser -p$dbpass -N`
if [ -z "$GROUPSRDN" ]; then
        echo "Impossible d'accéder au paramètre GROUPSRDN"
        exit 1
fi
RIGHTSRDN=`echo "SELECT value FROM params WHERE name='rightsRdn'" | mysql -h $dbhost $dbname -u $dbuser -p$dbpass -N`
if [ -z "$RIGHTSRDN" ]; then
        echo "Impossible d'accéder au paramètre RIGHTSRDN"
        exit 1
fi
DOMAIN=`echo "SELECT value FROM params WHERE name='domain'" | mysql -h $dbhost $dbname -u $dbuser -p$dbpass -N`
if [ -z "$DOMAIN" ]; then
        echo "Impossible d'accéder au paramètre DOMAIN"
        exit 1
fi

PEOPLER=`echo $PEOPLERDN |cut -d = -f 2`
RIGHTSR=`echo $RIGHTSRDN |cut -d = -f 2`
GROUPSR=`echo $GROUPSRDN |cut -d = -f 2`

ldapsearch -xLLL -h $LDAPIP -D $ADMINRDN,$BASEDN -w $ADMINPW uid=admin uid> /tmp/test.ldif || ERR=1

if [ "$ERR" = "1" ]; then
        echo "Erreur de connexion a l'annuaire de SE3"
        exit 1
fi

# Sauvegarde branche rights

ldapsearch -xLLL -h 127.0.0.1 -b $RIGHTSRDN,$BASEDN "cn=*" > /tmp/addse3.ldif


echo "dn: uid=webmaster.etab,$PEOPLERDN,$BASEDN" >> /tmp/addse3.ldif
ldapsearch -xLLL -h 127.0.0.1 -D $ADMINRDN,$BASEDN -w $ADMINPW "uid=webmaster.etab" |grep -v uidNumber | grep -v "^dn: " | grep ":" >> /tmp/addse3.ldif

getent passwd 598 || OKW=1
if [ "$OKW" = "1" ]; then
        echo "uidNumber: 598" >> /tmp/addse3.ldif
else
        echo "Entrer un uidNumber libre pour webmaster.etab"
        read uidn
        echo "uidNumber: $uidn" >> /tmp/addse3.ldif
fi

ldapadd -x -c -h $LDAPIP -D $ADMINRDN,$BASEDN -w $ADMINPW -f /tmp/addse3.ldif

# modif de la conf postfix
echo "server_host = $LDAPIP">/etc/postfix/ldap-aliases.cf
echo "search_base = $BASEDN">>/etc/postfix/ldap-aliases.cf
echo "query_filter = (mail=%s@$DOMAIN)">>/etc/postfix/ldap-aliases.cf
echo "result_attribute = uid">>/etc/postfix/ldap-aliases.cf
chmod 644 /etc/postfix/ldap-aliases.cf

# Modif de la conf Lcs pour pointer sur SE3
echo "ldap_server $OLDLDAPIP $LDAPIP">/tmp/params_lcs
/usr/share/lcs/scripts/edit_params.sh

# Modif du param ldap_server dans la BDD
echo "UPDATE params SET value=\"$LDAPIP\" WHERE name='ldap_server'" | mysql -h $dbhost $dbname -u $dbuser -p$dbpass -N

# Application des droits
if [ -d /home/admin ]; then
	chown -R admin /home/admin
fi
chown -R webmaster.etab /home/webmaster.etab/public_html/
chgrp -R lcs-users /home/*
/usr/share/lcs/sbin/groupAddUser.pl www-data lcs-users
/etc/init.d/apache2 restart
  