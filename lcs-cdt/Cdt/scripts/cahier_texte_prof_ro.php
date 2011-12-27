<?php
/* ===================================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 2.3 du 31/12/2011 
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script de consultationd'un  cahier de textes PROF -
			_-=-_
   =================================================== */
session_name("Cdt_Lcs");
@session_start();
include "../Includes/basedir.inc.php";
include "$BASEDIR/lcs/includes/headerauth.inc.php";
include "$BASEDIR/Annu/includes/ldap.inc.php";
include "$BASEDIR/Annu/includes/ihm.inc.php";
if ((ldap_get_right("Cdt_can_sign",$_SESSION['login'])=="N") && ((!isset($_SESSION['aliasprof']) || (!isset($_SESSION['proffull']))))) exit;
// Connexion a la base de donnees
include ('../Includes/config.inc.php');

// Creer la requ&egrave;ete.
$rq = "SELECT classe,matiere,id_prof FROM onglets
 WHERE login='{$_SESSION['aliasprof']}' OR cologin='{$_SESSION['aliasprof']}' ORDER BY classe ASC ";
 
// lancer la requ&egrave;ete
$result = @mysql_query ($rq) or die (mysql_error()); 

// si pas de rubrique, on redirige vers config_ctxt.php
if (mysql_num_rows($result)==0) 
    {
    echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
    <html  xmlns="http://www.w3.org/1999/xhtml" >
    <head>
    <title>Cahier de textes num&eacute;rique</title>
    <meta name="author" content="Philippe LECLERC -TICE CAEN" />
    <meta http-equiv="content-type" content="text/html;charset=utf-8" />
    <link href="../style/style.css" rel="stylesheet" type="text/css" />
    </head>
    <body>
    <div id="first">
    <div class="prg">
    <h2><br /><br />Le cahier de textes de '. $_SESSION['proffull'].' ne comporte actuellement aucune rubrique.</h2>
    </div></div>
    </body>
    </html>';
    mysql_close();	
    exit;
    }
include_once("/usr/share/lcs/Plugins/Cdt/Includes/fonctions.inc.php");	

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html  xmlns="http://www.w3.org/1999/xhtml" >
<head>
<title>Cahier de textes num&eacute;rique</title>
<meta name="author" content="Philippe LECLERC -TICE CAEN" />
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<link href="../style/style.css" rel="stylesheet" type="text/css" />
	<link  href="../style/navlist-prof.css" rel="stylesheet" type="text/css" />
        <script type="text/javascript" src="../../../libjs/jquery/jquery.js"></script>
        <script type="text/javascript" src="../Includes/sequence.js"></script>
	
<!--[if IE]>
<link href="../style/style-ie.css"  rel="stylesheet" type="text/css"/>
<![endif]-->
	
</head>

<body >
<?php

//on recupere le parametre de la rubrique
if (isset($_GET['rubrique'])) $cible=$_GET['rubrique'];
elseif (isset($_POST['rubriq'])) $cible=$_POST['rubriq'];
if ( isset($_POST['viser']))
    {
     $dat_vis= date('Y-m-d');
    if (!isset($_SESSION['login'])) $numvisa=2; else $numvisa=1;
     $rq = "UPDATE  onglets SET visa='$numvisa', datevisa='$dat_vis'  WHERE id_prof='$cible'"; 

    // lancer la requete
    $result = mysql_query($rq); 
    if (!$result)  // Si l'enregistrement est incorrect
        {                           
         echo "<p>Votre rubrique n'a pas pu \352tre enregistr\351e \340 cause d'une erreur syst\350me".
        "<p></p>" . mysql_error() . "<p></p>";
        }
     $dat_la=date('Y-m-d');
     $rq = "UPDATE cahiertxt SET on_off='$numvisa' WHERE id_auteur='$cible' AND date<'$dat_la' AND datevisibi<='$dat_la' AND on_off='0' ";
     // lancer la requête
    $result = mysql_query($rq); 
    if (!$result)  // 
        {                           
         echo "<p>L'apposition du visa n'a pu\352tre r\351alis\351e \340 cause d'une erreur syst\350me".
        "<p></p>" . mysql_error() . "<p></p>";
        }					
    }	
	
