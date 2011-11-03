<?php
@set_time_limit(0);
/*
* $Id: clean_tables.php 8528 2011-10-25 12:52:04Z crob $
*
* Copyright 2001-2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
}
// Check access

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

$valid = isset($_POST["valid"]) ? $_POST["valid"] : 'no';

$id_info=isset($_POST['id_info']) ? $_POST['id_info'] : (isset($_GET['id_info']) ? $_GET['id_info'] : '');
$mode_auto=isset($_POST['mode_auto']) ? $_POST['mode_auto'] : (isset($_GET['mode_auto']) ? $_GET['mode_auto'] : 'n');

//==================================
// header
$titre_page = "V�rification/nettoyage des tables de la base de donn�es GEPI";
require_once("../lib/header.inc");
//==================================

//======================================================
// Fonctions � utiliser... juste stock�es ici pour le moment.
function get_tab_utilisateurs_responsables_fantomes() {
	$retour=array();

	$sql="select login from utilisateurs where statut='responsable' and login not in (select login from resp_pers);";
	$res=mysql_query($sq);
	if(mysql_num_rows($res)>0) {
		while($lig=mysql_fetch_object($res)) {
			$retour[]=$lig->login;
		}
	}
	return $retour;
}

function menage_utilisateurs_responsables() {
	$sql="delete from utilisateurs where statut='responsable' and login not in (select login from resp_pers);";
	$res=mysql_query($sql);
	if(!$res) {
		return false;
	}
	else {
		return true;
	}
}

function get_tab_utilisateurs_eleves_fantomes() {
	$retour=array();

	$sql="select login from utilisateurs where statut='eleve' and login not in (select login from eleves);";
	$res=mysql_query($sq);
	if(mysql_num_rows($res)>0) {
		while($lig=mysql_fetch_object($res)) {
			$retour[]=$lig->login;
		}
	}
	return $retour;
}

function menage_utilisateurs_eleves() {
	$sql="delete from utilisateurs where statut='eleve' and login not in (select login from eleves);";
	$res=mysql_query($sql);
	if(!$res) {
		return false;
	}
	else {
		return true;
	}
}
//======================================================

//$total_etapes = 8;
$total_etapes = 15;
$duree = 8;
if (!isset($_GET['cpt'])) {
	$cpt = 0;
} else {
	$cpt = $_GET['cpt'];
}

$maj=isset($_POST['maj']) ? $_POST['maj'] : (isset($_GET['maj']) ? $_GET['maj'] : NULL);

$stop=isset($_POST['stop']) ? $_POST['stop'] : (isset($_GET['stop']) ? $_GET['stop'] :'n');

//debug_var();

if((isset($maj))||(isset($_REQUEST['action']))) {
	check_token();
}

//if (($_POST['maj'])=="9") {
if ($maj=="9") {
	echo "<p class=bold><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil</a> ";
	echo "| <a href='clean_tables.php'>Retour page V�rification / Nettoyage des tables</a></p>\n";
}

//debug_var();

function init_time() {
	global $TPSDEB,$TPSCOUR;
	list ($usec,$sec)=explode(" ",microtime());
	$TPSDEB=$sec;
	$TPSCOUR=0;
}

function current_time() {
	global $TPSDEB,$TPSCOUR;
	list ($usec,$sec)=explode(" ",microtime());
	$TPSFIN=$sec;
	if (round($TPSFIN-$TPSDEB,1)>=$TPSCOUR+1) //une seconde de plus
	{
	$TPSCOUR=round($TPSFIN-$TPSDEB,1);
	flush();
	}
}

function get_id_infos_action_nettoyage() {
	global $id_info;

	if($id_info!='') {
		return $id_info;
	}
	else {
		return new_id_infos_action_nettoyage();
	}
}

function new_id_infos_action_nettoyage() {
	//$id_info="";

	$titre="Nettoyage des tables : ".strftime("%d/%m/%Y � %H:%M:%S");
	$texte="Nettoyage des tables...<br />";
	$destinataire="administrateur";
	$mode="statut";
	$id_info=enregistre_infos_actions($titre,$texte,$destinataire,$mode);

	return $id_info;
}

function update_infos_action_nettoyage($id_info, $texte) {
	$retour="";

	$sql="SELECT description FROM infos_actions WHERE id='$id_info';";
	//echo "$sql<br />\n";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)>0) {
		$lig=mysql_fetch_object($res);

		//$sql="UPDATE infos_actions SET description='".addslashes($lig->description).addslashes($texte)."<hr align=\"center\" width=\"200\" />' WHERE id='$id_info';";
		$sql="UPDATE infos_actions SET description='".addslashes($lig->description).addslashes($texte)."' WHERE id='$id_info';";
		//echo "$sql<br />\n";
		$res=mysql_query($sql);
		if(!$res) {$retour="ERREUR lors de la mise � jour de la description de l'information n�$id_info.";}
	}
	else {
		$retour="ERREUR : L'information n�$id_info n'existe pas.";
	}

	//echo $retour;

	return $retour;
}

function script_suite_submit() {
	global $mode_auto;

	$retour="";
	if($mode_auto=='y') {
		$retour="<script type='text/javascript'>
	setTimeout(\"document.forms['formulaire'].submit();\",1000);
</script>\n";
	}
	return $retour;
}

//function etape7() {
function clean_table_matieres_appreciations() {
	global $id_info;

	global $TPSCOUR,$offset,$duree,$cpt,$nb_lignes;
	// Cas de la table matieres_appreciations
	$req = mysql_query("SELECT * FROM matieres_appreciations order by login,id_groupe,periode");
	$nb_lignes = mysql_num_rows($req);
	if ($offset >= $nb_lignes) {
		$offset = -1;
		return true;
		exit();
	}
	$fin = '';
	$cpt_2 = 0;
	while (($offset<$nb_lignes) and ($fin == '')) {
		$login_user = mysql_result($req,$offset,'login');
		$id_groupe = mysql_result($req,$offset,'id_groupe');
		$periode = mysql_result($req,$offset,'periode');

		// D�tection des doublons
		$req2 = mysql_query("SELECT login FROM matieres_appreciations
		where
		login ='$login_user' and
		id_groupe ='$id_groupe' and
		periode ='$periode'
		");
		$nb_lignes2 = mysql_num_rows($req2);
		if ($nb_lignes2 > "1") {
			$nb = $nb_lignes2-1;
			//echo "Suppression d'un doublon : login = $login_user - identifiant matiere = $id_matiere - Num�ro p�riode = $periode<br />\n";
			// On efface les lignes en trop
			$del = mysql_query("delete from matieres_appreciations where
			login ='$login_user' and
			id_groupe ='$id_groupe' and
			periode ='$periode' LIMIT $nb");
			$cpt++;
			$cpt_2++;
		}

		// D�tection des donn�es inutiles
		/*
		$test = mysql_query("select ma.login
		from
			matieres_appreciations ma,
			eleves e,
			j_eleves_classes jec,
			periodes p,
			matieres m,
			j_eleves_groupes jeg,
			groupes g
		where
			ma.login = '$login_user' and
			e.login = '$login_user' and
			jec.login = '$login_user' and
			jeg.login = '$login_user' and
			jec.id_classe = p.id_classe and
			jec.periode = '$periode' and
			p.num_periode = '$periode' and
			ma.periode = '$periode' and
			g.id = '$id_groupe' and
			jeg.id_groupe = '$id_groupe'
			");
		*/
		$sql="select jec.login
		from
			j_eleves_classes jec,
			j_eleves_groupes jeg
		where
			jec.login = jeg.login AND
			jec.periode = jeg.periode AND
			jec.login='$login_user' and
			jec.periode = '$periode' and
			jeg.id_groupe = '$id_groupe'
			";
		$test = mysql_query($sql);
		//echo "$sql<br />\n";
		$nb_lignes2 = mysql_num_rows($test);
		if ($nb_lignes2 == "0") {
			//echo "Suppression d'une donn�e orpheline : login = $login_user - identifiant mati�re = $id_matiere - Num�ro p�riode = $periode<br />\n";
			// On efface les lignes en trop
			$del = mysql_query("delete from matieres_appreciations where
			login ='$login_user' and
			id_groupe ='$id_groupe' and
			periode ='$periode'");
			$cpt++;
			$cpt_2++;
		}
		// on regarde si l'�l�ve suit l'option pour la p�riode donn�e.
		$test2 = mysql_query("select login from j_eleves_groupes where
		login = '$login_user' and
		id_groupe = '$id_groupe' and
		periode = '$periode'");
		$nb_lignes2 = mysql_num_rows($test2);
		if ($nb_lignes2 == "0") {
			//echo "Suppression d'une donn�e orpheline : login = $login_user - identifiant mati�re = $id_matiere - Num�ro p�riode = $periode<br />\n";
			// On efface les lignes en trop
			$del = mysql_query("delete from matieres_appreciations where
			login ='$login_user' and
			id_groupe ='$id_groupe' and
			periode ='$periode'");
		}
		current_time();
		if ($duree>0 and $TPSCOUR>=$duree) { //on atteint la fin du temps imparti
			$fin = 'yes';
		} else {
			$offset++;
		}
	}
	$offset = $offset - $cpt_2;
	return true;
}

//function etape8() {
function clean_table_matieres_notes($id_nettoyage=-1) {
	global $id_info;

	global $TPSCOUR,$offset,$duree,$cpt,$nb_lignes;
	// Cas de la table matieres_appreciations
	//$req = mysql_query("SELECT * FROM matieres_notes order by login,matiere,periode");
	$req = mysql_query("SELECT * FROM matieres_notes order by login,id_groupe,periode");
	$nb_lignes = mysql_num_rows($req);
	if ($offset >= $nb_lignes) {
		$offset = -1;
		return true;
		exit();
	}
	$fin = '';
	$cpt_2 = 0;
	while (($offset<$nb_lignes) and ($fin == '')) {
		$login_user = mysql_result($req,$offset,'login');
		$id_groupe = mysql_result($req,$offset,'id_groupe');
		$periode = mysql_result($req,$offset,'periode');

		// D�tection des doublons
		$req2 = mysql_query("SELECT login FROM matieres_notes
		where
		login ='$login_user' and
		id_groupe ='$id_groupe' and
		periode ='$periode'
		");
		$nb_lignes2 = mysql_num_rows($req2);
		if ($nb_lignes2 > "1") {
			$nb = $nb_lignes2-1;
			//echo "Suppression d'un doublon : login = $login_user - identifiant matiere = $id_matiere - Num�ro p�riode = $periode<br />\n";
			// On efface les lignes en trop
			$del = mysql_query("delete from matieres_notes where
			login ='$login_user' and
			id_groupe ='$id_groupe' and
			periode ='$periode' LIMIT $nb");
			$cpt++;
			$cpt_2++;
		}


		// D�tection des lignes inutiles
		/*
		$test = mysql_query("select mn.login
		from
		matieres_notes mn,
		eleves e,
		j_eleves_classes jec,
		periodes p,
		groupes g,
		j_eleves_groupes jeg
		where
		mn.login = '$login_user' and
		e.login = '$login_user' and
		jec.login = '$login_user' and
		jec.id_classe = p.id_classe and
		jec.periode = '$periode' and
		p.num_periode = '$periode' and
		mn.periode = '$periode' and
		g.id = '$id_groupe'
		");
		*/
		$sql="select jec.login
		from
			j_eleves_classes jec,
			j_eleves_groupes jeg
		where
			jec.login = jeg.login AND
			jec.periode = jeg.periode AND
			jec.login='$login_user' and
			jec.periode = '$periode' and
			jeg.id_groupe = '$id_groupe'
			";
		$test = mysql_query($sql);
		//echo "$sql<br />\n";
		$nb_lignes2 = mysql_num_rows($test);
		if ($nb_lignes2 == "0") {
			//echo "Suppression d'une donn�e orpheline : login = $login_user - identifiant mati�re = $id_matiere - Num�ro p�riode = $periode<br />\n";
			// On efface les lignes en trop
			$del = mysql_query("delete from matieres_notes where
			login ='$login_user' and
			id_groupe ='$id_groupe' and
			periode ='$periode'");
			$cpt++;
			$cpt_2++;
		}
		// on regarde si l'�l�ve suit l'option pour la p�riode donn�e.
		$test2 = mysql_query("select login from j_eleves_groupes where
		login = '$login_user' and
		id_groupe = '$id_groupe' and
		periode = '$periode'");
		$nb_lignes2 = mysql_num_rows($test2);
		if ($nb_lignes2 == "0") {
			//echo "Suppression d'une donn�e orpheline : login = $login_user - identifiant mati�re = $id_matiere - Num�ro p�riode = $periode<br />\n";
			// On efface les lignes en trop
			$del = mysql_query("delete from matieres_notes where
			login ='$login_user' and
			id_groupe ='$id_groupe' and
			periode ='$periode'");
			$cpt++;
			$cpt_2++;
		}
		current_time();
		if ($duree>0 and $TPSCOUR>=$duree) { //on atteint la fin du temps imparti
			$fin = 'yes';
		} else {
			$offset++;
		}
	}
	$offset = $offset - $cpt_2;
	return true;
}

// Etape 1
function clean_tables_aid_et_autres() {
	global $id_info;

	$retour="";

	$tab["j_aid_eleves"][0] = "aid"; //1�re table
	$tab["j_aid_eleves"][1] = "eleves"; // 2�me table
	$tab["j_aid_eleves"][2] = "id_aid"; // nom du champ de la table de liaison li� � la premi�re table
	$tab["j_aid_eleves"][3] = "login";  // nom du champ de la table de liaison li� � la deuxi�me table
	$tab["j_aid_eleves"][4] = "id";  // nom du champ de la premi�re table li� � la table de liaison
	$tab["j_aid_eleves"][5] = "login";  // nom du champ de la deuxi�me table li� � la table de liaison

	$tab["j_aid_eleves_resp"][0] = "aid"; //1�re table
	$tab["j_aid_eleves_resp"][1] = "eleves"; // 2�me table
	$tab["j_aid_eleves_resp"][2] = "id_aid"; // nom du champ de la table de liaison li� � la premi�re table
	$tab["j_aid_eleves_resp"][3] = "login";  // nom du champ de la table de liaison li� � la deuxi�me table
	$tab["j_aid_eleves_resp"][4] = "id";  // nom du champ de la premi�re table li� � la table de liaison
	$tab["j_aid_eleves_resp"][5] = "login";  // nom du champ de la deuxi�me table li� � la table de liaison

	$tab["j_aid_utilisateurs"][0] = "aid"; //1�re table
	$tab["j_aid_utilisateurs"][1] = "utilisateurs"; // 2�me table
	$tab["j_aid_utilisateurs"][2] = "id_aid"; // nom du champ de la table de liaison li� � la premi�re table
	$tab["j_aid_utilisateurs"][3] = "id_utilisateur";  // nom du champ de la table de liaison li� � la deuxi�me table
	$tab["j_aid_utilisateurs"][4] = "id";  // nom du champ de la premi�re table li� � la table de liaison
	$tab["j_aid_utilisateurs"][5] = "login";  // nom du champ de la deuxi�me table li� � la table de liaison
	if (getSettingValue("active_mod_gest_aid")=="y") {
		$tab["j_aid_utilisateurs_gest"][0] = "aid"; //1�re table
		$tab["j_aid_utilisateurs_gest"][1] = "utilisateurs"; // 2�me table
		$tab["j_aid_utilisateurs_gest"][2] = "id_aid"; // nom du champ de la table de liaison li� � la premi�re table
		$tab["j_aid_utilisateurs_gest"][3] = "id_utilisateur";  // nom du champ de la table de liaison li� � la deuxi�me table
		$tab["j_aid_utilisateurs_gest"][4] = "id";  // nom du champ de la premi�re table li� � la table de liaison
		$tab["j_aid_utilisateurs_gest"][5] = "login";  // nom du champ de la deuxi�me table li� � la table de liaison
	
		$tab["j_aidcateg_super_gestionnaires"][0] = "aid_config"; //1�re table
		$tab["j_aidcateg_super_gestionnaires"][1] = "utilisateurs"; // 2�me table
		$tab["j_aidcateg_super_gestionnaires"][2] = "indice_aid"; // nom du champ de la table de liaison li� � la premi�re table
		$tab["j_aidcateg_super_gestionnaires"][3] = "id_utilisateur";  // nom du champ de la table de liaison li� � la deuxi�me table
		$tab["j_aidcateg_super_gestionnaires"][4] = "indice_aid";  // nom du champ de la premi�re table li� � la table de liaison
		$tab["j_aidcateg_super_gestionnaires"][5] = "login";  // nom du champ de la deuxi�me table li� � la table de liaison
	
	}
	$tab["j_aidcateg_utilisateurs"][0] = "aid_config"; //1�re table
	$tab["j_aidcateg_utilisateurs"][1] = "utilisateurs"; // 2�me table
	$tab["j_aidcateg_utilisateurs"][2] = "indice_aid"; // nom du champ de la table de liaison li� � la premi�re table
	$tab["j_aidcateg_utilisateurs"][3] = "id_utilisateur";  // nom du champ de la table de liaison li� � la deuxi�me table
	$tab["j_aidcateg_utilisateurs"][4] = "indice_aid";  // nom du champ de la premi�re table li� � la table de liaison
	$tab["j_aidcateg_utilisateurs"][5] = "login";  // nom du champ de la deuxi�me table li� � la table de liaison

	$tab["j_eleves_etablissements"][0] = "eleves"; //1�re table
	$tab["j_eleves_etablissements"][1] = "etablissements"; // 2�me table
	$tab["j_eleves_etablissements"][2] = "id_eleve"; // nom du champ de la table de liaison li� � la premi�re table
	$tab["j_eleves_etablissements"][3] = "id_etablissement";  // nom du champ de la table de liaison li� � la deuxi�me table
	//$tab["j_eleves_etablissements"][4] = "login";  // nom du champ de la premi�re table li� � la table de liaison
	$tab["j_eleves_etablissements"][4] = "elenoet";  // nom du champ de la premi�re table li� � la table de liaison
	$tab["j_eleves_etablissements"][5] = "id";  // nom du champ de la deuxi�me table li� � la table de liaison

	$tab["j_eleves_regime"][0] = "eleves"; //1�re table
	$tab["j_eleves_regime"][1] = "eleves"; // 2�me table
	$tab["j_eleves_regime"][2] = "login"; // nom du champ de la table de liaison li� � la premi�re table
	$tab["j_eleves_regime"][3] = "login";  // nom du champ de la table de liaison li� � la deuxi�me table
	$tab["j_eleves_regime"][4] = "login";  // nom du champ de la premi�re table li� � la table de liaison
	$tab["j_eleves_regime"][5] = "login";  // nom du champ de la deuxi�me table li� � la table de liaison

	$tab["j_professeurs_matieres"][0] = "utilisateurs"; //1�re table
	$tab["j_professeurs_matieres"][1] = "matieres"; // 2�me table
	$tab["j_professeurs_matieres"][2] = "id_professeur"; // nom du champ de la table de liaison li� � la premi�re table
	$tab["j_professeurs_matieres"][3] = "id_matiere";  // nom du champ de la table de liaison li� � la deuxi�me table
	$tab["j_professeurs_matieres"][4] = "login";  // nom du champ de la premi�re table li� � la table de liaison
	$tab["j_professeurs_matieres"][5] = "matiere";  // nom du champ de la deuxi�me table li� � la table de liaison

	foreach ($tab as $key => $val) {
		$cpt=0;
		$retour.="<h2>V�rification de la table ".$key."</h2>\n";
		// $key : le nom de la table de liaison
		// $val[0] : le nom de la premi�re table
		// $val[1] : le nom de la deuxi�me table
		// etc...
		$req = mysql_query("SELECT * FROM $key order by $val[2],$val[3]");
		$nb_lignes = mysql_num_rows($req);
		$i = 0;
		$affiche = 'yes';
		while ($i < $nb_lignes) {
			$temp1 = mysql_result($req,$i,$val[2]);
			$temp2 = mysql_result($req,$i,$val[3]);

			$req2 = mysql_query("SELECT * FROM $key j, $val[0] t1, $val[1] t2

			where
			j.$val[2]=t1.$val[4] and
			j.$val[3]=t2.$val[5] and
			j.$val[2]='$temp1' and
			j.$val[3]='$temp2'
			");
			$nb_lignes2 = mysql_num_rows($req2);
			// suppression des doublons
			if ($nb_lignes2 > "1") {
				$nb = $nb_lignes2-1;
				// cas j_aid_eleves et j_aid_utilisateurs
				if (($key == "j_aid_eleves") or ($key == "j_aid_utilisateurs") or ($key == "j_aid_eleves_resp") or ($key == "j_aid_utilisateurs_gest")) {
					$indice_aid = mysql_result($req,$i,'indice_aid');
					$test = sql_query1("select a.indice_aid from aid_config ac, aid a
					where
					ac.indice_aid ='$indice_aid' and
					a.id = '$temp1'and
					a.indice_aid = '$indice_aid' ");
					if ($test == "-1") {
						//echo "Suppression d'un doublon : $temp1 - $temp2<br />\n";
						$del = mysql_query("delete from $key where ($val[2]='$temp1' and $val[3]='$temp2' and indice_aid='$indice_aid')");
						$cpt++;
					}
					// autres cas
				} else {
					//echo "Suppression d'un doublon : $temp1 - $temp2<br />\n";
					$del = mysql_query("delete from $key where ($val[2]='$temp1' and $val[3]='$temp2') LIMIT $nb");
					$cpt++;
				}
			}
			// On supprime les lignes inutiles
			if ($nb_lignes2 == "0") {
				//echo "Suppression d'une ligne inutile : $temp1 - $temp2<br />\n";
				$del = mysql_query("delete from $key where $val[2]='$temp1' and $val[3]='$temp2'");
				$cpt++;
			}
			$i++;
		}
		if ($cpt != 0) {
			$retour.="<font color=\"red\">Nombre de lignes supprim�es : ".$cpt."</font><br />\n";
		} else {
			$retour.="<font color=\"green\">Aucune ligne n'a �t� supprim�e.</font><br />\n";
		}

		if($key=='j_professeurs_matieres') {
			// Le test plus haut ne fonctionne pas completement: s'il y a eu des collisions de logins (?) d'une ann�e sur l'autre, et des tables non nettoy�es, on peut se retrouver avec un login attribu� � un parent alors que c'�tait le login d'un prof l'ann�e pr�c�dente... et si j_professeurs_matieres n'a pas �t� nettoy�e, on se retrouve avec un parent d'�l�ve propos� comme professeur lors de l'ajout d'enseignement
			//$sql="select * from j_professeurs_matieres j, resp_pers rp where rp.login=j.id_professeur AND j.id_professeur not in (select login from utilisateurs where statut='professeur');";
			$sql="SELECT * FROM j_professeurs_matieres j WHERE j.id_professeur NOT IN (SELECT login FROM utilisateurs WHERE statut='professeur');";
			$test=mysql_query($sql);
			if(mysql_num_rows($test)>0) {
				$sql="DELETE FROM j_professeurs_matieres WHERE id_professeur NOT IN (SELECT login FROM utilisateurs WHERE statut='professeur');";
				$del=mysql_query($sql);
				if($del) {$retour.="<font color=\"red\">Suppression de ".mysql_num_rows($test)." enregistrements suppl�mentaires.</font><br />\n";}
			}
		}

		$retour.="<b>La table $key est OK.</b><br />\n";
	}

	return $retour;
}

// Etape 2
function clean_table_j_eleves_professeurs() {
	global $id_info;
	global $cpt;

	$retour="";

	// cas j_eleves_professeurs
	$retour.="<h2>V�rification de la table j_eleves_professeurs</h2>\n";
	$req = mysql_query("SELECT * FROM j_eleves_professeurs order by login,professeur,id_classe");
	$nb_lignes = mysql_num_rows($req);
	$i = 0;
	while ($i < $nb_lignes) {

	$login_user = mysql_result($req,$i,'login');
		$professeur = mysql_result($req,$i,'professeur');
		$id_classe = mysql_result($req,$i,'id_classe');

		// D�tection des doublons
		$req2 = mysql_query("SELECT * FROM j_eleves_professeurs
		where
		login ='$login_user' and
		professeur ='$professeur' and
		id_classe ='$id_classe'
		");
		$nb_lignes2 = mysql_num_rows($req2);
		if ($nb_lignes2 > "1") {
			$nb = $nb_lignes2-1;
			//$retour.="Suppression d'un doublon : identifiant �l�ve : $login_user - identifiant professeur = $professeur - identifiant classe = $id_classe<br />\n";
			// On efface les lignes en trop
			$del = mysql_query("delete from j_eleves_professeurs where
			login ='$login_user' and
			professeur ='$professeur' and
			id_classe ='$id_classe' LIMIT $nb");
			$cpt++;
		}

		// D�tection des lignes inutiles
		$req3 = mysql_query("SELECT *
		FROM j_eleves_professeurs j,
		eleves e,
		utilisateurs u,
		j_eleves_classes jec,
		j_groupes_classes jgc,
		j_groupes_professeurs jgp
		where
		j.login ='$login_user' and
		e.login ='$login_user' and
		jec.login = '$login_user' and
		jec.id_classe = '$id_classe' and
		j.professeur ='$professeur' and
		u.login ='$professeur' and
		jgp.login = '$professeur' and
		jgc.id_classe = '$id_classe' and
		jgp.id_groupe = jgc.id_groupe and
		j.id_classe ='$id_classe'
		");
		$nb_lignes3 = mysql_num_rows($req3);
		if ($nb_lignes3 == "0") {
			$nb = $nb_lignes2-1;
			//$retour.="Suppression d'une ligne inutile : identifiant �l�ve : $login_user - identifiant professeur = $professeur - identifiant classe = $id_classe<br />\n";
			// On efface les lignes en trop
			$del = mysql_query("delete from j_eleves_professeurs where
			login ='$login_user' and
			professeur ='$professeur' and
			id_classe ='$id_classe'");
			$cpt++;
		}
		mysql_free_result($req2);
		mysql_free_result($req3);

	$i++;
	}
	if ($cpt != 0) {
		$retour.="<font color=\"red\">Nombre de lignes supprim�es : ".$cpt."</font><br />\n";
	} else {
		$retour.="<font color=\"green\">Aucune ligne n'a �t� supprim�e.</font><br />\n";
	}
	$retour.="<b>La table j_eleves_professeurs est OK.</b><br />\n";

	return $retour;
}

// Etape 4
function clean_table_j_eleves_classes() {
	global $id_info;
	global $cpt;

	$retour="";

	// V�rification de la table j_eleves_classes

	$retour.="<h2>V�rification de la table j_eleves_classes</h2>\n";
	$req = mysql_query("SELECT * FROM j_eleves_classes");
	$nb_lignes = mysql_num_rows($req);
	$i = 0;
	while ($i < $nb_lignes) {
		$login_user = mysql_result($req,$i,'login');
		$id_classe = mysql_result($req,$i,'id_classe');
		$periode = mysql_result($req,$i,'periode');
		// D�tection des doublons
		$req2 = mysql_query("SELECT * FROM j_eleves_classes
			where
			login ='$login_user' and
			id_classe ='$id_classe' and
			periode ='$periode'
			");
		$nb_lignes2 = mysql_num_rows($req2);
		if ($nb_lignes2 > "1") {
			$nb = $nb_lignes2-1;
			//$retour.="Suppression d'un doublon : login = $login_user - identifiant classe = $id_classe - Num�ro p�riode = $periode<br />\n";
			// On efface les lignes en trop
			$del = mysql_query("delete from j_eleves_classes where
			login ='$login_user' and
			id_classe ='$id_classe' and
			periode ='$periode' LIMIT $nb");
			$cpt++;
		}
		// D�tection des lignes inutiles
		$req3 = mysql_query("SELECT * FROM j_eleves_classes j, eleves e, classes c, periodes p
		where
		j.login ='$login_user' and
		j.id_classe ='$id_classe' and
		j.periode ='$periode' and
		e.login ='$login_user' and
		c.id ='$id_classe' and
		p.num_periode ='$periode' and
		p.id_classe = '$id_classe'


		");
		$nb_lignes3 = mysql_num_rows($req3);
		if ($nb_lignes3 == "0") {
			$nb = $nb_lignes2-1;
			//$retour.="Suppression d'une ligne inutile : login = $login_user - identifiant classe = $id_classe - Num�ro p�riode = $periode<br />\n";
			// On efface les lignes en trop
			$del = mysql_query("delete from j_eleves_classes where
			login ='$login_user' and
			id_classe ='$id_classe' and
			periode ='$periode'");
			$cpt++;
		}
		mysql_free_result($req2);
		mysql_free_result($req3);

		$i++;
	}
	if ($cpt != 0) {
		$retour.="<font color=\"red\">Nombre de lignes supprim�es : ".$cpt."</font><br />\n";
	} else {
		$retour.="<font color=\"green\">Aucune ligne n'a �t� supprim�e.</font><br />\n";
	}
	$retour.="<b>La table j_eleves_classes est OK.</b><br />\n";

	return $retour;
}

// Etape 6
function clean_tables_aid_appreciations_et_avis_conseil_classe() {
	global $id_info;
	global $cpt;

	$retour="";

	// Cas de la table aid_appreciations
	$retour.="<h2>Nettoyage de la table aid_appreciations (tables des appr�ciations AID)</h2>\n";
	$req = mysql_query("SELECT * FROM aid_appreciations order by login,id_aid,periode");
	$nb_lignes = mysql_num_rows($req);
	$i = 0;
	while ($i < $nb_lignes) {
		$login_user = mysql_result($req,$i,'login');
		$id_aid = mysql_result($req,$i,'id_aid');

	$periode = mysql_result($req,$i,'periode');


	$test = mysql_query("select aa.login
		from
		aid_appreciations aa,
		eleves e,
		j_eleves_classes jec,
		periodes p,
		aid a,

		j_aid_eleves jae

		where
		aa.login = '$login_user' and
		e.login = '$login_user' and
		jec.login = '$login_user' and
		jec.id_classe = p.id_classe and
		jec.periode = '$periode' and
		p.num_periode = '$periode' and
		aa.periode = '$periode' and
		aa.id_aid = '$id_aid' and

	a.id = '$id_aid' and
		jae.login = '$login_user' and
		jae.id_aid = '$id_aid'
		");
		$nb_lignes2 = mysql_num_rows($test);
		if ($nb_lignes2 == "0") {
			//$retour.="Suppression d'une donn�e orpheline : login = $login_user - identifiant aid = $id_aid - Num�ro p�riode = $periode<br />\n";
			// On efface les lignes en trop
			$del = mysql_query("delete from aid_appreciations where
			login ='$login_user' and
			id_aid ='$id_aid' and
			periode ='$periode'");
			$cpt++;
		}
		mysql_free_result($test);
		$i++;
	}
	if ($cpt != 0) {
		$retour.="<font color=\"red\">Nombre de lignes supprim�es : ".$cpt."</font><br />\n";
	} else {
		$retour.="<font color=\"green\">Aucune ligne n'a �t� supprim�e.</font><br />\n";
	}
	$retour.="<b>La table aid_appreciations est OK.</b><br />\n";


	// Cas de la table avis_conseil_classe
	$retour.="<h2>Nettoyage de la table avis_conseil_classe (tables des avis du conseil de classe)</h2>\n";
	$req = mysql_query("SELECT * FROM avis_conseil_classe order by login,periode");
	$nb_lignes = mysql_num_rows($req);
	$i = 0;
	while ($i < $nb_lignes) {
		$login_user = mysql_result($req,$i,'login');
		$periode = mysql_result($req,$i,'periode');

		$test = mysql_query("select acc.login
		from
		avis_conseil_classe acc,
		eleves e,
		j_eleves_classes jec,
		periodes p

		where
		acc.login = '$login_user' and
		e.login = '$login_user' and

	jec.login = '$login_user' and
		jec.id_classe = p.id_classe and

	jec.periode = '$periode' and
		p.num_periode = '$periode' and
		acc.periode = '$periode'
		");
		$nb_lignes2 = mysql_num_rows($test);
		if ($nb_lignes2 == "0") {
			//$retour.="Suppression d'une donn�e orpheline : login = $login_user - Num�ro p�riode = $periode<br />\n";
			// On efface les lignes en trop
			$del = mysql_query("delete from avis_conseil_classe where
			login ='$login_user' and
			periode ='$periode'");
			$cpt++;
		}
		mysql_free_result($test);
		$i++;
	}
	$retour.="<b>La table avis_conseil_classe est OK.</b><br />\n";

	return $retour;
}


// Etape XXX
function clean_table_XXX() {
	global $id_info;

	$retour="";

	return $retour;
}

// Voir s'il y a d'autres POST correspondant � des nettoyages lanc�s
if(isset($_POST['maj'])) {
	$id_info=get_id_infos_action_nettoyage();
}

if ((isset($_POST['maj']) and (($_POST['maj'])=="1"))||(isset($_GET['maj']) and (($_GET['maj'])=="1"))) {
	echo "<p class=bold><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil</a> ";
	echo "| <a href='clean_tables.php'>Retour page V�rification / Nettoyage des tables</a>\n";
	echo "</p>\n";

	echo "<h2 align=\"center\">Etape 1/$total_etapes</h2>\n";

	$retour=clean_tables_aid_et_autres();
	update_infos_action_nettoyage($id_info, $retour);
	echo $retour;

	echo "<form name='formulaire' action=\"clean_tables.php\" method=\"post\">\n";
	echo add_token_field();
	echo "<input type=\"hidden\" name=\"maj\" value=\"2\" />\n";
	echo "<input type=\"hidden\" name=\"id_info\" value=\"$id_info\" />\n";
	echo "<input type=\"hidden\" name='mode_auto' value='$mode_auto' />\n";
	echo "<center><input type=\"submit\" name=\"ok\" value=\"Suite de la v�rification\" /></center>\n";
	echo "</form>\n";

	echo script_suite_submit();
} else if ((isset($_POST['maj']) and (($_POST['maj'])=="2"))||(isset($_GET['maj']) and (($_GET['maj'])=="2"))) {
	echo "<p class=bold><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil</a> ";
	echo "| <a href='clean_tables.php'>Retour page V�rification / Nettoyage des tables</a>\n";
	echo "</p>\n";

	echo "<h2 align=\"center\">Etape 2/$total_etapes</h2>\n";

	$retour=clean_table_j_eleves_professeurs();
	update_infos_action_nettoyage($id_info, $retour);
	echo $retour;

	echo "<form name='formulaire' action=\"clean_tables.php\" method=\"post\">\n";
	echo add_token_field();
	echo "<input type=\"hidden\" name=\"maj\" value=\"3\" />\n";
	echo "<input type=\"hidden\" name=\"id_info\" value=\"$id_info\" />\n";
	echo "<input type=\"hidden\" name='mode_auto' value='$mode_auto' />\n";
	echo "<center><input type=\"submit\" name=\"ok\" value=\"Suite de la v�rification\" /></center>\n";
	echo "</form>\n";

	echo script_suite_submit();
} else if ((isset($_POST['maj']) and (($_POST['maj'])=="3"))||(isset($_GET['maj']) and (($_GET['maj'])=="3"))) {
	echo "<p class=bold><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil</a> ";
	echo "| <a href='clean_tables.php'>Retour page V�rification / Nettoyage des tables</a>\n";
	echo "</p>\n";

	echo "<h2 align=\"center\">Etape 3/$total_etapes</h2>\n";
	// Cas de la table j_classes_matieres_professeurs
	echo "<h2>V�rification de la table j_classes_matieres_professeurs</h2>\n";
	echo "<p>La table j_classes_matieres_professeurs n'existe plus et ne peut donc pas �tre nettoy�e. Cette �tape est remplac�e plus loin par un nettoyage des tables de gestion des groupes.</p>\n";
	echo "<form name='formulaire' action=\"clean_tables.php\" method=\"post\">\n";
	echo add_token_field();
	echo "<input type=\"hidden\" name=\"maj\" value=\"4\" />\n";
	echo "<input type=\"hidden\" name=\"id_info\" value=\"$id_info\" />\n";
	echo "<input type=\"hidden\" name='mode_auto' value='$mode_auto' />\n";
	echo "<center><input type=\"submit\" name=\"ok\" value=\"Suite de la v�rification\" /></center>\n";
	echo "</form>\n";

	echo script_suite_submit();
} else if ((isset($_POST['maj']) and (($_POST['maj'])=="4"))||(isset($_GET['maj']) and (($_GET['maj'])=="4"))) {
	echo "<p class=bold><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil</a> ";
	echo "| <a href='clean_tables.php'>Retour page V�rification / Nettoyage des tables</a>\n";
	echo "</p>\n";

	echo "<h2 align=\"center\">Etape 4/$total_etapes</h2>\n";

	$retour=clean_table_j_eleves_classes();
	update_infos_action_nettoyage($id_info, $retour);
	echo $retour;

	echo "<form name='formulaire' action=\"clean_tables.php\" method=\"post\">\n";
	echo add_token_field();
	echo "<input type=\"hidden\" name=\"maj\" value=\"5\" />\n";
	echo "<input type=\"hidden\" name=\"id_info\" value=\"$id_info\" />\n";
	echo "<input type=\"hidden\" name='mode_auto' value='$mode_auto' />\n";
	echo "<center><input type=\"submit\" name=\"ok\" value=\"Suite de la v�rification\" /></center>\n";
	echo "<center><b>Attention : l'�tape suivante peut �tre tr�s longue.</b></center>\n";
	echo "</form>\n";
	echo script_suite_submit();
} else if ((isset($_POST['maj']) and (($_POST['maj'])=="5")) or (isset($_GET['maj']) and (($_GET['maj'])=="5"))) {
	echo "<p class=bold><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil</a> ";
	echo "| <a href='clean_tables.php'>Retour page V�rification / Nettoyage des tables</a>\n";
	echo "</p>\n";

	echo "<h2 align=\"center\">Etape 5/$total_etapes</h2>\n";
	echo "<h2>Nettoyage de la table j_eleves_matieres</h2>\n";
	echo "<p>Cette table n'est plus utilis�e. Cette �tape a �t� remplac�e plus loin par une �tape de nettoyage des attributions d'�l�ves aux groupes...</p>\n";
	echo "<form name='formulaire' action=\"clean_tables.php\" method=\"post\">\n";
	echo add_token_field();
	echo "<input type=\"hidden\" name=\"maj\" value=\"6\" />\n";
	echo "<input type=\"hidden\" name=\"id_info\" value=\"$id_info\" />\n";
	echo "<input type=\"hidden\" name='mode_auto' value='$mode_auto' />\n";
	echo "<center><input type=\"submit\" name=\"ok\" value=\"Suite de la v�rification\" /></center>\n";
	echo "</form>\n";
	echo script_suite_submit();
	//}
} else if ((isset($_POST['maj']) and (($_POST['maj'])=="6"))||(isset($_GET['maj']) and (($_GET['maj'])=="6"))) {
	echo "<p class=bold><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil</a> ";
	echo "| <a href='clean_tables.php'>Retour page V�rification / Nettoyage des tables</a>\n";
	echo "</p>\n";

	echo "<h2 align=\"center\">Etape 6/$total_etapes</h2>\n";

	$retour=clean_tables_aid_appreciations_et_avis_conseil_classe();
	update_infos_action_nettoyage($id_info, $retour);
	echo $retour;

	echo "<form name='formulaire' action=\"clean_tables.php\" method=\"post\">\n";
	echo add_token_field();
	echo "<input type=\"hidden\" name=\"maj\" value=\"7\" />\n";
	//echo "<input type=\"hidden\" name=\"maj\" value=\"9\" />\n";
	echo "<input type=\"hidden\" name=\"id_info\" value=\"$id_info\" />\n";
	echo "<input type=\"hidden\" name='mode_auto' value='$mode_auto' />\n";
	echo "<center><input type=\"submit\" name=\"ok\" value=\"Suite de la v�rification\" /></center>\n";
	echo "<center><b>Attention : l'�tape suivante peut �tre tr�s longue.</b></center>\n";
	echo "</form>\n";
	echo script_suite_submit();
} else if ((isset($_POST['maj']) and (($_POST['maj'])=="7")) or (isset($_GET['maj']) and (($_GET['maj'])=="7"))) {
	echo "<p class=bold><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil</a> ";
	echo "| <a href='clean_tables.php'>Retour page V�rification / Nettoyage des tables</a>\n";
	echo "</p>\n";

	echo "<h2 align=\"center\">Etape 7/$total_etapes</h2>\n";

	echo "<h2>Nettoyage de la table matieres_appreciations (tables des appr�ciations par discipline)</h2>\n";

	init_time(); //initialise le temps
	//d�but de fichier
	if (!isset($_GET["offset"])) {
		$offset=0;

		$texte_info_action="<h2>Nettoyage de la table matieres_appreciations (tables des appr�ciations par discipline)</h2>\n";
		update_infos_action_nettoyage($id_info, $texte_info_action);
	}
	else {
		$offset=$_GET["offset"];
	}

	if (!isset($_GET['nb_lignes'])) {
		$req = mysql_query("SELECT * FROM matieres_appreciations order by login,id_groupe,periode");

		$nb_lignes = mysql_num_rows($req);
	} else {
		$nb_lignes = $_GET['nb_lignes'];
	}

	if(isset($offset)){
		if (($offset>=0)&&($nb_lignes>0)) {
			$percent=min(100,round(100*$offset/$nb_lignes,0));
		}
		else {$percent=100;}
	}
	else {$percent=0;}
	if ($percent >= 0) {
		$percentwitdh=$percent*4;

	echo "<div align='center'><table width=\"400\" border=\"0\">
		<tr><td width='400' align='center'><b>Nettoyage en cours</b><br /><br />Progression ".$percent."%</td></tr><tr><td><table><tr><td bgcolor='red'  width='$percentwitdh' height='20'>&nbsp;</td></tr></table></td></tr></table></div>\n";
	}
	flush();
	if ($offset>=0){
		//if (etape7()) {
		if (clean_table_matieres_appreciations()) {
			echo "<br />Redirection automatique sinon cliquez <a href=\"clean_tables.php?maj=7&duree=$duree&offset=$offset&cpt=$cpt&nb_lignes=$nb_lignes&mode_auto=$mode_auto&id_info=$id_info".add_token_in_url()."\">ici</a>\n";
			echo "<script>window.location=\"clean_tables.php?maj=7&duree=$duree&offset=$offset&cpt=$cpt&nb_lignes=$nb_lignes&mode_auto=$mode_auto&id_info=$id_info".add_token_in_url(false)."\";</script>\n";
			flush();
			exit;
		}
	} else {
		if ($cpt != 0) {
			$texte_info_action="<font color=\"red\">Nombre de lignes supprim�es : ".$cpt."</font><br />\n";
			$texte_info_action.="<b>La table matieres_appreciations est OK.</b><br />\n";
		} else {
			$texte_info_action="<font color=\"green\">Aucune ligne n'a �t� supprim�e.</font><br />\n";
			$texte_info_action.="<b>La table matieres_appreciations est OK.</b><br />\n";
		}
		echo $texte_info_action;
		update_infos_action_nettoyage($id_info, $texte_info_action);

		echo "<form name='formulaire' action=\"clean_tables.php\" method=\"post\">\n";
		echo add_token_field();
		echo "<input type=\"hidden\" name=\"maj\" value=\"8\" />\n";
		echo "<input type=\"hidden\" name=\"id_info\" value=\"$id_info\" />\n";
		echo "<input type=\"hidden\" name='mode_auto' value='$mode_auto' />\n";
		echo "<center><input type=\"submit\" name=\"ok\" value=\"Suite de la v�rification\" /></center>\n";
		echo "<center><b>Attention : l'�tape suivante peut �tre tr�s longue.</b></center>\n";
		echo "</form>\n";
		echo script_suite_submit();

		// Enregistrer le bilan et passer � la suite... via javascript

	}
} else if ((isset($_POST['maj']) and (($_POST['maj'])=="8")) or (isset($_GET['maj']) and (($_GET['maj'])=="8"))) {
	echo "<p class=bold><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil</a> ";
	echo "| <a href='clean_tables.php'>Retour page V�rification / Nettoyage des tables</a>\n";
	echo "</p>\n";

	echo "<h2 align=\"center\">Etape 8/$total_etapes</h2>\n";
	echo "<h2>Nettoyage de la table matieres_notes (tables des notes par discipline)</h2>\n";
	init_time(); //initialise le temps
	//d�but de fichier
	if (!isset($_GET["offset"])) {
		$offset=0;

		$texte_info_action="<h2>Nettoyage de la table matieres_notes (tables des notes par discipline)</h2>\n";
		update_infos_action_nettoyage($id_info, $texte_info_action);
	}
	else {
		$offset=$_GET["offset"];
	}

	if (!isset($_GET['nb_lignes'])) {
		$req = mysql_query("SELECT * FROM matieres_notes order by login,id_groupe,periode");
		$nb_lignes = mysql_num_rows($req);
	} else {
		$nb_lignes = $_GET['nb_lignes'];
	}

	if(isset($offset)){
		if (($offset>=0)&&($nb_lignes>0)) {
			$percent=min(100,round(100*$offset/$nb_lignes,0));
		}
		else {$percent=100;}
	}
	else {$percent=0;}
	if ($percent >= 0) {
		$percentwitdh=$percent*4;
		echo "<div align='center'><table width=\"400\" border=\"0\">
		<tr><td width='400' align='center'><b>Nettoyage en cours</b><br /><br />Progression ".$percent."%</td></tr><tr><td><table><tr><td bgcolor='red'  width='$percentwitdh' height='20'>&nbsp;</td></tr></table></td></tr></table></div>\n";
	}
	flush();
	if ($offset>=0){
		//if (etape8()) {
		if (clean_table_matieres_notes()) {
			echo "<br />Redirection automatique sinon cliquez <a href=\"clean_tables.php?maj=8&duree=$duree&offset=$offset&cpt=$cpt&nb_lignes=$nb_lignes&mode_auto=$mode_auto&id_info=$id_info".add_token_in_url()."\">ici</a>\n";
			echo "<script>window.location=\"clean_tables.php?maj=8&duree=$duree&offset=$offset&cpt=$cpt&nb_lignes=$nb_lignes&mode_auto=$mode_auto&id_info=$id_info".add_token_in_url(false)."\";</script>\n";
			flush();
			exit;
		}
	} else {
		if ($cpt != 0) {
			$texte_info_action="<font color=\"red\">Nombre de lignes supprim�es : ".$cpt."</font><br />\n";
			$texte_info_action.="<b>La table matieres_notes est OK.</b><br />\n";
		} else {
			$texte_info_action="<font color=\"green\">Aucune ligne n'a �t� supprim�e.</font><br />\n";
			$texte_info_action.="<b>La table matieres_notes est OK.</b><br />\n";
		}
		echo $texte_info_action;
		update_infos_action_nettoyage($id_info, $texte_info_action);

		//echo "<hr /><h2 align=\"center\">Fin de la v�rification des tables</h2>\n";
		echo "<form name='formulaire' action=\"clean_tables.php\" method=\"post\">\n";
		echo add_token_field();
		echo "<input type=\"hidden\" name=\"maj\" value=\"9\" />\n";
		echo "<input type=\"hidden\" name=\"id_info\" value=\"$id_info\" />\n";
		echo "<input type=\"hidden\" name='mode_auto' value='$mode_auto' />\n";
		echo "<center><input type=\"submit\" name=\"ok\" value=\"Suite de la v�rification\" /></center>\n";
		//echo "<center><b>Attention : l'�tape suivante peut �tre tr�s longue.</b></center>\n";
		echo "</form>\n";

		echo script_suite_submit();
	}

}
elseif ((isset($_POST['maj']) and (($_POST['maj'])=="9")) or (isset($_GET['maj']) and (($_GET['maj'])=="9"))) {
	echo "<p class=bold><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil</a> ";
	echo "| <a href='clean_tables.php'>Retour page V�rification / Nettoyage des tables</a>\n";
	echo "</p>\n";

	echo "<h2 align=\"center\">Etape 9/$total_etapes</h2>\n";

	//echo "<p><a href='index.php'>Retour � Outils de gestion</a> | <a href='index.php'>Retour � V�rification/nettoyage des tables</a></p>\n";

	$temoin_aberrations_groupes=0;

	$table=array('j_signalement', 'j_groupes_classes','j_groupes_matieres','j_groupes_professeurs','j_eleves_groupes');

	if(!isset($_POST['nettoyage_grp'])) {
		$texte_info_action="<h2>Nettoyage des aberrations sur les groupes</h2>\n";
		echo $texte_info_action;
		update_infos_action_nettoyage($id_info, $texte_info_action);

		for($i=0;$i<count($table);$i++){
			$err_no=0;
			$sql="SELECT DISTINCT id_groupe FROM ".$table[$i]." ORDER BY id_groupe";
			$res_grp1=mysql_query($sql);

			if(mysql_num_rows($res_grp1)>0){
				//echo "<p>On parcourt la table '".$table[$i]."'.</p>\n";
				while($ligne=mysql_fetch_array($res_grp1)){
					$sql="SELECT 1=1 FROM groupes WHERE id='".$ligne[0]."'";
					$res_test=mysql_query($sql);

					if(mysql_num_rows($res_test)==0){
						$sql="DELETE FROM $table[$i] WHERE id_groupe='$ligne[0]'";
						$texte_info_action="Suppression d'une r�f�rence � un groupe d'identifiant $ligne[0] dans la table $table[$i] alors que le groupe n'existe pas dans la table 'groupes'.<br />\n";
						echo $texte_info_action;
						update_infos_action_nettoyage($id_info, $texte_info_action);
						//echo "$sql<br />\n";
						$res_suppr=mysql_query($sql);
						$err_no++;
					}
				}
			}
			if($err_no==0){
				$texte_info_action="<b>La table $table[$i] est OK.</b><br />\n";
				echo $texte_info_action;
				update_infos_action_nettoyage($id_info, $texte_info_action);
			}
			else {$temoin_aberrations_groupes++;}
		}
	}

	$texte_info_action="<h2>Nettoyage des erreurs d'appartenance � des groupes</h2>\n";
	echo $texte_info_action;
	//update_infos_action_nettoyage($id_info, $texte_info_action);

	// El�ves dans des groupes pour lesquels ils ne sont pas dans la classe sur la p�riode
	// Mais association classe/groupe OK dans j_groupes_classes
	//===========
	// A FAIRE
	//===========
	/*
	// Probl�me la requ�te est tr�s longue
	mysql> select * from j_eleves_groupes where concat(login,"|",periode) not in (select concat(login,"|",periode) from j_eleves_classes);
	Empty set (3 min 1.34 sec)
	
	mysql>                                
	*/

	if(!isset($_POST['nettoyage_grp'])) {
		// BOUCLE classes... r�cup�rer le nombre de p�riodes... et supprimer ce qui est associ� pour les p�riodes sup�rieures dans j_eleves_classes et j_eleves_groupes... contr�ler avant si il y a des donn�es dans matieres_appreciations, matieres_notes, avis_conseil_classe et cn_cahier_notes

		$texte_info_action="<p class='bold'>Recherche des �l�ves affect�s dans des groupes sur des p�riodes non associ�es � leur classe.</p>\n";
		echo $texte_info_action;
		update_infos_action_nettoyage($id_info, $texte_info_action);

		// BOUCLE sur les classes
		$sql="SELECT id FROM classes ORDER BY classe;";
		//echo "$sql<br />\n";
		$res=mysql_query($sql);
		if(mysql_num_rows($res)==0) {
			$texte_info_action="<p>Aucune classe n'est enregistr�e dans la table 'classes'.</p>\n";
			echo $texte_info_action;
			update_infos_action_nettoyage($id_info, $texte_info_action);
		}
		else {
			$nb_corrections=0;
			$nb_erreurs=0;
			$prof_precedent="";
			while($lig=mysql_fetch_object($res)) {
	
				$texte_info_action="<p><b>Classe ".get_class_from_id($lig->id)."</b><br />\n";

				$sql="SELECT jeg.* FROM j_eleves_groupes jeg, j_groupes_classes jgc WHERE jgc.id_classe='$lig->id' AND jeg.id_groupe=jgc.id_groupe AND jeg.periode NOT IN (SELECT num_periode FROM periodes WHERE id_classe='$lig->id');";
				//echo "$sql<br />\n";
				$res2=mysql_query($sql);
				if(mysql_num_rows($res2)>0) {
					$nb_suppr=0;
					$texte_info_action.=mysql_num_rows($res2)." inscriptions en erreur d'�l�ves dans 'j_eleves_groupes' pour une p�riode non associ�e � la classe ".get_class_from_id($lig->id)."&nbsp;: ";

					while($lig2=mysql_fetch_object($res2)) {
						$sql="SELECT * FROM matieres_notes WHERE login='$lig2->login' AND id_groupe='$lig2->id_groupe' AND periode='$lig2->periode';";
						//echo "$sql<br />\n";
						$res_liste_notes=mysql_query($sql);

						$sql="SELECT * FROM matieres_appreciations WHERE login='$lig2->login' AND id_groupe='$lig2->id_groupe' AND periode='$lig2->periode';";
						//echo "$sql<br />\n";
						$res_liste_appreciations=mysql_query($sql);

						if((mysql_num_rows($res_liste_notes)==0)&&(mysql_num_rows($res_liste_appreciations)==0)){
							$sql="DELETE FROM j_eleves_groupes WHERE id_groupe='$lig2->id_groupe' AND login='$lig2->login' AND periode='$lig2->periode';";
							//echo "$sql<br />\n";
							$resultat_nettoyage_initial=mysql_query($sql);
							if($resultat_nettoyage_initial) {$nb_suppr++;}
						}
						else {
							$texte_info_action.="<br />\n";
							$texte_info_action.="<span style='color:red'>Bulletins non vides pour $lig2->login sur la p�riode $lig2->periode.</span><br />\n";
						}
						//echo "$lig2->id_groupe $lig2->login $lig2->periode<br />\n";
					}
					$texte_info_action.="$nb_suppr suppressions.<br />\n";
				}
				echo $texte_info_action;
				update_infos_action_nettoyage($id_info, $texte_info_action);

			}
		}
	}

	// El�ves dans des groupes pour lesquels l'association classe/groupe n'existe pas dans j_groupes_classes pour leurs classes
	//===========
	// A FAIRE
	//===========


	echo "<form name='formulaire' action=\"clean_tables.php\" name='formulaire' method=\"post\">\n";
	echo add_token_field();
	echo "<input type=\"hidden\" name='mode_auto' value='$mode_auto' />\n";

	if(!isset($_POST['nettoyage_grp'])) {
		$sql="CREATE TABLE tempo2 (
col1 varchar(100) NOT NULL default '',
col2 varchar(100) NOT NULL default ''
);";
		$create_table=mysql_query($sql);

		$sql="TRUNCATE tempo2;";
		$suppr=mysql_query($sql);

		$sql="INSERT INTO tempo2 SELECT DISTINCT login,periode FROM j_eleves_groupes ORDER BY login,periode;";
		$insert=mysql_query($sql);

		echo "<p>Vous allez rechercher les incoh�rences de groupes.</p>\n";

		echo "<input type='hidden' name='maj' value='9' />\n";
		//echo "<input type='submit' name='nettoyage_grp' value='Supprimer' />\n";
		echo "<input type='submit' name='submit_nettoyage_grp' value='Supprimer' />\n";
		echo "<input type='hidden' name='nettoyage_grp' value='y' />\n";

	}
	else {
		echo "<input type='hidden' name='nettoyage_grp' value='y' />\n";

		$sql="SELECT 1=1 FROM tempo2;";
		$res0=mysql_query($sql);
		$nb_assoc_login_periode=mysql_num_rows($res0);
		if($nb_assoc_login_periode>0) {
			echo "<p>$nb_assoc_login_periode association(s) login/p�riode reste(nt) � contr�ler.</p>\n";
		}

		/*
		$err_no=0;
		// On commence par ne r�cup�rer que les login/periode pour ne pas risquer d'oublier d'�l�ves
		// (il peut y avoir des incoh�rences non d�tect�es si on essaye de r�cup�rer davantage d'infos dans un premier temps)
		$sql="SELECT DISTINCT login,periode FROM j_eleves_groupes ORDER BY login,periode;";
		$res_ele=mysql_query($sql);
		$ini="";
		while($lig_ele=mysql_fetch_object($res_ele)){
		*/

		$tranche=20;
		// On commence par ne r�cup�rer que les login/periode pour ne pas risquer d'oublier d'�l�ves
		// (il peut y avoir des incoh�rences non d�tect�es si on essaye de r�cup�rer davantage d'infos dans un premier temps)
		$sql="SELECT col1 AS login,col2 AS periode FROM tempo2 LIMIT $tranche;";
		//echo "$sql<br />\n";
		$res_ele=mysql_query($sql);
		if(mysql_num_rows($res_ele)>0) {

			//$cpt_affichage_info=0;

			$err_no=0;
			// On commence par ne r�cup�rer que les login/periode pour ne pas risquer d'oublier d'�l�ves
			// (il peut y avoir des incoh�rences non d�tect�es si on essaye de r�cup�rer davantage d'infos dans un premier temps)
			//$sql="SELECT DISTINCT login,periode FROM j_eleves_groupes ORDER BY login,periode;";
			//$res_ele=mysql_query($sql);
			$ini="";
			while($lig_ele=mysql_fetch_object($res_ele)) {
				$texte_info_action="";

				if(strtoupper(substr($lig_ele->login,0,1))!=$ini){
					$ini=strtoupper(substr($lig_ele->login,0,1));
					echo "<p>\n<i>Parcours des logins commen�ant par la lettre $ini</i></p>\n";
				}

				// R�cup�ration de la liste des groupes auxquels l'�l�ve est inscrit sur la p�riode en cours d'analyse:
				$sql="SELECT id_groupe FROM j_eleves_groupes WHERE login='$lig_ele->login' AND periode='$lig_ele->periode'";
				//echo "$sql<br />\n";
				$res_jeg=mysql_query($sql);

				if(mysql_num_rows($res_jeg)>0){
					// On v�rifie si l'�l�ve est dans une classe pour cette p�riode:
					$sql="SELECT id_classe FROM j_eleves_classes WHERE login='$lig_ele->login' AND periode='$lig_ele->periode'";
					$res_jec=mysql_query($sql);

					if(mysql_num_rows($res_jec)==0){
						// L'�l�ve n'est dans aucune classe sur la p�riode choisie.
						$texte_info_action.="<p>\n";
						$texte_info_action.="<b>$lig_ele->login</b> n'est dans aucune classe en p�riode <b>$lig_ele->periode</b> et se trouve pourtant dans des groupes.<br />\n";
						$texte_info_action.="Suppression de l'�l�ve du(es) groupe(s) ";
						$cpt_tmp=1;
						while($lig_grp=mysql_fetch_object($res_jeg)){
							$id_groupe=$lig_grp->id_groupe;
							//$tmp_groupe=get_group($id_groupe);
							//$nom_groupe=$tmp_groupe['description'];
							$sql="SELECT description FROM groupes WHERE id='$id_groupe'";
							$res_grp_tmp=mysql_query($sql);
							if(mysql_num_rows($res_grp_tmp)==0){
								$nom_groupe="<font color='red'>GROUPE INEXISTANT</font>\n";
							}
							else{
								$lig_grp_tmp=mysql_fetch_object($res_grp_tmp);
								$nom_groupe=$lig_grp_tmp->description;
							}

							// On va le supprimer du groupe apr�s un dernier test:
							$test1=mysql_query("SELECT 1=1 FROM matieres_notes WHERE (id_groupe = '".$id_groupe."' and login = '".$lig_ele->login."' and periode = '$lig_ele->periode')");
							$nb_test1 = mysql_num_rows($test1);

							$test2=mysql_query("SELECT 1=1 FROM matieres_appreciations WHERE (id_groupe = '".$id_groupe."' and login = '".$lig_ele->login."' and periode = '$lig_ele->periode')");
							$nb_test2 = mysql_num_rows($test2);

							if (($nb_test1 != 0) or ($nb_test2 != 0)) {
								$texte_info_action.="<br /><font color='red'>Impossible de supprimer cette option pour l'�l�ve $lig_ele->login car des moyennes ou appr�ciations ont d�j� �t� rentr�es pour le groupe $nom_groupe pour la p�riode $lig_ele->periode !<br />\nCommencez par supprimer ces donn�es !</font><br />\n";
							} else {
								if($req=mysql_query("DELETE FROM j_eleves_groupes WHERE (login='".$lig_ele->login."' and id_groupe='".$id_groupe."' and periode = '".$lig_ele->periode."')")){
									if($cpt_tmp>1){echo ", ";}
									$texte_info_action.="$nom_groupe (<i>n�$id_groupe</i>)";
									$cpt_tmp++;
								}
							}
						}
					}
					else{
						if(mysql_num_rows($res_jec)==1){
							$lig_clas=mysql_fetch_object($res_jec);
							while($lig_grp=mysql_fetch_object($res_jeg)){
								// On cherche si l'association groupe/classe existe:
								$sql="SELECT 1=1 FROM j_groupes_classes WHERE id_groupe='$lig_grp->id_groupe' AND id_classe='$lig_clas->id_classe'";
								$res_test_grp_clas=mysql_query($sql);

								if(mysql_num_rows($res_test_grp_clas)==0){

									$id_groupe=$lig_grp->id_groupe;
									$tmp_groupe=get_group($id_groupe);
									$nom_groupe=$tmp_groupe['description'];

									$sql="SELECT classe FROM classes WHERE id='$lig_clas->id_classe'";
									$res_tmp=mysql_query($sql);
									$lig_tmp=mysql_fetch_object($res_tmp);
									$clas_tmp=$lig_tmp->classe;

									$sql="SELECT description FROM groupes WHERE id='$lig_grp->id_groupe'";
									$res_tmp=mysql_query($sql);
									$lig_tmp=mysql_fetch_object($res_tmp);
									$grp_tmp=$lig_tmp->description;

									$texte_info_action.="<p>\n";
									$texte_info_action.="<b>$lig_ele->login</b> est inscrit en p�riode $lig_ele->periode dans le groupe <b>$grp_tmp</b> (<i>groupe n�$lig_grp->id_groupe</i>) alors que ce groupe n'est pas associ� � la classe <b>$clas_tmp</b> dans 'j_groupes_classes'.<br />\n";

									$texte_info_action.="Suppression de l'�l�ve du groupe ";
									// On va le supprimer du groupe apr�s un dernier test:
									$test1=mysql_query("SELECT 1=1 FROM matieres_notes WHERE (id_groupe = '".$id_groupe."' and login = '".$lig_ele->login."' and periode = '$lig_ele->periode')");
									$nb_test1 = mysql_num_rows($test1);

									$test2=mysql_query("SELECT 1=1 FROM matieres_appreciations WHERE (id_groupe = '".$id_groupe."' and login = '".$lig_ele->login."' and periode = '$lig_ele->periode')");
									$nb_test2 = mysql_num_rows($test2);

									if (($nb_test1 != 0) or ($nb_test2 != 0)) {
										$texte_info_action.="<br /><font color='red'>Impossible de supprimer cette option pour l'�l�ve $lig_ele->login car des moyennes ou appr�ciations ont d�j� �t� rentr�es pour le groupe $nom_groupe pour la p�riode $lig_ele->periode !<br />\nCommencez par supprimer ces donn�es !</font><br />\n";
									} else {
										if($req=mysql_query("DELETE FROM j_eleves_groupes WHERE (login='".$lig_ele->login."' and id_groupe='".$id_groupe."' and periode = '".$lig_ele->periode."')")){
											$texte_info_action.="$nom_groupe (<i>n�$id_groupe</i>)";
											//$cpt_tmp++;
										}
									}

									$texte_info_action.="</p>\n";
									$err_no++;
								}
							}
						}
						else{
							$texte_info_action.="<p>\n";
							$texte_info_action.="<b>$lig_ele->login</b> est inscrit dans plusieurs classes sur la p�riode $lig_ele->periode:<br />\n";
							while($lig_clas=mysql_fetch_object($res_jec)){
								$sql="SELECT classe FROM classes WHERE id='$lig_clas->id_classe'";
								$res_tmp=mysql_query($sql);
								$lig_tmp=mysql_fetch_object($res_tmp);
								$clas_tmp=$lig_tmp->classe;
								$texte_info_action.="Classe de <a href='../classes/classes_const.php?id_classe=$lig_clas->id_classe&amp;quitter_la_page=y' target='_blank'>$clas_tmp</a> (<i>n�$lig_clas->id_classe</i>)<br />\n";
							}
							$texte_info_action.="Cela ne devrait pas �tre possible.<br />\n";
							$texte_info_action.="Faites le m�nage dans les effectifs des classes ci-dessus.\n";
							$texte_info_action.="</p>\n";
							$err_no++;
						}
					}

					// Cette association login/periode a �t� parcourue:
					$sql="DELETE FROM tempo2 WHERE col1='$lig_ele->login' AND col2='$lig_ele->periode';";
					//echo "$sql<br />\n";
					$nettoyage=mysql_query($sql);
				}

				echo $texte_info_action;
				if($texte_info_action!="") {update_infos_action_nettoyage($id_info, $texte_info_action);}

				// Pour envoyer ce qui a �t� �crit vers l'�cran sans attendre la fin de la page...
				flush();
			}
			if($err_no==0){
				$texte_info_action="<p>Aucune erreur d'affectation dans des groupes/classes n'a �t� d�tect�e.</p>\n";
			}
			else{
				$texte_info_action="<p>Une ou des erreurs ont �t� relev�es.";
				$texte_info_action.="</p>\n";
				update_infos_action_nettoyage($id_info, $texte_info_action);
			}
			echo $texte_info_action;
			//update_infos_action_nettoyage($id_info, $texte_info_action);

			echo "<input type='hidden' name='maj' value='9' />\n";
			echo "<input type='submit' name='suite' value='Poursuivre le nettoyage des groupes' />\n";

			//if(($err_no==0)&&($temoin_aberrations_groupes==0)) {
			if(($err_no==0)&&($temoin_aberrations_groupes==0)&&($mode_auto=='n')) {
				echo "<script type='text/javascript'>
	setTimeout(\"document.forms['formulaire'].submit();\",3000);
</script>\n";
			}

		}
		else {
			$texte_info_action="<p>V�rification des groupes termin�e.</p>\n";
			echo $texte_info_action;
			update_infos_action_nettoyage($id_info, $texte_info_action);

			echo "<input type=\"hidden\" name=\"maj\" value=\"10\" />\n";
			echo "<center><input type=\"submit\" name=\"ok\" value=\"Suite du nettoyage\" /></center>\n";
		}
	}

	echo "<input type=\"hidden\" name=\"id_info\" value=\"$id_info\" />\n";
	echo "</form>\n";
	echo script_suite_submit();

}
elseif ((isset($_POST['maj']) and (($_POST['maj'])=="10")) or (isset($_GET['maj']) and (($_GET['maj'])=="10"))) {
	echo "<p class=bold><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil</a> ";
	echo "| <a href='clean_tables.php'>Retour page V�rification / Nettoyage des tables</a>\n";
	echo "</p>\n";

	echo "<h2 align=\"center\">Etape 10/$total_etapes</h2>\n";

	$texte_info_action="<h2>Nettoyage des comptes �l�ves/responsables</h2>\n";
	echo $texte_info_action;
	update_infos_action_nettoyage($id_info, $texte_info_action);

	echo "<form action=\"clean_tables.php\" name='formulaire' method=\"post\">\n";
	echo add_token_field();
	echo "<input type=\"hidden\" name='mode_auto' value='$mode_auto' />\n";

	if(!isset($_POST['nettoyage_comptes_ele_resp'])) {
		$sql="CREATE TABLE tempo2 (
col1 varchar(100) NOT NULL default '',
col2 varchar(100) NOT NULL default ''
);";
		$create_table=mysql_query($sql);

		$sql="TRUNCATE tempo2;";
		$suppr=mysql_query($sql);

		$sql="INSERT INTO tempo2 SELECT login,statut FROM utilisateurs WHERE statut='eleve' OR statut='responsable';";
		$insert=mysql_query($sql);

		echo "<p>Vous allez supprimer les comptes d'�l�ves ayant quitt� l'�tablissement et de responsables n'ayant plus d'enfant scolaris� dans l'�tablissement.</p>\n";

		echo "<input type='hidden' name='maj' value='10' />\n";
		echo "<input type='submit' name='nettoyage_ele_resp' value='Supprimer' />\n";

	}
	else {
		$cpt_suppr=isset($_POST['cpt_suppr']) ? $_POST['cpt_suppr'] : 0;

		$cpt_suppr_etape=0;

		$sql="SELECT 1=1 FROM tempo2;";
		$res0=mysql_query($sql);
		$nb_comptes=mysql_num_rows($res0);
		if($nb_comptes>0) {echo "<p>$nb_comptes comptes reste(nt) � contr�ler.</p>\n";}

		$tranche=20;
		$sql="SELECT * FROM tempo2 LIMIT $tranche;";
		//echo "$sql<br />\n";
		$res1=mysql_query($sql);
		if(mysql_num_rows($res1)>0) {

			$cpt_affichage_info=0;

			$texte_info_action="";

			while($lig1=mysql_fetch_object($res1)) {
				if($lig1->col2=='eleve') {
					$sql="SELECT 1=1 FROM eleves WHERE login='$lig1->col1';";
					//echo "$sql<br />\n";
					$res2=mysql_query($sql);
					if(mysql_num_rows($res2)==0) {
						if($cpt_affichage_info==0) {$texte_info_action.="<p>";}

						$texte_info_action.="L'�l�ve $lig1->col1 est absent de la table 'eleves', son compte utilisateur doit �tre supprim�.<br />\n";
						$sql="DELETE FROM utilisateurs WHERE login='$lig1->col1';";
						//echo "$sql<br />\n";
						$suppr=mysql_query($sql);

						$cpt_suppr_etape++;

						$cpt_affichage_info++;
					}
				}
				else {
					$sql="SELECT rp.pers_id FROM resp_pers rp WHERE rp.login='$lig1->col1';";
					$res2=mysql_query($sql);
					if(mysql_num_rows($res2)==0) {
						if($cpt_affichage_info==0) {$texte_info_action.="<p>";}
						$texte_info_action.="Le responsable $lig1->col1 est absent de la table 'resp_pers', son compte utilisateur doit �tre supprim�.<br />\n";
						$sql="DELETE FROM utilisateurs WHERE login='$lig1->col1';";
						//echo "$sql<br />\n";
						$suppr=mysql_query($sql);

						$cpt_suppr_etape++;
						$cpt_affichage_info++;
					}
					else {
						$sql="SELECT 1=1 FROM eleves e, resp_pers rp, responsables2 r WHERE rp.login='$lig1->col1' AND r.pers_id=rp.pers_id AND e.ele_id=r.ele_id;";
						//echo "$sql<br />\n";
						$res2=mysql_query($sql);
						if(mysql_num_rows($res2)==0) {
							if($cpt_affichage_info==0) {$texte_info_action.="<p>";}
							$texte_info_action.="Le responsable $lig1->col1 n'est pas associ� � un �l�ve; \n";
							$sql="SELECT pers_id FROM resp_pers WHERE login='$lig1->col1';";
							//echo "$sql<br />\n";
							$res3=mysql_query($sql);
							if(mysql_num_rows($res3)>0) {
								$lig3=mysql_fetch_object($res3);
								$texte_info_action.="suppression des �ventuelles associations fantomes dans 'responsables2'.<br />\n";
								$sql="DELETE FROM responsables2 WHERE pers_id='$lig3->pers_id';";
								//echo "$sql<br />\n";
								$suppr=mysql_query($sql);
								$cpt_affichage_info++;
							}

							if($cpt_affichage_info==0) {$texte_info_action.="<p>";}
							$texte_info_action.="Suppression du responsable $lig1->col1 dans 'resp_pers'.<br />\n";
							$sql="DELETE FROM resp_pers WHERE login='$lig1->col1';";
							//echo "$sql<br />\n";
							$suppr=mysql_query($sql);

							$texte_info_action.="Suppression du responsable $lig1->col1 dans 'utilisateurs'.<br />\n";
							$sql="DELETE FROM utilisateurs WHERE login='$lig1->col1';";
							//echo "$sql<br />\n";
							$suppr=mysql_query($sql);

							$cpt_suppr_etape++;
							$cpt_affichage_info++;
						}
					}
				}

				$sql="DELETE FROM tempo2 WHERE col1='$lig1->col1';";
				//echo "$sql<br />\n";
				$suppr=mysql_query($sql);

				//if($cpt_affichage_info==0) {echo "<p style='color:green; font-size:xx-small;'>Compte $lig1->col1 conserv�.</p>";}
			}


			if($cpt_suppr_etape==0) {
				echo "<p>Aucun compte n'a �t� supprim� � cette �tape.</p>\n";
			}
			elseif($cpt_suppr_etape==1) {
				echo "<p>Un compte a �t� supprim� � cette �tape.</p>\n";
			}
			else {
				$texte_info_action.="<p>$cpt_suppr_etape comptes ont �t� supprim�s � cette �tape.</p>\n";

				echo $texte_info_action;
				update_infos_action_nettoyage($id_info, $texte_info_action);
			}

			$cpt_suppr+=$cpt_suppr_etape;

			echo "<input type='hidden' name='cpt_suppr' value='$cpt_suppr' />\n";
			echo "<input type='submit' name='suite' value='Poursuivre' />\n";

			echo "<input type='hidden' name='maj' value='10' />\n";

			//if($cpt_suppr_etape==0) {
			if(($cpt_suppr_etape==0)&&($mode_auto=='n')) {
				echo "<script type='text/javascript'>
	setTimeout(\"document.forms['formulaire'].submit();\",3000);
</script>\n";
			}
		}
		else {
			$texte_info_action="<p>Nettoyage des comptes d'�l�ves ayant quitt� l'�tablissement et de responsables n'ayant plus d'enfant scolaris� dans l'�tablissement termin�.</p>\n";

			if($cpt_suppr==0) {
				$texte_info_action.="<p>Aucun compte n'a �t� supprim�.</p>\n";
			}
			elseif($cpt_suppr==1) {
				$texte_info_action.="<p>Un compte a �t� supprim�.</p>\n";
			}
			else {
				$texte_info_action.="<p>$cpt_suppr comptes ont �t� supprim�s.</p>\n";
			}

			echo $texte_info_action;
			update_infos_action_nettoyage($id_info, $texte_info_action);

			//echo "<hr />\n";
			//echo "<h2 align=\"center\">Fin de la v�rification des tables</h2>\n";

			echo "<input type='hidden' name='maj' value='11' />\n";
			echo "<input type='submit' name='suite' value='Poursuivre' />\n";

		}
	}

	//echo "<input type='hidden' name='maj' value='10' />\n";
	echo "<input type='hidden' name='nettoyage_comptes_ele_resp' value='y' />\n";
	echo "<input type=\"hidden\" name=\"id_info\" value=\"$id_info\" />\n";

	echo "</form>\n";

	echo script_suite_submit();

}
elseif ((isset($_POST['maj']) and (($_POST['maj'])=="11")) or (isset($_GET['maj']) and (($_GET['maj'])=="11"))) {
	echo "<p class=bold><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil</a> ";
	echo "| <a href='clean_tables.php'>Retour page V�rification / Nettoyage des tables</a>\n";
	echo "</p>\n";

	echo "<h2 align=\"center\">Etape 11/$total_etapes</h2>\n";

	$texte_info_action="<h2>Nettoyage des mod�les de grilles PDF</h2>\n";

	$sql="SELECT 1=1 FROM modeles_grilles_pdf WHERE login NOT IN (SELECT login FROM utilisateurs);";
	$test=mysql_query($sql);
	$nb_scories=mysql_num_rows($test);
	if($nb_scories==0) {
		$texte_info_action.="<p>Toutes les grilles sont associ�es � des utilisateurs existants.</p>\n";
	}
	else {
		$texte_info_action.="<p>$nb_scories grille(s) ne sont associ�es � aucun utilisateurs existants&nbsp;: ";

		$sql="DELETE FROM modeles_grilles_pdf WHERE login NOT IN (SELECT login FROM utilisateurs);";
		$del=mysql_query($sql);
		if($del) {$texte_info_action.="<span style='color:green'>Nettoy�es</span>";}
		else {$texte_info_action.="<span style='color:red'>Echec du nettoyage</span>";}
		$texte_info_action.="</p>\n";
	}

	$sql="SELECT 1=1 FROM modeles_grilles_pdf WHERE id_modele NOT IN (SELECT id_modele FROM modeles_grilles_pdf_valeurs);";
	$test=mysql_query($sql);
	$nb_scories=mysql_num_rows($test);
	if($nb_scories==0) {
		$texte_info_action.="<p>Toutes les grilles sont associ�es � des param�tres de grilles.</p>\n";
	}
	else {
		$texte_info_action.="<p>$nb_scories grille(s) ne sont associ�es � aucun param�tre de grille&nbsp;: ";

		$sql="DELETE FROM modeles_grilles_pdf WHERE id_modele NOT IN (SELECT id_modele FROM modeles_grilles_pdf_valeurs);";
		$del=mysql_query($sql);
		if($del) {$texte_info_action.="<span style='color:green'>Nettoy�es</span>";}
		else {$texte_info_action.="<span style='color:red'>Echec du nettoyage</span>";}
		$texte_info_action.="</p>\n";
	}

	$sql="SELECT 1=1 FROM modeles_grilles_pdf_valeurs WHERE id_modele NOT IN (SELECT id_modele FROM modeles_grilles_pdf);";
	$test=mysql_query($sql);
	$nb_scories=mysql_num_rows($test);
	if($nb_scories==0) {
		$texte_info_action.="<p>Tous les param�tres de grilles sont associ�s � des grilles existantes.</p>\n";
	}
	else {
		$texte_info_action.="<p>$nb_scories param�tres de grilles ne sont associ�es � aucune grille&nbsp;: ";

		$sql="DELETE FROM modeles_grilles_pdf_valeurs WHERE id_modele NOT IN (SELECT id_modele FROM modeles_grilles_pdf);";
		$del=mysql_query($sql);
		if($del) {$texte_info_action.="<span style='color:green'>Nettoy�es</span>";}
		else {$texte_info_action.="<span style='color:red'>Echec du nettoyage</span>";}
		$texte_info_action.="</p>\n";
	}

	echo $texte_info_action;
	update_infos_action_nettoyage($id_info, $texte_info_action);

	//=====================================

	echo "<form action=\"clean_tables.php\" name='formulaire' method=\"post\">\n";
	echo add_token_field();
	echo "<input type=\"hidden\" name='mode_auto' value='$mode_auto' />\n";

	echo "<input type='hidden' name='is_confirmed' value='yes' />\n";
	echo "<input type='hidden' name='maj' value='check_jec_jep_point' />\n";
	echo "<input type=\"hidden\" name=\"id_info\" value=\"$id_info\" />\n";

	echo "<input type='submit' name='suite' value='Poursuivre' />\n";
	echo "</form>\n";

	echo script_suite_submit();

/*
	echo "<hr />\n";
	echo "<h2 align=\"center\">Fin de la v�rification des tables</h2>\n";
*/

}
elseif (isset($_POST['action']) AND $_POST['action'] == 'check_auto_increment') {
	// Le code de St�phane concernant la v�rification des auto_increment apr�s le bug d�tect�
	// concernant les backups r�alis� avec la commande syst�me mysqldump
	echo "<p class=bold><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil</a> ";
	echo "| <a href='clean_tables.php'>Retour page V�rification / Nettoyage des tables</a>\n";
	echo "</p>\n";

	if (isset($_POST['is_confirmed']) and $_POST['is_confirmed'] == "yes") {

		$liste_tab=array("classes","id","`id` smallint(6) unsigned NOT NULL auto_increment",
							"cn_cahier_notes","id_cahier_notes","`id_cahier_notes` int(11) NOT NULL auto_increment",
							"cn_conteneurs","id","`id` int(11) NOT NULL auto_increment",
							"cn_devoirs","id","`id` int(11) NOT NULL auto_increment",
							"commentaires_types","id","`id` INT( 11 ) NOT NULL AUTO_INCREMENT",
							"ct_devoirs_entry","id_ct","`id_ct` int(11) NOT NULL auto_increment",
							"ct_documents","id","`id` int(11) NOT NULL auto_increment",
							"ct_entry","id_ct","`id_ct` int(11) NOT NULL auto_increment",
							"ct_types_documents","id_type","`id_type` bigint(21) NOT NULL auto_increment",
							"messages","id","`id` int(11) NOT NULL auto_increment",
							"suivi_eleve_cpe","id_suivi_eleve_cpe","`id_suivi_eleve_cpe` int(11) NOT NULL auto_increment",
							"absences_eleves","id_absence_eleve","`id_absence_eleve` int(11) NOT NULL auto_increment",
							"edt_creneaux","id_definie_periode","`id_definie_periode` int(11) NOT NULL auto_increment",
							"absences_motifs","id_motif_absence","`id_motif_absence` int(11) NOT NULL auto_increment",
							"groupes","id","`id` int(11) NOT NULL auto_increment",
							"miseajour","id_miseajour","`id_miseajour` int(11) NOT NULL auto_increment",
							"absences_actions","id_absence_action","`id_absence_action` int(11) NOT NULL auto_increment",
							"edt_classes","id_edt_classe","`id_edt_classe` int(11) NOT NULL auto_increment",
							"model_bulletin","id_model_bulletin","`id_model_bulletin` int(11) NOT NULL auto_increment",
							"edt_dates_special","id_edt_date_special","`id_edt_date_special` int(11) NOT NULL auto_increment",
							"edt_semaines","id_edt_semaine","`id_edt_semaine` int(11) NOT NULL auto_increment",
							"etiquettes_formats","id_etiquette_format","`id_etiquette_format` int(11) NOT NULL auto_increment",
							"horaires_etablissement","id_horaire_etablissement","`id_horaire_etablissement` int(11) NOT NULL auto_increment",
							"lettres_cadres","id_lettre_cadre","`id_lettre_cadre` int(11) NOT NULL auto_increment",
							"lettres_suivis","id_lettre_suivi","`id_lettre_suivi` int(11) NOT NULL auto_increment",
							"lettres_tcs","id_lettre_tc","`id_lettre_tc` int(11) NOT NULL auto_increment",
							"lettres_types","id_lettre_type","`id_lettre_type` int(11) NOT NULL auto_increment",
							"vs_alerts_eleves","id_alert_eleve","`id_alert_eleve` int(11) NOT NULL auto_increment",
							"vs_alerts_groupes","id_alert_groupe","`id_alert_groupe` int(11) NOT NULL auto_increment",
							"vs_alerts_types","id_alert_type","`id_alert_type` int(11) NOT NULL auto_increment"
							);

		$temoin_poursuivre_corrections='yes';
		$corrections = array();
		echo "<p>\n";
		for($i=0;$i<count($liste_tab);$i+=3){
			$sql="SHOW TABLES LIKE '$liste_tab[$i]'";
			$test=mysql_num_rows(mysql_query($sql));
			//echo "$test<br />\n";
			if($test>0){
				$sql="show columns from $liste_tab[$i] like '".$liste_tab[$i+1]."';";
				//echo "$sql<br />\n";
				$res=mysql_query($sql);
				if(mysql_num_rows($res)>0){
					unset($lig);
					$lig=mysql_fetch_array($res);

					$temoin="no";
					//echo "<p>\n";
					for($j=0;$j<count($lig);$j++){
						if(isset($lig[$j])){
							//echo "\$lig[$j]=$lig[$j]<br />\n";
							if($lig[$j]=='auto_increment'){$temoin="yes";break;}
						}
					}
					//echo "</p>\n";

					echo "<br />Champ auto_increment de la table '$liste_tab[$i]': ";
					if($temoin=='yes'){
						echo "<font color='green'>OK</font>\n";
					} else {
						echo "<font color='red'>ERREUR (le champ a �t� ajout� � la liste des corrections).</font>\n";
						// On enregistre les infos dans un tableau distinct
						$corrections[] = array($liste_tab[$i], $liste_tab[$i+1], $liste_tab[$i+2]);

						echo "<br /><b>Test d'int�grit� :</b> ";
						$sql="SELECT 1=1 FROM $liste_tab[$i] WHERE ".$liste_tab[$i+1]."='0'";
						$test=mysql_query($sql);
						if(mysql_num_rows($test)==0){
							echo "<font color='blue'>Aucun d�gat ne semble encore fait sur cette table.</font><br />\n";
						} else {
							$temoin_poursuivre_corrections='no';
							echo "<font color='red'>Erreur : des d�g�ts ont d�j� �t� faits sur cette table.</font> Aucune correction de la structure de la base de donn�es n'aura lieu. Vous devez corriger les incoh�rences dans la base de donn�es en recherchant les entr�es ayant pour valeur '0' sur le champ suppos� auto-incr�ment� (champ ".$liste_tab[$i+1].")<br />\n";
						}
					}
				}
			}
		}
		echo "</p>\n";
		// Si aucun d�g�t n'a �t� constat� et qu'on a des tables � corriger, on le fait maintenant
		if ($temoin_poursuivre_corrections=='yes') {
			echo "<h2>Corrections effectives</h2>\n";

			if (empty($corrections)) {
				// Si aucune table n'avait de probl�me, pas besoin de corriger.
				//echo "<br /><p>Aucune table n'a besoin de corrections.</p>\n";
				echo "<p>Aucune table n'a besoin de corrections.</p>\n";
			} else {
				// On proc�de aux corrections
				foreach($corrections as $correct_table) {
					echo "<br />Correction de la table ".$correct_table[0]." : ";
					$sql="ALTER TABLE ".$correct_table[0]." CHANGE ".$correct_table[1]." ".$correct_table[2];
					$res=mysql_query($sql);
					if ($res) {
						// La correction s'est bien pass�e
						echo "<font color='green'>OK</font>\n";
					} else {
						echo "<font color='red'>ERREUR ! </font><br />\n";
						echo "Vous devez v�rifier la cause du dysfonctionnement et faire la correction � la main (le champ � passer en auto_increment est : ".$corrections[1].")<br />\n";
						echo "Vous devriez interdire les connexions ('Gestion g�n�rale/Gestion des connexions/D�sactiver les connexions') et contacter la liste de diffusion des utilisateurs pour prendre conseil.";
					}
				}
			}
		} else {
			// Des erreurs d'int�grit� ont �t� d�tect�es : on ne change rien...
			echo "<p><font color='red'>Aucune correction n'a �t� tent�e.</font> Des probl�mes d'int�grit� des donn�es ont �t� d�tect�s, la proc�dure ne peut pas continuer. Vous devez corriger les probl�mes � la main (recherchez les entr�es ayant la valeur '0' pour le champ suppos� auto-incr�ment�) et relancer cette proc�dure.</p>\n";
		}

	} else {
		echo "<h2>V�rification des champs auto-incr�ment�s</h2>\n";
		echo "<p>La proc�dure suivante v�rifie l'int�grit� de certains champs de la base de donn�es et tente de corriger les erreurs rencontr�es si elles existent.</p>\n";
		echo "<p>Ce script ne doit �tre ex�cut� que si vous avez restaur� sur votre Gepi une sauvegarde r�alis�e avec une version 1.4.4 en utilisant la m�thode 'mysqldump' (et non la m�thode classique de sauvegarde Gepi sans mysqldump).</p>\n";
		echo "<p>La proc�dure d�bute par une s�rie de tests sur les champs devant �tre auto-incr�ment�s. Si aucun probl�me n'est rencontr�, aucune modification n'est faite. Si certains champs n'ont pas l'option d'auto-incr�mentation, des tests sont faits sur l'int�grit� des donn�es. Si aucun probl�me d'int�grit� des donn�es n'a �t� d�tect�, la proc�dure corrigera les champs n�cessaires. Sinon, un message d'erreur sera affich� et aucune modification ne sera effectu�e sur la base de donn�es.</p>\n";
		echo "<p>Si vous �tes s�r de vouloir continuer, cliquez sur le bouton ci-dessous.</p>\n";
		echo "<form name='formulaire' action=\"clean_tables.php\" method=\"post\">\n";
		echo add_token_field();
		echo "<center><input type=submit value='Lancer la proc�dure' /></center>\n";
		echo "<input type='hidden' name='action' value='check_auto_increment' />\n";
		echo "<input type='hidden' name='is_confirmed' value='yes' />\n";
		//echo "<input type=\"hidden\" name=\"id_info\" value=\"$id_info\" />\n";
		echo "</form>\n";
	}

} elseif ((isset($_POST['action']) AND $_POST['action'] == 'check_jec_jep_point')||((isset($_POST['maj']))&&($_POST['maj']=='check_jec_jep_point'))||((isset($_GET['maj']))&&($_GET['maj']=='check_jec_jep_point'))) {
	echo "<p class=bold><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil</a> ";
	echo "| <a href='clean_tables.php'>Retour page V�rification / Nettoyage des tables</a>\n";
	echo "</p>\n";

	$gepi_prof_suivi=getSettingValue("gepi_prof_suivi");

	if (isset($_POST['is_confirmed']) and $_POST['is_confirmed'] == "yes") {
		if($_POST['maj']=='check_jec_jep_point') {
			$texte_info_action="<h2 align=\"center\">Etape 12/$total_etapes<br />V�rification des tables 'j_eleves_cpe' et 'j_eleves_professeurs'</h2>\n";
		}
		else {
			$texte_info_action="<h2>V�rification des tables 'j_eleves_cpe' et 'j_eleves_professeurs'</h2>\n";
		}
		echo $texte_info_action;
		update_infos_action_nettoyage($id_info, $texte_info_action);

		// Initialisation pour test
		$texte_info_action="";

		// Les champs vides pouvaient apparaitre avec le bug (d�sormais corrig�) sur les POINTS et TIRETS dans les noms de login.
		$sql="SELECT * FROM j_eleves_cpe WHERE cpe_login='' OR e_login='';";
		$test=mysql_query($sql);
		$nb_pb_cpe=mysql_num_rows($test);
		if($nb_pb_cpe>0){
			$sql="DELETE FROM j_eleves_cpe WHERE cpe_login='' OR e_login='';";
			$nettoyage=mysql_query($sql);

			if($nettoyage){
				$texte_info_action="<p>$nb_pb_cpe erreur(s) nettoy�e(s) dans la table 'j_eleves_cpe'.</p>\n";
				echo $texte_info_action;
				update_infos_action_nettoyage($id_info, $texte_info_action);
			}
			else{
				$texte_info_action="<p style='color:red;'>Erreur lors du nettoyage de la table 'j_eleves_cpe'.</p>\n";
				echo $texte_info_action;
				update_infos_action_nettoyage($id_info, $texte_info_action);
			}
		}

		// Probl�me de suppression de l'association eleve/cpe apr�s suppression d'un �l�ve de toutes les p�riodes... (plus dans aucune classe)
		$sql="SELECT jecpe.e_login FROM j_eleves_cpe jecpe LEFT JOIN j_eleves_classes jec ON jecpe.e_login=jec.login WHERE jec.login is NULL;";
		$test=mysql_query($sql);
		$nb_pb_cpe=mysql_num_rows($test);
		if($nb_pb_cpe>0){
			$texte_info_action="<p>Suppression d'associations CPE/El�ve pour un ou des �l�ves qui ne sont affect�s dans aucune classe: ";
			$cpt_ele_cpe=0;
			while($lig=mysql_fetch_object($test)){
				if($cpt_ele_cpe>0){echo ", ";}
				$sql="SELECT e.nom,e.prenom FROM eleves e WHERE login='$lig->e_login';";
				//echo "<!-- $sql -->\n";
				$info=mysql_query($sql);
				if(mysql_num_rows($info)>0) {
					$lig2=mysql_fetch_object($info);
					$eleve=ucfirst(strtolower($lig2->prenom))." ".strtoupper($lig2->nom);
				}
				else {
					$eleve=$lig->e_login;
				}
				//echo "<!-- eleve=$eleve -->\n";

				$sql="DELETE FROM j_eleves_cpe WHERE e_login='$lig->e_login';";
				//echo "<!-- $sql -->\n";
				$nettoyage=mysql_query($sql);
				if($nettoyage){
					$texte_info_action.=$eleve;
				}
				else{
					$texte_info_action.="<span style='color:red;'>$eleve</span>\n";;
				}
				$cpt_ele_cpe++;
			}
			$texte_info_action.=".</p>\n";
			echo $texte_info_action;
			update_infos_action_nettoyage($id_info, $texte_info_action);
		}

		$sql="SELECT * FROM j_eleves_professeurs WHERE login='' OR professeur='';";
		$test=mysql_query($sql);
		$nb_pb_pp=mysql_num_rows($test);
		if($nb_pb_pp>0){
			$sql="DELETE FROM j_eleves_professeurs WHERE login='' OR professeur='';";
			$nettoyage=mysql_query($sql);

			if($nettoyage){
				$texte_info_action="<p>$nb_pb_pp erreur(s) nettoy�e(s) dans la table 'j_eleves_professeurs'.</p>\n";;
			}
			else{
				$texte_info_action="<p style='color:red;'>Erreur lors du nettoyage de la table 'j_eleves_professeurs'.</p>\n";
			}
			echo $texte_info_action;
			update_infos_action_nettoyage($id_info, $texte_info_action);
		}

		// Probl�me de suppression de l'association eleve/professeur apr�s suppression d'un �l�ve de toutes les p�riodes... (plus dans aucune classe)
		$sql="SELECT jep.login FROM j_eleves_professeurs jep LEFT JOIN j_eleves_classes jec ON jep.login=jec.login WHERE jec.login is NULL;";
		$test=mysql_query($sql);
		$nb_pb_pp=mysql_num_rows($test);
		if($nb_pb_pp>0){
			$texte_info_action="<p>Suppression d'associations Professeur/El�ve pour un ou des �l�ves qui ne sont affect�s dans aucune classe: ";
			$cpt_ele_pp=0;
			while($lig=mysql_fetch_object($test)){
				if($cpt_ele_pp>0){echo ", ";}
				$sql="SELECT e.nom,e.prenom FROM eleves e WHERE login='$lig->login';";
				$info=mysql_query($sql);
				if(mysql_num_rows($info)>0) {
					$lig2=mysql_fetch_object($info);
					$eleve=ucfirst(strtolower($lig2->prenom))." ".strtoupper($lig2->nom);
				}
				else {
					$eleve=$lig->login;
				}

				$sql="DELETE FROM j_eleves_professeurs WHERE login='$lig->login';";
				$nettoyage=mysql_query($sql);
				if($nettoyage){
					$texte_info_action.=$eleve;
				}
				else{
					$texte_info_action.="<span style='color:red;'>$eleve</span>\n";
				}
				$cpt_ele_pp++;
			}
			$texte_info_action.=".</p>\n";
			echo $texte_info_action;
			update_infos_action_nettoyage($id_info, $texte_info_action);
		}

		if($texte_info_action=="") {
			$texte_info_action.="<p>Aucune erreur n'a �t� trouv�e.</p>\n";
			echo $texte_info_action;
			update_infos_action_nettoyage($id_info, $texte_info_action);
		}

		//=====================================
	
		echo "<form action=\"clean_tables.php\" name='formulaire' method=\"post\">\n";
		echo add_token_field();
		echo "<input type=\"hidden\" name='mode_auto' value='$mode_auto' />\n";
	
		echo "<input type='hidden' name='is_confirmed' value='yes' />\n";
		echo "<input type='hidden' name='maj' value='verif_interclassements' />\n";
		echo "<input type=\"hidden\" name=\"id_info\" value=\"$id_info\" />\n";
	
		echo "<input type='submit' name='suite' value='Poursuivre' />\n";
		echo "</form>\n";
	
		echo script_suite_submit();

		//=====================================

	} else {
		echo "<h2>V�rification des tables 'j_eleves_cpe' et 'j_eleves_professeurs'</h2>\n";

		echo "<p>La proc�dure suivante v�rifie la pr�sence d'enregistrements aberrants dans les tables 'j_eleves_cpe' et 'j_eleves_professeurs'.</p>\n";

		flush();

		$temoin_pb="n";
		$sql="SELECT * FROM j_eleves_cpe WHERE cpe_login='' OR e_login='';";
		$test=mysql_query($sql);
		$nb_pb_cpe=mysql_num_rows($test);
		if($nb_pb_cpe==0){
			echo "<p>Aucun enregistrement dans la table 'j_eleves_cpe' n'a de login �l�ve ou de login CPE vide.</p>\n";
		}
		elseif($nb_pb_cpe==1){
			echo "<p><b>$nb_pb_cpe</b> enregistrement dans la table 'j_eleves_cpe' a un login �l�ve ou un login CPE vide.<br />Cet enregistrement peut perturber la d�signation de CPE responsable.<br />Vous devrier le supprimer.</p>\n";
			$temoin_pb="y";
		}
		else{
			echo "<p><b>$nb_pb_cpe</b> enregistrements dans la table 'j_eleves_cpe' ont un login �l�ve ou un login CPE vide.<br />\n";
			echo "Ces enregistrements peuvent perturber la d�signation de CPE responsable.<br />\n";
			echo "Vous devrier les supprimer.</p>\n";
			$temoin_pb="y";
		}

		flush();

		$sql="SELECT jecpe.e_login FROM j_eleves_cpe jecpe LEFT JOIN j_eleves_classes jec ON jecpe.e_login=jec.login WHERE jec.login is NULL;";
		$test=mysql_query($sql);
		$nb_pb_cpe=mysql_num_rows($test);
		if($nb_pb_cpe==0){
			echo "<p>Aucun enregistrement dans la table 'j_eleves_cpe' n'associe un �l�ve non scolaris� � un CPE.</p>\n";
		}
		elseif($nb_pb_cpe==1){
			echo "<p><b>$nb_pb_cpe</b> enregistrement dans la table 'j_eleves_cpe' associe un �l�ve non scolaris� � un CPE.<br />Cet enregistrement peut perturber la d�signation de CPE responsable.<br />Vous devrier le supprimer.</p>\n";
			$temoin_pb="y";
		}
		else{
			echo "<p><b>$nb_pb_cpe</b> enregistrements dans la table 'j_eleves_cpe' associent des �l�ves non scolaris�s � un ou des CPE.<br />\n";
			echo "Ces enregistrements peuvent perturber la d�signation de CPE responsable.<br />\n";
			echo "Vous devrier les supprimer.</p>\n";
			$temoin_pb="y";
		}

		$sql="SELECT * FROM j_eleves_professeurs WHERE login='' OR professeur='';";
		$test=mysql_query($sql);
		$nb_pb_pp=mysql_num_rows($test);
		if($nb_pb_pp==0){
			echo "<p>Aucun enregistrement dans la table 'j_eleves_professeurs' n'a de login �l�ve ou de login $gepi_prof_suivi vide.</p>\n";
		}
		elseif($nb_pb_pp==1){
			echo "<p><b>$nb_pb_pp</b> enregistrement dans la table 'j_eleves_professeurs' a un login �l�ve ou un login $gepi_prof_suivi vide.<br />\n";
			echo "Cet enregistrement peut perturber la d�signation de $gepi_prof_suivi.<br />\n";
			echo "Vous devrier le supprimer.</p>\n";
			$temoin_pb="y";
		}
		else{
			echo "<p><b>$nb_pb_pp</b> enregistrements dans la table 'j_eleves_professeurs' ont un login �l�ve ou un login $gepi_prof_suivi vide.<br />\n";
			echo "Ces enregistrements peuvent perturber la d�signation de $gepi_prof_suivi.<br />\n";
			echo "Vous devrier les supprimer.</p>\n";
			$temoin_pb="y";
		}

		$sql="SELECT jep.login FROM j_eleves_professeurs jep LEFT JOIN j_eleves_classes jec ON jep.login=jec.login WHERE jec.login is NULL;";
		$test=mysql_query($sql);
		$nb_pb_cpe=mysql_num_rows($test);
		if($nb_pb_cpe==0){
			echo "<p>Aucun enregistrement dans la table 'j_eleves_professeurs' n'associe un �l�ve non scolaris� � un $gepi_prof_suivi.</p>\n";
		}
		elseif($nb_pb_cpe==1){
			echo "<p><b>$nb_pb_cpe</b> enregistrement dans la table 'j_eleves_professeurs' associe un �l�ve non scolaris� � un $gepi_prof_suivi.<br />Cet enregistrement peut perturber la d�signation de $gepi_prof_suivi.<br />Vous devrier le supprimer.</p>\n";
			$temoin_pb="y";
		}
		else{
			echo "<p><b>$nb_pb_cpe</b> enregistrements dans la table 'j_eleves_professeurs' associent des �l�ves non scolaris�s � un ou des $gepi_prof_suivi.<br />\n";
			echo "Ces enregistrements peuvent perturber la d�signation de $gepi_prof_suivi.<br />\n";
			echo "Vous devrier les supprimer.</p>\n";
			$temoin_pb="y";
		}

		if($temoin_pb=="y"){
			echo "<p>Des erreurs ont �t� relev�es.</p>\n";
			echo "<p>Si vous voulez effectuer le nettoyage, cliquez sur le bouton ci-dessous.<br />Vous devriez contr�ler par la suite si toutes vos associations CPE/�l�ve et $gepi_prof_suivi/�l�ve sont bien renseign�es.</p>\n";

			echo "<form name='formulaire' action=\"clean_tables.php\" method=\"post\">\n";
			echo add_token_field();
			echo "<center><input type=submit value='Lancer la proc�dure' /></center>\n";
			echo "<input type='hidden' name='action' value='check_jec_jep_point' />\n";
			echo "<input type='hidden' name='is_confirmed' value='yes' />\n";
			echo "</form>\n";
		}
		else{
			echo "<p>Aucune erreur n'a �t� relev�e.</p>\n";
		}
	}
} elseif (isset($_POST['action']) AND $_POST['action'] == 'clean_edt') {
	echo "<p class=bold><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil</a> ";
	echo "| <a href='clean_tables.php'>Retour page V�rification / Nettoyage des tables</a>\n";
	echo "</p>\n";

	echo "<p><b>Nettoyage des tables EDT&nbsp;:</b> \n";
	$tab_table=array('edt_classes', 'edt_cours', 'edt_calendrier');
	for($i=0;$i<count($tab_table);$i++) {
		if($i>0) {echo ", ";}
		echo $tab_table[$i];
		$sql="TRUNCATE TABLE $tab_table[$i];";
		//echo "$sql<br />\n";
		$suppr=mysql_query($sql);
	}
	echo "</p>\n";

	echo "<p>Termin�.</p>\n";

} elseif (isset($_REQUEST['action']) AND $_REQUEST['action'] == 'clean_absences') {
	echo "<p class=bold>";
	if(isset($_GET['chgt_annee'])) {
		echo "<a href='../gestion/changement_d_annee.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour � la page de Changement d'ann�e</a> ";
	}
	else {
		echo "<a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil</a> ";
		echo "| <a href='clean_tables.php'>Retour page V�rification / Nettoyage des tables</a>\n";
	}
	echo "</p>\n";

	$date_limite=isset($_REQUEST['date_limite']) ? $_REQUEST['date_limite'] : NULL;
	if(isset($date_limite)) {
		$tmp_tab=explode("/",$date_limite);
		$jour=$tmp_tab[0];
		$mois=$tmp_tab[1];
		$annee=$tmp_tab[2];

		if(!checkdate($mois,$jour,$annee)) {
			echo "<p style='color:red;'>La date saisie $date_limite n'est pas valide.</p>\n";
			unset($date_limite);
			require("../lib/footer.inc.php");
			die();
		}
	}

	if(!isset($date_limite)) {
		echo "<p style='color:red;'>Abandon&nbsp;: Aucune date limite n'a �t� saisie.</p>\n";
	}
	else {
		echo "<p><b>Nettoyage des tables absences&nbsp;:</b> \n";

		/*
		$tab_table=array('absences_rb', 'absences_repas', 'absences_eleves');
		for($i=0;$i<count($tab_table);$i++) {
			if($i>0) {echo ", ";}
			echo $tab_table[$i];
			$sql="DELETE FROM $tab_table[$i] WHERE ;";
			$suppr=mysql_query($sql);
		}
		*/

		echo "absences_rb (abs1)";
		$sql="DELETE FROM absences_rb WHERE date_saisie < ".mktime("0","0","0",$mois,$jour,$annee).";";
		//echo "$sql<br />\n";
		$suppr=mysql_query($sql);
		echo ", ";

		echo "absences_eleves (abs1)";
		$sql="DELETE FROM absences_eleves WHERE a_date_absence_eleve < date('$annee-$mois-$jour');";
		//echo "$sql<br />\n";
		$suppr=mysql_query($sql);
		echo ", ";

		echo "absences_repas (abs1)";
		$sql="DELETE FROM absences_repas WHERE a_date_absence_eleve < date('$annee-$mois-$jour');";
		//echo "$sql<br />\n";
		$suppr=mysql_query($sql);
		echo ", ";

		echo "lettres_suivis (abs1)";
		$sql="DELETE FROM lettres_suivis WHERE emis_date_lettre_suivi < date('$annee-$mois-$jour');";
		//echo "$sql<br />\n";
		$suppr=mysql_query($sql);
		echo ", ";

		echo "a_agregation_decompte (abs2)";
		$sql="DELETE FROM a_agregation_decompte WHERE debut_abs < date('$annee-$mois-$jour');";
		//echo "$sql<br />\n";
		$suppr=mysql_query($sql);
		echo ", ";

		echo "a_notifications (abs2)";
		$sql="DELETE FROM a_notifications WHERE created_at < date('$annee-$mois-$jour');";
		//echo "$sql<br />\n";
		$suppr=mysql_query($sql);

		echo "a_saisies (abs2)";
		$sql="DELETE FROM a_saisies WHERE debut_abs < date('$annee-$mois-$jour');";
		//echo "$sql<br />\n";
		$suppr=mysql_query($sql);
		echo ", ";

		echo "a_saisies_version (abs2)";
		$sql="DELETE FROM a_saisies_version WHERE debut_abs < date('$annee-$mois-$jour');";
		//echo "$sql<br />\n";
		$suppr=mysql_query($sql);
		echo ", ";

		echo "a_traitements (abs2)";
		$sql="DELETE FROM a_traitements WHERE created_at < date('$annee-$mois-$jour');";
		//echo "$sql<br />\n";
		$suppr=mysql_query($sql);
		echo ", ";

		echo "</p>\n";

		echo "<p>Termin�.</p>\n";
	}
} elseif (isset($_POST['action']) AND $_POST['action'] == 'clean_cdt') {
	echo "<p class=bold><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil</a> ";
	echo "| <a href='clean_tables.php'>Retour page V�rification / Nettoyage des tables</a>\n";
	echo "</p>\n";

	echo "<p><b>Nettoyage des tables du module Cahier de textes&nbsp;:</b> \n";
	$tab_table=array("ct_devoirs_entry",
					"ct_documents",
					"ct_entry",
					"ct_devoirs_documents",
					"ct_private_entry",
					"ct_sequences");
	for($i=0;$i<count($tab_table);$i++) {
		if($i>0) {echo ", ";}
		echo $tab_table[$i];
		$sql="TRUNCATE TABLE $tab_table[$i];";
		//echo "$sql<br />\n";
		$suppr=mysql_query($sql);
	}
	echo "</p>\n";

	echo "<p>Termin�.</p>\n";
} elseif((isset($_POST['action']) AND $_POST['action'] == 'verif_interclassements')||(isset($_POST['maj']) AND $_POST['maj'] == 'verif_interclassements')||(isset($_GET['maj']) AND $_GET['maj'] == 'verif_interclassements')) {
	echo "<p class=bold><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil</a> ";
	echo "| <a href='clean_tables.php'>Retour page V�rification / Nettoyage des tables</a>\n";
	echo "</p>\n";

	if((isset($_POST['maj']))&&($_POST['maj']=='verif_interclassements')) {
		$texte_info_action="<h2 align=\"center\">Etape 13/$total_etapes<br />V�rification des interclassements</h2>\n";
	}
	else {
		$texte_info_action="<h2>V�rification des interclassements</h2>\n";
	}
	echo $texte_info_action;
	update_infos_action_nettoyage($id_info, $texte_info_action);

	$sql="SHOW TABLES;";
	$res_table=mysql_query($sql);
	if(mysql_num_rows($res_table)==0) {
		$texte_info_action="<p style='color:red;'>Aucune table n'a �t� trouv�e???</p>\n";
	}
	else {
		$texte_info_action="";

		$tab_collations=array();
		$texte_info_action.="<table class='boireaus' summary='Interclassements'>";
		$texte_info_action.="<thead>";
		$texte_info_action.="<tr>";
		$texte_info_action.="<th>Table</th>";
		$texte_info_action.="<th>Champ</th>";
		$texte_info_action.="<th>Type</th>";
		$texte_info_action.="<th>Interclassement</th>";
		$texte_info_action.="</tr>";
		$texte_info_action.="</thead>";
		echo $texte_info_action;
		update_infos_action_nettoyage($id_info, $texte_info_action);
		$alt=1;
		while($tab=mysql_fetch_array($res_table)) {
			$texte_info_action="";

			$alt=$alt*(-1);
			$alt2=$alt;
			//$texte_info_action.="\$tab[0]=$tab[0]<br />\n";
			//$sql="show fields from $tab[0] where type like 'varchar%' or type like 'char%';";
			$sql="show full columns from $tab[0] where type like 'varchar%' or type like 'char%';";
			$res_champs=mysql_query($sql);
			$nb_champs=mysql_num_rows($res_champs);
			$texte_info_action.="<tr class='lig$alt'>";
			$texte_info_action.="<td style='vertical-align:top;'";
			if($nb_champs>0) {
				$texte_info_action.=" rowspan='$nb_champs'";
			}
			$texte_info_action.=">$tab[0]</td>";
			$cpt=0;
			while($lig_champ=mysql_fetch_object($res_champs)) {
				if($cpt>0) {
					$alt2=$alt2*(-1);
					$texte_info_action.="<tr class='lig$alt2'>";
				}
				$texte_info_action.="<td>$lig_champ->Field</td>";
				$texte_info_action.="<td>$lig_champ->Type</td>";
				$texte_info_action.="<td>";
				/*
				$sql="SELECT DISTINCT collation($lig->Field) as c FROM $tab[0];";
				$res_collation=mysql_query($sql);
				if(mysql_num_rows($res_collation)==0) {
					$texte_info_action.="Table vide... d�tection de l'interclassement impossible";
				}
				else {
					while($lig_collation=mysql_fetch_object($res_champs)) {
						$texte_info_action.=$lig_collation->c." ";
					}
				}
				*/
				$texte_info_action.=$lig_champ->Collation;
				if(!in_array($lig_champ->Collation,$tab_collations)) {$tab_collations[]=$lig_champ->Collation;}
				$texte_info_action.="</td>";
				$texte_info_action.="</tr>";
				$cpt++;
			}
			if($cpt==0) {
				$texte_info_action.="<td colspan='3'>Aucun champ VARCHAR ni CHAR</td>";
				$texte_info_action.="</tr>";
			}
			echo $texte_info_action;
			update_infos_action_nettoyage($id_info, $texte_info_action);
			flush();
		}
		$texte_info_action="</table>";

		$nb_collations=count($tab_collations);
		if($nb_collations==1) {
			$texte_info_action.="<p>Un seul interclassement a �t� trouv� dans vos tables.<br />Il n'y a pas de probl�me d'interclassement/collation.</p>\n";
		}
		elseif($nb_collations>1) {
			$texte_info_action.="<p style='color:red;'>$nb_collations interclassements ont �t� trouv�s dans vos tables.<br />Cela peut repr�senter un probl�me si deux interclassements diff�rents sont utilis�s sur une jointure de tables.<br />En cas de doute, signalez sur la liste de diffusion gepi-users les interclassements relev�s (<i>en indiquant sur quels champs cela se produit</i>).</p>\n";
			$texte_info_action.="<p>Voici la liste des interclassements trouv�s&nbsp;: ";
			for($loop=0;$loop<count($tab_collations);$loop++) {
				if($loop>0) {$texte_info_action.=", ";}
				$texte_info_action.="$tab_collations[$loop]";
			}
			$texte_info_action.="</p>";
		}
	}

	$texte_info_action.="<p>Termin�.</p>";
	echo $texte_info_action;
	update_infos_action_nettoyage($id_info, $texte_info_action);

	//=====================================

	echo "<form action=\"clean_tables.php\" name='formulaire' method=\"post\">\n";
	echo add_token_field();
	echo "<input type=\"hidden\" name='mode_auto' value='$mode_auto' />\n";

	echo "<input type='hidden' name='is_confirmed' value='yes' />\n";
	echo "<input type='hidden' name='maj' value='corrige_ordre_matieres_professeurs' />\n";
	echo "<input type=\"hidden\" name=\"id_info\" value=\"$id_info\" />\n";

	echo "<input type='submit' name='suite' value='Poursuivre' />\n";
	echo "</form>\n";

	echo script_suite_submit();

	//=====================================


} elseif ((isset($_POST['action']) AND $_POST['action'] == 'corrige_ordre_matieres_professeurs')||(isset($_POST['maj']) AND $_POST['maj'] == 'corrige_ordre_matieres_professeurs')||(isset($_GET['maj']) AND $_GET['maj'] == 'corrige_ordre_matieres_professeurs')) {
	echo "<p class=bold><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil</a> ";
	echo "| <a href='clean_tables.php'>Retour page V�rification / Nettoyage des tables</a>\n";
	echo "</p>\n";

	if((isset($_POST['maj']))&&($_POST['maj']=='corrige_ordre_matieres_professeurs')) {
		$texte_info_action="<h2 align=\"center\">Etape 14/$total_etapes<br />V�rification de l'ordre des mati�res des professeurs</h2>\n";
	}
	else {
		$texte_info_action="<h2>V�rification de l'ordre des mati�res des professeurs</h2>\n";
	}

	$texte_info_action.="<p><b>Correction de l'ordre de mati�res des professeurs&nbsp;:</b> \n";
	$texte_info_action.="</p>\n";
	echo $texte_info_action;
	update_infos_action_nettoyage($id_info, $texte_info_action);

	$sql="SELECT * FROM j_professeurs_matieres ORDER BY id_professeur, ordre_matieres, id_matiere;";
	//echo "$sql<br />\n";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)==0) {
		$texte_info_action="<p>Aucune association professeur/mati�re n'est enregistr�e dans la table 'j_professeurs_matieres'.</p>\n";
	}
	else {
		$texte_info_action="";

		$nb_corrections=0;
		$nb_erreurs=0;
		$prof_precedent="";
		while($lig=mysql_fetch_object($res)) {
			if($lig->id_professeur!=$prof_precedent) {
				$prof_precedent=$lig->id_professeur;
				$tab_matiere=array();
				$tab_ordre_matieres=array();
				$cpt=1;
			}

			if(in_array($lig->ordre_matieres,$tab_ordre_matieres)) {
				$texte_info_action.="Rang $lig->ordre_matieres de mati�re en doublon pour $lig->id_professeur (<i>$lig->id_matiere</i>)<br />\n";
				$nb_corrections++;
			}
			$tab_ordre_matieres[]=$lig->ordre_matieres;
			$sql="UPDATE j_professeurs_matieres SET ordre_matieres='$cpt' WHERE id_professeur='$lig->id_professeur' AND id_matiere='$lig->id_matiere';";
			$update=mysql_query($sql);
			if(!$update) {$nb_erreurs++;}
			$cpt++;
		}
	}
	$texte_info_action.="<p>$nb_corrections correction(s) effectu�e(s) avec $nb_erreurs erreur(s).</p>";
	$texte_info_action.="<p>Termin�.</p>\n";

	echo $texte_info_action;
	update_infos_action_nettoyage($id_info, $texte_info_action);

	//=====================================

	echo "<form action=\"clean_tables.php\" name='formulaire' method=\"post\">\n";
	echo add_token_field();
	echo "<input type=\"hidden\" name='mode_auto' value='$mode_auto' />\n";

	echo "<input type='hidden' name='is_confirmed' value='yes' />\n";
	echo "<input type='hidden' name='maj' value='controle_categories_matieres' />\n";
	echo "<input type=\"hidden\" name=\"id_info\" value=\"$id_info\" />\n";

	echo "<input type='submit' name='suite' value='Poursuivre' />\n";
	echo "</form>\n";

	echo script_suite_submit();

	//=====================================


} elseif ((isset($_POST['action']) AND $_POST['action'] == 'controle_categories_matieres')||(isset($_POST['maj']) AND $_POST['maj'] == 'controle_categories_matieres')||(isset($_GET['maj']) AND $_GET['maj'] == 'controle_categories_matieres')) {
	echo "<p class=bold><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil</a> ";
	echo "| <a href='clean_tables.php'>Retour page V�rification / Nettoyage des tables</a>\n";
	echo "</p>\n";

	if((isset($_POST['maj']))&&($_POST['maj']=='controle_categories_matieres')) {
		$texte_info_action="<h2 align=\"center\">Etape 15/$total_etapes<br />V�rification des cat�gories de mati�res</h2>\n";
	}
	else {
		$texte_info_action="<h2>V�rification des cat�gories de mati�res</h2>\n";
	}

	$texte_info_action.="<p><b>Contr�ler des cat�gories de mati�res&nbsp;:</b> \n";
	$texte_info_action.="</p>\n";

	$sql="SELECT id, classe FROM classes ORDER BY classe;";
	//echo "$sql<br />\n";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)==0) {
		$texte_info_action.="<p>Aucune classe n'est enregistr�e dans la table 'classes'.</p>\n";
	}
	else {
		$nb_corrections=0;
		$nb_erreurs=0;
		$prof_precedent="";
		while($lig=mysql_fetch_object($res)) {
			// categorie_id=='0' pour la "cat�gorie" Aucune... non pr�sente dans matieres_categories
			$sql="SELECT DISTINCT categorie_id, id_classe FROM j_groupes_classes jgc WHERE id_classe='$lig->id' and categorie_id!='0' AND categorie_id not in (select categorie_id from j_matieres_categories_classes where classe_id='$lig->id');";
			$res2=mysql_query($sql);
			if(mysql_num_rows($res2)>0) {
				while($lig2=mysql_fetch_object($res2)) {

					$sql="SELECT id, nom_court, nom_complet, priority FROM matieres_categories WHERE id='$lig2->categorie_id'";
					$res_cat=mysql_query($sql);
					if(mysql_num_rows($res_cat)==0) {
						$texte_info_action.="<span style='color:red'>La cat�gorie n�$lig2->categorie_id associ�e � la classe n�$lig->id ($lig->classe) n'existe pas dans la table 'matieres_categories'.</span><br />Vous devriez revoir le param�trage des cat�gories.<br />Une solution consiste � forcer le m�me param�trage pour toutes les classes depuis la page de <a href='../matieres/index.php' target='_blank'>Gestion des mati�res</a><br />Sinon, vous pouvez contr�ler et Enregistrer dans la page <a href='../groupes/edit_class.php?id_classe=$lig->id' target='_blank'>Gestion des classes/&lt;$lig->classe&gt;/Enseignements</a> (<i>voir le ou les icones <img src='../images/icons/flag2.gif' width='17' height='18' /></i>).<br />";
						$nb_erreurs++;
					}
					else {
						$lig_cat=mysql_fetch_object($res_cat);
	
						$texte_info_action.="Insertion de l'association de la cat�gorie de mati�re '$lig_cat->nom_court' (<i>'$lig_cat->nom_complet'</i>) avec la classe ".get_class_from_id($lig->id)."&nbsp;: ";
						$sql="INSERT INTO j_matieres_categories_classes SET classe_id='$lig->id', categorie_id='$lig2->categorie_id', priority='$lig_cat->priority', affiche_moyenne='0';";
						$res3=mysql_query($sql);
						if(!$res3) {
							$texte_info_action.="<span style='color:red'>Echec</span>";
							$nb_erreurs++;
						}
						else {
							$texte_info_action.="<span style='color:green'>Succ�s</span>";
							$nb_corrections++;
						}
						$texte_info_action.="<br />\n";
					}
				}
			}
			$cpt++;
		}
	}
	echo $texte_info_action;
	update_infos_action_nettoyage($id_info, $texte_info_action);

	$texte_info_action="";
	$sql="SELECT * FROM matieres_categories WHERE id='0';";
	$test=mysql_query($sql);
	if(mysql_num_rows($test)>0) {
		$lig_cat=mysql_fetch_object($test);
		$texte_info_action.="<p><span style='color:red'>Anomalie&nbsp;:</span> Une cat�gorie de mati�re '$lig_cat->nom_court' (<i>'$lig_cat->nom_complet'</i>) a l'identifiant 0 dans la table 'matieres_categories'.<br />Cet identifiant est r�serv� � la \"cat�gorie\" Aucune qui sert pour les mati�res ne devant �tre dans aucune cat�gorie (<i>une astuce qui permet de ne pas faire apparaitre certains enseignements sur les bulletins (demi-groupes de TP par exemple)</i>).</p>\n";
		$texte_info_action.="<p>Suppression de cette cat�gorie&nbsp;: ";
		$sql="DELETE FROM matieres_categories WHERE id='0';";
		$del=mysql_query($sql);
		if($del) {$texte_info_action.="<span style='color:green'>Succ�s</span>";} else {echo "<span style='color:red'>Echec</span>";}
		$texte_info_action.="</p>";
	}

	$texte_info_action.="<p>$nb_corrections correction(s) effectu�e(s) avec $nb_erreurs erreur(s).</p>";
	$texte_info_action.="<p>Termin�.</p>\n";
	echo $texte_info_action;
	update_infos_action_nettoyage($id_info, $texte_info_action);

	$texte_info_action="<hr />\n";
	$texte_info_action.="<h2 align=\"center\">Fin de la v�rification des tables</h2>\n";
	echo $texte_info_action;
	update_infos_action_nettoyage($id_info, $texte_info_action);

	if($mode_auto=="y") {
		echo "<p><b>Pensez � parcourir le compte-rendu de nettoyage en page d'accueil.</b><br />Il peut s'y trouver des messages et liens concernant des op�rations encore � effectuer, mais requ�rant un choix de votre part (<em>donc non effectu�e automatiquement ici</em>).</p>\n";
	}

} elseif (isset($_POST['action']) AND $_POST['action'] == 'vidage_mod_discipline') {
	echo "<p class=bold><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil</a> ";
	echo "| <a href='clean_tables.php'>Retour page V�rification / Nettoyage des tables</a>\n";
	echo "</p>\n";

	echo "<p><b>Vidage des tables du module Discipline&nbsp;:</b> \n";
	$tab_table=array(//"s_alerte_mail",
		"s_autres_sanctions",
		"s_communication",
		"s_exclusions",
		"s_incidents",
		"s_protagonistes",
		"s_retenues",
		"s_sanctions",
		"s_traitement_incident",
		"s_travail");
	for($i=0;$i<count($tab_table);$i++) {
		if($i>0) {echo ", ";}
		echo $tab_table[$i];
		$sql="TRUNCATE TABLE $tab_table[$i];";
		//echo "$sql<br />\n";
		$suppr=mysql_query($sql);
	}
	echo "</p>\n";

	echo "<p>Termin�.</p>\n";

} elseif (isset($_POST['action']) AND $_POST['action'] == 'nettoyage_mod_discipline') {
	echo "<p class=bold><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil</a> ";
	echo "| <a href='clean_tables.php'>Retour page V�rification / Nettoyage des tables</a>\n";
	echo "</p>\n";

	echo "<p><b>Nettoyage des tables du module Discipline&nbsp;:</b><br />\n";

	$cpt_nettoyage=0;

	//insert into s_traitement_incident set login_ele='titi', id_incident='4';

	$sql="select * from s_traitement_incident str where str.login_ele not in (select login from s_protagonistes spr where spr.id_incident=str.id_incident);";
	$test=mysql_query($sql);
	if(mysql_num_rows($test)>0) {
		echo mysql_num_rows($test)." protagonistes dans un traitement d'incident ne correspondent � aucun protagoniste d'incident&nbsp;: ";

		$nb_err=0;
		$nb_suppr=0;
		while($lig_tmp=mysql_fetch_object($test)) {
			$sql="delete from s_traitement_incident where id_incident='$lig_tmp->id_incident' and login_ele='$lig_tmp->login_ele';";
			$del=mysql_query($sql);
			if($del) {$nb_suppr++;} else {$nb_err++;}
		}

		if($nb_err==0) {
			echo "<span style='color:green'>nettoy�s</span>";
			$cpt_nettoyage+=mysql_num_rows($test);
		}
		else {
			echo "<span style='color:green'>$nb_suppr nettoy�s</span>, <span style='color:red'>$nb_err erreur lors du nettoyage</span>";
		}
		echo "<br />\n";
	}

	//insert into s_sanctions set login='toto', id_incident='4';

	$sql="select * from s_sanctions san where san.login not in (select login from s_protagonistes spr where spr.id_incident=san.id_incident);";
	$test=mysql_query($sql);
	if(mysql_num_rows($test)>0) {
		echo mysql_num_rows($test)." protagonistes dans une sanction ne correspondent � aucun protagoniste d'incident&nbsp;: ";

		$nb_err=0;
		$nb_suppr=0;
		while($lig_tmp=mysql_fetch_object($test)) {
			$sql="delete from s_sanctions where id_incident='$lig_tmp->id_incident' and login='$lig_tmp->login';";
			$del=mysql_query($sql);
			if($del) {$nb_suppr++;} else {$nb_err++;}
		}

		if($nb_err==0) {
			echo "<span style='color:green'>nettoy�s</span>";
			$cpt_nettoyage+=mysql_num_rows($test);
		}
		else {
			echo "<span style='color:green'>$nb_suppr nettoy�s</span>, <span style='color:red'>$nb_err erreur lors du nettoyage</span>";
		}
		echo "<br />\n";
	}

	$sql="select * from s_traitement_incident where id_incident not in (select id_incident from s_incidents);";
	$test=mysql_query($sql);
	if(mysql_num_rows($test)>0) {
		echo mysql_num_rows($test)." traitements ne correspondent � aucun incident&nbsp;: ";
		$sql="delete from s_traitement_incident where id_incident not in (select id_incident from s_incidents);";
		$del=mysql_query($sql);
		if($del) {
			echo "<span style='color:green'>nettoy�s</span>";
			$cpt_nettoyage+=mysql_num_rows($test);
		}
		else {
			echo "<span style='color:red'>erreur lors du nettoyage</span>";
		}
		echo "<br />\n";
	}

	$sql="select * from s_protagonistes where id_incident not in (select id_incident from s_incidents);";
	$test=mysql_query($sql);
	if(mysql_num_rows($test)>0) {
		echo mysql_num_rows($test)." protagonistes ne correspondent � aucun incident&nbsp;: ";
		$sql="delete from s_protagonistes where id_incident not in (select id_incident from s_incidents);";
		$del=mysql_query($sql);
		if($del) {
			echo "<span style='color:green'>nettoy�s</span>";
			$cpt_nettoyage+=mysql_num_rows($test);
		}
		else {
			echo "<span style='color:red'>erreur lors du nettoyage</span>";
		}
		echo "<br />\n";
	}

	$sql="select * from s_sanctions where id_incident not in (select id_incident from s_incidents);";
	$test=mysql_query($sql);
	if(mysql_num_rows($test)>0) {
		echo mysql_num_rows($test)." sanctions ne correspondent � aucun incident&nbsp;: ";
		$sql="delete from s_sanctions where id_incident not in (select id_incident from s_incidents);";
		$del=mysql_query($sql);
		if($del) {
			echo "<span style='color:green'>nettoy�s</span>";
			$cpt_nettoyage+=mysql_num_rows($test);
		}
		else {
			echo "<span style='color:red'>erreur lors du nettoyage</span>";
		}
		echo "<br />\n";
	}

	$tab_sanction=array("s_exclusions","s_retenues","s_travail","s_autres_sanctions");
	$tab_txt_sanction=array("exclusions","retenues","travaux","autres sanctions");
	for($loop=0;$loop<count($tab_sanction);$loop++) {
		$sql="select * from ".$tab_sanction[$loop]." where id_sanction not in (select id_sanction from s_sanctions);";
		$test=mysql_query($sql);
		if(mysql_num_rows($test)>0) {
			echo mysql_num_rows($test)." ".$tab_txt_sanction[$loop]." ne correspondent � aucune sanction&nbsp;: ";
			$sql="delete from s_sanctions where id_incident not in (select id_incident from s_incidents);";
			$del=mysql_query($sql);
			if($del) {
				echo "<span style='color:green'>nettoy�s</span>";
				$cpt_nettoyage+=mysql_num_rows($test);
			}
			else {
				echo "<span style='color:red'>erreur lors du nettoyage</span>";
			}
			echo "<br />\n";
		}
	}

	if($cpt_nettoyage==0) {echo "Aucune scorie n'a �t� trouv�e.";}
	echo "</p>\n";

	echo "<p>Termin�.</p>\n";
} elseif (isset($_POST['action']) AND $_POST['action'] == 'nettoyage_cdt') {
	echo "<p class=bold><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil</a> ";
	echo "| <a href='clean_tables.php'>Retour page V�rification / Nettoyage des tables</a>\n";
	echo "</p>\n";

	echo "<p><b>Nettoyage des tables du cahier de textes&nbsp;:</b><br />\n";

	if(!isset($_POST['confirmer_nettoyage_cdt'])) {
		$cpt_scories=0;

		$sql="select * from ct_entry where id_groupe not in (select id FROM groupes);";
		$test=mysql_query($sql);
		if(mysql_num_rows($test)>0) {
			echo mysql_num_rows($test)." compte-rendu(s) de s�ance(s) pour un ou des groupes n'existant plus a(ont) �t� trouv�(s).<br />\n";

			echo "<table class='boireaus' summary='Tableau des compte-rendus orphelins de groupe'>\n";
			echo "<tr>\n";
			echo "<th>Date</th>\n";
			echo "<th>Professeur</th>\n";
			echo "<th>Contenu</th>\n";
			echo "</tr>\n";
			$alt=1;
			while($lig=mysql_fetch_object($test)) {
				$alt=$alt*(-1);
				echo "<tr class='lig$alt white_hover'>\n";
				echo "<td>".strftime("%d/%m/%Y", $lig->date_ct)."</td>\n";
				echo "<td>".civ_nom_prenom($lig->id_login)."</td>\n";
				echo "<td>".$lig->contenu."</td>\n";
				echo "</tr>\n";
				$cpt_scories++;
			}
			echo "</table>\n";
		}
		else {
			echo "Aucun d�faut d'association � un enseignement n'a �t� trouv� dans 'ct_entry'.<br />\n";
		}

		$sql="select * from ct_documents where id_ct not in (select id_ct FROM ct_entry);";
		$test=mysql_query($sql);
		if(mysql_num_rows($test)>0) {
			echo mysql_num_rows($test)." document(s) joint(s) ne correspond(ent) � aucun compte-rendu existant.<br />\n";
		}

		$sql="select * from ct_devoirs_entry where id_groupe not in (select id FROM groupes);";
		$test=mysql_query($sql);
		if(mysql_num_rows($test)>0) {
			echo mysql_num_rows($test)." notice(s) de devoir(s) pour un ou des groupes n'existant plus a(ont) �t� trouv�(s).<br />\n";

			echo "<table class='boireaus' summary='Tableau des notices de devoirs orphelines de groupe'>\n";
			echo "<tr>\n";
			echo "<th>Date</th>\n";
			echo "<th>Professeur</th>\n";
			echo "<th>Contenu</th>\n";
			echo "</tr>\n";
			$alt=1;
			while($lig=mysql_fetch_object($test)) {
				$alt=$alt*(-1);
				echo "<tr class='lig$alt white_hover'>\n";
				echo "<td>".strftime("%d/%m/%Y", $lig->date_ct)."</td>\n";
				echo "<td>".civ_nom_prenom($lig->id_login)."</td>\n";
				echo "<td>".$lig->contenu."</td>\n";
				echo "</tr>\n";
				$cpt_scories++;
			}
			echo "</table>\n";
		}
		else {
			echo "Aucun d�faut d'association � un enseignement n'a �t� trouv� dans 'ct_devoirs_entry'.<br />\n";
		}

		$sql="select * from ct_devoirs_documents where id_ct_devoir not in (select id_ct FROM ct_devoirs_entry);";
		$test=mysql_query($sql);
		if(mysql_num_rows($test)>0) {
			echo mysql_num_rows($test)." document(s) joint(s) ne correspond(ent) � aucune notice de devoir existante.<br />\n";
		}

		$sql="select * from ct_private_entry where id_groupe not in (select id FROM groupes);";
		$test=mysql_query($sql);
		if(mysql_num_rows($test)>0) {
			echo mysql_num_rows($test)." notice(s) priv�e(s) pour un ou des groupes n'existant plus a(ont) �t� trouv�(s).<br />\n";

			echo "<table class='boireaus' summary='Tableau des notices priv�es orphelines de groupe'>\n";
			echo "<tr>\n";
			echo "<th>Date</th>\n";
			echo "<th>Professeur</th>\n";
			echo "<th>Contenu</th>\n";
			echo "</tr>\n";
			$alt=1;
			while($lig=mysql_fetch_object($test)) {
				$alt=$alt*(-1);
				echo "<tr class='lig$alt white_hover'>\n";
				echo "<td>".strftime("%d/%m/%Y", $lig->date_ct)."</td>\n";
				echo "<td>".civ_nom_prenom($lig->id_login)."</td>\n";
				echo "<td>".$lig->contenu."</td>\n";
				echo "</tr>\n";
				$cpt_scories++;
			}
			echo "</table>\n";
		}
		else {
			echo "Aucun d�faut d'association � un enseignement n'a �t� trouv� dans 'ct_private_entry'.<br />\n";
		}

		$sql="select id from ct_sequences;";
		$res_seq=mysql_query($sql);
		if(mysql_num_rows($res_seq)>0) {
			$tab_seq=array();
			while($lig_seq=mysql_fetch_object($res_seq)) {
				$tab_seq[]=$lig_seq->id;
			}

			$tab_seq2=array();
			$sql="(select id_sequence FROM ct_entry) UNION (select id_sequence FROM ct_devoirs_entry) UNION (select id_sequence FROM ct_private_entry);";
			$res_seq=mysql_query($sql);
			if(mysql_num_rows($res_seq)>0) {
				while($lig_seq=mysql_fetch_object($res_seq)) {
					$tab_seq2[]=$lig_seq->id_sequence;
				}
			}

			for($loop=0;$loop<count($tab_seq);$loop++) {
				if(!in_array($tab_seq[$loop], $tab_seq2)) {
					echo "La s�quence n�".$tab_seq[$loop]." n'est associ�e � aucune notice.<br />\n";
					$cpt_scories++;
				}
			}
		}
		else {
			echo "Aucune s�quence n'est saisie.<br />\n";
		}

		if($cpt_scories>0) {
			echo "<form action=\"clean_tables.php\" method=\"post\">\n";
			echo add_token_field();
			echo "<center><input type=submit value=\"Supprimer les scories trouv�es\" /></center>\n";
			echo "<input type='hidden' name='action' value='nettoyage_cdt' />\n";
			echo "<input type='hidden' name='confirmer_nettoyage_cdt' value='y' />\n";
			echo "</form>\n";
		}
	}
	else {
		$cpt_nettoyage=0;

		$sql="select * from ct_entry where id_groupe not in (select id FROM groupes);";
		$test=mysql_query($sql);
		if(mysql_num_rows($test)>0) {
			echo "Suppression de ".mysql_num_rows($test)." compte-rendu(s) de s�ance(s) pour un ou des groupes n'existant plus&nbsp;: ";

			$sql="DELETE FROM ct_entry where id_groupe not in (select id FROM groupes);";
			$del=mysql_query($sql);
			if($del) {
				echo "<span style='color:green'>OK</span>";
				$cpt_nettoyage+=mysql_num_rows($test);
			}
			else {
				echo "<span style='color:red'>ERREUR</span>";
			}
			echo "<br />\n";
		}
		else {
			echo "Aucune d�faut d'association � un enseignement n'a �t� trouv� dans 'ct_entry'.<br />\n";
		}

		$sql="select * from ct_documents where id_ct not in (select id_ct FROM ct_entry);";
		$test=mysql_query($sql);
		if(mysql_num_rows($test)>0) {
			$nb_err=0;
			echo "Suppression de ".mysql_num_rows($test)." document(s) joint(s) ne correspondant � aucun compte-rendu existant&nbsp;: \n";
			/*
			$sql="DELETE FROM ct_documents where id_groupe not in (select id FROM groupes);";
			$del=mysql_query($sql);
			if($del) {
				echo "<span style='color:green'>OK</span>";
			}
			else {
				echo "<span style='color:red'>ERREUR</span>";
			}
			*/
			while($lig=mysql_fetch_object($test)) {
				if(file_exists($lig->emplacement)) {
					@unlink($lig->emplacement);
				}

				$sql="DELETE FROM ct_documents WHERE id_ct='$lig->id_ct';";
				if(!$del) {
					$nb_err++;
				}
				else {
					$cpt_nettoyage++;
				}
			}

			if($nb_err==0) {
				echo "<span style='color:green'>OK</span>";
			}
			else {
				echo "<span style='color:red'>ERREUR</span>";
			}
			echo "<br />\n";
		}

		$sql="select * from ct_devoirs_entry where id_groupe not in (select id FROM groupes);";
		$test=mysql_query($sql);
		if(mysql_num_rows($test)>0) {
			echo mysql_num_rows($test)." notice(s) de devoir(s) pour un ou des groupes n'existant plus a(ont) �t� trouv�(s).<br />\n";
			echo "Suppression de ".mysql_num_rows($test)." notice(s) de devoir(s) pour un ou des groupes n'existant plus&nbsp;: ";

			$sql="DELETE FROM ct_devoirs_entry where id_groupe not in (select id FROM groupes);";
			$del=mysql_query($sql);
			if($del) {
				echo "<span style='color:green'>OK</span>";
				$cpt_nettoyage+=mysql_num_rows($test);
			}
			else {
				echo "<span style='color:red'>ERREUR</span>";
			}
			echo "<br />\n";
		}
		else {
			echo "Aucune d�faut d'association � un enseignement n'a �t� trouv� dans 'ct_devoirs_entry'.<br />\n";
		}

		$sql="select * from ct_devoirs_documents where id_ct_devoir not in (select id_ct FROM ct_devoirs_entry);";
		$test=mysql_query($sql);
		if(mysql_num_rows($test)>0) {
			$nb_err=0;
			echo "Suppression de ".mysql_num_rows($test)." document(s) joint(s) ne correspondant � aucune notice de devoir existante&nbsp;: \n";
			/*
			$sql="DELETE FROM ct_devoirs_documents where id_groupe not in (select id FROM groupes);";
			$del=mysql_query($sql);
			if($del) {
				echo "<span style='color:green'>OK</span>";
			}
			else {
				echo "<span style='color:red'>ERREUR</span>";
			}
			*/
			while($lig=mysql_fetch_object($test)) {
				if(file_exists($lig->emplacement)) {
					@unlink($lig->emplacement);
				}

				$sql="DELETE FROM ct_devoirs_documents WHERE id_ct='$lig->id_ct';";
				if(!$del) {
					$nb_err++;
				}
				else {
					$cpt_nettoyage++;
				}
			}

			if($nb_err==0) {
				echo "<span style='color:green'>OK</span>";
			}
			else {
				echo "<span style='color:red'>ERREUR</span>";
			}
			echo "<br />\n";
		}

		$sql="select * from ct_private_entry where id_groupe not in (select id FROM groupes);";
		$test=mysql_query($sql);
		if(mysql_num_rows($test)>0) {
			echo "Suppression de ".mysql_num_rows($test)." notice(s) priv�e(s) pour un ou des groupes n'existant plus&nbsp;: ";

			$sql="DELETE FROM ct_private_entry where id_groupe not in (select id FROM groupes);";
			$del=mysql_query($sql);
			if($del) {
				echo "<span style='color:green'>OK</span>";
				$cpt_nettoyage+=mysql_num_rows($test);
			}
			else {
				echo "<span style='color:red'>ERREUR</span>";
			}
			echo "<br />\n";

		}
		else {
			echo "Aucune d�faut d'association � un enseignement n'a �t� trouv� dans 'ct_private_entry'.<br />\n";
		}

		$sql="select id from ct_sequences;";
		$res_seq=mysql_query($sql);
		if(mysql_num_rows($res_seq)>0) {
			$tab_seq=array();
			while($lig_seq=mysql_fetch_object($res_seq)) {
				$tab_seq[]=$lig_seq->id;
			}

			$tab_seq2=array();
			$sql="(select id_sequence FROM ct_entry) UNION (select id_sequence FROM ct_devoirs_entry) UNION (select id_sequence FROM ct_private_entry);";
			$res_seq=mysql_query($sql);
			if(mysql_num_rows($res_seq)>0) {
				while($lig_seq=mysql_fetch_object($res_seq)) {
					$tab_seq2[]=$lig_seq->id_sequence;
				}
			}

			for($loop=0;$loop<count($tab_seq);$loop++) {
				if(!in_array($tab_seq[$loop], $tab_seq2)) {
					echo "Suppression de la s�quence n�".$tab_seq[$loop]." associ�e � aucune notice&nbsp;: ";
					$sql="DELETE FROM ct_sequences where id='".$tab_seq[$loop]."';";
					$del=mysql_query($sql);
					if($del) {
						echo "<span style='color:green'>OK</span>";
						$cpt_nettoyage++;
					}
					else {
						echo "<span style='color:red'>ERREUR</span>";
					}
					echo "<br />\n";
				}
			}
		}
		else {
			echo "Aucune s�quence n'est saisie.<br />\n";
		}
	}

	echo "<p>Termin�.</p>\n";
}
else {
	echo "<p class='bold'><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil</a> ";
	//echo "| <a href='clean_tables.php'>Retour page V�rification / Nettoyage des tables</a></p>\n";
	echo "</p>\n";

	echo "<h2>Sauvegarde pr�alable</h2>\n";
	echo "<div style='margin-left: 3em;'>\n";

		echo "<p>Il est tr�s vivement conseill� de <b>faire une sauvegarde de la base MySql avant de lancer la proc�dure.</b></p>\n";
		echo "<center><form enctype=\"multipart/form-data\" action=\"../gestion/accueil_sauve.php?action=";
		if(getSettingValue('mode_sauvegarde')=='mysqldump') {
			echo "system_dump";
		}
		else {
			echo "dump";
		}
		echo "\" method=post name='formulaire'>\n";
		echo add_token_field();
		echo "<input type=\"submit\" value=\"Lancer une sauvegarde de la base de donn�es\" /></form></center>\n";
		echo "<p>Il est �galement vivement conseill� de <b><a href='../gestion/gestion_connect.php'>d�sactiver les connexions � GEPI</a> durant la phase de nettoyage</b>.</p>
	<p align='center'><b><font size=\"+1\">Attention : selon la taille de la base, cette op�ration peut durer plusieurs heures.</font></b></p>\n";

	echo "</div>\n";

	echo "<hr />\n";
	//====================================================

	echo "<h2>Nettoyages</h2>\n";
	echo "<div style='margin-left: 3em;'>\n";

		echo "<p>Cette proc�dure op�re un <b>nettoyage</b> des lignes inutiles dans les <b>tables de liaison</b> de la base MySql de GEPI et dans les tables des donn�es scolaires des �l�ves (notes, appr�ciations, absences).";
		echo "<br />Les tables de liaison contiennent des informations qui mettent en relation les tables principales de GEPI
	(�l�ves, professeurs, mati�res, classes).<br /><br />
	Du fait de bugs mineurs (�ventuellement d�j� r�gl�s mais pr�sents dans des versions ant�rieures de GEPI) ou de mauvaises manipulations,
	ces tables de liaison peuvent contenir des donn�es obsol�tes ou des doublons qui peuvent nuire � un fonctionnement optimal de GEPI.";
	
	
		echo "<form action=\"clean_tables.php\" method=\"post\">\n";
		echo add_token_field();
		echo "<p><b>Cliquez sur le bouton suivant pour commencer le nettoyage des tables de la base</b></p>\n";
		echo "<p>Cette proc�dure s'effectue en plusieurs �tapes : � chaque �tape, une page affiche le compte-rendu du nettoyage et un <b>bouton situ� en bas de la page</b> vous permet de passer � l'�tape suivante.</p>\n";
	
		echo "<div align='center'>\n";
		echo "<p><input type='checkbox' name='mode_auto' id='mode_auto' value='y' /><label for='mode_auto' style='cursor: pointer;'> Mode automatique : Cochez cette case pour laisser d�rouler les �tapes du nettoyage.<br />Le rapport sera rappel� en page d'accueil.</label>\n";
		echo "<br />\n";
		echo "<input type=submit value='Proc�der au nettoyage des tables' />\n";
		echo "</div>\n";
		echo "<input type=hidden name='maj' value='1' />\n";
		echo "<input type=hidden name='valid' value='$valid' />\n";
		echo "</form>\n";
	
		echo "<p>Ou n'effectuer que certains des nettoyages de la liste&nbsp;:<br />\n";
		echo "<a href='clean_tables.php?maj=1".add_token_in_url()."'>Tables AID, j_eleves_etablissements, j_eleves_regime et j_professeurs_matieres</a><br />\n";
		echo "<a href='clean_tables.php?maj=2".add_token_in_url()."'>Table j_eleves_professeurs</a> (<i>associations �l�ves/".getSettingValue('gepi_prof_suivi')."</i>)<br />\n";
		echo "<a href='clean_tables.php?maj=4".add_token_in_url()."'>Table j_eleves_classes</a> (<i>inscription des �l�ves dans les classes pour chaque p�riode</i>)<br />\n";
		echo "<a href='clean_tables.php?maj=6".add_token_in_url()."'>Tables aid_appreciations et avis_conseil_classe</a><br />\n";
		echo "<a href='clean_tables.php?maj=7".add_token_in_url()."'>Table matieres_appreciations</a> (<i>appr�ciations des �l�ves sur les bulletins</i>)<br />\n";
		echo "<a href='clean_tables.php?maj=8".add_token_in_url()."'>Table matieres_notes</a> (<i>notes des �l�ves sur les bulletins</i>)<br />\n";
		echo "<a href='clean_tables.php?maj=9".add_token_in_url()."'>Tables concernant les groupes</a> (<i>associations �l�ves/enseignements/p�riodes/classes</i>)<br />\n";
		echo "<a href='clean_tables.php?maj=10".add_token_in_url()."'>Tables concernant les comptes �l�ves et responsables</a><br />\n";
		echo "<a href='clean_tables.php?maj=11".add_token_in_url()."'>Tables concernant les grilles PDF.</a><br />\n";
		echo "<a href='clean_tables.php?maj=check_jec_jep_point".add_token_in_url()."'>Contr�le des tables j_eleves_cpe et j_eleves_professeurs.</a><br />\n";
		echo "<a href='clean_tables.php?maj=verif_interclassements".add_token_in_url()."'>V�rification des interclassements (<em>collation,...</em>).</a><br />\n";
		echo "<a href='clean_tables.php?maj=corrige_ordre_matieres_professeurs".add_token_in_url()."'>V�rification de l'ordre des mati�res pour les professeurs.</a><br />\n";
		echo "<a href='clean_tables.php?maj=controle_categories_matieres".add_token_in_url()."'>V�rification des cat�gories de mati�res.</a><br />\n";
		//echo "<span style='color:red'>A DETAILLER...</span>";
		echo "</p>\n";
	
		echo "<hr />\n";
	
		echo "<h2>Nettoyages compl�mentaires</h2>\n";
	
		echo "<p>Il est arriv� que des �l�ves puissent �tre inscrits � des groupes sur des p�riodes o� ils ne sont plus dans la classe (<i>suite � des changements de classes, d�parts,... par exemple</i>).<br />Il en r�sulte des affichages d'erreur non fatales, mais disgracieuses.<br />Le probl�me n'est normalement plus susceptible de revenir, mais dans le cas o� vous auriez des erreurs inexpliqu�es concernant /lib/groupes.inc.php, vous pouvez contr�ler les appartenances aux groupes/classes en visitant la page suivante:</p>\n";
	
		//echo "<p><a href='verif_groupes.php'>Contr�ler les appartenances d'�l�ves � des groupes/classes</a>.</p>\n";
	
		echo "<form action=\"verif_groupes.php\" method=\"post\">\n";
		echo "<center><input type=submit value='Contr�ler les groupes' /></center>\n";
		echo "</form>\n";
	
		echo "<hr />\n";
	
		//echo "<p>Jusqu'� la version 1.4.3-1, GEPI a comport� un bug sur le calcul des moyennes de conteneurs (<i>boites/sous-mati�res</i>).<br />\nSi on d�pla�ait un devoir ou un conteneur vers un autre conteneur, il pouvait se produire une absence de recalcul des moyennes de certains conteneurs.<br />\nLe probl�me est d�sormais corrig�, mais dans le cas o� vos moyennes ne sembleraient pas correctes, vous pouvez provoquer le recalcul des moyennes de l'ensemble des conteneurs pour l'ensemble des groupes/mati�res.<br />\nLes modifications effectu�es seront affich�es.</p>\n";
		echo "<p>Jusqu'� la version 1.4.3-1, GEPI a comport� un bug sur le calcul des moyennes de conteneurs (<i>".getSettingValue("gepi_denom_boite")."s</i>).<br />\nSi on d�pla�ait un devoir ou un conteneur vers un autre conteneur, il pouvait se produire une absence de recalcul des moyennes de certains conteneurs.<br />\nLe probl�me est d�sormais corrig�, mais dans le cas o� vos moyennes ne sembleraient pas correctes, vous pouvez provoquer le recalcul des moyennes de l'ensemble des conteneurs pour l'ensemble des groupes/mati�res.<br />\nLes modifications effectu�es seront affich�es.</p>\n";
	
		echo "<form action=\"recalcul_moy_conteneurs.php\" method=\"post\">\n";
		echo "<center><input type=submit value='Recalculer les moyennes de conteneurs' /></center>\n";
		echo "</form>\n";
	
		echo "<hr />\n";
	
		echo "<p>La proc�dure de sauvegarde avec la commande 'mysqldump' dans la version 1.4.4-stable contenait un bug aboutissant � la perte de la fonction auto_increment sur certains champs, ce qui peut aboutir tr�s rapidement � des incoh�rences dans la base de donn�es.</p>\n";
		echo "<p>Si vous avez restaur� une sauvegarde g�n�r�e avec la m�thode mysqldump, vous devez absolument lancer cette v�rification le plus rapidement possible et corriger les erreurs si le script ne peut les corriger automatiquement.</p>\n";
		echo "<p><b>Il est vivement recommand� de faire une sauvegarde de la base avant d'effectuer cette op�ration !</b></p>\n";
		echo "<form action=\"clean_tables.php\" method=\"post\">\n";
		echo add_token_field();
		echo "<center><input type=submit value='Contr�ler les champs auto-incr�ment�s' /></center>\n";
		echo "<input type='hidden' name='action' value='check_auto_increment' />\n";
		echo "</form>\n";
	
		echo "<hr />\n";
	
		echo "<p>Gepi a un temps contenu un bug sur le format des login.<br />La pr�sence de 'point' dans un nom de login par exemple pouvait provoquer des dysfonctionnements.<br />Le contenu des tables 'j_eleves_cpe' et 'j_eleves_professeurs' pouvait �tre affect�.</p>\n";
		echo "<p><b>Il est vivement recommand� de faire une sauvegarde de la base avant d'effectuer cette op�ration !</b></p>\n";
		echo "<form action=\"clean_tables.php\" method=\"post\">\n";
		echo add_token_field();
		echo "<center><input type=submit value=\"Contr�ler les tables 'j_eleves_cpe' et 'j_eleves_professeurs'\" /></center>\n";
		echo "<input type='hidden' name='action' value='check_jec_jep_point' />\n";
		echo "</form>\n";
	
		echo "<hr />\n";
	
		echo "<p>V�rification de l'Emploi du temps.</p>\n";
		echo "Pour v�rifier votre emploi du temps en cas d'anomalies, suivez ce lien&nbsp;: <a href='../edt_organisation/verifier_edt.php?a=a".add_token_in_url()."'>V�rification de l'Emploi du temps</a></p>\n";

		echo "<hr />\n";

		echo "<p>Contr�le de l'interclassement (<i>COLLATION</i>) des champs des tables.<br />Des interclassements diff�rents sur des champs de deux tables intervenant dans une jointure peut provoquer des erreurs.<br />Un tel probl�me peut survenir avec des bases transf�r�es d'une machine � une autre,...</p>\n";
		echo "<form action=\"clean_tables.php\" method=\"post\">\n";
		echo add_token_field();
		echo "<center>\n";
		echo "<input type=submit value=\"Contr�ler les interclassements\" />\n";
		echo "<input type='hidden' name='action' value='verif_interclassements' />\n";
		echo "</form>\n";
	
		echo "<hr />\n";
	
		echo "<p>Contr�le des ordres de mati�res pour les professeurs.<br />Si les ordres de mati�res ne sont pas correctement renseign�s dans la table j_professeurs_matieres (<i>ordre_matieres tous � z�ro par exemple</i>), il n'est pas possible de choisir la mati�re principale d'un professeur.</p>\n";
		echo "<form action=\"clean_tables.php\" method=\"post\">\n";
		echo add_token_field();
		echo "<center>\n";
		echo "<input type=submit value=\"Corriger les ordres de mati�res des professeurs\" />\n";
		echo "<input type='hidden' name='action' value='corrige_ordre_matieres_professeurs' />\n";
		echo "</form>\n";
	
		echo "<hr />\n";
	
		echo "<p>Contr�le cat�gories de mati�res.<br />Si les vous n'obtenez aucune mati�re dans les relev�s de notes quand les Cat�gories de mati�res sont coch�es dans 'Gestion des bases/Gestion des classes/&lt;Une_classe&gt; Param�tres', les informations de la table 'j_matieres_categories_classes' sont probablement incompl�tes.</p>\n";
		echo "<form action=\"clean_tables.php\" method=\"post\">\n";
		echo add_token_field();
		echo "<center>\n";
		echo "<input type=submit value=\"Contr�ler les cat�gories de mati�res\" />\n";
		echo "<input type='hidden' name='action' value='controle_categories_matieres' />\n";
		echo "</form>\n";

		echo "<hr />\n";
	
		echo "<p>Nettoyage de scories dans le module Discipline.</p>\n";
		echo "<form action=\"clean_tables.php\" method=\"post\">\n";
		echo add_token_field();
		echo "<center>\n";
		echo "<input type=submit value=\"Nettoyage des tables du module Discipline\" />\n";
		echo "<input type='hidden' name='action' value='nettoyage_mod_discipline' />\n";
		echo "</form>\n";

		echo "<hr />\n";
	
		echo "<p>Nettoyage de scories dans les cahiers de textes.</p>\n";
		echo "<form action=\"clean_tables.php\" method=\"post\">\n";
		echo add_token_field();
		echo "<center>\n";
		echo "<input type=submit value=\"Nettoyage des tables du cahier de textes\" />\n";
		echo "<input type='hidden' name='action' value='nettoyage_cdt' />\n";
		echo "</form>\n";
	

	echo "</div>\n";

	echo "<hr />\n";

	//====================================================

	echo "<h2>Nettoyage par le vide;) au changement d'ann�e</h2>\n";
	echo "<div style='margin-left: 3em;'>\n";

		echo "<p>Au changement d'ann�e, il est recommand� de vider les entr�es des tables 'edt_classes', 'edt_cours', 'edt_calendrier' du module emploi du temps de Gepi.</p>\n";
		echo "<form action=\"clean_tables.php\" method=\"post\">\n";
		echo add_token_field();
		echo "<center><input type=submit value=\"Vider les tables Emploi du temps\" /></center>\n";
		echo "<input type='hidden' name='action' value='clean_edt' />\n";
		echo "<p><i>NOTE&nbsp;:</i> Prenez soin de faire une <a href='../gestion/accueil_sauve.php'>sauvegarde de la base</a> et un <a href='../mod_annees_anterieures/index.php'>archivage des donn�es ant�rieures</a> avant le changement d'ann�e.</p>\n";
		echo "</form>\n";
	
		echo "<hr />\n";
	
		echo "<p>Au changement d'ann�e, il est recommand� de vider les entr�es des tables 'absences_rb', 'absences_repas' et 'absences_eleves' du module abs1 de Gepi, ainsi que les tables a_agregation_decompte, a_notifications, a_saisies, a_saisies_version, a_traitements du module abs2 : </p>\n";
		echo "<form action=\"clean_tables.php\" method=\"post\">\n";
		echo add_token_field();
		echo "<center>\n";
		echo "<input type=submit value=\"Vider les tables enregistrements du module absences\" />\n";
		$annee=strftime("%Y");
		$mois=strftime("%m");
		if($mois<=7) {$annee--;}
		echo "pour les absences ant�rieures au <input type='text' name='date_limite' size='10' value='31/07/$annee' />\n";
		echo "</center>\n";
		echo "<input type='hidden' name='action' value='clean_absences' />\n";
		echo "<p><i>NOTE&nbsp;:</i> Prenez soin de faire une <a href='../gestion/accueil_sauve.php'>sauvegarde de la base</a> et un <a href='../mod_annees_anterieures/index.php'>archivage des donn�es ant�rieures</a> avant le changement d'ann�e.</p>\n";
		echo "</form>\n";
	
		echo "<hr />\n";
	
		echo "<p>Au changement d'ann�e, il est recommand� de vider les entr�es des tables du module Discipline de Gepi.</p>\n";
		echo "<form action=\"clean_tables.php\" method=\"post\">\n";
		echo add_token_field();
		echo "<center>\n";
		echo "<input type=submit value=\"Vider les tables du module Discipline\" />\n";
		echo "</center>\n";
		echo "<input type='hidden' name='action' value='vidage_mod_discipline' />\n";
		echo "<p><i>NOTE&nbsp;:</i> Prenez soin de faire une <a href='../gestion/accueil_sauve.php'>sauvegarde de la base</a> et un <a href='../mod_annees_anterieures/index.php'>archivage des donn�es ant�rieures</a> avant le changement d'ann�e.</p>\n";
		echo "</form>\n";

		echo "<hr />\n";
	
		echo "<p>Au changement d'ann�e, il faut <a href='../cahier_texte_2/archivage_cdt.php'>archiver les Cahiers de Textes</a>, puis le vider.</p>\n";
		echo "<form action=\"clean_tables.php\" method=\"post\">\n";
		echo add_token_field();
		echo "<center>\n";
		echo "<input type=submit value=\"Vider les tables du module Cahier de Textes\" />\n";
		echo "</center>\n";
		echo "<input type='hidden' name='action' value='clean_cdt' />\n";
		echo "<p><i>NOTE&nbsp;:</i> Prenez soin de faire une <a href='../gestion/accueil_sauve.php'>sauvegarde de la base</a>, l'<a href='../cahier_texte_2/archivage_cdt.php'>archivage des Cahiers de Textes</a> et l'<a href='../mod_annees_anterieures/index.php'>archivage des donn�es ant�rieures</a> avant le changement d'ann�e.</p>\n";
		echo "</form>\n";

	echo "</div>\n";

	//echo "<hr />\n";

	//====================================================

}
echo "<p><br /></p>\n";
require("../lib/footer.inc.php");
?>