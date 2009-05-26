<?php
/* =============================================
   Projet LCS : Linux Communication Server
   Consultation de l'annuaire LDAP
   Annu/mod_shell.php
   [LCS CoreTeam]
   « jLCF >:> » jean-luc.chretien@tice.ac-caen.fr
   Equipe Tice academie de Caen
   Derniere modification : 02/12/07
   ============================================= */
  include "../lcs/includes/headerauth.inc.php";
  include "includes/ldap.inc.php";
  include "includes/ihm.inc.php";


  list ($idpers,$login)= isauth();
  if ($idpers == "0") header("Location:$urlauth");
  
  //modif register
//$shell_orig=$_POST['shell_orig'];
//$shell_mod=$_POST['shell_mod'];
//$phase=$_POST['phase'];
if ( isset($_POST['phase']))  $cn = $_POST['phase'];
elseif ( isset($_GET['phase'])) $cn = $_GET['phase'];
if ( isset($_POST['shell_orig']))  $cn = $_POST['shell_orig'];
elseif ( isset($_GET['shell_orig'])) $cn = $_GET['shell_orig'];
if ( isset($_POST['shell_mod']))  $cn = $_POST['shell_mod'];
elseif ( isset($_GET['shell_mod'])) $cn = $_GET['shell_mod'];    
  // en-tete
  $html = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">\n";
  $html  = "<HTML>\n";
  $html .= "	<HEAD>\n";
  $html .= "		<TITLE>...::: Interface d'administration Serveur LCS :::...</TITLE>\n"; 
  $html .= "            <META HTTP-EQUIV=\"Content-Type\" CONTENT=\"tetx/html; charset=ISO-8859-1\">\n";
  echo $html;
  // Redirection vers phase suivante, gestion du «sablier»
  if( $phase == 1 )
	echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"1;url='$PHP_SELF?phase=2&shell_orig=$shell_orig&shell_mod=$shell_mod'\">\n";
  $html  = "		<LINK  href='style.css' rel='StyleSheet' type='text/css'>\n";
  $html .= "	</HEAD>\n";
  $html .= "	<BODY>\n";
  $html .= "<DIV>\n";
  echo $html;  
  aff_trailer ("1");
  if (is_admin("Annu_is_admin",$login)=="Y") {
    if ( !isset($phase) ) {
        $html = "<H3>Modification du shell des utilisateurs :</H3>\n";
        // Affichage du formulaire de selection du shell
        $html .= "<DIV style=\"margin-left: 50px;\">\n";
        $html .= "<form name = \"shellmod\" action=\"mod_shell.php\" method=\"post\">\n";
        $html .= "de \n";
        $html .= "<select name=\"shell_orig\">\n";
        $html .= "  <option>/bin/bash</option>\n";
        $html .= "  <option selected>/bin/true</option>\n";
        $html .= "  <option>/usr/lib/sftp-server</option>\n";
	$html .= "</select> \n"; 
        $html .= "en \n";
        $html .= "<select name=\"shell_mod\">\n";
        $html .= "  <option>/bin/bash</option>\n";
        $html .= "  <option>/bin/true</option>\n";
        $html .= "  <option selected>/usr/lib/sftp-server</option>\n";
	$html .= "</select> \n";   
        $html .= "<input type=\"hidden\" name=\"phase\" value=\"1\">\n";
        $html .= "<input type=\"submit\" value=\"Valider\">\n";          
        $html .= "</form>\n";
        // Message d'information
        $html .= "<DIV style=\"margin-top: 50px; margin-right: 50px;\">\n";
        $html .= "<img src=\"images/warning.png\" align=\"left\" alt=\"message d'alerte\">\n";
        $html .= "Attention cette modification concernera tous les utilisateurs !\n";
        $html .= "<P>Ainsi, si vous demandez la modification du shell <tt class=\"shell\">/bin/true</tt> vers le shell\n";
        $html .= "<tt class=\"shell\">/usr/lib/sftp-server</tt>, c'est l'ensemble des comptes pourvu du shell\n";
        $html .= "<tt class=\"shell\">/bin/true</tt> qui se verra affect&#233; le shell <tt class=\"shell\">/usr/lib/sftp-server</tt>.<br>\n";
        $html .= "Mesurer donc bien les cons&#233;quences de cette action avant de la valider.</P>\n";
        $html .= "<u>Signification des diff&#233;rents shell</u> :\n";
        $html .= "<li> <tt class=\"shell\">/bin/true</tt> : l'utilisateur ne poss&#232;de pas de shell sur le serveur,\n";
        $html .= "<li> <tt class=\"shell\">/usr/lib/sftp-server</tt> : l'utilisateur acc&#232;de &#224; son espace perso. en sftp sur le port 2222,\n";
        $html .= "<li> <tt class=\"shell\">/bin/bash</tt> : l'utilisateur dispose d'un shell bash sur le serveur.\n";            
        $html .= "</DIV>\n";
        $html .= "</DIV>\n";
        echo $html;
    }
    if ( $phase == 1 ) {
          echo "<div align='center'><img src=\"../Admin/Images/wait.gif\" title=\"Patientez...\" align=\"middle\" border=\"0\">&nbsp;Modification du shell des utilisateurs en cours. Veuillez patienter...</div>";
    } elseif ( $phase==2 ) {
           // Verification de la coherence de la selection
           if ($shell_orig != $shell_mod  ) {
            // Execution de la modification du shell
            exec ("$scriptsbinpath/changeShellAllUsers.pl $shell_orig $shell_mod" ,$AllOutPut,$ReturnValue);
            if ($ReturnValue == "0")
	      echo "La modification du shell des utilisateurs de <tt class=\"shell\">$shell_orig</tt> vers <tt class=\"shell\">$shell_mod</tt> s'est d&#233;roul&#233;e avec succ&#232;s.<br>";
	    else
              echo "<div class=error_msg>Echec de la modification du shell des utilisateurs  !</div>";           
           } else    
              echo "<div class=error_msg>Il faut s&#233;lectionner deux shells diff&#233;rents !</div>";
    }  
 
  } else {
    echo "<div class='error_msg'>Cette application, n&#233;cessite les droits d'administrateur du serveur LCS !</div>";
  }
  echo "</div>\n";
  include ("../lcs/includes/pieds_de_page.inc.php");
?>  
