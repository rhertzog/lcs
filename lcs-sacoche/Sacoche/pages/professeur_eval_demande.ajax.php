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
if(($_SESSION['SESAMATH_ID']==ID_DEMO)&&($_POST['f_action']!='Afficher_demandes')){exit('Action désactivée pour la démo...');}

$action       = (isset($_POST['f_action']))       ? clean_texte($_POST['f_action'])       : '';			// pour le form0
$action       = (isset($_POST['f_quoi']))         ? clean_texte($_POST['f_quoi'])         : $action;	// pour le form1
$matiere_id   = (isset($_POST['f_matiere']))      ? clean_entier($_POST['f_matiere'])     : 0;
$matiere_nom  = (isset($_POST['f_matiere_nom']))  ? clean_texte($_POST['f_matiere_nom'])  : '';
$groupe_id    = (isset($_POST['f_groupe_id']))    ? clean_entier($_POST['f_groupe_id'])   : 0;			// C'est l'id du groupe d'appartenance de l'élève, pas l'id du groupe associé à un devoir
$groupe_type  = (isset($_POST['f_groupe_type']))  ? clean_texte($_POST['f_groupe_type'])  : '';
$groupe_nom   = (isset($_POST['f_groupe_nom']))   ? clean_texte($_POST['f_groupe_nom'])   : '';

$qui          = (isset($_POST['f_qui']))          ? clean_texte($_POST['f_qui'])          : '';
$date         = (isset($_POST['f_date']))         ? clean_texte($_POST['f_date'])         : '';
$date_visible = (isset($_POST['f_date_visible'])) ? clean_texte($_POST['f_date_visible']) : '';
$info         = (isset($_POST['f_info']))         ? clean_texte($_POST['f_info'])         : '';
$devoir_ids   = (isset($_POST['f_devoir']))       ? clean_texte($_POST['f_devoir'])       : '';
$suite        = (isset($_POST['f_suite']))        ? clean_texte($_POST['f_suite'])        : '';

$tab_demande_id = array();
$tab_user_id    = array();
$tab_item_id    = array();
$tab_user_item  = array();
// Récupérer et contrôler la liste des items transmis
$tab_ids = (isset($_POST['ids'])) ? explode(',',$_POST['ids']) : array() ;
if(count($tab_ids))
{
	foreach($tab_ids as $ids)
	{
		$tab_id = explode('x',$ids);
		$tab_demande_id[] = $tab_id[0];
		$tab_user_id[]    = $tab_id[1];
		$tab_item_id[]    = $tab_id[2];
		$tab_user_item[]  = (int)$tab_id[1].'x'.(int)$tab_id[2];
	}
	$tab_demande_id = array_filter( array_map('clean_entier',$tab_demande_id)            ,'positif');
	$tab_user_id    = array_filter( array_map('clean_entier',array_unique($tab_user_id)) ,'positif');
	$tab_item_id    = array_filter( array_map('clean_entier',array_unique($tab_item_id)) ,'positif');
}
$nb_demandes = count($tab_demande_id);
$nb_users    = count($tab_user_id);
$nb_items    = count($tab_item_id);

$tab_types = array('Classes'=>'classe' , 'Groupes'=>'groupe' , 'Besoins'=>'groupe');
$tab_qui   = array('groupe','select');
$tab_suite = array('changer','retirer');

