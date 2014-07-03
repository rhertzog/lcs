<?php
@set_time_limit(0);
/*
 * $Id$
 *
 * Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
extract($_POST, EXTR_OVERWRITE);

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
	header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
	header("Location: ../logout.php?auto=1");
	die();
}

//INSERT INTO `droits` VALUES ('/utilitaires/verif_groupes.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Vérification des incohérences d appartenances à des groupes', '');
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

function affiche_debug($texte,$e_login){
	$debug=0;
	if($debug==1){
		//if($e_login=="BUNEL_M"){
		if(($e_login=="BUNEL_M")||($e_login=="BALESTA_M")){
			echo $texte."<br />\n";
		}
	}
}

//**************** EN-TETE *****************
$titre_page = "Vérification des affectations dans les groupes";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

echo "<p class=bold><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil</a> | <a href='clean_tables.php'>Retour page précédente</a></p>";

$verif=isset($_POST['verif']) ? $_POST['verif'] : (isset($_GET['verif']) ? $_GET['verif'] : NULL);

//if(!isset($_GET['verif'])){
if(!isset($verif)) {
	echo "<h2>Vérification des groupes</h2>\n";
	echo "<p>Cette page est destinée à repérer la cause d'éventuelles erreurs du type:</p>\n";
	echo "<pre style='color:green;'>Warning: old_mysql_result(): Unable to jump to row 0
on MySQL result index 468 in /var/wwws/gepi/lib/groupes.inc.php on line 143</pre>\n";
	echo "<p>Pour procéder à la vérification, cliquez sur ce lien: <a href='".$_SERVER['PHP_SELF']."?verif=oui".add_token_in_url()."'>Vérification</a><br />(<i>l'opération peut être très longue</i>)</p>\n";
}
else {
	check_token(false);

	$ini=isset($_POST['ini']) ? $_POST['ini'] : NULL;


	echo "<h2>Recherche des inscriptions erronées d'élèves</h2>\n";
	flush();
	$err_no=0;

	// Liste des numéros de périodes
	$sql="SELECT DISTINCT num_periode FROM periodes ORDER BY num_periode;";
	$res_per=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_per)==0) {
		echo "<p>Aucune période n'est encore définie.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}
	else {
		unset($tab_per);
		while($lig=mysqli_fetch_object($res_per)) {
			$tab_per[]=$lig->num_periode;
		}
	}

	if(!isset($_POST['c_est_parti'])) {
		$sql="TRUNCATE tempo2;";
		$nettoyage=mysqli_query($GLOBALS["mysqli"], $sql);

		$sql="SELECT DISTINCT login FROM j_eleves_groupes ORDER BY login;";
		$res_ele=mysqli_query($GLOBALS["mysqli"], $sql);

		if(mysqli_num_rows($res_ele)==0) {
			echo "<p>Aucun élève n'est encore inscrit dans un groupe.</p>\n";
			require("../lib/footer.inc.php");
			die();
		}

		while($lig=mysqli_fetch_object($res_ele)) {
			$sql="INSERT INTO tempo2 SET col1='$lig->login', col2='verif_grp';";
			$res_tempo2=mysqli_query($GLOBALS["mysqli"], $sql);
		}


		$sql="CREATE TABLE IF NOT EXISTS tempo3 (
		id int(11) NOT NULL auto_increment,
		col1 VARCHAR(255) NOT NULL,
		col2 TEXT,
		PRIMARY KEY  (id)
		) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
		$create_table=mysqli_query($GLOBALS["mysqli"], $sql);

		$sql="TRUNCATE tempo3;";
		$nettoyage=mysqli_query($GLOBALS["mysqli"], $sql);

		$ini="";
	}

	/*
	// On commence par ne récupérer que les login/periode pour ne pas risquer d'oublier d'élèves
	// (il peut y avoir des incohérences non détectées si on essaye de récupérer davantage d'infos dans un premier temps)
	$sql="SELECT DISTINCT login,periode FROM j_eleves_groupes ORDER BY login,periode";
	$res_ele=mysql_query($sql);
	*/

	$sql="SELECT * FROM tempo3 WHERE col1='rapport_verif_grp' ORDER BY id;";
	$res_rapport=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_rapport)>0) {
		while($lig_rapp=mysqli_fetch_object($res_rapport)){
			echo $lig_rapp->col2;
		}
	}

	$nb=20;
	$sql="SELECT col1 AS login FROM tempo2 WHERE col2='verif_grp' ORDER BY col1 LIMIT $nb";
	//echo "$sql<br />";
	$res_ele=mysqli_query($GLOBALS["mysqli"], $sql);

	//$ini="A";
	//$ini="";
	//echo "<i>Parcours des login commençant par la lettre $ini</i>";

	if(mysqli_num_rows($res_ele)>0) {
		$chaine_rapport="";
		while($lig_ele=mysqli_fetch_object($res_ele)){
			$temoin_erreur="n";

			if(mb_strtoupper(mb_substr($lig_ele->login,0,1))!=$ini){
				$ini=mb_strtoupper(mb_substr($lig_ele->login,0,1));
				//echo " - <i>$ini</i>";
				echo "<a name='suite'></a>\n";
				$info="<p>\n<i>Parcours des login commençant par la lettre $ini</i></p>\n";
				echo $info;
				$chaine_rapport.=$info;
			}

			for($loop=0;$loop<count($tab_per);$loop++) {
				$num_periode=$tab_per[$loop];

				// Récupération de la liste des groupes auxquels l'élève est inscrit sur la période en cours d'analyse:
				$sql="SELECT id_groupe FROM j_eleves_groupes WHERE login='$lig_ele->login' AND periode='$num_periode'";
				//echo "$sql<br />\n";
				affiche_debug($sql,$lig_ele->login);
				$res_jeg=mysqli_query($GLOBALS["mysqli"], $sql);

				//while($lig_jeg=mysql_fetch_object($res_jeg)){
				if(mysqli_num_rows($res_jeg)>0){
					// On vérifie si l'élève est dans une classe pour cette période:
					//$sql="SELECT 1=1 FROM j_eleves_classes WHERE login='$lig_ele->login' AND periode='$num_periode'";
					$sql="SELECT id_classe FROM j_eleves_classes WHERE login='$lig_ele->login' AND periode='$num_periode'";
					affiche_debug($sql,$lig_ele->login);
					$res_jec=mysqli_query($GLOBALS["mysqli"], $sql);

					if(mysqli_num_rows($res_jec)==0){
						$temoin_erreur="y";
						// L'élève n'est dans aucune classe sur la période choisie.
						$sql="SELECT c.* FROM classes c, j_eleves_classes jec WHERE jec.login='$lig_ele->login' AND periode='$num_periode' AND jec.id_classe=c.id";
						affiche_debug($sql,$lig_ele->login);
						$res_class_test=mysqli_query($GLOBALS["mysqli"], $sql);

						// Le test ci-dessous est forcément vrai si on est arrivé là!
						if(mysqli_num_rows($res_class_test)==0){
							$sql="SELECT DISTINCT c.id,c.classe FROM classes c, j_eleves_classes jec WHERE jec.login='$lig_ele->login' AND jec.id_classe=c.id";
							affiche_debug($sql,$lig_ele->login);
							$res_class=mysqli_query($GLOBALS["mysqli"], $sql);

							$chaine_msg="";
							$chaine_classes="";
							if(mysqli_num_rows($res_class)!=0){
								while($lig_class=mysqli_fetch_object($res_class)){
									$chaine_classes.=", $lig_class->classe";
									$chaine_msg.=",<br /><a href='../classes/eleve_options.php?login_eleve=".$lig_ele->login."&amp;id_classe=".$lig_class->id."' target='_blank'>Contrôler en $lig_class->classe</a>\n";
								}
								$chaine_msg=mb_substr($chaine_msg,7);
								$chaine_classes=mb_substr($chaine_classes,2);

								//echo "<br />\n";
								$info="<p>\n";
								$info.="<b>$lig_ele->login</b> de <b>$chaine_classes</b> est inscrit à des groupes pour la période <b>$num_periode</b>, mais n'est pas dans la classe pour cette période.<br />\n";
								echo $info;
								$chaine_rapport.=$info;

								echo $chaine_msg;
								$chaine_rapport.=$chaine_msg;


								// Contrôler à quelles classes les groupes sont liés.
								unset($tab_tmp_grp);
								$tab_tmp_grp=array();
								if(isset($tab_tmp_clas)){unset($tab_tmp_clas);}
								$tab_tmp_clas=array();
								while($lig_grp=mysqli_fetch_object($res_jeg)){
									$tab_tmp_grp[]=$lig_grp->id_groupe;
									$sql="SELECT DISTINCT c.id,c.classe FROM classes c,j_groupes_classes jgc WHERE jgc.id_classe=c.id AND jgc.id_groupe='$lig_grp->id_groupe'";
									$res_grp2=mysqli_query($GLOBALS["mysqli"], $sql);
									while($lig_tmp_clas=mysqli_fetch_object($res_grp2)){
										if(!in_array($lig_tmp_clas->classe,$tab_tmp_clas)){
											$tab_tmp_clas[]=$lig_tmp_clas->classe;
										}
									}
								}

								$info="<br />\n";
								$info.="Les groupes dont <b>$lig_ele->login</b> est membre sont liés ";
								echo $info;
								$chaine_rapport.=$info;

								if(count($tab_tmp_clas)>1){
									$info="aux classes suivantes: ";
								}
								else{
									$info="à la classe suivante: ";
								}
								echo $info;
								$chaine_rapport.=$info;

								$info=$tab_tmp_clas[0];
								echo $info;
								$chaine_rapport.=$info;

								for($i=1;$i<count($tab_tmp_clas);$i++){
									$info=", ".$tab_tmp_clas[$i];
									echo $info;
									$chaine_rapport.=$info;
								}
								$info="<br />\n";
								$info.="Si <b>$lig_ele->login</b> n'est pas dans une de ces classes, il faudrait l'affecter dans la classe sur une période au moins pour pouvoir supprimer son appartenance à ces groupes, ou procéder à un nettoyage des tables de la base GEPI.";
								$info.="</p>\n";
								echo $info;
								$chaine_rapport.=$info;
							}
							else{
								$info="<p>\n";
								$info.="<b>$lig_ele->login</b> est inscrit à des groupes pour la période <b>$num_periode</b>, mais n'est dans aucune classe.<br />\n";
								// ... dans aucune classe sur aucune période.
								$info.="Il va falloir l'affecter dans une classe pour pouvoir supprimer ses inscriptions à des groupes.<br />\n";
								$info.="</p>\n";
								echo $info;
								$chaine_rapport.=$info;
							}
						}
						$err_no++;


						// Est-ce qu'en plus l'élève aurait des notes ou moyennes saisies sur la période?
						//$sql="SELECT * FROM matieres_notes WHERE id_groupe='$tab_tmp_grp[$i]' AND periode='$num_periode' AND login='$lig_ele->login'"
						$sql="SELECT * FROM matieres_notes WHERE periode='$num_periode' AND login='$lig_ele->login'";
						$res_mat_not=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($res_mat_not)>0){
							$info="<b>$lig_ele->login</b> a de plus des moyennes saisies pour le bulletin sur la période <b>$num_periode</b>";
							echo $info;
							$chaine_rapport.=$info;
							/*
							echo " en "
							$lig_tmp=mysql_fetch_object($res_mat_not);
							$sql="SELECT description FROM groupes WHERE id='$lig_tmp->id_groupe'"
							*/
						}

					}
					else{
						if(mysqli_num_rows($res_jec)==1){
							$lig_clas=mysqli_fetch_object($res_jec);
							//$lig_grp=mysql_fetch_object($res_jeg);
							while($lig_grp=mysqli_fetch_object($res_jeg)){
								// On cherche si l'association groupe/classe existe:
								$sql="SELECT 1=1 FROM j_groupes_classes WHERE id_groupe='$lig_grp->id_groupe' AND id_classe='$lig_clas->id_classe'";
								affiche_debug($sql,$lig_ele->login);
								$res_test_grp_clas=mysqli_query($GLOBALS["mysqli"], $sql);

								if(mysqli_num_rows($res_test_grp_clas)==0){
									$temoin_erreur="y";
									$sql="SELECT classe FROM classes WHERE id='$lig_clas->id_classe'";
									$res_tmp=mysqli_query($GLOBALS["mysqli"], $sql);
									$lig_tmp=mysqli_fetch_object($res_tmp);
									$clas_tmp=$lig_tmp->classe;

									$sql="SELECT description FROM groupes WHERE id='$lig_grp->id_groupe'";
									$res_tmp=mysqli_query($GLOBALS["mysqli"], $sql);
									$lig_tmp=mysqli_fetch_object($res_tmp);
									$grp_tmp=$lig_tmp->description;

									$info="<p>\n";
									//echo "Il semble que $lig_ele->login de la classe $lig_clas->id_classe soit inscrit dans le groupe $lig_grp->id_groupe alors que ce groupe n'est pas associé à la classe dans 'j_groupes_classes'.<br />\n";
									$info.="<b>$lig_ele->login</b> est inscrit en période $num_periode dans le groupe <b>$grp_tmp</b> (<i>groupe n°$lig_grp->id_groupe</i>) alors que ce groupe n'est pas associé à la classe <b>$clas_tmp</b> dans 'j_groupes_classes'.<br />\n";
									echo $info;
									$chaine_rapport.=$info;

									// /groupes/edit_eleves.php?id_groupe=285&id_classe=8
									//$sql="SELECT id_classe FROM j_groupes_classes WHERE id_groupe='$lig_grp->id_groupe';";
									$sql="SELECT jgc.id_classe, c.classe FROM j_groupes_classes jgc, classes c WHERE jgc.id_groupe='$lig_grp->id_groupe' AND jgc.id_classe=c.id;";
									$res_tmp_clas=mysqli_query($GLOBALS["mysqli"], $sql);
									if(mysqli_num_rows($res_tmp_clas)>0){
										//$lig_tmp_clas=mysql_fetch_object($res_tmp_clas);
										//echo "Vous pouvez tenter de décocher l'élève de <b>$clas_tmp</b> du groupe <b>$grp_tmp</b> dans cette <a href='../groupes/edit_eleves.php?id_groupe=".$lig_grp->id_groupe."&id_classe=".$lig_tmp_clas->id_classe."' target='_blank'>page</a> si il s'y trouve.<br />\n";
										$info="Vous pouvez tenter de décocher l'élève de <b>$clas_tmp</b> du groupe <b>$grp_tmp</b> dans l'une des pages suivantes ";
										echo $info;
										$chaine_rapport.=$info;

										$tab_tmp_class=array();
										$tab_tmp_classe=array();
										while($lig_tmp_clas=mysqli_fetch_object($res_tmp_clas)){
											$tab_tmp_class[]=$lig_tmp_clas->id_classe;
											$tab_tmp_classe[]=$lig_tmp_clas->classe;
											$info="<a href='../groupes/edit_eleves.php?id_groupe=".$lig_grp->id_groupe."&amp;id_classe=".$lig_tmp_clas->id_classe."' target='_blank'>$lig_tmp_clas->classe</a>, ";
											echo $info;
											$chaine_rapport.=$info;
										}
										$info="si il s'y trouve.<br />\n";
										echo $info;
										$chaine_rapport.=$info;
									}

									$info="Si aucune erreur n'est relevée non plus dans la(es) classe(s) de ";
									$info.="<a href='../classes/eleve_options.php?login_eleve=".$lig_ele->login."&amp;id_classe=".$lig_clas->id_classe."' target='_blank'>$clas_tmp</a>, \n";
									echo $info;
									$chaine_rapport.=$info;

									for($i=0;$i<count($tab_tmp_class);$i++){
										$info="<a href='../classes/eleve_options.php?login_eleve=".$lig_ele->login."&amp;id_classe=".$tab_tmp_class[$i]."' target='_blank'>".$tab_tmp_classe[$i]."</a>, \n";
										echo $info;
										$chaine_rapport.=$info;
									}
									$info="il faudra effectuer un <a href='clean_tables.php?maj=9".add_token_in_url()."'>nettoyage des tables de la base de données GEPI</a> (<i>après une <a href='../gestion/accueil_sauve.php?action=";
									if(getSettingValue("mode_sauvegarde")=="gepi") {$info.="dump";} else {$info.="system_dump";}
									$info.=add_token_in_url()."' target='blank'>sauvegarde de la base</a></i>).<br />\n";
									$info.="</p>\n";
									echo $info;
									$chaine_rapport.=$info;

									$err_no++;
								}
							}
						}
						else{
							$temoin_erreur="y";
							$info="<p>\n";
							$info.="<b>$lig_ele->login</b> est inscrit dans plusieurs classes sur la période $num_periode:<br />\n";
							echo $info;
							$chaine_rapport.=$info;

							while($lig_clas=mysqli_fetch_object($res_jec)){
								$sql="SELECT classe FROM classes WHERE id='$lig_clas->id_classe'";
								$res_tmp=mysqli_query($GLOBALS["mysqli"], $sql);
								$lig_tmp=mysqli_fetch_object($res_tmp);
								$clas_tmp=$lig_tmp->classe;
								$info="Classe de <a href='../classes/classes_const.php?id_classe=$lig_clas->id_classe'>$clas_tmp</a> (<i>n°$lig_clas->id_classe</i>)<br />\n";
								echo $info;
								$chaine_rapport.=$info;
							}
							$info="Cela ne devrait pas être possible.<br />\n";
							$info.="Faites le ménage dans les effectifs des classes ci-dessus.\n";
							$info.="</p>\n";
							echo $info;
							$chaine_rapport.=$info;
							$err_no++;
						}
					}
				}
				// Pour envoyer ce qui a été écrit vers l'écran sans attendre la fin de la page...
				flush();
			}

			$sql="UPDATE tempo2 SET col2='$temoin_erreur' WHERE col1='$lig_ele->login';";
			$update=mysqli_query($GLOBALS["mysqli"], $sql);
		}


		// INSERER $chaine_rapport DANS UNE TABLE
		$sql="INSERT INTO tempo3 SET col1='rapport_verif_grp', col2='".addslashes($chaine_rapport)."';";
		$insert=mysqli_query($GLOBALS["mysqli"], $sql);

		echo "<form action=\"".$_SERVER['PHP_SELF']."#suite\" name='suite' method=\"post\">\n";
		echo add_token_field();
		echo "<input type=\"hidden\" name=\"verif\" value=\"y\" />\n";
		echo "<input type=\"hidden\" name=\"ini\" value=\"$ini\" />\n";
		echo "<input type=\"hidden\" name=\"c_est_parti\" value=\"y\" />\n";

		echo "<script type='text/javascript'>
	setTimeout(\"document.forms['suite'].submit();\", 2000);
</script>\n";

		echo "<NOSCRIPT>\n";
		echo "<div id='fixe'><input type=\"submit\" name=\"ok\" value=\"Suite de la vérification\" /></div>\n";
		echo "</NOSCRIPT>\n";


		echo "</form>\n";


	}
	else {

		$sql="SELECT 1=1 FROM tempo2 WHERE col2='y';";
		$test_err=mysqli_query($GLOBALS["mysqli"], $sql);
		$err_no=mysqli_num_rows($test_err);

		if($err_no==0){
			echo "<p>Aucune erreur d'affectation dans des groupes/classes n'a été détectée.</p>\n";
		}
		else{
			echo "<p>Une ou des erreurs ont été relevées.<br />\n";
			echo "Pour corriger, il faut passer par 'Gestion des bases/Gestion des classes/Gérer les élèves' et contrôler pour quelles périodes l'élève est dans la classe.<br />\n";
			echo "Puis, cliquer sur le lien 'Matières suivies' pour cet élève et décocher l'élève des périodes souhaitées appropriées.<br />\n";
			echo "</p>\n";
			echo "<p>Il se peut également qu'un <a href='clean_tables.php?maj=9".add_token_in_url()."'>nettoyage de la base (<i>étape des Groupes</i>)</a> soit nécessaire.<br />\n";
			echo "Prenez soin de faire une <a href='../gestion/accueil_sauve.php?action=";
			if(getSettingValue("mode_sauvegarde")=="gepi") {$info.="dump";} else {$info.="system_dump";}
			$info.=add_token_in_url()."' target='blank'>sauvegarde de la base</a> auparavant par précaution.<br />\n";
		}

		echo "<hr />\n";

		echo "<h2>Recherche des références à des identifiants de groupes inexistants</h2>\n";

		$err_no=0;
		$table=array('j_groupes_classes','j_groupes_matieres','j_groupes_professeurs','j_eleves_groupes', 'j_groupes_visibilite', 'acces_cdt_groupes');
		$id_grp_suppr=array();

		for($i=0;$i<count($table);$i++){
			$sql="SELECT DISTINCT id_groupe FROM ".$table[$i]." ORDER BY id_groupe";
			$res_grp1=mysqli_query($GLOBALS["mysqli"], $sql);

			if(mysqli_num_rows($res_grp1)>0){
				echo "<p>On parcourt la table '".$table[$i]."'.</p>\n";
				while($ligne=mysqli_fetch_array($res_grp1)){
					$sql="SELECT 1=1 FROM groupes WHERE id='".$ligne[0]."'";
					$res_test=mysqli_query($GLOBALS["mysqli"], $sql);

					if(mysqli_num_rows($res_test)==0){
						echo "<b>Erreur:</b> Le groupe d'identifiant $ligne[0] est utilisé dans $table[$i] alors que le groupe n'existe pas dans la table 'groupes'.<br />\n";
						$id_grp_suppr[]=$ligne[0];
						// FAIRE UNE SAUVEGARDE DE LA BASE AVANT DE DECOMMENTER LES 3 LIGNES CI-DESSOUS:
						/*
						$sql="DELETE FROM $table[$i] WHERE id_groupe='$ligne[0]'";
						echo "$sql<br />";
						$res_suppr=mysql_query($sql);
						*/
						$err_no++;
					}
					flush();
				}
			}
		}
		if($err_no==0){
			echo "<p>Aucune erreur d'identifiant de groupe n'a été relevée dans les tables 'j_groupes_classes', 'j_groupes_matieres', 'j_groupes_professeurs' et 'j_eleves_groupes'.</p>\n";
		}
		else{
			echo "<p>Une ou des erreurs ont été relevées.<br />\n";
			echo "Pour corriger, vous devriez procéder à un <a href='clean_tables.php?maj=9".add_token_in_url()."'>nettoyage de la base (<i>étape des Groupes</i>)</a>.<br />\n";
			echo "Prenez soin de faire une <a href='../gestion/accueil_sauve.php?action=";
			if(getSettingValue("mode_sauvegarde")=="gepi") {$info.="dump";} else {$info.="system_dump";}
			$info.=add_token_in_url()."' target='blank'>sauvegarde de la base</a> auparavant par précaution.<br />\n";
			echo "</p>\n";
		}
	}
}
echo "<p><br /></p>\n";
require("../lib/footer.inc.php");
?>
