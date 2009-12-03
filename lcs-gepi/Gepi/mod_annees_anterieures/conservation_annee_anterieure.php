<?php
/*
 * $Id : $
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

// Initialisations files
require_once("../lib/initialisations.inc.php");
include("../lib/initialisationsPropel.inc.php");
require_once("./fonctions_annees_anterieures.inc.php");

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
    header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();};

// INSERT INTO droits VALUES ('/mod_annees_anterieures/conservation_annee_anterieure.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Conservation des donn�es ant�rieures', '');
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}



/*
$prof=isset($_POST['prof']) ? $_POST['prof'] : NULL;
$page=isset($_POST['page']) ? $_POST['page'] : NULL;
$enregistrer=isset($_POST['enregistrer']) ? $_POST['enregistrer'] : NULL;
*/

$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : NULL;
$deja_traitee_id_classe=isset($_POST['deja_traitee_id_classe']) ? $_POST['deja_traitee_id_classe'] : NULL;
$annee_scolaire=isset($_POST['annee_scolaire']) ? $_POST['annee_scolaire'] : NULL;
$confirmer=isset($_POST['confirmer']) ? $_POST['confirmer'] : NULL;

// Si le module n'est pas activ�...
if($gepiSettings['active_annees_anterieures'] !="y"){
	// A DEGAGER
	// A VOIR: Comment enregistrer une tentative d'acc�s illicite?

	header("Location: ../logout.php?auto=1");
	die();
}

$msg="";
/*
if(isset($enregistrer)){

	if($msg==""){
		$msg="Enregistrement r�ussi.";
	}

	unset($page);
}
*/

// Suppression des donn�es archiv�es pour une ann�e donn�e.
if (isset($_GET['action']) and ($_GET['action']=="supp_annee")) {
        $sql="DELETE FROM archivage_disciplines WHERE annee='".$_GET["annee_supp"]."';";
		$res_suppr1=mysql_query($sql);

		// Maintenant, on regarde si l'ann�e est encore utilis�e dans archivage_types_aid
		// Sinon, on supprime les entr�es correspondantes � l'ann�e dans archivage_eleves2 car elles ne servent plus � rien.
		$test = sql_query1("select count(annee) from archivage_types_aid where annee='".$_GET['annee_supp']."'");
		if ($test == 0) {
            $sql="DELETE FROM archivage_eleves2 WHERE annee='".$_GET["annee_supp"]."';";
            $res_suppr2=mysql_query($sql);
		} else {
            $res_suppr2 = 1;
        }

        $sql="DELETE FROM archivage_ects WHERE annee='".$_GET["annee_supp"]."';";
		$res_suppr3=mysql_query($sql);

    // Maintenant, il faut supprimer les donn�es �l�ves qui ne servent plus � rien
    suppression_donnees_eleves_inutiles();

		if (($res_suppr1) and ($res_suppr2) and ($res_suppr3)) {
			$msg = "La suppression des donn�es a �t� correctement effectu�e.";
		} else {
			$msg = "Un ou plusieurs probl�mes ont �t� rencontr�s lors de la suppression.";
		}

}

$themessage  = 'Etes-vous s�r de vouloir supprimer toutes les donn�es concerant cette ann�e ?';

//**************** EN-TETE *****************
$titre_page = "Conservation des donn�es ant�rieures (autres que AID)";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

echo "<form enctype=\"multipart/form-data\" name= \"formulaire\" action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">\n";

