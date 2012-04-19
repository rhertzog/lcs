<?php
/* ==================================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 2.5 du 20/04/2012
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script de mise a jour des onglets archives eleves-
			_-=-_
   =================================================== */
   
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Expires: " . gmdate("D, d M Y H:i:s") . " GMT");
session_name("Cdt_Lcs");
@session_start();
//si la page est appelee par un utilisateur non identifie
if (!isset($_SESSION['login']) )exit;
//si la page est appelee par un utilisateur non prof
elseif ($_SESSION['cequi']!="prof") exit;
//indique que le type de la reponse renvoyee au client sera du Texte
header("Content-Type: text/plain" ); 
//anti Cache pour HTTP/1.1
header("Cache-Control: no-cache , private");
//anti Cache pour HTTP/1.0
header("Pragma: no-cache");
if(isset($_POST['rubrik']) && isset($_POST['thedate']))
    {
    $tsmp=$_POST['thedate'];
    $cible=$_POST['rubrik'];
    $ann_arch=$_POST['thearch'];
    echo '<ul id="navlist-elv">';
    for($x=0;$x < count($_SESSION['prof']);$x++)
        {
        if ($cible == ($_SESSION['numero'][$x])) 
            {
            echo '<li id="active"><a href="#" title="" onclick="refresh_cdt_arch('. $_SESSION['numero'][$x].','.$tsmp.',\''.$ann_arch.'\')" id="courant">&nbsp;'.$_SESSION["mat"][$x].'&nbsp;<br />&nbsp;'.$_SESSION["pref"][$x].'  '.$_SESSION["prof"][$x].'&nbsp;</a></li>';
            }
        else 
            {
            echo '<li><a href="#" title="" onclick="refresh_cdt_arch('. $_SESSION['numero'][$x].','.$tsmp.',\''.$ann_arch.'\')" >&nbsp;'.$_SESSION["mat"][$x].'&nbsp;<br />&nbsp;'.$_SESSION["pref"][$x].'  '.$_SESSION["prof"][$x].'&nbsp;</a></li>';
            }
        }
    echo '</ul>';
    if ($vis) echo '<div id="visa-cdt'.$vis.'e">'.$datv.'</div>';
    }
else echo "Erreur";
?>