#!/bin/bash
# Module : spip Mise à jour en version 2.0.10
#
MODULE="spip"
PATHMAJ="/var/lib/lcs/$MODULE"
#
# sauvegarde de la base lcs-spip et du repertoire spip en cas de pépin
#
mysqldump spip_lcs > $PATHMAJ/Sql/spip_lcs_1-9-2h.sql
mkdir -p $PATHMAJ/spipold
tar zcf $PATHMAJ/spipold/spip_lcs_1-9-2h.tgz /usr/share/lcs/spip
#
# Maj de la base
#
echo "Update spip base in one minute"
at now+1minutes <<END
cp $PATHMAJ/Scripts/maj-spip-cli.php /usr/share/lcs/spip/
cd /usr/share/lcs/spip/
/usr/bin/php maj-spip-cli.php
rm maj-spip-cli.php
END
#
# Nettoyage
#
rm -R /usr/share/lcs/spip/tmp/* > /dev/null 2>&1
echo "deny from all" >> /usr/share/lcs/spip/tmp/.htaccess


