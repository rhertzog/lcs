<?php
/*
 *
 * $Id: admin_periodes_absences.php 4152 2010-03-21 23:32:16Z adminpaulbert $
 *
 * Copyright 2001, 2007 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Christian Chapel
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

$niveau_arbo = 2;
// Initialisations files
require_once("../../lib/initialisations.inc.php");
//les fonctions sp�cifiques
include("../lib/functions.php");
include("../../edt_organisation/fonctions_calendrier.php");

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
    header("Location: ../../utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ../../logout.php?auto=1");
    die();
};

// Check access
if (!checkAccess()) {
    header("Location: ../../logout.php?auto=1");
    die();
}
// header
$titre_page = "D�finition des cr�neaux horaires";
require_once("../../lib/header.inc");

	if (empty($_GET['action_sql']) and empty($_POST['action_sql'])) {$action_sql="";}
	   else { if (isset($_GET['action_sql'])) {$action_sql=$_GET['action_sql'];} if (isset($_POST['action_sql'])) {$action_sql=$_POST['action_sql'];} }
	if (empty($_GET['action']) and empty($_POST['action'])) {exit();}
	   else { if (isset($_GET['action'])) {$action=$_GET['action'];} if (isset($_POST['action'])) {$action=$_POST['action'];} }
	if (empty($_GET['id_periode']) and empty($_POST['id_periode'])) { $id_periode="";}
	   else { if (isset($_GET['id_periode'])) {$id_periode=$_GET['id_periode'];} if (isset($_POST['id_periode'])) {$id_periode=$_POST['id_periode'];} }
	if (empty($_GET['nb_ajout']) and empty($_POST['nb_ajout'])) { $nb_ajout="1";}
	   else { if (isset($_GET['nb_ajout'])) {$nb_ajout=$_GET['nb_ajout'];} if (isset($_POST['nb_ajout'])) {$nb_ajout=$_POST['nb_ajout'];} }
	if (empty($_GET['nom_definie_periode']) and empty($_POST['nom_definie_periode'])) { $nom_definie_periode=""; }
	   else { if (isset($_GET['nom_definie_periode'])) {$nom_definie_periode=$_GET['nom_definie_periode'];} if (isset($_POST['nom_definie_periode'])) {$nom_definie_periode=$_POST['nom_definie_periode'];} }
	if (empty($_GET['heuredebut_definie_periode']) and empty($_POST['heuredebut_definie_periode'])) { $heuredebut_definie_periode="";}
	   else { if (isset($_GET['heuredebut_definie_periode'])) {$heuredebut_definie_periode=$_GET['heuredebut_definie_periode'];} if (isset($_POST['heuredebut_definie_periode'])) {$heuredebut_definie_periode=$_POST['heuredebut_definie_periode'];} }
	if (empty($_GET['heurefin_definie_periode']) and empty($_POST['heurefin_definie_periode'])) { $heurefin_definie_periode="";}
	   else { if (isset($_GET['heurefin_definie_periode'])) {$heurefin_definie_periode=$_GET['heurefin_definie_periode'];} if (isset($_POST['heurefin_definie_periode'])) {$heurefin_definie_periode=$_POST['heurefin_definie_periode'];} }
	if (empty($_GET['suivi_definie_periode']) and empty($_POST['suivi_definie_periode'])) { $suivi_definie_periode = ''; }
	   else { if (isset($_GET['suivi_definie_periode'])) { $suivi_definie_periode = $_GET['suivi_definie_periode']; } if (isset($_POST['suivi_definie_periode'])) { $suivi_definie_periode = $_POST['suivi_definie_periode']; } }
	if (empty($_GET['type_creneaux']) and empty($_POST['type_creneaux'])) { $type_creneaux = ''; }
	   else { if (isset($_GET['type_creneaux'])) { $type_creneaux = $_GET['type_creneaux']; } if (isset($_POST['type_creneaux'])) { $type_creneaux = $_POST['type_creneaux']; } }

$jour_semaine = isset($_POST["jour_semaine"]) ? $_POST["jour_semaine"] : NULL;
$demande_jour_semaine = isset($_POST["demande_jour_semaine"]) ? $_POST["demande_jour_semaine"] : NULL;

// on pr�voit de passer syst�matiquement vers les cr�neaux du jour diff�rent si $cren est initialis�
// Dans ce cas, on appelle edt_creneaux_bis
$cren = isset($_GET["cren"]) ? $_GET["cren"] : (isset($_POST["cren"]) ? $_POST["cren"] : NULL);
if ($cren == "diff") {
	$aff_creneau_diff = '&amp;cren=diff';
	$choix_table = '_bis';
}else{
	$aff_creneau_diff = '';
	$choix_table = '';
}
$total = '0';
$verification[0] = '1';
$erreur = '0';

// On commence par sauvegarder le r�glage sur le jour o� les cr�neaux sont diff�rents si c'est demand�
	$creneau_different = getSettingValue("creneau_different"); // on charge la variable pour �viter les prob avec la fonction getSettingValue
if ($demande_jour_semaine == "ok_diff") {
	// On compare la demande avec le setting actuel
	if ($jour_semaine != getSettingValue("creneau_different")) {
		// On met � jour le setting
		$query = mysql_query("UPDATE setting SET value = '".$jour_semaine."' WHERE name = 'creneau_different'");
		$creneau_different = $jour_semaine;
	}else{
		$creneau_different = getSettingValue("creneau_different");
	}
}

if ($action_sql == "ajouter" or $action_sql == "modifier") {
	while ($total < $nb_ajout) {
		// V�rification des variables
		$nom_definie_periode_ins = $_POST['nom_definie_periode'][$total];
		$heuredebut_definie_periode_ins = $_POST['heuredebut_definie_periode'][$total];
		$heurefin_definie_periode_ins = $_POST['heurefin_definie_periode'][$total];
		if ( isset($type_creneaux[$total]) ) {
			$type_creneaux_ins = $type_creneaux[$total];
		} else {
			$type_creneaux_ins = 'cours';
		}
		if ( isset($suivi_definie_periode[$total]) ) {
			$suivi_definie_periode_ins = $suivi_definie_periode[$total];
		} else {
			$suivi_definie_periode_ins = '';
		}

		if ($action_sql == "modifier") {
			$id_definie_periode_ins = $_POST['id_periode'][$total];
		}
		// V�rification des champs nom et prenom (si il ne sont pas vides ?)
		if($nom_definie_periode_ins != "" && $heuredebut_definie_periode_ins != "" && $heurefin_definie_periode_ins != ""){
			if($heuredebut_definie_periode_ins != "00:00") {
				if($heurefin_definie_periode_ins != "00:00") {
					if($heurefin_definie_periode_ins > $heuredebut_definie_periode_ins) {
						if($action_sql == "ajouter") {
							$test = mysql_result(mysql_query("SELECT count(*) FROM ".$prefix_base."edt_creneaux".$choix_table." WHERE nom_definie_periode='$nom_definie_periode_ins' OR (heuredebut_definie_periode='$heuredebut_definie_periode_ins' AND heurefin_definie_periode='$heurefin_definie_periode_ins')"),0);
						}
						if($action_sql == "modifier") {
							$test = mysql_result(mysql_query("SELECT count(*) FROM ".$prefix_base."edt_creneaux".$choix_table." WHERE id_definie_periode != '$id_definie_periode_ins' AND (nom_definie_periode='$nom_definie_periode_ins' OR (heuredebut_definie_periode='$heuredebut_definie_periode_ins' AND heurefin_definie_periode='$heurefin_definie_periode_ins'))"),0);
						}
                        if ($test == "0") {
                        	if($action_sql == "ajouter") {
                        		// Requete d'insertion MYSQL
								$requete = "INSERT INTO ".$prefix_base."edt_creneaux".$choix_table." (nom_definie_periode,heuredebut_definie_periode,heurefin_definie_periode,suivi_definie_periode,type_creneaux) VALUES ('$nom_definie_periode_ins','$heuredebut_definie_periode_ins','$heurefin_definie_periode_ins', '$suivi_definie_periode_ins', '$type_creneaux_ins')";
							}
							if($action_sql == "modifier") {
								// Requete de mise � jour MYSQL
								$requete = "UPDATE ".$prefix_base."edt_creneaux".$choix_table." SET
											nom_definie_periode = '$nom_definie_periode_ins',
											heuredebut_definie_periode = '$heuredebut_definie_periode_ins',
											heurefin_definie_periode = '$heurefin_definie_periode_ins',
											suivi_definie_periode = '$suivi_definie_periode_ins',
											type_creneaux = '$type_creneaux_ins'
												WHERE id_definie_periode = '".$id_definie_periode_ins."' ";
							}
							// Execution de cette requete dans la base cartouche
							mysql_query($requete) or die('Erreur SQL !'.$sql.'<br />'.mysql_error());
							$verification[$total] = 1;
						} else {
							// v�rification = 2 - Ce cr�neaux horaires existe d�jas
							$verification[$total] = 2;
							$erreur = 1;
						}
					} else {
						// v�rification = 5 - L'heure de fin n'est pas d�finie
						$verification[$total] = 6;
						$erreur = 1;
					}
				} else {
					// v�rification = 5 - L'heure de fin n'est pas d�finie
					$verification[$total] = 5;
					$erreur = 1;
				}
			} else {
				// v�rification = 4 - L'heure de d�but n'est pas d�finie
				$verification[$total] = 4;
				$erreur = 1;
			}
		} else {
			// v�rification = 3 - Tous les champs ne sont pas remplie
			$verification[$total] = 3;
			$erreur = 1;
		}
		$total = $total + 1;
	} // fin du while

	if($erreur == 0) {
		$action = "visualiser";
	} else {
		$o = 0;
		$n = 0;
		while ($o < $nb_ajout) {
			if($verification[$o] != 1) {
				$nom_definie_periode_erreur[$n] = $nom_definie_periode[$o];
				$heuredebut_definie_periode_erreur[$n] = $heuredebut_definie_periode[$o];
				$heurefin_definie_periode_erreur[$n] = $heurefin_definie_periode[$o];
				$verification_erreur[$n] = $verification[$o];
				if ( isset($suivi_definie_periode[$o]) ) {
					$suivi_definie_periode_erreur[$n] = $suivi_definie_periode[$o];
				}
				if ($action_sql == "modifier") {
					$id_definie_periode_erreur[$n] = $id_periode[$o];
				}
				$n = $n + 1;
			}
			$o = $o + 1;
		}
		$nb_ajout = $n;
		if ($action_sql == "ajouter") {
			$action = "ajouter";
		}
		if ($action_sql == "modifier") {
			$action = "modifier";
		}
	}
} //if ($action_sql == "ajouter" or $action_sql == "modifier")

if ($action_sql == "supprimer") {
	//Requete d'insertion MYSQL
	$requete = "DELETE FROM ".$prefix_base."edt_creneaux".$choix_table." WHERE id_definie_periode ='$id_periode'";
	// Execution de cette requete
	mysql_query($requete) or die('Erreur SQL !'.$requete.'<br />'.mysql_error());
}

if ($action == "modifier") {
	$requete_modif_periode = 'SELECT * FROM '.$prefix_base.'edt_creneaux'.$choix_table.' WHERE id_definie_periode="'.$id_periode.'"';
	$resultat_modif_periode = mysql_query($requete_modif_periode) or die('Erreur SQL !'.$requete_modif_periode.'<br />'.mysql_error());
	$data_modif_periode = mysql_fetch_array($resultat_modif_periode);
}

echo "<p class=\"bold\">\n";
if ($action=="modifier" or $action=="ajouter") {
	echo "<a href=\"admin_periodes_absences.php?action=visualiser".$aff_creneau_diff."\">\n";
} else {
	// On veut distinguer le retour vers EdT ou Absences en fonction de la $_SESSION["retour"]
	if (isset($_SESSION["retour"]) AND $_SESSION["retour"] !== "") {
		$retour = "	<a href='../../edt_organisation/".$_SESSION["retour"].".php'>\n";
	} else {
		$retour = "	<a href='index.php'>\n";
	}
	echo $retour;
}
echo "	<img src='../../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>\n";
// On regarde la table utilis�e avant d'afficher le lien
	if ($cren == "diff") {
		$cherche_table = '';
		$texte_lien = ' Voir les cr�neaux de tous les jours';
	}else{
		$cherche_table = '&amp;cren=diff';
		$texte_lien = ' Voir les cr&eacute;neaux du jour diff&eacute;rent';
	}
		echo "| <a href='admin_periodes_absences.php?action=visualiser".$cherche_table."'>".$texte_lien."</a>
	</p>\n";


if ($action == "visualiser") {
	if ($cren == "diff") {
		// On peut cocher un seul jour diff�rent des autres
		// Si ce jour n'existe pas, on coche "aucun"
		if ($creneau_different == 'n') {
			$coche = ' checked="checked"';
		}else{
			$coche = '';
		}
		echo '<p>Vous devez choisir un seul jour o� les cr�neaux sont diff�rents des autres jours (comme le mercredi...)</p>';

		// On affiche alors une s�rie de radio qui correspondent aux jours de la semaine
		echo '
		<form name="choisir_jour_diff" action="admin_periodes_absences.php" method="post">
			<p>';
		for($c = 0; $c < 7; $c++){
			$id = $c;
			$jour = retourneJour($c); // le jour de la semaine en Fran�ais
			// On d�termine le coche
			if ($creneau_different == $c) {
				$coched = ' checked="checked"';
			}else{
				$coched = '';
			}
			echo '
			<label for="jourSemaine'.$c.'">'.$jour.'</label>
			<input type="radio" id="jourSemaine'.$c.'" name="jour_semaine" value="'.$c.'"'.$coched.' />
			';
		} // for($c...
		echo '
			<label for="aucunCren">Aucun</label>
			<input type="radio" id="aucunCren" name="jour_semaine" value="n"'.$coche.' />
			<input type="hidden" name="action" value="visualiser" />
			<input type="hidden" name="cren" value="diff" />
			<input type="hidden" name="demande_jour_semaine" value="ok_diff" />
			<input type="submit" name="valider" value="Enregistrer" />
		</p>
		</form>';
	}
// On teste la table des emplois du temps et on envoie un message ad�quat si elle est remplie
$query = mysql_query("SELECT * FROM edt_cours LIMIT 5");
$compter = mysql_num_rows($query);
if ($compter >= 1) {
	echo "<p class=\"red\">Attention, si vous modifiez les cr�neaux maintenant, les cours de l'emploi du temps seront perturb�s !</p>";
}
	/* div de centrage du tableau pour ie5 */
