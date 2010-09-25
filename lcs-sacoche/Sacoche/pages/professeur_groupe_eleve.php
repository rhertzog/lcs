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
$TITRE = "Affecter les élèves aux groupes de besoin";
$VERSION_JS_FILE += 1;
?>

<?php
// Fabrication des éléments select du formulaire
$select_groupe        = afficher_select(DB_STRUCTURE_OPT_groupes_professeur($_SESSION['USER_ID']) , $select_nom=false , $option_first='oui' , $selection=false , $optgroup='oui');
$select_groupe_besoin = afficher_select(DB_STRUCTURE_OPT_besoins_professeur($_SESSION['USER_ID']) , $select_nom=false , $option_first='non' , $selection=false , $optgroup='non');
?>

<ul class="puce">
	<li><span class="manuel"><a class="pop_up" href="<?php echo SERVEUR_DOCUMENTAIRE ?>?fichier=support_professeur__gestion_groupes_besoin">DOC : Gestion des groupes de besoin.</a></span></li>
	<li><span class="danger">La composition d'un groupe de besoin déjà utilisé lors d'une évaluation ne devrait pas être modifiée (sinon vous n'aurez plus accès à certaines saisies) !</span></li>
</ul>

<hr />

<form action="">
	<table><tr>
		<td class="nu" style="width:25em">
			<b>Liste des élèves :</b><br />
			<select id="f_groupe" name="f_groupe" class="t8"><?php echo $select_groupe ?></select><br />
			<select id="select_users" name="select_users[]" multiple="multiple" size="8" class="t8"><option value=""></option></select>
		</td>
		<td class="nu" style="width:20em">
			<b>Liste des groupes de besoin :</b><br />
			<select id="select_groupes" name="select_groupes[]" multiple="multiple" size="10" class="t8"><?php echo $select_groupe_besoin; ?></select>
		</td>
		<td class="nu" style="width:25em">
			<p><span class="astuce">Utiliser "<span class="i">Shift + clic</span>" ou "<span class="i">Ctrl + clic</span>"<br />pour une sélection multiple.</span></p>
			<button id="ajouter" type="button"><img alt="" src="./_img/bouton/groupe_ajouter.png" /> Ajouter ces associations.</button><br />
			<button id="retirer" type="button"><img alt="" src="./_img/bouton/groupe_retirer.png" /> Retirer ces associations.</button>
			<p><label id="ajax_msg">&nbsp;</label></p>
		</td>
	</tr></table>
</form>

<div id="bilan">
</div>
