### lcs-cas-install.sh
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
LOGFOLDER="/var/log/rubycas-server"
CONF="/etc/rubycas-server"
RUN="/var/run/rubycas-server"
USERRUN="casserver"
GROUPRUN="casserver"
USERHOME="/var/lib/lcs/cas"
PATH_RUBYCAS_CERT=$CONF
IN_CONFIG_PATH=$USERHOME
RUBYCAS_CERT_TT="openssl.cas.in" # template opensll cert for cas
# Fix gem version to install
#activemodel (3.0.0)
#activerecord (2.3.4)
#activesupport (2.3.4)
#arel (1.0.1)
#builder (2.1.2)
#gettext (2.1.0)
#i18n (0.4.1)
#locale (2.0.5)
#markaby (0.7.1)
#mysql (2.8.1)
#picnic (0.8.1.20100201, 0.8.0)
#rack (1.2.1)
#ruby-net-ldap (0.0.4)
#rubycas-server (0.7.999999.20100202)
#rubygems-update (1.3.7, 1.3.1)
#tzinfo (0.3.23)

RUBYGEMSUPDATEVERSION=1.3.7
ACTIVERECORDVERSION=2.3.4
MARKABYVERSION=0.7.1
PICNICVERSION=0.8.1.20100201
RUBYNETLDAPVERSION=0.0.4
MYSQLVERSION=2.8.1
RUBYCASVERSION=0.7.999999.20100202

GEMSSOURCE="http://lcsgems.crdp.ac-caen.fr"
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
if [ $VERGEM -lt "137" ];then
	gem install rubygems-update --version $RUBYGEMSUPDATEVERSION --no-ri --no-rdoc 
    chmod +x /var/lib/gems/1.8/bin/update_rubygems
    /var/lib/gems/1.8/bin/update_rubygems
fi
#
# Install packages for gem mysql
#
apt-get --force-yes -y install ruby1.8-dev build-essential libmysqlclient15-dev
#
# Install gems
#
# Install : activeesupport-2.3.4  activerecord-2.3.4
### --source $GEMSSOURCE
gem install activerecord --version $ACTIVERECORDVERSION --no-ri --no-rdoc --source $GEMSSOURCE
# Install builder-2.1.2 markaby-0.7.1
gem install markaby --version $MARKABYVERSION --no-ri --no-rdoc --source $GEMSSOURCE
# Installed rack-1.2.1 picnic-0.8.1.20100201
gem install picnic --version $PICNICVERSION --no-ri --no-rdoc --source $GEMSSOURCE
#  Install ruby-net-ldap-0.0.4
gem install ruby-net-ldap --version $RUBYNETLDAPVERSION --no-ri --no-rdoc --source $GEMSSOURCE
# Install mysql-2.8.1
gem install mysql --version $MYSQLVERSION --no-ri --no-rdoc --source $GEMSSOURCE
# Install : locale-2.0.5 gettext-2.1.0 rubycas-server-0.7.999999.20100202 i18n-0.4.1 activemodel-3.0.0 arel-1.0.1 tzinfo-0.3.23
gem install rubycas-server --version $RUBYCASVERSION --no-ri --no-rdoc -f --source $GEMSSOURCE
#
#
#
if [ `getent passwd $USERRUN | wc -l` = "0" ]; then
    adduser  $USERRUN --disabled-password --gecos 'CAS Server Account,,,' --shell /bin/bash --no-create-home --home $USERHOME
fi
mkdir -p $CONF
#cp /usr/lib/ruby/gems/1.8/gems/rubycas-lcs-$VER/config.example.yml  $CONF/config.yml
cp $USERHOME/config.yml.in $CONF/config.yml
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
cp $USERHOME/rubycas-lcs /etc/init.d/rubycas-lcs
chmod +x /etc/init.d/rubycas-lcs
update-rc.d rubycas-lcs defaults
#
# Remove and purge critical packages 
#
apt-get -y remove --purge binutils build-essential cpp cpp-4.3 dpkg-dev g++ g++-4.3 gcc gcc-4.3 libc6-dev libstdc++6-4.3-dev
#
# Validate CAS service
#
/usr/bin/mysql -e "UPDATE lcs_db.params SET value='1' WHERE name='lcs_cas';"
#
# Start rubycas-lcs service
#
invoke-rc.d rubycas-lcs start
exit 0