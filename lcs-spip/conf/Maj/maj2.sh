#!/bin/bash

# Module : spip 
# INDICEMAJNBR="2"
# Mise � jour de la base de donn�es

MODULE="spip"
PATHMAJ="/var/lib/lcs/$MODULE"

# Mise en place des fichiers lcs modifi�s
echo "Mise en place des fichiers lcs modifi�s."
cp $PATHMAJ/Mod/lcs/auth.php /var/www/lcs/
chown 644 /var/www/lcs/auth.php
cp $PATHMAJ/Mod/lcs/logout.php /var/www/lcs/
chown 644 /var/www/lcs/logout.php