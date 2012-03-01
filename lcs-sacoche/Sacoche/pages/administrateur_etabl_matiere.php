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
$TITRE = "Matières";

// Formulaire des familles de matières, en 3 catégories
$select_matiere_famille = Formulaire::afficher_select(DB_STRUCTURE_COMMUN::DB_OPT_familles_matieres() , $select_nom='f_famille' , $option_first='oui' , $selection=false , $optgroup='oui');

// Lister les matières de l'établissement
$DB_TAB = DB_STRUCTURE_ADMINISTRATEUR::DB_lister_matieres_etablissement( TRUE /*order_by_name*/ );
$matieres_options = '<option value="0"></option>';
foreach($DB_TAB as $DB_ROW)
{
	$matieres_options .= '<option value="'.$DB_ROW['matiere_id'].'">'.html($DB_ROW['matiere_nom'].' ('.$DB_ROW['matiere_ref'].')').'</option>' ;
}

?>

<div><span class="manuel"><a class="pop_up" href="<?php echo SERVEUR_DOCUMENTAIRE ?>?fichier=support_administrateur__gestion_matieres">DOC : Matières</a></span></div>

<script type="text/javascript">
	var id_matiere_partagee_max = "<?php echo ID_MATIERE_PARTAGEE_MAX ?>";
</script>

<form action="#" method="post" id="form_partage">
	<hr />
	<h2>Matières partagées (officielles)</h2>
	<table class="form hsort">
		<thead>
			<tr>
				<th>Référence</th>
				<th>Nom complet</th>
				<th class="nu"><q class="ajouter" title="Ajouter une matière."></q></th>
			</tr>
		</thead>
		<tbody>
			<?php
			// Lister les matières partagées
			$DB_TAB = DB_STRUCTURE_ADMINISTRATEUR::DB_lister_matieres(FALSE /*is_specifique*/);
			foreach($DB_TAB as $DB_ROW)
			{
				// Afficher une ligne du tableau
				echo'<tr id="id_'.$DB_ROW['matiere_id'].'">';
				echo	'<td>'.html($DB_ROW['matiere_ref']).'</td>';
				echo	'<td>'.html($DB_ROW['matiere_nom']).'</td>';
				echo	'<td class="nu">';
				echo		'<q class="supprimer" title="Supprimer cette matière."></q>';
				echo	'</td>';
				echo'</tr>';
			}
			?>
		</tbody>
	</table>
</form>

<form action="#" method="post" id="form_perso">
	<hr />
	<h2>Matières spécifiques</h2>
	<table class="form hsort">
		<thead>
			<tr>
				<th>Référence</th>
				<th>Nom complet</th>
				<th class="nu"><q class="ajouter" title="Ajouter une matière."></q></th>
			</tr>
		</thead>
		<tbody>
			<?php
			// Lister les matières spécifiques
			$DB_TAB = DB_STRUCTURE_ADMINISTRATEUR::DB_lister_matieres(TRUE /*is_specifique*/);
			foreach($DB_TAB as $DB_ROW)
			{
				// Afficher une ligne du tableau
				echo'<tr id="id_'.$DB_ROW['matiere_id'].'">';
				echo	'<td>'.html($DB_ROW['matiere_ref']).'</td>';
				echo	'<td>'.html($DB_ROW['matiere_nom']).'</td>';
				echo	'<td class="nu">';
				echo		'<q class="modifier" title="Modifier cette matière."></q>';
				echo		'<q class="supprimer" title="Supprimer cette matière."></q>';
				echo	'</td>';
				echo'</tr>';
			}
			?>
		</tbody>
	</table>
</form>

<form action="#" method="post" id="form_move">
	<hr />
	<h2>Déplacer les référentiels d'une matière vers une autre</h2>
	<p class="astuce">Ce menu peut par exemple servir à convertir une matière spécifique en matière officielle afin de pouvoir en partager les référentiels.</p>
	<ul class="puce">
		<li>Les deux matières doivent être visibles ci-dessus.</li>
		<li>La nouvelle matière doit être vierge de tout référentiel.</li>
		<li>L'ancienne matière sera retirée / supprimée après l'opération.</li>
	</ul>
	<p class="danger">Cette opération est sensible : il est recommandé de l'effectuer en période creuse et de sauvegarder la base avant.</p>
	<fieldset>
		<label class="tab" for="f_matiere_avant">Ancienne matière :</label><select id="f_matiere_avant" name="f_matiere_avant"><?php echo $matieres_options ?></select><br />
		<label class="tab" for="f_matiere_apres">Nouvelle matière :</label><select id="f_matiere_apres" name="f_matiere_apres"><?php echo $matieres_options ?></select><br />
		<span class="tab"></span><button id="deplacer_referentiels" type="button" class="parametre">Déplacer les référentiels.</button><label id="ajax_msg_move">&nbsp;</label>
	</fieldset>
</form>

<form action="#" method="post" id="zone_ajout_form" onsubmit="return false" class="hide">
	<hr />
	<h2>Rechercher une matière officielle</h2>
	<p><span class="tab"></span><button id="ajout_annuler" type="button" class="annuler">Annuler / Retour.</button></p>
	<p id="f_recherche_mode">
		<label class="tab">Technique :</label><label for="f_mode_famille"><input type="radio" id="f_mode_famille" name="f_mode" value="famille" /> recherche par famille de matières</label>&nbsp;&nbsp;&nbsp;<label for="f_mode_motclef"><input type="radio" id="f_mode_motclef" name="f_mode" value="motclef" /> recherche par mots-clefs</label>
	</p>
	<fieldset id="f_recherche_famille" class="hide">
		<label class="tab" for="f_famille">Famille :</label><?php echo $select_matiere_famille ?><br />
	</fieldset>
	<fieldset id="f_recherche_motclef" class="hide">
		<label class="tab" for="f_motclef">Mots-clefs :</label><input id="f_motclef" name="f_motclef" size="50" type="text" value="" /><br />
		<span class="tab"></span><button id="rechercher_motclef" type="button" class="rechercher">Lancer la recherche.</button><br />
	</fieldset>
	<span class="tab"></span><label id="ajax_msg_recherche">&nbsp;</label>
	<ul id="f_recherche_resultat" class="puce hide">
		<li></li>
	</ul>
</form>

<p>&nbsp;</p>
