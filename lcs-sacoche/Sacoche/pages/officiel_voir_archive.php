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
$TITRE = html(Lang::_("Archives consultables des bilans officiels"));

$tab_types = array
(
  'brevet'   => array( 'droit'=>'FICHE_BREVET'      , 'titre'=>'Fiche brevet'          ) ,
  'releve'   => array( 'droit'=>'OFFICIEL_RELEVE'   , 'titre'=>'Relevé d\'évaluations' ) ,
  'bulletin' => array( 'droit'=>'OFFICIEL_BULLETIN' , 'titre'=>'Bulletin scolaire'     ) ,
  'palier1'  => array( 'droit'=>'OFFICIEL_SOCLE'    , 'titre'=>'Maîtrise du palier 1'  ) ,
  'palier2'  => array( 'droit'=>'OFFICIEL_SOCLE'    , 'titre'=>'Maîtrise du palier 2'  ) ,
  'palier3'  => array( 'droit'=>'OFFICIEL_SOCLE'    , 'titre'=>'Maîtrise du palier 3'  ) ,
);

$droit_voir_archives_pdf = FALSE;

foreach($tab_types as $BILAN_TYPE => $tab)
{
  $droit_voir_archives_pdf = $droit_voir_archives_pdf || test_user_droit_specifique($_SESSION['DROIT_'.$tab['droit'].'_VOIR_ARCHIVE']) ;
  if($BILAN_TYPE=='palier1') break; // car droit commun pour tous les paliers
}

if(!$droit_voir_archives_pdf)
{
  echo'<p class="danger">Vous n\'êtes pas habilité à accéder à cette fonctionnalité !</p>'.NL;
  echo'<p class="astuce">Profils autorisés (par les administrateurs) :</p>'.NL;
  foreach($tab_types as $BILAN_TYPE => $tab)
  {
    $titre = ($BILAN_TYPE!='palier1') ? $tab['titre'] : 'Maîtrise du socle' ;
    echo'<h3>'.$titre.'</h3>'.NL;
    echo afficher_profils_droit_specifique($_SESSION['DROIT_'.$tab['droit'].'_VOIR_ARCHIVE'],'li');
    if($BILAN_TYPE=='palier1') break; // car droit commun pour tous les paliers
  }
  return; // Ne pas exécuter la suite de ce fichier inclus.
}

// Récupérer la liste des périodes, dans l'ordre choisi par l'admin.

$DB_TAB = DB_STRUCTURE_ADMINISTRATEUR::DB_lister_periodes();
if(empty($DB_TAB))
{
  echo'<p><label class="erreur">Aucune période n\'a été configurée par les administrateurs !</label></p>'.NL;
  return; // Ne pas exécuter la suite de ce fichier inclus.
}

$annee_session_brevet = annee_session_brevet();

$tab_thead = ($_SESSION['USER_PROFIL_TYPE']=='eleve') ? array(0=>'') : array(0=>'<th class="nu"></th>');
$tab_tbody = array();
foreach($DB_TAB as $DB_ROW)
{
  $tab_thead[$DB_ROW['periode_id']] = '<th class="hc">'.html($DB_ROW['periode_nom']).'</th>';
}

// identifiants élèves concernés
$tab_eleve_id = array();
if($_SESSION['USER_PROFIL_TYPE']=='eleve')
{
  $tab_eleve_id[] = $_SESSION['USER_ID'];
  $tab_tbody[$_SESSION['USER_ID']][0] = '';
}
else
{
  if(!$_SESSION['NB_ENFANTS'])
  {
    echo'<p class="danger">'.$_SESSION['OPT_PARENT_ENFANTS'].'</p>'.NL;
    return; // Ne pas exécuter la suite de ce fichier inclus.
  }
  foreach($_SESSION['OPT_PARENT_ENFANTS'] as $tab)
  {
    $tab_eleve_id[] = $tab['valeur'];
    $tab_tbody[$tab['valeur']][0] = '<th>'.html($tab['texte']).'</th>';
  }
}