?>
<div style="text-align: center;">
<h2>D�finition des cr�neaux horaires</h2>
	<p>
		<a href="admin_periodes_absences.php?action=ajouter<?php echo $aff_creneau_diff; ?>">
		<img src='../../images/icons/add.png' alt='' class='back_link' /> Ajouter un cr�neau horaire
		</a>
	</p><br />

	<table cellpadding="0" cellspacing="1" class="tab_table" summary="Cr�neaux">
		<tr>
			<th class="tab_th" style="width: 80px;">code</th>
			<th class="tab_th" style="width: 90px;">heure de d�but</th>
			<th class="tab_th" style="width: 90px;">heure de fin</th>
			<th class="tab_th" style="width: 90px;">type</th>
			<th class="tab_th" style="width: 25px;"></th>
			<th class="tab_th" style="width: 25px;"></th>
		</tr>
<?php
	$requete_periode = 'SELECT * FROM '.$prefix_base.'edt_creneaux'.$choix_table.' ORDER BY heuredebut_definie_periode, nom_definie_periode ASC';

    $execution_periode = mysql_query($requete_periode) or die('Erreur SQL !'.$requete_periode.'<br />'.mysql_error());
    $i=1;
	while ( $data_periode = mysql_fetch_array( $execution_periode ) ) {
		if ($i === '1') {
			$i = '2';
			$couleur_cellule = 'couleur_ligne_1';
		} else {
			$couleur_cellule = 'couleur_ligne_2';
			$i = '1';
		}
		// Pour l'affichage, on enl�ve les secondes qui ne servent � rien.
		$expl_heuredebut = explode(":", $data_periode['heuredebut_definie_periode']);
		$heuredebut_creneau = $expl_heuredebut[0].":".$expl_heuredebut[1];
		$expl_heurefin = explode(":", $data_periode['heurefin_definie_periode']);
		$heurefin_creneau = $expl_heurefin[0].":".$expl_heurefin[1];
	?>
        <tr class="<?php echo $couleur_cellule; ?>">
          <td><?php echo $data_periode['nom_definie_periode']; ?></td>
          <td><?php echo $heuredebut_creneau; ?></td>
          <td><?php echo $heurefin_creneau; ?></td>
          <td><?php echo $data_periode['type_creneaux']; ?></td>
          <td><a href="admin_periodes_absences.php?action=modifier<?php echo $aff_creneau_diff; ?>&amp;id_periode=<?php echo $data_periode['id_definie_periode']; ?>"><img src="../../images/icons/configure.png" title="Modifier" border="0" alt="Modifier" /></a></td>
          <td><a href="admin_periodes_absences.php?action=visualiser<?php echo $aff_creneau_diff; ?>&amp;action_sql=supprimer&amp;id_periode=<?php echo $data_periode['id_definie_periode']; ?>" onClick="return confirm('Etes-vous certain de vouloir supprimer ce cr�neau ?')"><img src="../images/x2.png" width="22" height="22" title="Supprimer" border="0" alt="Supprimer" /></a></td>
        </tr>
     <?php } ?>
    </table>
