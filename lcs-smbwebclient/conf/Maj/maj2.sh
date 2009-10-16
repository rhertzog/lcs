#!/bin/bash

# Module : smbwebclient
# INDICEMAJNBR="2"
#
# Add smb_is_open right
if ! ldapsearch -x cn=smbweb_is_open|grep -q "^cn"; then
    ldap_server=$(echo "SELECT value FROM params WHERE name='ldap_server';" | mysql -uroot -N lcs_db)
    ldap_base_dn=$(echo "SELECT value FROM params WHERE name='ldap_base_dn';" | mysql -uroot -N lcs_db)
    adminRdn=$(echo "SELECT value FROM params WHERE name='adminRdn';" | mysql -uroot -N lcs_db)
    adminPw=$(echo "SELECT value FROM params WHERE name='adminPw';" | mysql -uroot -N lcs_db)

    tmp=/root/tmp/smbweb_is_open_$(date +%Y%m%d%H%M%S)
    mkdir -p $tmp
echo "dn: cn=smbweb_is_open,ou=rights,$ldap_base_dn
objectClass: groupOfNames
cn: smbweb_is_open
member: uid=admin,ou=People,$ldap_base_dn
member: cn=Administratifs,ou=Groups,$ldap_base_dn
member: cn=Profs,ou=Groups,$ldap_base_dn
member: cn=Eleves,ou=Groups,$ldap_base_dn
" > $tmp/smbweb_is_open.ldif
    ldapadd -x -h $ldap_server -D $adminRdn,$ldap_base_dn -w $adminPw -f $tmp/smbweb_is_open.ldif &>/dev/null
fi