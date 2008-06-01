<?
/* Version du 02/11/2004 */
include ("../lcs/includes/headerauth.inc.php");
include ("../Annu/includes/ldap.inc.php");
include ("../Annu/includes/ihm.inc.php");

list ($idpers, $login)= isauth();
if ($idpers == "0") header("Location:$urlauth");

$html = "<HTML>\n";
$html .= "	<HEAD>\n";
$html .= "		<TITLE>...::: Interface d'administration Serveur LCS :::...</TITLE>\n";
$html .= "		<LINK  href='../Annu/style.css' rel='StyleSheet' type='text/css'>\n";
$html .= "	</HEAD>\n";
$html .= "	<BODY>\n";
echo $html;

if (is_admin("lcs_is_admin",$login)=="Y") {
  $html = "<H2>Journal des mises à jour :</H2>\n";
  $html .= "<pre>\n";
  if ( file_exists("maj.log") ) {
    $fd = fopen("maj.log", "r");
    while ( !feof($fd) ) {
      $html .= nl2br(fgets($fd, 255));
    } 
  $html .= "</pre>\n"; 
  } else $html .= "<div class='alert_msg'>Le journal des mises à jour n'est pas disponible !</div>\n";  
  echo $html;
}// fin is_admin
else echo "Vous n'avez pas les droits nécessaires pour ordonner cette action...";
include ("../lcs/includes/pieds_de_page.inc.php");
?>