<?php /* fin du div de centrage du tableau pour ie5 */ ?>
</div>
<br/><br/>
<?php } ?>


<?php if ($action == "ajouter" or $action == "modifier") { ?>
<div style="text-align:center">
  <?php if ($action == "ajouter") { ?>

	<h2>Ajout de cr�neaux horaires</h2>

    <form name="form1" method="post" action="admin_periodes_absences.php?action=ajouter<?php echo $aff_creneau_diff; ?>">
      <table class="tab_table" summary="Cr�neaux">
        <tr>
          <th class="tab_th">Nombre de cr�neaux horaires � ajouter</th>
        </tr>
        <tr>
			<td class="couleur_ligne_1" style="text-align: right;">
			<select name="nb_ajout" onchange='document.form1.submit();'>
<?php
	// On propose le nombre de cr�neaux � ajouter
for($a=1; $a<=15; $a++) {
	if (isset($nb_ajout)) {
		if ($a == $nb_ajout) {
			$selected = " selected='selected'";
		} else {
			$selected = "";
		}
	} else {
		$selected = "";
	}

	echo '<option value="'.$a.'"'.$selected.'>'.$a.'</option>
	';
}
echo '			</select>
	';


?>
			</td>
        </tr>
      </table>
    </form>
	<br />
  <?php }

    if ($action=="modifier") {
		echo "<h2>Modifier un cr�neau horaire</h2>";
	}
	?>
    <form action="admin_periodes_absences.php?action=visualiser<?php echo $aff_creneau_diff; ?>&amp;action_sql=<?php if($action=="ajouter") { ?>ajouter<?php } if($action=="modifier") { ?>modifier<?php } ?>" method="post" name="form2" id="form2">
      <table cellpadding="2" cellspacing="2" class="tab_table" summary="Cr�neaux">
        <tr>
          <th class="tab_th">Code</th>
          <th class="tab_th">Heure de d�but</th>
          <th class="tab_th">Heure de fin</th>
          <th class="tab_th">Type</th>
          <th class="tab_th">Suite logique</th>
        </tr>
        <?php
        $i = '1';
        $nb = 0;
        while($nb < $nb_ajout) {
        if ($i === '1') { $i = '2'; $couleur_cellule = 'couleur_ligne_1'; } else { $couleur_cellule = 'couleur_ligne_2'; $i = '1'; } ?>
        <?php if (isset($verification_erreur[$nb]) and $verification_erreur[$nb] != 1) { ?>
         <tr>
          <td><img src="../images/attention.png" width="28" height="28" alt="" /></td>
          <td colspan="3" class="erreur_rouge_jaune"><b>- Erreur -<br />
          <?php if ($verification_erreur[$nb] == 2) { ?>Ce cr�neau horaire existe d�ja<?php } ?>
          <?php if ($verification_erreur[$nb] == 5) { ?>L'heure de fin n'est pas d�finie<?php } ?>
          <?php if ($verification_erreur[$nb] == 4) { ?>L'heure de d�but n'est pas d�finie<?php } ?>
          <?php if ($verification_erreur[$nb] == 3) { ?>Tous les champs ne sont pas remplis<?php } ?>
          <?php if ($verification_erreur[$nb] == 6) { ?>L'heure de fin ne peut pas �tre plus petite que l'heure de d�but<?php } ?>
          </b><br /></td>
         </tr>
        <?php } ?>
        <tr class="<?php echo $couleur_cellule; ?>">
          <td><input name="nom_definie_periode[<?php echo $nb; ?>]" type="text" id="nom_definie_periode" size="10" maxlength="10" value="<?php if($action=="modifier") { echo $data_modif_periode['nom_definie_periode']; } elseif (isset($nom_definie_periode_erreur[$nb])) { echo $nom_definie_periode_erreur[$nb]; } ?>" class="input_sans_bord" /></td>
          <td><input name="heuredebut_definie_periode[<?php echo $nb; ?>]" type="text" id="heuredebut_definie_periode" size="5" maxlength="5" value="<?php if($action=="modifier") { echo $data_modif_periode['heuredebut_definie_periode']; } elseif (isset($heuredebut_definie_periode_erreur[$nb])) { echo $heuredebut_definie_periode_erreur[$nb]; } else { ?>00:00<?php } ?>" class="input_sans_bord" /></td>
          <td><input name="heurefin_definie_periode[<?php echo $nb; ?>]" type="text" id="heurefin_definie_periode" size="5" maxlength="5" value="<?php if($action=="modifier") { echo $data_modif_periode['heurefin_definie_periode']; } elseif (isset($heurefin_definie_periode_erreur[$nb])) { echo $heurefin_definie_periode_erreur[$nb]; } else { ?>00:00<?php } ?>" class="input_sans_bord" /></td>
          <td>
		<select name="type_creneaux[<?php echo $nb; ?>]" size="3" class="input_sans_bord">
			<option value="cours" <?php if ( $action === 'modifier' ) { if ( $data_modif_periode['type_creneaux'] === 'cours' ) { ?>selected="selected"<?php } } elseif (isset($type_creneaux_erreur[$nb])) { if ( $type_creneaux_erreur[$nb] === 'cours' ) { ?>selected="selected"<?php } } ?>>cours</option>
			<option value="pause" <?php if ( $action === 'modifier' ) { if ( $data_modif_periode['type_creneaux'] === 'pause' ) { ?>selected="selected"<?php } } elseif (isset($type_creneaux_erreur[$nb])) { if ( $type_creneaux_erreur[$nb] === 'pause' ) { ?>selected="selected"<?php } } ?>>pause</option>
			<option value="repas" <?php if ( $action === 'modifier' ) { if ( $data_modif_periode['type_creneaux'] === 'repas' ) { ?>selected="selected"<?php } } elseif (isset($type_creneaux_erreur[$nb])) { if ( $type_creneaux_erreur[$nb] === 'repas' ) { ?>selected="selected"<?php } } ?>>repas</option>
		</select>
	  <td><input name="suivi_definie_periode[<?php echo $nb; ?>]" value="1" type="checkbox" <?php if ( ( $action === 'modifier' and $data_modif_periode['suivi_definie_periode'] === '1' ) or ( isset($suivi_definie_periode_erreur[$nb]) and $suivi_definie_periode_erreur[$nb] === '1') ) { ?>checked="checked"<?php } ?> title="suite logique des horaires" />

            <?php if($action=="modifier") { ?>
              <input type="hidden" name="id_periode[<?php echo $nb; ?>]" value="<?php if (isset($id_definie_periode_erreur[$nb])) { echo $id_definie_periode_erreur[$nb]; } else { echo $id_periode; } ?>" />
            <?php } ?>
	</td>
        </tr>
        <?php $nb = $nb + 1; } ?>
      </table>

      <input type="hidden" name="nb_ajout" value="<?php echo $nb_ajout; ?>" />
      <br/>
      <input type="submit" name="Submit" value="Enregistrer" />
    </form>
<?php /* fin du div de centrage du tableau pour ie5 */ ?>
</div>
<?php }

require("../../lib/footer.inc.php");

?>
