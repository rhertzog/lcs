#!/bin/bash
# Configure courrier pop sur LCS
# jLCF <(°_-)/> on 15 Mai 2008
#

if [ "$4" = "" ];
then
        echo "Usage: pop_reconfigure.sh <LDAP_SERVER> <LDAP_BASE_DN> <LDAP_ADMIN_RDN> <LDAP_ADMIN_PW>"
        exit 0
fi

/usr/share/lcs/scripts/imap_reconfigure.sh $*
invoke-rc.d courier-pop restart >/dev/null

