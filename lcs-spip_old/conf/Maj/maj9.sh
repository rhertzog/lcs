#!/bin/bash
# Module : Release spip to version 2.1.19
#
MODULE="spip"
PATHMAJ="/var/lib/lcs/$MODULE"
#
# Update spip base
#
echo "Update spip base in one minute"
at now+1minutes <<END
cp $PATHMAJ/Scripts/maj-spip-cli.php /usr/share/lcs/spip/
cd /usr/share/lcs/spip/
/usr/bin/php maj-spip-cli.php
rm maj-spip-cli.php
END
#
# Cleaning spip/tmp
#
cd /usr/share/lcs/spip
mkdir -p tmp_new/cache
chown spip.manager:www-data tmp_new
chmod 770 tmp_new
chown -R www-data:www-data tmp_new/cache
chmod 777 tmp_new/cache
mv tmp/dump tmp_new/
mv tmp/*.log tmp_new/
rm -Rf tmp
mv tmp_new tmp
echo "deny from all" >> /usr/share/lcs/spip/tmp/.htaccess


