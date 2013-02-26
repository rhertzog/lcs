#!/bin/sh
cd /tmp
wget --passive-ftp  ftp://ftp.ut-capitole.fr/pub/reseau/cache/squidguard_contrib/blacklists.tar.gz
tar -xzf blacklists.tar.gz -C /var/lib/squidguard/db/
rm blacklists.tar.gz
if [ ! -e /var/lib/squidguard/db/blacklists/lcs/lcs.db ]; then
  /usr/bin/squidGuard -C all 
else
  /usr/bin/squidGuard -u
fi
chown proxy.www-data /var/lib/squidguard/db/blacklists -R
chmod g+x /var/lib/squidguard/db/blacklists
chmod g+x /var/lib/squidguard/db/blacklists/*
chmod g+w /var/lib/squidguard/db/blacklists -R
