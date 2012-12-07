<?
include_once 'includes/init.php';
if (!$is_admin) {echo 'Vous n\'avez pas les droits suffisants';exit;}

echo '<script language="javascript" type="text/javascript" src="lcs_import.js"></script>';
/* Generate the selection list for calendar user selection.
 * Only ask for calendar user if user is an administrator.
 *
 * We may enhance this in the future to allow:
 *  - selection of more than one user
 *  - non-admin users this functionality
 */
 print_header ();
 
$error = $sqlLog = '';

$upload = ini_get ( 'file_uploads' );
$upload_enabled = ( ! empty ( $upload ) &&
preg_match ( '/(On|1|true|yes)/i', $upload ) );

print_header ( array ( 'js/export_import.php', 'js/visible.php' ),
  '', 'onload="toggle_import();"' );

echo '
    <h2>' . translate ( 'Import' ) . '&nbsp;en masse <img src="images/help.gif" alt="'
 . translate ( 'Help' ) . '" class="help" onclick="window.open( '
 . "'lcs_help_import2.php', 'cal_help', '"
 . 'dependent,menubar,scrollbars,height=400,width=400\' );" /></h2>';
//si clic sur le bouton Valider
if (isset($_POST['Importer']))
	{
	global $message;
	//traitement du  fichier 	
			if ((!empty($_FILES["FileName"]["name"])) )
			{
			if ($_FILES["FileName"]["size"]>0)
				{
				$extension= substr($_FILES["FileName"]["name"],-3);
				if (strtolower($extension) != "zip") $error="Le fichier n/'est pas conforme";
				else 
					{
					//chargement du fichier
					copy($_FILES["FileName"]["tmp_name"],"/tmp/archi.zip");
					
					echo "<ul> <li>Pendant l'importation un bilan s'affichera ci-dessous au fur et &#224; mesure de l'avancement des op&#233;rations.<li> Si vous quittez cette page avant la fin, l'op&#233;ration se poursuivra quand m&#234;me. <li>Lorsqu'elle sera termin&#233;e, vous pourrez consulter le bilan dans le menu \"Emploi du temps -> Compte rendu d'importation en masse\".</ul>";

					//echo'<input name="button"  type="button" onClick="go(7)" value="Commencer l\'import" title="" />';	
					//echo '<a href="import_cr.php" target="_blank"  >Import</a>';
					echo '<div id="cr"></div>';
					echo '<script language="javascript" type="text/javascript" >
					go(7);
					setTimeout("affiche_cr()",500);
					</script>';
					}
				}
			}
		exit;	
	}
else 
{
if ( ! $upload_enabled )
  // The php.ini file does not have file_uploads enabled,
  // so we will not receive the uploaded import file.
  // Note: do not translate "php.ini file_uploads"
  // since these are the filename and config name.
  echo '
    <p>' . translate ( 'Disabled' ) . ' (php.ini file_uploads)</p>';
else {
	
  echo '<p> <b>Le fichier compress&#233; (.zip) doit contenir des fichiers "ical" dont le nom est au format : nom pr&#233;nom.ics</b></p>';

  // File uploads enabled.
  $noStr = translate ( 'No' );
  $yesStr = translate ( 'Yes' );
  echo '
    <form action="'. htmlentities($_SERVER['PHP_SELF']).'" method="post" name="importform" '
   . 'enctype="multipart/form-data" >
      <table>
        
<!-- /IVCAL -->
        <tr class="browse">
          <td><label for="fileupload">' . translate ( 'Upload file' ) . ':</label></td>
          <td><input type="file" name="FileName" id="fileupload" size="45" '
   . 'maxlength="50" /></td>
        </tr>';
       
  //print_user_list ();
  echo '
      </table><br />
      <input type="submit" name="Importer" value="' . translate ( 'Import' ) . '" />
    </form>';
    
    
}

}
//echo print_trailer ();

?>
