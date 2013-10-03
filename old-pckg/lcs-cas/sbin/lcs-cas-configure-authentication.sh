### lcs-cas-configure-authentication.sh
#!/bin/bash
# lcs-cas-configure-authentication.sh
# Configure rubycas-server authentication ldap or mysql
#
USERHOME="/var/lib/lcs/cas"
CONF="/etc/rubycas-server"

# Get LCS params in lcs_db
get_lcsdb_params() {
    PARAMS=`echo  "SELECT value FROM params WHERE name='$1'"| mysql lcs_db -N`
    echo "$PARAMS"
}
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
echo "MODAUTH : $MODAUTHENTICATION"
LCSMGRPASS=`cat /var/www/lcs/includes/config.inc.php | grep "PASSAUTH=" | cut -d '"' -f 2`
LDAP_SERVER=$(get_lcsdb_params ldap_server)
LDAP_BASE_DN=$(get_lcsdb_params ldap_base_dn)
# 
if [ "$MODAUTHENTICATION" = "ldap" ]; then
 	echo "ldap config"
	# Ldap authentication
	cp $USERHOME/config.yml.ldap.in $CONF/config.yml
	# LCSMGR PASS
	sed -i s/#LCSPASS#/$LCSMGRPASS/g $CONF/config.yml
	#LDAP_SERVER
	sed -i s/#LDAP_SERVER#/$LDAP_SERVER/g $CONF/config.yml
	#LDAP_BASE_DN
	sed -i s/#LDAP_BASE_DN#/$LDAP_BASE_DN/g $CONF/config.yml
else
    echo "mysql config"
	# Mysql authentication
	cp $USERHOME/config.yml.mysql.in $CONF/config.yml
	# LCSMGR PASS
	sed -i s/#LCSPASS#/$LCSMGRPASS/g $CONF/config.yml
fi
# restart CAS service
invoke-rc.d rubycas-lcs restart