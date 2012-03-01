#!/bin/bash
# Configure courrier squirrelmail sur LCS
# jLCF on 15/01/2011
#
IN_CONFIG_PATH="/etc/lcs/squirrelmail"

if [ -z "$1" ] || [ -z "$2" ];
then
        echo "Usage: squirrelmail_reconfigure.sh <LDAP_SERVER> <LDAP_BASE_DN>"
        exit 0
fi
LDAP_SERVER="$1"
LDAP_BASE_DN="$2"
#
# Authentification LCS Squirrelmail
#
cp /etc/lcs/squirrelmail/redirect.php /usr/share/squirrelmail/src/
cp /etc/lcs/squirrelmail/page_header.php /usr/share/squirrelmail/functions/
#
# Compatibilite avec lcs-desktop
#
cp /etc/lcs/squirrelmail/left_main.php /usr/share/squirrelmail/src/
#
#
#
cp /etc/lcs/squirrelmail/options.php /usr/share/squirrelmail/src/
#
# Configuration squirrelmail
#
cp /etc/lcs/squirrelmail/config.php.in /etc/squirrelmail/config.php
#
# Gestion du carnet d'adresses ldap
#
cp /etc/squirrelmail/config_local.php /etc/squirrelmail/config_local.php.lcssav
echo "<?" > /etc/squirrelmail/config_local.php
echo "/* LDAP CONFGURATION */" >> /etc/squirrelmail/config_local.php
echo "\$ldap_server[0] = array(" >> /etc/squirrelmail/config_local.php
echo -e "\t'host' => '$LDAP_SERVER',">> /etc/squirrelmail/config_local.php
echo -e "\t'base' => '$LDAP_BASE_DN',">> /etc/squirrelmail/config_local.php
echo -e "\t'name' => 'Annuaire de tous les utilisateurs',">> /etc/squirrelmail/config_local.php
echo -e "\t'protocol' => 2">> /etc/squirrelmail/config_local.php
echo -e ");">> /etc/squirrelmail/config_local.php
echo "?>" >> /etc/squirrelmail/config_local.php

