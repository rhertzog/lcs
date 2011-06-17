#!/bin/sh

cd /home
NBRHOMES=`ls -l | wc -l`
ldapsearch -xLLL uid ou=people | grep uid: | cut -d ' ' -f 2 | while read PEOPLE
do
       if [ ! -d /home/$PEOPLE ]; then
                mkdir /home/$PEOPLE
                chown root:lcs-users $PEOPLE
                chmod 750 $PEOPLE
                cp -r /etc/skel/Maildir /home/$PEOPLE/
                chown -R $1:lcs-users /home/$PEOPLE/Maildir
                chmod -R 700 /home/$PEOPLE/Maildir
       fi
done
NBRHOMESAFTER=`ls -l |wc -l`
NBRCREATE=$(($NBRHOMESAFTER - $NBRHOMES))
echo "$NBRCREATE"
