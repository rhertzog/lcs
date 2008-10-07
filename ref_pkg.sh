#!/bin/bash
# Misterphi 
# Version du : 04/10/2008 
# $1 : repertoire source du paquet
# $2 : N° de version
# $3 : Branche stable, testing ou xp
# $4 : Repository :BacASable ou LCS
# $5 : (optionnel) Nécessaire uniquement si le module est nouveau dans la branche (c.a.d s'il n'existe pas de version antérieure). 
# Ce paramčtre doit correspondre ŕ la description du module qui sera insérée dans lcs_db.applis (voir le postinst)


if [ "$1" = "--help" -o "$1" = "-h" ] || [ -z "$1" ] || [ -z "$2" ] || [ -z "$3" ] || [ -z "$4"  ]; then
        echo "Script destiné referencer un paquet Debian LCS-*"
        echo ""
        echo "Usage : ref_pkg <repertoire source du paquet> <N° de Version>  <branche de destination> <Repository> <description>"
        echo "Les branches possibles sont : stable testing experimentale (ou xp) "
        echo "Les repository possibles sont BacASable ou LCS"
        echo "Exemple : ./ref_pkg.sh lcs-pla 2.1~2 xp LCS 'Administration web serveur LDAPLCS' "
        exit
fi


    
#======== Référencement du paquet si c'est un modules LCS ===================     
# by misterphi
Module=`echo $1 | cut -d- -f2`

if [ "$4" == "LCS" ];then
	cd ../../../../LCS/LCS2.0/sources/trunk/
	CheminDoc="$1/usr/share/doc/lcs/$Module/html"
	Deb="DEBIAN"
	elif [ "$4" == "BacASable" ];then
	CheminDoc="$1/doc-html"
	Deb="debian"
	else
	echo "repository errone"
	exit
fi

	

# test si le paquet est un module 
if grep -q ", 'M');\"$" $1/$Deb/postinst || grep -q ", 'S');\"$" $1/$Deb/postinst ; then
	echo "Voulez vous referencer le module $1 (O/N)"
    	read reponse
    	if [  "$reponse" = "O" ] || [  "$reponse" = "o" ] ; then
		
		#
		#mise en forme de l'url du xml
		#		
		
			if [ "$3" == "stable" ];then
  	  			depot="Lcs"
			elif [ "$3" == "testing" ];then
  	 		 	depot="LcsTesting"
			elif [ "$3" == "experimentale" -o "$3" == "xp" ];then
	   		 	depot="LcsXP"
			else 
			echo "branche erronee"
			exit
		fi

		
		
		Repcourant=`pwd`
		#
		# on télécharge le xml correspondant
		#
		cd /tmp;
		if [ -e moduleslcs.xml ]; then 
			rm /tmp/moduleslcs.xml
		fi
		echo "Telechargement du fichier xml"	
		urlxml="http://linux.crdp.ac-caen.fr/modules$depot/moduleslcs.xml"
		wget -q --cache=off $urlxml
		echo "Modification du fichier xml"
		# 
		# le paquet est-il déjŕ référencé dans le xml ?
		#
		
		if grep -q $Module moduleslcs.xml;then
			#paquet déja référencé 
			#on récupčre le n° ligne de la section 
			filtre="nom=\"$Module"
			nligne=`grep -n $filtre moduleslcs.xml | cut -d: -f1`
			#on pointe sur la ligne contenant la  version
			let nligne+=2
			#on modifie le n° de version
			cat moduleslcs.xml | sed $nligne,$nligne"s/\".*\"/\"$2\"/g" > moduleslcs.tmp
		else
			#nouveau paquet
			#affectation de l'intitulé 
			if [ -z "$5" ]; then
				intitule=$Module
			else
				intitule=$5
			fi		
			# on récupčre le n° de la derničre ligne
			filtre="</modules>"
			lastligne=`grep -n $filtre moduleslcs.xml | cut -d: -f1`
			#on crée un fichier d'insertion sed
			echo $lastligne"i\\" > section.txt
			echo "	<module nom=\"$Module\"> \\" >>section.txt
			echo "	<intitule>$intitule</intitule> \\"  >>section.txt
			echo "	<version ver=\"$2\"> \\"  >>section.txt
			echo "		<ID>1</ID> \\" >> section.txt
			echo "		<etat>1</etat> \\" >> section.txt
			echo "		<serveur type=\"http\">deb http://lcs.crdp.ac-caen.fr/etch $depot main</serveur>\\" >> section.txt
			echo "		<aide type=\"http\">http://linux.crdp.ac-caen.fr/modules$depot/Docs/$Module""_html/index.html</aide>\\" >> section.txt
			echo "		<type>M</type>\\" >> section.txt
			echo "	</version>\\" >> section.txt
			echo "	</module>\\" >> section.txt
			echo "" >> section.txt
	
			#
			#on ajoute la nouvelle section 
			#
			cat moduleslcs.xml | sed -f section.txt > moduleslcs.tmp
		fi

		#
		#on pousse le xml sur linux.crdp
		#
		echo "Transfert du fichier xml modifie "
		scp -P 2222 /tmp/moduleslcs.tmp pacman@193.49.64.20:/var/www/linux/modules$depot/moduleslcs.xml
				
		#
		#on pousse la doc du paquet sur linux.crdp
		#		
		echo "Transfert de la documentation sur le depot"
		if [ -e /tmp/$Module'_html' ]; then
			rm -rf /tmp/$Module'_html'
		fi
		cd $Repcourant
		#suppression des .svn de la doc
			
		if [ -e $CheminDoc ];then
		cp -r  $CheminDoc /tmp/$Module'_html'
		rm -rf /tmp/$Module'_html'/.svn /tmp/$Module'_html'/*/.svn
		scp -r -P 2222 /tmp/$Module'_html' pacman@193.49.64.20:/var/www/linux/modules$depot/Docs/
						
		#
		#Nettoyage 
		#
		cd /tmp
		rm moduleslcs.*
		rm -rf $Module'_html'
		if [ -e section.txt ]; then
			rm section.txt
		fi
		else
		echo "Le repertoire $cheminDoc n'existe pas "
		echo "Pas de documentation transferee" 
		fi
	fi
else
	echo " ce paquet n'est pas un module LCS, il n'a donc pas ete refrence dans le fichier xml."	
fi

#======= Fin de la section référencement du module ==========================    

