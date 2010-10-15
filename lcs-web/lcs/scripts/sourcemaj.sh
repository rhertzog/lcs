#!/bin/bash
# sourcemaj.sh 
# Changement de la source des mises à jour LCS
# parametres : xp, testing, stable

#Couleurs
COLJAUNE="\033[1;33m"   # Jaune
COLVERT="\033[1;32m"    # Vert
COLBLANC="\033[1;37m"   # Blanc
COLROUGE="\033[1;31m"   # Rouge

# Sources
# Des màj différentielles :
# a) le dépot expérimental :
#   (urlmajtgz) : ftp://debian.crdp.ac-caen.fr/debian/LcsMaj/LcsMajTGZ-XP
#   (urlmajmd5) : ftp://193.49.66.4/LcsMajMD5-XP
# b) le dépot testing :
#   (urlmajtgz) : ftp://debian.crdp.ac-caen.fr/debian/LcsMaj/LcsMajTGZ-TST
#   (urlmajmd5) : ftp://193.49.66.4/LcsMajMD5-TST
# c) Le dépot stable :
#   (urlmajtgz) : ftp://debian.crdp.ac-caen.fr/debian/LcsMaj/LcsMajTGZ
#   (urlmajmd5) : ftp://193.49.66.4/LcsMajMD5
#
# Des paquets modules
# a) le dépot expérimental :
#    deb ftp://debian.crdp.ac-caen.fr/debian sarge LcsXP
# b) le dépot testing :
#    deb ftp://debian.crdp.ac-caen.fr/debian sarge LcsTesting
# c) Le dépot stable :
#    deb ftp://debian.crdp.ac-caen.fr/debian sarge Lcs

DEBMODULE="deb ftp://debian.crdp.ac-caen.fr/debian sarge Lcs"
URLMAJTGZ="ftp://debian.crdp.ac-caen.fr/debian/LcsMaj/LcsMajTGZ"
URLMAJMD5="ftp://193.49.66.4/LcsMajMD5"

if [ "$1" = "--help" -o "$1" = "-h" ] || [ -z $1 ]; then
        echo "Script permettant de modifier la source des mises à jour LCS"
        echo ""
        echo "Usage : sourcemaj.sh source"
        echo "Avec source egal a :"
        echo -e "$COLROUGE\c"
        echo "xp : source experimentale"
        echo -e "$COLJAUNE\c"
        echo "testing : source testing"
        echo -e "$COLVERT\c"
        echo "stable : source stable"
        echo -e "$COLBLANC\c"
        exit
elif [ $1 == "xp" ]; then
     grep -v "$DEBMODULE" /etc/apt/sources.list >> /tmp/sources.list
     mv /tmp/sources.list /etc/apt/sources.list
     echo $DEBMODULE"XP" >> /etc/apt/sources.list
     #
     URLMAJTGZ="$URLMAJTGZ"-XP
     URLMAJMD5="$URLMAJMD5"-XP
     mysql -e "UPDATE lcs_db.params SET value = '$URLMAJTGZ' WHERE name = 'urlmajtgz';"
     mysql -e "UPDATE lcs_db.params SET value = '$URLMAJMD5' WHERE name = 'urlmajmd5';"
     # On affiche la source courante
     echo -e "$COLROUGE\c"
     echo "Source maj experimentale"
elif [ $1 == "testing" ]; then
     grep -v "$DEBMODULE" /etc/apt/sources.list >> /tmp/sources.list
     mv /tmp/sources.list /etc/apt/sources.list
     echo $DEBMODULE"Testing" >> /etc/apt/sources.list
     #
     URLMAJTGZ="$URLMAJTGZ"-TST
     URLMAJMD5="$URLMAJMD5"-TST
     mysql -e "UPDATE lcs_db.params SET value = '$URLMAJTGZ' WHERE name = 'urlmajtgz';"
     mysql -e "UPDATE lcs_db.params SET value = '$URLMAJMD5' WHERE name = 'urlmajmd5';"
     # On affiche la source courante
     echo -e "$COLJAUNE\c"
     echo "Source maj testing"
elif [ $1 == "stable" ]; then
     grep -v "$DEBMODULE" /etc/apt/sources.list >> /tmp/sources.list
     mv /tmp/sources.list /etc/apt/sources.list
     echo $DEBMODULE >> /etc/apt/sources.list
     #
     mysql -e "UPDATE lcs_db.params SET value = '$URLMAJTGZ' WHERE name = 'urlmajtgz';"
     mysql -e "UPDATE lcs_db.params SET value = '$URLMAJMD5' WHERE name = 'urlmajmd5';"     
     # On affiche la source courante
     echo -e "$COLVERT\c"
     echo "Source maj stable"
else
    echo -e "$COLROUGE\c"
    echo "Parametre inconnu, tapez sourcemaj.sh -h pour obtenir de l'aide !"
fi

echo -e "$COLBLANC\c"