<?php
/* =============================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 2.5 du 10/04/2014
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script de consultation des absences hebdomadaires -
			_-=-_
    "Valid XHTML 1.0 Strict"
   ============================================= */
session_name("Lcs");
@session_start();
//fichiers necessaires a l'exploitation de l'API
$BASEDIR="/var/www";
//include "../Includes/basedir.inc.php";
include "$BASEDIR/lcs/includes/headerauth.inc.php";
include "$BASEDIR/Annu/includes/ldap.inc.php";
include "$BASEDIR/Annu/includes/ihm.inc.php";

//si la page est appelee par son URL
if ((!isset($_SESSION['version'])) || (!isset( $_SESSION['saclasse']) && !isset($_SESSION['login'])) ) exit;
elseif (isset($_GET['uid']))
    {
    //test si uid est un enfant de parent
    if (isset($_SESSION['parentde']))
        {
        $parent_ok="false";
        foreach ( $_SESSION['parentde'] as $key => $value)
            {
            if (in_array($_GET['uid'], $value)) $parent_ok="true";
            }
        }
    //exit si l'uid passe n'est pas autorise
    if (
        (($_SESSION['cequi']!="eleve") || ($_SESSION['login'] != $_GET['uid']))
        && ($_SESSION['cequi']!="prof")
        && (ldap_get_right("Cdt_is_cpe",$_SESSION['login'])=="N")
        && (ldap_get_right("Cdt_can_sign",$_SESSION['login'])=="N")
        && ( !isset($_SESSION['parentde']) || $parent_ok=="false")
        ) exit;
    }

$cren_off=array();
include ("../Includes/config.inc.php");
include ("../Includes/fonctions.inc.php");
include ("../Includes/creneau.inc.php");

