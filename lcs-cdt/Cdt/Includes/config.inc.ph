<?php
/* =============================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 1.0 du 29/10/2008
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script de connexion � la base de donn�es -
			_-=-_
   ============================================= */
$NAME_PLUG = "Cahier de textes";
$VER_PLUG = "1.01";
$FLAG_ABSENCE=1;
$Grain="#PASS2#";

// D�finition des param�tres d'acc�s sous forme de constantes
DEFINE ('DB_USER', 'cdt_user');
DEFINE ('DB_PASSWORD', '#PASS#');
DEFINE ('DB_HOST', 'localhost');
DEFINE ('DB_NAME', 'cdt_plug');

// Ouvrir la connexion et s�lectionner la base de donn�es
$dbc = @mysql_connect (DB_HOST, DB_USER, DB_PASSWORD) 
       OR die ('Connexion � MySQL impossible : '.mysql_error().'<br>');
mysql_select_db (DB_NAME)
       OR die ('S�lection de la base de donn�es impossible : '.mysql_error().'<br>');
?>