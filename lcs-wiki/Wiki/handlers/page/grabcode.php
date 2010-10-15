<?php
if (!eregi("wakka.php", $_SERVER['PHP_SELF'])) {
    die ("acc&egrave;s direct interdit");
}
$contenu = $_POST[contenu];
header('Content-type: application/force-download');
header("Content-type: text/plain");
header('Content-Disposition: attachment; filename="tableau.csv"');
// display raw page
echo $contenu;
?>