// marqueur mis en session pour vérifier que c'est bien cet utilisateur qui veut voir (et à donc le droit de voir) le fichier, car il n'y a pas d'autre vérification de droit ensuite
$_SESSION['tmp_droit_voir_archive'] = array();
// lister les bilans officiels archivés de l'année courante
$DB_TAB = DB_STRUCTURE_OFFICIEL::DB_lister_bilan_officiel_fichiers( '' /*BILAN_TYPE*/ , 0 /*periode_id*/ , $tab_eleve_id );
foreach($DB_TAB as $DB_ROW)
{
  if(test_user_droit_specifique($_SESSION['DROIT_'.$tab_types[$DB_ROW['officiel_type']]['droit'].'_VOIR_ARCHIVE']))
  {
    if(is_file(CHEMIN_DOSSIER_OFFICIEL.$_SESSION['BASE'].DS.fabriquer_nom_fichier_bilan_officiel( $DB_ROW['user_id'] , $DB_ROW['officiel_type'] , $DB_ROW['periode_id'] )))
    {
      $_SESSION['tmp_droit_voir_archive'][$DB_ROW['user_id'].$DB_ROW['officiel_type']] = TRUE; // marqueur mis en session pour vérifier que c'est bien cet utilisateur qui veut voir (et à donc le droit de voir) le fichier, car il n'y a pas d'autre vérification de droit ensuite
      $tab_tbody[$DB_ROW['user_id']][$DB_ROW['periode_id']][] = '<a href="releve_pdf.php?fichier='.$DB_ROW['user_id'].'_'.$DB_ROW['officiel_type'].'_'.$DB_ROW['periode_id'].'" target="_blank">'.$tab_types[$DB_ROW['officiel_type']]['titre'].'</a>' ;
    }
  }
}

// autre boucle pour les fiches brevet (ce n'est pas la même table)
if(test_user_droit_specifique($_SESSION['DROIT_'.$tab_types['brevet']['droit'].'_VOIR_ARCHIVE']))
{
  $bilan_type = 'brevet';
  $DB_TAB = DB_STRUCTURE_BREVET::DB_lister_brevet_fichiers( implode(',',$tab_eleve_id) );
  foreach($DB_TAB as $user_id => $tab)
  {
    if(is_file(CHEMIN_DOSSIER_OFFICIEL.$_SESSION['BASE'].DS.fabriquer_nom_fichier_bilan_officiel( $user_id , $bilan_type , $annee_session_brevet )))
    {
      $_SESSION['tmp_droit_voir_archive'][$user_id.$bilan_type] = TRUE; // marqueur mis en session pour vérifier que c'est bien cet utilisateur qui veut voir (et à donc le droit de voir) le fichier, car il n'y a pas d'autre vérification de droit ensuite
      $tab_tbody[$user_id]['+'.$annee_session_brevet][] = '<a href="releve_pdf.php?fichier='.$user_id.'_'.$bilan_type.'_'.$annee_session_brevet.'" target="_blank">'.$tab_types['brevet']['titre'].'</a>' ;
      $tab_thead['+'.$annee_session_brevet] = '<th class="hc">Année</th>';
    }
  }
}

// Assemblage et affichage du tableau.

echo'<p>Ces bilans sont des copies numériques, laissées à disposition <span class="danger">seulement jusqu\'à la fin de l\'année scolaire.</span></p>'.NL;
echo'<p class="astuce">Cliquer sur un lien atteste que vous avez pris connaissance du document correspondant.</p>'.NL;
echo'<hr />'.NL;
echo'<table id="table_bilans"><thead>'.NL.'<tr>'.implode('',$tab_thead).'</tr>'.NL.'</thead><tbody>'.NL;
unset($tab_thead[0]);
foreach($tab_eleve_id as $eleve_id)
{
  echo'<tr>'.$tab_tbody[$eleve_id][0] ;
  foreach($tab_thead as $periode_id => $th)
  {
    echo (isset($tab_tbody[$eleve_id][$periode_id])) ? '<td class="hc">'.implode('<br />',$tab_tbody[$eleve_id][$periode_id]).'</td>' : '<td class="hc">-</td>' ;
  }
    echo'</tr>'.NL;
}
echo'</tbody></table>'.NL;

?>
