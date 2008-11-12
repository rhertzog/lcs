#!/bin/bash
set -e

get_lcsdb_params() {
  mysqlpass=`cat /var/www/lcs/includes/config.inc.php | grep PASSAUTH= | cut -d = -f 2 | cut -d \" -f 2`
  PARAMS=`echo  "SELECT value FROM params WHERE name='$1'"| mysql -u lcsmgr -p$mysqlpass lcs_db -N`
  echo "$PARAMS"
}

# get LCS params
LDAP_BASE_DN=$(get_lcsdb_params ldap_base_dn)
LDAP_ADMIN_RDN=$(get_lcsdb_params adminRdn)
LDAP_ADMIN_PW=$(get_lcsdb_params adminPw)
LDAP_PEOPLE_RDN=$(get_lcsdb_params peopleRdn)
DOMAIN=$(get_lcsdb_params domain)
# Create spip.manager account
if ! getent passwd spip.manager >/dev/null; then
  echo "Creation compte spip.manager"
  sed -e "s|#PEOPLE#|$LDAP_PEOPLE_RDN|g" \
      -e "s|#BASEDN#|$LDAP_BASE_DN|g" \
      -e "s|#MAILDOMAIN#|$DOMAIN|g" \
       /var/lib/lcs/spip/Ldif/spipmgr.ldif.in > /var/lib/lcs/spip/Ldif/spipmgr.ldif
  ldapadd -x -D "$LDAP_ADMIN_RDN,$LDAP_BASE_DN" -w $LDAP_ADMIN_PW -f /var/lib/lcs/spip/Ldif/spipmgr.ldif
  rm -f /var/lib/lcs/spip/Ldif/spipmgr.ldif
fi
exit 0