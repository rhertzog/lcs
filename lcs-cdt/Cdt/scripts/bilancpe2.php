<?php
/* ==================================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 2.3 du 06/01/2011
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script bilan absences pour cpe-
			_-=-_
  "Valid XHTML 1.0 Strict"
   =================================================== */
   
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Expires: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache"); 

session_name("Cdt_Lcs");
@session_start();
include "../Includes/functions2.inc.php";
include "../Includes/fonctions.inc.php";
//fichiers n�cessaires � l'exploitation de l'API
	$BASEDIR="/var/www";
	//include "../Includes/basedir.inc.php";
	include "$BASEDIR/lcs/includes/headerauth.inc.php";
	include "$BASEDIR/Annu/includes/ldap.inc.php";
	include "$BASEDIR/Annu/includes/ihm.inc.php";  
	
//si la page est appel�e par un utilisateur non identifi� ou non autorise
if (!isset($_SESSION['login'])) exit;
elseif ((ldap_get_right("Cdt_is_cpe",$_SESSION['login'])=="N") && (ldap_get_right("Cdt_can_sign",$_SESSION['login'])=="N")) exit;

$tsmp=time();
$tsmp2=time() - 7776000;//j-90

// Connexion � la base de donn�es
require_once ('../Includes/config.inc.php');
//$tab_cla=array("2DE","1ES","TES");
if (isset($_GET['kr'])) 
	{
$tab_kren=explode('-',$_GET['kr']);
	}


if (isset($_GET['dkr'])) 
	{
$Morceauc=explode('/',$_GET['dkr']);
	$datsql= $Morceauc[2]."/".$Morceauc[1]."/".$Morceauc[0];
	
	}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta name="author" content="Philippe LECLERC -TICE CAEN" />
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
    <title>module &#139;(+_-)/&#155;</title>
	<link rel="stylesheet" type="text/css" href="../style/style.css"  media="screen" />
	<link rel="stylesheet" href="../style/style_imp.css" type="text/css" media="print" />
	<!--[if IE]>
        <link href="../style/style-ie.css"  rel="stylesheet" type="text/css"/>
        <![endif]-->
</head>
<body>
<h1 class='title'></h1>
<form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="post" >
<fieldset id="field7">
<legend id="legende">Bilan des Absences &amp; Retards  du <?php echo $_GET['dkr'];?></legend>
<?php
if (isset($_GET['dkr']))
	{
	$aucun="true";
	echo '<div id="abs-contenu">';
	//nom du creneau
	foreach ( $tab_kren as $cle => $valcren)
			{	
			$tab_cla=array();	
			//recherche des classes avec absents ou retardataires
			$rq = "SELECT DISTINCT classe FROM absences WHERE date='$datsql' AND ".$valcren."!='' ORDER BY date ASC";
			// lancer la requ�te
			$result = mysql_query ($rq) or die (mysql_error());
			if (mysql_num_rows($result)>0)
			{
			$aucun="false";
			echo "<h2>".$valcren."</h2>";
			echo "<ul>";
			$loop=0;
			while ($enrg = mysql_fetch_array($result, MYSQL_NUM)) 
				{
				$tab_cla[$loop]= $enrg[0];$loop++;
				}
	 		foreach ( $tab_cla as $cle => $valcla)
					{
					echo "<li>".$valcla."";
					echo "<ul><li>Absents : ";
					//recherche des absents de la classe
					$rq = "SELECT uideleve FROM absences WHERE date='$datsql' AND ".$valcren."='A' AND classe='$valcla' ORDER BY id_abs ASC";
					// lancer la requ�te
					$result = mysql_query ($rq) or die (mysql_error());
					$nb = mysql_num_rows($result);
					if ($nb==0) echo "aucun";
					else  
					while ($enrg = mysql_fetch_array($result, MYSQL_NUM)) 
						{
						list($user, $groups)=people_get_variables($enrg[0], false);
						echo $user["fullname"];
                                                 if (mb_ereg("^Cours",$valcla)) echo '( '.people_get_classe ($enrg[0]). ' )';
                                                echo "; ";
						}
                                            echo '</li>';
					echo "<li>Retards : ";
					//recherche des retardataires//
					$rq = "SELECT uideleve FROM absences WHERE date='$datsql' AND ".$valcren."='R' AND classe='$valcla' ORDER BY id_abs ASC";
					// lancer la requ�te
					$result = mysql_query ($rq) or die (mysql_error());
					$nb = mysql_num_rows($result);
					if ($nb==0) echo "aucun";
					else  
					while ($enrg = mysql_fetch_array($result, MYSQL_NUM)) 
						{
						list($user, $groups)=people_get_variables($enrg[0], false);
						echo $user["fullname"]."; ";
						}
                                            echo '</li>';
					echo "</ul></li>";
					}//fin each class
				echo "</ul>";
				}
			}//fin each creneau
	if ($aucun=="true") echo "Pas de donn&eacute;es pour le(s) cr&eacute;neau(x) s&eacute;lectionn&eacute;(s) !<br />";
	echo '<div > <h5> N\'apparaissent que les classes pour lesquelles l\'appel a &eacute;t&eacute; fait !</h5></div>'; 

	echo "<script type=\"text/javascript\">
            //<![CDATA[
		document.write('<div id=\"abs-bt\"><a href=\"javascript:window.print()\" id=\"bt-imp\"></a>');
		document.write('<a href=\"javascript:window.close()\" id=\"bt-close\"></a></div>');
             //]]>
	</script>";
	echo '</div>';
	}
?>
</fieldset>	
</form>
<?php
include ('../Includes/pied.inc');
?>
</body>
</html>