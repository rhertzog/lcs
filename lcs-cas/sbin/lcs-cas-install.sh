### lcs-cas-install.sh
#!/bin/bash
# lcs-cas-install.sh 
# Install CAS service on LCS base on rubycas-server <http://code.google.com/p/rubycas-server/>
# 
# Get LCS params in lcs_db
get_lcsdb_params() {
    PARAMS=`echo  "SELECT value FROM params WHERE name='$1'"| mysql lcs_db -N`
    echo "$PARAMS"
}
# get ruby version
if grep -q ^7 /etc/debian_version; then
	RUBYVER="1.9.1"
else
	RUBYVER="1.8"
fi

#
# rubycas-server configuration and path
#
LOGFOLDER="/var/log/rubycas-server"
CONF="/etc/rubycas-server"
RUN="/var/run/rubycas-server"
USERRUN="casserver"
GROUPRUN="casserver"
USERHOME="/var/lib/lcs/cas"
PATH_RUBYCAS_CERT=$CONF
IN_CONFIG_PATH=$USERHOME
RUBYCAS_CERT_TT="openssl.cas.in" # template opensll cert for cas
#
# Fix gem version to install
#
#RUBYGEMSUPDATEVERSION=1.3.7

ACTIVERECORDVERSION=2.3.4
MARKABYVERSION=0.7.1
PICNICVERSION=0.8.1.20100201

MYSQLVERSION=2.9.0
MYSQL2VERSION=0.3.11
NETLDAPVERSION=0.3.1
RUBYCASVERSION=1.1.2
DAEMONSVERSION=1.1.9

#GEMSSOURCE="http://lcsgems.crdp.ac-caen.fr"
#
# get LCSMGR PASS
#
LCSMGRPASS=`cat /var/www/lcs/includes/config.inc.php | grep "PASSAUTH=" | cut -d '"' -f 2`
#
# get LCS params
#
FQN=`hostname -f`
DOMAIN=$(get_lcsdb_params domain)
COUNTRYNAME=$(get_lcsdb_params country)
PROVINCENAME=$(get_lcsdb_params province)
LOCALITYNAME=$(get_lcsdb_params locality)
ORGANIZATIONNAME=$(get_lcsdb_params organization)
ORGANIZATIONALUNITNAME=$(get_lcsdb_params organizationalunit)
LDAP_SERVER=$(get_lcsdb_params ldap_server)
LDAP_BASE_DN=$(get_lcsdb_params ldap_base_dn)
#
# Update system gem
#
#VERGEM=`gem -v | sed s/"\."//g`
#if [ $VERGEM -lt "137" ];then
#	gem install rubygems-update --version $RUBYGEMSUPDATEVERSION --no-ri --no-rdoc 
#    chmod +x /var/lib/gems/1.8/bin/update_rubygems
#    /var/lib/gems/1.8/bin/update_rubygems
#fi
#
# Install packages for gem mysql
#
apt-get --force-yes -y install ruby$RUBYVER-dev build-essential libmysqlclient-dev
#
# Install gems
#
# Install : activeesupport-2.3.4  activerecord-2.3.4
### --source $GEMSSOURCE
#gem install activerecord --version $ACTIVERECORDVERSION --no-ri --no-rdoc --source $GEMSSOURCE
# Install builder-2.1.2 markaby-0.7.1
#gem install markaby --version $MARKABYVERSION --no-ri --no-rdoc --source $GEMSSOURCE
# Installed rack-1.2.1 picnic-0.8.1.20100201
#gem install picnic --version $PICNICVERSION --no-ri --no-rdoc --source $GEMSSOURCE
#  Install ruby-net-ldap-0.0.4
#gem install ruby-net-ldap --version $RUBYNETLDAPVERSION --no-ri --no-rdoc --source $GEMSSOURCE
# Install mysql-2.8.1
#gem install mysql --version $MYSQLVERSION --no-ri --no-rdoc --source $GEMSSOURCE

# Install : mysql and mysql2
gem install mysql --version $MYSQLVERSION --no-ri --no-rdoc -f
gem install mysql2 --version $MYSQL2VERSION --no-ri --no-rdoc -f
# Install : net-ldap
gem install net-ldap --version $NETLDAPVERSION --no-ri --no-rdoc -f
# Install : rubycas-server and dependencies
gem install rubycas-server --version $RUBYCASVERSION --no-ri --no-rdoc -f
# Install : daemons
gem install daemons --version $DAEMONSVERSION --no-ri --no-rdoc -f
#
#
#
if [ `getent passwd $USERRUN | wc -l` = "0" ]; then
    adduser  $USERRUN --disabled-password --gecos 'CAS Server Account,,,' --shell /bin/bash --no-create-home --home $USERHOME
fi
mkdir -p $CONF
#Configure authentication ldap or mysql
AUTH_MOD=$(get_lcsdb_params auth_mod)
if [ ! -z $AUTH_MOD ]; then
	if [ "$AUTH_MOD" = "ENT" ]; then
		MODAUTHENTICATION="mysql"
	else
		MODAUTHENTICATION="ldap"
	fi
else
	MODAUTHENTICATION="ldap"
