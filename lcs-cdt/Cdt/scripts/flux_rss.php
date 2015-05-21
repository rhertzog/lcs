<?php
/* =============================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 2.5 du 10/04/2014
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script de generation du flux RSS -
			_-=-_
   ============================================= */
session_name("Lcs");
@session_start();
//if ((!isset($_SESSION['version'])) || (!isset( $_SESSION['saclasse']) && !isset($_SESSION['login'])) ) exit;
include ('../Includes/data.inc.php');
include "../Includes/functions2.inc.php";
include ("/var/www/Annu/includes/ldap.inc.php");
include ("/var/www/lcs/includes/headerauth.inc.php");
include ("/var/www/Annu/includes/ihm.inc.php");

function xml_character_encode($string, $trans='') {
    $trans = (is_array($trans)) ? $trans : get_html_translation_table(HTML_ENTITIES, ENT_QUOTES);
    foreach ($trans as $k=>$v)
    $trans[$k]= "&#".ord($k).";";
    return strtr($string, $trans);
}

// Connexion a la base de donnees
require_once ('../Includes/config.inc.php');
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

//controle des parametres $_GET
if (!isset($_GET['div'])) exit;
else
    {
    $divret=explode(':',$_GET['div']);
    if ($divret[1]!= substr(md5(crypt($divret[0],$Grain)),2))
    exit;
    }
$ch=$divret[0];
//echo "toto";
$cmd="hostname -f";
exec($cmd,$hn,$retour);
$hostn= $hn[0];


// Creer la requete (Recuperer les rubriques de la classe)
$rq = "SELECT prof,matiere,id_prof,prefix FROM onglets
 WHERE classe='$ch' ORDER BY 'id_prof' asc ";

 // lancer la requete
$result = @mysqli_query($GLOBALS["___mysqli_ston"], $rq) or die (((is_object($dbc)) ? mysqli_error($dbc) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
$nb = mysqli_num_rows($result);  // Combien y a-t-il d'enregistrements ?

if ($nb>0)
    {
    //on recupere les donnees
    $loop=0;
    while ($enrg = mysqli_fetch_array($result,  MYSQLI_NUM))
        {
        $prof[$loop]=$enrg[0];//nom du prof
        $mat[$loop]=$enrg[1];//matiere
        $numero[$loop]=$enrg[2];//numero de l'onglet
        $pref[$loop]=$enrg[3];// prefixe
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
            $rq = "SELECT prof,matiere,id_prof,prefix FROM onglets  WHERE classe='{$groups[$loopo]["cn"]}' ORDER BY 'id_prof' asc ";
            $result = @mysqli_query($GLOBALS["___mysqli_ston"], $rq) or die (((is_object($dbc)) ? mysqli_error($dbc) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
            $nb = mysqli_num_rows($result);  // Combien y a-t-il d'enregistrements ?
            if ($nb>0)
                {
                //on recupere les donnees
                while ($enrg = mysqli_fetch_array($result,  MYSQLI_NUM))
                    {
                    $prof[$loop]=$enrg[0];//nom du prof
                    $mat[$loop]=$enrg[1];//matiere
                    $numero[$loop]=$enrg[2];//numero de l'onglet
                    $pref[$loop]=$enrg[3];// prefixe
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
            $result = @mysqli_query($GLOBALS["___mysqli_ston"], $rq) or die (((is_object($dbc)) ? mysqli_error($dbc) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
            $nb = mysqli_num_rows($result);  // Combien y a-t-il d'enregistrements ?
            if ($nb>0)
                {
                //on recupere les donnees
                while ($enrg = mysqli_fetch_array($result,  MYSQLI_NUM))
                    {
                    $prof[$loop]=$enrg[0];//nom du prof
                    $mat[$loop]=$enrg[1];//matiere
                    $numero[$loop]=$enrg[2];//numero de l'onglet
                    $pref[$loop]=$enrg[3];// prefixe
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
    $dat2=date('YmdHis',$tsmp);
    //recuperation des travaux a faire pour la classe
    $ind=0;
    for ($loop=0; $loop < count($numero) ; $loop++)
        {
        $rq = "SELECT afaire,DATE_FORMAT(datafaire,'%d/%m/%Y'),id_rubrique,datafaire FROM cahiertxt
        WHERE (id_auteur='$numero[$loop]') AND (datafaire<='$dat') AND (datafaire>='$dat2') AND (afaire!='') AND datevisibi<='$dat2'";

        // lancer la requete
        $result = @mysqli_query($GLOBALS["___mysqli_ston"], $rq) or die (((is_object($dbc)) ? mysqli_error($dbc) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

        // Combien y a-t-il d'enregistrements ?
        $nb2 = mysqli_num_rows($result);
        //on fait un tableau de donnees
        while ($ligne = mysqli_fetch_array($result,  MYSQLI_NUM))
            {
            $idtaf[$ind]=$ligne[2];
            $idtafcrypt[$ind]=$ligne[2].":".substr(md5(crypt($ligne[2],$Grain)),2);
            $mattaf[$ind]=$mat[$loop];
            $preftaf[$ind]=$pref[$loop];
            $proftaf[$ind]=$prof[$loop];
            $texttaf[$ind]=addSlashes(strip_tags(stripslashes($ligne[0])));
            if (strlen($texttaf[$ind])>200) $texttaf[$ind]=substr($texttaf[$ind],0,199);
            $dattaf[$ind]=$ligne[1];
            $tsmpaf[$ind]=$ligne[3];
            $ind++;
            }
        }

    //on trie les donnees par dates croissantes
    if (count($mattaf)>0)
        {
        array_multisort($tsmpaf,$dattaf,$mattaf,$idtaf,$proftaf,$preftaf,$texttaf,$idtafcrypt);
        }
    }

echo '<?xml version="1.0" encoding="ISO-8859-15" ?>'."\n";
echo '<rss version="2.0">
<channel>
<generator>LCS RSS</generator>
<title>Travail &#224; faire en '.$ch.'</title>
<link>http://'.$hostn.'/</link>
<description>Travaux donn&#233;s &#224; la classe enti&#232;re ET &#224; tous les groupes</description>
<image>
<url>../images/appli22_on.png</url>
</image>
<language>fr</language>
<ttl>5</ttl>'."\n\n";

if (count($mattaf)>0)
    {
    for ($loop=0; $loop < count($dattaf) ; $loop++)
        {
        echo '<item>
        <title>'.stripslashes(xml_character_encode($mattaf[$loop])).' :  '.$preftaf[$loop] .'  '. $proftaf[$loop].'</title>
        <description> Pour le '.$dattaf[$loop]."/&lt;br /&gt;".stripslashes(xml_character_encode($texttaf[$loop])).' </description>
        <link>http://'.$hostn.'/Plugins/Cdt/scripts/poprss.php?id='.$idtafcrypt[$loop].'</link>
        </item>'. "\n";
        }
    }
else
    {
    echo '<item>
    <title>Rien de programm&#233; !</title>
    <description> Mais en cherchant bien ...</description>
    <link>http://'.$hostn.'/</link>
    </item>'. "\n";
    }
echo '</channel>' . "\n" . '</rss>' . "\n";
?>