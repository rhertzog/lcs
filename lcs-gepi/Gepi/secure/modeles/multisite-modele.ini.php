; <?php header('Location:../login.php');die(); ?>
; Une fois renseign�, pensez � renommer ce fichier multisite.ini.php dans le repertoire secure

[RNE]
nomhote		= localhost
mysqluser	= user
mysqlmdp	= motdepasse
nombase		= essai
pathname	= /gepi
nometablissement = College du bois vert 

[RNE1]
nomhote		= localhost
mysqluser	= user1
mysqlmdp	= motdepasse1
nombase		= essai1
pathname	= /gepi

[RNE2]
nomhote		= localhost
mysqluser	= user2
mysqlmdp	= motdepasse2
nombase		= essai2
pathname	= /gepi

;
; @version $Id: multisite-modele.ini.php 7920 2011-08-23 12:14:07Z jjacquard $
;
; Copyright 2001-2004 Thomas Belliard, Laurent Delineau, Eric Lebrun, St�phane Boireau, Julien Jocal
;
; This file is part of GEPI.
;
; GEPI is free software; you can redistribute it and/or modify
; it under the terms of the GNU General Public License as published by
; the Free Software Foundation; either version 2 of the License, or
; (at your option) any later version.
;
; GEPI is distributed in the hope that it will be useful,
; but WITHOUT ANY WARRANTY; without even the implied warranty of
; MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
; GNU General Public License for more details.
;
; You should have received a copy of the GNU General Public License
; along with GEPI; if not, write to the Free Software
; Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
;/

; Multisite.ini, fichier permettant de configurer l'utilisation d'un seul Gepi
; sur plusieurs �tablissements. Ce fichier est pars� pour r�cup�rer la bonne base en fonction du RNE de l'�tablissement
; Il doit comporter les informations pour chaque �tablissement en suivant le mod�le pr�sent� et en sautant une ligne entre chaque configuration
; Le param�tre nometablissement n'est pas obligatoire
; le RNE peut �tre une autre information que le rne Education nationale mais doit correspondre � une information disponible
; en GET (le lien vers le gepi doit donc �tre du type https://nom_serveur/gepipath/login.php?rne=RNE (ce RNE correspondant � celui qui est entre crochets ci-dessous)
