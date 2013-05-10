#!/bin/sh

##### Met en place la replication LDAP avec syncrepl #####
##### LCS/SE3 derniere modification : 18/04/2013
	if [ "$1" = "--help" -o "$1" = "-h" ]
then
	echo "Met en place la replication LDAP (syncrepl)a partir des donnees de la base sql"
	echo "Usage : -r replace l'annuaire en annuaire local sans replication"
	echo "-h Cette aide"
	exit
fi

#LCS
bdd="lcs_db"
svr="lcs"
svr_name="LCS"
dossier_svg="/root/save/sauvegarde_ldap_avant_replica"
pathsbin="/usr/share/lcs/sbin"

mkdir -p /var/backups/ldap/

if [ -e /var/lock/syncrepl.lock ]
then
	echo "lock trouve"
	logger -t "SLAPD" "Lock syncrepl.lock existant"
	exit 1
fi	

## recuperation des variables necessaires pour interoger mysql ###
if [ -e /root/.my.cnf ]; then
	. /root/.my.cnf 2>/dev/null
else
        echo "Fichier de conf inaccessible desole !!"
        echo "le script ne peut se poursuivre"
        exit 1
fi

# Permettre un retour sur l'annuaire local
if [ "$1" = "-r" ]
then
  /usr/bin/mysql -u $user -p$password -D $bdd -e "UPDATE params set value='' WHERE name='replica_ip'"
  /usr/bin/mysql -u $user -p$password -D $bdd -e "UPDATE params set value='0' WHERE name='replica_status'"
  /usr/bin/mysql -u $user -p$password -D $bdd -e "UPDATE params set value='127.0.0.1' WHERE name='ldap_server'"
  echo "Annuaire replace en mode annuaire local"
fi

#########################################################################################
# 	Recup et verif  des donnees dans la base SQL					#
#########################################################################################
replica_status=`/usr/bin/mysql -u $user -p$password -D $bdd -e "SELECT value from params WHERE name='replica_status'" | grep -v value`
replica_ip=`/usr/bin/mysql -u $user -p$password -D $bdd -e "SELECT value from params WHERE name='replica_ip'" | grep -v value`
ldap_base_dn=`/usr/bin/mysql -u $user -p$password -D $bdd -e "SELECT value from params WHERE name='ldap_base_dn'" | grep -v value`
ldap_server=`/usr/bin/mysql -u $user -p$password -D $bdd -e "SELECT value from params WHERE name='ldap_server'" | grep -v value`
ldap_admin=`/usr/bin/mysql -u $user -p$password -D $bdd -e "SELECT value from params WHERE name='adminRdn'" | grep -v value`
ldap_adminPw=`/usr/bin/mysql -u $user -p$password -D $bdd -e "SELECT value from params WHERE name='adminPw'" | grep -v value`

# Verification des variables
if [ "$ldap_server" = "" -o "$ldap_admin" = "" ]
then
	echo "Impossible de connaitre la base dn et/ou l'admin"
	echo "le script ne peut se poursuivre"
	exit 1
fi
if [ "$replica_status" = "" ]
then
	# Si pas de valeur on le place en standalone
	replica_status=0
fi	
if [ "$ldap_server" = "" ]
then
	ldap_server="127.0.0.1"
fi
if [ "$replica_status" = "1" -o "$replica_status" = "3" -o "$replica_status" = "0" ]
then
     LDAP_LOCAL="$ldap_server"
else
     LDAP_LOCAL="$replica_ip"
fi
if [ "$LDAP_LOCAL" = "" ]
then
     LDAP_LOCAL="127.0.0.1"
fi
		  

# lock
touch /var/lock/syncrepl.lock

# On stoppe ldap
/etc/init.d/slapd stop
sleep 2

# On sauvegarde LDAP
DATE="$(date +%d%m%Y)"
SAUV_LDAP=ldap_$DATE.ldif
/usr/sbin/slapcat > /var/backups/ldap/$SAUV_LDAP