fi
# 
if [ "$MODAUTHENTICATION" = "ldap" ]; then
	# Ldap authentication
	cp $USERHOME/config.yml.ldap.in $CONF/config.yml
	# LCSMGR PASS
	sed -i s/#LCSPASS#/$LCSMGRPASS/g $CONF/config.yml
	#LDAP_SERVER
	sed -i s/#LDAP_SERVER#/$LDAP_SERVER/g $CONF/config.yml
	#LDAP_BASE_DN
	sed -i s/#LDAP_BASE_DN#/$LDAP_BASE_DN/g $CONF/config.yml
else
	# Mysql authentication
	cp $USERHOME/config.yml.mysql.in $CONF/config.yml
	# LCSMGR PASS
	sed -i s/#LCSPASS#/$LCSMGRPASS/g $CONF/config.yml
fi
####
#cp $USERHOME/config.yml.in $CONF/config.yml
#
# LCSMGR PASS
# 
#sed -i s/#LCSPASS#/$LCSMGRPASS/g $CONF/config.yml
#
#LDAP_SERVER
#
#sed -i s/#LDAP_SERVER#/$LDAP_SERVER/g $CONF/config.yml
#
#LDAP_BASE_DN#
#
#sed -i s/#LDAP_BASE_DN#/$LDAP_BASE_DN/g $CONF/config.yml
####
#
# Bdd
#create database casserver;
if [  ! -d /var/lib/mysql/casserver ]; then
	mysqladmin create casserver
	mysql -se "grant all privileges on casserver.* to lcsmgr@localhost;"
fi

# Now by lcs-certmanager !!!!
# Generate certificat
# ssl_cert: /etc/rubycas-server/server.crt
# ssl_key: /etc/rubycas-server/server.key
#
#cat $IN_CONFIG_PATH/$RUBYCAS_CERT_TT \
#        |sed -e "s!#FQN#!$FQN!g" \
#        |sed -e "s!#DOMAIN#!$DOMAIN!g" \
#        |sed -e "s!#COUNTRYNAME#!$COUNTRYNAME!g" \
#        |sed -e "s!#PROVINCENAME#!$PROVINCENAME!g" \
#        |sed -e "s!#LOCALITYNAME#!$LOCALITYNAME!g" \
#        |sed -e "s!#ORGANIZATIONNAME#!$ORGANIZATIONNAME!g" \
#        |sed -e "s!#ORGANIZATIONALUNITNAME#!$ORGANIZATIONALUNITNAME!g" \
#        > $IN_CONFIG_PATH/openssl.cas
# 
#openssl req  -config $IN_CONFIG_PATH/openssl.cas -x509 -nodes -days 365 \
#                -newkey rsa:1024 -out $PATH_RUBYCAS_CERT/server.crt -keyout $PATH_RUBYCAS_CERT/server.key
# Make pem certificat
#cat $PATH_RUBYCAS_CERT/server.crt $PATH_RUBYCAS_CERT/server.key > $PATH_RUBYCAS_CERT/server.pem

# Make symb links on ssl certificat
rm -rf /etc/rubycas-server/server.*
ln -s /etc/ssl/lcs/server.key /etc/rubycas-server/server.key
ln -s /etc/ssl/lcs/server.crt /etc/rubycas-server/server.crt
#
# Fix owner on folders and files rubycas
#
chown -R casserver:casserver $CONF
if [ ! -d $LOGFOLDER ]; then
	mkdir -p $LOGFOLDER
	touch $LOGFOLDER/$USERRUN.log
fi
chmod 750 $LOGFOLDER
chmod 640 $LOGFOLDER/$USERRUN.log
chown $USERRUN:$GROUPRUN $LOGFOLDER -R

#if [ ! -d $RUN ]; then
#	mkdir $RUN
#	chown -R $USERRUN:$GROUPRUN $RUN
#fi

chown $USERRUN /var/lib/gems/$RUBYVER/gems/rubycas-server-$RUBYCASVERSION/bin
#
# Run Levels
#
cp $USERHOME/rubycas-lcs /etc/init.d/rubycas-lcs
chmod +x /etc/init.d/rubycas-lcs
update-rc.d rubycas-lcs defaults
#
# Remove and purge critical packages 
#
apt-get -y remove --purge binutils build-essential
#
# Validate CAS service
#
/usr/bin/mysql -e "UPDATE lcs_db.params SET value='1' WHERE name='lcs_cas';"
#
# Modify cas.rb for consumed st ticket
#
cp /var/lib/lcs/cas/cas_new.rb /var/lib/gems/$RUBYVER/gems/rubycas-server-$RUBYCASVERSION/lib/casserver/cas.rb
# Modify server.rb to bind 0.0.0.0
if [ $RUBYVER = 1.9.1 ]; then
	cp /var/lib/lcs/cas/server_1.9.1.rb 	/var/lib/gems/$RUBYVER/gems/rubycas-server-$RUBYCASVERSION/lib/casserver/server.rb
fi
#
# Add rubycas-server-control to start/stop service cas in daemon mode
#
cp /var/lib/lcs/cas/rubycas-server-control_$RUBYVER /var/lib/gems/$RUBYVER/gems/rubycas-server-$RUBYCASVERSION/bin/rubycas-server-control
#
# Start rubycas-lcs service
#
invoke-rc.d rubycas-lcs start
exit 0
