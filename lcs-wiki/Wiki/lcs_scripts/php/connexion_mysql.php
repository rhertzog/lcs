<?php

/*
---------------------------------------------------------
Ajout� par le lyc�e laetitia Bonaparte 2008 pour le LCS
---------------------------------------------------------
*/

//connexion � la base de donn�es dans ce script. On est oblig� de travailler avec le fichier de conf wakka.config.php car on ne se trouve plus dans une page wiki et donc nous n'avons plus acc�s aux m�thodes definies pour l'objet wiki

include("../../wakka.config.php");

//recup�ration des variables dans le fichier de conf du wiki (wakka.config.php)
$host = $wakkaConfig['mysql_host'];
$user = $wakkaConfig['mysql_user'];
$pass = $wakkaConfig['mysql_password'];
$database = $wakkaConfig['mysql_database'];
$prefix = $wakkaConfig['table_prefix'];

//connexion � la base de donn�es
$link = @mysql_connect($host,$user,$pass);

if ($link) {
	if (!@mysql_select_db($database, $link)){
		@mysql_close($link);
		$link = false;
	}
}

?>
