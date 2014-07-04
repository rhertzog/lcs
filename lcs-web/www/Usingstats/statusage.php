<?
/*===========================================
   Projet LcSE3
   Equipe Tice academie de Caen
   Distribue selon les termes de la licence GPL
   Derniere modification : 27/03/2014
   ============================================= */
include ("../lcs/includes/headerauth.inc.php");
include ("../Annu/includes/ldap.inc.php");
include ("../Annu/includes/ihm.inc.php");

session_name("Lcs");
@session_start();
if ( ! isset($_SESSION['login'])) {
     echo "<script type='text/javascript'>";
    echo 'alert("Suite \340 une p\351riode d\'inactivit\351 trop longue, votre session a expir\351 .\n\n Vous devez vous r\351authentifier");';
    echo 'location.href = "../lcs/logout.php"</script>';
    exit;
    }
$login=$_SESSION['login'];
if (count($_GET)>0) {
  	//configuration objet
 	include ("../lcs/includes/htmlpurifier/library/HTMLPurifier.auto.php");
 	$config = HTMLPurifier_Config::createDefault();
 	$purifier = new HTMLPurifier($config);
    //purification des variables
 	if (isset($_GET['when'])) $when=$purifier->purify($_GET['when']);
  }
?>
<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">
  <html>
  <head>
	<meta HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=utf-8\">
	<title> ..::: LCS Statistiques d'usage :::..</title>
	<link  href='../style.css' rel='StyleSheet' type='text/css'>
  </head>

<?
//Verification du droit de lecture des stats
if (ldap_get_right("stats_can_read",$login)=="Y")

	{
	//si la date des stats est passee en parametre
	if (isset($when))
                    {
                    //$when=addSlashes(strip_tags(stripslashes($_GET['when'])));
                    $fichier = fopen("http://193.49.64.33/stats/referer.php?quand=$when", "r");
                    }
	else
	// sinon, on demande les stats disponibles
                    {
                    $fichier = fopen("http://193.49.64.33/stats/referer.php", "r");
                    }

	if ($fichier)
                    {
                    // lecture du fichier distant.
                    while(!feof($fichier))
                        {
                        $Ligne = fgets($fichier, 255);
                        print($Ligne);
                        }
                    }
	// close the file
	fclose($fichier);

	}

else echo "<div class=alert_msg>Vous n'avez pas les droits suffisants pour acc&#233;der &#224; cette page !</div>\n";

include ("../lcs/includes/pieds_de_page.inc.php");
?>