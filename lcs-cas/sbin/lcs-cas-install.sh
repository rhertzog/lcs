#!/bin/bash
# lcs-cas-install.sh 
# Install rubycas-lcs base on rubycas-server <http://code.google.com/p/rubycas-server/>
# 
# Get LCS params in lcs_db
function get_lcsdb_params() {
	PARAMS=`echo  "SELECT value FROM params WHERE name='$1'"| mysql lcs_db -N`
	echo "$PARAMS"
}
#
# Gem rubycas-lcs version to install
#
VER="0.7.1.1"
#
# rubycas-lcs configuration and path
#
LOGFOLDER="/var/log/casserver"
CONF="/etc/rubycas-lcs"
RUN="/var/run/rubycas-lcs"
USERRUN="casserver"
GROUPRUN="casserver"
USERHOME="/var/lib/lcs/cas"
PATH_RUBYCAS_CERT=$CONF
IN_CONFIG_PATH=$USERHOME
RUBYCAS_CERT_TT="openssl.cas.in" # template opensll cert for cas

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
gem update --system
if [ ! -e /usr/bin/gem.old ]; then
  if [ -e /usr/bin/gem1.8 ]; then
    mv /usr/bin/gem /usr/bin/gem.old
    ln -s /usr/bin/gem1.8 /usr/bin/gem
  fi
fi
#
# Install packages for gem mysql 
#
apt-get -y install ruby1.8-dev build-essential libmysqlclient15-dev
# 
# Install gems 
#
gem install picnic activerecord ruby-net-ldap activerecord mysql
cd /var/lib/lcs/cas/
if [ -e rubycas-lcs-$VER.gem  ]; then
  rm rubycas-lcs-$VER.gem
fi
wget http://lcs.crdp.ac-caen.fr/gems/rubycas-lcs-$VER.gem
if [ -e rubycas-lcs-$VER.gem  ]; then
  gem install rubycas-lcs-$VER.gem
else
  echo "ERROR no rubycas-lcs-$VER gem to install !"
  exit 1
fi
#
#
#
adduser  $USERRUN --disabled-password --gecos 'CAS Server Account,,,' --shell /bin/bash --no-create-home --home $USERHOME
#mkdir $USERHOME
mkdir $CONF
cp /usr/lib/ruby/gems/1.8/gems/rubycas-lcs-$VER/config.example.yml  $CONF/config.yml
#
# LCSMGR PASS
# 
sed -i s/#LCSPASS#/$LCSMGRPASS/g $CONF/config.yml
#
#LDAP_SERVER
#
sed -i s/#LDAP_SERVER#/$LDAP_SERVER/g $CONF/config.yml
#
#LDAP_BASE_DN#
#
sed -i s/#LDAP_BASE_DN#/$LDAP_BASE_DN/g $CONF/config.yml
#
# Bdd
#create database casserver;
if [  ! -d /var/lib/mysql/casserver ]; then
	mysqladmin create casserver
	mysql -se "grant all privileges on casserver.* to lcsmgr@localhost;"
fi
#
# Generate certificat
# ssl_cert: /etc/rubycas-lcs/server.crt
# ssl_key: /etc/rubycas-lcs/server.key
#
cat $IN_CONFIG_PATH/$RUBYCAS_CERT_TT \
        |sed -e "s!#FQN#!$FQN!g" \
        |sed -e "s!#DOMAIN#!$DOMAIN!g" \
        |sed -e "s!#COUNTRYNAME#!$COUNTRYNAME!g" \
        |sed -e "s!#PROVINCENAME#!$PROVINCENAME!g" \
        |sed -e "s!#LOCALITYNAME#!$LOCALITYNAME!g" \
        |sed -e "s!#ORGANIZATIONNAME#!$ORGANIZATIONNAME!g" \
        |sed -e "s!#ORGANIZATIONALUNITNAME#!$ORGANIZATIONALUNITNAME!g" \
        > $IN_CONFIG_PATH/openssl.cas
 
openssl req  -config $IN_CONFIG_PATH/openssl.cas -x509 -nodes -days 365 \
                -newkey rsa:1024 -out $PATH_RUBYCAS_CERT/server.crt -keyout $PATH_RUBYCAS_CERT/server.key
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
if [ ! -d $RUN ]; then
	mkdir $RUN
	chown -R $USERRUN:$GROUPRUN $RUN
fi
#
# Run Levels
#
cp /usr/lib/ruby/gems/1.8/gems/rubycas-lcs-$VER/resources/init.d.sh /etc/init.d/rubycas-lcs
chmod +x /etc/init.d/rubycas-lcs
update-rc.d rubycas-lcs defaults
#
# Remove and purge critical packages 
#
apt-get -y remove --purge binutils build-essential cpp cpp-4.1 dpkg-dev g++ g++-4.1 gcc gcc-4.1 libc6-dev libstdc++6-4.1-dev
#
# Validate CAS service
#
/usr/bin/mysql -e "UPDATE lcs_db.params SET value='1' WHERE name='lcs_cas';"
#
# Start rubycas-lcs service
#
invoke-rc.d rubycas-lcs start