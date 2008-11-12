#!/bin/bash

chown -R root:root /usr/share/doc/lcs/$1
chown -R root:root /var/lib/lcs/$1
chown -R www-data:www-data /usr/share/lcs/$1
chown root:lcs-users /usr/share/lcs/$1

chmod 750 /usr/share/lcs/$1
if [ -d /usr/share/lcs/$1/CACHE/ ]; then
       chmod g+w /usr/share/lcs/$1/CACHE/
fi
chmod g+w /usr/share/lcs/$1/IMG/
if [ -d /usr/share/lcs/$1/ecrire/data ]; then
    chmod g+w /usr/share/lcs/$1/ecrire/data
    chown spip.manager /usr/share/lcs/$1/ecrire/data
fi
chmod g+w /usr/share/lcs/$1/ecrire/
# Squelette spip lcs
chown -R spip.manager /usr/share/lcs/$1/lcs_skel
chown spip.manager /usr/share/lcs/$1/config/mes_options.php
chown spip.manager /usr/share/lcs/$1/lcs_skel/mes_fonctions.php
# Mise en place d'un repertoire plugins
chown -R spip.manager:www-data /usr/share/lcs/$1/plugins

chmod 600 /usr/share/lcs/$1/config/connect.php

# a partir de la version 1.9.2c il existe deux nouveaux repertoire
echo "Positionnement des droits sur les nouveaux repertoires tmp et local "
chown -R spip.manager:www-data /usr/share/lcs/$1/tmp
chmod 770 /usr/share/lcs/$1/tmp
chown -R www-data:www-data /usr/share/lcs/$1/local