# On sauvegarde DB_CONFIG
if [ -e "/var/lib/ldap/DB_CONFIG" ]
then
  cp /var/lib/ldap/DB_CONFIG /var/backups/ldap/
else
  cp /var/backups/ldap/DB_CONFIG /var/lib/ldap/
fi

#################################################################################
# 	On supprime l'existant							#
#################################################################################
# On vire le repertoire des logs de slurpd
if [ \( -d "/var/spool/slurpd/replica" \) ]
then
	rm -Rf /var/spool/slurpd/replica
fi	


# On vire syncrepl.conf
if [ -e "/etc/ldap/syncrepl.conf" ]
then
	rm -f /etc/ldap/syncrepl.conf
fi	


#################################################################################
#   Fichier de conf de slapd.conf						#
#################################################################################

# On crypte le mot de passe
ldap_passwd=`cat /etc/ldap.secret`
# verifie la concordence avec la base SQL
if [ "$ldap_passwd" != "$ldap_adminPw" ]
then
	# Implique un changement de mot de passe, on change donc celui de ldap.secret
	echo "$ldap_adminPw" > /etc/ldap.secret
	chmod 400 /etc/ldap.secret
fi	
crypted_ldap_passwd=`/usr/sbin/slappasswd -h {MD5} -s $ldap_adminPw`

# TLS
echo "
[ req ]
distinguished_name =  req_distinguished_name
prompt = no

[ req_distinguished_name ]
OU = $svr_name
CN = $LDAP_LOCAL
" > /etc/ldap/config.$svr

PEM1=`/bin/mktemp /tmp/openssl.XXXXXX`
PEM2=`/bin/mktemp /tmp/openssl.XXXXXX`


/usr/bin/openssl req -config /etc/ldap/config.$svr -newkey rsa:1024 -keyout $PEM1 -nodes -x509 -days 3650 -out $PEM2 >/dev/null 2>/dev/null
cat $PEM1 >  /etc/ldap/slapd.pem
echo ""    >> /etc/ldap/slapd.pem
cat $PEM2 >> /etc/ldap/slapd.pem
/bin/rm -f $PEM1 $PEM2

# Fichier slapd.conf
echo "# This is the main ldapd configuration file. See slapd.conf(5) for more
# info on the configuration options.
# Cree pour $nom_svr par mkSlapdConf.sh

# Schema and objectClass definitions
include         /etc/ldap/schema/core.schema
include         /etc/ldap/schema/cosine.schema
include         /etc/ldap/schema/nis.schema
include         /etc/ldap/schema/inetorgperson.schema
include         /etc/ldap/schema/ltsp.schema
include         /etc/ldap/schema/samba.schema
include         /etc/ldap/schema/printer.schema" > /etc/ldap/slapd.conf

if [ -e "/etc/ldap/schema/RADIUS-LDAPv3.schema" ]
then
echo "include         /etc/ldap/schema/RADIUS-LDAPv3.schema" >> /etc/ldap/slapd.conf
fi 

#if [ -e "/etc/ldap/schema/apple.schema" ]
#then
#echo "include         /etc/ldap/schema/apple.schema" >> /etc/ldap/slapd.conf
#fi 

echo "TLSCACertificatePath /etc/ldap/
TLSCertificateFile /etc/ldap/slapd.pem
TLSCertificateKeyFile /etc/ldap/slapd.pem

allow bind_v2

# Where clients are refered to if no
# match is found locally
#referral	ldap://some.other.ldap.server

# Where the pid file is put. The init.d script
# will not stop the server if you change this.
pidfile		/var/run/slapd/slapd.pid

# List of arguments that were passed to the server
argsfile	/var/run/slapd/slapd.args

# Read slapd.conf(5) for possible values
loglevel	0

# Where the dynamically loaded modules are stored
modulepath	/usr/lib/ldap
moduleload	back_bdb

