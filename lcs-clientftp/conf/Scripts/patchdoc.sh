#!/bin/bash
set -e

function get_lcsdb_params() {
  PARAMS=`echo  "SELECT value FROM params WHERE name='$1'"| mysql lcs_db -N`
  echo "$PARAMS"
}

VLANSC=$(get_lcsdb_params vlansc)
VLANADMINISTRATIF=$(get_lcsdb_params vlanadmin)
VLANPEDA1=$(get_lcsdb_params vlanpeda1)
VLANPEDA2=$(get_lcsdb_params vlanpeda2)
VLANPEDA3=$(get_lcsdb_params vlanpeda3)
VLANPEDA4=$(get_lcsdb_params vlanpeda4)

LANS="$VLANSC $VLANADMINISTRATIF $VLANPEDA1 $VLANPEDA2 $VLANPEDA3 $VLANPEDA4"

sed -i "s|@IPLAN@|$LANS|g" /usr/share/doc/lcs/clientftp/html/index.html

exit 0
