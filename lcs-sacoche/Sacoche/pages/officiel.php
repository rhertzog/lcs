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
$TITRE = html(Lang::_("Synthèses / Bilans"));
?>

<?php if( ($_SESSION['USER_PROFIL_TYPE']=='administrateur') || (($_SESSION['USER_PROFIL_TYPE']=='directeur')&&(substr($SECTION,0,8)=='reglages')) ): ?>
<div class="hc">
  <a href="./index.php?page=<?php echo $PAGE ?>&amp;section=reglages_ordre_matieres">Ordre d'affichage des matières.</a>  ||
  <a href="./index.php?page=<?php echo $PAGE ?>&amp;section=reglages_format_synthese">Format de synthèse par référentiel.</a>  <br />
  <a href="./index.php?page=<?php echo $PAGE ?>&amp;section=reglages_configuration">Configuration des bilans officiels.</a>  ||
  <a href="./index.php?page=<?php echo $PAGE ?>&amp;section=reglages_mise_en_page">Mise en page des bilans officiels.</a>  <br />
  <?php if($_SESSION['USER_PROFIL_TYPE']=='administrateur'): ?>
  <a href="./index.php?page=<?php echo $PAGE ?>&amp;section=assiduite">Absences / Retards.</a>  <br />
  <a href="./index.php?page=<?php echo $PAGE ?>&amp;section=accueil_releve">[ Relevé d'évaluations ]</a>
  <a href="./index.php?page=<?php echo $PAGE ?>&amp;section=accueil_bulletin">[ Bulletin scolaire ]</a>
  <?php
  $tab_paliers_actifs = explode(',',$_SESSION['LISTE_PALIERS_ACTIFS']);
  for( $palier_id=1 ; $palier_id<4 ; $palier_id++ )
  {
    if(in_array($palier_id,$tab_paliers_actifs))
    {
      echo'<a href="./index.php?page='.$PAGE.'&amp;section=accueil_palier'.$palier_id.'">[ Maîtrise du palier '.$palier_id.' ]</a>'.NL;
    }
  }
  ?>
  <?php endif; ?>
</div>
<hr />
<?php endif; ?>

<?php
if($SECTION=='reglages')
{
  echo'<p class="astuce">Choisir une rubrique ci-dessus&hellip;</p>'.NL;
  $nb_inconnu = DB_STRUCTURE_BILAN::DB_compter_modes_synthese_inconnu();
  $s = ($nb_inconnu>1) ? 's' : '' ;
  echo ($nb_inconnu) ? '<label class="alerte">Il y a '.$nb_inconnu.' référentiel'.$s.' <img alt="" src="./_img/bulle_aide.png" width="16" height="16" title="'.str_replace('§BR§','<br />',html(html(DB_STRUCTURE_BILAN::DB_recuperer_modes_synthese_inconnu()))).'" /> dont le format de synthèse est inconnu (donc non pris en compte).</label> <a href="./index.php?page='.$PAGE.'&amp;section=reglages_format_synthese">&rarr; Configurer les formats de synthèse.</a>'.NL : '<label class="valide">Tous les référentiels ont un format de synthèse prédéfini.</label>'.NL ;
}
elseif($SECTION=='assiduite')
{
  $fichier_section = CHEMIN_DOSSIER_PAGES.$PAGE.'_'.$SECTION.'.php';
  $PAGE = $PAGE.'_'.$SECTION ;
  require($fichier_section);
}
else
{
  if(substr($SECTION,0,8)=='accueil_')
  {
    $BILAN_TYPE = substr($SECTION,8);
    $SECTION = 'accueil';
  }
  // Afficher la bonne page et appeler le bon js / ajax par la suite
  $fichier_section = CHEMIN_DOSSIER_PAGES.$PAGE.'_'.$SECTION.'.php';
  if(!is_file($fichier_section))
  {
    echo'<p class="danger">Page introuvable (paramètre manquant ou incorrect) !</p>'.NL;
    return; // Ne pas exécuter la suite de ce fichier inclus.
  }
  if( isset($BILAN_TYPE) && in_array($BILAN_TYPE,array('palier1','palier2','palier3')) )
  {
    $tab_paliers_actifs = explode(',',$_SESSION['LISTE_PALIERS_ACTIFS']);
    $palier = mb_substr($BILAN_TYPE,-1);
    if(!in_array($palier,$tab_paliers_actifs))
    {
      $liste_paliers_actifs = ($_SESSION['LISTE_PALIERS_ACTIFS']) ? ( (count($tab_paliers_actifs)==1) ? 'palier '.$_SESSION['LISTE_PALIERS_ACTIFS'].' activé' : 'paliers '.str_replace(',',' et ',$_SESSION['LISTE_PALIERS_ACTIFS']).' activés' ) : 'aucun' ;
      echo'<p class="danger">Le palier '.$palier.' n\'a pas été activé par les administrateurs ('.$liste_paliers_actifs.').</p>'.NL;
      return; // Ne pas exécuter la suite de ce fichier inclus.
    }
  }
  if( !isset($BILAN_TYPE) || in_array($BILAN_TYPE,array('releve','bulletin','palier1','palier2','palier3')) )
  {
    $PAGE = $PAGE.'_'.$SECTION ;
    require($fichier_section);
  }
  else
  {
    echo'<p class="danger">Page introuvable (paramètre manquant ou incorrect) !</p>'.NL;
    return; // Ne pas exécuter la suite de ce fichier inclus.
  }
}
?>
