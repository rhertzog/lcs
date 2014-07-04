#!/bin/bash
# ===================================================================
# Projet LCS : Linux Communication Server
# par Misterphi le 06/11/2010
# gestion des modules par interface web
# scripts d'apt-get sudoifie
# paramètres passés :	$1=source du paquet; 
#			$2= action (install/remove);
#			$3= nom du paquet
# ====================================================================
#on recherche un depot similaire a $1
Depot=`grep \`echo $1 | cut -d"/" -f3\` /etc/apt/sources.list`
if [ -n "$Depot" ] && [ "$2" == "install" -o "$2" == "remove --purge" ] && [[ $3 =~ ^lcs- ]];  then
source="'$1'"
action="'$2'"
echo "/usr/share/lcs/scripts/inst_rm_mod.sh $source $action $3" | at now 
fi
