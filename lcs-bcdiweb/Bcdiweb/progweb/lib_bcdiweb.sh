#!/bin/bash

#############################################################
# Script d'installation des lib. BcdiWeb dans /usr/lib      #
#                                                           #
# Alexis Abram� - CRDP de Poitou-Charentes - nov. 2008      #
#############################################################

#A lancer en tant que SuperUtilisateur (su ou root)
repinst=/usr/lib

function accueil () {
WHOAMI=`whoami`
if [ ! $WHOAMI == "root" ]; then
    echo "Cette installation doit �tre lanc�e par l'utilisateur 'root'"
    echo "Vous pouvez forcer l'installation sans v�rification de l'utilisateur avec l'option -f"
    exit 1 
else menu
fi
}

function modif_param () {
bcdiwebdefaut=/usr/bcdiserv/progweb
emplacementbcdiweb=$bcdiwebdefaut

#Emplacement d'installation
echo "Emplacement de BcdiWeb : $emplacementbcdiweb"
echo "Nouvel emplacement (entr�e pour garder la valeur par d�faut) :"
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
    if [ -e "/usr/lib/${LIB[$i]}" ]; then echo "La biblioth�que ${LIB[$i]} existe d�j� dans le r�pertoire $repinst : copie annul�e !";
    else ln -s --target-directory=$repinst $emplacementbcdiweb/${LIB[$i]}&&echo "Cr�ation du lien symbolique (${LIB[$i]}) : ok"||echo "L'op�ration a �chou� !"
fi  
  done

}

function remove_lib (){
  LIB[1]="libstdc++-libc6.1-1.so.2"
  LIB[2]="libxerces-c1_6_0.so"
  LIB[3]="libxercesxmldom.so.1"
  for i in 1 2 3 ;do
    if [ -e "/usr/lib/${LIB[$i]}" ]; then unlink /usr/lib/${LIB[$i]}&&echo "Suppression du lien vers la  biblioth�que ${LIB[$i]} : ok"|| echo "L'op�ration a �chou� !";
    else echo "Le lien vers ${LIB[$i]} n'existe pas : op�ration annul�e !"
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
                  *   Installation des biblioth�ques de BcdiWeb   *
                  *                                               *
                  *************************************************




    1. Installation des biblioth�ques
    2. D�sinstallation des biblioth�ques
    3. Quitter

Notes :
#######
Ce programme installe dans $repinst des liens symboliques permettant � BcdiWeb
d'atteindre ses biblioth�que dans le cas o� la directive SetEnv n'est pas 
comprise par Apache.
Ce programme utilise le jeu de caract�re ISO-8859-15, r�glez votre terminal en
cons�quence.
EOF

while [ "$REPLY" != "3" ] ; do
echo -n "Votre choix : "
read
case "$REPLY" in

1 ) clear;echo "Installation des biblioth�ques"
echo ""
modif_param
install_lib
echo ""
echo "Installation termin�e"
retour
;;

2 ) clear;echo "D�sinstallation des biblioth�ques"
echo ""
remove_lib
echo ""
echo "D�sinstallation termin�e"
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

* ) echo "R�ponse incorrecte !"; retour;;

esac
}

function continuer () {
echo "Voulez-vous continuer ? (o/n)"; read reponse ; case "$reponse" in

o ) menu;;

n ) exit 0 ;;

* ) echo "R�ponse incorrecte !"; continuer;;

esac
}


###############################
# Test d'un �ventuel argument #
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

