#!/bin/sh
module=`echo $1 | tr [:upper:] [:lower:]`
chown -R root:root /usr/share/doc/lcs/$module
chown -R root:root /var/lib/lcs/$1
chown -R root:www-data /usr/share/lcs/Plugins/$1
chmod -R 750 /usr/share/lcs/Plugins/$1
CHEMIN=`pwd`
cd  /usr/share/lcs/Plugins/$1
chmod  770 documents documents/archives images images/background photos artichow/cache mod_ooo/tmp lib/standalone/HTMLPurifier/DefinitionCache/Serializer 
chmod -R 770 temp mod_ooo/mes_modeles backup 
cd $CHEMIN 

