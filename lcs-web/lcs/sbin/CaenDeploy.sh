#!/bin/bash
# Simon CAVEY  18/10/2007
# Recupertation et execution script de fin d'install / migration
# LCS Academie de Caen

cd /tmp
echo "Recuperation du script LCS Caen"
wget -q ftp://193.49.66.4/lcs/install/RemoteDeploy.sh
if [ $?=0 ]; then
	chmod +x RemoteDeploy.sh
	./RemoteDeploy.sh
	echo "Remote Deploy lance le" `date` >> /var/log/lcs/remotedeploy
	rm /tmp/RemoteDeploy.sh
else
	echo "Erreur pas d'acces au script distant"
	exit 1
fi