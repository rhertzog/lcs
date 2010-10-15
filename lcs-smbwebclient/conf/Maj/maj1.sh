#!/bin/bash

# Module : smbwebclient
# INDICEMAJNBR="1"
#
# Inscription du params shareview dans lcs_db.params
#
if [ `/usr/bin/mysql -se "SELECT id FROM lcs_db.params WHERE name='shareview';" | wc -l` == "0" ]; then
    echo "Inscription du params shareview dans lcs_db.params"
    /usr/bin/mysql -e "INSERT INTO lcs_db.params VALUES (NULL, 'shareview', 'Docs/Classes/Progs', '0', 'Partages Visibles avec smbwebclient', '1');"
fi
# On efface l'entr�e share_view utilis�e pour les tests
/usr/bin/mysql -e "DELETE FROM lcs_db.params WHERE name = 'share_view' ;"
