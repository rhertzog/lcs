<?php
/* =============================================
   Projet LCS-SE3
   Consultation/ Gestion de l'annuaire LDAP
   Equipe Tice academie de Caen
   Distribue selon les termes de la licence GPL
   Derniere modification : 23/05/2014
   ============================================= */
include "includes/check-token.php";
if (!check_acces()) exit;

$login=$_SESSION['login'];
include "../lcs/includes/headerauth.inc.php";
include "includes/ldap.inc.php";
include "includes/ihm.inc.php";
$phase="";
if ( count($_GET)>0 || count($_POST)>0 ) {
  	//configuration objet
 	include ("../lcs/includes/htmlpurifier/library/HTMLPurifier.auto.php");
 	$config = HTMLPurifier_Config::createDefault();
 	$purifier = new HTMLPurifier($config);
    //purification des variables
	if ( isset($_POST['phase']))  $phase = $purifier->purify($_POST['phase']);
	elseif ( isset($_GET['phase'])) $phase = $purifier->purify($_GET['phase']);
	if ( isset($_POST['shell_orig']))  $shell_orig = $purifier->purify($_POST['shell_orig']);
	elseif ( isset($_GET['shell_orig'])) $shell_orig = $_GET['shell_orig'];
	if ( isset($_POST['shell_mod']))  $shell_mod = $_POST['shell_mod'];
	elseif ( isset($_GET['shell_mod'])) $shell_mod = $_GET['shell_mod'];
}

  // en-tete
  $html = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">\n";
  $html .= "<html>\n";
  $html .= "	<head>\n";
  $html .= "		<title>...::: Interface d'administration Serveur LCS :::...</title>\n";
  $html .= "            <meta HTTP-EQUIV=\"Content-Type\" CONTENT=\"tetx/html; charset=utf-8\">\n";
  echo $html;
  // Redirection vers phase suivante, gestion du «sablier»
  if( $phase == 1 )
	echo "<meta HTTP-EQUIV=\"Refresh\" CONTENT=\"1;url='".$_SERVER['PHP_SELF']."?phase=2&shell_orig=$shell_orig&shell_mod=$shell_mod&jeton=".md5($_SESSION['token'].htmlentities($_SERVER['PHP_SELF']))."'\">\n";

  $html  = "		<link  href='style.css' rel='StyleSheet' type='text/css'>\n";
  $html .= "	</head>\n";
  $html .= "	<body>\n";
  $html .= "<div>\n";
  echo $html;
  aff_trailer ("1");
  if (is_admin("Annu_is_admin",$login)=="Y") {
    if ( $phase !=1 ) {
        $html = "<h3>Modification du shell des utilisateurs :</h3>\n";
        // Affichage du formulaire de selection du shell
        $html .= "<div style=\"margin-left: 50px;\">\n";
        $html .= "<form name = \"shellmod\" action=\"mod_shell.php\" method=\"post\">\n";
        $html .= "de \n";
        $html .= "<select name=\"shell_orig\">\n";
        $html .= "  <option>/bin/bash</option>\n";
        $html .= "  <option selected>/bin/true</option>\n";
        //$html .= "  <option>/usr/lib/sftp-server</option>\n";
	$html .= "</select> \n";
        $html .= "en \n";
        $html .= "<select name=\"shell_mod\">\n";
        $html .= "  <option>/bin/bash</option>\n";
        $html .= "  <option>/bin/true</option>\n";
        //$html .= "  <option selected>/usr/lib/sftp-server</option>\n";
	$html .= "</select> \n";
        $html .= "<input type=\"hidden\" name=\"phase\" value=\"1\">\n";
        $html.='<input name="jeton" type="hidden"  value="'. md5($_SESSION['token'].htmlentities($_SERVER['PHP_SELF'])).'" /> ';
        $html .= "<input type=\"submit\" value=\"Valider\">\n";
        $html .= "</form>\n";
        // Message d'information
        $html .= "<div style=\"margin-top: 50px; margin-right: 50px;\">\n";
        $html .= "<img src=\"images/warning.png\" align=\"left\" alt=\"message d'alerte\">\n";
        $html .= "Attention cette modification concernera tous les utilisateurs !\n";
        $html .= "<p>Ainsi, si vous demandez la modification du shell <tt class=\"shell\">/bin/true</tt> vers le shell\n";
        $html .= "<tt class=\"shell\">/bin/bash</tt>, c'est l'ensemble des comptes pourvu du shell\n";
        $html .= "<tt class=\"shell\">/bin/true</tt> qui se verra affect&#233; le shell <tt class=\"shell\">/bin/bash</tt>.<br>\n";
        $html .= "Mesurez donc bien les cons&#233;quences de cette action avant de la valider.</p>\n";
        $html .= "<u>Signification des diff&#233;rents shell</u> :\n";
        $html .= "<ul>\n<li> <tt class=\"shell\">/bin/true</tt> : l'utilisateur ne poss&#232;de pas de shell sur le serveur,</li>\n";
        //$html .= "<li> <tt class=\"shell\">/usr/lib/sftp-server</tt> : l'utilisateur acc&#232;de &#224; son espace perso. en sftp sur le port 2222,\n";
        $html .= "<li> <tt class=\"shell\">/bin/bash</tt> : l'utilisateur dispose d'un shell bash sur le serveur.</li>\n</ul>\n";
        $html .= "<u>Autoriser l'acc&#232;s <strong>sftp</strong> aux utilisateurs</u> :\n";
        $html .= "<p>Pour autoriser l'acc&#232;s <strong>sftp</strong>, ind&#233;pendemment du shell, il faudra ajouter chaque utilisateur dans le groupe <strong>sftp-allowed</strong>.</p>\n";
        $html .= "</div>\n";
        $html .= "</div>\n";
        echo $html;
    }
    if ( $phase == 1 ) {
          echo "<div align='center'><img src=\"../Admin/Images/wait.gif\" title=\"Patientez...\" align=\"middle\" border=\"0\">&nbsp;Modification du shell des utilisateurs en cours. Veuillez patienter...</div>";
    } elseif ( $phase==2 ) {
           // Verification de la coherence de la selection
           if ($shell_orig != $shell_mod  ) {
            // Execution de la modification du shell
            exec ("$scriptsbinpath/changeShellAllUsers.pl ". escapeshellarg($shell_orig) . " ". escapeshellarg($shell_mod) ,$AllOutPut,$ReturnValue);
            if ($ReturnValue == "0")
	      echo "La modification du shell des utilisateurs de <tt class=\"shell\">$shell_orig</tt> vers <tt class=\"shell\">$shell_mod</tt> s'est d&#233;roul&#233;e avec succ&#232;s.<br>";
	    else
              echo "<div class=error_msg>Echec de la modification du shell des utilisateurs !</div>";
           } else
              echo "<div class=error_msg>Il faut s&#233;lectionner deux shells diff&#233;rents !</div>";
    }

  } else {
    echo "<div class='error_msg'>Cette application, n&#233;cessite les droits d'administrateur du serveur LCS !</div>";
  }
  echo "</div>\n";
  include ("../lcs/includes/pieds_de_page.inc.php");
?>
