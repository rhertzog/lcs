#!/bin/bash
# lcs-cas-update.sh 
# remove old rubycas-server and dependencies  <http://code.google.com/p/rubycas-server/>
#
/usr/sbin/lcs-cas-uninstall.sh
#
# Reinstall rubycas-server
#
/usr/sbin/lcs-cas-install.sh
#
# Re-Start rubycas-lcs service
#
invoke-rc.d rubycas-lcs start

exit 0