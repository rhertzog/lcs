### lcs-cas-uninstall.sh
#!/bin/bash
# lcs-cas-uninstall.sh 
# Uninstall CAS service on LCS base on rubycas-server <http://code.google.com/p/rubycas-server/>

#
# rubycas-server configuration and path
#
LOGFOLDER="/var/log/rubycas-server"
CONF="/etc/rubycas-server"
RUN="/var/run/rubycas-server"
USERRUN="casserver"
GROUPRUN="casserver"
USERHOME="/var/lib/lcs/cas"
PATH_RUBYCAS_CERT=$CONF
IN_CONFIG_PATH=$USERHOME
RUBYCAS_CERT_TT="openssl.cas.in" # template opensll cert for cas


gem uninstall rubycas-server
gem uninstall ruby-net-ldap
gem uninstall mysql
gem uninstall ruby-net-ldap
gem uninstall picnic
gem uninstall markaby
gem uninstall activerecord