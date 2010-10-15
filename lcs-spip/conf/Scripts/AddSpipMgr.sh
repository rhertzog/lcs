#!/bin/bash
set -e

# Create spip.manager account
if ! getent passwd spip.manager >/dev/null; then
  echo "Creation compte spip.manager"
  PASS=`date | md5sum | cut -c 3-9`
  UIDPOLICY=`echo "SELECT value FROM params WHERE name='uidPolicy'" | mysql -h localhost lcs_db -N`
  echo "UPDATE params SET value='1' WHERE name='uidPolicy'" | mysql -h localhost lcs_db
  /usr/share/lcs/sbin/userAdd.pl  spip manager $PASS 00000000 M Profs
  echo "UPDATE params SET value=\"$UIDPOLICY\" WHERE name='uidPolicy'" | mysql -h localhost lcs_db
fi
exit 0
