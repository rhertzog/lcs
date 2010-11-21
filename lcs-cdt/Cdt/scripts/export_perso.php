<?php
/* ==================================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 2.2 du 25/10/2010
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script d'export de son Cdt-
			_-=-_
   =================================================== */
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Expires: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache"); 
session_name("Cdt_Lcs");
@session_start();
include "../Includes/check.php";
if (!check()) exit; 
//error_reporting(0);
//si la page est appelee par un utilisateur non identifiee
if (!isset($_SESSION['login']) )exit;

//si la page est appelee par un utilisateur non prof
elseif ($_SESSION['cequi']!="prof") exit;
if (isset($_POST['Fermer'])) 
	echo "<SCRIPT language='Javascript'>
					<!--
					window.close()
					// -->
					</script>";	
include "../Includes/basedir.inc.php";
include "$BASEDIR/lcs/includes/headerauth.inc.php";

// Connexion a la base de donnees
require_once ('../Includes/config.inc.php');

function dumpMySQL()
{   global $dbc;
	$connexion=$dbc;
    $entete = "-- ----------------------\n";
    $entete .= "-- dump de la base ".$base." au ".date("d-M-Y")."\n";
    $entete .= "-- ----------------------\n\n\n";
    $creations = "";
    $insertions = "\n\n";
    
    $listeTables = mysql_query("show tables", $connexion);
    while($table = mysql_fetch_array($listeTables))
    {
        // si l'utilisateur a demande la structure ou la totale
        //if ( $table[0] == "cahiertxt" || $table[0] == "onglets" )
        if ( mb_ereg("^cahiertxt",$table[0]) || mb_ereg("^onglets",$table[0]))
        {
            $creations .= "-- -----------------------------\n";
            $creations .= "-- creation de la table ".$table[0]."\n";
            $creations .= "-- -----------------------------\n";
            $creations .= "DROP TABLE IF EXISTS `".$table[0]."`;\n";
            $listeCreationsTables = mysql_query("show create table ".$table[0], $connexion);
            while($creationTable = mysql_fetch_array($listeCreationsTables))
            {
              $creations .= $creationTable[1].";\n\n";
            }
        //donnees
            $donnees = mysql_query("SELECT * FROM ".$table[0]."  WHERE login='".$_SESSION['login']."'");
            $insertions .= "-- -----------------------------\n";
            $insertions .= "-- insertions dans la table ".$table[0]."\n";
            $insertions .= "-- -----------------------------\n";
            while($nuplet = mysql_fetch_array($donnees))
            {
                $insertions .= "INSERT INTO ".$table[0]." VALUES(";
                for($i=0; $i < mysql_num_fields($donnees); $i++)
                {
                  if($i != 0)
                     $insertions .=  ", ";
                  if(mysql_field_type($donnees, $i) == "string" || mysql_field_type($donnees, $i) == "blob" || mysql_field_type($donnees, $i) == "timestamp" || mysql_field_type($donnees, $i) == "date")
                     $insertions .=  "'";
                  $insertions .= addslashes($nuplet[$i]);
                  if(mysql_field_type($donnees, $i) == "string" || mysql_field_type($donnees, $i) == "blob" || mysql_field_type($donnees, $i) == "timestamp" || mysql_field_type($donnees, $i) == "date")
                    $insertions .=  "'";
                }
                $insertions .=  ");\n";
            }
            $insertions .= "\n";
        }
    }
 
    mysql_close($connexion);
 $rep_tmp="/tmp/".$_SESSION['login'];
 mkdir($rep_tmp);
    $fichierDump = fopen($rep_tmp."/dump.sql", "wb");
    fwrite($fichierDump, $entete);
    fwrite($fichierDump, $creations);
    fwrite($fichierDump, $insertions);
    fclose($fichierDump);
    
}
if (isset($_POST['enregistrer']))
{

dumpMySQL();
$rep_tmp="/tmp/".$_SESSION['login'];

if (file_exists("/home/".$_SESSION['login']."/public_html/Docs_Cdt")) 
		{
		$cmd="cp -ar /home/".$_SESSION['login']."/public_html/Docs_Cdt ".$rep_tmp;
		exec($cmd);
		}
if (file_exists("/home/".$_SESSION['login']."/public_html/Images_Cdt")) 
		{
		$cmd="cp -ar /home/".$_SESSION['login']."/public_html/Images_Cdt ".$rep_tmp;
		exec($cmd);
		}		
$cmd="cd /tmp/".$_SESSION['login']." && tar czvf my_cdt.tgz *";
exec($cmd);
$cmd="cd /tmp/".$_SESSION['login']." && tar -tzvf my_cdt.tgz > ctrl.txt && md5sum ctrl.txt | cut -b 10-20";
exec($cmd,$retour);
$fichier = $_SESSION['login'].'@'.$retour[0].'@'.$domain.'.cdt';
$chemin = $rep_tmp.'/my_cdt.tgz';
//echo filesize($chemin);

 if (file_exists($chemin))
{
     header('Content-disposition: attachment; filename="' . $fichier . '"');
     header('Content-Type: application/force-download');
     header('Content-Transfer-Encoding: binary');
     header('Content-Length: '. filesize($chemin));
     if (mb_ereg('MSIE', $_SERVER['HTTP_USER_AGENT'])) header('Pragma: public');
     else header('Pragma: no-cache');
     header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
     header('Expires: 0');
     readfile($chemin);
     $cmd=" rm -rf ".$rep_tmp;
   	exec($cmd);
     
 }
  else
 {
     $erreurFichier = 'le fichier "' . $fichier . '" n\'existe pas. ';
 }
 
}
else
{

echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title>Cahier de textes num&eacute;rique</title>
<meta http-equiv="content-type" content="text/html;charset=utf-8" >
	<link href="../style/style.css" rel=StyleSheet type="text/css">
</head>

<body>
<form  action="'. htmlentities($_SERVER['PHP_SELF']).'" method="post" >';
echo '<INPUT name="TA" type="hidden"  value="'. md5($_SESSION['RT'].htmlentities($_SERVER['PHP_SELF'])).'">';
echo '<div id="first">
	<div class="prg">
		<fieldset id="field7">
		<legend id="legende"> Export de donn&#233;es </legend>
		<h4.perso>Cet utilitaire vous permet de t&#233;l&#233;charger toutes vos donn&#233;es personnelles saisies dans le cahier de textes. 
		Celles-ci pourront &#234;tre import&#233;es ult&#233;rieurement dans les archives d\'un cahier de textes d\'un autre &#233;tablissement, &#224; condition de ne PAS RENOMMER LE FICHIER !<br /><br /></h4>
		<input type="submit" title="Enregistrer" name="enregistrer" value="" class="bt-valid" >
		<input class="bt-fermer" type="submit" name="Fermer" value="" >
</fieldset>
	</div></div>
	</form>
	</body>
	</html>';
	}
	
?>