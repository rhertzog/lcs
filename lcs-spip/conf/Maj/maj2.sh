#!/bin/bash

# Module : spip 
# INDICEMAJNBR="2"
# Mise à jour de la base de données

MODULE="spip"
PATHMAJ="/var/lib/lcs/$MODULE"

# Mise en place des fichiers lcs modifiés
echo "Mise en place des fichiers lcs modifiés."
cp $PATHMAJ/Mod/lcs/auth.php /var/www/lcs/
chown 644 /var/www/lcs/auth.php
cp $PATHMAJ/Mod/lcs/logout.php /var/www/lcs/
chown 644 /var/www/lcs/logout.php