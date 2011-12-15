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
$TITRE = "Associer les professeurs à ses groupes de besoin";
?>

<?php
// Fabrication des éléments select du formulaire
$select_professeurs   = Formulaire::afficher_select(DB_STRUCTURE_COMMUN::DB_OPT_professeurs_etabl()                      , $select_nom=false , $option_first='non' , $selection=false , $optgroup='non');
$select_groupe_besoin = Formulaire::afficher_select(DB_STRUCTURE_COMMUN::DB_OPT_besoins_professeur($_SESSION['USER_ID']) , $select_nom=false , $option_first='non' , $selection=false , $optgroup='non');
?>

<ul class="puce">
	<li><span class="manuel"><a class="pop_up" href="<?php echo SERVEUR_DOCUMENTAIRE ?>?fichier=support_professeur__gestion_groupes_besoin">DOC : Gestion des groupes de besoin.</a></span></li>
	<li><span class="astuce">Vous pouvez associer des collègues à vos groupes de besoin, mais pas vous retirer de vos propres groupes de besoin !</span></li>
</ul>

<hr />

<form action="#" method="post">
	<table><tr>
		<td class="nu" style="width:25em">
			<b>Liste des professeurs :</b> <img alt="" src="./_img/bulle_aide.png" title="Utiliser la touche &laquo;&nbsp;Shift&nbsp;&raquo; pour une sélection multiple contiguë.<br />Utiliser la touche &laquo;&nbsp;Ctrl&nbsp;&raquo; pour une sélection multiple non contiguë." /><br />
			<select id="select_professeurs" name="select_professeurs[]" multiple size="10" class="t8"><?php echo $select_professeurs; ?></select>
		</td>
		<td class="nu" style="width:20em">
			<b>Liste des groupes de besoin :</b> <img alt="" src="./_img/bulle_aide.png" title="Utiliser la touche &laquo;&nbsp;Shift&nbsp;&raquo; pour une sélection multiple contiguë.<br />Utiliser la touche &laquo;&nbsp;Ctrl&nbsp;&raquo; pour une sélection multiple non contiguë." /><br />
			<select id="select_groupes" name="select_groupes[]" multiple size="10" class="t8"><?php echo $select_groupe_besoin; ?></select>
		</td>
		<td class="nu" style="width:25em">
			<button id="ajouter" type="button" class="groupe_ajouter">Ajouter ces associations.</button><br />
			<button id="retirer" type="button" class="groupe_retirer">Retirer ces associations.</button>
			<p><label id="ajax_msg">&nbsp;</label></p>
		</td>
	</tr></table>
</form>

<div id="bilan">
</div>

