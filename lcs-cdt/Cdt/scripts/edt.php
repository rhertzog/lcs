<?php
/* ==================================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 2.5 du 20/04/2012
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script d'affichage edt-
			_-=-_
    "Valid XHTML 1.0 Strict"
   =================================================== */
session_name("Cdt_Lcs");
@session_start();
include "../Includes/check.php";
if (!check()) exit;
//si la page est appelee par un utilisateur non identifie
if (!isset($_SESSION['login']) )exit;

//si la page est appelee par un utilisateur non prof
elseif ($_SESSION['cequi']!="prof") exit;
$filename = "../json_files/".$_SESSION['login'].".json";
//si clic sur Personnaliser le cahier de textes
if (! is_file($filename) && isset($_GET['from'])) {header("location: cahier_texte_prof.php");exit;}
if (! is_file($filename))  {header("location: error_edt.php");exit;}
require_once('../Includes/parsical/SG_iCal.php');
setlocale(LC_TIME,"french");
$evts=  json_decode(file_get_contents($filename));
//redirection si vient d'index pendant cours
if (isset($_GET['from']))
    {
    function my_cours_search($ref, $haystack) {
        if ($ref == "") $ref = time();
        foreach ($haystack as $key => $value) {
            if ($value['start'] <= $ref && $value['end'] >= $ref && $value['matiere']!="") {
                $encours[0] = $value['id'];
                $encours[1] = $value['start'];
                break;
            }

        }return $encours;
    }

    $filename = "../json_files/" . $_SESSION['login'] . ".json";
    $evenements = json_decode(file_get_contents($filename), true);
    //chercher un event depuis son id
    $maintenant=time();
    $rep = my_cours_search(  $maintenant, $evenements);
    if (count($rep)==2)
        {
        header("location: cahier_texte_prof.php?id=".$rep[0] . '&start=' . $rep[1]);
        exit;
        }
    }
