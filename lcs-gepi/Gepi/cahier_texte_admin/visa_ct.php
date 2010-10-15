<?php
/*
 * @version: $Id: visa_ct.php 4837 2010-07-19 15:23:55Z regis $
 *
 * Copyright 2001, 2005 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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


// INSERT INTO `droits`  VALUES ('/cahier_texte_admin/visa_ct.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Page de signature des cahiers de texte', '');
// ALTER TABLE `ct_devoirs_entry` ADD `vise` CHAR( 1 ) NOT NULL DEFAULT 'n' AFTER `contenu` ;
// ALTER TABLE `ct_entry` ADD `vise` VARCHAR( 1 ) NOT NULL DEFAULT 'n' AFTER `contenu` ;
// ALTER TABLE `ct_entry` ADD `visa` VARCHAR( 1 ) NOT NULL DEFAULT 'n' AFTER `vise` ;


// Initialisations files
require_once("../lib/initialisations.inc.php");
//debug_var();
// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
};
// Check access
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

include("../fckeditor/fckeditor.php") ;

$texte_visa_cdt = getSettingValue("texte_visa_cdt");

if (isset($_POST['ok_enr_visa'])) {
	$error = false;
	if	(isset($_POST['texte_visa_FCK'])) {
			$txt = html_entity_decode_all_version($_POST['texte_visa_FCK']);
			if (!saveSetting("texte_visa_cdt", $txt)) {
				$msg .= "Erreur lors de l'enregistrement du texte du visa !";
				$erreur = true;
			}
	}
    if (!$error) {
    	$msg = "Le visa a bien �t� enregistr�.";
    }
}

if (isset($_POST['begin_day']) and isset($_POST['begin_month']) and isset($_POST['begin_year'])) {
    $date_signature = mktime(0,0,0,$_POST['begin_month'],$_POST['begin_day'],$_POST['begin_year']);
    if (!saveSetting("date_signature", $date_signature)) $msg .= "Erreur lors de l'enregistrement de date de signature des cahiers de textes !";
}

//on r�cup�re la date butoir pour la signture des CDT
$date_signature = getSettingValue("date_signature");

// visa d'un ou plusieurs cahiers de texte
if (isset($_POST['visa_ct'])) {
  // les entr�es
  // on vise les notices (le champs vise de la table ct_entry est mis � 'y')
  $query = sql_query("SELECT DISTINCT id_groupe, id_login FROM ct_entry ORDER BY id_groupe");
  $msg = '';
  $iterateur = 0;
  for ($i=0; ($row=sql_row($query,$i)); $i++) {
      $id_groupe = $row[0];
      $id_prop = $row[1];
      $temp = "visa_".$iterateur;
    if (isset($_POST[$temp])) {
      $error = 'no';
      $id_groupe = isset($_POST["groupe_".$iterateur]) ? $_POST["groupe_".$iterateur] : NULL;
      $id_prop = isset($_POST["prof_".$iterateur]) ? $_POST["prof_".$iterateur] : NULL;

      $sql_visa_ct = "UPDATE `ct_entry` SET `vise` = 'y' WHERE ((id_groupe='".$id_groupe."' and id_login = '".$id_prop."') and (date_ct<$date_signature))";
      //echo $sql_visa_ct;
      $visa_ct = sql_query($sql_visa_ct);

      // On ajoute une notice montrant la signature du cahier de texte
      //$aujourdhui = mktime(0,0,0,date("m"),date("d"),date("Y"));
      $aujourdhui = date("U");

      $id_sequence="0";
      $sql_insertion_visa = "INSERT INTO `ct_entry` VALUES (NULL, '00:00:00', '".$id_groupe."', '".$aujourdhui."', '".$id_prop."', '".$id_sequence."', '".$texte_visa_cdt."', 'y', 'y')";
      //echo $sql_insertion_visa;
      $insertion_visa = sql_query($sql_insertion_visa);
      if ($error == 'no') {
        $msg = "Cahier(s) de textes sign�(s).";
      } else {
        $msg = "Il y a eu un probl�me lors de la signature du cahier de textes.";
      }
    }
    $iterateur++;
  }

  $query = sql_query("SELECT DISTINCT id_groupe, id_login FROM ct_devoirs_entry ORDER BY id_groupe");
  //les devoirs
  // on vise les notices devoirs (le champs vise de la table ct_devoirs_entry est mis � 'y')
  $itera = 0;
  for ($i=0; ($row=sql_row($query,$i)); $i++) {
      $id_groupe = $row[0];
      $id_prop = $row[1];
      $temp = "visa_".$itera;

      if (isset($_POST[$temp])) {
         $error = 'no';
         $id_professeur = isset($_POST["prof_".$itera]) ? $_POST["prof_".$itera] : NULL;
         $id_groupe = isset($_POST["groupe_".$itera]) ? $_POST["groupe_".$itera] : NULL;
  
		 $sql_visa_ct = "UPDATE `ct_devoirs_entry` SET `vise` = 'y' WHERE ((id_groupe='".$id_groupe."' and id_login = '".$id_professeur."') and (date_ct<$date_signature))";
		 //echo $sql_visa_ct;
         $visa_ct = sql_query($sql_visa_ct);

    }
    $itera++;
  }
}



// header
$titre_page = "Signature des cahiers de textes";
require_once("../lib/header.inc");


if (!(isset($_GET['action']))) {
  // Affichage du tableau complet

  if ($_SESSION['statut'] == "autre"||$_SESSION['statut']== "scolarite") {
	echo "<p class=\"bold\"><a href=\"../accueil.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil</a>";
  } else {
    echo "<p class=\"bold\"><a href=\"index.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil</a>";
  }

  ?>

  </p>
  <h2>Signature des cahiers de textes</h2>
  <p>Le tableau ci-dessous pr�sente l'ensemble des cahiers de textes actuellement en ligne.</p>
<ul>
  <li>&nbsp;&nbsp;Vous pouvez trier le tableau par le groupe ou le propri�taire d'un cahier de textes en cliquant sur le lien correspondant.</li>
  <li>&nbsp;&nbsp;Vous pouvez visualiser un cahier de textes .</li>
  <li>&nbsp;&nbsp;Vous pouvez �galement signer un ou plusieurs cahiers de textes avec le texte ci-dessous (cliquer sur enregistrer pour le sauvegarder).</li>
</ul>
  <br /><br />

<?PHP
echo "<div style='width: 750px;'>\n";
echo "<fieldset style=\"border: 1px solid grey; font-size: 0.8em; padding-top: 8px; padding-bottom: 8px;  margin-left: auto; margin-right: auto;\">\n";

echo "<form enctype=\"multipart/form-data\" action=\"visa_ct.php\" method=\"post\">\n";

echo "<h2 class='gepi' style=\"text-align: center;\">Texte du visa � apposer sur les cahiers de textes</h2>\n";
echo "<p><em>Mise en forme du visa :</em></p><p>\n";

$oFCKeditor = new FCKeditor('texte_visa_FCK') ;
$oFCKeditor->BasePath = '../fckeditor/' ;
$oFCKeditor->Config['DefaultLanguage']  = 'fr' ;
$oFCKeditor->ToolbarSet = 'Basic' ;
$oFCKeditor->Value      = $texte_visa_cdt ;
$oFCKeditor->Create() ;

echo "<input type='submit' name=\"ok_enr_visa\" value='Enregistrer le visa' /></p>\n";
echo "</form>\n";
echo "<br /><br />";

echo "</fieldset>";
echo "<br /><br />\n";
echo "</div>\n";
?>

  <form action="visa_ct.php" method="post">
  <table border="1"><tr valign='middle' align='center'>
  <td><b><a href='visa_ct.php?order_by=jc.id_classe,jm.id_matiere'>Classe(s)</a></b></td>
  <td><b><a href='visa_ct.php?order_by=jm.id_matiere,jc.id_classe'>Groupe</a></b></td>
  <td><b><a href='visa_ct.php?order_by=ct.id_login,jc.id_classe,jm.id_matiere'>Propri�taire</a></b></td>
  <td><b>Nombre<br />de notices</b></td>
  <td><b>Nombre<br />de notices<br />"devoirs"</b></td>
  <td>
  <b>Action</b></td>
  <td><b><input type="submit" name="visa_ct" value="Signer les cahiers" onclick="return confirmlink(this, 'La signature d\'un cahier de texte est d�finitive. Etes-vous s�r de vouloir continuer ?', 'Confirmation de la signature')" /></b>
  <p><b>dont la date est inf�rieure au</b></p>
  <?php
        $bday = strftime("%d", getSettingValue("date_signature"));
        $bmonth = strftime("%m", getSettingValue("date_signature"));
        $byear = strftime("%Y", getSettingValue("date_signature"));
        genDateSelector("begin_", $bday, $bmonth, $byear,"more_years") ?>
  </td>
  
  <td><b>Nombre de visa</b></td></tr>

  <?php
  if (!isset($_GET['order_by'])) {
     $order_by = "jc.id_classe,jm.id_matiere";
  } else {
     $order_by = $_GET['order_by'];
  }

  $iter = 0; // it�rateur

  $query = sql_query("SELECT DISTINCT ct.id_groupe, ct.id_login FROM ct_entry ct, j_groupes_classes jc, j_groupes_matieres jm WHERE (jc.id_groupe = ct.id_groupe AND jm.id_groupe = ct.id_groupe) ORDER BY ".$order_by);
  for ($i=0; ($row=sql_row($query,$i)); $i++) {
      $id_groupe = $row[0];
      $id_prop = $row[1];
      $nom_groupe = sql_query1("select name from groupes where id = '".$id_groupe."'");
      $nom_matiere = sql_query1("select m.nom_complet from matieres m, j_groupes_matieres jm where (jm.id_groupe = '".$id_groupe."' AND m.matiere = jm.id_matiere)");
      $get_classes = mysql_query("SELECT c.id, c.classe FROM classes c, j_groupes_classes jc WHERE (c.id = jc.id_classe and jc.id_groupe = '" . $id_groupe . "')");
      $nb_classes = mysql_num_rows($get_classes);
      $id_classe = mysql_result($get_classes, 0, "id"); // On ne garde qu'un id pour ne pas perturber le GET ensuite
      $classes = null;
      for ($c=0;$c<$nb_classes;$c++) {
      	$current_classe = mysql_result($get_classes, $c, "classe");
      	$classes .= $current_classe;
      	if ($c+1<$nb_classes) $classes .= ", ";
      }

      if ($nom_groupe == "-1") $nom_groupe = "<font color='red'>Groupe inexistant</font>";
      $sql_prof = sql_query("select nom, prenom from utilisateurs where login = '".$id_prop."'");
      if (!($sql_prof)) {
         $nom_prof = "<font color='red'>".$id_prop." : utilisateur inexistant</font>";
      } else {
         $row_prof=sql_row($sql_prof,0);
         $nom_prof = $row_prof[1]." ".$row_prof[0];
         $test_groupe_prof = sql_query("select login from j_groupes_professeurs WHERE (id_groupe='".$id_groupe."' and login = '".$id_prop."')");
         if (sql_count($test_groupe_prof) == 0) $nom_prof = "<font color='red'>".$nom_prof." : <br />Ce professeur n'enseigne pas dans ce groupe</font>";
      }
      // Nombre de notices de chaque utilisateurs
      $nb_ct = sql_count(sql_query("select 1=1 FROM ct_entry WHERE (id_groupe='".$id_groupe."' and id_login='".$id_prop."' AND visa != 'y') "));

      // Nombre de notices devoirs de haque utilisateurs
      $nb_ct_devoirs = sql_count(sql_query("select 1=1 FROM ct_devoirs_entry WHERE (id_groupe='".$id_groupe."' and id_login='".$id_prop."') "));

	  //Nombre de visa sur un cahier de texte
	  $nb_ct_visa = sql_count(sql_query("select 1=1 FROM ct_entry WHERE (id_groupe='".$id_groupe."' and id_login='".$id_prop."' and visa ='y') "));


      // Affichage des lignes
      echo "<tr><td>".$classes."</td>";
      echo "<td>".$nom_groupe."</td>";
      echo "<td>".$nom_prof."</td>";
      echo "<td>".$nb_ct."</td>";
      echo "<td>".$nb_ct_devoirs."</td>";
	// Modif pour le statut 'autre'
	if ($_SESSION["statut"] == 'autre' OR $_SESSION["statut"] == 'administrateur' OR $_SESSION["statut"] == 'scolarite') {
		echo '<td><a href="../cahier_texte/see_all.php?id_groupe='.$id_groupe.'&amp;id_classe='.$id_classe.'">Voir</a></td>';
	}else{
		echo "<td><a href='../public/index.php?id_groupe=".$id_groupe."' target='_blank'>Voir</a></td>";
	}
      echo "<td><center><input type=\"checkbox\" name=\"visa_".$iter."\" />
                        <input type=\"hidden\" name=\"prof_".$iter."\" value=\"".$id_prop."\" />
                        <input type=\"hidden\" name=\"groupe_".$iter."\" value=\"".$id_groupe."\" />
            </center></td>";
	  echo "<td>".$nb_ct_visa."</td>";
      echo "</tr>";
    $iter++;
  }
  echo "</table></form>";
}
require ("../lib/footer.inc.php");
?>