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
if($_SESSION['SESAMATH_ID']==ID_DEMO){exit('Action désactivée pour la démo...');}

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// Récupération des informations transmises
// ////////////////////////////////////////////////////////////////////////////////////////////////////

$f_objet   = (isset($_POST['f_objet']))   ? Clean::texte($_POST['f_objet'])   : '';
$f_profils = (isset($_POST['f_profils'])) ? Clean::texte($_POST['f_profils']) : 'erreur';

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// Vérification des informations transmises
// ////////////////////////////////////////////////////////////////////////////////////////////////////

// Tableau avec les sigles des profils pouvant être proposés
$tab_profils_possibles = array();
$tab_profils_possibles['dir_pers_pp']  = array(                  'DIR','ENS','IEX','ONLY_PP','DOC','EDU','AED','SUR','ORI','MDS','ADF');
$tab_profils_possibles['dir_pers_lv']  = array(                  'DIR','ENS','IEX','ONLY_LV','DOC','EDU','AED','SUR','ORI','MDS','ADF');
$tab_profils_possibles['pers_coord']   = array(                        'ENS','IEX',          'DOC','EDU','AED','SUR','ORI','MDS','ADF','ONLY_COORD');
$tab_profils_possibles['tous']         = array('ELV','TUT','AVS','DIR','ENS','IEX',          'DOC','EDU','AED','SUR','ORI','MDS','ADF');
$tab_profils_possibles['parent_eleve'] = array('ELV','TUT','AVS');

$tab_objet_profils = array();
$tab_objet_profils['droit_affecter_langue']    = $tab_profils_possibles['dir_pers_lv'];
$tab_objet_profils['droit_validation_entree']  = $tab_profils_possibles['dir_pers_pp'];
$tab_objet_profils['droit_validation_pilier']  = $tab_profils_possibles['dir_pers_pp'];
$tab_objet_profils['droit_annulation_pilier']  = $tab_profils_possibles['dir_pers_pp'];
$tab_objet_profils['droit_gerer_referentiel']  = $tab_profils_possibles['pers_coord'];
$tab_objet_profils['droit_gerer_ressource']    = $tab_profils_possibles['pers_coord'];
$tab_objet_profils['droit_modifier_mdp']                          = $tab_profils_possibles['tous'];
$tab_objet_profils['droit_voir_algorithme']                       = $tab_profils_possibles['tous'];
$tab_objet_profils['droit_voir_etat_acquisition_avec_evaluation'] = $tab_profils_possibles['tous'];
$tab_objet_profils['droit_voir_grilles_items']                    = $tab_profils_possibles['tous'];
$tab_objet_profils['droit_voir_referentiels']                     = $tab_profils_possibles['tous'];
$tab_objet_profils['droit_voir_score_bilan']                      = $tab_profils_possibles['tous'];
$tab_objet_profils['droit_releve_etat_acquisition']   = $tab_profils_possibles['parent_eleve'];
$tab_objet_profils['droit_releve_moyenne_score']      = $tab_profils_possibles['parent_eleve'];
$tab_objet_profils['droit_releve_pourcentage_acquis'] = $tab_profils_possibles['parent_eleve'];
$tab_objet_profils['droit_releve_conversion_sur_20']  = $tab_profils_possibles['parent_eleve'];
$tab_objet_profils['droit_socle_acces']               = $tab_profils_possibles['parent_eleve'];
$tab_objet_profils['droit_socle_pourcentage_acquis']  = $tab_profils_possibles['parent_eleve'];
$tab_objet_profils['droit_socle_etat_validation']     = $tab_profils_possibles['parent_eleve'];
$tab_objet_profils['droit_officiel_saisir_assiduite']               = $tab_profils_possibles['dir_pers_pp'];
$tab_objet_profils['droit_officiel_releve_modifier_statut']         = $tab_profils_possibles['dir_pers_pp'];
$tab_objet_profils['droit_officiel_releve_corriger_appreciation']   = $tab_profils_possibles['dir_pers_pp'];
$tab_objet_profils['droit_officiel_releve_appreciation_generale']   = $tab_profils_possibles['dir_pers_pp'];
$tab_objet_profils['droit_officiel_releve_impression_pdf']          = $tab_profils_possibles['dir_pers_pp'];
$tab_objet_profils['droit_officiel_bulletin_modifier_statut']       = $tab_profils_possibles['dir_pers_pp'];
$tab_objet_profils['droit_officiel_bulletin_corriger_appreciation'] = $tab_profils_possibles['dir_pers_pp'];
$tab_objet_profils['droit_officiel_bulletin_appreciation_generale'] = $tab_profils_possibles['dir_pers_pp'];
$tab_objet_profils['droit_officiel_bulletin_impression_pdf']        = $tab_profils_possibles['dir_pers_pp'];
$tab_objet_profils['droit_officiel_socle_modifier_statut']          = $tab_profils_possibles['dir_pers_pp'];
$tab_objet_profils['droit_officiel_socle_corriger_appreciation']    = $tab_profils_possibles['dir_pers_pp'];
$tab_objet_profils['droit_officiel_socle_appreciation_generale']    = $tab_profils_possibles['dir_pers_pp'];
$tab_objet_profils['droit_officiel_socle_impression_pdf']           = $tab_profils_possibles['dir_pers_pp'];
$tab_objet_profils['droit_fiche_brevet_modifier_statut']            = $tab_profils_possibles['dir_pers_pp'];
$tab_objet_profils['droit_fiche_brevet_corriger_appreciation']      = $tab_profils_possibles['dir_pers_pp'];
$tab_objet_profils['droit_fiche_brevet_appreciation_generale']      = $tab_profils_possibles['dir_pers_pp'];
$tab_objet_profils['droit_fiche_brevet_impression_pdf']             = $tab_profils_possibles['dir_pers_pp'];
$tab_objet_profils['droit_officiel_releve_voir_archive']   = $tab_profils_possibles['tous'];
$tab_objet_profils['droit_officiel_bulletin_voir_archive'] = $tab_profils_possibles['tous'];
$tab_objet_profils['droit_officiel_socle_voir_archive']    = $tab_profils_possibles['tous'];
$tab_objet_profils['droit_fiche_brevet_voir_archive']      = $tab_profils_possibles['tous'];

if(!isset($tab_objet_profils[$f_objet]))
{
  exit('Droit inconnu !');
}

$tab_profils_transmis  = ($f_profils) ? explode(',',$f_profils) : array() ;
$tab_profils_possibles = $tab_objet_profils[$f_objet];
$tab_profils_inconnus  = array_diff($tab_profils_transmis,$tab_profils_possibles);
if(count($tab_profils_inconnus))
{
  exit('Profils incohérents !');
}

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// Appliquer la modification demandée
// ////////////////////////////////////////////////////////////////////////////////////////////////////

DB_STRUCTURE_COMMUN::DB_modifier_parametres( array($f_objet=>$f_profils) );
// ne pas oublier de mettre aussi à jour la session
$_SESSION[strtoupper($f_objet)] = $f_profils;
exit('ok');

?>