list($devoir_id,$devoir_groupe_id) = (substr_count($devoir_ids,'_')==1) ? explode('_',$devoir_ids) : array(0,0);

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Afficher une liste de demandes
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
if( ($action=='Afficher_demandes') && $matiere_id && $matiere_nom && $groupe_id && (isset($tab_types[$groupe_type])) && $groupe_nom )
{
	$retour = '';
	// Récupérer la liste des élèves concernés
	$DB_TAB = DB_STRUCTURE_COMMUN::DB_OPT_eleves_regroupement($tab_types[$groupe_type],$groupe_id,$user_statut=1);
	if(!is_array($DB_TAB))
	{
		exit($DB_TAB);	// Erreur : aucun élève de ce regroupement n\'est enregistré !
	}
	$tab_eleves = array();
	$tab_autres = array();
	foreach($DB_TAB as $DB_ROW)
	{
		$tab_eleves[$DB_ROW['valeur']] = $DB_ROW['texte'];
		$tab_autres[$DB_ROW['valeur']] = $DB_ROW['texte'];
	}
	$listing_user_id = implode(',', array_keys($tab_eleves) );
	// Lister les demandes
	$tab_demandes = array();
	$DB_TAB = DB_STRUCTURE_PROFESSEUR::DB_lister_demandes_prof($matiere_id,$listing_user_id);
	if(!count($DB_TAB))
	{
		exit('Aucune demande n\'a été formulée pour ces élèves et cette matière !');
	}
	foreach($DB_TAB as $DB_ROW)
	{
		unset($tab_autres[$DB_ROW['user_id']]);
		$tab_demandes[] = $DB_ROW['demande_id'] ;
		$score  = ($DB_ROW['demande_score']!==null) ? $DB_ROW['demande_score'] : false ;
		$statut = ($DB_ROW['demande_statut']=='eleve') ? 'demande non traitée' : 'évaluation en préparation' ;
		$class  = ($DB_ROW['demande_statut']=='eleve') ? ' class="new"' : '' ;
		// Afficher une ligne du tableau 
		$retour .= '<tr'.$class.'>';
		$retour .= '<td class="nu"><input type="checkbox" name="f_ids" value="'.$DB_ROW['demande_id'].'x'.$DB_ROW['user_id'].'x'.$DB_ROW['item_id'].'" /></td>';
		$retour .= '<td class="label">'.html($matiere_nom).'</td>';
		$retour .= '<td class="label">'.html($DB_ROW['item_ref']).' <img alt="" src="./_img/bulle_aide.png" title="'.html($DB_ROW['item_nom']).'" /></td>';
		$retour .= '<td class="label">$'.$DB_ROW['item_id'].'$</td>';
		$retour .= '<td class="label">'.html($groupe_nom).'</td>';
		$retour .= '<td class="label">'.html($tab_eleves[$DB_ROW['user_id']]).'</td>';
		$retour .= str_replace( '<td class="' , '<td class="label ' , affich_score_html($score,'score',$pourcent='') );
		$retour .= '<td class="label"><i>'.html($DB_ROW['demande_date']).'</i>'.convert_date_mysql_to_french($DB_ROW['demande_date']).'</td>';
		$retour .= '<td class="label">'.$statut.'</td>';
		$retour .= '</tr>';
	}
	// Calculer pour chaque item sa popularité (le nb de demandes pour les élèves affichés)
	$listing_demande_id = implode(',', $tab_demandes );
	$DB_TAB = DB_STRUCTURE_PROFESSEUR::DB_recuperer_item_popularite($listing_demande_id,$listing_user_id);
	$tab_bad = array();
	$tab_bon = array();
	foreach($DB_TAB as $DB_ROW)
	{
		$s = ($DB_ROW['popularite']>1) ? 's' : '' ;
		$tab_bad[] = '$'.$DB_ROW['item_id'].'$';
		$tab_bon[] = '<i>'.sprintf("%02u",$DB_ROW['popularite']).'</i>'.$DB_ROW['popularite'].' demande'.$s;
	}
	// Inclure dans le retour la liste des élèves sans demandes
	echo '<td>'.implode('<br />',$tab_autres).'</td>'.'<¤>'.str_replace($tab_bad,$tab_bon,$retour);
}

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Créer une nouvelle évaluation
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
elseif( ($action=='creer') && $groupe_id && (isset($tab_types[$groupe_type])) && in_array($qui,$tab_qui) && $date && $date_visible && $info && in_array($suite,$tab_suite) && $nb_demandes && $nb_users && $nb_items )
{
	// Dans le cas d'une évaluation sur une liste d'élèves sélectionnés,
	// Commencer par créer un nouveau groupe de type "eval", utilisé uniquement pour cette évaluation (c'est transparent pour le professeur)
	if($qui=='select')
	{
		$groupe_id = DB_STRUCTURE_PROFESSEUR::DB_ajouter_groupe_par_prof('eval','',0);
		// Y associer le prof, en responsable du groupe
		DB_STRUCTURE_PROFESSEUR::DB_modifier_liaison_user_groupe_par_prof($_SESSION['USER_ID'],'professeur',$groupe_id,'eval',TRUE);
		DB_STRUCTURE_PROFESSEUR::DB_ajouter_liaison_professeur_responsable($_SESSION['USER_ID'],$groupe_id);
	}
	// Insérer l'enregistrement de l'évaluation
	$date_mysql = convert_date_french_to_mysql($date);
	$date_visible_mysql = convert_date_french_to_mysql($date_visible);
	$devoir_id = DB_STRUCTURE_PROFESSEUR::DB_ajouter_devoir($_SESSION['USER_ID'],$groupe_id,$date_mysql,$info,$date_visible_mysql);
	// Dans le cas d'une évaluation sur une liste d'élèves sélectionnés,
	// Affecter tous les élèves choisis
	if($qui=='select')
	{
		DB_STRUCTURE_PROFESSEUR::DB_modifier_liaison_devoir_user($devoir_id,$groupe_id,$tab_user_id,'creer');
	}
	// Insérer les enregistrements des items de l'évaluation
	DB_STRUCTURE_PROFESSEUR::DB_modifier_liaison_devoir_item($devoir_id,$tab_item_id,'creer');
	// Insérer les scores 'REQ' pour indiquer au prof les demandes dans le tableau de saisie
	$info = 'Demande en attente ('.$_SESSION['USER_NOM'].' '.$_SESSION['USER_PRENOM']{0}.'.)';
	foreach($tab_user_item as $key)
	{
		list($eleve_id,$item_id) = explode('x',$key);
		DB_STRUCTURE_PROFESSEUR::DB_ajouter_saisie($_SESSION['USER_ID'],$eleve_id,$devoir_id,$item_id,$date_mysql,'REQ',$info,$date_visible_mysql);
	}
	// Pour terminer, on change le statut des demandes ou on les supprime
	$listing_demande_id = implode(',',$tab_demande_id);
	if($suite=='changer')
	{
		DB_STRUCTURE_PROFESSEUR::DB_modifier_statut_demandes($listing_demande_id,'prof');
	}
	else
	{
		DB_STRUCTURE_PROFESSEUR::DB_supprimer_demandes_devoir($listing_demande_id);
	}
	exit('ok');
}

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Compléter une évaluation existante
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
elseif( ($action=='completer') && (isset($tab_types[$groupe_type])) && in_array($qui,$tab_qui) && $devoir_id && $devoir_groupe_id && in_array($suite,$tab_suite) && $nb_demandes && $nb_users && $nb_items && $date && $date_visible )
{
	// Dans le cas d'une évaluation sur une liste d'élèves sélectionnés
	if($qui=='select')
	{
		// Il faut ajouter tous les élèves choisis
		DB_STRUCTURE_PROFESSEUR::DB_modifier_liaison_devoir_user($devoir_id,$devoir_groupe_id,$tab_user_id,'ajouter'); // ($devoir_groupe_id et non $groupe_id qui correspond à la classe d'origine des élèves...)
	}
	// Maintenant on peut modifier les items de l'évaluation
	DB_STRUCTURE_PROFESSEUR::DB_modifier_liaison_devoir_item($devoir_id,$tab_item_id,'ajouter');
	// Insérer les scores 'REQ' pour indiquer au prof les demandes dans le tableau de saisie
	$date_mysql = convert_date_french_to_mysql($date);
	$date_visible_mysql = convert_date_french_to_mysql($date_visible);
	$info = 'Demande en attente ('.$_SESSION['USER_NOM'].' '.$_SESSION['USER_PRENOM']{0}.'.)';
	foreach($tab_user_item as $key)
	{
		list($eleve_id,$item_id) = explode('x',$key);
		DB_STRUCTURE_PROFESSEUR::DB_ajouter_saisie($_SESSION['USER_ID'],$eleve_id,$devoir_id,$item_id,$date_mysql,'REQ',$info,$date_visible_mysql);
	}
	// Pour terminer, on change le statut des demandes ou on les supprime
	$listing_demande_id = implode(',',$tab_demande_id);
	if($suite=='changer')
	{
		DB_STRUCTURE_PROFESSEUR::DB_modifier_statut_demandes($listing_demande_id,'prof');
	}
	else
	{
		DB_STRUCTURE_PROFESSEUR::DB_supprimer_demandes_devoir($listing_demande_id);
	}
	exit('ok');
}

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Changer le statut pour "évaluation en préparation"
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
elseif( ($action=='changer') && $nb_demandes )
{
	$listing_demande_id = implode(',',$tab_demande_id);
	DB_STRUCTURE_PROFESSEUR::DB_modifier_statut_demandes($listing_demande_id,'prof');
	exit('ok');
}

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Retirer de la liste des demandes
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
elseif( ($action=='retirer') && $nb_demandes )
{
	$listing_demande_id = implode(',',$tab_demande_id);
	DB_STRUCTURE_PROFESSEUR::DB_supprimer_demandes_devoir($listing_demande_id);
	exit('ok');
}

else
{
	echo'Erreur avec les données transmises !';
}
?>
