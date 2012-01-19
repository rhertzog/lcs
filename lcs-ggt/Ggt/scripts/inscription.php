<?php
/* ==================================================
   Projet LCS : Linux Communication Server
  Plugin "Gestion ateliers AP"
  VERSION 1.0 du 15/12/2011
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   -  gestion des ateliers -
			_-=-_
    "Valid XHTML 1.0 Strict"
   =================================================== */

header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Expires: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
session_name("gestap_Lcs");
@session_start();

//si la page est appelee par un utilisateur non identifie
if (!isset($_SESSION['login']) )exit;

//si la page est appelee par un utilisateur non prof
elseif ($_SESSION['cequi']!="eleve") exit;

//include "../Includes/functions2.inc.php";
$tsmp=time();
$datecourante=date('Y-m-d');
$ind=0;
$tsmp2=time() + 604800;//j+7

include ("/var/www/lcs/includes/user_lcs.inc.php");
include ("/var/www/lcs/includes/functions.inc.php");

// Connexion a la base de donnees
require_once ('../Includes/config.inc.php');
// Creer la requete
$rq = "SELECT id_atelier,DATE_FORMAT(too,'%d/%m/%Y'),id_nivo FROM proposes WHERE classe LIKE  '%".$_SESSION['saclasse']."%'
    AND frome <='".$datecourante."' AND too >='".$datecourante."'";

 // lancer la requ&egrave;ete
$result = mysql_query ($rq) or die (mysql_error());

if ( mysql_num_rows($result)>0)
	{
	$loop=0;
	$enrg = mysql_fetch_array($result, MYSQL_NUM);
                  $liste_ateliers=mb_split(",",$enrg[0]) ;
                  $limite=$enrg[1];
                  $nivo=$enrg[2];
	}
//inscriptions existante
$rq = "SELECT v1,v2,v3 FROM inscriptions WHERE login ='".$_SESSION['login']."'";
$result = @mysql_query ($rq) or die (mysql_error());
if (mysql_num_rows($result)>0)
    {
    $row = mysql_fetch_array($result, MYSQL_NUM);//)
    $voeux=$row;
    }

$j=0;$TitreAt2=array();
    for ($loop=0; $loop < count ($voeux); $loop++)
        {
        $rq = "SELECT id_at,nom,description,prof FROM ateliers WHERE id_at='".$voeux[$loop]."'";
        $result = @mysql_query ($rq) or die (mysql_error());

        while ($row = mysql_fetch_object($result))
                {
                $IdAt2[$j]=$row->id_at;
                $TitreAt2[$j]=$row->nom;
                $ContenuAt2[$j]=$row->description;
                }
        $j++;
    }
    $rq = "SELECT nom FROM ateliers WHERE id_at IN (SELECT at_id FROM repartition WHERE eleve_id ='".$_SESSION['login']."' order BY id ASC)";
    $result = @mysql_query ($rq) or die (mysql_error());
    $i=0;$nom_at=array();
    while ($row = mysql_fetch_object($result))
        {
        $nom_at[$i]=$row->nom;
        $i++;
        }
    $rq = "SELECT DATE_FORMAT(deb,'%d/%m/%Y') , DATE_FORMAT(fin,'%d/%m/%Y') FROM repartition WHERE eleve_id ='".$_SESSION['login']."' order BY id ASC";
    $result = @mysql_query ($rq) or die (mysql_error());
    $i=0;
    while ($row = mysql_fetch_array($result))
        {
        $datedebut[$i]=$row[0];
        $datefin[$i]=$row[1];
        $i++;
        }

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html  xmlns="http://www.w3.org/1999/xhtml" >
<head>
<title>Inscription aux  ateliers </title>
<meta name="author" content="Philippe LECLERC -TICE CAEN" />
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<link  href="../../../libjs/jquery-ui/css/redmond/jquery-ui.css" rel="stylesheet" type="text/css" />
<link href="../style/ap.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="../../../libjs/jquery/jquery.js"></script>
<script type="text/javascript" src="../../../libjs/jquery-ui/jquery-ui.js"></script>
<script type="text/javascript" src="../Includes/jq_eleve.js"></script>
 </head>
