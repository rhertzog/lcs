#!/bin/bash
# ===================================================================
# Projet LCS : Linux Communication Server
# gestion des modules par interface web
# scripts finalisation d'install/desinstall
# paramètres passés :	$1=nom du  fichier temporaire 
#			$2=nom du fichier de log

if [[ $1 =~ ^/tmp/ecran_install_+[a-z0-9-]*\.html$ ]] && [[ $2 =~ ^/usr/share/lcs/Modules/Logs/+[0-9A-Za-z_-]*\.html$ ]] ;  then
mv $1 $2
#on reset le flag install/desinstall en cours
echo "<? \$flip=0; ?>" > /usr/share/lcs/Modules/flag.php
fi
