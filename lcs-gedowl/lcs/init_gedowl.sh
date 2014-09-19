#!/bin/bash
cd /usr/share/lcs/Plugins/Gedowl/Documents;
rm -r *
cp -a /usr/share/lcs/Plugins/Gedowl/admin/A_LIRE.pdf /usr/share/lcs/Plugins/Gedowl/Documents/
mysql gedowl_plug < /usr/share/lcs/Plugins/Gedowl/admin/backup_owl.sql
exit 0
