<?php
/**
 * @version $Id$
 * @author Thomas Crespin <thomas.crespin@sesamath.net>
 * @copyright Thomas Crespin 2010-2014
 * 
 * ****************************************************************************************************
 * SACoche <http://sacoche.sesamath.net> - Suivi d'Acquisitions de Compétences
 * © Thomas Crespin pour Sésamath <http://www.sesamath.net> - Tous droits réservés.
 * Logiciel placé sous la licence libre Affero GPL 3 <https://www.gnu.org/licenses/agpl-3.0.html>.
 * ****************************************************************************************************
 * 
 * Ce fichier est une partie de SACoche.
 * 
 * SACoche est un logiciel libre ; vous pouvez le redistribuer ou le modifier suivant les termes 
 * de la “GNU Affero General Public License” telle que publiée par la Free Software Foundation :
 * soit la version 3 de cette licence, soit (à votre gré) toute version ultérieure.
 * 
 * SACoche est distribué dans l’espoir qu’il vous sera utile, mais SANS AUCUNE GARANTIE :
 * sans même la garantie implicite de COMMERCIALISABILITÉ ni d’ADÉQUATION À UN OBJECTIF PARTICULIER.
 * Consultez la Licence Publique Générale GNU Affero pour plus de détails.
 * 
 * Vous devriez avoir reçu une copie de la Licence Publique Générale GNU Affero avec SACoche ;
 * si ce n’est pas le cas, consultez : <http://www.gnu.org/licenses/>.
 * 
 */

if(!defined('SACoche')) {exit('Ce fichier ne peut être appelé directement !');}
$TITRE = "Valider les compétences (piliers) du socle";

if(!test_user_droit_specifique( $_SESSION['DROIT_VALIDATION_PILIER'] , NULL /*matiere_coord_or_groupe_pp_connu*/ , 0 /*matiere_id_or_groupe_id_a_tester*/ ))
{
  echo'<p class="danger">Vous n\'êtes pas habilité à accéder à cette fonctionnalité !</p>'.NL;
  echo'<div class="astuce">Profils autorisés (par les administrateurs) :</div>'.NL;
  echo afficher_profils_droit_specifique($_SESSION['DROIT_VALIDATION_PILIER'],'li');
  return; // Ne pas exécuter la suite de ce fichier inclus.
}

// Remarque : on ne peut être pp que d'une classe, pas d'un groupe, donc si seuls les PP ont un accès parmi les profs, ils ne peuvent trier les élèves que par classes

Form::load_choix_memo();

if($_SESSION['USER_PROFIL_TYPE']=='directeur')
{
  $tab_groupes = DB_STRUCTURE_COMMUN::DB_OPT_classes_groupes_etabl();
  $of_g = '';
}
elseif($_SESSION['USER_PROFIL_TYPE']=='professeur')
{
  if(test_droit_specifique_restreint($_SESSION['DROIT_VALIDATION_PILIER'],'ONLY_PP'))
  {
    $tab_groupes = DB_STRUCTURE_COMMUN::DB_OPT_classes_prof_principal($_SESSION['USER_ID']);
    $of_g = FALSE;
  }
  else
  {
    $tab_groupes = ($_SESSION['USER_JOIN_GROUPES']=='config') ? DB_STRUCTURE_COMMUN::DB_OPT_groupes_professeur($_SESSION['USER_ID']) : DB_STRUCTURE_COMMUN::DB_OPT_classes_groupes_etabl() ;
    $of_g = '';
  }
}
$tab_paliers = DB_STRUCTURE_COMMUN::DB_OPT_paliers_etabl();
$of_p = (count($tab_paliers)<2) ? FALSE : '' ;

$select_palier = Form::afficher_select($tab_paliers , 'f_palier' /*select_nom*/ , $of_p /*option_first*/ , Form::$tab_choix['palier_id'] /*selection*/ ,              '' /*optgroup*/);
$select_groupe = Form::afficher_select($tab_groupes , 'f_groupe' /*select_nom*/ , $of_g /*option_first*/ , FALSE                         /*selection*/ , 'regroupements' /*optgroup*/);
?>

<ul class="puce">
  <li><span class="manuel"><a class="pop_up" href="<?php echo SERVEUR_DOCUMENTAIRE ?>?fichier=referentiels_socle__socle_valider_pilier">DOC : Valider des compétences du socle.</a></span></li>
</ul>

<hr />

<form action="#" method="post" id="zone_choix"><fieldset>
  <label class="tab" for="f_palier">Palier :</label><?php echo $select_palier ?><label id="ajax_maj_pilier">&nbsp;</label><br />
  <span id="bloc_pilier" class="hide"><label class="tab" for="f_pilier">Compétence(s) :</label><span id="f_pilier" class="select_multiple"></span><span class="check_multiple"><q class="cocher_tout" title="Tout cocher."></q><br /><q class="cocher_rien" title="Tout décocher."></q></span></span>
  <p>
    <label class="tab" for="f_groupe">Classe / groupe :</label><?php echo $select_groupe ?><input type="hidden" id="f_groupe_type" name="f_groupe_type" value="" /><label id="ajax_maj_eleve">&nbsp;</label><br />
    <span id="bloc_eleve" class="hide"><label class="tab" for="f_eleve">Élève(s) :</label><span id="f_eleve" class="select_multiple"></span><span class="check_multiple"><q class="cocher_tout" title="Tout cocher."></q><br /><q class="cocher_rien" title="Tout décocher."></q></span></span>
  </p>
  <span class="tab"></span><input type="hidden" name="f_action" value="Afficher_bilan" /><button id="Afficher_validation" type="submit" class="valider">Afficher le tableau des validations.</button><label id="ajax_msg_choix">&nbsp;</label>
</fieldset></form>

<form action="#" method="post" id="zone_validation" class="hide">
  <table id="tableau_validation">
    <tbody><tr><td></td></tr></tbody>
  </table>
</form>

<div id="zone_information" class="hide" style="height:60ex">
  <h3>Aide à la décision : états de validation des items d'une compétence du socle</h3>
  <ul class="puce">
    <li><span id="identite" class="socle_info eleve"></span></li>
    <li><span id="pilier" class="socle_info socle_n1"></span></li>
    <li><span id="stats" class="socle_info stats"></span><label id="ajax_msg_information"></label></li>
  </ul>
  <div id="items">
  </div>
</div>

<div id="zone_confirmer_fermer_validation" class="hide">
  <p class="danger">Des modifications ont été effectuées, mais n'ont pas été enregistrées.</p>
  <p>Confirmez-vous vouloir quitter l'interface de saisie ?</p>
  <p>
    <button id="confirmer_fermer_zone_validation" type="button" class="valider">Oui, je ne veux pas enregistrer</button>
    <button id="annuler_fermer_zone_validation" type="button" class="annuler">Non, je reste sur l'interface</button>
  </p>
</div>
