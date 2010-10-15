#!/bin/bash
#
#
#
## verification argument
if [ -z "$1" ];
then
        echo "Usage: lcs-openvpn-generclient.sh <login> "
        exit 0
fi

## recuperation des parametres
get_lcsdb_params() {
mysqlpass=`cat /var/www/lcs/includes/config.inc.php | grep PASSAUTH= | cut -d = -f 2 | cut -d \" -f 2`
PARAMS=`echo  "SELECT value FROM params WHERE name='$1'"| mysql -u lcsmgr -p$mysqlpass lcs_db -N`
echo "$PARAMS"
}
DH=$(get_lcsdb_params vpndh)
VPNPORT=$(get_lcsdb_params vpnport)
FULLNAME=`hostname -f`
source /etc/lcs/lcs.conf
export KEY_DIR=/usr/share/lcs/openvpn/keys
export KEY_COUNTRY=$COUNTRYNAME
export KEY_PROVINCE=$PROVINCENAME
export KEY_CITY=$LOCALITYNAME
export KEY_ORG=$ORGANIZATIONNAME
export KEY_EMAIL=admin@$DOMAIN
export KEY_SIZE=1024
export KEY_CONFIG=/etc/lcs/openvpn/openssl.cnf

if test $# -ne 1; then
        echo "usage: lcs-openvpn-generclient.sh <name>";
        exit 1
fi
## on verifi l'existance du compte
getent passwd $1
if test $? -ne 0; then
        echo "Utilisateur inconnu";
        exit 1
fi

##
if test $KEY_DIR; then
	export KEY_COMMONNAME=$1
        cd $KEY_DIR && \
        openssl req -days 3650 -nodes -new -keyout $1.key -out $1.csr -config $KEY_CONFIG -batch && \
        openssl ca -days 3650 -out $1.crt -in $1.csr -config $KEY_CONFIG -batch && \
        chmod 0600 $1.key
else
        echo you must define KEY_DIR
fi

## on genere la config client

## on met de certif dans le rep du user
mkdir -p /home/$1/Documents/vpn/lcs
cp /usr/share/lcs/openvpn/keys/ca.crt /home/$1/Documents/vpn/lcs
cp /usr/share/lcs/openvpn/keys/ta.key /home/$1/Documents/vpn/lcs
cp /usr/share/lcs/openvpn/keys/$1.crt /home/$1/Documents/vpn/lcs
cp /usr/share/lcs/openvpn/keys/$1.key /home/$1/Documents/vpn/lcs
cp /var/lib/lcs/openvpn/openvpn-client.conf.in  /home/$1/Documents/vpn/lcs.ovpn
sed -i'' "s/@@FULLDOMAINE@@/$FULLNAME/g" /home/$1/Documents/vpn/lcs.ovpn
sed -i'' "s/@@VPNPORT@@/$VPNPORT/g" /home/$1/Documents/vpn/lcs.ovpn
sed -i'' "s/@@USER@@/$1/g" /home/$1/Documents/vpn/lcs.ovpn
chown $1 /home/$1/Documents/vpn
cd  /home/$1/Documents/vpn 
zip -rq configuration-vpn.zip *
rm -r lcs*
chown $1 /home/$1/Documents/vpn/configuration-vpn.zip
mail -s "Utilisation du VPN-LCS" $1@$FULLNAME < /var/lib/lcs/openvpn/openvpn-mail-client.in 