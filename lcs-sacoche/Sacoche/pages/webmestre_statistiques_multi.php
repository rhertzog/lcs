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
$VERSION_JS_FILE += 7;
?>

<?php
$selection = (isset($_POST['listing_ids'])) ? explode(',',$_POST['listing_ids']) : false ; // demande de stats depuis structure_multi.php
$select_structure = afficher_select(DB_WEBMESTRE_OPT_structures_sacoche() , $select_nom=false , $option_first='non' , $selection , $optgroup='oui') ;
?>

<form id="statistiques" action=""><fieldset>
	<label class="tab" for="f_basic">Structure(s) <img alt="" src="./_img/bulle_aide.png" title="Utiliser la touche &laquo&nbsp;Shift&nbsp;&raquo; pour une sélection multiple contiguë.<br />Utiliser la touche &laquo&nbsp;Ctrl&nbsp;&raquo; pour une sélection multiple non contiguë." /> :</label><select id="f_base" name="f_base" multiple size="10"><?php echo $select_structure ?></select><br />
	<span class="tab"></span><input type="hidden" id="f_action" name="f_action" value="calculer" /><input type="hidden" id="f_listing_id" name="f_listing_id" value="" /><button id="bouton_valider" type="submit"><img alt="" src="./_img/bouton/stats.png" /> Calculer les statistiques.</button><label id="ajax_msg">&nbsp;</label>
</fieldset></form>

<div id="ajax_info" class="hide">
	<h2>Calcul des statistiques en cours</h2>
	<label id="ajax_msg1"></label>
	<ul class="puce"><li id="ajax_msg2"></li></ul>
	<span id="ajax_num" class="hide"></span>
	<span id="ajax_max" class="hide"></span>
</div>

<p />

<form id="structures" action="" class="hide">
	<hr />
	<table class="form" id="statistiques">
		<thead>
			<tr>
				<th class="nu"><input id="all_check" type="image" src="./_img/all_check.gif" title="Tout cocher." /><br /><input id="all_uncheck" type="image" src="./_img/all_uncheck.gif" title="Tout décocher." /></th>
				<th>Id</th>
				<th>Structure</th>
				<th>Contact</th>
				<th>Ancienneté</th>
				<th>professeurs<br />enregistrés</th>
				<th>professeurs<br />connectés</th>
				<th>élèves<br />enregistrés</th>
				<th>élèves<br />connectés</th>
				<th>saisies<br />enregistrées</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
			</tr>
		</tfoot>
		<tbody>
			<tr>
			</tr>
		</tbody>
	</table>
	<p id="zone_actions">
		Pour les structures cochées : <input id="listing_ids" name="listing_ids" type="hidden" value="" />
		<button id="bouton_newsletter" type="button"><img alt="" src="./_img/bouton/mail_ecrire.png" /> Écrire un courriel.</button>
		<button id="bouton_transfert" type="button"><img alt="" src="./_img/bouton/fichier_export.png" /> Exporter données &amp; bases.</button>
		<button id="bouton_supprimer" type="button"><img alt="" src="./_img/bouton/supprimer.png" /> Supprimer.</button>
		<label id="ajax_supprimer">&nbsp;</label>
	</p>
	<div class="astuce">Concernant les <b>utilisateurs enregistrés</b>, seuls sont comptés ceux au statut "actif".</div>
	<div class="astuce">Les <b>utilisateurs connectés</b> sont ceux s'étant identifiés au cours du dernier semestre.</div>
	</p>
</form>
