#!/bin/bash
# Module : spip 
#
MODULE="spip"
PATHMAJ="/var/lib/lcs/$MODULE"
#
# sauvegarde de la base lcs-spip en cas de pépin
#
mysqldump spip_lcs > $PATHMAJ/Sql/spip_lcs_1-8-3.sql
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
