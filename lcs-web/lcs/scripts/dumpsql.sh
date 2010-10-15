#!/bin/bash
# sauvegarde des bases de donnees MySQL en .sql
# Simon CAVEY     simon.cavey@crdp.ac-caen.fr
## creation du depot de backup
mkdir -p /var/backups/sql/
if [ -e /var/backups/old_sql.tgz ]; then
    tar czf /var/backups/old_sql.tgz /var/backups/sql/*.sql
fi
mysql -N -e "show databases;" | cut -d " " -f 2 | while true; do
read i
if [ -z "$i" ]; then
        break
fi
if [ "$i" != "Database" ]; then
        mysqldump $i >/var/backups/sql/$i.sql
fi
done