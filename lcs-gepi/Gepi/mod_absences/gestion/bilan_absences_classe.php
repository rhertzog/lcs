<?php

/**
 *
 *
 * @version $Id: bilan_absences_classe.php 1427 2008-01-30 10:36:23Z jjocal $
 * @copyright 2007
 */


// Initialisation des variables
$choix_classe = isset($_GET["id_classe"]) ? $_GET["id_classe"] : (isset($_POST["id_classe"]) ? $_POST["id_classe"] : NULL);


echo '
	<h2>Cette fonctionnalit� n\'est pas encore op�rationnelle.</h2>
	<br />
	<p><a href="./bilan_absences_quotidien.php">RETOUR</a></p>

	';

?>