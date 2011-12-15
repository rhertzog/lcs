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
$TITRE = "Nettoyage / Initialisation";
?>

<p><span class="manuel"><a class="pop_up" href="<?php echo SERVEUR_DOCUMENTAIRE ?>?fichier=support_administrateur__gestion_nettoyage">DOC : Nettoyage et initialisation annuelle de la base</a></span></p>

<hr />

<h2>Recherche et correction d'anomalies de numérotation</h2>

<div class="astuce">Cet outil n'est utile que si une anomalie spécifique a été constaté (voir documentation ci-dessus).</div>
<form action="#" method="post" id="form_numeroter"><fieldset>
	<span class="tab"></span><button id="bouton_numeroter" type="button" class="nettoyage">Lancer le nettoyage d'éventuelles anomalies de numérotation.</button><label id="ajax_msg_numeroter">&nbsp;</label>
</fieldset></form>

<hr />

<h2>Recherche et suppression de correspondances anormales</h2>

<div class="astuce">Cet outil est facultatif ; il ne met pas à jour la structure ni les données de la base (voir documentation ci-dessus).</div>
<form action="#" method="post" id="form_nettoyer"><fieldset>
	<span class="tab"></span><button id="bouton_nettoyer" type="button" class="nettoyage">Lancer le nettoyage d'éventuelles correspondances anormales.</button><label id="ajax_msg_nettoyer">&nbsp;</label>
</fieldset></form>

<hr />

<h2>Initialisation annuelle des données</h2>

<div class="astuce">Entre deux années scolaires, il faut purger la base avant d'importer les nouveaux utilisateurs (voir documentation ci-dessus).</div>
<div class="danger">N'effectuez jamais une initialisation en cours d'année scolaire !</div>
<form action="#" method="post" id="form_purger"><fieldset>
	<span class="tab"></span><button id="bouton_purger" type="button" class="nettoyage">Lancer l'initialisation annuelle des données.</button><label id="ajax_msg_purger">&nbsp;</label>
</fieldset></form>

<hr />

<h2>Suppression des notes et des validations</h2>

<div class="danger">Cet fonctionnalité ne doit être utilisée qu'exceptionnellement, pour repartir à zéro après une année de test (voir documentation ci-dessus) !</div>
<form action="#" method="post" id="form_supprimer"><fieldset>
	<span class="tab"></span><button id="bouton_supprimer" type="button" class="nettoyage">Supprimer les scores d'évaluations et les validations.</button><label id="ajax_msg_supprimer">&nbsp;</label>
</fieldset></form>

<hr />

<h2>Effacement des étiquettes nom &amp; prénom</h2>

<div class="astuce">Cet outil n'est utile que si une anomalie spécifique a été constaté (voir documentation ci-dessus).</div>
<form action="#" method="post" id="form_effacer"><fieldset>
	<span class="tab"></span><button id="bouton_effacer" type="button" class="nettoyage">Effacer les étiquettes nom &amp; prénom.</button><label id="ajax_msg_effacer">&nbsp;</label>
</fieldset></form>

<hr />

<ul class="puce hide" id="ajax_info">
	<li></li>
</ul>
<p>&nbsp;</p>