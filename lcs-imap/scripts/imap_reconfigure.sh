#!/bin/bash
# Configure courrier imap sur LCS
# jLCF <(°_-)/> on 15 Mai 2008
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

sed -i.lcssav 's/authmodulelist="authpam"/authmodulelist="authldap"/g' /etc/courier/authdaemonrc 
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