/*
   ================================
   - Traitement de la barre des menus  -
   ================================
*/

// Creer la requete (Recuperer les rubriques de l'utilisateur) 
$rq = "SELECT classe,matiere,id_prof,visa,DATE_FORMAT(datevisa,'%d/%m/%Y') FROM onglets
 WHERE login='{$_SESSION['aliasprof']}' OR cologin='{$_SESSION['aliasprof']}' ORDER BY id_prof ASC ";

 // lancer la requ&egrave;ete
$result = @mysql_query ($rq) or die (mysql_error());
$nb = mysql_num_rows($result);  // Combien y a-t-il d'enregistrements ?

//on recupere les donnees
$loop=0;
while ($enrg = mysql_fetch_array($result, MYSQL_NUM)) 
    {
    $clas[$loop]=$enrg[0];
    $mat[$loop]=utf8_encode($enrg[1]);
    $numero[$loop]=$enrg[2];
    $visa[$loop]=$enrg[3];// 
    $datvisa[$loop]=$enrg[4];
    $loop++;
    }
	
//on calcule la largeur des onglets
if($cible==""){$cible=($numero[0]);}
$nmax=$nb;
 
//cr&eacute;ation de la barre de menu 
echo '<div id="entete">';
echo '<br />';
echo '<div id="navcontainer">';
echo ("<ul id='navlist'>");
for($x=0;$x < $nmax;$x++)
    {	
    if ($cible == ($numero[$x]))
        {//cellule active	
        echo "<li id='select'><a href='cahier_texte_prof_ro.php?rubrique=$numero[$x]' id='courant'>".$mat[$x]."<br />$clas[$x] "."</a></li>";	
        if ($visa[$x])
            {
            $vis=$visa[$x];
            $datv=$datvisa[$x];
            }
        }
    else 
        {
        if (!isset($mat[$x])) $mat[$x]="&nbsp;";
        if (!isset($clas[$x])) $clas[$x]="&nbsp;";
        if (!isset($numero[$x]))
        echo ("<li><a href='#'>$clas[$x]"."</a></li>");
        else  echo "<li><a href='cahier_texte_prof_ro.php?rubrique=$numero[$x]' >".$mat[$x]."<br />$clas[$x]"."</a></li>";
        }
    }
echo '</ul>';
echo '</div>';
echo '</div>';
//affichage du visa
if ($vis) echo '<div id="visa-cdt'.$vis.'">'.$datv.'</div>';

?>
<!--bloc qui contient la saisie et le contenu du cahier de texte-->
<div id="container2">
<fieldset id="field7">
<legend id="legende"> Cahier de textes de <?echo $_SESSION['proffull']; ?>  &nbsp; 
</legend>

<form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="post">
<div><input type="submit" name="viser" value="" class="bt-visa" /><br /><br />
<input type="hidden" name="rubriq" value= "<?php echo $cible; ?>" />
<a href="mailto:<?php echo $_SESSION['aliasprof']."@".$domain; ?>" > <img src="../images/mail.png" alt="Envoyer un mail"
         title="Envoyer un mail &#224; <?php echo $_SESSION['proffull']; ?>" class="nobord" /></a>
</div></form>

<?php
/*========================================================================
   - Affichage du contenu du cahier de textes  -
   =======================================================================*/

if ($_SESSION['version']==">=432") setlocale(LC_TIME,"french");
else setlocale("LC_TIME","french");
echo '<div id="boite5">';
include_once  ('./contenu.php');
echo '</div>';
?>
</fieldset>
 <?php
include ('../Includes/pied.inc');
?>
</div>
</body>
</html>
