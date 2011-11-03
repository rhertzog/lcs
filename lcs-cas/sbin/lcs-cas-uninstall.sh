### lcs-cas-uninstall.sh
#!/bin/bash
# lcs-cas-uninstall.sh 
# Uninstall CAS service on LCS base on rubycas-server <http://code.google.com/p/rubycas-server/>

#
# rubycas-server configuration and path
#

gem uninstall -a -I -x  rubycas-server
gem uninstall  -a -I -x mysql
gem uninstall  -a -I -x ruby-net-ldap
gem uninstall  -a -I -x picnic
gem uninstall  -a -I -x markaby
gem uninstall  -a -I -x activerecord
gem uninstall  -a -I -x activesupport
gem uninstall  -a -I -x rack
gem uninstall  -a -I -x locale
gem uninstall  -a -I -x gettext
gem uninstall  -a -I -x builder
