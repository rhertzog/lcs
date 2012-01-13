### lcs-cas-configure-authentication.sh
#!/bin/bash
# lcs-cas-configure-authentication.sh
# Configure rubycas-server authentication ldap or mysql
# 
# Get LCS params in lcs_db
get_lcsdb_params() {
    PARAMS=`echo  "SELECT value FROM params WHERE name='$1'"| mysql lcs_db -N`
    echo "$PARAMS"
}
#Configure authentication ldap or mysql
AUTH_MOD=$(get_lcsdb_params auth_mod)
if [ ! -z $AUTH_MOD ]; then
	if [ $AUTH_MOD == "ENT" ]; then
		MODAUTHENTICATION="mysql"
	else
		MODAUTHENTICATION="ldap"
	fi
else
	MODAUTHENTICATION="ldap"
fi
# 
if [ $MODAUTHENTICATION == "ldap" ]; then
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