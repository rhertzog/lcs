#!/bin/bash

# Module : spip 
# INDICEMAJNBR="3"
MODULE="spip"
PATHMAJ="/var/lib/lcs/$MODULE"
#
# sauvegarde de la base lcs-spip en cas de pépin
#
mysqldump spip_lcs > $PATHMAJ/Sql/spip_lcs_1-8-2g.sql
# Maj de la base
cp $PATHMAJ/Scripts/maj-spip-cli.php3 /usr/share/lcs/spip/ecrire/
cd /usr/share/lcs/spip/ecrire/
/usr/bin/php4 maj-spip-cli.php3
rm maj-spip-cli.php3
# Mise en place des fichiers lcs modifiés
echo "Mise en place des fichiers lcs modifiés."
cp $PATHMAJ/Mod/lcs/auth.php /var/www/lcs/
chown 644 /var/www/lcs/auth.php
cp $PATHMAJ/Mod/lcs/logout.php /var/www/lcs/
chown 644 /var/www/lcs/logout.php