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

$action = (isset($_POST['f_action'])) ? clean_texte($_POST['f_action']) : '';
$id     = (isset($_POST['f_id']))     ? clean_entier($_POST['f_id'])    : 0;
$nom    = (isset($_POST['f_nom']))    ? clean_texte($_POST['f_nom'])    : '';
$ordre  = (isset($_POST['f_ordre']))  ? clean_entier($_POST['f_ordre']) : 0;

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Ajouter une nouvelle période / Dupliquer une pédiode existante
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
if( (($action=='ajouter')||($action=='dupliquer')) && $ordre && $nom )
{
	// Vérifier que le nom de la période est disponible
	if( DB_STRUCTURE_ADMINISTRATEUR::DB_tester_periode_nom($nom) )
	{
		exit('Erreur : nom de période déjà existant !');
	}
	// Insérer l'enregistrement
	$periode_id = DB_STRUCTURE_ADMINISTRATEUR::DB_ajouter_periode($ordre,$nom);
	// Afficher le retour
	echo'<tr id="id_'.$periode_id.'" class="new">';
	echo	'<td>'.$ordre.'</td>';
	echo	'<td>'.html($nom).'</td>';
	echo	'<td class="nu">';
	echo		'<q class="modifier" title="Modifier cette période."></q>';
	echo		'<q class="dupliquer" title="Dupliquer cette période."></q>';
	echo		'<q class="supprimer" title="Supprimer cette période."></q>';
	echo	'</td>';
	echo'</tr>';
}

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Modifier une période existante
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
else if( ($action=='modifier') && $id && $ordre && $nom )
{
	// Vérifier que le nom de la période est disponible
	if( DB_STRUCTURE_ADMINISTRATEUR::DB_tester_periode_nom($nom,$id) )
	{
		exit('Erreur : nom de période déjà existant !');
	}
	// Mettre à jour l'enregistrement
	DB_STRUCTURE_ADMINISTRATEUR::DB_modifier_periode($id,$ordre,$nom);
	// Afficher le retour
	echo'<td>'.$ordre.'</td>';
	echo'<td>'.html($nom).'</td>';
	echo'<td class="nu">';
	echo	'<q class="modifier" title="Modifier cette période."></q>';
	echo	'<q class="dupliquer" title="Dupliquer cette période."></q>';
	echo	'<q class="supprimer" title="Supprimer cette période."></q>';
	echo'</td>';
}

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Supprimer une période existante
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
else if( ($action=='supprimer') && $id )
{
	// Effacer l'enregistrement
	DB_STRUCTURE_ADMINISTRATEUR::DB_supprimer_periode($id);
	// Log de l'action
	ajouter_log_SACoche('Suppression d\'une période (n°'.$id.').');
	// Afficher le retour
	echo'<td>ok</td>';
}

else
{
	echo'Erreur avec les données transmises !';
}
?>
