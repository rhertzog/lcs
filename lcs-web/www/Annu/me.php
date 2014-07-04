<?php
/* =============================================
   Projet LCS-SE3
   Consultation/ Gestion de l'annuaire LDAP
   Equipe Tice academie de Caen
   Distribue selon les termes de la licence GPL
   Derniere modification : 04/04/2014
   ============================================= */
include "includes/check-token.php";
if (!check_acces()) exit;

$login=$_SESSION['login'];
$jeton_people=md5($_SESSION['token'].htmlentities("/Annu/people.php"));
header("Location:people.php?uid=$login&jeton=$jeton_people");
?>
