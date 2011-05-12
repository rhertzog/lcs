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
$TITRE = "Maintenance &amp; mise à jour";
$VERSION_JS_FILE += 1;
?>

<?php
// Initialisation de l'état de l'accès
$fichier_blocage_webmestre = $CHEMIN_CONFIG.'blocage_webmestre_0.txt';
if(is_file($fichier_blocage_webmestre))
{
	$label = '<label class="erreur">Application fermée : '.html(file_get_contents($fichier_blocage_webmestre)).'</label>';
}
else
{
	$label = '<label class="valide">Application accessible.</label>';
}
// Tests de droits suffisants pour la maj automatique
$fichier_test_chemin_tmp = './__tmp/index.htm';
$fichier_test_chemin_new = './_dtd/index.htm';
Ecrire_Fichier($fichier_test_chemin_tmp,'Circulez, il n\'y a rien à voir par ici !');
if( !copy( $fichier_test_chemin_tmp , $fichier_test_chemin_new ) )
{
	$test_droits = '<label class="erreur">Echec lors du test des droits en écriture !</label>';
}
else
{
	$test_droits = '<label class="valide">Réussite lors du test des droits en écriture !</label>';
	unlink($fichier_test_chemin_new);
}
unlink($fichier_test_chemin_tmp);
?>

<hr />

<h2>Version de SACoche</h2>

<ul class="puce">
	<li>Version actuellement installée : <label id="ajax_version_installee"><?php echo VERSION_PROG ?></label></li>
	<li>Dernière version disponible : <span id="ajax_version_disponible" class="astuce"><?php echo recuperer_numero_derniere_version() ?></span></li>
</ul>

<h2>Mise à jour des fichiers</h2>

<p><span class="manuel"><a class="pop_up" href="<?php echo SERVEUR_DOCUMENTAIRE ?>?fichier=support_webmestre__maj">DOC : Mise à jour de l'application</a></span></p>
<p><?php echo $test_droits ?></p>
<form id="form_maj" action=""><fieldset>
	<span class="tab"></span><button id="bouton_maj" type="button"><img alt="" src="./_img/bouton/parametre.png" /> Lancer la mise à jour automatique.</button><label id="ajax_maj">&nbsp;</label>
</fieldset></form>
<ul id="puces_maj" class="puce hide">
	<li></li>
</ul>

<hr />

<h2>État de l'accès actuel</h2>

<p><span class="manuel"><a class="pop_up" href="<?php echo SERVEUR_DOCUMENTAIRE ?>?fichier=environnement_generalites__verrouillage">DOC : Verrouillage de l'application</a></span></p>
<p id="ajax_acces_actuel"><?php echo $label ?></p>

<h2>Verrouillage de l'application</h2>

<form id="form" action=""><fieldset>
	<label for="f_bloquer"><input type="radio" id="f_bloquer" name="f_action" value="bloquer" /> Bloquer l'application</label><br />
	<span id="span_motif" class="hide">
		<label class="tab" for="f_motif">Motif :</label>
			<select id="f_proposition" name="f_proposition">
				<option value="rien">autre motif</option>
				<option value="mise-a-jour" selected>mise à jour</option>
				<option value="maintenance">maintenance</option>
				<option value="demenagement">déménagement</option>
			</select>
			<input id="f_motif" name="f_motif" size="50" maxlength="100" type="text" value="Mise à jour des fichiers en cours." />
	</span>&nbsp;<p />
	<label for="f_debloquer"><input type="radio" id="f_debloquer" name="f_action" value="debloquer" /> Débloquer l'application</label><p />
	<span class="tab"></span><button id="bouton_valider" type="submit"><img alt="" src="./_img/bouton/parametre.png" /> Valider cet état.</button><label id="ajax_msg">&nbsp;</label>
</fieldset></form>

<hr />
