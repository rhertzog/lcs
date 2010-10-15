#!/bin/bash
# Import de la topologie de l'etablissement dans la base maint_plug.topologie
# version du 23/12/04
CONF="/usr/share/lcs/Plugins/Maintenance/Includes/config.inc.php"
#
# Recherche du mdp de la base maint_plug
#
if [ -e $CONF ]; then
  PASSAUTH=`cat $CONF | grep "PASSAUTH.*=" | cut -d = -f 2 |cut -d \" -f 2`
else
  echo "Fichier de conf inaccessible"
  exit 1
fi
#
# Traitement
#
if [ -e $1 ]; then
  #
  # Connection a la base
  #
  MYSQLCNX="mysql -h localhost maint_plug -u maint_user -p$PASSAUTH -N"
  #
  # Purge de la table topologie avant import
  #
  echo "TRUNCATE TABLE topologie" | $MYSQLCNX 
  #
  # Import dans la base
  #
  while read CIBLE; do
    echo $CIBLE > /tmp/config.tmp
    BAT=`cut -d\; /tmp/config.tmp -f2  | cut -d " " -f2`
    ETAGE=`cut -d\; -f3 /tmp/config.tmp`
    SALLE=`cut -d\; -f4 /tmp/config.tmp`
    #echo "$BAT | $ETAGE | $SALLE"
    echo "INSERT INTO topologie ( id , batiment , etage , salle ) VALUES ('', '$BAT', '$ETAGE', '$SALLE');" | $MYSQLCNX
  done < $1
  #
  # Nettoyage
  #
  rm /tmp/config.tmp
else
  echo "Fichier d'import inaccessible"
  exit 1
fi
