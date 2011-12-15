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
$TITRE = "Identité de l'établissement";
?>

<form action="#" method="post" id="form_instance">

	<div><span class="manuel"><a class="pop_up" href="<?php echo SERVEUR_DOCUMENTAIRE ?>?fichier=support_administrateur__gestion_informations_structure">DOC : Gestion de l'identité de l'établissement</a></span></div>

	<hr />

	<h2>Données saisies par le webmestre</h2>

	<p>
		<label class="tab" for="f_uai">Code UAI (ex-RNE) :</label><input id="f_uai" name="f_uai" size="8" type="text" value="<?php echo html($_SESSION['UAI']); ?>" disabled /><br />
		<label class="tab" for="f_denomination">Dénomination :</label><input id="f_denomination" name="f_denomination" size="50" type="text" value="<?php echo html($_SESSION['DENOMINATION']); ?>" disabled />
	</p>
	<ul class="puce"><li>En cas d'erreur, <?php echo mailto(WEBMESTRE_COURRIEL,'Modifier données SACoche '.$_SESSION['BASE'],'contactez le webmestre'); ?> responsable de <em>SACoche</em> sur ce serveur.</li></ul>

	<hr />

	<h2>Identification de l'établissement dans la base Sésamath</h2>

	<p>
		<label class="tab" for="f_sesamath_id">Identifiant <img alt="" src="./_img/bulle_aide.png" title="Valeur non modifiable manuellement.<br />Utilisez le lien ci-dessous." /> :</label><input id="f_sesamath_id" name="f_sesamath_id" size="5" type="text" value="<?php echo html($_SESSION['SESAMATH_ID']); ?>" readonly /><br />
		<label class="tab" for="f_sesamath_uai">Code UAI <img alt="" src="./_img/bulle_aide.png" title="Valeur non modifiable manuellement.<br />Utilisez le lien ci-dessous." /> :</label><input id="f_sesamath_uai" name="f_sesamath_uai" size="8" type="text" value="<?php echo html($_SESSION['SESAMATH_UAI']); ?>" readonly /><br />
		<label class="tab" for="f_sesamath_type_nom">Dénomination <img alt="" src="./_img/bulle_aide.png" title="Valeur non modifiable manuellement.<br />Utilisez le lien ci-dessous." /> :</label><input id="f_sesamath_type_nom" name="f_sesamath_type_nom" size="50" type="text" value="<?php echo html($_SESSION['SESAMATH_TYPE_NOM']); ?>" readonly /><br />
		<label class="tab" for="f_sesamath_key">Clef de contrôle <img alt="" src="./_img/bulle_aide.png" title="Valeur non modifiable manuellement.<br />Utilisez le lien ci-dessous." /> :</label><input id="f_sesamath_key" name="f_sesamath_key" size="35" type="text" value="<?php echo html($_SESSION['SESAMATH_KEY']); ?>" readonly /><br />
		<span class="tab"></span><button id="bouton_valider" type="submit" class="parametre">Valider.</button><label id="ajax_msg_instance">&nbsp;</label>
	</p>
	<ul class="puce"><li><a id="ouvrir_recherche" href="#"><img alt="" src="./_img/find.png" /> Rechercher l'établissement dans la base Sésamath</a> afin de pouvoir échanger ensuite avec le serveur communautaire.</li></ul>

	<hr />

</form>
<form action="#" method="post" id="form_communautaire" class="hide">
	<h2>Rechercher l'établissement dans la base Sésamath</h2>
	<p><button id="rechercher_annuler" type="button" class="annuler">Annuler la recherche.</button></p>
	<p id="f_recherche_mode">
		<label class="tab">Technique :</label><label for="f_mode_geo"><input type="radio" id="f_mode_geo" name="f_mode" value="geo" /> recherche sur critères géographiques</label>&nbsp;&nbsp;&nbsp;<label for="f_mode_uai"><input type="radio" id="f_mode_uai" name="f_mode" value="uai" /> recherche à partir du numéro UAI (ex-RNE)</label>
	</p>
	<fieldset id="f_recherche_geo" class="hide">
		<label class="tab" for="f_geo1">Etape 1/3 :</label><select id="f_geo1" name="f_geo1"><option value=""></option></select><br />
		<label class="tab" for="f_geo2">Etape 2/3 :</label><select id="f_geo2" name="f_geo2"><option value=""></option></select><br />
		<label class="tab" for="f_geo3">Etape 3/3 :</label><select id="f_geo3" name="f_geo3"><option value=""></option></select><br />
	</fieldset>
	<fieldset id="f_recherche_uai" class="hide">
		<label class="tab" for="f_uai2">Code UAI (ex-RNE) :</label><input id="f_uai2" name="f_uai2" size="8" type="text" value="" /><br />
		<span class="tab"></span><button id="rechercher_uai" type="button" class="rechercher">Lancer la recherche.</button>
	</fieldset>
	<ul id="f_recherche_resultat" class="puce p hide">
		<li></li>
	</ul>
	<span class="tab"></span><label id="ajax_msg_communautaire">&nbsp;</label>
</form>
