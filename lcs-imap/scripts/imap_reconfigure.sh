#!/bin/bash
# Configure courrier imap for LCS
# Jean-Luc Chretien on 05 Avril 2012
#

if [ "$4" = "" ];
then
        echo "Usage: imap_reconfigure.sh <LDAP_SERVER> <LDAP_BASE_DN> <LDAP_ADMIN_RDN> <LDAP_ADMIN_PW>"
        exit 0
fi

LDAP_SERVER="$1"
LDAP_BASE_DN="$2"
LDAP_ADMIN_RDN="$3"
LDAP_ADMIN_PW="$4"

#MODAUTH=`mysql -se "select value from lcs_db.params where name='auth_mod';"`

# Get LCS params in lcs_db
get_lcsdb_params() {
    PARAMS=`echo  "SELECT value FROM params WHERE name='$1'"| mysql lcs_db -N`
    echo "$PARAMS"
}

#Configure authentication ldap or mysql
AUTH_MOD=$(get_lcsdb_params auth_mod)

if [ "$AUTH_MOD" = "ENT"  ];
then
	# Configure authdaemonrc for pam_cas
	cp /etc/lcs/courier/authdaemonrc.ent /etc/courier/authdaemonrc
	# Configure pam.d/imap
	cp /etc/lcs/pam.d/imap.ent /etc/pam.d/imap
	# Configure pam-cas.conf
	cp /usr/share/doc/libpam-cas/pam_cas.conf /etc/security/
	DOMAINENAME=`hostname -f | tr A-Z a-z`
	sed -i s/"# proxy https:\/\/lcs"/"proxy https:\/\/$DOMAINENAME"/g /etc/security/pam_cas.conf
else
	# Configure authdaemonrc for pamldap
	cp /etc/lcs/courier/authdaemonrc.lcs /etc/courier/authdaemonrc
	# Configure pam.d/imap
	cp /etc/lcs/pam.d/imap.lcs /etc/pam.d/imap
	# Configure pam-cas.conf
	rm -f /etc/security/pam_cas.conf
fi

# Configure authldaprc
sed -e "s/#LDAPSERVER#/$LDAP_SERVER/g" \
    -e "s/#BASEDN#/$LDAP_BASE_DN/g" \
    -e "s/#ADMINRDN#/$LDAP_ADMIN_RDN/g" \
    -e "s/#ADMINPASSWD#/$LDAP_ADMIN_PW/g" \
    /etc/lcs/courier/authldaprc.in >/etc/courier/authldaprc
chmod 660 /etc/courier/authldaprc
chown daemon:daemon /etc/courier/authldaprc

invoke-rc.d postfix restart >/dev/null
invoke-rc.d courier-authdaemon restart >/dev/null
invoke-rc.d courier-imap restart >/dev/null

