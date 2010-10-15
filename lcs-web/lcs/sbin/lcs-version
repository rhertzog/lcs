#!/bin/bash
#
# Affiche le numero de version de LCS
#

mysql -N -B -e "select value from lcs_db.params WHERE name='VER';"

if [ "$1" = "-v" ]; then
	dpkg -l lcs*
fi 

if [ "$1" = "-h" ]; then
	echo "usage : lcsVersion.sh  Affiche le numero de version"
	echo "lcsVersion.sh -v  Affiche le numero de version de Lcs et tous les paquets lcs"
fi
 
