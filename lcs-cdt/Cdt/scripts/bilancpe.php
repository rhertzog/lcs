<?php
/* ==================================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 2.3 du 31/12/2011
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
//fichiers necessaires a l'exploitation de l'API
$BASEDIR="/var/www";
include "$BASEDIR/lcs/includes/headerauth.inc.php";
include "$BASEDIR/Annu/includes/ldap.inc.php";
include "$BASEDIR/Annu/includes/ihm.inc.php";  
	
//si la page est appelee par un utilisateur non identifie ou non autorise
if (!isset($_SESSION['login'])) exit;
elseif ((ldap_get_right("Cdt_is_cpe",$_SESSION['login'])=="N") &&  (ldap_get_right("Cdt_can_sign",$_SESSION['login'])=="N")) exit;
$tsmp=time();
$tsmp2=time() - 7776000;//j-90

// Connexion a la base de donnees
require_once ('../Includes/config.inc.php');

if (isset($_GET['fn'])) $nom =$_GET['fn'];
if (isset($_GET['kl']))
    {
     $nom =$_GET['kl'];
    $filtre="cn=".$nom;
    $grp_cl=search_groups($filtre);
    if (count($grp_cl[0])==0) $grp_cl=search_groups("cn=Classe_*".$nom); 
    $uids = search_uids ("(cn=".$grp_cl[0]["cn"].")", "half");
    $users = search_people_groups ($uids,"(sn=*)","cat");

    }

function affiche_abs($potache) {
    global $dtajadebut, $dtajafin;
    //$potache=$_POST["eleve"];
    $horaire = array("M1","M2","M3","M4","M5","S1","S2","S3","S4","S5");	
    $nbabs=0;$nbrtd=0;
    foreach ( $horaire as $cle => $val)
        {
        $rq4= "SELECT count(*) FROM absences WHERE  $val='A'  AND uideleve='$potache' AND date >='$dtajadebut' AND date<='$dtajafin' ";
        $result4 = @mysql_query ($rq4) or die (mysql_error()); 
        while ($nb = mysql_fetch_array($result4, MYSQL_NUM)) 
            {
             $nbabs+=$nb[0];
            }
        $rq5= "SELECT count(*) FROM absences WHERE   $val='R'  AND uideleve='$potache' AND date >='$dtajadebut' AND date<='$dtajafin' ";
        $result5 = @mysql_query ($rq5) or die (mysql_error()); 
        while ($nb = mysql_fetch_array($result5, MYSQL_NUM)) 
            {
             $nbrtd+=$nb[0];
            }	
        }//fin foreach $horaire	

    if ($nbabs>0)
        {
         echo "<h3 class='perso'>".$nbabs."h d'absence  - ";
        }
    else echo "<h3 class='perso'>Aucune absence - ";
    if ($nbrtd>0)
        {
        if ($nbrtd>1) echo $nbrtd." retards  <br />"; else echo $nbrtd." retard  </h3>";
        }
    else echo "Aucun retard </h3>";

    $rq = "SELECT DATE_FORMAT(date,'%d/%m/%Y'),M1,motifM1,M2,motifM2,M3,motifM3,M4,motifM4,M5,motifM5,
    S1,motifS1,S2,motifS2,S3,motifS3,S4,motifS4,S5,motifS5,date FROM absences WHERE   uideleve='$potache' AND
    date >='$dtajadebut' AND date<='$dtajafin' ORDER BY date ASC";
    
    // lancer la requete
    $result = mysql_query ($rq) or die (mysql_error());
    
    // Combien y a-t-il d'enregistrements ? 
    $nb2 = mysql_num_rows($result); 
    while ($ligne = mysql_fetch_array($result, MYSQL_NUM)) 
        { 
        $typmat ="";
        foreach ( $horaire as $cle => $val)
            {
            if ($ligne[2*$cle+1]==A) $typmat .=" : absence en $val ( ".utf8_encode($ligne[2*$cle+2])." )";
            elseif ($ligne[2*$cle+1]==R) $typmat .=" : retard en $val ( ".utf8_encode($ligne[2*$cle+2])." )";
            else $typmat .="";
            }
        echo "&nbsp;&nbsp;- Le ".LeJour(strToTime($ligne[21]))." ".$ligne[0]." " . $typmat ."<br />";
        }
    }//fin function
    
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
        <meta name="author" content="Philippe LECLERC -TICE CAEN" />
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<title>module &#139;(+_-)/&#155; n�2 </title>
	<link rel="stylesheet" type="text/css" href="../style/style.css"  media="screen" />
	<link rel="stylesheet" href="../style/style_imp.css" type="text/css" media="print" />
	<link  href="../../../libjs/jquery-ui/css/ui-lightness/jquery-ui.css" rel="stylesheet" type="text/css" />
	<!--[if IE]>
                  <link href="../style/style-ie.css"  rel="stylesheet" type="text/css"/>
                  <![endif]-->
	<script type="text/javascript" src="../../../libjs/jquery/jquery.js"></script>
	<script type="text/javascript" src="../../../libjs/jquery-ui/jquery-ui.js"></script>
	<script type="text/javascript" src="../Includes/JQ/cdt-script.js"></script>

</head>
<body >
<h1 class='title'></h1>
<?php
if (isset($_GET['df'])) 
    {
    $Morceauc=explode('/',$_GET['df']);
    $jour_c=$Morceauc[0];
    $mois_c=$Morceauc[1];
    $an_c=$Morceauc[2];
    $dtajafin= $an_c."/".$mois_c."/".$jour_c;
    $dtajac_dif=$jour_c."/".$mois_c."/".$an_c;
    $mesdatefin= " au ". $dtajac_dif;	
    }
if (isset($_GET['dd'])) 
    {
    $Morceauf=explode('/',$_GET['dd']);
    $jour_f=$Morceauf[0];
    $mois_f=$Morceauf[1];
    $an_f=$Morceauf[2];
    $dtajadebut= $an_f."/".$mois_f."/".$jour_f;
    $dtajaf_dif=$jour_f."/".$mois_f."/".$an_f;
    $mesdatedebut= " du ". $dtajaf_dif;
    }
?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" >
<fieldset id="field7">
<legend id="legende">Bilan des Absences &amp; Retards  de <?php echo $nom;?><?php echo $mesdatedebut . $mesdatefin ?></legend>
<?php
if (isset($_GET['dd']))
    {
    echo '<div id="abs-contenu">';
    if (isset($_GET['kl']))
        {
        for ($loop=0; $loop<count($users);$loop++) 
            {
            echo "<b>".$users[$loop]["fullname"] ."</b>  : ";
            affiche_abs($users[$loop]["uid"]);
            echo"<br />";
            }
        }
    else
        {
        affiche_abs($_GET['uid']);
        }
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
