<?php
/* 
 * @version $Id: function.php 5114 2010-08-26 15:29:50Z crob $
 * multisite
 * Les dossiers contenant les mod�les Gepi et mes mod�les. Il faut un / � la fin du chemin.
*/

function repertoire_modeles($nom_fichier_modele){
  if ($_SESSION['rne']!='') {
	$rne=$_SESSION['rne']."/";
  } else {
	$rne='';
  }
  $nom_dossier_modeles_par_defaut='../mod_ooo/modeles_gepi/';
  $nom_dossier_modeles_mes_modeles='../mod_ooo/mes_modeles/';

//traitement du mod�le (par defaut ou mod�le personnel ) On fait un test  sur l'existance du fichier dans le dossier mes_modeles/
  if  (file_exists($nom_dossier_modeles_mes_modeles.$rne.$nom_fichier_modele))   {
    $nom_dossier_modele_a_utiliser = $nom_dossier_modeles_mes_modeles.$rne.$nom_fichier_modele; //Le dossier dans mes mod�les et eventuellement RNE
  } else {
    $nom_dossier_modele_a_utiliser = $nom_dossier_modeles_par_defaut.$nom_fichier_modele; //le dossier contenant les mod�les par defaut.
  }
  return $nom_dossier_modele_a_utiliser;
}

?>