// fin redir
$mats=array();
$data = array();
$classes=array();
foreach($evts as $id => $ev) {
                  if (!in_array($ev->matiere,$mats) && $ev->title!='' && $ev->matiere!='' ) array_push ($mats, $ev->matiere);
                  if (!in_array($ev->title,$classes) && $ev->title!='' && $ev->matiere!=''  ) array_push ($classes, $ev->title);
}

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Fullcalendar iCal Loader</title>
<link rel="stylesheet" type="text/css" href="../Includes/fullcalendar/fullcalendar.css">
<link rel="stylesheet" type="text/css" href="../Includes/timepicker/jquery.ui.timepicker.css">
<link rel='stylesheet' type='text/css' href='../../../libjs/jquery-ui/css/redmond/jquery-ui.css' />
<link href="../style/style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../../../libjs/jquery/jquery.js"></script>
<script type="text/javascript" src="../Includes/jquery.tools.min.js"></script>
<script type="text/javascript" src="../../../libjs/jquery-ui/jquery-ui.js"></script>
<script type="text/javascript" src="../Includes/fullcalendar/fullcalendar.js"></script>
<script type="text/javascript" src="../Includes/timepicker/jquery.ui.timepicker.js"></script>
<script type="text/javascript" src="../Includes/edt.js"></script>
<style type='text/css'>
    #dialog , #dialog2{text-align: left; font-size:0.9em;}
    div#calendar { width: 800px;margin: 0 auto }
    span.ui-icon-close {float:right;width:9px;height:10px;margin:-11px -9px;background-color:#ffffcc;background-position: -83px -131px;border: solid 1px  #3366CC;}
    span.ui-icon-pencil {float: right;width:9px;height:10px;margin: -25px -9px;background-color:#ffffcc;background-position: -67px -115px;border: solid 1px  #3366CC;}
    .fc-event-vert .ui-resizable-s  {height:12px !important; }
    .ui-widget {font-size: 0.9em;}
    .tooltip {display:none; background-color: #600e00;
    font-size:12px;
    height:15px;
    /*width:160px;*/
    padding:5px;
    color:#eee;
    -moz-box-shadow: 2px 2px 11px #666;
    -webkit-box-shadow: 2px 2px 11px #666;
    }
    #mess {width:30%;background-color: #660000;color:#EEE;margin:5px auto;text-align:center;-moz-box-shadow: 2px 2px 11px #666;
    -webkit-box-shadow: 2px 2px 11px #666;
    -moz-border-radius: 12px;
    -webkit-border-radius: 12px;}
    a.message {color: #fff; font-family:   arial, verdana ; font-size : 14px;}
</style>
</head>
<body>
<div id="cfg-container">
<div id="cfg-contenu">
<h1 class='title'>Emploi du temps de <span class="evidence"> <?echo $_SESSION['nomcomplet'];?></span></h1>
<div id="calendar"></div>
<div id="mess"> </div>
<?php
echo ' <div id="dialog" title="Ajout d\'un cours" >
<form>
<fieldset class="ui-helper-reset ">
<label > Vos classes/Mati&egrave;res existantes :</label>
<div class="ui-state-default ><p>
<label for="tab_title"> Classe :</label>
<select name="classe" id="tab_title" class="ui-widget-content ui-corner-all">';
for ($loop=0; $loop <count($classes); $loop++)
        {
        echo '<option value="'.$classes[$loop].'"';
        echo '>'.$classes[$loop].'</option>';
        }
echo ' </select></p>
<p><label for="tab_descr"> Mati&egrave;re </label>
 <select name="matiere" id="tab_descr" class="ui-widget-content ui-corner-all">
 ';
for ($loop=0; $loop <count($mats); $loop++)
        {
        $mat_tronc=mb_split(':',$mats[$loop],2);
        $mat_tronc[1]=preg_replace ( "/[\r\n]+/", "", $mat_tronc[1]);
        echo '<option value="'.$mats[$loop].'"';
        echo '>'.$mats[$loop].'</option>';
        }
echo ' </select></p></div>
  <label > ou, cr&eacute;er une nouvelle Classe/Mati&egrave;re :</label>
<div class="ui-state-default ><p>
<label for="new_classe"> Classe :</label>
 <input type="text" style="width: 70px" id="new_classe" value="" /></p>
  <p><label for="new_mat"> Mati&egrave;re :</label>
  <input type="text" style="width: 250px" id="new_mat" value="" /></p></div>
<label for="horaires"> Dur&eacute;e :</label>
<div class="ui-state-default id="horaires"><p>
<label for="DEB"> de  :</label>
 <input type="text" style="width: 70px" id="timepicker_start" value="" />
<label for="FIN">&agrave; :</label>
<input type="text" style="width: 70px" id="timepicker_end" value="" /></p></div>';
echo '</fieldset>
</form>
</div>';

echo ' <div id="dialog2" title="Modification d\'un cours" >
<form>
<fieldset class="ui-helper-reset">
<label > Vos classes/Mati&egrave;res existantes :</label>
<div class="ui-state-default ><p>
<label for="tab_title2"> Classe :</label>
<select name="classe" id="tab_title2" class="ui-widget-content ui-corner-all">';
for ($loop=0; $loop <count($classes); $loop++)
        {
        echo '<option value="'.$classes[$loop].'"';
        echo '>'.$classes[$loop].'</option>';
        }
echo ' </select></p>
<p><label for="tab_descr"> Mati&egrave;re </label>
 <select name="matiere" id="tab_descr2" class="ui-widget-content ui-corner-all">
 ';
for ($loop=0; $loop <count($mats); $loop++)
        {
        //$mat_tronc=mb_split(':',$mats[$loop],2);
        $mat_tronc=preg_replace ( "/[\r\n]+/", "", $mats[$loop]);
        echo '<option value="'.$mats[$loop].'"';
        echo '>'.$mats[$loop].'</option>';
        }
echo ' </select></p></div>
  <label > ou, cr&eacute;er une nouvelle Classe/Mati&egrave;re :</label>
<div class="ui-state-default ><p>
<label for="new_classe2"> Classe :</label>
 <input type="text" style="width: 70px" id="new_classe2" value="" /></p>
  <p><label for="new_mat2"> Mati&egrave;re :</label>
  <input type="text" style="width: 250px" id="new_mat2" value="" /></p></div>
<label for="horaires"> Dur&eacute;e :</label>
<div class="ui-state-default id="horaires2"><p>
<label for="DEB2"> de  :</label>
 <input type="text" style="width: 70px" id="timepicker_start2" value="" />
<label for="FIN2">&agrave; :</label>
<input type="text" style="width: 70px" id="timepicker_end2" value="" /></p></div>';
echo '</fieldset>
</form>
</div>';
?>
</body>
</html>