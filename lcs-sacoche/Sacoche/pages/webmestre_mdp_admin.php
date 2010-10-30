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
$TITRE = "Mot de passe administrateur";

// Uniquement pour une installation de type mono-structure ; pour du multi-structures, cela se fait à la page de gestion des établissements.
$select_admin = afficher_select(DB_STRUCTURE_OPT_administrateurs_etabl() , $select_nom=false , $option_first='non' , $selection=false , $optgroup='non');
?>

<hr />

<form action=""><fieldset>
	<label class="tab" for="f_admin">Administrateur :</label><select id="f_admin" name="f_admin" size="5"><?php echo $select_admin ?></select><br />
	<span class="tab"></span><button id="bouton_valider" type="submit"><img alt="" src="./_img/bouton/mdp_perso.png" /> Générer un nouveau mot de passe.</button><label id="ajax_msg">&nbsp;</label><br />
</fieldset></form>

<hr />

<div id="bilan">
</div>
