#!/bin/bash
# action.sh ordonnancement d'une action sur le serveur LcSe3
# Version du 16/10/2008

#$1 action : halt, reboot, settime, update, synchro_mdp
#$2 server ntp for settime crypt passwd for synchro_mdp

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
    /usr/bin/htpasswd  -bc /var/www/setup/.htpasswd admin $2
    chown root:www-data /var/www/setup/.htpasswd
    chmod 640 /var/www/setup/.htpasswd  
    exit 0;;  
esac
