<?php
/**
 *
 *
 * @version $Id: saisir_ajax.php 3028 2009-04-06 21:28:42Z jjocal $
 *
 * Copyright 2001, 2007 Thomas Belliard, Laurent Delineau, Eric Lebrun, Stephane Boireau, Julien Jocal
 *
 * This file is part of GEPI.
 *
 * GEPI is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * GEPI is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with GEPI; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */


// Initialisation des feuilles de style apr�s modification pour am�liorer l'accessibilit�
$accessibilite="y";

// Initialisations files
include("../lib/initialisationsPropel.inc.php");
require_once("../lib/initialisations.inc.php");
// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
    header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
}

// ==================== VARIABLES ===============================
$_id        = isset($_POST["_id"]) ? $_POST["_id"] : NULL;
$type       = isset($_POST["type"]) ? $_POST["type"] : NULL;
$_droits    = isset($_POST["droits"]) ? $_POST["droits"] : NULL; // permet de d�fnir ce qui est affich� en fonction des droits
$_ok        = 'oui';
$aff_coche  = '';
$test_afffiche = "ok";
$aff_creneaux_deb = $aff_creneaux_fin = NULL;
$aff_motifs = $aff_justifications = NULL;
$aff_liste  = array ();

// +++++++++++++++++++++ Code m�tier ++++++++++++++++++++++++++++
include("lib/erreurs.php");
include('../orm/helpers/AbsencesParametresHelper.php');

try{

  // Quelle est la p�riode active actuellement
  $periode = 2;

  // On teste $_id pour savoir s'il renvoie qu'un seul num�ro d'�l�ve ou plusieurs
  $test_id = explode(",", $_id);
  $test_nbre = count($test_id);
  if ($test_nbre >= 2) {
    $test_aff_fiche = 'no'; // Il y a plus d'un �l�ve appel�, donc, pas de fiche �l�ve
  }

  $test_type = substr($type, 6); // permet de savoir quel type d'info il faut renvoyer

  switch($test_type){
    case 'aid':
      $liste = 'AID';
      $c_aid = new Criteria();
      $c_aid->addAscendingOrderByColumn(ElevePeer::NOM);
      $test_liste = AidDetailsPeer::retrieveByPK($_id);
      $aff_liste = $test_liste->getJAidElevessJoinEleve($c_aid);
      //aff_debug($test_liste);exit();
      $test_type = 'AID : ' . $test_liste->getNom();
      break;
    case 'groupe':
      $liste = 'GRP';
      $criteres_groupes = new Criteria();
      $criteres_groupes->add(JEleveGroupePeer::PERIODE, $periode);
      $test_liste = GroupePeer::retrieveByPK($_id);
      $aff_liste = $test_liste->getJEleveGroupesJoinEleve($criteres_groupes);
      //aff_debug($test_liste->getJEleveGroupesJoinEleve($criteres_groupes));exit();
      $test_type = 'Enseignement : ' . $test_liste->getDescriptionAvecClasses();//$test_type = 'Classe : ' . $test_liste[0]->getGroupe()->getNameAvecClasses();
      break;
    case 'dEleves':
      $liste = $_id;
         // On r�cup�re les infos sur tous les �l�ves s�lectionn�s (qu'il y en ait un ou plusieurs)
      $aff_coche = ' checked="checked"';
      $aff_liste = ElevePeer::retrieveByPKs($test_id);
      break;
    case 'classe':
      $liste = 'CLA';
      $c_cla = new Criteria();
      $c_cla->add(JEleveClassePeer::PERIODE, $periode);
      $c_cla->addAscendingOrderByColumn(ElevePeer::NOM);
      $test_liste = ClassePeer::retrieveByPK($_id);
      $aff_liste = $test_liste->getJEleveClassesJoinEleve($c_cla);
      //aff_debug($test_liste);exit();
      $test_type = 'CLASSE : ' . $test_liste->getNomComplet();
      break;
    default:
      $liste = '';
  } // switch


  // *********************************************************************************** //
  // ************** CRENEAUX : On cr�e les options pour les selects des cr�neaux ******* //
  $critere = new Criteria();
  $critere->add(CreneauPeer::TYPE_CRENEAU, 'pause', Criteria::NOT_EQUAL);
  $critere->addAscendingOrderByColumn(CreneauPeer::DEBUT_CRENEAU);
  $liste_creneaux = CreneauPeer::doSelect($critere);

  foreach($liste_creneaux as $creneaux){
    // On d�termine le selected
    $heure_actu = mktime(date("H"), date("i"), date("s"), 1, 1, 1970) + 3600;
    if ($creneaux->getDebutCreneau() <= $heure_actu AND $creneaux->getFinCreneau() >= $heure_actu){
      $selected = ' selected="selected" ';
    }else{
      $selected = '';
    }
    $aff_creneaux_deb .= '<option value="' . $creneaux->getId() . '"'.$selected.'>' . $creneaux->getNomCreneau() . ' <span class="gras">'.(date("H:i", $creneaux->getDebutCreneau() - 3600)).'</span></option>'."\n";
    $aff_creneaux_fin .= '<option value="' . $creneaux->getId() . '"'.$selected.'>' . $creneaux->getNomCreneau() . ' <span class="gras">'.(date("H:i", $creneaux->getFinCreneau() - 3600)).'</span></option>'."\n";
  }
  // ****************************** Fin de la liste des cr�neaux *********************** //
  // *********************************************************************************** //



}catch(exception $e){
  // Cette fonction est pr�sente dans /lib/erreurs.php
  affExceptions($e);
}
// On pr�cise l'ent�te HTML pour que le navigateur ne se perde pas .
header('Content-Type: text/html; charset:iso-8859-1');
?>

