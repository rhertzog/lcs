<?php
/* =============================================
   Projet LCS : Linux Communication Server
   Module "Gestions des groupes de travail"
   VERSION 2.3 du 04/01/2012
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script de connexion a la base de donnees -
			_-=-_
   ============================================= */
$NAME_MOD="Gestions des groupes de travail";
$VER_MOD="1.0";


// Definition des parametres d'acces sous forme de constantes
DEFINE ('DB_USER', 'ggt_user');
DEFINE ('DB_PASSWORD', '#PASS#');
DEFINE ('DB_HOST', 'localhost');
DEFINE ('DB_NAME', 'ggt_plug');

// Ouvrir la connexion et selectionner la base de donnees
$dbc = @mysql_connect (DB_HOST, DB_USER, DB_PASSWORD)
       OR die ('Connexion a MySQL impossible : '.mysql_error().'<br>');
mysql_select_db (DB_NAME)
       OR die ('Selection de la base de donnees impossible : '.mysql_error().'<br>');
?>
