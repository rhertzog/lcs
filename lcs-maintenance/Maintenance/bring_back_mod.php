<?php
      include "Includes/basedir.inc.php";
	include "$BASEDIR/lcs/includes/headerauth.inc.php";
  	include "$BASEDIR/Annu/includes/ldap.inc.php";
  	include "$BASEDIR/Annu/includes/ihm.inc.php";
	include "Includes/func_maint.inc.php";

        // Register Global GET
        $Rid=$_GET['Rid'];
        // Register Global POST
        $bringback=$_POST['bringback'];
        $TimeLife=$_POST['TimeLife'];
        $unit=$_POST['unit'];
        $Cost=$_POST['Cost'];
        $Close=$_POST['Close'];
        $post=$_POST['post'];
        $RidCR=$_POST['RidCR'];

	html();
	list ($idpers,$uid)= isauth();
  	if ($idpers == "0") {
		// L'utilisateur n'est pas authentifie 
		table_alert ("Vous devez pr&#233;alablement vous authentifier sur votre «Espace perso  LCS» pour acc&#233;der c cette application !");
	} else {
		// L'utilisateur est authentifie
		list($user, $groups)=people_get_variables($uid, false);
		// Initialisation de la variable mnuchoice si elle vide
		if ( !isset($mnuchoice) ) $mnuchoice="wait";
		// include des params de config de l'appli
		require "Includes/config.inc.php";
		// Recherche si l'utilisateur authentifie fait partie du groupe Info_Reseau
                if (is_admin("Maint_is_admin",$uid)=="Y") {
                        $mode = "team_CR";
			// Lecture des parametres LDAP associes a cet utilisateur
			list($user, $groups)=people_get_variables($uid, false);
			// Affichage du menu haut
			Aff_mnu($mode);
			$filter = "Rid='$Rid' AND Acq='1' AND Author='".$user["fullname"]."'";
			Aff_feed_take($mode,$filter, $tri);
			// Affichage du formulaire de redaction du rapport d'intervention
			Aff_bar_mode ("Modification rapport d'intervention");
			Aff_bringback_mod_form($Rid,$user["fullname"], $user["email"]);
			// A la validation le CR est poste et ajoute a la table maint_thread et retour a la page Encours
		} else {
			Aff_mnu($mode);
			table_alert ("Vous devez faire partie de l'&#233;quipe de maintenance informatique pour acc&#233;der &#233; cette fonctionalit&#233; !");
		}
	}
	include "Includes/pieds_de_page.inc.php";
?>