if(!isset($annee_scolaire)){
	echo "<div class='norme'><p class=bold><a href='./index.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a> | \n";
	echo "</p></div>\n";

	$sql="SELECT DISTINCT annee FROM archivage_disciplines ORDER BY annee";
	$res_annee=mysql_query($sql);
	//if(){
	if(mysql_num_rows($res_annee)==0){
		echo "<p>Concernant les donn�es autres que les AIDs, aucune ann�e n'est encore sauvegard�e.</p>\n";
	}
	else{
		echo "<p>Voici la liste des ann�es sauvegard�es:</p>\n";
		echo "<ul>\n";
		while($lig_annee=mysql_fetch_object($res_annee)){
			$annee_scolaire=$lig_annee->annee;
			echo "<li><b>Ann�e $annee_scolaire (<a href='".$_SERVER['PHP_SELF']."?action=supp_annee&amp;annee_supp=".$annee_scolaire."'   onclick=\"return confirm_abandon (this, 'yes', '$themessage')\">Supprimer toute les donn�es archiv�es pour cette ann�e</a>) :<br /></b> ";
			$sql="SELECT DISTINCT classe FROM archivage_disciplines WHERE annee='$annee_scolaire' ORDER BY classe;";
			$res_classes=mysql_query($sql);
			if(mysql_num_rows($res_classes)==0){
				echo "Aucune classe???";
			}
			else{
				$lig_classe=mysql_fetch_object($res_classes);
				echo $lig_classe->classe;

				while($lig_classe=mysql_fetch_object($res_classes)){
					echo ", ".$lig_classe->classe;
				}
			}
			echo "</li>\n";
		}
		echo "</ul>\n";
		echo "<p><br /></p>\n";

	}
	echo "<p>Sous quel nom d'ann�e voulez-vous sauvegarder l'ann�e?</p>\n";
	$default_annee=getSettingValue('gepiYear');

	if($default_annee==""){
		$instant=getdate();
		$annee=$instant['year'];
		$mois=$instant['mon'];

		$annee2=$annee+1;
		$default_annee=$annee."-".$annee2;
	}

	echo "<p>Ann�e: <input type='text' name='annee_scolaire' value='$default_annee' /></p>\n";

	echo "<center><input type=\"submit\" name='ok' value=\"Valider\" style=\"font-variant: small-caps;\" /></center>\n";

}
else{
	echo "<div class='norme'><p class=bold><a href='./index.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a> | \n";

	$sql="SELECT DISTINCT classe FROM archivage_disciplines WHERE annee='$annee_scolaire'";
	$res_test=mysql_query($sql);

	if(mysql_num_rows($res_test)>0){
		if(!isset($confirmer)){
			echo "</p></div>\n";

			$lig_classe=mysql_fetch_object($res_test);
			$chaine_classes=$lig_classe->classe;

			if(mysql_num_rows($res_test)>1){
				while($lig_classe=mysql_fetch_object($res_test)){
					$chaine_classes.=", ".$lig_classe->classe;
				}

				echo "<p>Des donn�es ont d�j� �t� sauvegard�es pour l'ann�e $annee_scolaire (<i>classes de $chaine_classes</i>).<br />Si vous confirmez, ces donn�es seront �cras�es avec les nouvelles donn�es (<i>si vous ne cochez pas les m�mes classes, les donn�es seront seulement compl�t�es</i>).</p>\n";
			}
			else{
				echo "<p>Des donn�es ont d�j� �t� sauvegard�es pour l'ann�e $annee_scolaire (<i>classe de $chaine_classes</i>).<br />Si vous confirmez, ces donn�es seront �cras�es avec les nouvelles donn�es (<i>si vous ne cochez pas les m�mes classes, les donn�es seront seulement compl�t�es</i>).</p>\n";
			}

			echo "<input type='hidden' name='annee_scolaire' value='$annee_scolaire' />\n";

			echo "<center><input type=\"submit\" name='confirmer' value=\"Confirmer\" style=\"font-variant: small-caps;\" /></center>\n";
			echo "</form>\n";
			require("../lib/footer.inc.php");
			die();
		}
	}

	if(!isset($id_classe)){
		echo "</p></div>\n";

		echo "<h2>Choix des classes</h2>\n";

		echo "<p>Conservation des donn�es pour l'ann�e scolaire: $annee_scolaire</p>\n";

		echo "<p>Choisissez les classes dont vous souhaitez archiver les r�sultats, appr�ciations,...</p>";
		echo "<p>Tout <a href='javascript:modif_coche(true)'>cocher</a> / <a href='javascript:modif_coche(false)'>d�cocher</a>.</p>";


		// Afficher les classes pour lesquelles les donn�es sont d�j� migr�es...

		$sql="SELECT id,classe FROM classes ORDER BY classe";
		$res1=mysql_query($sql);
		$nb_classes=mysql_num_rows($res1);
		if($nb_classes==0){
			echo "<p>ERREUR: Il semble qu'aucune classe ne soit encore d�finie.</p>\n";
			require("../lib/footer.inc.php");
			die();
		}

		// Affichage sur 3 colonnes
		$nb_classes_par_colonne=round($nb_classes/3);

		echo "<table width='100%' summary='Choix des classes'>\n";
		echo "<tr valign='top' align='center'>\n";

		$i = 0;

		echo "<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>\n";
		echo "<td align='left'>\n";

		while ($i < $nb_classes) {

			if(($i>0)&&(round($i/$nb_classes_par_colonne)==$i/$nb_classes_par_colonne)){
				echo "</td>\n";
				echo "<td align='left'>\n";
			}

			$lig_classe=mysql_fetch_object($res1);

			echo "<input type='checkbox' id='classe".$i."' name='id_classe[]' value='$lig_classe->id' /><label for='classe".$i."' style='cursor:pointer;'> $lig_classe->classe</label><br />\n";

			$i++;
		}
		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>\n";

		echo "<script type='text/javascript'>
			function modif_coche(statut){
				for(k=0;k<$i;k++){
					if(document.getElementById('classe'+k)){
						document.getElementById('classe'+k).checked=statut;
					}
				}
				//changement();
			}
		</script>\n";

		echo "<input type='hidden' name='annee_scolaire' value='$annee_scolaire' />\n";
		echo "<input type='hidden' name='confirmer' value='ok' />\n";
		echo "<center><input type=\"submit\" name='ok' value=\"Valider\" style=\"font-variant: small-caps;\" /></center>\n";

	}
	else{
		echo "<a href='".$_SERVER['PHP_SELF']."'>Choisir d'autres classes</a> | ";
		echo "</div>\n";

		if(count($id_classe)==0){
			echo "<p>ERREUR: Vous n'avez pas coch� de classe.</p>\n";
			echo "</form>\n";
			require("../lib/footer.inc.php");
			die();
		}

		/*
		echo "<p>Mise � jour du calcul du rang des �l�ves dans les mati�res...</p>\n";
		include "../lib/periodes.inc.php";
		include("../lib/calcul_rang.inc.php");
		*/




		//===================================

		if(isset($deja_traitee_id_classe)){
			echo "<p>Classes d�j� trait�es: ";

			echo "<input type='hidden' name='deja_traitee_id_classe[]' value='$deja_traitee_id_classe[0]' />";
			echo get_nom_classe($deja_traitee_id_classe[0]);

			for($i=1;$i<count($deja_traitee_id_classe);$i++){
				echo "<input type='hidden' name='deja_traitee_id_classe[]' value='$deja_traitee_id_classe[$i]' />";
				echo ", ".get_nom_classe($deja_traitee_id_classe[$i]);
			}
			echo "</p>\n";
		}

		$temoin_encore_des_classes=0;
		//$chaine="";
		$chaine=get_nom_classe($id_classe[0]);
		for($i=1;$i<count($id_classe);$i++){
			echo "<input type='hidden' name='id_classe[]' value='$id_classe[$i]' />\n";
			$temoin_encore_des_classes++;

			$chaine.=", ".get_nom_classe($id_classe[$i]);
		}
		//if($chaine!=""){
		if($temoin_encore_des_classes>0) {
			// Pour faire sauter un "' ":
			//echo "<p>Classes restant � traiter: ".substr($chaine,2)."</p>\n";
			echo "<p>Classes restant � traiter: ".$chaine."</p>\n";
		}
		else {
			echo "<p>Traitement de la derni�re classe s�lectionn�e: <span id='annonce_fin_traitement' style='font-weight:bold; font-size:2em; color: green;'></span></p>\n";
		}
		//===================================



		$classe=get_nom_classe($id_classe[0]);

		echo "<p><b>Classe de $classe:</b></p>\n";

		//echo "<p>Classe de $classe</p>\n";

		// Boucle sur les p�riodes de la classe
		$sql="SELECT * FROM periodes WHERE id_classe='".$id_classe[0]."' ORDER BY num_periode";
		$res_periode=mysql_query($sql);

		if(mysql_num_rows($res_periode)==0){
			echo "<p>Aucune p�riode ne semble d�finie pour la classe $classe</p>\n";
		}
		else{
			unset($tab_periode);
			$tab_periode=array();
			//$cpt=0;
			$cpt=1;
			while($lig_periode=mysql_fetch_object($res_periode)){
				$tab_periode[$cpt]=$lig_periode->nom_periode;
				$cpt++;
			}



			$sql="SELECT DISTINCT e.* FROM eleves e,j_eleves_classes jec WHERE id_classe='".$id_classe[0]."' AND jec.login=e.login ORDER BY login";
			//echo "$sql<br />\n";
			$res_ele=mysql_query($sql);

			if(mysql_num_rows($res_ele)==0){
				echo "<p>Aucun �l�ve dans la classe $classe???</p>\n";
			}
			else{
				unset($tab_eleve);
				$tab_eleve=array();
				$cpt=0;
				while($lig_ele=mysql_fetch_object($res_ele)){
					// Infos �l�ve
					$tab_eleve[$cpt]=array();

					$tab_eleve[$cpt]['nom']=$lig_ele->nom;
					$tab_eleve[$cpt]['prenom']=$lig_ele->prenom;
					$tab_eleve[$cpt]['naissance']=$lig_ele->naissance;
					$tab_eleve[$cpt]['naissance2']=formate_date($lig_ele->naissance);
					$tab_eleve[$cpt]['login_eleve']=$lig_ele->login;

					$tab_eleve[$cpt]['ine']=$lig_ele->no_gep;

					if($tab_eleve[$cpt]['ine']==""){
						$tab_eleve[$cpt]['ine']="LOGIN_".$tab_eleve[$cpt]['login_eleve'];
						$tab_eleve[$cpt]['ine'] = cree_substitut_INE_unique($tab_eleve[$cpt]['ine']);
					}
          // On v�rifie que l'�l�ve est enregistr� dans archive_eleves. Sinon, on l'enregistre

          $error_enregistre_eleve[$tab_eleve[$cpt]['login_eleve']] = insert_eleve($tab_eleve[$cpt]['login_eleve'],$tab_eleve[$cpt]['ine'],$annee_scolaire,'y');

					// Statut de redoublant ou non:
					$sql="SELECT * FROM j_eleves_regime WHERE login='".$tab_eleve[$cpt]['login_eleve']."'";
					$res_red=mysql_query($sql);

					if(mysql_num_rows($res_red)==0){
						$tab_eleve[$cpt]['doublant']="-";
					}
					else{
						$lig_red=mysql_fetch_object($res_red);
						$tab_eleve[$cpt]['doublant']=$lig_red->doublant;
					}


					// CPE associ�(s) � l'�l�ve
					$sql="SELECT jec.cpe_login FROM j_eleves_cpe jec WHERE jec.e_login='".$tab_eleve[$cpt]['login_eleve']."';";
					$res_cpe=mysql_query($sql);

					if(mysql_num_rows($res_cpe)==0){
						$tab_eleve[$cpt]['cpe']="";
					}
					else{
						$lig_cpe=mysql_fetch_object($res_cpe);
						$tab_eleve[$cpt]['cpe']=affiche_utilisateur($lig_cpe->cpe_login,$id_classe[0]);

						while($lig_cpe=mysql_fetch_object($res_cpe)){
							$tab_eleve[$cpt]['cpe'].=", ".affiche_utilisateur($lig_cpe->cpe_login,$id_classe[0]);
						}
					}



					$cpt++;
				}



				// Personne assurant le suivi de la classe...
				$sql="SELECT suivi_par FROM classes WHERE id='$id_classe[0]'";
				$res_suivi=mysql_query($sql);
				if(mysql_num_rows($res_suivi)==0){
					$suivi_par="-";
				}
				else{
					$lig_suivi=mysql_fetch_object($res_suivi);
					$suivi_par=$lig_suivi->suivi_par;
				}



				echo "<table class='boireaus' border='1' summary='Tableau des �l�ves'>\n";

				// Boucle sur les p�riodes
				echo "<tr>\n";
				echo "<th>�l�ve</th>\n";
				//for($i=0;$i<count($tab_periode);$i++){
				for($i=1;$i<=count($tab_periode);$i++){
					echo "<th>$tab_periode[$i]</th>\n";
				}
				echo "</tr>\n";

				// Boucle sur les �l�ves
				$alt=1;
				for($j=0;$j<count($tab_eleve);$j++){
					$alt=$alt*(-1);
					echo "<tr class='lig$alt'>\n";
					echo "<td id='td_0_".$j."'>".$tab_eleve[$j]['nom']." ".$tab_eleve[$j]['prenom']."</td>\n";
					//for($i=0;$i<count($tab_periode);$i++){
					for($i=1;$i<=count($tab_periode);$i++){
						echo "<td id='td_".$i."_".$j."'>&nbsp;</td>\n";
					}
					echo "</tr>\n";
				}

				echo "</table>\n";


				// D�but du traitement

				for($i=1;$i<=count($tab_periode);$i++){
					// Nettoyage:
					$sql="DELETE FROM archivage_disciplines WHERE annee='$annee_scolaire' AND classe='$classe' AND num_periode='$i'";
					$res_nettoyage=mysql_query($sql);

					if(!$res_nettoyage){
						echo "<p style='color:red'><b>ERREUR</b> lors du nettoyage</p>\n";
						echo "</form>\n";
						require("../lib/footer.inc.php");
						die();
					}

					$erreur=0;

					$num_periode=$i;
					$nom_periode=$tab_periode[$i];


					// Calculer les moyennes de classe, rechercher min et max pour tous les groupes associ�s � la classe sur la p�riode.
					$sql="SELECT DISTINCT id_groupe FROM j_groupes_classes WHERE id_classe='".$id_classe[0]."'";
					$res_groupes=mysql_query($sql);

					$moymin=array();
					$moymax=array();
					$moyclasse=array();
					if(mysql_num_rows($res_groupes)==0){
						// Dans ce cas, il ne doit pas y avoir de note,... pour les �l�ves
					}
					else{
						while($lig_groupes=mysql_fetch_object($res_groupes)){
							$id_groupe=$lig_groupes->id_groupe;

							$sql="SELECT AVG(note) moyenne FROM matieres_notes WHERE id_groupe='$id_groupe' AND statut='' AND periode='$i'";
							//echo "$sql<br />\n";
							$res_moy=mysql_query($sql);
							if(mysql_num_rows($res_moy)==0){
								$moyclasse[$id_groupe]="-";
							}
							else{
								$lig_moy=mysql_fetch_object($res_moy);
								$moyclasse[$id_groupe]=round($lig_moy->moyenne*10)/10;
							}

							$sql="SELECT MAX(note) moyenne FROM matieres_notes WHERE id_groupe='$id_groupe' AND statut='' AND periode='$i'";
							$res_moy=mysql_query($sql);
							if(mysql_num_rows($res_moy)==0){
								$moymax[$id_groupe]="-";
							}
							else{
								$lig_moy=mysql_fetch_object($res_moy);
								$moymax[$id_groupe]=$lig_moy->moyenne;
							}

							$sql="SELECT MIN(note) moyenne FROM matieres_notes WHERE id_groupe='$id_groupe' AND statut='' AND periode='$i'";
							$res_moy=mysql_query($sql);
							if(mysql_num_rows($res_moy)==0){
								$moymin[$id_groupe]="-";
							}
							else{
								$lig_moy=mysql_fetch_object($res_moy);
								$moymin[$id_groupe]=$lig_moy->moyenne;
							}
						}
					}


					// Boucle sur les �l�ves
					for($j=0;$j<count($tab_eleve);$j++){
						$ine=$tab_eleve[$j]['ine'];
						$nom=$tab_eleve[$j]['nom'];
						$prenom=$tab_eleve[$j]['prenom'];
						$naissance=$tab_eleve[$j]['naissance'];
						$naissance2=$tab_eleve[$j]['naissance2'];
						$login_eleve=$tab_eleve[$j]['login_eleve'];
						$doublant=$tab_eleve[$j]['doublant'];
						$cpe=$tab_eleve[$j]['cpe'];
						if ($error_enregistre_eleve[$login_eleve] != '')
            echo "<script type='text/javascript'>
  	document.getElementById('td_0_".$j."').style.backgroundColor='red';
</script>\n";





						// Absences, retards,... de l'�l�ve
						$sql="SELECT * FROM absences WHERE login='".$login_eleve."' AND periode='$i'";
						$res_abs=mysql_query($sql);

						if(mysql_num_rows($res_abs)==0){
							$nb_absences="-";
							$non_justifie="-";
							$nb_retards="-";
							$appreciation="-";
						}
						else{
							$lig_abs=mysql_fetch_object($res_abs);
							$nb_absences=$lig_abs->nb_absences;
							$non_justifie=$lig_abs->non_justifie;
							$nb_retards=$lig_abs->nb_retards;
							$appreciation=$lig_abs->appreciation;
						}

						$sql="INSERT INTO archivage_disciplines SET
											annee='$annee_scolaire',
											ine='$ine',
											classe='".addslashes($classe)."',
											num_periode='$num_periode',
											nom_periode='".addslashes($nom_periode)."',
											special='ABSENCES',
											matiere='',
											prof='".addslashes($cpe)."',
											note='',
											moymin='',
											moymax='',
											moyclasse='',
											appreciation='".addslashes($appreciation)."',
											nb_absences='$nb_absences',
											non_justifie='$non_justifie',
											nb_retards='$nb_retards'
											";
						//echo "$sql<br />";
						echo "<!-- $sql -->\n";
						$res_insert=mysql_query($sql);

						if(!$res_insert){
							$erreur++;

							echo "<script type='text/javascript'>
	document.getElementById('td_".$i."_".$j."').style.backgroundColor='red';
</script>\n";
						}




						// Avis du conseil de classe
						$sql="SELECT * FROM avis_conseil_classe WHERE login='$login_eleve' AND periode='$num_periode'";
						$res_avis=mysql_query($sql);

						if(mysql_num_rows($res_avis)==0){
							$avis="-";
						}
						else{
							$lig_avis=mysql_fetch_object($res_avis);
							$avis=$lig_avis->avis;
							// A quoi sert le champ statut de la table avis_conseil_classe ?
						}

						// Insertion de l'avis dans archivage_disciplines
						$sql="INSERT INTO archivage_disciplines SET
											annee='$annee_scolaire',
											ine='$ine',
											classe='".addslashes($classe)."',
											num_periode='$num_periode',
											nom_periode='".addslashes($nom_periode)."',
											special='AVIS_CONSEIL',
											matiere='',
											prof='".addslashes($suivi_par)."',
											note='',
											moymin='',
											moymax='',
											moyclasse='',
											appreciation='".addslashes($avis)."',
											nb_absences='',
											non_justifie='',
											nb_retards=''
											";
						echo "<!-- $sql -->\n";
						$res_insert=mysql_query($sql);

						if(!$res_insert){
							$erreur++;

							echo "<script type='text/javascript'>
	document.getElementById('td_".$i."_".$j."').style.backgroundColor='red';
</script>\n";
						}




						// Boucle sur les mati�res de l'�l�ve
						/*
						$sql="SELECT mn.*,g.description FROM groupes g,matieres_notes mn
														WHERE login='$login_eleve' AND
																periode='$num_periode'";
						*/
						$sql="SELECT mn.*,m.nom_complet FROM j_groupes_matieres jgm,matieres m,matieres_notes mn
														WHERE mn.login='$login_eleve' AND
																mn.periode='$num_periode' AND
																jgm.id_groupe=mn.id_groupe AND
																jgm.id_matiere=m.matiere";
						$res_grp=mysql_query($sql);

						if(mysql_num_rows($res_grp)==0){
							// Que faire? Est-il possible qu'il y ait quelque chose dans matieres_appreciations dans ce cas?
							// Ca ne devrait pas...
						}
						else{
							while($lig_grp=mysql_fetch_object($res_grp)){

								$id_groupe=$lig_grp->id_groupe;
								$matiere=$lig_grp->nom_complet;
								if($lig_grp->statut!=''){
									$note=$lig_grp->statut;
								}
								else{
									$note=$lig_grp->note;
								}
								$rang=$lig_grp->rang;


								// R�cup�ration de l'appr�ciation
								$sql="SELECT appreciation FROM matieres_appreciations
														WHERE login='$login_eleve' AND
																periode='$num_periode' AND
																id_groupe='$id_groupe'";
								$res_app=mysql_query($sql);

								if(mysql_num_rows($res_app)==0){
									$appreciation="-";
								}
								else{
									$lig_app=mysql_fetch_object($res_app);
									$appreciation=$lig_app->appreciation;
								}

								// R�cup�ration des professeurs associ�s
								$sql="SELECT login FROM j_groupes_professeurs WHERE id_groupe='$id_groupe' ORDER BY login";
								$res_prof=mysql_query($sql);

								if(mysql_num_rows($res_prof)==0){
									$prof="";
								}
								else{
									$lig_prof=mysql_fetch_object($res_prof);
									$prof=affiche_utilisateur($lig_prof->login,$id_classe[0]);
									while($lig_prof=mysql_fetch_object($res_prof)){
										$prof.=", ".affiche_utilisateur($lig_prof->login,$id_classe[0]);
									}
								}

								// Insertion de la note, l'appr�ciation,... dans la mati�re,...
								if (!isset($moymin[$id_groupe])) $moymin[$id_groupe]="-";
								if (!isset($moymax[$id_groupe])) $moymax[$id_groupe]="-";
								if (!isset($moyclasse[$id_groupe])) $moyclasse[$id_groupe]="-";

								$sql="INSERT INTO archivage_disciplines SET
													annee='$annee_scolaire',
													ine='$ine',
													classe='".addslashes($classe)."',
													num_periode='$num_periode',
													nom_periode='".addslashes($nom_periode)."',
													matiere='".addslashes($matiere)."',
													special='',
													prof='".addslashes($prof)."',
													note='$note',
													moymin='".$moymin[$id_groupe]."',
													moymax='".$moymax[$id_groupe]."',
													moyclasse='".$moyclasse[$id_groupe]."',
													rang='".$rang."',
													appreciation='".addslashes($appreciation)."',
													nb_absences='',
													non_justifie='',
													nb_retards=''
													";
								echo "<!-- $sql -->\n";
								$res_insert=mysql_query($sql);

								if(!$res_insert){
									$erreur++;

									echo "<script type='text/javascript'>
	document.getElementById('td_".$i."_".$j."').style.backgroundColor='red';
</script>\n";
								}


							} // Fin de la boucle mati�res



                                                        //--------------------
                                                        // Les cr�dits ECTS
                                                        //--------------------

                                                        // On a besoin de : annee, ine, classe, num_periode, nom_periode, matiere, prof, valeur_ects, mention_ects
                                                        // On a d�j� pratiquement tout... �a ne va pas �tre compliqu� !
                                                        $Eleve = ElevePeer::retrieveByLOGIN($login_eleve);
                                                        $Groupes = $Eleve->getGroupes($num_periode);

                                                        foreach($Groupes as $Groupe) {
                                                            
                                                            $Ects = $Eleve->getEctsCredit($num_periode,$Groupe->getId());

                                                            if ($Ects != null) {
                                                                $Archive = new ArchiveEcts();
                                                                $Archive->setAnnee($annee_scolaire);
                                                                $Archive->setIne($ine);
                                                                $Archive->setClasse($classe);
                                                                $Archive->setNumPeriode($num_periode);
                                                                $Archive->setNomPeriode($nom_periode);
                                                                $Archive->setMatiere($Groupe->getDescription());
                                                                $Archive->setSpecial('');
                                                                $Archive->setProfs($prof);
                                                                $Archive->setValeur($Ects->getValeur());
                                                                $Archive->setMention($Ects->getMention());
                                                                $Archive->save();
                                                            }
                                                        }




							if($erreur==0){
								echo "<script type='text/javascript'>
	document.getElementById('td_".$i."_".$j."').style.backgroundColor='green';
</script>\n";
							}
							flush();
						}

					}

				}

			}



//==================================================
//**************************************************
//==================================================

			/*
			while($lig_periode=mysql_fetch_object($res_periode)){

				$num_periode=$lig_periode->num_periode;
				$nom_periode=$lig_periode->nom_periode;

				// Nettoyage:
				$sql="DELETE FROM archivage_disciplines WHERE annee='$annee_scolaire' AND classe='$classe' AND num_periode='$num_periode'";
				$res_nettoyage=mysql_query($sql);

				if(!$res_nettoyage){
					echo "<p style='color:red'><b>ERREUR</b> lors du nettoyage</p>\n";
					echo "</form>\n";
					require("../lib/footer.inc.php");
					die();
				}

				echo "<p>P�riode: $nom_periode</p>\n";

				// Calculer les moyennes de classe, rechercher min et max pour tous les groupes associ�s � la classe sur la p�riode.
				$sql="SELECT DISTINCT id_groupe FROM j_groupes_classes WHERE id_classe='".$id_classe[0]."'";
				$res_groupes=mysql_query($sql);

				$moymin=array();
				$moymax=array();
				$moyclasse=array();
				if(mysql_num_rows($res_groupes)==0){
					// Dans ce cas, il ne doit pas y avoir de note,... pour les �l�ves
				}
				else{
					while($lig_groupes=mysql_fetch_object($res_groupes)){
						$id_groupe=$lig_groupes->id_groupe;

						$sql="SELECT AVG(note) moyenne FROM matieres_notes WHERE id_groupe='$id_groupe' AND statut=''";
						//echo "$sql<br />\n";
						$res_moy=mysql_query($sql);
						if(mysql_num_rows($res_moy)==0){
							$moyclasse[$id_groupe]="-";
						}
						else{
							$lig_moy=mysql_fetch_object($res_moy);
							$moyclasse[$id_groupe]=round($lig_moy->moyenne*10)/10;
						}

						$sql="SELECT MAX(note) moyenne FROM matieres_notes WHERE id_groupe='$id_groupe' AND statut=''";
						$res_moy=mysql_query($sql);
						if(mysql_num_rows($res_moy)==0){
							$moymax[$id_groupe]="-";
						}
						else{
							$lig_moy=mysql_fetch_object($res_moy);
							$moymax[$id_groupe]=$lig_moy->moyenne;
						}

						$sql="SELECT MIN(note) moyenne FROM matieres_notes WHERE id_groupe='$id_groupe' AND statut=''";
						$res_moy=mysql_query($sql);
						if(mysql_num_rows($res_moy)==0){
							$moymin[$id_groupe]="-";
						}
						else{
							$lig_moy=mysql_fetch_object($res_moy);
							$moymin[$id_groupe]=$lig_moy->moyenne;
						}
					}
				}




				// Boucle sur les �l�ves de la classe pour la p�riode
				$sql="SELECT e.* FROM eleves e,j_eleves_classes jec WHERE id_classe='".$id_classe[0]."' AND periode='$num_periode' AND jec.login=e.login ORDER BY login";
				//echo "$sql<br />\n";
				$res_ele=mysql_query($sql);

				if(mysql_num_rows($res_ele)==0){
					echo "<p>Aucun �l�ve dans la classe $classe pour la p�riode '$nom_periode'.</p>\n";
				}
				else{
					echo "<table border='1'>\n";
					while($lig_ele=mysql_fetch_object($res_ele)){
						// Infos �l�ve
						$ine=$lig_ele->no_gep;
						$nom=$lig_ele->nom;
						$prenom=$lig_ele->prenom;
						$naissance=$lig_ele->naissance;
						$naissance2=formate_date($lig_ele->naissance);
						$login_eleve=$lig_ele->login;

						echo "<tr>\n";
						echo "<td>$nom</td>\n";
						echo "<td>$prenom</td>\n";

						if($ine==""){
							$ine="LOGIN_".$login_eleve;
						}

						// Statut de redoublant ou non:
						$sql="SELECT * FROM j_eleves_regime WHERE login='$login_eleve'";
						$res_red=mysql_query($sql);

						if(mysql_num_rows($res_red)==0){
							$doublant="-";
						}
						else{
							$lig_red=mysql_fetch_object($res_red);
							$doublant=$lig_red->doublant;
						}
						//echo "doublant=$doublant<br />\n";

						// CPE associ� � l'�l�ve
						//$sql="SELECT u.nom,u.prenom FROM j_eleves_cpe jec, utilisateurs u WHERE jec.cpe_login=u.login AND jec.e_login='$login_eleve'";
						$sql="SELECT jec.cpe_login FROM j_eleves_cpe jec WHERE jec.e_login='$login_eleve'";
						$res_cpe=mysql_query($sql);

						if(mysql_num_rows($res_cpe)==0){
							$cpe="";
						}
						else{
							$lig_cpe=mysql_fetch_object($res_cpe);
							$cpe=affiche_utilisateur($lig_cpe->cpe_login,$id_classe[0]);

							while($lig_cpe=mysql_fetch_object($res_cpe)){
								$cpe.=", ".affiche_utilisateur($lig_cpe->cpe_login,$id_classe[0]);
							}
						}

						// Absences, retards,... de l'�l�ve
						$sql="SELECT * FROM absences WHERE login='$login_eleve' AND periode='$num_periode'";
						$res_abs=mysql_query($sql);

						if(mysql_num_rows($res_abs)==0){
							$nb_absences="-";
							$non_justifie="-";
							$nb_retards="-";
							$appreciation="-";
						}
						else{
							$lig_abs=mysql_fetch_object($res_abs);
							$nb_absences=$lig_abs->nb_absences;
							$non_justifie=$lig_abs->non_justifie;
							$nb_retards=$lig_abs->nb_retards;
							$appreciation=$lig_abs->appreciation;
						}

						// Insertion de l'absence dans archivage_disciplines
						$sql="SELECT 1=1 FROM archivage_disciplines WHERE annee='$annee_scolaire' AND
																		ine='$ine' AND
																		num_periode='$num_periode' AND
																		special='ABSENCES'";
						$res_test=mysql_query($sql);


						echo "<td>\n";
							echo "<table border='1'>\n";
							echo "<tr>\n";
								echo "<td rowspan='3'>$cpe</td>\n";
								echo "<td>nb_absences</td>\n";
								echo "<td>$nb_absences</td>\n";
							echo "</tr>\n";

							echo "<tr>\n";
								echo "<td>non_justifie</td>\n";
								echo "<td>$non_justifie</td>\n";
							echo "</tr>\n";

							echo "<tr>\n";
								echo "<td>nb_retards</td>\n";
								echo "<td>$nb_retards</td>\n";
							echo "</tr>\n";
							echo "</table>\n";
						echo "</td>\n";



						if(mysql_num_rows($res_test)==0){
							$sql="INSERT INTO archivage_disciplines SET
												annee='$annee_scolaire',
												ine='$ine',
												classe='".addslashes($classe)."',
												num_periode='$num_periode',
												nom_periode='".addslashes($nom_periode)."',
												special='ABSENCES',
												matiere='',
												prof='".addslashes($cpe)."',
												note='',
												moymin='',
												moymax='',
												moyclasse='',
												appreciation='".addslashes($appreciation)."',
												nb_absences='$nb_absences',
												non_justifie='$non_justifie',
												nb_retards='$nb_retards'
												";
							//echo "$sql<br />";
							echo "<!-- $sql -->\n";
							$res_insert=mysql_query($sql);

							// AJOUTER UN TRAITEMENT D'ERREUR...

						}
						else{
							$sql="UPDATE archivage_disciplines SET
												classe='".addslashes($classe)."',
												nom_periode='".addslashes($nom_periode)."',
												matiere='',
												prof='".addslashes($cpe)."',
												note='',
												moymin='',
												moymax='',
												moyclasse='',
												appreciation='".addslashes($appreciation)."',
												nb_absences='$nb_absences',
												non_justifie='$non_justifie',
												nb_retards='$nb_retards'
											WHERE
												annee='$annee_scolaire',
												ine='$ine',
												num_periode='$num_periode',
												special='ABSENCES'
												";
							echo "<!-- $sql -->\n";
							$res_update=mysql_query($sql);

							// AJOUTER UN TRAITEMENT D'ERREUR...

						}




						// Personne assurant le suivi de la classe...
						$sql="SELECT suivi_par FROM classes WHERE id='$id_classe[0]'";
						$res_suivi=mysql_query($sql);
						if(mysql_num_rows($res_suivi)==0){
							$suivi_par="-";
						}
						else{
							$lig_suivi=mysql_fetch_object($res_suivi);
							$suivi_par=$lig_suivi->suivi_par;
						}


						// Avis du conseil de classe
						$sql="SELECT * FROM avis_conseil_classe WHERE login='$login_eleve' AND periode='$num_periode'";
						$res_avis=mysql_query($sql);

						if(mysql_num_rows($res_avis)==0){
							$avis="-";
						}
						else{
							$lig_avis=mysql_fetch_object($res_avis);
							$avis=$lig_avis->avis;
							// A quoi sert le champ statut de la table avis_conseil_classe ?
						}




						echo "<td>\n";
							echo "<table border='1'>\n";
							echo "<tr>\n";
								echo "<td>$suivi_par</td>\n";
							echo "</tr>\n";
							echo "<tr>\n";
								echo "<td>Avis</td>\n";
							echo "</tr>\n";
							echo "<tr>\n";
								echo "<td>$avis</td>\n";
							echo "</tr>\n";
							echo "</table>\n";
						echo "</td>\n";



						// Insertion de l'avis dans archivage_disciplines
						$sql="SELECT 1=1 FROM archivage_disciplines WHERE annee='$annee_scolaire' AND
																		ine='$ine' AND
																		num_periode='$num_periode' AND
																		special='AVIS_CONSEIL'";
						$res_test=mysql_query($sql);

						if(mysql_num_rows($res_test)==0){
							$sql="INSERT INTO archivage_disciplines SET
												annee='$annee_scolaire',
												ine='$ine',
												classe='".addslashes($classe)."',
												num_periode='$num_periode',
												nom_periode='".addslashes($nom_periode)."',
												special='AVIS_CONSEIL',
												matiere='',
												prof='".addslashes($suivi_par)."',
												note='',
												moymin='',
												moymax='',
												moyclasse='',
												appreciation='".addslashes($avis)."',
												nb_absences='',
												non_justifie='',
												nb_retards=''
												";
							echo "<!-- $sql -->\n";
							$res_insert=mysql_query($sql);

							// AJOUTER UN TRAITEMENT D'ERREUR...

						}
						else{
							$sql="UPDATE archivage_disciplines SET
												classe='".addslashes($classe)."',
												nom_periode='".addslashes($nom_periode)."',
												matiere='',
												prof='".addslashes($suivi_par)."',
												note='',
												moymin='',
												moymax='',
												moyclasse='',
												appreciation='".addslashes($avis)."',
												nb_absences='',
												non_justifie='',
												nb_retards=''
											WHERE
												annee='$annee_scolaire',
												ine='$ine',
												num_periode='$num_periode',
												special='AVIS_CONSEIL'
												";
							echo "<!-- $sql -->\n";
							$res_update=mysql_query($sql);

							// AJOUTER UN TRAITEMENT D'ERREUR...

						}



						// Boucle sur les mati�res de l'�l�ve

						//$sql="SELECT mn.*,g.description FROM groupes g,matieres_notes mn
						//								WHERE login='$login_eleve' AND
						//										periode='$num_periode'";

						$sql="SELECT mn.*,m.nom_complet FROM j_groupes_matieres jgm,matieres m,matieres_notes mn
														WHERE mn.login='$login_eleve' AND
																mn.periode='$num_periode' AND
																jgm.id_groupe=mn.id_groupe AND
																jgm.id_matiere=m.matiere";
						$res_grp=mysql_query($sql);

						if(mysql_num_rows($res_grp)==0){
							// Que faire? Est-il possible qu'il y ait quelque chose dans matieres_appreciations dans ce cas?
							// Ca ne devrait pas...
						}
						else{

							echo "<td>\n";
								echo "<table border='1'>\n";


							while($lig_grp=mysql_fetch_object($res_grp)){

								$id_groupe=$lig_grp->id_groupe;
								$matiere=$lig_grp->nom_complet;
								if($lig_grp->statut!=''){
									$note=$lig_grp->statut;
								}
								else{
									$note=$lig_grp->note;
								}
								$rang=$lig_grp->rang;


								// R�cup�ration de l'appr�ciation
								$sql="SELECT appreciation FROM matieres_appreciations
														WHERE login='$login_eleve' AND
																periode='$num_periode' AND
																id_groupe='$id_groupe'";
								$res_app=mysql_query($sql);

								if(mysql_num_rows($res_app)==0){
									$appreciation="-";
								}
								else{
									$lig_app=mysql_fetch_object($res_app);
									$appreciation=$lig_app->appreciation;
								}

								// R�cup�ration des professeurs associ�s
								$sql="SELECT login FROM j_groupes_professeurs WHERE id_groupe='$id_groupe' ORDER BY login";
								$res_prof=mysql_query($sql);

								if(mysql_num_rows($res_prof)==0){
									$prof="";
								}
								else{
									$lig_prof=mysql_fetch_object($res_prof);
									$prof=affiche_utilisateur($lig_prof->login,$id_classe[0]);
									while($lig_prof=mysql_fetch_object($res_prof)){
										$prof.=", ".affiche_utilisateur($lig_prof->login,$id_classe[0]);
									}
								}



								echo "<tr>\n";
									echo "<td>$matiere</td>\n";
									echo "<td>$prof</td>\n";
									echo "<td>$note</td>\n";
									echo "<td>$rang</td>\n";
									echo "<td>$appreciation</td>\n";
								echo "</tr>\n";


								// Insertion de la note, l'appr�ciation,... dans la mati�re,...
								$sql="SELECT 1=1 FROM archivage_disciplines WHERE annee='$annee_scolaire' AND
																				ine='$ine' AND
																				num_periode='$num_periode' AND
																				matiere='$matiere'";
								$res_test=mysql_query($sql);

								if(mysql_num_rows($res_test)==0){
									$sql="INSERT INTO archivage_disciplines SET
														annee='$annee_scolaire',
														ine='$ine',
														classe='".addslashes($classe)."',
														num_periode='$num_periode',
														nom_periode='".addslashes($nom_periode)."',
														matiere='".addslashes($matiere)."',
														special='',
														prof='".addslashes($prof)."',
														note='$note',
														moymin='".$moymin[$id_groupe]."',
														moymax='".$moymax[$id_groupe]."',
														moyclasse='".$moyclasse[$id_groupe]."',
														rang='".$rang."',
														appreciation='".addslashes($appreciation)."',
														nb_absences='',
														non_justifie='',
														nb_retards=''
														";
									echo "<!-- $sql -->\n";
									$res_insert=mysql_query($sql);

									// AJOUTER UN TRAITEMENT D'ERREUR...

								}
								else{
									$sql="UPDATE archivage_disciplines SET
														classe='".addslashes($classe)."',
														nom_periode='".addslashes($nom_periode)."',
														special='',
														prof='".addslashes($prof)."',
														note='$note',
														moymin='".$moymin[$id_groupe]."',
														moymax='".$moymax[$id_groupe]."',
														moyclasse='".$moyclasse[$id_groupe]."',
														rang='".$rang."',
														appreciation='".addslashes($appreciation)."',
														nb_absences='',
														non_justifie='',
														nb_retards=''
													WHERE
														annee='$annee_scolaire',
														ine='$ine',
														num_periode='$num_periode',
														matiere='".addslashes($matiere)."'
														";
									echo "<!-- $sql -->\n";
									$res_update=mysql_query($sql);

									// AJOUTER UN TRAITEMENT D'ERREUR...

								}


							}


								echo "</table>\n";
							echo "</td>\n";

						}
						echo "</tr>\n";
					}
					echo "</table>\n";
				}
			}
			*/

		}


		//===================================

		echo "<input type='hidden' name='deja_traitee_id_classe[]' value='$id_classe[0]' />\n";

		/*
		$temoin_encore_des_classes=0;
		$chaine="";
		for($i=1;$i<count($id_classe);$i++){
			echo "<input type='hidden' name='id_classe[]' value='$id_classe[$i]' />\n";
			$temoin_encore_des_classes++;

			$chaine.=", ".get_nom_classe($id_classe[$i]);
		}
		if($chaine!=""){
			echo "<p>Classes restant � traiter: ".substr($chaine,2)."</p>\n";
		}
		*/

		if($temoin_encore_des_classes>0){
			echo "<script type='text/javascript'>
	setTimeout('document.formulaire.submit();', 5000);
</script>\n";
			echo "<center><input type=\"submit\" name='ok' value=\"Valider\" style=\"font-variant: small-caps;\" /></center>\n";
		}
		else{
			echo "<p style='text-align:center; font-weight:bold; font-size:2em; color: green;'>Traitement termin�.</p>\n";
			echo "<script type='text/javascript'>
	document.getElementById('annonce_fin_traitement').innerHTML='Traitement termin�.';
</script>\n";

		}

		echo "<input type='hidden' name='annee_scolaire' value='$annee_scolaire' />\n";
		echo "<input type='hidden' name='confirmer' value='ok' />\n";
		//echo "<center><input type=\"submit\" name='ok' value=\"Valider\" style=\"font-variant: small-caps;\" /></center>\n";

	//===================================


	}
}

//echo "<center><input type=\"submit\" name='ok' value=\"Valider\" style=\"font-variant: small-caps;\" /></center>\n";

echo "</form>\n";
echo "<br />\n";
require("../lib/footer.inc.php");
?>