#######################################################################
# Specific Backend Directives for bdb:
# Backend specific directives apply to this backend until another
# 'backend' directive occurs
backend		bdb
# Specific Directives for database #1, of type bdb:
# Database specific directives apply to this databasse until another
# 'database' directive occurs
database        bdb

# The base of your directory
suffix		\"$ldap_base_dn\"
rootdn		\"$ldap_admin,$ldap_base_dn\"
rootpw		$crypted_ldap_passwd
# Where the database file are physically stored
directory	\"/var/lib/ldap\"

checkpoint 512 30

index      objectClass,uidNumber,gidNumber,uniqueMember,member  eq
index      cn,sn,uid,displayName,l                              pres,sub,eq
index      memberUid,mail,givenname                             eq,subinitial
index      sambaSID,sambaPrimaryGroupSID,sambaDomainName        eq
index      sambaSIDList,sambaGroupType                          eq
index      entryCSN,entryUUID                                   eq
index      default                                              sub,eq

# Save the time that the entry gets modified
lastmod on

# For Netscape Roaming support, each user gets a roaming
# profile for which they have write access to
#access to dn=\".*,ou=Roaming,@SUFFIX@\"
#	by dnattr=owner write

# The userPassword by default can be changed
# by the entry owning it if they are authenticated.
# Others should not be able to see it, except the
# admin entry below
access to attrs=userPassword
	by anonymous auth
	by self write
	by * none

# ACLs proposees par Bruno Bzeznic
access to attrs=userpassword
	by self write
	by users none
	by anonymous auth

access to attrs=sambaLmPassword
	by self write
	by users none
	by anonymous auth

access to attrs=sambaNtPassword
	by self write
	by users none
	by anonymous auth
	
access to attrs=printer-uri
        by self write
        by users none
        by anonymous auth


# The admin dn has full write access
access to *
	by * read

# out put of this database using slapcat(8C), and then importing that into
#
#	credentials=\"XXXXXX\"

# End of ldapd configuration file
sizelimit	3500
" >> /etc/ldap/slapd.conf

#################################################################################
# Cree le fichier /etc/default/slapd						#
#################################################################################

echo "# Default location of the slapd.conf file or slapd.d cn=config directory. If
# empty, use the compiled-in default (/etc/ldap/slapd.d with a fallback to
# /etc/ldap/slapd.conf).

SLAPD_CONF=\"/etc/ldap/slapd.conf\"

# System account to run the slapd server under. If empty the server
# will run as root.
SLAPD_USER=\"openldap\"

# System group to run the slapd server under. If empty the server will
# run in the primary group of its user.
SLAPD_GROUP=\"openldap\"

# Path to the pid file of the slapd server. If not set the init.d script
# will try to figure it out from \$SLAPD_CONF (/etc/ldap/slapd.conf)
SLAPD_PIDFILE=

# slapd normally serves ldap only on all TCP-ports 389. slapd can also
# service requests on TCP-port 636 (ldaps) and requests via unix
# sockets.
# Example usage:
# SLAPD_SERVICES=\"ldap://127.0.0.1:389/ ldaps:/// ldapi:///\"
SLAPD_SERVICES=\"ldap:/// ldapi:///\"

# If SLAPD_NO_START is set, the init script will not start or restart
# slapd (but stop will still work).  Uncomment this if you are
# starting slapd via some other means or if you don't want slapd normally
# started at boot.
#SLAPD_NO_START=1

# If SLAPD_SENTINEL_FILE is set to path to a file and that file exists,
# the init script will not start or restart slapd (but stop will still
# work).  Use this for temporarily disabling startup of slapd (when doing
# maintenance, for example, or through a configuration management system)
# when you don't want to edit a configuration file.
SLAPD_SENTINEL_FILE=/etc/ldap/noslapd

# For Kerberos authentication (via SASL), slapd by default uses the system
# keytab file (/etc/krb5.keytab).  To use a different keytab file,
# uncomment this line and change the path.
#export KRB5_KTNAME=/etc/krb5.keytab

