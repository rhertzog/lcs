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
$TITRE = "Demandes d'évaluations formulées";

// Lister le nb de demandes d'évaluations autorisées suivant les matières
$infobulle = '';
$DB_TAB = DB_STRUCTURE_COMMUN::DB_OPT_matieres_eleve($_SESSION['MATIERES'],$_SESSION['USER_ID']);
if(!is_array($DB_TAB))
{
	$infobulle .= $DB_TAB;
}
else
{
	foreach($DB_TAB as $key => $DB_ROW)
	{
		$infobulle .= $DB_ROW['texte'].' : '.$DB_ROW['info'].'<br />';
	}
}
?>

<ul class="puce">
	<li><span class="manuel"><a class="pop_up" href="<?php echo SERVEUR_DOCUMENTAIRE ?>?fichier=environnement_generalites__demandes_evaluations">DOC : Demandes d'évaluations.</a></span></li>
	<li><span class="astuce"><a title="<?php echo $infobulle ?>" href="#">Nombre de demandes autorisées par matière.</a></span></li>
</ul>

<hr />

<form action="#" method="post">
	<table class="form">
		<thead>
			<tr>
				<th>Date</th>
				<th>Matière</th>
				<th>Item</th>
				<th>Score</th>
				<th>Statut</th>
				<th class="nu"></th>
			</tr>
		</thead>
		<tbody>
			<?php
			// Lister les demandes d'évaluation
			$DB_TAB = DB_STRUCTURE_ELEVE::DB_lister_demandes_eleve($_SESSION['USER_ID']);
			foreach($DB_TAB as $DB_ROW)
			{
				$score  = ($DB_ROW['demande_score']!==null) ? $DB_ROW['demande_score'] : false ;
				$statut = ($DB_ROW['demande_statut']=='eleve') ? 'demande non traitée' : 'évaluation en préparation' ;
				$texte_lien_avant = ($DB_ROW['item_lien']) ? '<a class="lien_ext" href="'.html($DB_ROW['item_lien']).'">' : '';
				$texte_lien_apres = ($DB_ROW['item_lien']) ? '</a>' : '';
				// Afficher une ligne du tableau 
				echo'<tr id="ids_'.$DB_ROW['demande_id'].'_'.$DB_ROW['item_id'].'_'.$DB_ROW['matiere_id'].'">';
				echo	'<td><i>'.html($DB_ROW['demande_date']).'</i>'.convert_date_mysql_to_french($DB_ROW['demande_date']).'</td>';
				echo	'<td>'.html($DB_ROW['matiere_nom']).'</td>';
				echo	'<td>'.$texte_lien_avant.html($DB_ROW['item_ref']).$texte_lien_apres.' <img alt="" src="./_img/bulle_aide.png" title="'.html($DB_ROW['item_nom']).'" /></td>';
				echo	affich_score_html($score,'score',$pourcent='');
				echo	'<td>'.$statut.'</td>';
				echo	'<td class="nu"><q class="supprimer" title="Supprimer cette demande d\'évaluation."></q></td>';
				echo'</tr>';
			}
			?>
		</tbody>
	</table>
</form>
