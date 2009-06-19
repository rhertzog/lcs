<?php

/*
---------------------------------------------------------
Ajouté par le lycée laetitia Bonaparte 2008 pour le LCS
---------------------------------------------------------
*/

//connexion à la base de données dans ce script. On est obligé de travailler avec le fichier de conf wakka.config.php car on ne se trouve plus dans une page wiki et donc nous n'avons plus accès aux méthodes definies pour l'objet wiki

include("../../wakka.config.php");

//recupération des variables dans le fichier de conf du wiki (wakka.config.php)
$host = $wakkaConfig['mysql_host'];
$user = $wakkaConfig['mysql_user'];
$pass = $wakkaConfig['mysql_password'];
$database = $wakkaConfig['mysql_database'];
$prefix = $wakkaConfig['table_prefix'];

//connexion à la base de données
$link = @mysql_connect($host,$user,$pass);

if ($link) {
	if (!@mysql_select_db($database, $link)){
		@mysql_close($link);
		$link = false;
	}
}

?>
