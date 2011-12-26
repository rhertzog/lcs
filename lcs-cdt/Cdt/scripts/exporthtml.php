<?php
/* ==================================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 2.3 du 31/12/2011
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script d'export html-
			_-=-_
   =================================================== */
session_name("Cdt_Lcs");
@session_start(); 
error_reporting(0);
//si la page est appelee par un utilisateur non identifie
if (!isset($_SESSION['login']) )exit;
include "../Includes/basedir.inc.php";
include "$BASEDIR/lcs/includes/headerauth.inc.php";
include "$BASEDIR/Annu/includes/ldap.inc.php";
include "$BASEDIR/Annu/includes/ihm.inc.php";

//si la page est appelee par un utilisateur non "direction"
if (ldap_get_right("Cdt_can_sign",$_SESSION['login'])=="N") exit;

// Connexion a la base de donnees
require_once ('../Includes/config.inc.php');
include_once("../Includes/fonctions.inc.php");
$filesql = "Archives_Cdt_".date("d-m-Y\_H\hi")."_".$domain.".html";
$maintenant = date('D, d M Y H:i:s') . ' GMT';
header('Content-Type: application/octet-stream');
header('Expires: ' . $maintenant);
if (mb_ereg('MSIE', $_SERVER['HTTP_USER_AGENT'])) 
    {
    header('Content-Disposition: attachment; filename="' . $filesql . '"');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    } 
else 
    {
    header('Content-Disposition: attachment; filename="' . $filesql . '"');
    header('Pragma: no-cache');
    }

//recuperation des donnees
$list_classe=array();

//liste des classes
$rq="SELECT DISTINCT `classe` FROM `onglets` WHERE `classe` IS NOT NULL";
$result = @mysql_query ($rq);
if ($result)
    {
    $n=0;	
    while ($enrg = mysql_fetch_array($result, MYSQL_NUM))
        {
        $list_classe[$n]=$enrg[0];
        $n++;
        }
    }
mysql_free_result($result);
//afficher entete
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html  xmlns="http://www.w3.org/1999/xhtml" >
<head>
<title>Archivage au format HTML</title>
<meta name="author" content="Philippe LECLERC -TICE CAEN" />
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<link rel="shortcut icon" href="favicon.ico" type="image/x-icon"/>
<style type="text/css">
  
