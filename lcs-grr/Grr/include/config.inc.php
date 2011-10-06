<?php
/**
 * config.inc.php
 * Fichier de configuration de GRR
 * Ce script fait partie de l'application GRR
 * Derni�re modification : $Date: 2009-12-02 20:11:08 $
 * @author    Laurent Delineau <laurent.delineau@ac-poitiers.fr>
 * @copyright Copyright 2003-2008 Laurent Delineau
 * @link      http://www.gnu.org/licenses/licenses.html
 * @package   root
 * @version   $Id: config.inc.php,v 1.7 2009-12-02 20:11:08 grr Exp $
 * @filesource
 *
 * This file is part of GRR.
 *
 * GRR is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * GRR is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with GRR; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

/*
Probl�me de sessions qui expirent pr�matur�ment :
Chez certains prestataire qui utilisent des serveurs en clustering, il arrive que les sessions expirent al�atoirement.
Une solution consiste � enregistrer les sessions PHP dans un autre r�pertoire que le r�pertoire par d�faut.
Pour cela, il suffit de d�commenter la ligne suivante (en supprimant le premier caract�re #)
et en indiquant � la place de "le_chemin_de_stockage_de_la_session", l'emplacement du nouveau dossier de stockage des sessions.
*/
# ini_set ('session.save_path' , 'le_chemin_de_stockage_de_la_session');


/*
$nb_year_calendar permet de fixer la plage de choix de l'ann�e dans le choix des dates de d�but et fin des r�servations
La plage s'�tend de ann�e_en_cours - $nb_year_calendar � ann�e_en_cours + $nb_year_calendar
Par exemple, si on fixe $nb_year_calendar = 5 et que l'on est en 2005, la plage de choix de l'ann�e s'�tendra de 2000 � 2010
*/
$nb_year_calendar = 10;

# Avance en nombre d'heure du serveur sur les postes clients
# Le param�tre $correct_diff_time_local_serveur permet de corriger une diff�rence d'heure entre le serveur et les postes clients
# Exemple : si Grr est install� sur un serveur configur� GMT+1 alors qu'il est utilis� dans un pays dont le fuseau horaire est GMT-5
# Le serveur a donc six heures d'avance sur les postes clients
# On indique alors : $correct_diff_time_local_serveur=6;
$correct_diff_time_local_serveur=0;

/* Param�trage du fuseau horaire (imposer � GRR un fuseau horaire diff�rent de celui du serveur)
 TZ (Time Zone) est une variable permettant de pr�ciser dans quel fuseau horaire, GRR travaille.
 L'ajustement de cette variable TZ permet au programme GRR de travailler dans la zone de votre choix.
 la valeur � donner � TZ diff�re d'un syst�me � un autre (Windows, Linux, ...)
 Par exemple, sur un syst�me Linux, si on d�sire retarder de 7 heures l'heure syst�me de GRR, on aura :
 putenv("TZ=posix/Etc/GMT-7")
 Remarque : putenv() est la fonction php  qui permet de fixer la valeur d'une variable d'environnement.
 Cette valeur n'existe que durant la vie du script courant, et l'environnement initial sera restaur� lorsque le script sera termin�.
 En r�sum�, pour activer cette fonctionnalit�, d�commentez la ligne suivante (en supprimant le premier caract�re #,
 et remplacez -7 par +n ou -n o� "n" est le nombre d'heures d'avance ou de retard de GRR sur l'heure syst�me du serveur.
*/
#putenv("TZ=posix/Etc/GMT-7");

# Changement d'heure �t�<->hiver
# $correct_heure_ete_hiver = 1 => GRR prend en compte les changements d'heure
# $correct_heure_ete_hiver = 0 => GRR ne prend en compte les changements d'heure
# Par d�faut ($correct_heure_ete_hiver non d�finie) GRR prend en compte les changements d'heure.
$correct_heure_ete_hiver = 1;

# Affichage d'un domaine par defaut en fonction de l'adresse IP de la machine cliente (voir documentation)
# Mettre 0 ou 1 pour d�sactiver ou activer la fonction dans la page de gestion des domaines
define('OPTION_IP_ADR', 1);

# Nom de la session PHP.
# Le nom de session fait r�f�rence � l'identifiant de session dans les cookies.
# Il ne doit contenir que des caract�res alpha-num�riques; si possible, il doit �tre court et descriptif.
# Normalement, vous n'avez pas � modifier ce param�tre.
# Mais si un navigateur est amen� � se connecter au cours de la m�me session, � deux sites GRR diff�rents,
# ces deux sites GRR doivent avoir des noms de session diff�rents.
# Dans ce cas, il vous faudra changer la valeur GRR ci-dessous par une autre valeur.
define('SESSION_NAME', "GRR");

# Nombre maximum (+1) de r�servations autoris�s lors d'une r�servation avec p�riodicit�
$max_rep_entrys = 365 + 1;

# Positionner la valeur $unicode_encoding � 1 pour utiliser l'UTF-8 dans toutes les pages et dans la base
# Dans le cas contraire, les textes stock�s dans la base d�pendent des diff�rents encodage selon la langue selectionn�e par l'utilisateur
# Il est fortement conseill� de lire le fichier notes-utf8.txt � la racine de cette archive
$unicode_encoding = 0;