<div id="div_saisie_abs" style="border: 1px solid grey; height: 200%; margin: 5px 5px 5px 5px; padding: 5px 5px 5px 5px; background-color: #99FFFF;">

  <form method="post" action="saisir_enregistrer.php">

    <p><input type="hidden" name="action" value="eleves" /></p>
    <p><input type="submit" name="enregistrer_absences" value="Enregistrer" /> - <?php echo $test_type; ?></p>
    <table class="_center">
      <tr><th>Absents</th><th>Nom Pr&eacute;nom</th><th>Abs. Journ.</th><th>D&eacute;but</th><th>Fin</th><th>Type</th><th>Justification</th><th>Motif</th></tr>

      <?php $a = 0;

        foreach($aff_liste as $eleve):

            if ($liste == 'GRP'){$eleve = $eleve->getEleve();}
            if ($liste == 'AID'){$eleve = $eleve->getEleve();}
            if ($liste == 'CLA'){$eleve = $eleve->getEleve();}
            //aff_debug($eleve->getJEleveClasses());exit();
            $classes = $eleve->getJEleveClasses();
            $classe = isset($classes[0]) ? $classes[0]->getClasse() : NULL;
            $options_j = array('id'=>'idJustif'.$a, 'name'=>'_justifications['.$a.']');
            $aff_justifications = AbsencesParametresHelper::AfficherListeDeroulanteJustifications($options_j);
            $options_m = array ('id'=>'idMotif'.$a, 'name'=>'_motifs['.$a.']');
            $aff_motifs = AbsencesParametresHelper::AfficherListeDeroulanteMotifs($options_m);
            $options_t = array('id'=>'idType'.$a, 'name'=>'_types['.$a.']', 'selected'=>'1'); // par d�faut, c'est le type 1 qui est s�lectionn� (absence)
            $aff_types  = AbsencesParametresHelper::AfficherListeDeroulanteTypes($options_t);


            // Gestion des droits pour l'affichage
            if ($_droits == "professeur"){
              // Pour un professeur, on n'affiche pas les motifs ni les justifications, ni les types.
              $aff_motifs = $aff_justifications = $aff_types = NULL;
            }

      ?>
        
        <tr>

          <td><input type="checkbox" name="_eleve[<?php echo $a; ?>]" id="el<?php echo $a; ?>" value="<?php echo $eleve->getIdEleve(); ?>"<?php echo $aff_coche; ?> /></td>
          <td><label for="el<?php echo $a; ?>"><?php echo htmlentities($eleve->getNom()) . ' ' . htmlentities($eleve->getPrenom()); ?> (<?php  if (is_a($classe, "Classe")){echo $classe->getClasse();} ?>)</label></td>
          <td><input type="checkbox" name="_jourentier[<?php echo $a; ?>]" id="j<?php echo $a; ?>" value="ok"<?php echo $aff_coche; ?> /></td>
          <td><select name="_deb[<?php echo $a; ?>]" id="decod<?php echo $a; ?>"><?php echo $aff_creneaux_deb; ?></select></td>
          <td><select name="_fin[<?php echo $a; ?>]" id="decof<?php echo $a; ?>"><?php echo $aff_creneaux_fin; ?></select></td>
          <td><?php echo $aff_types; ?>
          <td><?php echo $aff_justifications; ?></td>
          <td><?php echo $aff_motifs; ?></td>

        </tr>

      <?php $a++; endforeach; ?>

    </table>

    <p><input type="submit" name="enregistrer_absences" value="Enregistrer" /></p>

  </form>

</div>