//memorisation des parametres POST classe et matiere renvoyes par le formulaire
if (isset($_GET['fn'])) $nom =$_GET['fn'];
if (isset($_POST['nomeleve'])) $nom = $_POST['nomeleve'];
if (isset($_GET['uid'])) $potache =$_GET['uid'];
if (isset($_POST['gamin'])) $potache = $_POST['gamin'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html  xmlns="http://www.w3.org/1999/xhtml" >
<head>
<title>Absences</title>
<meta name="author" content="Philippe LECLERC -TICE CAEN" />
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<link href="../style/style.css" rel="stylesheet" type="text/css" />
<!--[if IE]>
<link href="../style/style-ie.css"  rel="stylesheet" type="text/css"/>
<![endif]-->
</head>
<body>

<?php

//Determination du jour courant
if (isset($_POST['JourCourant']))
    {
    $JourCourant = $_POST['JourCourant'];
    //semaine suivante
    if (isset($_POST['suiv']))
        {
        $Lundi=DebutSemaine($JourCourant + 7 * 86400);
        }
    //semaine precedente
    elseif (isset($_POST['prec']))
        {
        //tableau hebdomadaire commencant au Lundi
        $Lundi=DebutSemaine($JourCourant - 7 * 86400);
        }
    else
        {
        $Lundi=DebutSemaine($JourCourant);
        }
    }
    else
        {
        $Lundi=DebutSemaine();
        $la=time();
        }

//fin de la semaine
$Samedi = $Lundi + 432000; //5 * 86400
//Date du debut de la semaine courante
$datdebut=date('Ymd',$Lundi);
//Date de la fin de la semaine courante
$datfin=date('Ymd',$Samedi);

$datd=getdate($Lundi);
$dtdebut= date('Y',$datd['0'])."/".date('m',$datd['0'])."/".date('d',$datd['0']);
$datf=getdate($Samedi );
$dtdfin= date('Y',$datf['0'])."/".date('m',$datf['0'])."/".date('d',$datf['0']);

//Recherche des absences /retards pour la semaine courante
$rq = "SELECT M1,M2,M3,M4,M5,S1,S2,S3,S4,S5,DATE_FORMAT(date,'%d'),DATE_FORMAT(date,'%m'),DATE_FORMAT(date,'%Y'),
motifM1,motifM2,motifM3,motifM4,motifM5,motifS1,motifS2,motifS3,motifS4,motifS5 FROM absences WHERE
 uideleve='$potache' AND date >='$dtdebut' AND date<='$dtdfin' ORDER BY date ASC";
$res = mysqli_query($GLOBALS["___mysqli_ston"], $rq);
$nb = mysqli_num_rows($res);
//si des absences/retards ont ete programmes
if ($nb>0)
    {
    //pour chaque absence/retard programme
    while ($row = mysqli_fetch_array($res,  MYSQLI_NUM))
        {
        //determination du timestamp
        $tsmp=mkTime(8,0,0,$row[11],$row[10],$row[12]);
        //tsmp=mkTime(8,0,0,$row[2],$row[1],$row[3]);
        // on parcourt les jours de la semaine
        for ($j=0; $j<=5; $j++)
            {
            $jour = $Lundi + $j * 86400;
            // on parcourt les heures de la  journee
            for ($h=0; $h<=9; $h++)
                {
                if ($jour==$tsmp)
                    {
                    if ($row[$h]!="")
                        {
                        $plan[$j][$h] = $row[$h];
                        $why[$j][$h]= utf8_encode($row[$h+13]);
                        }
                    }
                }
            }
        }
    }

?>

<!-- affichage du calendrier hebdomadaire -->
<form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="post"  id="planning">
<fieldset id="field7">
<legend id="legende"><?php echo $nom;?></legend>
<div id="abs-contenu">
<!-- Affichage des boutons -->
<input name="JourCourant" type="hidden" id="JourCourant" value="<?php echo $Lundi;?>" />
<input name="gamin" type="hidden"  value="<?php echo $potache;?>" />
<input name="nomeleve" type="hidden"  value="<?php echo $nom;?>" />
<table id="btn-plan-cdt">
<tr>
<td  ><input type="submit" name="prec" value="&lt;&lt;" class="bt50" /></td>
<td class="hebdo"> <?echo datefr2($Lundi)?> - <?echo datefr2($Samedi);?></td>
<td ><input type="submit" name="suiv" value="&gt;&gt;" class="bt50" /></td>
</tr>
</table>
<table id="plan-cdt" cellpadding="1" cellspacing="1">
<thead>
<tr><th> </th>
<?php
// Affichage des jours et dates de la semaine en haut du tableau"j-M-Y",
for ($i=0; $i<=5; $i++)
    {
    $TS = $Lundi+$i*86400;
    echo '<td class="abs" >'.LeJour($TS)."</td>";
    }
?>
</tr></thead>
<tbody>
<?php
$horaire = array("M1","M2","M3","M4","M5","S1","S2","S3","S4","S5");
for ($h=0; $h<=9; $h++)
    {
    if (in_array($horaire[$h], $cren_off))
        {
        echo '<tr><td class="mi-jour" colspan="7"></td></tr>';
        continue;
        }
    //Affichage de la designation des creneaux horaires
    echo "<tr><th>".$horaire[$h]."</th>\n";

    //Affichage du contenu des creneaux horaires
    for ($j=0; $j<=5; $j++)
        {
        if (isset($plan[$j][$h]))
            {
            if ($plan[$j][$h]=="A") echo '<td class="absence"><a href="" title="'.$why[$j][$h].'">A</a></td>'."\n";
            elseif ($plan[$j][$h]=="R") echo '<td class="retard"><a href="" title="'.$why[$j][$h].'">R</a></td>'."\n";
            elseif ($plan[$j][$h]=="") echo '<td class="libre">-</td>'."\n";
            }
        else echo '<td class="libre">-</td>'."\n";

        //cellules de separation a la mi-journee
        if (($h==4)&&($j==5)) echo '</tr><tr><td class="mi-jour" colspan="7"></td>';
        }
    echo "</tr>\n";
    }
?>
</tbody>
</table></div>
<div><p></p>
 <?php
echo "<script type=\"text/javascript\">
        //<![CDATA[
        document.write('<a href=\"javascript:window.close()\" id=\"bt-close\"></a></div>');
         //]]>
        </script>";
?>
</div>
</fieldset>
</form>
<?php
Include ('../Includes/pied.inc');
?>
</body>
</html>