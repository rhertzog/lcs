<?php
/*
 * $Id: visu_profs_eleve.php 2345 2008-09-02 20:16:07Z crob $
 *
 *  Copyright 2001, 2007 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
 *
 * This file is part of GEPI.
 *
 * GEPI is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * GEPI is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with GEPI; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

// Initialisations files
require_once("../lib/initialisations.inc.php");

// Resume session

$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
    header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
};

if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}
$error_login = false;
// Quelques filtrages de d�part pour pr�-initialiser la variable qui nous importe ici : $login_eleve
$login_eleve = isset($_GET['login_eleve']) ? $_GET['login_eleve'] : (isset($_POST['login_eleve']) ? $_POST["login_eleve"] : null);
if ($_SESSION['statut'] == "responsable") {
	$get_eleves = mysql_query("SELECT e.login " .
			"FROM eleves e, resp_pers r, responsables2 re " .
			"WHERE (" .
			"e.ele_id = re.ele_id AND " .
			"re.pers_id = r.pers_id AND " .
			"r.login = '".$_SESSION['login']."' AND (re.resp_legal='1' OR re.resp_legal='2'))");

	if (mysql_num_rows($get_eleves) == 1) {
		// Un seul �l�ve associ� : on initialise tout de suite la variable $login_eleve
		$login_eleve = mysql_result($get_eleves, 0);
	} elseif (mysql_num_rows($get_eleves) == 0) {
		$error_login = true;
	}
	// Si le nombre d'�l�ves associ�s est sup�rieur � 1, alors soit $login_eleve a �t� d�j� d�fini, soit il faut pr�senter le formulaire.

} else if ($_SESSION['statut'] == "eleve") {
	// Si l'utilisateur identifi� est un �l�ve, pas le choix, il ne peut consulter que son �quipe p�dagogique
	if ($login_eleve != null and (strtoupper($login_eleve) != strtoupper($_SESSION['login']))) {
		tentative_intrusion(2, "Tentative d'un �l�ve d'acc�der � l'�quipe p�dagogique d'un autre �l�ve.");
	}
	$login_eleve = $_SESSION['login'];
}

//**************** EN-TETE **************************************
$titre_page = "Equipe p�dagogique";
require_once("../lib/header.inc");
//**************** FIN EN-TETE **********************************

echo "<p class='bold'>";
echo "<a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
echo "</p>\n";

// Quelques v�rifications de droits d'acc�s.
if ($_SESSION['statut'] == "responsable" and $error_login == true) {
	echo "<p>Il semble que vous ne soyez associ� � aucun �l�ve. Contactez l'administrateur pour r�soudre cette erreur.</p>";
	require "../lib/footer.inc.php";
	die();
}

if (
	($_SESSION['statut'] == "responsable" AND getSettingValue("GepiAccesEquipePedaParent") != "yes") OR
	($_SESSION['statut'] == "eleve" AND getSettingValue("GepiAccesEquipePedaEleve") != "yes") OR
	($_SESSION['statut'] != "responsable" AND $_SESSION['statut'] != "eleve")
	) {
	tentative_intrusion(1, "Tentative d'acc�s � l'�quipe p�dagogique sans y �tre autoris�.");
	echo "<p>Vous n'�tes pas autoris� � visualiser cette page.</p>";
	require "../lib/footer.inc.php";
	die();
}

// Et une autre v�rification de s�curit� : est-ce que si on a un statut 'responsable' le $login_eleve est bien un �l�ve dont le responsable a la responsabilit�
if ($login_eleve != null and $_SESSION['statut'] == "responsable") {
	$test = mysql_query("SELECT count(e.login) " .
			"FROM eleves e, responsables2 re, resp_pers r " .
			"WHERE (" .
			"e.login = '" . $login_eleve . "' AND " .
			"e.ele_id = re.ele_id AND " .
			"re.pers_id = r.pers_id AND " .
			"r.login = '" . $_SESSION['login'] . "' AND (re.resp_legal='1' OR re.resp_legal='2'))");
	if (mysql_result($test, 0) == 0) {
	    tentative_intrusion(2, "Tentative par un parent d'acc�der � l'�quipe p�dagogique d'un �l�ve dont il n'est pas responsable l�gal.");
	    echo "Vous ne pouvez visualiser que les relev�s de notes des �l�ves pour lesquels vous �tes responsable l�gal.\n";
	    require("../lib/footer.inc.php");
		die();
	}
}

// Maintenant on arrive au code en lui-m�me.
// On commence par traiter le cas o� il faut s�lectionner un �l�ve (cas d'un responsable de plusieurs �l�ves)

if ($login_eleve == null and $_SESSION['statut'] == "responsable") {
	// Si on est l� normalement c'est parce qu'on a un responsable de plusieurs �l�ves qui n'a pas encore choisi d'�l�ve.
	$quels_eleves = mysql_query("SELECT e.login, e.nom, e.prenom " .
				"FROM eleves e, responsables2 re, resp_pers r WHERE (" .
				"e.ele_id = re.ele_id AND " .
				"re.pers_id = r.pers_id AND " .
				"r.login = '" . $_SESSION['login'] . "' AND (re.resp_legal='1' OR re.resp_legal='2'))");
    echo "<form enctype=\"multipart/form-data\" action=\"visu_profs_eleve.php\" method=\"post\">\n";
	echo "<table summary='Choix'>\n";
	echo "<tr>\n";
	echo "<td valign='top'>\n";
	echo "<span class='bold'>Choisissez l'�l�ve : </span>";
	echo "</td>\n";
	echo "<td valign='top'>\n";
	echo "<select size=\"".mysql_num_rows($quels_eleves)."\" name=\"login_eleve\">";
	$cpt=0;
	while ($current_eleve = mysql_fetch_object($quels_eleves)) {
		echo "<option value=".$current_eleve->login;
		if($cpt==0) {echo " selected='selected'";}
		echo ">" . $current_eleve->prenom . " " . $current_eleve->nom . "</option>\n";
		$cpt++;
	}
	echo "</select>\n";
	echo "</td>\n";
	echo "<td valign='top'>\n";
	echo "<input type='submit' value='Valider' />\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
    echo "</form>\n";

} else {
	// On a un �l�ve. On affiche l'�quipe p�dagogique !
	$eleve = mysql_query("SELECT e.nom, e.prenom FROM eleves e WHERE e.login = '".$login_eleve."'");
	$nom_eleve = mysql_result($eleve, 0, "nom");
	$prenom_eleve = mysql_result($eleve, 0, "prenom");
	//$id_classe = mysql_result(mysql_query("SELECT id_classe FROM j_eleves_classes WHERE login = '" . $login_eleve ."' LIMIT 1"), 0);

	echo "<h3>Equipe p�dagogique de l'�l�ve : ".$prenom_eleve ." " . $nom_eleve;

	$sql="SELECT jec.id_classe, c.* FROM j_eleves_classes jec, classes c WHERE jec.login='".$login_eleve."' AND jec.id_classe=c.id ORDER BY periode DESC LIMIT 1";
	$res_class=mysql_query($sql);
	if(mysql_num_rows($res_class)==0) {
		echo "</h3>\n";
		echo "<p>L'�l�ve n'est dans aucune classe???</p>\n";
		require "../lib/footer.inc.php";
		die();
	}
	$lig_clas=mysql_fetch_object($res_class);
	$id_classe=$lig_clas->id_classe;
	echo " de ".$lig_clas->nom_complet." (<i>".$lig_clas->classe."</i>)";
	/*
	$tmp_classes=get_noms_classes_from_ele_login($login_eleve);
	echo " (<i>";
	for($i=0;$i<count($tmp_classes);$i++) {
		if($i>0) {echo ", ";}
		echo $tmp_classes[$i];
	}
	echo "</i>)";
	*/
	echo "</h3>\n";

    echo "<table border='0' summary='Equipe'>\n";

    // On commence par le CPE
    $req = mysql_query("SELECT DISTINCT u.nom,u.prenom,u.email,u.show_email,jec.cpe_login " .
    		"FROM utilisateurs u,j_eleves_cpe jec " .
    		"WHERE jec.e_login='".$login_eleve."' AND " .
    		"u.login=jec.cpe_login " .
    		"ORDER BY jec.cpe_login");
    // Il ne doit y en avoir qu'un...
    $cpe = mysql_fetch_object($req);
    echo "<tr valign='top'><td>VIE SCOLAIRE</td>\n";
    echo "<td>";
    // On affiche l'email s'il est non nul, si le cpe l'a autoris�, et si l'utilisateur est autoris� par les droits d'acc�s globaux
    if ($cpe->email!="" AND $cpe->show_email == "yes" AND (
    	($_SESSION['statut'] == "responsable" AND
    			(getSettingValue("GepiAccesEquipePedaEmailParent") == "yes" OR
    			getSettingValue("GepiAccesCpePPEmailParent") == "yes"))
    	OR
    	($_SESSION['statut'] == "eleve" AND
    		(getSettingValue("GepiAccesEquipePedaEmailEleve") == "yes" OR
    		getSettingValue("GepiAccesEquipePedaEmailEleve") == "yes")
    		)
    	)){
        //echo "<a href='mailto:".$cpe->email."?".urlencode("subject=[GEPI] eleve : ".$prenom_eleve . " ".$nom_eleve)."'>".$cpe->nom . " ".ucfirst(strtolower($cpe->prenom))."</a>";
        echo "<a href='mailto:".$cpe->email."?".urlencode("subject=[GEPI] eleve : ".$prenom_eleve . " ".$nom_eleve)."'>".affiche_utilisateur($cpe->cpe_login,$id_classe)."</a>";
    } else {
        //echo $cpe->nom." ".ucfirst(strtolower($cpe->prenom));
		echo affiche_utilisateur($cpe->cpe_login,$id_classe);
    }
    echo "</td></tr>\n";

	// On passe maintenant les groupes un par un, sans se pr�occuper de la p�riode : on affiche tous les groupes
	// auxquel l'�l�ve appartient ou a appartenu
	$groupes = mysql_query("SELECT DISTINCT jeg.id_groupe, m.nom_complet " .
							"FROM j_eleves_groupes jeg, matieres m, j_groupes_matieres jgm, j_groupes_classes jgc WHERE " .
							"jeg.login = '".$login_eleve."' AND " .
							"m.matiere = jgm.id_matiere AND " .
							"jgm.id_groupe = jeg.id_groupe AND " .
							"jgc.id_groupe = jeg.id_groupe AND " .
							"jgc.id_classe = '".$id_classe . "' " .
							"ORDER BY jgc.priorite, m.matiere");
	while ($groupe = mysql_fetch_object($groupes)) {
		// On est dans la boucle 'groupes'. On traite les groupes un par un.

        // Mati�re correspondant au groupe:
        echo "<tr valign='top'><td>".htmlentities($groupe->nom_complet)."</td>\n";

        // Professeurs
        echo "<td>";
        $sql="SELECT jgp.login,u.nom,u.prenom,u.email,u.show_email FROM j_groupes_professeurs jgp,utilisateurs u WHERE jgp.id_groupe='".$groupe->id_groupe."' AND u.login=jgp.login";
        $result_prof=mysql_query($sql);
        while($lig_prof=mysql_fetch_object($result_prof)){

            // Le prof est-il PP de l'�l�ve ?
            $sql="SELECT * FROM j_eleves_professeurs WHERE login = '".$login_eleve."' AND professeur='".$lig_prof->login."'";
            $res_pp=mysql_query($sql);

			if($lig_prof->email!="" AND $lig_prof->show_email == "yes" AND
		    	(($_SESSION['statut'] == "responsable" AND
		    		(getSettingValue("GepiAccesEquipePedaEmailParent") == "yes"
		    			OR
		    		 (getSettingValue("GepiAccesCpePPEmailParent") == "yes" AND mysql_num_rows($res_pp)>0)
		    		 )
        		) OR (
				  $_SESSION['statut'] == "eleve" AND
		    		(getSettingValue("GepiAccesEquipePedaEmailEleve") == "yes"
		    			OR
		    		 (getSettingValue("GepiAccesCpePPEmailEleve") == "yes" AND mysql_num_rows($res_pp)>0)
		    		 )
		    	)
		    	)){
                //echo "<a href='mailto:$lig_prof->email?".urlencode("subject=[GEPI] eleve : ".$prenom_eleve . " " . $nom_eleve)."'>$lig_prof->nom ".ucfirst(strtolower($lig_prof->prenom))."</a>";
                echo "<a href='mailto:$lig_prof->email?".urlencode("subject=[GEPI] eleve : ".$prenom_eleve . " " . $nom_eleve)."'>".affiche_utilisateur($lig_prof->login,$id_classe)."</a>";
            }
            else{
                //echo "$lig_prof->nom ".ucfirst(strtolower($lig_prof->prenom));
				echo affiche_utilisateur($lig_prof->login,$id_classe);
            }


            if(mysql_num_rows($res_pp)>0){
                 echo " (<i>".getSettingValue('gepi_prof_suivi')."</i>)";
            }
            echo "<br />\n";
        }
        echo "</td>\n";
        echo "</tr>\n";
	}
	// On a fini le traitement.
	echo "</table>\n";

}

require "../lib/footer.inc.php";
?>