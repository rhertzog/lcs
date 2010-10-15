<?php
#########################################################################
#                         cas.inc.php                                   #
#     script de redirection vers l'authentification CAS                 #
#                                                                       #
#                  Derni�re modification : 22/12/2005                   #
#                                                                       #
#########################################################################
/*
 * Copyright 2005 Olivier Mounier
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

// Le package phpCAS doit etre stock� dans un sous-r�pertoire ��CAS��
// dans un r�pertoire correspondant a l'include_path du php.ini (exemple : /var/lib/php)
include_once('CAS/CAS.php');

// cas.sso est le fichier d'informations de connexions au serveur cas
// Le fichier cas.sso doit etre stock� dans un sous-r�pertoire ��CAS��
// dans un r�pertoire correspondant a l'include_path du php.ini (exemple : /var/lib/php)
include('CAS/cas.sso');

// declare le script comme un client CAS
// Le dernier argument (true par d�faut) donne la possibilit� � phpCAS d'ouvrir une session php.
// Si tel est le cas, l'authentification CAS n'est pas r�percut�e dans GRR (et il faut se r�authentifier dans l'appli),
// car le "start_session()" de l'application (environ ligne 232 dans le fichier "session.inc.php") ne marche pas ===> la session a �t� ouverte par phpCAS
// et les variables de session positionn�es par la suite par grr ne sont pas r�cup�rables.
phpCAS::client(CAS_VERSION_2_0,$serveurSSO,$serveurSSOPort,$serveurSSORacine,false);

phpCAS::setLang('french');

// Set the fixed URL that will be set as the CAS service parameter. When this method is not called, a phpCAS script uses its own URL.
if (isset($Url_CAS_setFixedServiceURL) and ($Url_CAS_setFixedServiceURL != ''))
    phpCAS::setFixedServiceURL($Url_CAS_setFixedServiceURL) ;

// redirige vers le serveur CAS si n�cessaire
phpCAS::forceAuthentication();

// A ce stade, l'utilisateur est authentifi�
$user=phpCAS::getUser();
$login=phpCAS::getUser();
$user_ext_authentifie = 'cas';
?>