<?php
/* =============================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 2.5 du 04/06/2015
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script archives perso du cahier de textes -
			_-=-_
    "Valid XHTML 1.0 Strict"
   ============================================= */
header("X-XSS-Protection: 0");
session_name("Lcs");
@session_start();

// autorisation d'acces ?
if ($_SESSION['cequi']!="prof" || !isset($_COOKIE['LCSuser'])) exit;
require_once '../Includes/htmlpur/library/HTMLPurifier.auto.php';

if (isset($_GET['arch']))
    {
    $config = HTMLPurifier_Config::createDefault();
    //$config->set('Core.Encoding', 'ISO-8859-15');
    $purifier = new HTMLPurifier($config);
    $arch=$purifier->purify($_GET['arch']);
    }
    else $arch="";

if (isset($_POST['Fermer']))
echo '<script type="text/javascript">
            //<![CDATA[
            window.close();
             //]]>
            </script>';

//si clic sur le bouton Copier-Coller
if (isset($_POST['copie']))
    {
    if (get_magic_quotes_gpc()) $textc1=mb_ereg_replace("\n","\\n",stripslashes($_POST['textecours']));
    else $textc1=mb_ereg_replace("\n","\\n",$_POST['textecours']);
    $textc=mb_ereg_replace("\r","\\r",$textc1);
    if (get_magic_quotes_gpc()) $textaf1=mb_ereg_replace("\n","\\n",stripslashes($_POST['texteafaire']));
    else $textaf1=mb_ereg_replace("\n","\\n",$_POST['texteafaire']);
    $textaf=mb_ereg_replace("\r","\\r",$textaf1);
            echo '<script type="text/javascript">
            //<![CDATA[
            opener.tinyMCE.execInstanceCommand("coursfield","mceInsertContent",false,"'.$textc.'");
            opener.tinyMCE.execInstanceCommand("afairefield","mceInsertContent",false,"'.$textaf.'");
            window.close();
             //]]>
            </script>';
    exit;
    }

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html  xmlns="http://www.w3.org/1999/xhtml" >
<head>
<title>Archives personnelles</title>
<meta name="author" content="Philippe LECLERC -TICE CAEN" />
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<link href="../style/style.css" rel="stylesheet" type="text/css" />
	<link  href="../style/deroulant.css" rel="stylesheet" type="text/css" />
	<link  href="../style/navlist-prof.css" rel="stylesheet" type="text/css" />
         <!--[if IE]>
        <link href="../style/style-ie.css"  rel="stylesheet" type="text/css"/>
        <![endif]-->
                    <script type="text/x-mathjax-config">
                    MathJax.Hub.Config({
                    tex2jax: {inlineMath: [['$','$'], ['\\(','\\)']]},
                    MMLorHTML: { prefer: { Firefox: "HTML" } }
                    });
                   </script>
                   <script type="text/javascript"  src="../../../libjs/MathJax/MathJax.js?config=TeX-AMS-MML_HTMLorMML"></script>

</head>

<body >

<?php
echo "<form action='";
echo htmlentities($_SERVER["PHP_SELF"]);
echo "' method='post'>";
echo '<div id="bt-fixe" ><input class="bt2-fermer" type="submit" name="Fermer" value="" /></div></form>';

// Connexion a la base de donnees
include_once "/var/www/lcs/includes/headerauth.inc.php";
if (isset($_COOKIE['LCSuser']))  $_LCSkey = urldecode( xoft_decode($_COOKIE['LCSuser'],$key_priv) );
$nom_bdd=mb_ereg_replace("\.","",$_SESSION['login']);
DEFINE ('DBP_USER', $_SESSION['login']);
DEFINE ('DBP_PASSWORD', $_LCSkey);
DEFINE ('DBP_HOST', 'localhost');
DEFINE ('DBP_NAME', $nom_bdd.'_db');

