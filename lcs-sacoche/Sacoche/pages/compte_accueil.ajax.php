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

$f_type = (isset($_POST['f_type'])) ? Clean::texte($_POST['f_type'])  : '';
$f_etat = (isset($_POST['f_etat'])) ? Clean::entier($_POST['f_etat']) : -1;

if(substr($f_type,0,8)=='messages')
{
  $message_id = (int)substr($f_type,8);
  $f_type = 'messages';
}

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// Vérification des informations transmises
// ////////////////////////////////////////////////////////////////////////////////////////////////////

$tab_types = array( 'user'=>'modifiable' , 'alert'=>'imposé' , 'messages'=>'modifiable' , 'demandes'=>'modifiable' , 'help'=>'modifiable' , 'ecolo'=>'modifiable' );

if( (!isset($tab_types[$f_type])) || ($tab_types[$f_type]=='imposé') || ($f_etat==-1) )
{
  exit('Erreur avec les données transmises !');
}

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// Hors messages d'accueil - Construction de la nouvelle chaine à mettre en session et à enregistrer dans la base
// ////////////////////////////////////////////////////////////////////////////////////////////////////

if($f_type!='messages')
{
  foreach($tab_types as $key => $kill)
  {
    $val = ($key==$f_type) ? $f_etat : ( (strpos($_SESSION['USER_PARAM_ACCUEIL'],$key)===FALSE) ? 0 : 1 ) ;
    $tab_types[$key] = $val ;
  }
  $_SESSION['USER_PARAM_ACCUEIL'] = implode( ',' , array_keys( array_filter($tab_types) ) );
  DB_STRUCTURE_COMMUN::DB_modifier_user_param_accueil( $_SESSION['USER_ID'] , $_SESSION['USER_PARAM_ACCUEIL'] );
  exit('ok');
}

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// Messages d'accueil - Enregistrer l'information associée au message dans la base
// ////////////////////////////////////////////////////////////////////////////////////////////////////

if(!empty($message_id))
{
  DB_STRUCTURE_COMMUN::DB_modifier_message_dests_cache( $message_id , $_SESSION['USER_ID'] , (bool)$f_etat );
  exit('ok');
}

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// On ne devrait pas en arriver là...
// ////////////////////////////////////////////////////////////////////////////////////////////////////

exit('Erreur avec les données transmises !');

?>
