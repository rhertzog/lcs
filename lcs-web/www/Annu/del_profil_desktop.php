<?php
/* =============================================
   Projet LCS : Linux Communication Server
   Nettoyage du profil desktop utilisateur
   Annu/del_profil_desktop.php
   [LCS CoreTeam]
   Equipe Tice academie de Caen
   04/04/2013
   ============================================= */

	include "../lcs/includes/headerauth.inc.php";
	include "includes/ldap.inc.php";
	include "includes/ihm.inc.php";

	$uid = $_GET[uid];

	list ($idpers,$login)= isauth();
	if ($idpers == "0") header("Location:$urlauth");
	header_html();
	aff_trailer ("3");
	  
    if (is_admin("Annu_is_admin",$login) == "Y" ) {
    	if ( isset ( $uid ) ) {
    		list($user, $groups)=people_get_variables($uid, true);
    		
    		$mask = "/home/$uid/Profile/*.json";
    		array_map( "unlink", glob( $mask ) );
    		echo "<p>Le profil du bureau de <b><i>".$user["fullname"]."</i></b> a &#233;t&#233; r&#233;initialis&#233;.</p>";
    	} else echo "<div class=error_msg>ERREUR !</div>";
    	
  	} else 
    	echo "<div class=error_msg>Cette application, n&#233;cessite les droits d'administrateur du serveur LCS !</div>";

	include ("../lcs/includes/pieds_de_page.inc.php");
?>
