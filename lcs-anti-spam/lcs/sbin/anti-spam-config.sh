#!/bin/bash
#
# Activation de l'anti-spam sur LCS
# Utilisation de Spamasssin et de Procmail
# Simon CAVEY simon.cavey@crdp.ac-caen.fr
# 09/10/2008

sed -i.before-antispam s/ENABLED=0/ENABLED=1/g /etc/default/spamassassin 
echo "mailbox_command = procmail -a \"\$EXTENSION\"" >> /etc/postfix/main.cf
