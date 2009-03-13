#!/bin/bash
# lcs-cas-update.sh 
# update rubycas-lcs base on rubycas-server <http://code.google.com/p/rubycas-server/>
#
cd /var/lib/lcs/cas/
if [ -e rubycas-lcs-latest.gem  ]; then
  rm rubycas-lcs-latest.gem
fi
wget http://lcs.crdp.ac-caen.fr/gems/rubycas-lcs-latest.gem
if [ -e rubycas-lcs-latest.gem  ]; then
    gem install rubycas-lcs-latest.gem
    VER=`gem list | grep rubycas-lcs | cut -d '(' -f 2 | cut -d ',' -f 1 | cut -d ')' -f 1`
    mv rubycas-lcs-latest.gem rubycas-lcs-$VER.gem
else
  echo "ERROR no rubycas-lcs-latest gem to update !"
  exit 1
fi
#
# Restart cas service
#
invoke-rc.d rubycas-lcs restart
exit 0
