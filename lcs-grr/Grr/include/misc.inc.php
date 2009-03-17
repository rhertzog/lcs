<?php
#########################################################################
#                           misc.inc.php                                #
#                                                                       #
#                       fichier de variables diverses                   #
#                                                                       #
#                  Derni�re modification : 10/07/2006                   #
#                                                                       #
#########################################################################
/*
 * Copyright 2003-2005 Laurent Delineau
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

################################
# Development information
#################################
$grr_devel_email = "laurent.delineau@ac-poitiers.fr";
$grr_devel_url = "http://grr.mutualibre.org";
// Num�ro de version actuel
$version_grr = "1.9.5";
// Num�ro de sous-version actuel (a, b, ...)
// Utilisez cette variable pour des versions qui corrigent la la version finale sans toucher � la base.
$sous_version_grr = "c";
// Num�ro de la release candidate (doit �tre strictement inf�rieure � 9). Laisser vide s'il s'agit de la version stable.
$version_grr_RC = "";

# Liste des tables
$liste_tables = array(
"grr_area_periodes",
"grr_type_area",
"grr_j_type_area",
"grr_j_mailuser_room",
"grr_j_user_area",
"grr_j_user_room",
"grr_log",
"grr_area",
"grr_entry",
"grr_repeat",
"grr_room",
"grr_setting",
"grr_utilisateurs",
"grr_j_useradmin_area",
"grr_calendar",
"grr_overload",
"grr_entry_moderate"
);

# Liste des feuilles de style
$liste_themes = array(
"default",
"forestier",
"or",
"orange",
"argent",
"volcan",
"toulouse"
);

# Liste des noms des styles
$liste_name_themes = array(
"Grand bleu",
"Forestier",
"Dor�",
"Orange",
"Argent",
"Volcan",
"Toulouse"
);

# Liste des langues
$liste_language = array(
"fr",
"de",
"en",
"it",
"es"
);

# Liste des noms des langues
$liste_name_language = array(
"Fran�ais",
"Deutch",
"English",
"Italiano",
"Spanish"
);

################################################
# Configuration du planning : valeurs par d�faut
# Une interface en ligne permet une configuration domaine par domaine de ces valeurs
################################################
# Resolution - quel bloc peut �tre r�serv�, en secondes
# remarque : 1800 secondes = 1/2 heure.
$resolution = 900;

# Dur�e maximale de r�servation, en minutes
# -1 : d�sactivation de la limite
$duree_max_resa = -1 ;

# D�but et fin d'une journ�e : valeur enti�res uniquement de 0 � 23
# morningstarts doit �tre inf�rieur �  < eveningends.
$morningstarts = 8;
$eveningends   = 19;

# Minutes � ajouter � l'heure $eveningends pour avoir la fin r�elle d'une journ�e.
# Examples: pour que le dernier bloc r�servable de la journ�e soit 16:30-17:00, mettre :
# eveningends=16 et eveningends_minutes=30.
# Pour avoir une journ�e de 24 heures avec un pas de 15 minutes mettre :
# morningstarts=0; eveningends=23;
# eveningends_minutes=45; et resolution=900.
$eveningends_minutes = 0;

# D�but de la semaine: 0 pour dimanche, 1 pou lundi, etc.
$weekstarts = 1;

# Format d'affichage du temps : valeur 0 pour un affichage ��12 heures�� et valeur 1 pour un affichage  ��24 heure��.
$twentyfourhour_format = 1;

# Ci-dessous des fonctions non officielles (non document�es) de GRR
# En attendant qu'elles soient impl�ment�es dans GRR avec une interface en ligne

# Vous pouvez indiquer ci-dessous l'id d'une ressource qui sera r�servable, m�me par un simple visiteur
$id_room_autorise = "";

# Possibilit� de d�sactiver le bandeau sup�rieur dans le cas de simples visiteurs
# Pour se connecter il est alors n�cessaire de se rendre directement � l'adresse du type http://mon-site.fr/grr/login.php
# Mettre ci-dessous $desactive_bandeau_sup = 1;  pour d�sactiver le bandeau sup�rieur pour les simples visiteurs.
# Mettre ci-dessous $desactive_bandeau_sup = 0;  pour ne pas d�sactiver le bandeau sup�rieur pour les simples visiteurs.
$desactive_bandeau_sup = 0;
?>