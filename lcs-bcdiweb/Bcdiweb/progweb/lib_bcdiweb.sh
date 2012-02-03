#!/bin/bash

#############################################################
# Script d'installation des lib. BcdiWeb dans /usr/lib      #
#                                                           #
# Alexis Abramé - CRDP de Poitou-Charentes - nov. 2008      #
#############################################################

#A lancer en tant que SuperUtilisateur (su ou root)
repinst=/usr/lib

function accueil () {
WHOAMI=`whoami`
if [ ! $WHOAMI == "root" ]; then
    echo "Cette installation doit être lancée par l'utilisateur 'root'"
    echo "Vous pouvez forcer l'installation sans vérification de l'utilisateur avec l'option -f"
    exit 1 
else menu
fi
}

function modif_param () {
bcdiwebdefaut=/usr/bcdiserv/progweb
emplacementbcdiweb=$bcdiwebdefaut

#Emplacement d'installation
echo "Emplacement de BcdiWeb : $emplacementbcdiweb"
echo "Nouvel emplacement (entrée pour garder la valeur par défaut) :"
read emplacementbcdiweb
if [ -z $emplacementbcdiweb ] 
then 
emplacementbcdiweb=$bcdiwebdefaut
fi
}


function install_lib (){
  LIB[1]="libstdc++-libc6.1-1.so.2"
  LIB[2]="libxerces-c1_6_0.so"
  LIB[3]="libxercesxmldom.so.1"
  for i in 1 2 3 ;do
    if [ -e "/usr/lib/${LIB[$i]}" ]; then echo "La bibliothèque ${LIB[$i]} existe déjà dans le répertoire $repinst : copie annulée !";
    else ln -s --target-directory=$repinst $emplacementbcdiweb/${LIB[$i]}&&echo "Création du lien symbolique (${LIB[$i]}) : ok"||echo "L'opération a échoué !"
fi  
  done

}

function remove_lib (){
  LIB[1]="libstdc++-libc6.1-1.so.2"
  LIB[2]="libxerces-c1_6_0.so"
  LIB[3]="libxercesxmldom.so.1"
  for i in 1 2 3 ;do
    if [ -e "/usr/lib/${LIB[$i]}" ]; then unlink /usr/lib/${LIB[$i]}&&echo "Suppression du lien vers la  bibliothèque ${LIB[$i]} : ok"|| echo "L'opération a échoué !";
    else echo "Le lien vers ${LIB[$i]} n'existe pas : opération annulée !"
fi  
  done

}


function menu (){ 
#############
#L'interface#
#############
clear

cat <<EOF
                  *************************************************
                  *   Installation des bibliothèques de BcdiWeb   *
                  *                                               *
                  *************************************************




    1. Installation des bibliothèques
    2. Désinstallation des bibliothèques
    3. Quitter

Notes :
#######
Ce programme installe dans $repinst des liens symboliques permettant à BcdiWeb
d'atteindre ses bibliothèque dans le cas où la directive SetEnv n'est pas 
comprise par Apache.
Ce programme utilise le jeu de caractère UTF-8, réglez votre terminal en
conséquence.
EOF

while [ "$REPLY" != "3" ] ; do
echo -n "Votre choix : "
read
case "$REPLY" in

1 ) clear;echo "Installation des bibliothèques"
echo ""
modif_param
install_lib
echo ""
echo "Installation terminée"
retour
;;

2 ) clear;echo "Désinstallation des bibliothèques"
echo ""
remove_lib
echo ""
echo "Désinstallation terminée"
retour
;;



3 ) exit 0 ;;

* ) echo "Option invalide !" 
attente
menu
;;

esac
done

}

function attente () {
count="0"
max="4"
echo -n Retour au menu
while [ $count != $max ]; do count=`expr $count + 1`
sleep 1
echo -n .
done
}

function retour () {
echo -n  "Retour au menu ? (o/n)" ; read reponse ; case "$reponse" in

o ) menu;;

n ) exit 0 ;;

* ) echo "Réponse incorrecte !"; retour;;

esac
}

function continuer () {
echo "Voulez-vous continuer ? (o/n)"; read reponse ; case "$reponse" in

o ) menu;;

n ) exit 0 ;;

* ) echo "Réponse incorrecte !"; continuer;;

esac
}


###############################
# Test d'un éventuel argument #
###############################
if [ -n "$1"  ]; then
    if [ $1 == "-f" ];then
	menu
    else
	echo "Option \"$1\" inconnue !"
	echo "usage: $0 ou $0 -f"
    fi
else
     accueil
fi

