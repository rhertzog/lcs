<?php
/**
 * @version $Id$
 * @author Thomas Crespin <thomas.crespin@sesamath.net>
 * @copyright Thomas Crespin 2010
 * 
 * ****************************************************************************************************
 * SACoche <http://sacoche.sesamath.net> - Suivi d'Acquisitions de Compétences
 * © Thomas Crespin pour Sésamath <http://www.sesamath.net> - Tous droits réservés.
 * Logiciel placé sous la licence libre GPL 3 <http://www.rodage.org/gpl-3.0.fr.html>.
 * ****************************************************************************************************
 * 
 * Ce fichier est une partie de SACoche.
 * 
 * SACoche est un logiciel libre ; vous pouvez le redistribuer ou le modifier suivant les termes 
 * de la “GNU General Public License” telle que publiée par la Free Software Foundation :
 * soit la version 3 de cette licence, soit (à votre gré) toute version ultérieure.
 * 
 * SACoche est distribué dans l’espoir qu’il vous sera utile, mais SANS AUCUNE GARANTIE :
 * sans même la garantie implicite de COMMERCIALISABILITÉ ni d’ADÉQUATION À UN OBJECTIF PARTICULIER.
 * Consultez la Licence Générale Publique GNU pour plus de détails.
 * 
 * Vous devriez avoir reçu une copie de la Licence Générale Publique GNU avec SACoche ;
 * si ce n’est pas le cas, consultez : <http://www.gnu.org/licenses/>.
 * 
 */

if(!defined('SACoche')) {exit('Ce fichier ne peut être appelé directement !');}
if(($_SESSION['SESAMATH_ID']==ID_DEMO)&&($_GET['action']!='initialiser')){exit('Action désactivée pour la démo...');}

$action     = (isset($_GET['action']))        ? $_GET['action']                     : '';
$date_debut = (isset($_POST['f_date_debut'])) ? clean_texte($_POST['f_date_debut']) : '';
$date_fin   = (isset($_POST['f_date_fin']))   ? clean_texte($_POST['f_date_fin'])   : '';
$tab_select_periodes        = (isset($_POST['select_periodes']))        ? array_map('clean_entier',explode(',',$_POST['select_periodes']))        : array() ;
$tab_select_classes_groupes = (isset($_POST['select_classes_groupes'])) ? array_map('clean_entier',explode(',',$_POST['select_classes_groupes'])) : array() ;

function positif($n) {return $n;}
$tab_select_periodes        = array_filter($tab_select_periodes,'positif');
$tab_select_classes_groupes = array_filter($tab_select_classes_groupes,'positif');


//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Ajouter des périodes à des classes & groupes
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

if( ($action=='ajouter') && $date_debut && $date_fin )
{
	// Formater les dates
	$date_debut_mysql = convert_date_french_to_mysql($date_debut);
	$date_fin_mysql   = convert_date_french_to_mysql($date_fin);
	// Vérifier que le date de début est antérieure à la date de fin
	if($date_debut_mysql>$date_fin_mysql)
	{
		exit('Erreur : la date de début est postérieure à la date de fin !');
	}
	foreach($tab_select_periodes as $periode_id)
	{
		foreach($tab_select_classes_groupes as $groupe_id)
		{
			DB_STRUCTURE_modifier_liaison_groupe_periode($groupe_id,$periode_id,true,$date_debut_mysql,$date_fin_mysql);
		}
	}
}

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Retirer des périodes à des classes & groupes
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

elseif($action=='retirer')
{
	foreach($tab_select_periodes as $periode_id)
	{
		foreach($tab_select_classes_groupes as $groupe_id)
		{
			DB_STRUCTURE_modifier_liaison_groupe_periode($groupe_id,$periode_id,false);
		}
	}
}

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Affichage du bilan des affectations des périodes aux classes & groupes ; en plusieurs requêtes pour récupérer les périodes sans classes-groupes et les classes-groupes sans périodes
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

