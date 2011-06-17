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

$action = (isset($_GET['action'])) ? $_GET['action'] : '';

require_once('./_inc/tableau_langues.php');

//
// Modifier des associations
//

if($action=='associer')
{
	// liste des élèves
	$tab_select_eleves  = (isset($_POST['select_eleves']))  ? array_map('clean_entier',explode(',',$_POST['select_eleves']))  : array() ;
	$tab_select_eleves  = array_filter($tab_select_eleves,'positif');
	$listing_user_id    = implode(',',$tab_select_eleves);
	if(!$listing_user_id)
	{
		exit('Erreur : élève(s) non récupéré(s) !');
	}
	// langue
	$langue = (isset($_POST['select_langue'])) ? clean_entier($_POST['select_langue']) : 0 ;
	if( (!$langue) || (!isset($tab_langues[$langue])) )
	{
		exit('Erreur : langue non transmise ou incorrecte !');
	}
	// go
	DB_STRUCTURE_modifier_user_langue($listing_user_id,$langue);
}

//
// Affichage du bilan des affectations des langues aux élèves
//

$tab_niveau_groupe = array();
$tab_user          = array();
$tab_niveau_groupe[0][0] = '<i>sans classe</i>';
$tab_user[0]             = '';

// Récupérer la liste des classes
$DB_TAB = DB_STRUCTURE_lister_classes_avec_niveaux($niveau_ordre='DESC');
foreach($DB_TAB as $DB_ROW)
{
	$tab_niveau_groupe[$DB_ROW['niveau_id']][$DB_ROW['groupe_id']] = html($DB_ROW['groupe_nom']);
	$tab_user[$DB_ROW['groupe_id']] = '';
}
// Récupérer la liste des élèves / classes
$DB_TAB = DB_STRUCTURE_lister_users('eleve',$only_actifs=true,$with_classe=false);
foreach($DB_TAB as $DB_ROW)
{
	$tab_user[$DB_ROW['eleve_classe_id']] .= '<img src="./_img/drapeau/'.$DB_ROW['eleve_langue'].'.gif" alt="" title="'.$tab_langues[$DB_ROW['eleve_langue']]['texte'].'" /> '.html($DB_ROW['user_nom'].' '.$DB_ROW['user_prenom']).'<br />';
}
// Assemblage du tableau résultant
$TH = array();
$TB = array();
$TF = array();
foreach($tab_niveau_groupe as $niveau_id => $tab_groupe)
{
	$TH[$niveau_id] = '';
	$TB[$niveau_id] = '';
	$TF[$niveau_id] = '';
	foreach($tab_groupe as $groupe_id => $groupe_nom)
	{
		$nb = mb_substr_count($tab_user[$groupe_id],'<br />','UTF-8');
		$s = ($nb>1) ? 's' : '' ;
		$TH[$niveau_id] .= '<th>'.$groupe_nom.'</th>';
		$TB[$niveau_id] .= '<td>'.mb_substr($tab_user[$groupe_id],0,-6,'UTF-8').'</td>';
		$TF[$niveau_id] .= '<td>'.$nb.' élève'.$s.'</td>';
	}
}
echo'<hr />';
foreach($tab_niveau_groupe as $niveau_id => $tab_groupe)
{
	echo'<table class="affectation">';
	echo'<thead><tr>'.$TH[$niveau_id].'</tr></thead>';
	echo'<tbody><tr>'.$TB[$niveau_id].'</tr></tbody>';
	echo'<tfoot><tr>'.$TF[$niveau_id].'</tr></tfoot>';
	echo'</table><p />';
}
?>
