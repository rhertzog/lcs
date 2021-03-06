<?php
# Les quatre lignes suivantes sont � modifier selon votre configuration
# ligne suivante : l'adresse de l'annuaire LDAP.
# Si c'est le m�me que celui qui heberge les scripts, mettre "localhost"
$ldap_adresse="localhost";
# ligne suivante : le port utilis�
$ldap_port="389";
# ligne suivante : l'identifiant et le mot de passe dans le cas d'un acc�s non anonyme
$ldap_login="";
# Remarque : des probl�mes li�s � un mot de passe contenant un ou plusieurs caract�res accentu�s ont d�j� �t� constat�s
$ldap_pwd="";
# ligne suivante : le chemin d'acc�s dans l'annuaire
$ldap_base="";
# ligne suivante : filtre LDAP suppl�mentaire (facultatif)
$ldap_filter="";
# Attention : si vous configurez manuellement ce fichier (sans passer par la configuration en ligne)
# vous devez tout de m�me activer LDAP en choisissant le "statut par d�faut des utilisateurs import�s".
# Pour cela, rendez-vous sur la page : configuration -> Configuration LDAP.
?>