<?php
/**
 * Ensemble de m�thodes utilis�es par le script d'initialisation
 * 
 * $Id: update_functions.php 7923 2011-08-23 12:36:29Z regis $
 * 
 * @copyright Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
 * @license GNU/GPL,
 * @package General
 * @subpackage mise_a jour
*/

/**
 *
 * @param type $tablename
 * @param type $indexname
 * @param type $indexcolumns
 * @return string 
 */
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
      $result .= msj_ok();
    } else {
      $result .= msj_erreur();
    }
  } else {
    $result .= msj_present("L'index existe d�j�.");
  }
  return $result;
}

/**
 * mise � jour r�ussie
 * @param sring $message
 * @return string Ok ! ou $message �crit en vert 
 */
function msj_ok($message=""){
  if ($message=="") {
    return "<span class='msj_ok'>Ok !</span><br />";
  } else {
    return "<span style='color:green;'>$message</span><br />";
  }
  
}

/**
 * Echec d'une mise � jour
 * @param string $message
 * @return string Erreur suivi de $message �crit en rouge
 */
function msj_erreur($message=""){
  return "<span class='msj_erreur'>Erreur $message</span><br />";
}

/**
 * Mise � jour d�j� effectu�e
 * @param string $message
 * @return string $message �crit en bleu
 */
function msj_present($message){
  return "<span class='msj_present'> $message.</span><br />";
}

?>
