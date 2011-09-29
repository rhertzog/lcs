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
$TITRE = "Affecter les élèves aux classes";
$VERSION_JS_FILE += 1;
?>

<?php
// Fabrication des éléments select du formulaire
$select_f_groupes = afficher_select(DB_STRUCTURE_OPT_regroupements_etabl() , $select_nom=false , $option_first='oui' , $selection=false , $optgroup='oui');
$select_classes   = afficher_select(DB_STRUCTURE_OPT_classes_etabl()       , $select_nom=false , $option_first='non' , $selection=false , $optgroup='non');
?>

<p><span class="manuel"><a class="pop_up" href="<?php echo SERVEUR_DOCUMENTAIRE ?>?fichier=support_administrateur__gestion_classes">DOC : Gestion des classes</a></span></p>

<hr />

<form action="" method="post">
	<table><tr>
		<td class="nu" style="width:25em">
			<b>Liste des élèves :</b> <img alt="" src="./_img/bulle_aide.png" title="Utiliser la touche &laquo&nbsp;Shift&nbsp;&raquo; pour une sélection multiple contiguë.<br />Utiliser la touche &laquo&nbsp;Ctrl&nbsp;&raquo; pour une sélection multiple non contiguë." /><br />
			<select id="f_groupe" name="f_groupe" class="t8"><?php echo $select_f_groupes ?></select><br />
			<select id="select_eleves" name="select_eleves[]" multiple size="8" class="t8"><option value=""></option></select>
		</td>
		<td class="nu" style="width:20em">
			<b>Liste des classes :</b> <img alt="" src="./_img/bulle_aide.png" title="Utiliser la touche &laquo&nbsp;Shift&nbsp;&raquo; pour une sélection multiple contiguë.<br />Utiliser la touche &laquo&nbsp;Ctrl&nbsp;&raquo; pour une sélection multiple non contiguë." /><br />
			<select id="select_classes" name="select_classes[]" multiple size="10" class="t8"><?php echo $select_classes; ?></select>
		</td>
		<td class="nu" style="width:25em">
			<button id="ajouter" type="button"><img alt="" src="./_img/bouton/groupe_ajouter.png" /> Ajouter ces associations.</button><br />
			<button id="retirer" type="button"><img alt="" src="./_img/bouton/groupe_retirer.png" /> Retirer ces associations.</button>
			<p><label id="ajax_msg">&nbsp;</label></p>
		</td>
	</tr></table>
</form>

<div id="bilan">
</div>
