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
if(($_SESSION['SESAMATH_ID']==ID_DEMO)&&($_GET['action']!='initialiser')){exit('Action désactivée pour la démo...');}

$action = (isset($_GET['action']))   ? $_GET['action']                 : '';
$serie  = (isset($_POST['f_serie'])) ? Clean::texte($_POST['f_serie']) : '' ;
// Avant c'était un tableau qui est transmis, mais à cause d'une limitation possible "suhosin" / "max input vars", on est passé à une concaténation en chaine...
$tab_eleve = (isset($_POST['f_eleve'])) ? ( (is_array($_POST['f_eleve'])) ? $_POST['f_eleve'] : explode(',',$_POST['f_eleve']) ) : array() ;
$tab_eleve = array_filter( Clean::map_entier($tab_eleve) , 'positif' );

// Lister les séries de Brevet
$tab_brevet_series = array();
$DB_TAB = DB_STRUCTURE_BREVET::DB_OPT_brevet_series();
foreach($DB_TAB as $DB_ROW)
{
  $tab_brevet_series[$DB_ROW['valeur']] = html($DB_ROW['texte']);
}

//
// Modifier des associations
//

if($action=='associer')
{
  // liste des élèves
  $listing_user_id = implode(',',$tab_eleve);
  if(!$listing_user_id)
  {
    exit('Erreur : élève(s) non récupéré(s) !');
  }
  // serie
  if( (!$serie) || (!isset($tab_brevet_series[$serie])) )
  {
    exit('Erreur : serie non transmise ou incorrecte !');
  }
  // go
  DB_STRUCTURE_BREVET::DB_modifier_user_brevet_serie($listing_user_id,$serie);
}

//
// Affichage du bilan des affectations des series aux élèves
//

$tab_niveau_groupe = array();
$tab_user          = array();
$tab_niveau_groupe[0][0] = 'sans classe';
$tab_user[0]             = '';

// Récupérer la liste des classes
$DB_TAB = DB_STRUCTURE_ADMINISTRATEUR::DB_lister_classes_avec_niveaux($niveau_ordre='DESC');
foreach($DB_TAB as $DB_ROW)
{
  $tab_niveau_groupe[$DB_ROW['niveau_id']][$DB_ROW['groupe_id']] = html($DB_ROW['groupe_nom']);
  $tab_user[$DB_ROW['groupe_id']] = '';
}
// Récupérer la liste des élèves / classes
$DB_TAB = DB_STRUCTURE_ADMINISTRATEUR::DB_lister_users( 'eleve' , 1 /*only_actuels*/ , 'eleve_classe_id,eleve_brevet_serie,user_nom,user_prenom' /*liste_champs*/ , FALSE /*with_classe*/ );
foreach($DB_TAB as $DB_ROW)
{
  $tab_user[$DB_ROW['eleve_classe_id']] .= '<img src="./_img/brevet/serie_'.$DB_ROW['eleve_brevet_serie'].'.png" alt="" title="'.$tab_brevet_series[$DB_ROW['eleve_brevet_serie']].'" /> '.html($DB_ROW['user_nom'].' '.$DB_ROW['user_prenom']).'<br />';
}
// Assemblage du tableau résultant
$TH = array();
$TB = array();
$TF = array();
foreach($tab_niveau_groupe as $niveau_id => $tab_groupe)
{
  $TH[$niveau_id] = '';
  $TB[$niveau_id] = '';
  $TF[$niveau_id] = '';
  foreach($tab_groupe as $groupe_id => $groupe_nom)
  {
    $nb = mb_substr_count($tab_user[$groupe_id],'<br />','UTF-8');
    $s = ($nb>1) ? 's' : '' ;
    $TH[$niveau_id] .= '<th>'.$groupe_nom.'</th>';
    $TB[$niveau_id] .= '<td>'.mb_substr($tab_user[$groupe_id],0,-6,'UTF-8').'</td>';
    $TF[$niveau_id] .= '<td>'.$nb.' élève'.$s.'</td>';
  }
}
echo'<hr />'.NL;
foreach($tab_niveau_groupe as $niveau_id => $tab_groupe)
{
  if(mb_strlen($TB[$niveau_id])>9)
  {
    echo'<table class="affectation">'.NL;
    echo  '<thead><tr>'.$TH[$niveau_id].'</tr></thead>'.NL;
    echo  '<tbody><tr>'.$TB[$niveau_id].'</tr></tbody>'.NL;
    echo  '<tfoot><tr>'.$TF[$niveau_id].'</tr></tfoot>'.NL;
    echo'</table>'.NL;
  }
}
?>
