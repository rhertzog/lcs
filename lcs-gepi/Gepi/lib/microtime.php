<?php
/**
 * Affichage du temps de g�n�ration de la page
 */
if ($gepiShowGenTime == "yes") {
   $pageload_endtime = microtime(true);
   $pageload_time = $pageload_endtime - $pageload_starttime;
   echo "<p class='microtime'>Page g�n�r�e en ".$pageload_time." sec</p>";
}
?>