#!/bin/bash
# Module : spip 
#
MODULE="spip"
PATHMAJ="/var/lib/lcs/$MODULE"
#
# sauvegarde de la base lcs-spip et du repertoire spip en cas de pépin
#
mysqldump spip_lcs > $PATHMAJ/Sql/spip_lcs_1-9-1.sql
mkdir $PATHMAJ/spipold
tar zcf $PATHMAJ/spipold/spip_lcs_1-9-1.tgz /usr/share/lcs/spip
#
# Maj de la base
#
echo "Update de la bdd spip"
cp $PATHMAJ/Scripts/maj-spip-cli.php /usr/share/lcs/spip/
cd /usr/share/lcs/spip/
/usr/bin/php4 maj-spip-cli.php
#
# Nettoyage
#
rm maj-spip-cli.php
rm -R /usr/share/lcs/spip/CACHE > /dev/null 2>&1
rm -R /usr/share/lcs/spip/ecrire/data > /dev/null 2>&1

