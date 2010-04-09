<?php
/* $Id: import_handler.php,v 1.40.2.1 2008/03/03 20:29:00 cknudsen Exp $
 *
 * Description:
 * Loads appropriate import file parser and processes the data returned.
 *    Currently supported:
 *      Palmdesktop (dba file)
 *      iCal (ics file)
 *      vCal (vcs file)
 *
 *
 * Notes:
 * User defined import routines may be used, see example
 * in the SWITCH statement below.
 *
 * Input parameters:
 *   FileName:    File name specified by user on import.php user's calendar to
 *                import data into, unless single user = Y or Admin,
 *                caluser will equal logged in user.
 *   exc_private: Exclude private records from Palmdesktop import.
 *   overwrite:   Overwrite previous import.
 *
 * Security:
 * TBD
 */
include_once 'includes/init.php';
include_once 'includes/xcal.php';
include_once 'lcs_functions.php';
$error = $sqlLog = $message = '';
print_header ();

$overwrite = getValue ( 'overwrite' );
$doOverwrite = ( empty ( $overwrite ) || $overwrite != 'Y' ) ? false : true;
$numDeleted = 0;

if ( ! empty ( $_FILES['FileName'] ) )
  {
  $file = $_FILES['FileName'];
$message.= '<br />'.date("H:i:s")." - <b>Traitement du fichier ".$file['name']."</b><br/>";
//recherche de l'utilisateur concerné par le fichier
  $userlist = user_get_users ();
  $nom_fichier = ereg_replace("'|[[:blank:]]",'_',$file['name']);
  $nom_users=explode('.',$nom_fichier);
  $nom_user=$nom_users[0];
  $match=false;
  for ( $i = 0, $cnt = count ( $userlist ); $i < $cnt; $i++ ) {
	if ( strtolower($userlist[$i]['cal_lastname'].'_'.$userlist[$i]['cal_firstname']) == $nom_user) {
	$calUser= $userlist[$i]['cal_login'];
	$match=true;
	break;
	}
   }
 
 }

if ( empty ( $file ) )
  echo translate ( 'No file' ) . '!<br />';
if ( ! $match) $errormsg .=' Pas de correspondance trouv&#233;e avec un utilisateur du LCS.<br /><br /> - V&#233;rifiez la coh&#233;rence entre le nom du fichier .ics et les nom et pr&#233;nom de l\'utilisateur tels qu\'ils sont d&#233;finis dans l\'annuaire du LCS ! <br />';

if ( ! empty ( $calUser ) ) {
   $login  = $calUser;
} else
  $calUser = $login;

$ImportType = 'ICAL';
$exc_private = getValue ( 'exc_private' );
$overwrite = getValue ( 'overwrite' );
if ( $file['size'] > 0 ) {
  
  $data = parse_ical ( $file['tmp_name'] );
  $count_con = $count_suc = $error_num = 0;
  if ( ! empty ( $data ) && empty ( $errormsg ) && $match) {
  	$login=$calUser;
   	$type = 'ical';
   lcs_import_data ( $data, $doOverwrite, $type );
   echo $message;
   echo translate ( 'Events successfully imported' ) . ': ' . $count_suc
     . '<br />
    '  . ( empty ( $ALLOW_CONFLICTS )
      ? translate ( 'Conflicting events' ) . ': ' . $count_con . '<br />
    ' : '' ) . translate ( 'Errors' ) . ': ' . $error_num . '<br /><br />';
  } elseif ( ! empty ( $errormsg ) )
    echo $message.'
    <br /><br />
    <b>' . translate ( 'Error' ) . ':</b> ' . $errormsg . '<br />';
  else
    echo '
    <br /><br />
    <b>' . translate ( 'Error' ) . ':</b> '
     . translate ( 'There was an error parsing the import file or no events were returned' )
     . '.<br />';
} else
  echo '
    <br /><br />
    <b>' . translate ( 'Error' ) . ':</b> '
   . translate ( 'The import file contained no data' ) . '.<br />';

echo print_trailer ();

?>
