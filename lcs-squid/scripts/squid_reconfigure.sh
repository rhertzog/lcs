#!/bin/bash
# LCS squid_reconfigure.sh
# Jean-Luc Chretien <(-_°)/> <jean-luc.chretien@tice.ac-caen.fr

IN_CONFIG_PATH="/var/lib/lcs/squid/"
CONFIG_PATH="/etc/squid"

if [ -z "$2" ];
then
        echo "Usage: squid_reconfigure.sh <DOMAIN> <VLANSC> <VLANADMINISTRATIF> <VLANPEDA1> <VLANPEDA2> <VLANPEDA3> <VLANPEDA4>"
        echo "<DOMAIN> : Nom de domaine,"
        echo "<VLANSC> : Adresse/masque du Vlan service commun,"
        echo "<VLANADMINISTRATIF> : Adresse/masque du VLAN ADMINISTRATIF,"
        echo "<VLANPEDA1> : Adresse/masque du VLAN pédagogique n°1,"
        echo "<VLANPEDA2> : Adresse/masque du VLAN pédagogique n°2,"
        echo "<VLANPEDA3> : Adresse/masque du VLAN pédagogique n°3,"
        echo "<VLANPEDA4> : Adresse/masque du VLAN pédagogique n°4,"
        exit 0
fi
 
DOMAIN="$1"
VLANSC="$2"
VLANADMINISTRATIF="$3"
VLANPEDA1="$4"
VLANPEDA2="$5"
VLANPEDA3="$6"
VLANPEDA4="$7"

sed -e "s|#DOMAIN#|$DOMAIN|g" \
$IN_CONFIG_PATH/squid.conf.in > $CONFIG_PATH/squid.conf

if [ -n "$VLANSC" ]; then
    sed -i "s|#VLANSC#|"$VLANSC"|g" $CONFIG_PATH/squid.conf
    sed -i "s|#acl VLANSC|acl VLANSC|g" $CONFIG_PATH/squid.conf
    sed -i "s|#http_access allow VLANSC|http_access allow VLANSC|g" $CONFIG_PATH/squid.conf
fi

if [ -n "$VLANADMINISTRATIF" ]; then
    sed -i "s|#VLANADMINISTRATIF#|"$VLANADMINISTRATIF"|g" $CONFIG_PATH/squid.conf
    sed -i "s|#acl VLANADMINISTRATIF|acl VLANADMINISTRATIF|g" $CONFIG_PATH/squid.conf
    sed -i "s|#http_access allow VLANADMINISTRATIF|http_access allow VLANADMINISTRATIF|g" $IN_CONFIG_PATH/squid.conf
fi

if [ -n "$VLANPEDA1" ]; then
    sed -i "s|#VLANPEDA1#|"$VLANPEDA1"|g" $CONFIG_PATH/squid.conf
    sed -i "s|#acl VLANPEDA1|acl VLANPEDA1|g" $CONFIG_PATH/squid.conf
    sed -i "s|#http_access allow VLANPEDA1|http_access allow VLANPEDA1|g" $CONFIG_PATH/squid.conf
fi

if [ -n "$VLANPEDA2" ]; then
    sed -i "s|#VLANPEDA2#|"$VLANPEDA2"|g" $CONFIG_PATH/squid.conf
    sed -i "s|#acl VLANPEDA2|acl VLANPEDA2|g" $CONFIG_PATH/squid.conf
    sed -i "s|#http_access allow VLANPEDA2|http_access allow VLANPEDA2|g" $CONFIG_PATH/squid.conf
fi

if [ -n "$VLANPEDA3" ]; then
    sed -i "s|#VLANPEDA3#|"$VLANPEDA3"|g" $CONFIG_PATH/squid.conf
    sed -i "s|#acl VLANPEDA3|acl VLANPEDA3|g" $CONFIG_PATH/squid.conf
    sed -i "s|#http_access allow VLANPEDA3|http_access allow VLANPEDA3|g" $CONFIG_PATH/squid.conf
fi

if [ -n "$VLANPEDA4" ]; then
    sed -i "s|#VLANPEDA4#|"$VLANPEDA4"|g" $CONFIG_PATH/squid.conf
    sed -i "s|#acl VLANPEDA4|acl VLANPEDA4|g" $CONFIG_PATH/squid.conf
    sed -i "s|#http_access allow VLANPEDA4|http_access allow VLANPEDA4|g" $CONFIG_PATH/squid.conf
fi
#
#
#
if [ `grep '/var/spool/squid' /etc/fstab | wc -l` = 1 ]; then
  echo "Configuration taille partition squid"
  dfsquid=`df -m | grep "/var/spool/squid" | awk '{ print $2 }'`
  # On marge pour les bloques de reserve
  marge=$(( $dfsquid / 8 ))
  squidpart=$(( $dfsquid - $marge ))
  # remplacement
  ligne=`cat /etc/squid/squid.conf |grep cache_dir | grep -v "#"`
  sub="cache_dir ufs /var/spool/squid $squidpart 16 256"
  sed -i 's#$ligne#$sub#g' /etc/squid/squid.conf
  chown proxy:proxy /var/spool/squid
fi
#
# Restart squid
#
/usr/sbin/invoke-rc.d squid restart > /dev/null

