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

if ((is_admin("Annu_is_admin",$login)=="Y") || (is_admin("sovajon_is_admin",$login)=="Y"))  {

	//Aide

	echo "<h1>R&#233;initialisation mot de passe par d&#233;faut</h1>\n";

	if ( count($_GET)>0 ) {
  		//configuration objet
 		include ("../lcs/includes/htmlpurifier/library/HTMLPurifier.auto.php");
 		$config = HTMLPurifier_Config::createDefault();
 		$purifier = new HTMLPurifier($config);
    	//purification des variables
		$uid_init=$purifier->purify($_GET['uid']);
	}
    // Recherche d'utilisateurs dans la branche people
	$filter="(uid=$uid_init)";
	$ldap_search_people_attr = array("gecos");

	$ds = @ldap_connect ( $ldap_server, $ldap_port );
	if ( $ds ) {
    		$r = @ldap_bind ( $ds ); // Bind anonyme
    		if ($r) {
      			// Recherche dans la branche people
      			$result = @ldap_search ( $ds, $dn["people"], $filter, $ldap_search_people_attr );
      			if ($result) {
        			$info = @ldap_get_entries ( $ds, $result );
        			if ( $info["count"]) {
          				for ($loop=0; $loop<$info["count"];$loop++) {
         					$gecos = $info[0]["gecos"][0];
         					$tmp = preg_split ("/,/",$info[0]["gecos"][0],4);
         					$date_naiss=$tmp[1];
         					echo "Vous avez choisi de r&#233;initialiser le mot de passe de l'utilisateur <b>$uid_init</b> avec sa date de naissance. <br/><br/>";
        		 			if (userChangedPwd($uid_init, $date_naiss, ''))
        		 				echo "<strong>Le mot de passe a &#233;t&#233; modifi&#233; avec succ&#232;s</strong><br>\n";
        		 			else
        		 				echo "<div class=error_msg><strong>Echec de la r&#233;initialisation du mot de passe !</strong><br></div>\n";
        		 		}
        			}

				@ldap_free_result ( $result );
      			} else {
        			$error = "Erreur de lecture dans l'annuaire LDAP";
      			}

    		} else {
      			$error = "Echec du bind anonyme";
    		}
    		@ldap_close ( $ds );
  	} else {
    		$error = "Erreur de connection au serveur LDAP";
  	}
  } else {
    echo "<div class=error_msg>Vous n'avez pas les droits n&eacute;cessaires pour utiliser cette fonctionnalit&eacute; !</div>";
  }
include ("../lcs/includes/pieds_de_page.inc.php");
?>
