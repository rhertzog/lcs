#!/bin/bash
#
# Script de nettoyage Lcs
# 19/10/2007 
# Simon CAVEY     simon.cavey@crdp.ac-caen.fr
#

## suppression des log ldap superflux
db4.2_archive -d
## suppression du cache des packets debian
apt-get clean

## suppression des anciennes sauvegardes en cas de probleme amanda :
annee=`date +%Y`
mois=`date +%m`

if [ $mois -le 10 ]; then
    moismoins1=0$((mois - 1));
else 
    moismoins1=$((mois - 1));
fi
if [ -d  ]; then
    for i in `ls -d /var/backups/$annee$moismoins1?? 2>/dev/nul`
    do
        #echo "suppression de $i"
        rm -rf $i
    done
fi