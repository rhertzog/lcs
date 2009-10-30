#!/bin/bash
# creation d un fichier forward
# $1 : adresse mail perso
# $2 : login
# $3 : adresse mail locale
# 27/10/2009

if [ "$1" != "aucune" ]; then
echo $1 > /home/$2/.forward
if [ "$3" != "coucou" ]; then
echo $3 >> /home/$2/.forward
fi
else
rm -f /home/$2/.forward
fi