<body>
<p></p>
<?php
echo '<fieldset id="field10">
<legend id="legende">Inscription aux ateliers </legend>
<input type="hidden" name="niv_ins" id="niv_ins" value= "'.$nivo.'" />';
echo '<div class="ui-widget-content ui-corner-all" >';
if (count( $nom_at)>0)
{
    echo '<p >&nbsp;Vous êtes actuellement inscrit ';
    if (count( $nom_at)==1) echo 'à l\'atelier : '; else echo 'aux ateliers :<br /> ';
    for ($i=0; $i< count ($nom_at); $i++)
        {
        echo '<span class="ui-state-error"> &nbsp;'.$nom_at[$i].' </span> &nbsp; pour la période  <span class=" ui-state-highlight"> &nbsp;du '.$datedebut[$i].' jusqu\'au '.$datefin[$i].'&nbsp;</span>';
        if ($i!=0) echo '<br />';
    }
    echo '</p>';
}

    echo '<div id="propos" class="ui-widget-content">';
     if (count( $liste_ateliers)==0) echo '<p class="validateTips ui-state-error "> Il n\'y a pas de nouvelle inscription propos&eacute;e aux &eacute;l&egrave;ves de votre classe par ce dispositif</p>';
else {
    echo '<p class="validateTips ui-state-highlight  ">'. $_SESSION['nomcomplet'] .' - '.$_SESSION['saclasse'].' <br /> Nouvelle  inscription modifiable jusqu\'au '.$limite.'</p>';
    echo ' <ul class="connectedSortable ui-helper-reset" id="draggable1" >';
    echo ' <label for="propos">Etiquettes des ateliers propos&eacute;s</label><br />';
    //lecture
    $j=0;$TitreAt=array();
    for ($loop=0; $loop < count ($liste_ateliers); $loop++)
        {
        $rq = "SELECT id_at,nom,description,prof FROM ateliers WHERE id_at='".$liste_ateliers[$loop]."' order by id_at ASC";
        $result = @mysql_query ($rq) or die (mysql_error());

        while ($row = mysql_fetch_object($result))
                {
                $IdAt[$j]=$row->id_at;
                $TitreAt[$j]=$row->nom;
                $ContenuAt[$j]=$row->description;
                }
        $j++;
    }


    for ($i=0; $i< count ($TitreAt); $i++)
        {
        if (!in_array($TitreAt[$i], $TitreAt2))
            {
            echo '<li class="ui-state-default sequ';
            echo '" id="li'.$IdAt[$i].'"  title="'.$TitreAt[$i].'">
            - '.$TitreAt[$i].'
            <span class="buttons"><button class="showform" tabindex="'.$IdAt[$i].'">Détails</button>
            </span></li>';
            }
         }
           // echo $niveaux[$loop];
    echo '</ul></div>';




echo '<div id="droppable" class="ui-widget-content">
    <p>Inscrivez vous  en pla&ccedil;ant 3 &eacute;tiquettes "atelier" ci-dessous. <span id="aide"><img src="../images/aide.png" alt="Aide" title="Aide"/></span></p>
    <div id="listevoeux">
    <div class="voeu ui-state-highlight"> v&oelig;u 1</div>
    <div class="voeu ui-state-highlight"> v&oelig;u 2</div>
    <div class="voeu ui-state-highlight"> v&oelig;u 3</div>
    </div>
    <div id="droppable-inner" class="ui-widget-content">
    <ul id="sortable2" class="connectedSortable">';


    for ($i=0; $i< count ($TitreAt2); $i++)
        {
        echo '<li class="ui-state-default sequ';
                             echo '" id="li'.$IdAt2[$i].'"  title="'.$TitreAt2[$i].'">
                        - '.$TitreAt2[$i].'
        <span class="buttons"><button class="showform" tabindex="'.$IdAt2[$i].'">Détails</button>
        </span></li>';
        }
echo '</ul>
    </div>';
    }
 //dialogs
 //edition atelier
echo '<div id="dialog2" title="test">
                    <fieldset class="ui-helper-reset">
                 	  <p  id="descript" class="ui-widget-content ui-corner-all" /></p><br />
                    </fieldset>';

//aide
echo '<div id="dialog" title="Aide">
                    <fieldset class="ui-helper-reset">
                        <div  class="ui-widget-content ui-corner-all" >
                       <ul>
                       <li>Pour placer une étiquette, faites un clic gauche maintenu sur une des étiquettes proposées et glissez la sur l\'espace correspondant à un voeu. <br /></li>
                       <li>Vous pouvez modifier l\'ordre des voeux en déplaçant verticalement les étiquettes.<br /></li>
                       <li>Pour modifier un voeu, remettre son étiquette dans la zone "étiquettes proposées".<br /></li>
                       <li>L\'enregistrement des données s\'effectue automatiquement lorsque vous déposez la troisième étiquette.<br /></li>
                       <li>Vous pouvez modifier votre inscription pendant toute la période d\'inscription.
                       </ul></div>
                    </fieldset>';

echo '</div>';
echo '</fieldset>';
?>
</body>
</html>