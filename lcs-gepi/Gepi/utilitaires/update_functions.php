<?php
/*
$Id: update_functions.php 3793 2009-11-25 13:19:02Z crob $
*/
// Ensemble de m�thodes utilis�es par le script d'initialisation

function add_index($tablename, $indexname, $indexcolumns) {
  $result = "&nbsp;->Ajout de l'index '$indexname' � la table $tablename<br />";
  $req_res=0;
  $req_test = mysql_query("SHOW INDEX FROM $tablename");
  if (mysql_num_rows($req_test)!=0) {
    while ($enrg = mysql_fetch_object($req_test)) {
      if ($enrg-> Key_name == $indexname) {$req_res++;}
    }
  }
  if ($req_res == 0) {
    $query = mysql_query("ALTER TABLE `$tablename` ADD INDEX $indexname ($indexcolumns)");
    if ($query) {
      $result .= "<font color=\"green\">Ok !</font><br />";
    } else {
      $result .= "<font color=\"red\">Erreur</font><br />";
    }
  } else {
    $result .= "<font color=\"blue\">L'index existe d�j�.</font><br />";
  }
  return $result;
}

?>
