#!/bin/bash
# moulinage des groupOfnames en posix
# Olivier lecluse & Jean-Luc Chretien
# 20/09/2007

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

# Recuperation des params ldap

LDAPIP=`echo "SELECT value FROM params WHERE name='ldap_server'" | mysql -h $dbhost $dbname -u $dbuser -p$dbpass -N`
if [ -z "$LDAPIP" ]; then
        echo "Impossible d'accéder au paramètre BASEDN"
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
PARCSRDN=`echo "SELECT value FROM params WHERE name='parcsRdn'" | mysql -h $dbhost $dbname -u $dbuser -p$dbpass -N`
PARCSR=`echo $PARCSRDN |cut -d = -f 2`
if [ -z "$PARCSRDN" ]; then
        PARCSR="Parcs"
        echo "Impossible d'accéder au paramètre PARCSRDN"
        echo "Vous pouvez répondre oui (O) dans le cas ou l'annuaire est exploité par un LCS sans couplage avec un Se3"
        echo "Ou si l'annuaire est exploitée et hébergé sur un se3 également oui (O) si le RDN de Parcs est ou=Parcs"
        echo "Voulez-vous continuer ? (O/n)"
        read REPONSE
        case $REPONSE in
            n)
                echo "Pas de migration groupOfnames en posixGroup !"
                exit 1;;
            O)
                ;;
            *) 
                echo "Erreur, vous deviez répondre par oui (O) ou par non (n)."
                echo "Pas de migration groupOfnames en posixGroup !"
                exit 1;;
        esac
fi

PEOPLER=`echo $PEOPLERDN |cut -d = -f 2`
RIGHTSR=`echo $RIGHTSRDN |cut -d = -f 2`
GROUPSR=`echo $GROUPSRDN |cut -d = -f 2`


# Sauvegarde de l'annuaire avant moulinage

ldapsearch -xLLL -h $LDAPIP -D $ADMINRDN,$BASEDN -w $ADMINPW objectClass=* > gonbeforeposix.ldif

# Moulinage des gon dans un fichier ldif

echo "">/tmp/addposix.ldif
GIDN=`ldapsearch -xLLL objectClass=posixGroup gidNumber | grep gidNumber | cut -d" " -f 2 | sort -n | tail -n 10 | head -n 1`

ldapsearch -xLLL -h $LDAPIP objectClass=groupOfNames dn | grep dn | grep -v $RIGHTSR | grep -v $PARCSR | cut -c 5- | while read GDN; do
	GRDN=`echo $GDN | cut -d"," -f1`
	ldapsearch -xLLL -h $LDAPIP $GRDN |sed -e "s/member: uid=/memberUid: /g" | grep -v "member:" | sed -e "s/groupOfNames/posixGroup/g" | sed -e "s/,$PEOPLERDN,$BASEDN//g" |grep ":" >> /tmp/addposix.ldif
	# recherche d'un gidNumber libre...
	while getent group $GIDN; do
		let GIDN+=1
	done

	echo "gidNumber: $GIDN">>/tmp/addposix.ldif
	echo "">>/tmp/addposix.ldif
	echo "=======> groupe : $GRDN ($GIDN)"
	let GIDN+=1
	ldapdelete -x -h $LDAPIP -D $ADMINRDN,$BASEDN -w $ADMINPW $GDN
done

# Integration des groupes posix

ldapadd -x -c -h $LDAPIP -D $ADMINRDN,$BASEDN -w $ADMINPW -f /tmp/addposix.ldif

