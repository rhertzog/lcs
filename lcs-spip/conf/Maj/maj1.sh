#!/bin/bash

# Module : spip 
# INDICEMAJNBR="1"
# Mise à jour de la base de données

MODULE="spip"
PATHMAJ="/var/lib/lcs/$MODULE"
PATHMOD="/usr/share/lcs/$MODULE"
#
# Cas de woody
#
if [ `grep -c "^extension=mysql.so" /etc/php4/cgi/php.ini` = "0" ]; then
    echo "extension=mysql.so" >> /etc/php4/cgi/php.ini
fi
#
# Cas de Sarge
#
if [ -e /etc/php4/cli/php.ini ] && [ `grep -c "^extension=mysql.so" /etc/php4/cli/php.ini` = "0" ]; then
    echo "extension=mysql.so" >> /etc/php4/cli/php.ini
fi
#
# sauvegarde de la base lcs-spip en cas de pépin
#
mysqldump spip_lcs > $PATHMAJ/Sql/spip_lcs_1-7-31.sql
# Maj de la base
cp $PATHMAJ/Scripts/maj-spip-cli.php3 /usr/share/lcs/spip/ecrire/
cd /usr/share/lcs/spip/ecrire/
/usr/bin/php4 maj-spip-cli.php3
rm maj-spip-cli.php3
# ajout de l'utilisateur spip manager
echo "Ajout de l'utilisateur spip.manager"
$PATHMAJ/Scripts/AddSpipMgr.sh
# nettoyage de l'ancien squelette
rm $PATHMOD/*.html > /dev/null 2>&1