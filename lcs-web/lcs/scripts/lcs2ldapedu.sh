#!/bin/sh

set -e

if [ ! -e /root/.my.cnf ]; then
    echo "/root/.my.cnf is missing, I don't know how to connect to mysql"
fi

eval $(grep -E '(user|password)=' /root/.my.cnf)

get_lcs_param() {
    local name=$1
    echo $(mysql -u $user -p$password lcs_db -N -s -e "SELECT value FROM params WHERE name='$name'")
}

debconf-set-selections <<END
ldapedu-server ldapedu-server/master string $(get_lcs_param ldap_master_server)
ldapedu-server ldapedu-server/rid string $(get_lcs_param ldaprid)
ldapedu-server ldapedu-server/adminpass password $(get_lcs_param adminPw)
ldapedu-server ldapedu-server/ad-auth-delegation boolean $(get_lcs_param ad_auth_delegation)
ldapedu-server ldapedu-server/ad-server string $(get_lcs_param ad_server)
ldapedu-server ldapedu-server/ad-base-dn string $(get_lcs_param ad_base_dn)
ldapedu-server ldapedu-server/ad-bind-dn string $(get_lcs_param ad_bind_dn)
ldapedu-server ldapedu-server/ad-bind-pw password $(get_lcs_param ad_bind_pw)
END

. /usr/share/slis/slis-common.sh
handle_initial_config ldapedu-server-config 90 --reconfigure >/dev/null

