<?php
/**
 * @version $Id$
 * @author Thomas Crespin <thomas.crespin@sesamath.net>
 * @copyright Thomas Crespin 2009-2015
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

// ////////////////////////////////////////////////////////////////////////////////////////////////////
//  Lister les stats des établissements du serveur Sésamath ayant une connexion ENT compatible avec celle du partenaire conventionné.
// ////////////////////////////////////////////////////////////////////////////////////////////////////

$num  = (isset($_POST['num'])) ? (int)$_POST['num'] : 0 ;  // Numéro de l'étape en cours
$max  = (isset($_POST['max'])) ? (int)$_POST['max'] : 0 ;  // Nombre d'étapes à effectuer

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// 1/3 : Récupération de la liste des structures avant collecte des stats
// ////////////////////////////////////////////////////////////////////////////////////////////////////

if((!$num)||(!$max))
{
  // Pour mémoriser les totaux
  $_SESSION['tmp']['totaux'] = array( 'personnel_use'=>0 , 'eleve_use'=>0 , 'evaluation_use'=>0 , 'validation_use'=>0 );
  // Mémoriser les données des structures concernées par les stats
  $_SESSION['tmp']['infos'] = array();
  $DB_TAB = DB_WEBMESTRE_WEBMESTRE::DB_lister_structures();
  foreach($DB_TAB as $DB_ROW)
  {
    $_SESSION['tmp']['infos'][] = array(
      'base_id'   => $DB_ROW['sacoche_base'],
      'structure' => $DB_ROW['structure_uai'].' '.$DB_ROW['structure_denomination'],
      'geo'       => $DB_ROW['geo_nom'],
    );
  }
  // Retour
  $max = count($DB_TAB) + 1 ; // La dernière étape consistera à vider la session temporaire et à renvoyer les totaux
  exit('ok-'.$max);
}

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// 2/3 : Etape de récupération des stats
// ////////////////////////////////////////////////////////////////////////////////////////////////////

if( $num && $max && ($num<$max) )
{
  // Récupérer les infos ($base_id $structure $geo)
  extract($_SESSION['tmp']['infos'][$num-1]);
  // Récupérer une série de stats
  charger_parametres_mysql_supplementaires($base_id);
  list($personnel_use,$eleve_use,$evaluation_use,$validation_use,$connexion_nom) = DB_STRUCTURE_WEBMESTRE::DB_recuperer_statistiques( FALSE /*info_user_nb*/ , TRUE /*info_user_use*/ , FALSE /*info_action_nb*/ , TRUE /*info_action_use*/ , TRUE /*info_connexion*/ );
  if( mb_strpos( $_SESSION['USER_CONNECTEURS'] , '|'.$connexion_nom.',' ) !== FALSE )
  {
    // maj les totaux
    $_SESSION['tmp']['totaux']['personnel_use']  += $personnel_use;
    $_SESSION['tmp']['totaux']['eleve_use']      += $eleve_use;
    $_SESSION['tmp']['totaux']['evaluation_use'] += $evaluation_use;
    $_SESSION['tmp']['totaux']['validation_use'] += $validation_use;
    // Retour
    $ligne_etabl = '<tr>'.
      '<td>'.html($geo).'</td>'.
      '<td>'.html($structure).'</td>'.
      '<td>'.html($connexion_nom).'</td>'.
      '<td>'.$personnel_use.'</td>'.
      '<td>'.$eleve_use.'</td>'.
      '<td>'.sprintf("%07u",$evaluation_use).'</i>'.number_format($evaluation_use,0,'',' ').'</td>'.
      '<td>'.sprintf("%07u",$validation_use).'</i>'.number_format($validation_use,0,'',' ').'</td>'.
      '</tr>';
    exit('ok-'.$ligne_etabl);
  }
  else
  {
    exit('ok-');
  }
}

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// 3/3 : Finalisation (totaux)
// ////////////////////////////////////////////////////////////////////////////////////////////////////

if( $num && $max && ($num==$max) )
{
  $ligne_total = '<tr>'.
    '<th colspan="3" class="nu">Totaux</th>'.
    '<th class="hc">'.number_format($_SESSION['tmp']['totaux']['personnel_use'] ,0,'',' ').'</th>'.
    '<th class="hc">'.number_format($_SESSION['tmp']['totaux']['eleve_use']     ,0,'',' ').'</th>'.
    '<th class="hc">'.number_format($_SESSION['tmp']['totaux']['evaluation_use'],0,'',' ').'</th>'.
    '<th class="hc">'.number_format($_SESSION['tmp']['totaux']['validation_use'],0,'',' ').'</th>'.
    '</tr>';
  unset($_SESSION['tmp']);
  exit('ok-'.$ligne_total);
}

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// On ne devrait pas en arriver là...
// ////////////////////////////////////////////////////////////////////////////////////////////////////

exit('Erreur avec les données transmises !');

?>
