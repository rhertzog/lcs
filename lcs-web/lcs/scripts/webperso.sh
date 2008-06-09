#!/bin/bash
# Ouverture/Fermeture de l'espace web perso 29/03/2004
# $1 : login
# $2 : 1 = Ouverture, 0 = Fermeture


# Si $2 1 Ouverture de l'espace web
if [ $2 == "1" ]; then
    chown -R $1:lcs-users /home/$1/public_html
    chmod 770 /home/$1/public_html
else 
    # Fermeture de l'espace web en �criture
    chown -R root:root /home/$1/public_html
    chmod 755 /home/$1/public_html
    mv /home/$1/public_html/index.html /home/$1/public_html/index.html.sav
    cp /etc/skel/public_html/index.html /home/$1/public_html/
    chmod 664 /home/$1/public_html/index.html
fi


exit 0
