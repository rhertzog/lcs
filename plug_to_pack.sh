#!/bin/bash
# ---------------------------------------------------
# plug_to_pack <nom du paquet> <version> <description>
# by misterphi  mars 2009 
#----------------------------------------------------
if [ "$1" = "--help" -o "$1" = "-h" ] || [ -z "$1" ] || [ -z "$2" ] || [ -z "$3" ]; then
        echo "Script destiné à créer l'arborescence d'un paquet Debian LCS compatible svn-build-package"
        echo ""
        echo "Usage : ./plug_to_pack <nom du paquet> <version> <description>"
        echo "L'archive nom_du_paquet-version.tgz et le fichier nom_du_paquet-version.html"
        echo "doivent etre prealablement deposes dans tgz_area"
        echo""
        echo "Exemple : ./plug_to_pack.sh cdt 1.02 'cahier de textes' "
        exit
fi

if [ -d lcs-$1 ]; then
    echo "Ce paquet existe deja !"
    exit
fi

	#----------------------
	# Creation du squelette
	#----------------------
	
	echo "creation du squelette"
	mkdir lcs-$1-$2
	
	if [  -e ./tgz_area/$1-$2.tgz ]; then
	cp ./tgz_area/$1-$2.tgz lcs-$1-$2/$1-$2.tgz
	else
	echo "Archive $1-$2.tgz non trouvee"
	exit
	fi
	
	cd lcs-$1-$2
	touch Makefile
	tar xzvf $1-$2.tgz
	#extraction du nom de repertoire de l'appli
	REP=`tar -tzf $1-$2.tgz | grep 'patchconf' | cut -d"/" -f1`
	rep=`echo $REP | tr [:upper:] [:lower:]`
	dh_make --createorig -s -n
	#nettoyage
	cd debian
	rm *.ex *.EX
	rm -r dirs docs
	cd ../../
	#ajout des modèles postinst, postrm,..
	cp -r lcs-template/* lcs-$1-$2
	rm -rf lcs-$1-$2/.svn lcs-$1-$2/*/.svn lcs-$1-$2/*/*/.svn
	rm lcs-$1-$2/$1-$2.tgz
	mv lcs-$1-$2 lcs-$1
	# recopie des fichiers modifies
	if [ -e lcs-$1/$REP/Admin/Mod ]; then
	cp  lcs-$1/$REP/Admin/Mod/* lcs-$1/conf/Mod/
	fi
	#recopie des fichiers originaux
	if [ -e lcs-$1/$REP/Admin/Orig ]; then
	cp  lcs-$1/$REP/Admin/Orig/* lcs-$1/conf/Orig/
	fi
	
	#-----------------
	#patch du postinst
	#-----------------
	
	echo "Patch du postinst"
	
	#recherche indice maj
		
	let INDICEMAJ=0
	let MAJNBR=1
        while [ -e lcs-$1/$REP/Admin/Maj/maj.$MAJNBR ]; do
            INDICEMAJ=$MAJNBR
            let MAJNBR+=1
	done

	sed -i "s/#MODUL#/$REP/g" lcs-$1/debian/postinst
	sed -i "s/#NUMMAJ#/$INDICEMAJ/g" lcs-$1/debian/postinst
	sed -i "s/#DESCR#/$3/g" lcs-$1/debian/postinst

	#---------------
	#patch du prerm
	#---------------
	
	echo "Patch du prerm"
	sed -i "s/#MODUL#/$REP/g" lcs-$1/debian/prerm
	

	#-----------------
	#patch du Makefile
	#-----------------
	
	echo "Patch du Makefile"
	sed -i "s/#MODUL#/$REP/g" lcs-$1/Makefile
	sed -i "s/#modul#/$rep/g" lcs-$1/Makefile

	#-----------------
	#patch du control
	#-----------------
	echo "Patch de Control"
	sed -i "s/Section: unknown/Section: misc/g" lcs-$1/debian/control
	sed -i "s/Priority: extra/Priority: optional/g" lcs-$1/debian/control
	sed -i "s/Architecture: all/Architecture: any/g" lcs-$1/debian/control
	sed -i "s/misc:Depends}/misc:Depends}, lcs-web/g" lcs-$1/debian/control
	sed -i "s/<insert up to 60 chars description>/Install $3 on LCS server/g" lcs-$1/debian/control
	sed -i "s/<insert long description, indented with spaces>/$3/g" lcs-$1/debian/control
	
	#----------------
	# copie de la doc
	#----------------
	
	if [ -e ./tgz_area/$1-$2.html ]; then
		echo " Transfert de la documentation"
        test=true
        share=false
        cat ./tgz_area/$1-$2.html | while ($test)
        do
            read ligne || test=false
            if [ $test = false ]; then
                exit 0;
            fi
            if [ "$ligne" = "</HEAD>" ]; then
                share=true
            fi
            if [ $share = true ]; then
                echo "$ligne" >>  lcs-$1/doc-html/index.html
            fi
            if [ "$ligne" = "</HTML>" ]; then
                share=false
            fi
        done
        
	#---------------
	#patch de la doc
	#---------------
	
	echo "Patch de la documentation"
	sed -i "s/#MODUL#/$REP/g" lcs-$1/doc-html/index.html
	else
	echo "fichier $1-$2.html introuvable"
	fi

exit 0

