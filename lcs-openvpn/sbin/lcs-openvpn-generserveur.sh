#!/bin/bash

source /etc/lcs/lcs.conf
export KEY_DIR=/usr/share/lcs/openvpn/keys
export KEY_COUNTRY=$COUNTRYNAME
export KEY_PROVINCE=$PROVINCENAME
export KEY_CITY=$LOCALITYNAME
export KEY_ORG=$ORGANIZATIONNAME
#ORGANIZATIONALUNITNAME
export KEY_EMAIL=admin@$DOMAIN
export KEY_SIZE=1024
export KEY_CONFIG=/etc/lcs/openvpn/openssl.cnf
mkdir $KEY_DIR
touch $KEY_DIR/index.txt 
echo 01 > $KEY_DIR/serial

### Generation de cles ssl base sur easy-ssl
if test $KEY_DIR; then
	### buld-dh
	openssl dhparam -out ${KEY_DIR}/dh${KEY_SIZE}.pem ${KEY_SIZE}
	### build-ca
	export KEY_COMMONNAME=`hostname`
        cd $KEY_DIR && \
        openssl req -days 3650 -nodes -new -x509 -keyout ca.key -out ca.crt -config $KEY_CONFIG && \
        chmod 0600 ca.key
	### build-key-server
        openssl req -days 3650 -nodes -new -keyout $KEY_COMMONNAME.key -out $KEY_COMMONNAME.csr -extensions server -config $KEY_CONFIG -batch && \
        openssl ca -days 3650 -out $KEY_COMMONNAME.crt -in $KEY_COMMONNAME.csr -extensions server -config $KEY_CONFIG -batch && \
        chmod 0600 $KEY_COMMONNAME.key
	### generation de la cle de securite.
	openvpn --genkey --secret $KEY_DIR/ta.key
else
    echo you must define KEY_DIR
fi

