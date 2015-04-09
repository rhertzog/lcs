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
if(!isset($STEP))       {exit('Ce fichier ne peut être appelé directement !');}

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// Étape 53 - Récupérer les identifiants des nouveaux utilisateurs (tous les cas)
// ////////////////////////////////////////////////////////////////////////////////////////////////////

$archive = (isset($_POST['archive'])) ? $_POST['archive'] : '';
if(!$archive)
{
  exit('Erreur : le nom du fichier contenant les identifiants est manquant !');
}
echo'<p><label class="alerte">Voici les identifiants des nouveaux inscrits :</label></p>'.NL;
echo'<ul class="puce">'.NL;
echo  '<li><a target="_blank" href="'.URL_DIR_LOGINPASS.$archive.'.pdf"><span class="file file_pdf">Archiver / Imprimer (étiquettes <em>pdf</em>).</span></a></li>'.NL;
echo  '<li><a target="_blank" href="./force_download.php?auth&amp;fichier='.$archive.'.csv"><span class="file file_txt">Récupérer / Manipuler (fichier <em>csv</em> pour tableur).</span></a></li>'.NL;
echo'</ul>'.NL;
echo'<p class="danger">Les mots de passe, cryptés, ne seront plus accessibles ultérieurement !</p>'.NL;
switch($import_origine.'+'.$import_profil)
{
  case 'sconet+eleve'       : $etape = 6; $STEP = 61; break;
  case 'sconet+professeur'  : $etape = 6; $STEP = 61; break;
  case 'tableur+eleve'      : $etape = 5; $STEP = 61; break;
  case 'tableur+professeur' : $etape = 4; $STEP = 61; break;
  case 'sconet+parent'      : $etape = 4; $STEP = 71; break;
  case 'tableur+parent'     : $etape = 4; $STEP = 71; break;
  case 'base_eleves+parent' : $etape = 4; $STEP = 71; break;
  case 'base_eleves+eleve'  : $etape = 5; $STEP = 90; break;
}
echo'<ul class="puce p"><li><a href="#step'.$STEP.'" id="passer_etape_suivante">Passer à l\'étape '.$etape.'.</a><label id="ajax_msg">&nbsp;</label></li></ul>'.NL;

?>
