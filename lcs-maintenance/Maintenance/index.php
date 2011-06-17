<?php
	include "Includes/basedir.inc.php";
	include "$BASEDIR/lcs/includes/headerauth.inc.php";
  	include "$BASEDIR/Annu/includes/ldap.inc.php";
  	include "$BASEDIR/Annu/includes/ihm.inc.php";
	include "Includes/func_maint.inc.php";

        // Register Global GET
        $mnuchoice=$_GET['mnuchoice'];
        $tri=$_GET['tri']; 
        $Rid=$_GET['Rid'];
        $action=$_GET['action'];

	html();

	list ($idpers,$uid)= isauth();
  	if ($idpers == "0") {
		// L'utilisateur n'est pas authentifié
		table_alert ("Vous devez préalablement vous authentifier sur votre &#171;Espace perso  LCS» pour accéder à cette application !");
	} else {
		// L'utilisateur est authentifié
		list($user, $groups)=people_get_variables($uid, false);
		// Initialisation de la variable mnuchoice si elle vide
		if ( !isset($mnuchoice) ) $mnuchoice="wait";
		// include des params de config de l'appli
		require "Includes/config.inc.php";
		// Traitements liés au chargement de la page
		# Suppression d'une demande
		if ( $action == "del_task" ) {
			if ($DEBUG) echo "DEBUG >> del task $Rid by $uid<br>";
			del_task($Rid, $user["fullname"]);
		} elseif ( $action == "del_cr" ) {
			del_cr($Rid);
		} elseif ( $action == "take" ) {
			if ( $DEBUG ) echo "DEBUG >> take task $Rid by ".$user["fullname"]."<br>";
			take_task($Rid, $user["fullname"], $user["email"]);
		} elseif ( $action=="relance" ) {
			boost_task ($Rid,  $user["fullname"], $user["email"]);
		}

		// Recherche si l'utilisateur authentifié a le droit Maint_is_admin
                if (is_admin("Maint_is_admin",$uid)=="Y") {
		 	$mode = "team";
			// Lecture des parametres LDAP associés à cet utilisateur
			list($user, $groups)=people_get_variables($uid, false);
			// Affichage du menu haut
			Aff_mnu($mode);
		//	echo '<div id="loadingContainer"><div id="loading">Chargement...</div></div>';

			if ($mnuchoice == "wait" ) {
				Aff_bar_mode ("En attente");
				// Affichage du feed en attente
				$filter ="Acq='0'";
				if (Is_feed ($mode, $filter, "<b>T.V.A. BIEN :-)</b> Il n'y a pas de demande d'intervention en attente !") )
                        		Aff_feed_wait($mode,$filter,$tri);
			} elseif ( $mnuchoice == "myspool" ) {
				Aff_bar_mode ("Votre Encours");
				$filter = "Acq='1' AND Author='".$user["fullname"]."'";
				if (Is_feed ($mode, $filter, "Vous n'avez pas d'intervention en cours. !") )
				Aff_feed_take($mode,$filter,$tri);
			} elseif ( $mnuchoice == "spool" ) {
				Aff_bar_mode ("Encours gen.");
				$filter = "Acq='1'";
				if (Is_feed ($mode, $filter, "Vous n'avez pas d'intervention en cours. !") )
				Aff_feed_take($mode,$filter,$tri);
			} elseif ( $mnuchoice == "myhistorique" ) {
				Aff_bar_mode ("Votre Historique");
				$filter = "Acq='2' AND Author='".$user["fullname"]."'";
				if (Is_feed ($mode, $filter, "Vous n'avez pas d'intervention clotur&eacute;e. !") )
				Aff_feed_close($mode,$filter,$tri);
			} elseif ( $mnuchoice == "historique") {
				Aff_bar_mode ("Historique gen.");
				$filter = "Acq='2' ";
				if (Is_feed ($mode, $filter, "Il  n'y a pas d'intervention clotur&eacute;e. !") )
				Aff_feed_close($mode,$filter,$tri);
			//	Aff_feed_closeTab($mode,$filter,$tri);
			}
		} else {
			// l'utilisateur ne fait pas partie de l'équipe de maintenance
			$mode = "user";
			Aff_mnu($mode);
			if ($mnuchoice == "wait" ) {
				Aff_bar_mode ("En attente");
				$filter = "Acq='0' AND Owner='".$user["fullname"]."'";
				if (Is_feed ($mode, $filter, "Vous n'avez pas de demande d'intervention en attente !") )
					Aff_feed_wait($mode,$filter,$tri);
			} elseif (  $mnuchoice == "myspool") {
				Aff_bar_mode ("Votre Encours");
				$filter = "Acq='1' AND Owner='".$user["fullname"]."'";
				if (Is_feed ($mode, $filter, "Vous n'avez pas de demande d'intervention en cours de traitement. !") )
				Aff_feed_take($mode,$filter,$tri);
			} elseif ( $mnuchoice == "myhistorique" ) {
				Aff_bar_mode ("Votre Historique");
				$filter = "Acq='2' AND Owner='".$user["fullname"]."'";
				if (Is_feed ($mode, $filter, "Vous n'avez pas de demande d'intervention clotur&eacute;e. !") )
				Aff_feed_close($mode,$filter,$tri);
			}
		}
	}
 	include "Includes/pieds_de_page.inc.php";
 	echo "<script>
 	$(document).ready(function(){
		$('div.mnu>a').removeClass('active');
 		$('div.mnu>a.".$mnuchoice ."').addClass('active');
 	});
 	</script>";
 ?>
