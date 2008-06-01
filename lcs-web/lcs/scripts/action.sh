#!/bin/bash
# action.sh odronnancement d'une action sur le serveur LcSe3
# Version du 02/11/2004

#$1 action : halt, reboot, settime, update, synchro_mdp
#$2 serveur ntp

case $1 in
  halt)
    /sbin/shutdown -h now &
    exit 0;;
  reboot)
    /sbin/shutdown -r now &   
    exit 0;;  
  settime)
    /usr/sbin/ntpdate -s $2 &
    exit 0;;  
  update)
    apt-get update
    apt-get -y dist-upgrade &
    exit 0;;  
  synchro_mdp)
    cryptpasswd=`getent shadow admin | perl -ne '/:(.*)/;@result=split(/:/,$1);print $result[0];'`
    echo "admin:$cryptpasswd" > /var/www/setup/.htpasswd
    chown root:www-data /var/www/setup/.htpasswd
    chmod 640 /var/www/setup/.htpasswd  
    exit 0;;  
esac
