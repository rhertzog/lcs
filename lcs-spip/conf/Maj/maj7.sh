#!/bin/bash
# Module : Release spip to version 2.1.10
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
cd /usr/share/lcs/spip/tmp
mv sessions /tmp/
rm -Rf /usr/share/lcs/spip/tmp/* > /dev/null 2>&1
mv /tmp/sessions .
echo "deny from all" >> /usr/share/lcs/spip/tmp/.htaccess