a:link { color: #fbcb09 }
a:visited {color: #ffffff; }
a:hover {color: #fcc; }
table#cdt {border: solid 4px #fce3b2;width:100%; font-size: 14px;font-family: Arial, Helvetica, sans-serif; margin:0;padding:0px;border-collapse: collapse;}
table#cdt h2 {color:#fefefe;}
table#cdt th.haut{background-color:#1B2C68;}
table#cdt th.nameclasse{;}
table#cdt th.bas{background:#1B2C68; margin-bottom: 10px ;}
p.rmq{color:#ffffcc;}
td.hpetite {width: 10%;border: solid 1px #fdb218;background:#999;text-align: center;font-weight: bold;margin: 0;padding:3px;}
td.hgrande {width: 35%;border: solid 1px #fdb218;background:#999;text-align: center;font-weight: bold;margin: 0;padding:3px;}
td.date{width:10% ;background:#9ec3a5; border: solid 1px #fdb218;text-align: center;font-weight: bold;}
td.petite{width: 10%;border: solid 1px #fdb218;background:#ccf;padding:3px;text-align: center;font-weight: bold;}
td.grande{width: 35%;border: solid 1px #fdb218;background:#fff;padding:3px;}
th.titre {background-color:#9757b5;color:#fff;}
table#cdt thead img {vertical-align: middle;float: left;border: 0px;margin-left: 10px;}
  </style>
</head>
<body>
<table id="cdt" cellspacing="1">
<thead>
<tr><th colspan="5" class="haut" ><a name="liste"></a>
<h2> - Archives du cahier de textes - </h2>
<p class="rmq"> Les donn&#233;es sont class&#233;es par Classe/groupe puis par date croissante<br />
(pensez &agrave; la fonction "Rechercher" de votre navigateur)<br />
Liens vers les classes et groupes <br /></p>
<?php
for ($loop=0; $loop < count ($list_classe)  ; $loop++)
    {
    echo '<a href="#'.$list_classe[$loop].'">'.$list_classe[$loop].'</a> - ';
    }
?>
<p><img src="http://www.w3.org/Icons/valid-xhtml10" alt="Valid XHTML 1.0 Strict" height="31" width="88" /></p></th></tr>
<tr> <td class="hpetite" >Date</td><td  class="hpetite">Enseignant</td><td  class="hpetite">Mati&#232;re</td>
<td class="hgrande">Contenu de la s&#233;ance </td><td class="hgrande">Travail &agrave; faire</td></tr>
</thead>
<?php
for ($loop=0; $loop < count ($list_classe)  ; $loop++)
    {
    echo ' <tr><th colspan="5" class="titre"><a name="'.$list_classe[$loop].'">'.$list_classe[$loop].' </a></th></tr>';

    //liste des dates
    $rq="SELECT date,DATE_FORMAT(date,'%d/%m/%Y') from cahiertxt WHERE id_auteur IN ( SELECT id_prof from onglets WHERE classe='$list_classe[$loop]') GROUP BY date ORDER BY date asc";
    $result = @mysql_query ($rq);
    if ($result)
        {
        $list_dates=array();
        $n=0;	
        while ($enrg = mysql_fetch_array($result, MYSQL_NUM))
            {
            $list_dates[$n]=$enrg[0];
            $list_dattes[$n]=$enrg[1];
            $n++;
            }
        }
    mysql_free_result($result);
    /// 
    if (count ($list_dates)>0)
        {
        // pour chaque jour
        for ($loop1=0; $loop1 < count ($list_dates); $loop1++)
            {
            //recherche des id du jour
            $rq="SELECT id_auteur,contenu,afaire from cahiertxt WHERE date='$list_dates[$loop1]' AND 
             id_auteur IN ( SELECT id_prof from onglets WHERE classe='$list_classe[$loop]') ORDER BY id_auteur asc";
            //if ($list_classe[$loop]=='ATI1') {echo $rq;exit;}
            $result = @mysql_query ($rq);
            if ($result)
                {
                $id=array();$cours=array();$afR=array();
                $n=0;	
                while ($enrg = mysql_fetch_array($result, MYSQL_NUM))
                    {
                    $id[$n]=$enrg[0];
                    $cours[$n]=utf8_encode(mb_ereg_replace("../../../","http://".$hostname.".".$domain."/",$enrg[1]));
                    $afR[$n]=utf8_encode(mb_ereg_replace("../../../","http://".$hostname.".".$domain."/",$enrg[2]));
                    $n++;
                    }
                }
            mysql_free_result($result);	
            echo '<tr>
                    <td rowspan="'.count($id).'" class="date">
                    <p>'.LeJour(strToTime($list_dates[$loop1])).'<br />'.$list_dattes[$loop1].'</p>
                    </td>';
            // pour chaque jour
            for ($loop2=0; $loop2 < count ($id); $loop2++)
                {
                //recherche donnes auteur
                $rq="SELECT prefix,prof,matiere from onglets WHERE id_prof='$id[$loop2]'";
                //echo $rq;exit;
                $result = @mysql_query ($rq);
                if ($result)
                    {
                    while ($enrg = mysql_fetch_array($result, MYSQL_NUM))
                        {
                        $Prof=utf8_encode($enrg[0])." ".utf8_encode($enrg[1]);
                        $matiere=utf8_encode($enrg[2]);
                        }
                    }
                mysql_free_result($result);
                if ($loop2 > 0)  echo '<tr>';
                echo '<td  class="petite">
                '.$Prof.'
                </td>
                <td  class="petite">
                '.$matiere.'
                </td>
                <td class="grande">
                '.$cours[$loop2].'
                </td>
                <td class="grande">
                '.$afR[$loop2].'
                </td>				
                </tr>';
                }//fin ligne de donnees
            }//fin jour
        }
echo '<tr><th colspan="5"  class="bas"><a href="#liste">Retour &#224; la liste des classes </a></th></tr>';
}
?>
</table>
</body>
</html>