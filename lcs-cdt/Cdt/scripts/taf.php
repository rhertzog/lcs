<?php
/* =============================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 2.5 du 20/04/2012
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script du Travail A Faire ELEVE -
   			_-=-_
  "Valid XHTML 1.0 Strict"
   ============================================= */
session_name("Cdt_Lcs");
@session_start();
if ((!isset($_SESSION['version'])) || (!isset( $_SESSION['saclasse']) && !isset($_SESSION['login'])) ) exit;
include ('../Includes/data.inc.php');
include "../Includes/functions2.inc.php";
include ("/var/www/Annu/includes/ldap.inc.php");	
include ("/var/www/lcs/includes/headerauth.inc.php");
include ("/var/www/Annu/includes/ihm.inc.php"); 
//initialisation 
$tsmp=time();
$tsmp15=$tsmp+1209600;
$Delta=0;
if (is_file("../Includes/vac.inc.php")) 
    {
    include ("../Includes/vac.inc.php");
    for ($loop=0; $loop < count ($debv) ; $loop++)
        {
        // t15 pendant vacances
        if ( $tsmp15>$debv[$loop] && $tsmp15<$finv[$loop]) 
            {
            $Delta=$finv[$loop]-$debv[$loop] ;
            break;
            }
        //to avant vacances & t15 apres fin vacances
        if ( $tsmp<$debv[$loop] && $tsmp15>$finv[$loop]) 
            {
            $Delta=$finv[$loop]-$debv[$loop] ;
            break;
            }
         //to pendant vacances &t15 apres fin vacances
        if ( ($tsmp>$debv[$loop] && $tsmp<$finv[$loop]) && $tsmp15>$finv[$loop])
            {
            $Delta=$finv[$loop]-$tsmp;
            break;
            }
        //to pendant vacances &t15 avant fin vacances
        if ( ($tsmp>$debv[$loop] && $tsmp<$finv[$loop]) && $tsmp15<$finv[$loop]) 
            {
            $Delta=$finv[$loop]-$tsmp15[$loop] + 1209600;
            break;
            }
        }
    }

function is_authorized($x) {
$flg="false";
foreach ($_SESSION['saclasse'] as $cle => $valeur)
    { 
    if ($valeur==$x) 
        {
        $flg="true";
        break;
        }
    }
return $flg;
}

//contrôle des parametres $_GET
if ((isset($_GET['div'])) && (isset($_SESSION['saclasse']))) {	
if (is_authorized($_GET['div'])=="false")   exit;
}
//recuperation de la classse concernee
if(isset($_GET['div'])) $ch=$_GET['div'];
else exit;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html  xmlns="http://www.w3.org/1999/xhtml" >
<head>
<title>R&#233;sum&#233; du travail &#224; faire</title>
<meta name="author" content="Philippe LECLERC -TICE CAEN" />
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<link href="../style/style.css" rel="stylesheet" type="text/css" />
<!--[if IE]>
<link href="../style/style-ie.css"  rel="stylesheet" type="text/css"/>
<![endif]-->
</head>
<body>
<h1 class='title'>Travail &#224; faire pour la classe de <?echo $ch;?> </h1>
<div id="taf">
<?php

// Connexion a la base de donnees
require_once ('../Includes/config.inc.php');
if (( isset($_SESSION['parentde']))  && (!isset($_SESSION['login'])))
    {
    foreach ( $_SESSION['parentde'] as $cle => $valcla)
        {
        if ($valcla[2]==$ch)
            {
            $uid_actif=$valcla[0];
            }
        }	
    }
	
if ($_SESSION['cequi']=="eleve") $uid_actif=$_SESSION['login'];	
// Creer la requete (Recuperer les rubriques de la classe) 
$rq = "SELECT prof,matiere,id_prof,prefix FROM onglets  WHERE classe='$ch' ORDER BY 'id_prof' asc ";
// lancer la requete
$result = @mysql_query ($rq) or die (mysql_error());
$nb = mysql_num_rows($result);  // Combien y a-t-il d'enregistrements ?
if ($nb>0)
    {
    //on recupere les donnees
    $loop=0;
    while ($enrg = mysql_fetch_array($result, MYSQL_NUM)) 
        {
        $prof[$loop]=$enrg[0];//nom du prof
        $mat[$loop]=utf8_encode($enrg[1]);//matiere
        $numero[$loop]=$enrg[2];//numero de l'onglet
        $pref[$loop]=utf8_encode($enrg[3]);// prefixe
        $loop++;
        }
      }
//recherche des onglets "cours d'un eleve"
if ($uid_actif!="") 
    {
    $groups=people_get_cours($uid_actif);
    if ( count($groups) > 0 ) 
        {
        for ($loopo=0; $loopo < count ($groups) ; $loopo++) 
            {
            $rq = "SELECT prof,matiere,id_prof,prefix FROM onglets
            WHERE classe='{$groups[$loopo]["cn"]}' ORDER BY 'id_prof' asc ";
            $result = @mysql_query ($rq) or die (mysql_error());
            $nb = mysql_num_rows($result);  // Combien y a-t-il d'enregistrements ? 
            if ($nb>0)
                {
                //on recupere les donnees 
                while ($enrg = mysql_fetch_array($result, MYSQL_NUM)) 
                    {
                    $prof[$loop]=$enrg[0];//nom du prof
                    $mat[$loop]=utf8_encode($enrg[1]);//matiere
                    $numero[$loop]=$enrg[2];//numero de l'onglet
                    $pref[$loop]=utf8_encode($enrg[3]);// prefixe
                    $loop++;
                    }
                }
            }
        }
    }