# Additional options to pass to slapd
SLAPD_OPTIONS=\"\"

" > /etc/default/slapd

SSL="start_tls"

# desactivation TLS pour contournement bug en attendant utilsation autre lib
SSL="off"

if [ "$replica_status" = "2" ]
then
	SSL="off"
fi
# Pas de ssl si le ldap est local
if [ "$replica_status" == "" -o "$replica_status" = "0" ]
then	
	if [ "$ldap_server" == "$se3ip" ]
	then
		echo "Pas de replication, LDAP local, SSL off"
		SSL="off"
	fi
fi

#################################################################################
#	Slave Syncrepl						  		#
#################################################################################
if [ "$replica_status" = "4" ]
then
	# On supprime la base 
	if [ -e "/var/backups/ldap/DB_CONFIG" ]
        then
	    cp /var/backups/ldap/DB_CONFIG /var/lib/ldap/
	else
	    mkdir -p /var/backups/ldap/
	    cp /var/lib/ldap/DB_CONFIG /var/backups/ldap/
	fi

rm -f /var/lib/ldap/*

echo "syncrepl rid=0
 provider=ldap://$ldap_server:389
 type=refreshOnly
 interval=00:00:01:00
 searchbase=\"$ldap_base_dn\"
 scope=sub
 schemachecking=off
 bindmethod=simple
 binddn=\"cn=admin,$ldap_base_dn\"
 credentials=$ldap_passwd" > /etc/ldap/syncrepl.conf

# Ajout de l'include dans slapd.conf
echo "# Replication Slave Syncrepl
include /etc/ldap/syncrepl.conf" >> /etc/ldap/slapd.conf 

# Modife les differents fichiers de conf
serveurs="$ldap_server $LDAP_LOCAL"
fi

#################################################################################
# Master Syncrepl								#
#################################################################################
if [ "$replica_status" = "3" ]
then
	serveurs="$ldap_server $replica_ip"
	
	# touch syncrepl vide pour indiquer la methode
	
echo "moduleload syncprov
overlay syncprov
syncprov-checkpoint 50 5
syncprov-sessionlog 50" > /etc/ldap/syncrepl.conf
	
# Ajout de l'include dans slapd.conf
echo "# Replication Slave Syncrepl
include /etc/ldap/syncrepl.conf" >> /etc/ldap/slapd.conf
fi


################################################################################# 
#		Pas de replication 						#
#################################################################################
if [ "$replica_status" = "0" ]
then
    serveurs="$ldap_server"
fi

#################################################################################
# 		Creation de : libnss-ldap.conf pam_ldap.conf ldap.conf		#
#################################################################################
echo "ldap_version 3
base $ldap_base_dn
rootbinddn $ldap_admin,$ldap_base_dn
#bindpw pasecure
host $serveurs
#scope sub

#ssl start_tls
#tls_checkpeer no
" > /etc/libnss-ldap.conf

# Creation de pam_ldap.conf
echo "ldap_version 3
base $ldap_base_dn
rootbinddn $ldap_admin,$ldap_base_dn
#bindpw pasecure
host $serveurs
pam_crypt local
#ssl start_tls
#tls_checkpeer no
" > /etc/pam_ldap.conf

# Creation de ldap.conf
echo "HOST $serveurs
BASE $ldap_base_dn
#TLS_REQCERT never
#TLS_CACERTDIR /etc/ldap/
#TLS_CACERT /etc/ldap/slapd.pem
" > /etc/ldap/ldap.conf

#################################################################################
# 		Fin de la conf							#
#################################################################################

chmod 550 /etc/ldap/slapd.conf
chmod 444 /etc/ldap/slapd.pem

chown -R openldap:openldap /etc/ldap
chown -R openldap:openldap /var/lib/ldap

service slapd start

# Supprime le lock
rm -f /var/lock/syncrepl.lock
