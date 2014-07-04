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
include "../lcs/includes/headerauth.inc.php";
include "includes/ldap.inc.php";
include "includes/ihm.inc.php";

  header_html();
  aff_trailer ("3");
  if (is_admin("Annu_is_admin",$login)=="Y") {
    if (count($_GET)>0) {
  		//configuration objet
 		include ("../lcs/includes/htmlpurifier/library/HTMLPurifier.auto.php");
 		$config = HTMLPurifier_Config::createDefault();
 		$purifier = new HTMLPurifier($config);
    	//purification des variables
  		$uid=$purifier->purify($_GET['uid']);
    }
    // suppression d'un d'utilisateur
    if ($uid == "admin" )  {
      echo "<div class=error_msg>Vous ne pouvez pas effacer le compte administrateur !</div>";
    } elseif (!$uid)  {
      echo "<div class=error_msg>Vous devez pr&eacute;ciser le login du compte &agrave; effacer !</div>";
    } elseif(preg_match("#^[A-Za-z0-9._-]{3,19}$#", $uid)) {
       exec ("/usr/bin/sudo $scriptsbinpath/userDel.pl ".escapeshellarg($uid)."",$AllOutPut,$ReturnValue);
        if ($ReturnValue == "0") {
          echo "Le compte <strong>$uid</strong> a &eacute;t&eacute; effac&eacute; avec succ&egrave;s !<br />\n";
        } else {
          echo "<div class=error_msg>
                  Echec, l'utilisateur $uid n'a pas &eacute;t&eacute; effac&eacute; !
                  (type d'erreur : $ReturnValue), veuillez contacter
                  <a href='mailto:$MelAdminLCS?subject=Effacement utilisateur $uid'>
                  l'administrateur du syst&eagrave;me</a>
                </div>\n<br />\n";
        }
    }
    else echo "<div class=error_msg>L'uid fourni n'est pas conforme</div>";
  } else {
    echo "<div class=error_msg>Cette fonctionnalit&eacute;, n&#233;cessite les droits d'administrateur du serveur LCS !</div>";
  }
  include ("../lcs/includes/pieds_de_page.inc.php");
?>
