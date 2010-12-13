#!/bin/bash
# lcs-cas-update.sh 
# remove rubycas-lcs and install rubycas-server <http://code.google.com/p/rubycas-server/>
#
CASINSTALL=`gem list | grep rubycas-lcs`
if [ ! -z "$CASINSTALL" ]; then
	LOGFOLDER="/var/log/rubycas-server"
	CONF="/etc/rubycas-server"
	RUN="/var/run/rubycas-server"
	USERRUN="casserver"
	GROUPRUN="casserver"
	USERHOME="/var/lib/lcs/cas"
	PATH_RUBYCAS_CERT=$CONF
	IN_CONFIG_PATH=$USERHOME
	# Kill service CAS and clean
	kill -9 `cat /var/run/rubycas-lcs/casserver.pid`
	rm -rf /var/run/rubycas-lcs
	rm -rf /var/log/casserver
	mkdir $CONF
	mv /etc/rubycas-lcs/server* $CONF/
	rm -rf $CONF
	#
	# Fix owner on folders and files rubycas
	#
	if [ ! -d $LOGFOLDER ]; then
		mkdir -p $LOGFOLDER
		touch $LOGFOLDER/$USERRUN.log
	fi
	chmod 750 $LOGFOLDER
	chmod 640 $LOGFOLDER/$USERRUN.log
	chown $USERRUN:$GROUPRUN $LOGFOLDER -R
	if [ ! -d $RUN ]; then
		mkdir $RUN
		chown -R $USERRUN:$GROUPRUN $RUN
	fi
	# Remove des gems
	#activerecord (2.3.4)
	#activesupport (2.3.4)
	#builder (2.1.2)
	#markaby (0.5)
	#mysql (2.7)
	#picnic (0.7.1)
	#ruby-net-ldap (0.0.4)
	#rubycas-lcs (0.7.1.2)
	gem uninstall rubycas-lcs --ignore-dependencies --quiet -x
	gem uninstall ruby-net-ldap --ignore-dependencies --quiet
	gem uninstall picnic --ignore-dependencies --quiet
	gem uninstall mysql --ignore-dependencies --quiet
	gem uninstall markaby  --ignore-dependencies --quiet
	gem uninstall builder --ignore-dependencies --quiet
	gem uninstall activesupport --ignore-dependencies --quiet
	gem uninstall activerecord --ignore-dependencies --quiet
	#
	# rubycas-server install
	#
	#echo "rubycas-server installation in one minute"
	#at now+1minutes <<END
	#/usr/sbin/lcs-cas-install.sh
	#END
	/usr/sbin/lcs-cas-install.sh
fi
exit 0