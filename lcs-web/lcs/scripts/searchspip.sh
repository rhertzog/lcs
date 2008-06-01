#!/bin/bash
HOSTNAME=`hostname -f`
LAN=`grep ^mynetworks /etc/postfix/main.cf | cut -d "=" -f 2 | sed -e 's/,//g'`

if [ "$1" = "--help" -o "$1" = "-h" ]
then
	echo "Recherche les spip en version < � argument et place un .htacess si la version est inferieure "
	echo "Usage : numero de version"
	exit
fi

if [ -z "$1" ];
then
    echo "Veuillez pr�ciser un num�ro de version"
    exit
fi

find /home/ -name 'inc_version.php*' | while read SPIPDESTVER 
do
    #echo "SPIP : $SPIPDESTVER" 
    VER=`grep 'spip_version =' $SPIPDESTVER | cut -d ' ' -f 3 | cut -d ';' -f 1`
    #echo "VER : $VER"
    if [ $VER != $1 ]; then
        SPIPREP=`echo "$SPIPDESTVER" | sed -e "s/ecrire.*\/inc_version.php.*//"`
	echo "la version de spip h�berg�e en : $SPIPREP n'est pas � jour !"
        # On d�pose un .htaccess
        echo "order deny,allow" > $SPIPREP/.htaccess
        echo "deny from all" >> $SPIPREP/.htaccess
        echo "allow from $LAN" >> $SPIPREP/.htaccess
        echo "ErrorDocument 403 /Err/spipversion.html" >> $SPIPREP/.htaccess
        chmod 777 $SPIPREP/.htaccess
	# On poste un mail a l'admin
	echo -e "Bonjour,\n
La version de spip h�berg�e sur $HOSTNAME dans le r�pertoire $SPIPREP, n'est pas � jour et pr�sente une faille de s�curit�.\n
L'acc�s � ce site, est d�sormais interdit depuis Internet.\n
Pour r�tablir l'acc�s, il vous faudra :\n
- Mettre � jour cette version de spip (voir : http://www.spip.net/fr_download);\n
- Supprimer le fichier .htaccess, situ� en $SPIPREP\n
\n
Pour toute question ou information, merci de bien vouloir nous contacter � l'adresse suivante : LcsDevTeam@tice.ac-caen.fr
"| mail -s "Controle version de spip hebergee dans les homes" admin 

    fi
done