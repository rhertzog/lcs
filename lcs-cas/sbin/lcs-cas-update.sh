#!/bin/bash
# lcs-cas-update.sh 
# update rubycas-lcs base on rubycas-server <http://code.google.com/p/rubycas-server/>
#
# Get LCS params in lcs_db
function get_lcsdb_params() {
    PARAMS=`echo  "SELECT value FROM params WHERE name='$1'"| mysql lcs_db -N`
    echo "$PARAMS"
}
#
# rubycas-lcs configuration and path
#
CONF="/etc/rubycas-lcs"
USERHOME="/var/lib/lcs/cas"
#
# get LCSMGR PASS
#
LCSMGRPASS=`cat /var/www/lcs/includes/config.inc.php | grep "PASSAUTH=" | cut -d '"' -f 2`
#
# get LCS params
#
LDAP_SERVER=$(get_lcsdb_params ldap_server)
LDAP_BASE_DN=$(get_lcsdb_params ldap_base_dn)

cd $USERHOME
if [ -e rubycas-lcs-latest.gem  ]; then
  rm rubycas-lcs-latest.gem
fi
wget http://lcs.crdp.ac-caen.fr/gems/rubycas-lcs-latest.gem
if [ -e rubycas-lcs-latest.gem  ]; then
    gem install rubycas-lcs-latest.gem --no-ri --no-rdoc
    VER=`gem list | grep rubycas-lcs | cut -d '(' -f 2 | cut -d ',' -f 1 | cut -d ')' -f 1`
    mv rubycas-lcs-latest.gem rubycas-lcs-$VER.gem
else
  echo "ERROR no rubycas-lcs-latest gem to update !"
  exit 1
fi
#
# Pass to Authenticators::LDAP
#
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
# Restart cas service
#
invoke-rc.d rubycas-lcs restart
exit 0