echo'<hr />';
$tab_groupe    = array();
$tab_periode   = array();
$tab_jointure  = array();
$tab_graphique = array();
// Récupérer la liste des classes & groupes, dans l'ordre des niveaux
$DB_TAB = DB_STRUCTURE_lister_classes_et_groupes_avec_niveaux();
if(!count($DB_TAB))
{
	exit('Aucune classe et aucun groupe ne sont enregistrés !');
}
foreach($DB_TAB as $DB_ROW)
{
	$tab_groupe[$DB_ROW['groupe_id']]    = '<th>'.html($DB_ROW['groupe_nom']).'</th>';
	$tab_graphique[$DB_ROW['groupe_id']] = '';
}
// Récupérer la liste des périodes, dans l'ordre choisi par l'admin
$DB_TAB = DB_STRUCTURE_lister_periodes();
if(!count($DB_TAB))
{
	exit('Aucune période n\'est enregistrée !');
}
foreach($DB_TAB as $DB_ROW)
{
	$tab_periode[$DB_ROW['periode_id']] = '<th>'.html($DB_ROW['periode_nom']).'</th>';
}
// Récupérer l'amplitude complète sur l'ensemble des périodes
$DB_ROW = DB_STRUCTURE_recuperer_amplitude_periodes();
$tout_debut     = ($DB_ROW['tout_debut'])     ? $DB_ROW['tout_debut']     : '2000-01-01' ;
$toute_fin      = ($DB_ROW['toute_fin'])      ? $DB_ROW['toute_fin']      : '2000-01-01' ;
$nb_jours_total = ($DB_ROW['nb_jours_total']) ? $DB_ROW['nb_jours_total'] : 0;
// Récupérer la liste des jointures, et le nécessaire pour établir les graphiques
$DB_SQL = 'SELECT * , ';
$DB_SQL.= 'DATEDIFF(jointure_date_debut,:tout_debut) AS position_jour_debut , DATEDIFF(jointure_date_fin,jointure_date_debut) AS nb_jour ';
$DB_SQL.= 'FROM sacoche_jointure_groupe_periode ';
$DB_SQL.= 'ORDER BY groupe_id ASC, jointure_date_debut ASC, jointure_date_fin ASC';
$DB_VAR = array(':tout_debut'=>$tout_debut);
$DB_TAB = DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
$memo_groupe_id = 0;
foreach($DB_TAB as $DB_ROW)
{
	$groupe_id = $DB_ROW['groupe_id'];
	$date_affich_debut = convert_date_mysql_to_french($DB_ROW['jointure_date_debut']);
	$date_affich_fin   = convert_date_mysql_to_french($DB_ROW['jointure_date_fin']);
	$tab_jointure[$groupe_id][$DB_ROW['periode_id']] = html($date_affich_debut).' ~ '.html($date_affich_fin).' <input type="image" src="./_img/date_add.png" title="Cliquer pour importer ces dates dans les champs." />';
	// graphique (début)
	if($memo_groupe_id!=$groupe_id)
	{
		$memo_position = 0;
		$memo_groupe_id = $groupe_id;
	}
	$margin_left = 100*round($DB_ROW['position_jour_debut'] / $nb_jours_total , 4);
	$width       = 100*round( ($DB_ROW['nb_jour']+1) / $nb_jours_total , 4);	// On ajoute un jour pour dessiner les barres jusqu'au jour suivant.
	if($memo_position+0.02<$margin_left) // Le 0.02 sert à éviter les erreurs d'arrondi et une erreur PHP style un test 12.34<12.34 qui renvoie vrai !
	{
		// Deux périodes ne sont pas consécutives
		$margin_left_erreur = $memo_position;
		$width_erreur = $margin_left - $memo_position;
		$tab_graphique[$groupe_id] .= '<div class="graph_erreur" style="margin-left:'.$margin_left_erreur.'%;width:'.$width_erreur.'%"></div>';
	}
	elseif($memo_position>$margin_left+0.02) // Le 0.02 sert à éviter les erreurs d'arrondi et une erreur PHP style un test 12.34<12.34 qui renvoie vrai !
	{
		// Deux périodes se chevauchent
		$margin_left_erreur = $margin_left;
		$width_erreur = $memo_position - $margin_left;
		$tab_graphique[$groupe_id] .= '<div class="graph_erreur" style="margin-left:'.$margin_left_erreur.'%;width:'.$width_erreur.'%"></div>';
	}
	$tab_graphique[$groupe_id] .= '<div class="graph_partie" style="margin-left:'.$margin_left.'%;width:'.$width.'%"></div>';
	$memo_position = $margin_left + $width;
	// graphique (fin)
}
// Fabrication du tableau résultant
foreach($tab_groupe as $groupe_id => $groupe_text)
{
	foreach($tab_periode as $periode_id => $periode_text)
	{
		$tab_groupe[$groupe_id] .= (isset($tab_jointure[$groupe_id][$periode_id])) ? '<td>'.$tab_jointure[$groupe_id][$periode_id].'</td>' : '<td class="hc">-</td>' ;
	}
	$tab_groupe[$groupe_id] .= '<td>'.$tab_graphique[$groupe_id].'</td>';
}
// Affichage du tableau résultant
echo'<table>';
echo'<thead><tr><td class="nu"></td>'.implode('',$tab_periode).'<td class="graph_total">Étendue du '.convert_date_mysql_to_french($tout_debut).' au '.convert_date_mysql_to_french($toute_fin).'.</td></tr></thead>';
echo'<tbody><tr>'.implode('</tr>'."\r\n".'<tr>',$tab_groupe).'</tr></tbody>';
echo'</table><p />';

?>
