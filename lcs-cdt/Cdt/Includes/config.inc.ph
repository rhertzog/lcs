<?php
/* =============================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 2.1 du 4/06/2010
	par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script de connexion a la base de donnees -
			_-=-_
   ============================================= */
$NAME_PLUG = "Cahier de textes";
$VER_PLUG = "2.2";
$FLAG_ABSENCE=1;
$Grain="#PASS2#";

// Definition des parametres d'acces sous forme de constantes
DEFINE ('DB_USER', 'cdt_user');
DEFINE ('DB_PASSWORD', '#PASS#');
DEFINE ('DB_HOST', 'localhost');
DEFINE ('DB_NAME', 'cdt_plug');

// Ouvrir la connexion et selectionner la base de donnees
$dbc = @mysql_connect (DB_HOST, DB_USER, DB_PASSWORD) 
       OR die ('Connexion a MySQL impossible : '.mysql_error().'<br>');
mysql_select_db (DB_NAME)
       OR die ('Selection de la base de donnees impossible : '.mysql_error().'<br>');
?>
