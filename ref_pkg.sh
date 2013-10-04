#!/bin/bash
# Misterphi 
# Version du : 04/11/2011 
# $1 : repertoire source du paquet ( doit commencer obligatoirement par lcs- )  
# $2 : N° de version
# $3 : Distribution : squeeze
# $4 : Branche : stable, testing ou xp
# $5 : (optionnel) Necessaire uniquement si le module est nouveau dans la branche (c.a.d s'il n'existe pas de version anterieure). 
# Ce parametre doit correspondre a la description du module qui sera inseree dans lcs_db.applis (voir le postinst)


if [ "$1" = "--help" -o "$1" = "-h" ] || [ -z "$1" ] || [ -z "$2" ] || [ -z "$3" ] || [ -z "$4" ] ; then
        echo "Script destine referencer un paquet Debian LCS-*"
        echo ""
        echo "Usage : ref_pkg <repertoire source du paquet> <N° de Version> <Distribution> <branche de destination> <description>"
        echo "Les distributions possibles sont etch, lenny ou squeeze"
        echo "Les branches possibles sont : stable testing experimentale (ou xp) "
        echo "Exemple : ./ref_pkg.sh lcs-pla 2.1~2 lenny xp 'Administration web serveur LDAPLCS' "
        exit
fi

    
#======== Referencement du paquet si c'est un modules LCS ===================     
# by misterphi
Mod=$1
Module=`echo ${Mod:4}`
CheminDoc="$1/doc-html"
Deb="debian"

# test si le paquet est un module
if grep -q ", 'M');\"$" $1/$Deb/postinst || grep -q ", 'S');\"$" $1/$Deb/postinst || grep -q ", 'N');\"$" $1/$Deb/postinst ; then
	echo "Voulez vous referencer le module $1 (O/N)"
    	read reponse
    	if [  "$reponse" = "O" ] || [  "$reponse" = "o" ] ; then
		
		#
		#mise en forme de l'url du xml
		#		
		if [ "$3" == "wheezy" ];then
			if [ "$4" == "stable" ];then
  	  			depot="Lcswheezy"
  	  			br="Lcs"
			elif [ "$4" == "testing" ];then
  	 		 	depot="LcswheezyTesting"
  	 		 	br="LcsTesting"
			elif [ "$4" == "experimentale" -o "$4" == "xp" ];then
	   		 	depot="LcswheezyXP"
	   		 	br="LcsXP"
			else 
			echo "branche erronee"
			exit 
			fi
		elif [ "$3" == "lenny" ];then
			if [ "$4" == "stable" ];then
  	  			depot="Lennycs"
  	  			br="Lcs"
			elif [ "$4" == "testing" ];then
  	 		 	depot="LennycsTesting"
  	 		 	br="LcsTesting"
			elif [ "$4" == "experimentale" -o "$4" == "xp" ];then
	   		 	depot="LennycsXP"
	   		 	br="LcsXP"
			else 
			echo "branche erronee"
			exit 
			fi

		elif [ "$3" == "squeeze" ];then
                        if [ "$4" == "stable" ];then
                                depot="Lcsqueeze"
                                br="Lcs"
                        elif [ "$4" == "testing" ];then
                                depot="LcsqueezeTesting"
                                br="LcsTesting"
                        elif [ "$4" == "experimentale" -o "$4" == "xp" ];then
                                depot="LcsqueezeXP"
                                br="LcsXP"
                        else 
                        echo "branche erronee"
                        exit 
                        fi

		else
		echo "Distribution erronee"
		exit 
		fi
		
		
		Repcourant=`pwd`
		#
		# on telecharge le xml correspondant
		#
		cd /tmp;
		if [ -e moduleslcs.xml ]; then 
			rm /tmp/moduleslcs.xml
		fi
		echo "Telechargement du fichier xml"	
		urlxml="http://linux.crdp.ac-caen.fr/modules$depot/moduleslcs.xml"
		wget  -q --cache=off $urlxml
		#
		#verification presence xml
		#
		if [ ! -e moduleslcs.xml ]; then 
                echo "Echec du telechargement du fichier xml"
		exit
                fi
		taille=`stat -c '%s' moduleslcs.xml`
		if [ "$taille" = 0  ]; then
		echo "Fichier xml vide !!"
		exit
		fi
		echo "Modification du fichier xml"
		# 
		# le paquet est-il deja reference dans le xml ?
		#
		
		if grep -q $Module moduleslcs.xml;then
			#paquet deja reference 
			#on recupere le n° ligne de la section 
			filtre="nom=\"$Module"
			nligne=`grep -n $filtre moduleslcs.xml | cut -d: -f1`
			#on pointe sur la ligne contenant la  version
			let nligne+=2
			#on modifie le n° de version
			cat moduleslcs.xml | sed $nligne,$nligne"s/\".*\"/\"$2\"/g" > moduleslcs.tmp
		else
			#nouveau paquet
			#affectation de l'intitule 
			if [ -z "$5" ]; then
				intitule=$Module
			else
				intitule=$5
			fi		
			# on recupere le n° de la derničre ligne
			filtre="</modules>"
			lastligne=`grep -n $filtre moduleslcs.xml | cut -d: -f1`
			#on cree un fichier d'insertion sed
			echo $lastligne"i\\" > section.txt
			echo "	<module nom=\"$Module\"> \\" >>section.txt
			echo "	<intitule>$intitule</intitule> \\"  >>section.txt
			echo "	<version ver=\"$2\"> \\"  >>section.txt
			echo "		<ID>1</ID> \\" >> section.txt
			echo "		<etat>1</etat> \\" >> section.txt
			echo "		<serveur type=\"http\">deb http://lcs.crdp.ac-caen.fr/$3 $br main</serveur>\\" >> section.txt
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
		svn export --force  $CheminDoc /tmp/$Module'_html'
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

#======= Fin de la section refeerencement du module ==========================    

