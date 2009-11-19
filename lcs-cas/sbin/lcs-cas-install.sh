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
PICNICVERSION=0.7.1
MYSQLVERSION=0.7.1

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
VERGEM=`gem -v | sed s/"\."//g`
if [ $VERGEM -lt "131" ];then
	cd /var/lib/lcs/cas/
	if [ -e rubygems-update-1.3.1.gem ]; then
  		rm rubygems-update-1.3.1.gem 
	fi
	wget http://lcs.crdp.ac-caen.fr/gems/rubygems-update-1.3.1.gem 
	if [ -e rubygems-update-1.3.1.gem   ]; then
  		gem install rubygems-update-1.3.1.gem --no-ri --no=rdoc 
	else
  		echo "ERROR no rubygems-update-1.3.1 gem to install !"
  		exit 1
	fi
	gem install rubygems-update-1.3.1.gem --no-ri --no-rdoc
	chmod +x /var/lib/gems/1.8/bin/update_rubygems
	/var/lib/gems/1.8/bin/update_rubygems
	if [ ! -e /usr/bin/gem.old ]; then
  		if [ -e /usr/bin/gem1.8 ]; then
    			mv /usr/bin/gem /usr/bin/gem.old
    			ln -s /usr/bin/gem1.8 /usr/bin/gem
  		fi
	fi
fi
#
# Install packages for gem mysql 
#
apt-get -y install ruby1.8-dev build-essential libmysqlclient15-dev
# 
# Install gems 
#
test1=$(gem list activerecord | grep activerecord)
if [ "$test1" = "activerecord (2.3.4)" ]; then
	echo "Le gem $test1 est present on ne l'installera pas"
else
	echo "Le gem n'est pas present on l'installe"
	gem install activerecord --no-ri --no-rdoc
fi

test1=$(gem list picnic | grep picnic)
if [ "$test1" = "picnic (0.7.1)" ]; then
	echo "Le gem $test1 est present on ne l'installera pas"
else
	echo "Le gem n'est pas present on l'installe"
  	gem install picnic --version $PICNICVERSION --no-ri --no-rdoc
fi

test1=$(gem list ruby-net-ldap | grep ruby-net-ldap)
if [ "$test1" = "ruby-net-ldap (0.0.4)" ]; then
	echo "Le gem $test1 est present on ne l'installera pas"
else
	echo "Le gem n'est pas present on l'installe"
  	gem install ruby-net-ldap --version 0.0.4 --no-ri --no-rdoc
fi

test1=$(gem list mysql | grep mysql)
if [ "$test1" = "mysql (2.7)" ]; then
	echo "Le gem $test1 est present on ne l'installera pas"
else
	echo "Le gem n'est pas present on l'installe"
  	gem install mysql --version 2.7 --no-ri --no-rdoc
fi


if [ -e rubycas-lcs-latest.gem  ]; then
  rm rubycas-lcs-latest.gem
fi
wget http://lcs.crdp.ac-caen.fr/gems/rubycas-lcs-latest.gem
if [ -e rubycas-lcs-latest.gem  ]; then
    gem install rubycas-lcs-latest.gem --no-ri --no-rdoc
    VER=`gem list | grep rubycas-lcs | cut -d '(' -f 2 | cut -d ',' -f 1 | cut -d ')' -f 1`
    mv rubycas-lcs-latest.gem rubycas-lcs-$VER.gem
else
  echo "ERROR no rubycas-lcs-latest gem to install !"
  exit 1
fi
#
#
#
if [ `getent passwd $USERRUN | wc -l` = "0" ]; then
    adduser  $USERRUN --disabled-password --gecos 'CAS Server Account,,,' --shell /bin/bash --no-create-home --home $USERHOME
fi
mkdir -p $CONF
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
exit 0
