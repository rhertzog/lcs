<?php
/*
 * @version $Id: pop.calendrier_id.php 2281 2008-08-17 09:22:44Z crob $
 */

             
/**
 * Int�rieur du fichier php contenant le seul calendrier. On r�cup�re en GET les valeurs
 * repr�sentant le nom du formulaire et le nom du champ de la date.
 */
$frm = $_GET['frm'];
$chm = $_GET['ch'];

include("calendrier_id.class.php");

/**
 * On cr�� un nouveau calendrier, on r�cup�re la date � afficher (par d�faut, le calendrier
 * affiche le mois en cours de l'ann�e en cours). Les valeurs de POST sont transmises au
 * moment o� on change le SELECT des mois ou celui des ann�es. Finalement, on affiche le
 * calendrier.
 */
$cal = new Calendrier($frm, $chm);
$cal->auto_set_date($_POST);
$cal->affiche();

?>
