### lcs-cas-uninstall.sh
#!/bin/bash
# lcs-cas-uninstall.sh 
# Uninstall gems rubycas-server and dependencies <http://code.google.com/p/rubycas-server/>

#
# Stop service
#
kill -9 `cat /var/run/rubycas-server/casserver.pid`
rm -f /var/run/rubycas-server/casserver.pid
#
# Uninstall gem rubycas-server
#
gem uninstall -a -I -x  rubycas-server

for i in `gem list | grep '(' | cut -d '(' -f 1`; do
        echo $i
        gem uninstall -a -I -x  $i
done
