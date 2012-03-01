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
if($_SESSION['SESAMATH_ID']==ID_DEMO) {exit('Action désactivée pour la démo...');}

$action     = (isset($_POST['f_action'])) ? clean_texte($_POST['f_action'])  : '';
$groupe_id  = (isset($_POST['f_id']))     ? clean_entier($_POST['f_id'])     : 0;
$niveau     = (isset($_POST['f_niveau'])) ? clean_entier($_POST['f_niveau']) : 0;
$groupe_nom = (isset($_POST['f_nom']))    ? clean_texte($_POST['f_nom'])     : '';

// Contrôler la liste des élèves transmis
$tab_eleves = (isset($_POST['f_eleve_liste']))  ? explode('_',$_POST['f_eleve_liste'])  : array() ;
$tab_eleves = array_map('clean_entier',$tab_eleves);
$tab_eleves = array_filter($tab_eleves,'positif');
$nb_eleves  = count($tab_eleves);
// Contrôler la liste des profs transmis
$tab_profs = (isset($_POST['f_prof_liste'])) ? explode('_',$_POST['f_prof_liste']) : array() ;
$tab_profs = array_map('clean_entier',$tab_profs);
$tab_profs = array_filter($tab_profs,'positif');
$nb_profs = count($tab_profs);
// Si profs transmis, en retirer le responsable (si le responsable est le seul prof, rien n'est transmis)
if(count($tab_profs))
{
	$indice = array_search($_SESSION['USER_ID'],$tab_profs);
	if($indice===FALSE)
	{
		exit('Erreur : absent de la liste des professeurs !');
	}
	unset($tab_profs[$indice]);
}

//	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//	Ajouter un nouveau groupe de besoin
//	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

if( ($action=='ajouter') && $niveau && $groupe_nom && $nb_eleves )
{
	// Vérifier que le nom du groupe est disponible
	if( DB_STRUCTURE_PROFESSEUR::DB_tester_groupe_nom($groupe_nom) )
	{
		exit('Erreur : nom de groupe déjà existant !');
	}
	// Insérer l'enregistrement ; y associe automatiquement le prof, en responsable du groupe
	$groupe_id = DB_STRUCTURE_PROFESSEUR::DB_ajouter_groupe_par_prof('besoin',$groupe_nom,$niveau);
	// Affecter les élèves et les profs au groupe
	DB_STRUCTURE_PROFESSEUR::DB_modifier_liaison_user_groupe_par_prof( $groupe_id , $tab_eleves , $tab_profs , 'creer' /*mode*/ , 0 /*devoir_id*/ );
	// Afficher le retour
	$eleves_texte  = ($nb_eleves>1) ? $nb_eleves.' élèves' : '1 élève' ;
	$profs_texte   = ($nb_profs>1)  ? $nb_profs.' profs'   : 'moi seul' ;
	echo'<tr id="id_'.$groupe_id.'" class="new">';
	echo	'<td>{{NIVEAU_NOM}}</td>';
	echo	'<td>'.html($groupe_nom).'</td>';
	echo	'<td>'.$eleves_texte.'</td>';
	echo	'<td>'.$profs_texte.'</td>';
	echo	'<td class="nu">';
	echo		'<q class="modifier" title="Modifier ce groupe de besoin."></q>';
	echo		'<q class="supprimer" title="Supprimer ce groupe de besoin."></q>';
	echo	'</td>';
	echo'</tr>';
	echo'<SCRIPT>';
	echo'tab_eleves["'.$groupe_id.'"]="'.implode('_',$tab_eleves).'";';
	echo'tab_profs["'.$groupe_id.'"]="'.implode('_',$tab_profs).'";';
	exit();
}

//	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//	Modifier un groupe de besoin existant
//	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

if( ($action=='modifier') && $groupe_id && $niveau && $groupe_nom && $nb_eleves )
{
	// Vérifier que le nom du groupe est disponible
	if( DB_STRUCTURE_PROFESSEUR::DB_tester_groupe_nom($groupe_nom,$groupe_id) )
	{
		exit('Erreur : nom de groupe de besoin déjà existant !');
	}
	// Mettre à jour l'enregistrement
	DB_STRUCTURE_PROFESSEUR::DB_modifier_groupe_par_prof($groupe_id,$groupe_nom,$niveau);
	// Mettre les affectations des élèves et des profs au groupe
	DB_STRUCTURE_PROFESSEUR::DB_modifier_liaison_user_groupe_par_prof( $groupe_id , $tab_eleves , $tab_profs , 'substituer' /*mode*/ , 0 /*devoir_id*/ );
	// Afficher le retour
	$eleves_texte  = ($nb_eleves>1) ? $nb_eleves.' élèves' : '1 élève' ;
	$profs_texte   = ($nb_profs>1)  ? $nb_profs.' profs'   : 'moi seul' ;
	echo'<td>{{NIVEAU_NOM}}</td>';
	echo'<td>'.html($groupe_nom).'</td>';
	echo'<td>'.$eleves_texte.'</td>';
	echo'<td>'.$profs_texte.'</td>';
	echo'<td class="nu">';
	echo	'<q class="modifier" title="Modifier ce groupe de besoin."></q>';
	echo	'<q class="supprimer" title="Supprimer ce groupe de besoin."></q>';
	echo'</td>';
	echo'<SCRIPT>';
	echo'tab_eleves["'.$groupe_id.'"]="'.implode('_',$tab_eleves).'";';
	echo'tab_profs["'.$groupe_id.'"]="'.implode('_',$tab_profs).'";';
	exit();
}

//	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//	Supprimer un groupe de besoin existant
//	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

if( ($action=='supprimer') && $groupe_id )
{
	// Effacer l'enregistrement
	DB_STRUCTURE_PROFESSEUR::DB_supprimer_groupe_par_prof( $groupe_id , 'besoin' , TRUE /*with_devoir*/ );
	// Log de l'action
	ajouter_log_SACoche('Suppression d\'un regroupement (besoin '.$groupe_id.'), avec les devoirs associés.');
	// Afficher le retour
	exit('<td>ok</td>');
}

//	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//	On ne devrait pas en arriver là !
//	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

exit('Erreur avec les données transmises !');

?>