// Ouvrir la connexion et selectionner la base de donnees
$dbcp = @($GLOBALS["___mysqli_ston"] = mysqli_connect(DBP_HOST,  DBP_USER,  DBP_PASSWORD))
OR die ('Connexion a MySQL impossible : '.((is_object($dbcp)) ? mysqli_error($dbcp) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)).'<br />');
((bool)mysqli_query($dbcp, "USE " . constant('DBP_NAME')))
OR die ('Selection de la base de donnees impossible : '.((is_object($dbcp)) ? mysqli_error($dbcp) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)).'<br />');

echo '<div id="entete">';
echo '<div id="navcontainer">';
echo ("<ul id='arch-navlist'>");
/*================================
   -      Affichage des archives  -
   ================================*/

//recherche du  nom des archives
$TablesExist= mysqli_query($GLOBALS["___mysqli_ston"], "show tables");
$x=0;
while ($table=mysqli_fetch_row($TablesExist))
if (mb_ereg("^onglets",$table[0]))
    {
    $archive=explode('s',$table[0]);
    if ($archive[1]!="") $archnum=$archive[1]; else $archnum="An dernier";
    $x++;
    //archive courante
    if ($arch==$archive[1]) echo "<li  class=\"arch\"> <a href='cahier_texte_arch_perso.php?arch=$archive[1]' id='courant2'>$archnum</a></li>";
    else
    //autres archives
    echo "<li><a href='cahier_texte_arch_perso.php?arch=$archive[1]'>$archnum</a></li> ";
    }
//s'il n'esiste pas d'archive
if ($x==0)
    {
    echo '<p class="vide">Aucune archive</p>';
    exit;
    }
echo "</ul>";

