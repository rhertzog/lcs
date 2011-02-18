<?
/* =============================================
	Projet LCS : Linux Communication Server
	jLCF >:>  jean-luc.chretien@tice.ac-caen.fr
				-
Statistiques d'usages des LCS de l'academie de CAEN
				-
			 08/06/2009
		 par philippe LECLERC
	       philippe.leclerc1@ac-caen.fr
		     - script statusage.php -
			 version 1
		    	     _-=-_
 ============================================= */
include ("../lcs/includes/headerauth.inc.php");
include ("../Annu/includes/ldap.inc.php");
include ("../Annu/includes/ihm.inc.php");

//Verification de l'authentification
list ($idpers, $login)= isauth();
if ($idpers == "0")    header("Location:$urlauth");
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
	if (isset($_GET['when']))
		{
		$when=addSlashes(strip_tags(stripslashes($_GET['when'])));
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