#!/bin/bash
#
# Suppression anti-spam LCS
# Simon CAVEY simon.cavey@crdp.ac-caen.fr
# 09/10/2008

sed -i'' s/ENABLED=1/ENABLED=0/g /etc/default/spamassassin
rm /etc/default/spamassassin.before-antispam
sed -i'' '/mailbox_command = procmail -a \"\$EXTENSION\"/d' /etc/postfix/main.cf

