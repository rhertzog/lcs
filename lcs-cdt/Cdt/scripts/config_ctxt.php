<?php
/* =============================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 2.5 du 20/04/2012
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script de personnalisation du cahier de textes -
			_-=-_
   "Valid XHTML 1.0 Strict"
   ============================================= */
//inclusion fichier liste des classes
include ('../Includes/data.inc.php');
   // si clic sur le bouton "retour au cahier de texte"
if (isset($_POST['retour']))
    {
    header("location: cahier_texte_prof.php");
    exit;
    }
// si clic sur le bouton "creer la rubrique"
if (isset($_GET['bn16drgv2r'])) $code=$_GET['bn16drgv2r'];
else $code='';
//premiere passe -> initialisation des variables
if ($code!='s61lyc6uby54trh')
    {
    $val_classe='--';//valeur par defaut de la variable classe
    $val_matiere='';//valeur par defaut de la variable matiere
    $val_prefix='';//valeur par defaut de la variable prefixe
    $cible="";//valeur par defaut du numero de la rubrique
    $val_edt='';
    }
session_name("Cdt_Lcs");
@session_start();
include "../Includes/check.php";
if (!check()) exit;
//si la page est applee par un utilisateur non identifie,
if (!isset($_SESSION['cequi'])) {exit;}
// autorisation d'acces ?  si pas prof, non autorise
if ($_SESSION['cequi']!="prof")
    {
    print('<p align="center"> </p> <p align="center"><font face="Arial" size="5" color="#FF6600">
    D&eacute;sol&eacute;, vous ne pouvez pas acc&eacute;der &agrave; cette page</font></p>');
    exit;
    }

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html  xmlns="http://www.w3.org/1999/xhtml" >
<head>
<title>Personnalisation du cahier de textes num&eacute;rique</title>
<meta name="author" content="Philippe LECLERC -TICE CAEN" />
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<link href="../style/style.css" rel="stylesheet" type="text/css" />
	<link  href="../style/navlist-prof.css" rel="stylesheet" type="text/css" />
	<link  href="../style/navlist-eleve.css" rel="stylesheet" type="text/css" />
<!--[if IE]>
<link href="../style/style-ie.css"  rel="stylesheet" type="text/css"/>
<![endif]-->
</head>
<body>
<?php
//recherche des groupes cours
$ind=count($classe);
include ("/var/www/lcs/includes/user_lcs.inc.php");
include ("/var/www/lcs/includes/functions.inc.php");
list($user, $groups)=people_get_variables($_SESSION['login'], true);
if ( count($groups) )
    {
    for ($loop=0; $loop < count ($groups) ; $loop++)
        {
        if (mb_ereg("^Cours",$groups[$loop]["cn"]))
            {
            $classe[$ind]=$groups[$loop]["cn"];
            $ind++;
            }
        }
    }
//edt cdt
$filename = "../json_files/".$_SESSION['login'].".json";
//si clic sur Personnaliser le cahier de textes
if (is_file($filename)) {
    $evts=  json_decode(file_get_contents($filename));
    $tab=array();
    foreach($evts as $id => $ev) {
        $onglet=$ev->title.':'.$ev->matiere;
        if (!in_array($onglet,$tab) && $ev->matiere!="") array_push ($tab, $onglet);

}

}


//lecture EDT agenda
elseif (is_dir("../../Agendas"))
    {
    $aujourdhui=date('Ymd');
    $tab=array();
    $loop=0;
    //connexion
    require_once ("../Includes/connect_agendas.php");

    //entrees edt ?
    $rq="SELECT cal_name,cal_description FROM webcal_entry WHERE cal_date >= ".$aujourdhui." AND cal_id IN
    (SELECT cal_id FROM webcal_entry_categories WHERE cat_owner='".$_SESSION['login']."' AND cat_id IN
    (SELECT cat_id FROM webcal_categories WHERE cat_owner='".$_SESSION['login']."' AND cat_name='EDT')) ";

    // lancer la requete
    $result = @mysql_query ($rq);
    if ($result)
        {
        //on recupere les donnees
        while ($enrg = mysql_fetch_array($result, MYSQL_NUM))
            {
            $mati= explode(":",$enrg[1]);
            $mati[1]=preg_replace ( "/[\r\n]+/", "", $mati[1] );
            $ong=$enrg[0].":".$mati[1];
            $ong=mb_ereg_replace("[[:blank:]]$","",$ong);
            if (!in_array($ong,$tab) && $enrg[0]!="" && $mati[1]!="")
                {
                $tab[$loop]=$ong;
                $loop++;
                }
            }
        }
    mysql_close();
    }

// Connexion a la base de donnees Cdt
require_once ('../Includes/config.inc.php');

//Traitement du formulaire

if (isset($_POST['cours_edt']))
    {
    $val_edt=$_POST['cours_edt'];
    $donnee=explode(":",$_POST['cours_edt']);
    //recherche correspondance classe
    if (in_array($donnee[0],$classe))
    $val_classe =$donnee[0];
    $val_matiere =$donnee[1];
    }

// §1.ENREGISTRER OU MODIFIER UNE RUBRIQUE
if (isset($_POST['enregistrer']) || isset($_POST['modifier']))
    {
    $rq="";
    // Verifier $matiere  et la debarrasser de tout antislash et tags possibles
    if (strlen($_POST['matiere']) > 0)
            {
            $matiR = mb_ereg_replace("&|[[:blank:]]","_",$_POST['matiere']);
            $matiere  = utf8_decode(addSlashes(strip_tags(stripslashes($matiR))));
             }
        else
            { // Si aucun commentaire n'a ete saisi
              $matiere = "";
            }
    // Verifier $prefixe et la debarrasser de tout antislash et tags possibles
    if (strlen($_POST['prefixe']) > 0) $prefixe= utf8_decode(addSlashes(strip_tags(stripslashes($_POST['prefixe']))));
    else
        { // Si aucun commentaire n'a ete saisi
        $prefixe= "";
        }
    // nom dela classe (division) selectionnee
    $div = ($_POST['div']);
     //prof en doublette
    if (isset($_POST['BIPROF']) && $_POST['BIPROF']!="0")
        {
        $prof2=preg_split('/#/',$_POST['BIPROF']);
        $colog=$prof2[0];
        $prefixe=substr(utf8_decode(addSlashes(strip_tags(stripslashes($prof2[2].' - ')))),0,12);
        }
    else $colog="";
    // Creer la requete d'ecriture pour l'enregistrement des donnees
    if (isset($_POST['enregistrer']) && $div!="--")
        {
        $rq = "INSERT INTO onglets (login ,cologin,prof ,classe, matiere, prefix,edt )
        VALUES ( '{$_SESSION['login']}', '$colog','{$_SESSION['name']}', '$div',
        '$matiere', '$prefixe','$val_edt' )";
        }
    //Creer la requete pour la mise a jour des donnees
    if (isset($_POST['modifier'])&& $div!="--")
        {
        $cible= ($_POST['numrub']);
        $rq = "UPDATE  onglets SET classe='$div', matiere='$matiere', prefix='$prefixe', edt='$val_edt',cologin='$colog'
        WHERE id_prof='$cible'";
        }

    // lancer la requete
    if ($rq!="")
        {
        $result = mysql_query($rq);
        if (!$result)  // Si l'enregistrement est incorrect
            {
            echo "<p>Votre rubrique n'a pas pu \352tre enregistr\351e \340 cause d'une erreur syst\350me".
                  "<p></p>" . mysql_error() . "<p></p>";
            // refermer la connexion avec la base de donnees
            mysql_close();
            //sortir
            exit();
            }
        }
    }

//fin enregistrement ou modification d'une rubrique


//§2. SUPPRESSION D'UNE RUBRIQUE

//si la suppression d'une rubrique est confirmee
if (isset($_GET['delrub'])&& isset($_GET['num']) && $_GET['TA']==md5($_SESSION['RT'].htmlentities($_SERVER['PHP_SELF'])))
    {
    $action=$_GET['delrub'];
    $cible=$_GET['num'];

    //la rubrique existe elle ?
    $rq = "SELECT login FROM onglets WHERE id_prof='$cible'  ";
    // lancer la requete
    $result = @mysql_query ($rq) or die (mysql_error());
    $nb = mysql_num_rows($result);
    //si la rubrique existe, on l'efface
    if (($nb==1) &&($action==1245))
        {
        $rq = "DELETE  FROM onglets WHERE id_prof='$cible' LIMIT 1";
        $result2 = @mysql_query ($rq) or die (mysql_error());
        }
    }
$action ="";
$num="";

// §3. MODIFICATION D'UNE RUBRIQUE
if (isset($_GET['modrub'])&& isset($_GET['num']))
    {
    $action=$_GET['modrub'];
    $cible=$_GET['num'];

    //la rubrique existe elle ?
    $rq = "SELECT login,classe, matiere, prefix,edt,cologin FROM onglets WHERE id_prof='$cible'  ";

    // lancer la requete
    $result = @mysql_query ($rq) or die (mysql_error());
    $nb = mysql_num_rows($result);

    //si elle existe, on recupere les datas pour les afficher dans les champs
    if (($nb==1) &&($action=='erg45er5ze'))
        {
        while ($enrg = mysql_fetch_array($result, MYSQL_NUM))
            {
            $val_classe=$enrg[1];
            $val_matiere=utf8_encode($enrg[2]);
            $val_prefix=utf8_encode($enrg[3]);
            $val_edt=$enrg[4];
            $colo=$enrg[5];
            }
        }
    }
//FIN TRAITEMENT FORMULAIRE
/* recherche des onglets existants*/
// Creer la requete.
$rq = "SELECT classe,matiere,id_prof,prof,prefix,edt FROM onglets
 WHERE login='{$_SESSION['login']}' ORDER BY id_prof ASC ";

// lancer la requete
$result = @mysql_query ($rq) or die (mysql_error());

// Combien y a-t-il d'enregistrements ?
$nb = mysql_num_rows($result);

//on recupere les donnees
$loop=0;
while ($enrg = mysql_fetch_array($result, MYSQL_NUM))
    {
    $clas[$loop]=$enrg[0]; //classe
    $mat[$loop]=utf8_encode($enrg[1]); // matiere
    $numero[$loop]=$enrg[2];//numero de la rubrique
    $prof[$loop]=$enrg[3]; // nom du prof
    $pref[$loop]=utf8_encode($enrg[4]); //prefixe
    $edt[$loop]=$enrg[5];//edt
    $loop++;
    }
/*================================
   -      Affichage du formulaire  -
   ================================*/
?>
<div id="cfg-container">
<div id="cfg-contenu">
<h1 class='title'>Personnalisation du cahier de textes de <span class="evidence"> <?echo $_SESSION['nomcomplet'];?></span></h1>
<form id="cfg-form" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>?bn16drgv2r=s61lyc6uby54trh" method="post">
<p><input name="TA" type="hidden"  value="<?php echo md5($_SESSION['RT'].htmlentities($_SERVER['PHP_SELF'])); ?>" /></p>
<ol>
<li>Cr&eacute;ation des rubriques (onglets)
<ul>
<?php
if (count($tab)>0)
    {
    echo '<li> Selectionnez le cours de votre emploi du temps : ';
    echo '<select name="cours_edt"';
    if (!isset($_GET['modrub'])) echo ' onchange="submit()"';
    echo ' >';
    echo '<option value="--">--</option>';
    foreach ($tab as $cle => $valeur)
        {
        //on affiche que les cours non deja affectes
        if (!in_array($valeur,$edt)  )
            {
            echo '<option value="'.htmlentities($valeur,ENT_QUOTES,'UTF-8').'"';
            if (htmlentities($valeur)==htmlentities($val_edt) ){echo ' selected="selected"';}
            echo '>'.htmlentities($valeur,ENT_QUOTES,'UTF-8').'</option>';
            }
        //si modif on affiche le cours edt correspondant
        if ($action=='erg45er5ze' && htmlentities($valeur)==htmlentities($val_edt))
            {
            echo '<option value="'.htmlentities($valeur,ENT_QUOTES,'UTF-8').'"';
            echo ' selected="selected"';
            echo '>'.htmlentities($valeur,ENT_QUOTES,'UTF-8').'</option>';
            }
         }
    echo '</select></li>';
    }
if (count($tab)>0)  echo '<li>Modifiez s\'il y a lieu le nom de la classe ou du groupe : ';
else echo '<li>Choisissez votre classe ou votre groupe : ';
//liste de selection de la classe
echo '<select id="laclasse" name="div">';
echo '<option value="--">--</option>';
foreach ($classe as $cle => $valeur)
    {
    echo '<option value="'.$valeur.'"';
    if ($valeur==$val_classe) {echo ' selected="selected"';}
    echo '>'.$valeur.'</option>';
    }
echo '</select></li>';
if (count($tab)>0)  echo '<li>Modifiez s\'il y a lieu le nom de la mati&egrave;re : ';
else echo '<li>Indiquez la mati&egrave;re enseign&eacute;e : ';
echo '<input type="text" id="lamat" name="matiere" value="'.$val_matiere.'" size="20" maxlength="20" /></li>';
 //add cologin
echo "<li>S&#233;lectionner s'il  y lieu le professeur qui assure le cours avec vous : ";
$uids = search_uids ("cn=Profs", "half");
$people = search_people_groups ($uids,"(sn=*)","cat");
echo '<select name="BIPROF" >';
echo '<option value="0">---</option>';
for ($loop=0; $loop <count($people); $loop++)
    {
    $uname = $people[$loop]['uid'];
    $nom = $people[$loop]["fullname"];
    $patronyme=$people[$loop]["name"];
    echo '<option value="'.$uname.'#'.$nom.'#'.$patronyme.'"';
    if ($uname ==$colo) {echo 'selected="selected"';}
    echo '>'.$nom.'</option>';
    }
echo ' </select></li>';

?>
</ul>
</li>
<li>Sur les onglets du <b>cahier de textes de vos &eacute;l&egrave;ves</b> apparaitront la mati&egrave;re enseign&eacute;e et votre nom
pr&eacute;c&eacute;d&eacute; d&rsquo;un &quot;&nbsp;pr&eacute;fixe&nbsp;&quot;
<ul>
<li>Indiquez le <b>pr&eacute;fixe </b> que vous souhaitez voir appara&icirc;tre (M, Mme, pr&eacute;nom abr&eacute;g&eacute;, ....) :
<input type="text" name="prefixe" value="<?php echo $val_prefix ?>" size="10" /></li>
</ul></li>
</ol>

<div id="cfg-btn">
<input type="hidden" name="numrub" value= "<?php echo $cible ; ?>" />
<?php
if (isset($_GET['modrub'])&& isset($_GET['num'])) echo ('<input class="modif" type="submit" name="modifier" value="" />');
else  echo ('<input class="enreg" type="submit" name="enregistrer" value="" />');
if ($nb>0) echo('<input class="retour" type="submit" name="retour" value="" />');
?>
<input type="button" class="annul" value="" onclick="history.back()"/>
</div>
</form>
<?php

//AFFICHAGE DES ONGLETS EXISTANTS
//creation du tableau , onglets prof
if ($nb>0)
    {
    echo '<div id="cfg-onglet">';
    echo '<h4 class="perso">Aspect des onglets de votre cahier de textes (cliquer sur un onglet pour le <span class="evidence"><b>Modifier</b></span> )</h4>';
    echo '<ul id="cfg-navlist">';
    for($x=0;$x < $nb;$x++)
        {
        if ($x==0)
            {
            echo '<li><a href="config_ctxt.php?modrub=erg45er5ze&amp;num='.$numero[$x].'" title="Modifier cet onglet" >'.$mat[$x].'<br />'.$clas[$x].'</a></li>';
            }
        else
            {
            echo '<li><a href="config_ctxt.php?modrub=erg45er5ze&amp;num='.$numero[$x].'" title="Modifier cet onglet" >'.$mat[$x].'<br />'.$clas[$x].'</a></li>';
            }
        }
    echo '</ul>';
    echo '<h4 class="perso"> Aspect des onglets du cahier de textes des &#233;l&#232;ves ( cliquer sur un onglet pour le <span class="evidence"><b>Supprimer</b></span>)</h4>';

    //creation du tableau , onglets eleves
    echo '<ul id="cfg-navlist-elv">';
    for($x=0;$x < $nb;$x++)
        {
        if ($x==0)
            {
            echo '<li><a href="config_ctxt.php?suppr=yes&amp;numong='.$numero[$x].'" title="Supprimer cet onglet">'.$mat[$x].' ( '.$clas[$x].' )<br />'.$pref[$x].'&nbsp;'.$prof[$x].'</a></li>';
            }
        else
            {
            echo '<li><a href="config_ctxt.php?suppr=yes&amp;numong='.$numero[$x].'" title="Supprimer cet onglet">'.$mat[$x].' ( '.$clas[$x].' )<br />'.$pref[$x].'&nbsp;'.$prof[$x].'</a></li>';
            }
        }
    echo '</ul>';
    echo '</div>';
    }

//ouverture d'un popup de confirmation de suppression
if (isset($_GET["suppr"]))
    {
    echo "<script type='text/javascript'>";
    echo"      if (confirm('Confirmer la suppression de cette rubrique')){";
    echo ' location.href = "';
    echo htmlentities($_SERVER['PHP_SELF']);echo'" + "?num=" +'." '";
    echo $_GET["numong"] ;
    echo "'".' + "&delrub=" +'."1245";
    echo ' + "&TA=" +'." '". md5($_SESSION["RT"].htmlentities($_SERVER["PHP_SELF"]))."'";
    echo " ;} else  {";
    echo ' location.href = "';
    echo htmlentities($_SERVER['PHP_SELF']);
    echo '"; } </script>';
    }
?>
</div><!-- fin du contenu -->
</div><!-- fin du container -->
<?php
include ('../Includes/pied.inc');
?>
</body>
</html>
