#!/bin/bash
# ===================================================================
# Projet LCS : Linux Communication Server
# par Misterphi le 06/11/2010
# geston des modules par interface web
# scripts d'autorisation de mise a jour
# paramètres passés :	$1=nom du paquet 
#			$2=action(install/hold)

if [[ "$1" =~ ^lcs+[a-z0-9-]*$ ]] && [ "$2" == "install" -o "$2" == "hold" ] ;  then
	#test si module est installe
	vartest=`dpkg -l $1 | grep '\(^ii \|^hi \)' | wc -l`
	if [ $vartest -eq 1 ]; then
	echo "$1 $2" | dpkg --set-selections
	fi
fi
