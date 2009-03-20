#!/bin/bash

# Module : smbwebclient
# INDICEMAJNBR="1"
#
# Inscription du params shareview dans lcs_db.params
#
echo "Inscription du params shareview dans lcs_db.params"
/usr/bin/mysql -e "INSERT INTO lcs_db.params VALUES (NULL, 'shareview', 'Docs/Classes/Progs', '0', 'Partages Visibles avec smbwebclient', '1');"
# On efface l'entrée share_view utilisée pour les tests
/usr/bin/mysql -e "DELETE FROM lcs_db.params WHERE name = 'share_view' ;"
