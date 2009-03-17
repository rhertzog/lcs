#!/bin/bash
# synchro_comptes_lcs.sh LCS Plugin Grr V0.1 du 11/12/05
# Synchronisation de la base de comptes Grr avec la base de comptes de l'annuaire LCS
# « jLCF » Jean-Luc Chrétien <jean-luc.chretien@tice.ac-caen.fr>  

#
# Récupération des paramètres de connexion à la base Grr
#

if [ -e /usr/share/lcs/Plugins/Grr/include/connect.inc.php ]; then
  PASSWORD=`cat /usr/share/lcs/Plugins/Grr/include/connect.inc.php | grep "dbPass" | cut -d '"' -f 2`
else
  echo "Fichier de configuration de Grr inaccessible"
  exit 1
fi
MYSQLCNX="-u grr_user -p$PASSWORD"
#
# Etablisement de la liste des uids du groupe profs et administratifs
#
ldapsearch -xLL "(|(cn=Profs)(cn=Administratifs))" | grep memberUid | cut -d " " -f 2 > /tmp/list_grr
while read VAL; do
  #
  # Recherche de l'existance de l'utilisateur
  #
  TEST=`mysql -se "SELECT login FROM grr_plug.grr_utilisateurs WHERE login='$VAL'" $MYSQLCNX`
  TEST="x$TEST"
  if [ $TEST == "x" ]; then
    CN=`ldapsearch -xLLL uid=$VAL | grep cn:`
    SN=`ldapsearch -xLLL uid=$VAL | grep sn:`
    if [ `echo "$CN" | grep -c ::` = "1" ]; then
        B64=`echo "$CN" | cut -d ':' -f 3`
        PRENOM_NOM=`/usr/share/lcs/Plugins/Grr/Admin/b64.pl $B64`
    else
        PRENOM_NOM=`echo "$CN" | cut -d ':' -f 2`
    fi

    if [ `echo "$SN" | grep -c ::` = "1" ]; then
        B64=`echo "$SN" | cut -d ':' -f 3`
        NOM=`/usr/share/lcs/Plugins/Grr/Admin/b64.pl $B64`
    else
        NOM=`echo "$SN" | cut -d ':' -f 2`
    fi
    PRENOM=`echo ${PRENOM_NOM/$NOM}`

    MEL=`ldapsearch -xLLL uid=$VAL | grep mail | cut -d ":" -f 2 | cut -d " " -f 2`
    #
    # Insertion dans la table si il n'est pas présent
    #
    echo "INSERT INTO grr_plug.grr_utilisateurs VALUES ('$VAL', '$NOM', '$PRENOM', '', '$MEL', 'utilisateur', 'actif', 0, 0, '', '', '', 'ext');" | mysql $MYSQLCNX
  fi
done < /tmp/list_grr
#
# Recherche des comptes Grr non présents dans l'annuaire et effacement de ceux-ci de la base Grr
#
mysql -se "SELECT login FROM grr_plug.grr_utilisateurs" $MYSQLCNX > /tmp/list_grr
while read VAL; do
  if [ `ldapsearch -xLL uid=$VAL | grep -c uid:`  = 0 ]; then
    echo " DELETE FROM grr_plug.grr_utilisateurs WHERE login = '$VAL';" | mysql $MYSQLCNX     
  fi
done < /tmp/list_grr
#
# Nettoyage
# 
rm /tmp/list_grr
