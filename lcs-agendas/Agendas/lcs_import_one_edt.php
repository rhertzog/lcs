<?
include 'includes/init.php';

/* Generate the selection list for calendar user selection.
 * Only ask for calendar user if user is an administrator.
 *
 * We may enhance this in the future to allow:
 *  - selection of more than one user
 *  - non-admin users this functionality
 */

$upload = ini_get ( 'file_uploads' );
$upload_enabled = ( ! empty ( $upload ) &&
  preg_match ( '/(On|1|true|yes)/i', $upload ) );

print_header ( array ( 'js/export_import.php', 'js/visible.php' ),
  '', 'onload="toggle_import();"' );
ob_start ();
echo '
    <h2>' . translate ( 'Import' ) . '&nbsp;l\'emploi du temps d\'un enseignant <img src="images/help.gif" alt="'
 . translate ( 'Help' ) . '" class="help" onclick="window.open( '
 . "'lcs_help_import.php', 'cal_help', '"
 . 'dependent,menubar,scrollbars,height=400,width=400\' );" /></h2>';

if ( ! $upload_enabled )
  // The php.ini file does not have file_uploads enabled,
  // so we will not receive the uploaded import file.
  // Note: do not translate "php.ini file_uploads"
  // since these are the filename and config name.
  echo '
    <p>' . translate ( 'Disabled' ) . ' (php.ini file_uploads)</p>';
else {
	
  echo '<p> <b>Le nom du fichier doit imp&#233;rativement &#234;tre au format : nom pr&#233;nom.ics</b></p>';

  // File uploads enabled.
  $noStr = translate ( 'No' );
  $yesStr = translate ( 'Yes' );
  echo '
    <form action="lcs_import_handler.php" method="post" name="importform" '
   . 'enctype="multipart/form-data" onsubmit="return checkExtension()">
      <table>
        <tr>
          <td><label for="importtype">' . translate ( 'Import format' ) . ':</label></td>
          <td>
            <select name="ImportType" id="importtype" onchange="toggle_import()">
              <option value="ICAL">iCal</option>
              Fichiers ics compress&#233;s
           </select>
          
          </td>
        </tr>

<!-- Not valid for Outlook CSV as it doesn\'t generate UID for import tracking. -->
        <tr id="ivcal">
          <td><label>' . translate ( 'Overwrite Prior Import' ) . ':</label></td>
          <td>
            <label><input type="radio" name="overwrite" value="Y" checked="checked" />&nbsp;'
   . $yesStr . '</label>
            <label><input type="radio" name="overwrite" value="N" />&nbsp;'
   . $noStr . '</label>
          </td>
        </tr>
<!-- /IVCAL -->
        <tr class="browse">
          <td><label for="fileupload">' . translate ( 'Upload file' ) . ':</label></td>
          <td><input type="file" name="FileName" id="fileupload" size="45" '
   . 'maxlength="50" /></td>
        </tr>';
        
  //print_user_list ();
  echo '
      </table><br />
      <input type="hidden" name="calUser" id="caluser" value="" />
      <input type="submit" value="' . translate ( 'Import' ) . '" />
    </form>';
}
ob_end_flush ();
echo print_trailer ();

?>