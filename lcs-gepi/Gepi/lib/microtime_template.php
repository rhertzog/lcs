<?php
/**
 * Affichage du temps de g�n�ration de la page
 * 
 * ---------Variables envoy�es au gabarit
 * - $tbs_microtime							nombre de secondes pour charger la page
 * $Id:  $
*/

$tbs_microtime="";

if ($gepiShowGenTime == "yes") {
   $pageload_endtime = microtime(true);
   $pageload_time = $pageload_endtime - $pageload_starttime;
   $tbs_microtime=$pageload_time;
}
?>
