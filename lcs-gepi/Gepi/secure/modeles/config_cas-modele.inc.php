<?php
/*
 * $Id$
 *
 * Copyright 2001, 2008 Thomas Belliard
 *
 * This file is part of GEPI.
 *
 * GEPI is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * GEPI is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with GEPI; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

#---
# Ce fichier doit �tre renomm� en config_cas.inc.php dans le repertoire secure pour pouvoir �tre
# pris en compte !
#---


// Param�trage du serveur CAS
// L'URL est construite de la forme suivante :
// https://$cas_host:$cas_port/$cas_root

$cas_host = "localhost"; // l'h�te du serveur CAS
$cas_port = 8443; // Le port
$cas_root = 'cas';

# Si la variable suivante contient une URL, l'utilisateur qui se d�connecte de Gepi
# sera redirig� vers la page logout de CAS. Ensuite, soit l'utilisateur sera invit�
# � cliquer sur cette URL, soit il sera automatiquement redirig� vers elle. Ce
# comportement est d�termin� par la seconde variable ($cas_logout_redirect).

$cas_logout_url = ''; 	# Laissez vide pour n'afficher que la page logout de Gepi.
						# Assurez-vous de bien saisir une URL valide !

$cas_logout_redirect = true; 	# true/false : activer ou non la redirection automatique
								# lors du logout.
$cas_use_logout = true; // true/false pour se d�connecter ou non de CAS quand on se d�connecte de GEPI.

?>