# Apr�s installation de GRR, si vous avez le message "Fatal error: Call to undefined function: mysql_real_escape_string() ...",
# votre version de PHP est inf�rieure � 4.3.0.
# En effet, la fonction mysql_real_escape_string() est disponible � partir de la version 4.3.0 de php.
# Vous devriez mettre � jour votre version de php.
# Sinon, positionnez la variable suivante � "0"; (valeur par d�faut = 1)
$use_function_mysql_real_escape_string = 1;

# Apres installation de GRR, si vous avez le message "Fatal error: Call to undefined function: html_entity_decode() ...",
# votre version de PHP est inferieure a 4.3.0.
# En effet, la fonction html_entity_decode() est disponible a partir de la version 4.3.0 de php.
# Vous devriez mettre a jour votre version de php.
# Sinon, positionnez la variable suivante a "0"; (valeur par defaut = 1)
$use_function_html_entity_decode = 1;

###################################
# Cas d'une authentification SSO  #
###################################

/*
$sso_super_admin : false|true
Mettre la valeur du param�tre $sso_super_admin � "true" pour rendre possible l'acc�s � la page login.php m�me si l'administrateur a coch� dans l'interface en ligne le choix "Emp�cher l'acc�s � la page de login".
*/
$sso_super_admin = false;

/*
 $sso_restrictions : false|true
 Mettre la valeur du param�tre $sso_restrictions � "true" permet de cacher dans l'interface de GRR l'affichage de la rubrique "Authentification et ldap"
*/
$sso_restrictions = false;

// Le param�tre $Url_CAS_setFixedServiceURL est le param�tre utilis� dans la m�thode phpCAS::setFixedServiceURL(), dans le fichier cas.inc.php
// Si ce param�tre est non vide, il sera utilis� par le service CAS
// Set the fixed URL that will be set as the CAS service parameter. When this method is not called, a phpCAS script uses its own URL.
$Url_CAS_setFixedServiceURL = '';


#####################################################
# Param�tres propres � une authentification SSO LASSO
#####################################################
// Indiquez ci-dessous le r�pertoire d'installation du package spkitlasso
// (la valeur par d�faut le cherche dans le 'include_path' de PHP)
define('SPKITLASSO',"spkitlasso");

##############################################################
# Param�tres propres � une authentification sur un serveur LCS
##############################################################
# Page d'authentification LCS
define('LCS_PAGE_AUTHENTIF',"../../lcs/auth.php");
# Page de la librairie ldap
define('LCS_PAGE_LDAP_INC_PHP',"/var/www/Annu/includes/ldap.inc.php");
# R�alise la connexion � la base d'authentification du LCS et include des fonctions de lcs/includes/functions.inc.php
define('LCS_PAGE_AUTH_INC_PHP',"/var/www/lcs/includes/headerauth.inc.php");

#############
# Entry Types
#############
# Les lignes ci-dessous correspondent aux couleurs disponibles pour les types de r�servation
# Vous pouvez modifier les couleurs ou m�me en rajouter � votre convenance.
$tab_couleur[1] = "#FFCCFF"; # mauve p�le
$tab_couleur[2] = "#99CCCC"; # bleu
$tab_couleur[3] = "#FF9999"; # rose p�le
$tab_couleur[4] = "#FFFF99"; # jaune p�le
$tab_couleur[5] = "#C0E0FF"; # bleu-vert
$tab_couleur[6] = "#FFCC99"; # p�che
$tab_couleur[7] = "#FF6666"; # rouge
$tab_couleur[8] = "#66FFFF"; # bleu "aqua"
$tab_couleur[9] = "#DDFFDD"; # vert clair
$tab_couleur[10] = "#CCCCCC"; # gris
$tab_couleur[11] = "#7EFF7E"; # vert p�le
$tab_couleur[12] = "#8000FF"; # violet
$tab_couleur[13] = "#FFFF00"; # jaune
$tab_couleur[14] = "#FF00DE"; # rose
$tab_couleur[15] = "#00FF00"; # vert
$tab_couleur[16] = "#FF8000"; # orange
$tab_couleur[17] = "#DEDEDE"; # gris clair
$tab_couleur[18] = "#C000FF"; # Mauve
$tab_couleur[19] = "#FF0000"; # rouge vif
$tab_couleur[20] = "#FFFFFF"; # blanc
$tab_couleur[21] = "#A0A000"; # Olive verte
$tab_couleur[22] = "#DAA520"; # marron goldenrod
$tab_couleur[23] = "#40E0D0"; # turquoise
$tab_couleur[24] = "#FA8072"; # saumon
$tab_couleur[25] = "#4169E1"; # bleu royal
$tab_couleur[26] = "#6A5ACD"; # bleu ardoise
$tab_couleur[27] = "#AA5050"; # bordeaux
$tab_couleur[28] = "#FFBB20"; # p�che


###################
# Database settings
###################

# Quel syst�me de base de donn�es : "pgsql"=PostgreSQL, "mysql"=MySQL
# Actuellement, GRR ne supporte que mysql.
$dbsys = "mysql";
# Uncomment this to NOT use PHP persistent (pooled) database connections:
#$db_nopersist = 1;

################################
# Backup information
#################################
#true=sauvegarde la structure des tables
$structure = true;
#true=sauvegarde les donnees des tables
$donnees = true;
#clause INSERT avec nom des champs
$insertComplet = false;

# Global settings array
$grrSettings = array();

# Make sure notice errors are not reported
#error_reporting (E_ALL ^ E_NOTICE);
?>