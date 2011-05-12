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
$TITRE = "Gérer les groupes";
?>

<p><span class="manuel"><a class="pop_up" href="<?php echo SERVEUR_DOCUMENTAIRE ?>?fichier=support_administrateur__gestion_groupes">DOC : Gestion des groupes</a></span></p>

<hr />

<form action="">
	<table class="form">
		<thead>
			<tr>
				<th>Niveau</th>
				<th>Nom</th>
				<th class="nu"><q class="ajouter" title="Ajouter un groupe."></q></th>
			</tr>
		</thead>
		<tbody>
			<?php
			$DB_TAB = DB_STRUCTURE_lister_groupes_avec_niveaux();
			foreach($DB_TAB as $DB_ROW)
			{
				// Afficher une ligne du tableau
				echo'<tr id="id_'.$DB_ROW['groupe_id'].'">';
				echo	'<td><i>'.sprintf("%02u",$DB_ROW['niveau_ordre']).'</i>'.html($DB_ROW['niveau_nom']).'</td>';
				echo	'<td>'.html($DB_ROW['groupe_nom']).'</td>';
				echo	'<td class="nu">';
				echo		'<q class="modifier" title="Modifier ce groupe."></q>';
				echo		'<q class="supprimer" title="Supprimer ce groupe."></q>';
				echo	'</td>';
				echo'</tr>';
			}
			?>
		</tbody>
	</table>
</form>

<?php
$select_niveau = '<option value=""></option>';
$tab_niveau_ordre_js = 'var tab_niveau_ordre = new Array();';

if($_SESSION['NIVEAUX'])
{
	$DB_TAB = DB_STRUCTURE_lister_niveaux_etablissement($_SESSION['NIVEAUX'],$listing_cycles=false);
	foreach($DB_TAB as $DB_ROW)
	{
		$select_niveau .= '<option value="'.$DB_ROW['niveau_id'].'">'.html($DB_ROW['niveau_nom']).'</option>';
		$tab_niveau_ordre_js .= 'tab_niveau_ordre["'.html($DB_ROW['niveau_nom']).'"]="'.sprintf("%02u",$DB_ROW['niveau_ordre']).'";';
	}
}
else
{
	$select_niveau .= '<option value="" disabled>Aucun niveau n\'est rattaché à l\'établissement !</option>';
}
?>

<script type="text/javascript">
	// <![CDATA[
	var select_niveau="<?php echo str_replace('"','\"',$select_niveau); ?>";
	// ]]>
	<?php echo $tab_niveau_ordre_js ?>
</script>
