<?php
/* =============================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 1.0 du 29/10/2008
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script de connexion à la base de données -
			_-=-_
   ============================================= */
$NAME_PLUG = "Cahier de textes";
$VER_PLUG = "1.01";
$FLAG_ABSENCE=1;
$Grain="#PASS2#";

// Définition des paramètres d'accès sous forme de constantes
DEFINE ('DB_USER', 'cdt_user');
DEFINE ('DB_PASSWORD', '#PASS#');
DEFINE ('DB_HOST', 'localhost');
DEFINE ('DB_NAME', 'cdt_plug');

// Ouvrir la connexion et sélectionner la base de données
$dbc = @mysql_connect (DB_HOST, DB_USER, DB_PASSWORD) 
       OR die ('Connexion à MySQL impossible : '.mysql_error().'<br>');
mysql_select_db (DB_NAME)
       OR die ('Sélection de la base de données impossible : '.mysql_error().'<br>');
?>