else 
    {
    //recherche des onglets "Cours" de la classe
    if (!mb_ereg("^Classe",$ch)) 
        {
        $grp_cl=search_groups("cn=Classe_*".$ch);
        $grp_cl=$grp_cl[0]["cn"];
        }
    else $grp_cl=$ch;
    $uids = search_uids ("(cn=".$grp_cl.")", "half");	
    $liste_cours=array();
    $i=0;
    for ($loup=0; $loup < count($uids); $loup++)
        {
        $logun= $uids[$loup]["uid"];
        if (is_eleve($logun)) 
            {
            $groops=people_get_cours($logun);
            if (count($groops))
                {
                for($n=0; $n<count($groops); $n++)
                    { 
                    if (!in_array($groops[$n]["cn"], $liste_cours)) 
                        {	
                        $liste_cours[$i]=$groops[$n]["cn"];
                        $i++;
                        }
                    }
                }
            }
        }
    if (count($liste_cours)>0)
        {
        for($n=0; $n<count($liste_cours); $n++)
            {
            $rq = "SELECT prof,matiere,id_prof,prefix FROM onglets
            WHERE classe='{$liste_cours[$n]}' ORDER BY 'id_prof' asc ";
            $result = @mysql_query ($rq) or die (mysql_error());
            $nb = mysql_num_rows($result);  // Combien y a-t-il d'enregistrements ? 
            if ($nb>0)
                {
                //on recupere les donnees 
                while ($enrg = mysql_fetch_array($result, MYSQL_NUM)) 
                    {
                    $prof[$loop]=$enrg[0];//nom du prof
                    $mat[$loop]=utf8_encode($enrg[1]);//matiere
                    $numero[$loop]=$enrg[2];//numero de l'onglet
                    $pref[$loop]=utf8_encode($enrg[3]);// prefixe
                    $loop++;
                    }
                }
            }
        }
    }
//fin modif
if (count($numero)>0)
    {
    //elaboration des dates limites
    $dat=date('Ymd',$tsmp+1209600+$Delta);//J+15
    //echo $dat;exit;
    $dat2=date('YmdHis',$tsmp);
    //recuperation des travaux a faire pour la classe
    $ind=0;
    for ($loop=0; $loop < count($numero) ; $loop++)
        {
        $rq = "SELECT afaire,DATE_FORMAT(datafaire,'%d/%m/%Y'),id_rubrique,datafaire FROM cahiertxt 
        WHERE (id_auteur='$numero[$loop]') AND (datafaire<='$dat') AND (datafaire>='$dat2') AND (afaire!='') AND datevisibi<='$dat2'";
        // lancer la requete
        $result = mysql_query ($rq) or die (mysql_error());
        // Combien y a-t-il d'enregistrements ?
        $nb2 = mysql_num_rows($result);
        //on fait un tableau de donnees
        while ($ligne = mysql_fetch_array($result, MYSQL_NUM)) 
            { 
            $idtaf[$ind]=$ligne[2];
            $mattaf[$ind]=$mat[$loop];
            $preftaf[$ind]=$pref[$loop];
            $proftaf[$ind]=$prof[$loop];
            $texttaf[$ind]=$ligne[0];
            $dattaf[$ind]=$ligne[1];
            $tsmpaf[$ind]=$ligne[3];
            $ind++;
            }
        }
    //on trie les donnees par dates croissantes
    if (count($mattaf)>0)
        {
        array_multisort($tsmpaf,$dattaf,$mattaf,$idtaf,$proftaf,$preftaf,$texttaf);
        }
    //fin recup
    //affichage
    if (count($mattaf)>0)
        {
        echo('<table id="tb-cdt">');
        for ($loop=0; $loop < count($dattaf) ; $loop++)
            {	
            $textafaire=$texttaf[$loop];//conversion du travail a faire
            if (!($loop>0 &&  ($dattaf[$loop] ==$dattaf[$loop-1])))
                {		 
                echo '<tbody>';
                echo '<tr><th colspan="2">Pour le '.$dattaf[$loop].'</th></tr>';}
                echo '<tr><td class="afaire">'.$mattaf[$loop].'<br />'.$preftaf[$loop].' '.$proftaf[$loop].'</td><td class="contenu">'.$textafaire.'</td></tr>';
                }
        echo "</tbody></table>";
        }
    else echo '<span class="nook">Aucun travail n\'est programm&#232; pour cette classe !</span>';
    }
else echo '<span class="nook">La rubrique n\'est pas encore cr&#232;&#232;e pour cette classe !</span>';
echo "<script type=\"text/javascript\">
        //<![CDATA[
        document.write('<div id=\"bt-abs\"><a href=\"javascript:window.close()\" id=\"bt-close\"></a></div>');
        //]]>
        </script>";
?>
</div> <!-- fin conteneur -->
<?php
include ('../Includes/pied.inc');
?>
</body>
</html>