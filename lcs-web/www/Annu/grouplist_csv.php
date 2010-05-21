<?php

/* =============================================
   Projet LCS-SE3
   Consultation de l'annuaire LDAP
   Annu/grouplist_csv.php
   * @auteurs Equipe Tice academie de Caen
   Derniere modifications : 15/01/2010
   Distribue selon les termes de la licence GPL
   ============================================= */


//====================================
// Portion de code correspondant a la partie entete.inc.php sans l'affichage HTML

include "../lcs/includes/headerauth.inc.php";
include "ldap.inc.php";
include "ihm.inc.php";

list ($idpers,$login)= isauth();
if ($idpers == "0") header("Location:$urlauth");


// Prise en compte de la page demandee initialement - leb 25/6/2005
if ($login == "") {
	$request = $PHP_SELF;
	if ( $_SERVER['QUERY_STRING'] != "") $request .= "?".$_SERVER['QUERY_STRING'];
	echo "<script language=\"JavaScript\" type=\"text/javascript\">\n<!--\n";
	echo "top.location.href = '$urlauth?request=" . rawurlencode($request) . "';\n";
	echo "//-->\n</script>\n";
} else {
//====================================



	$filter=$_GET['filter'];

	if ((is_admin("Annu_is_admin",$login)=="Y") || (is_admin("sovajon_is_admin",$login)=="Y")) {


	require ("crob_ldap_functions.php");

	//==============================================
	function search_people_groups2 ($uids,$filter,$order) {


		/**

		* Recherche des utilisateurs dans la branche people a partir d'un tableau d'uids nons tries
		* Function: search_people_groups2


		* @Parametres 	$order - "cat"   => Tri par categorie (Eleves, Equipe...) - "group" => Tri par intitule de group (ex: 1GEA, TGEA...)
		* @Parametres $uids - Tableau d'uids d'utilisateurs
		* @Parametres $filter - Filtre de recherche

		* @Return Retourne un tableau des utilisateurs repondant au filtre de recherche
		*/

		// Fonction modifeie pour recueprer aussi le mail

		global $ldap_server, $ldap_port, $dn;
		global $error;
		$error="";

		// LDAP attributs
		$ldap_user_attr = array(
				"cn",                 // Nom complet
				"sn",                 // Nom
				"gecos",            // Nom prenom (cn sans accents), Date de naissance,Sexe (F/M),Status administrateur LCS (Y/N)
				"sexe",
				"mail"
		);

		if (!$filter) $filter="(sn=*)";
		$ds = @ldap_connect ( $ldap_server, $ldap_port );
		if ( $ds ) {
			$r = @ldap_bind ( $ds ); // Bind anonyme
			if ($r) {
				$loop1=0;
				for ($loop=0; $loop < count($uids); $loop++) {
					$result = @ldap_read ( $ds, "uid=".$uids[$loop]["uid"].",".$dn["people"], $filter, $ldap_user_attr );
					if ($result) {
						$info = @ldap_get_entries ( $ds, $result );
						if ( $info["count"]) {

							// Ajout pour r&#233;cup&#233;rer le mail:
							$attribut_tmp=array("mail");
							$tabtmp=get_tab_attribut("people", "uid=".$uids[$loop]["uid"], $attribut_tmp);
							$uids[$loop]["mail"]=$tabtmp[0];

							// traitement du gecos pour identification du sexe
							$gecos = $info[0]["gecos"][0];
							$tmp = split ("[\,\]",$gecos,4);
							#echo "debug ".$info["count"]." init ".$init." loop ".$loop."<BR>";
							$ret[$loop1] = array (
								"uid"           => $uids[$loop]["uid"],
								"fullname"      => utf8_decode($info[0]["cn"][0]),
								"name"          => utf8_decode($info[0]["sn"][0]),
								"sexe"          => $tmp[2],
								"owner"         => $uids[$loop]["owner"],
								"group"         => $uids[$loop]["group"],
								"cat"           => $uids[$loop]["cat"],
								"gecos"         => $gecos,
								"prof"          => $uids[$loop]["prof"],
								"mail"          => $uids[$loop]["mail"]
							);
							$loop1++;
						}

						@ldap_free_result ( $result );
					}
				}
			} else {
				$error = gettext("Echec du bind anonyme");
			}

			@ldap_close ( $ds );
		} else $error = gettext("Erreur de connection au serveur LDAP");


		if (count($ret)) {
			# Correction tri du tableau
			# Tri par critere categorie ou intitule de groupe
			if ( $order == "cat" ) usort ($ret, "cmp_cat");
			elseif ( $order == "group" ) usort ($ret, "cmp_group");
			# Recherche du nombre de catgories ou d'intitules de groupe
			$i = 0;
			for ( $loop=0; $loop < count($ret); $loop++) {
				if ( $ret[$loop][$order] != $ret[$loop-1][$order]) {
					$tab_order[$i] = $ret[$loop][$order];
					$i++;
				}
			}

			if (count($tab_order) > 0 ) {
				# On decoupe le tableau $ret en autant de sous tableaux $tmp que de criteres $order
				for ($i=0; $i < count($tab_order); $i++) {
					$j=0;
					for ( $loop=0; $loop < count($ret); $loop++) {
						if ( $ret[$loop][$order] == $tab_order[$i] ) {
							$ret_tmp[$i][$j] = $ret[$loop];
							$j++;
						}
					}
				}

				# Tri alpabetique des sous tableaux
				for ( $loop=0; $loop < count($ret_tmp); $loop++) usort ($ret_tmp[$loop], "cmp_name");

				# Reassemblage des tableaux temporaires
				$ret_final = array();
				for ($loop=0; $loop < count($tab_order); $loop++)  $ret_final = array_merge ($ret_final, $ret_tmp[$loop]);
				return $ret_final;
			} else {
				usort ($ret, "cmp_name");
				return $ret;
			}
		}
	}
	//==============================================


		$group=search_groups ("(cn=".$filter.")");
		$uids = search_uids ("(cn=".$filter.")","mode");
		//$people = search_people_groups ($uids,"(sn=*)","cat");
		$people = search_people_groups2 ($uids,"(sn=*)","cat");

		if (count($people)) {

			//$nom_fic = "nom_du_groupe.csv";
			$nom_fic = "$filter.csv";
			$now = gmdate('D, d M Y H:i:s') . ' GMT';
			header('Content-Type: text/x-csv');
			header('Expires: ' . $now);
			// lem9 & loic1: IE need specific headers
			if (ereg('MSIE', $_SERVER['HTTP_USER_AGENT'])) {
				header('Content-Disposition: inline; filename="' . $nom_fic . '"');
				header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				header('Pragma: public');
			} else {
				header('Content-Disposition: attachment; filename="' . $nom_fic . '"');
				header('Pragma: no-cache');
			}

			//$contenu_fichier='';
			$contenu_fichier="Login;Nom complet;Nom;Prenom;Naissance;Sexe;Email\n";

			for ($loop=0; $loop < count($people); $loop++) {
				ereg("([0-9]{8})",$people[$loop]["gecos"],$naiss);
				$contenu_fichier.=$people[$loop]["uid"].";".$people[$loop]["fullname"].";".$people[$loop]["name"].";".getprenom($people[$loop]["fullname"],$people[$loop]["name"]).";".$naiss[0].";".$people[$loop]["sexe"].";".$people[$loop]["mail"]."\n";
			}
			echo $contenu_fichier;
		} else {
			include "entete.inc.php";
			echo " <STRONG>".gettext("Pas de membres")." </STRONG> ".gettext(" dans le groupe")." $filter.<BR>";
			include ("../lcs/includes/pieds_de_page.inc.php");
		}
	}
}
?>
