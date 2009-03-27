<?php
Function DebutSemaine($J = "") {
// Retourne un timestamp du lundi à 8 h de la semaine contenant le jour $J
// Si $J est vide c'est la semaine en cours qui est prise en compte.
	if ($J=="") $J=mktime(8,0,0);
	return (mktime(8,0,0,date("m",$J),date("d",$J),date("Y",$J))-((date("w",$J)+6) % 7)*86400);
}
Function LeJour ($J = "") {
// Retourne le nom du jour de la semaine en français correspondant au timestamp passé en paramètre
// Si $J est vide c'est la semaine en cours qui est prise en compte.
	if ($_SESSION['version']=">=432") setlocale(LC_TIME,"french");
	else setlocale("LC_TIME","french");
	if ($J=="") {
		$JourSem=strftime("%A");
	} else {
		$JourSem=strftime("%A", $J);
	}
		Return $JourSem;
}

Function datefr($tstamp="") {
//retourne la date en français
	if ($_SESSION['version']=">=432") setlocale(LC_TIME,"french");
	else setlocale("LC_TIME","french");
	$datfr=strFTime("%d %b %Y", $tstamp);
	Return $datfr;
}

Function datefr2($tstamp="") {
//retourne le jour et le mois en français
	if ($_SESSION['version']=">=432") setlocale(LC_TIME,"french");
	else setlocale("LC_TIME","french");
	$datfr=strFTime("%d %b ", $tstamp);
	Return $datfr;
}

Function moisanfr($tstamp="") {
//retourne le mois et l'année  en français
	if ($_SESSION['version']=">=432") setlocale(LC_TIME,"french");
	else setlocale("LC_TIME","french");
	$moisanfr=strFTime("%b %Y", $tstamp);
	Return $moisanfr;
}
?>
