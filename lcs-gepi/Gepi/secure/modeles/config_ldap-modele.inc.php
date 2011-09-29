<?php
#
# Vous devez renommer ce fichier en config_ldap.inc.php dans le repertoire secure pour qu'il soit pris en compte !
#

# Les lignes suivantes sont � modifier selon votre configuration

# adresse de l'annuaire LDAP.
# Si c'est le m�me que celui qui heberge les scripts, mettre "localhost"
$ldap_host="localhost";     # Exemple : localhost, 192.168.1.1

# port utilis�
$ldap_port="389";

# identifiant et mot de passe dans le cas d'un acc�s non anonyme
$ldap_login="";
$ldap_password="";

# chemin d'acc�s dans l'annuaire (= BaseDN)
# Exemple pour Eole : "o=gouv,c=fr"
$ldap_base_dn="o=gouv,c=fr";

# Compl�ment de chemin o� sont list�s les utilisateurs
# Ce param�tre est plac� devant le BaseDN lors des requ�tes.
$ldap_people_ou = "ou=People";

# Les classes de l'entr�e LDAP d'un utilisateur. Elles doivent
# �tre coh�rentes avec les attributs utilis�s.
$ldap_people_object_classes = array("top","person","inetOrgPerson");

# Diff�rents noms de champs contenant des informations indispensables
# pour Gepi. Si certaines �quivalences ne sont pas renseign�es, l'information
# ne sera pas import�e.
$ldap_champ_login = "uid";
$ldap_champ_prenom = "";
$ldap_champ_nom = "";
$ldap_champ_nom_complet = ""; 	# Si ce champ est renseign�, il sera utilis� en combinaison avec le champ
								# pr�nom ou nom pour d�terminer l'attribut manquant.
$ldap_champ_email = "";
$ldap_champ_civilite = "";
$ldap_champ_statut = "";

$ldap_code_civilite_madame = "";
$ldap_code_civilite_monsieur = "";
$ldap_code_civilite_mademoiselle = "";

# Options suppl�mentaires
# Type de cryptage utilis� pour la g�n�ration du mot de passe (acc�s en �criture) :
$ldap_password_encryption = "crypt"; # clear, crypt, md5, ssha

# Les attributs ci-dessous permettent de d�terminer quel
# statut donner � des utilisateurs import�s � la vol�e
# depuis le LDAP.
# Le test est effectu� sur la cha�ne du DN. Ces attributs
# ne sont donc utiles que dans l'hypoth�se o� le DN contient
# une information fiable quant au statut de l'utilisateur.
# Remarque : ces param�tres ne sont utilis�s que pour l'acc�s au LDAP
# en lecture (importation). L'acc�s en �criture ne prend en compte
# qu'un �ventuel attribut statut (voir $ldap_champ_statut).
$ldap_chaine_dn_statut_professeur = "";
$ldap_chaine_dn_statut_eleve = "";
$ldap_chaine_dn_statut_responsable = "";
$ldap_chaine_dn_statut_scolarite = "";
$ldap_chaine_dn_statut_cpe = "";


##
# Attributs sp�cifiques � Scribe NG
$ldap_base_dn_extension = "";

?>