$rq = "SELECT classe,matiere,id_prof FROM onglets$arch WHERE 1 ORDER BY classe ASC ";
// lancer la requete
$result = @mysqli_query($GLOBALS["___mysqli_ston"], $rq) or die (((is_object($dbc)) ? mysqli_error($dbc) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

// si pas de rubrique
if (mysqli_num_rows($result)==0)
    {
    echo("<br /> L'archive ".$arch."de votre cahier de textes ne comporte  aucune rubrique. ");exit;
    ((is_null($___mysqli_res = mysqli_close($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);
    exit;
    }

//on recupere le parametre de la rubrique
if (isset($_GET['rubrique'])) $cible=$_GET['rubrique'];
elseif (isset($_POST['rubriq'])) $cible=$_POST['rubriq'];
else $cible="";
/*
   ================================
   - Traitement de la barre des menus  -
   ================================
*/

// Creer la requete (Recuperer les rubriques de l'utilisateur)
$rq = "SELECT classe,matiere,id_prof FROM onglets$arch
 WHERE 1 ORDER BY id_prof ASC ";

 // lancer la requete
$result = @mysqli_query($GLOBALS["___mysqli_ston"], $rq) or die (((is_object($dbc)) ? mysqli_error($dbc) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
$nb = mysqli_num_rows($result);  // Combien y a-t-il d'enregistrements ?

//on recupere les donnees
$loop=0;
while ($enrg = mysqli_fetch_array($result,  MYSQLI_NUM))
    {
    $clas[$loop]=$enrg[0];
    $mat[$loop]=utf8_encode($enrg[1]);
    $numero[$loop]=$enrg[2];
    $loop++;
    }

//on calcule la largeur des onglets
if($cible==""){$cible=($numero[0]);}
$nmax=$nb;

echo ("<ul id='navlist'>");
for($x=0;$x < $nmax;$x++)
    {
    if ($cible == ($numero[$x]))
        {//cellule active
        echo "<li id='select'><a href='cahier_texte_arch_perso.php?rubrique=$numero[$x]&amp;arch=$arch'
        id='courant'>".htmlentities($mat[$x])."<br />$clas[$x] "."</a></li>";
        $contenu_postit=stripslashes($com[$x]);
        }
    else
        {
        if (!isset($mat[$x])) $mat[$x]="&nbsp;";
        if (!isset($clas[$x])) $clas[$x]="&nbsp;";
        if (!isset($numero[$x])) echo ("<li><a href='#'>$clas[$x]"."</a></li>");
        else  echo "<li><a href='cahier_texte_arch_perso.php?rubrique=$numero[$x]&amp;arch=$arch'>".htmlentities($mat[$x])."<br />$clas[$x]"."</a></li>";
        }
    }
echo '</ul>';
echo '</div>';
echo '</div>';
echo '<div id="container">';

/*======================================
   - Affichage du contenu   -
   =======================================*/
 //creer la requete
$rq = "SELECT DATE_FORMAT(date,'%d/%m/%Y'),contenu,afaire,DATE_FORMAT(datafaire,'%d/%m/%Y'),id_rubrique FROM cahiertxt$arch
 WHERE (id_auteur=$cible)  ORDER BY date desc ,id_rubrique desc";

// lancer la requete
$result = @mysqli_query($GLOBALS["___mysqli_ston"], $rq) or die (((is_object($dbc)) ? mysqli_error($dbc) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

// Combien y a-t-il d'enregistrements ?
$nb2 = mysqli_num_rows($result);
echo '<div id="boite5">';
echo '<table id="tb-cdt" cellpadding="1" cellspacing="2">';
while ($ligne = mysqli_fetch_array($result,  MYSQLI_NUM))
    {
    $textcours=stripslashes($ligne[1]);
    $textafaire=stripslashes($ligne[2]);
    $textco=utf8_encode(stripslashes($ligne[1]));
    $textafa=utf8_encode(stripslashes($ligne[2]));
    if ($ligne[1]!="")
        {
        echo '<tbody><tr><th colspan="2"></th></tr></tbody>';
        echo '<tbody>';
        echo '<tr>';
        //affichage de la seance
        echo '<td class="seance">S&eacute;ance du <br/>'.$jour.'&nbsp;'.$ligne[0].'<br /> </td>';
        echo '<td class="contenu">';
        echo $textco.'</td></tr>';
        //affichage, s'il existe, du travail a effectuer
        if ($ligne[2]!="")
            {
            echo '<tr><td class="afaire">A faire pour le :<br/>'.$ligne[3].'</td><td class="contenu">';
            echo $textafa.'</td></tr>';
            }
        echo '</tbody>';
        echo '<tbody><tr><th class="bas" colspan="2">';
        echo '<form action="';
        echo htmlentities($_SERVER["PHP_SELF"]);
        echo '" method="post"><div><input type="hidden" name="textecours" value="'.htmlentities(addslashes($textcours)).'" />';
        echo '<input type="hidden" name="texteafaire" value="'.htmlentities(addslashes($textafaire)).'" />';
        echo '<input type="submit" name="copie" value="" class="bt-copier" /></div></form>';
        echo '</th></tr>';
        echo '</tbody>';
        echo '<tbody><tr><th colspan="2"><hr /></th></tr></tbody>';
        }
    else
        {
        echo '<tbody><tr><th colspan="2"></th></tr></tbody>';
        echo '<tbody>';
        echo '<tr>';
        //affichage de la seance
        echo '<td class="afaire">Donn&eacute; le :&nbsp;'.$ligne[0].'<br />';
        //affichage, s'il existe, du travail a effectuer
        if ($ligne[2]!="")
            {
            echo '<br/>Pour le :&nbsp;'.$ligne[3].'</td>';
            echo '<td class="contenu">';
            echo $textafa.'</td></tr>';
            }
        echo '</tbody>';
        echo '<tbody><tr><th class="bas" colspan="2">';
        echo '<form action="';
        echo htmlentities($_SERVER["PHP_SELF"]);
        echo '" method="post"><div><input type="hidden" name="textecours" value="'.htmlentities(addslashes($textcours)).'" />';
        echo '<input type="hidden" name="texteafaire" value="'.htmlentities(addslashes($textafaire)).'" />';
        echo '<input type="submit" name="copie" value="" class="bt-copier" /></div></form>';
        echo '</th></tr>';
        echo '</tbody>';
        echo '<tbody><tr><th colspan="2"><hr /></th></tr></tbody>';
        }
    }
echo "</table>";
echo "</div>";
echo "</div>";
include ('../Includes/pied.inc');
?>
</body>
</html>
