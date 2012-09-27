<?php
/* ==================================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 2.5 du 20/04/2012
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script d'import d'emploi du temps-
			_-=-_
    "Valid XHTML 1.0 Strict"
   =================================================== */
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Expires: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
session_name("Cdt_Lcs");
@session_start();
include "../Includes/check.php";
if (!check()) exit;
//si la page est appelee par un utilisateur non identifie
if (!isset($_SESSION['login']) )exit;

//si la page est appelee par un utilisateur non prof
elseif ($_SESSION['cequi']!="prof") exit;

if (isset($_POST['Fermer']))
echo '<script type="text/javascript">
            //<![CDATA[
            window.close();
           //]]>
            </script>';


function SansAccent($texte){
    $accent='ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËéèêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ';
    $noaccent='AAAAAAaaaaaaOOOOOOooooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn';
    $texte = strtr($texte,$accent,$noaccent);
    return $texte;
}
function offset_search( ) {
     $filename = "../json_files/".$_SESSION['login'].".json";
        $evenements=  json_decode(file_get_contents($filename),true);
        foreach ($evenements as $key => $row) {
        $id_array[$key]  = $row['id'];
        }
        sort($id_array);
        return (end($id_array));
 }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html  xmlns="http://www.w3.org/1999/xhtml" >
<head>
<title>Import perso</title>
<meta name="author" content="Philippe LECLERC -TICE CAEN" />
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<link href="../style/style.css" rel="stylesheet" type="text/css"/>
<!--[if IE]>
<link href="../style/style-ie.css"  rel="stylesheet" type="text/css"/>
<![endif]-->
</head>
<body>
<h1 class='title'></h1>
<?php

//si clic sur le bouton Valider
if (isset($_POST['Valider']))
    {
    //verification de l'existence du repertoire
    if ($_POST['choice']==1 || $_POST['choice']==2 || !isset($_POST['choice']))
        {
        $offset=($_POST['choice']!=2) ? 0 :  offset_search( );
        //traitement du  fichier
        if ((!empty($_FILES["FileSelection1"]["name"])))
            {
            if ($_FILES["FileSelection1"]["size"]>0)
                {

                $nomFichier = SansAccent($_FILES["FileSelection1"]["name"]) ;
                $nomFichier=mb_ereg_replace("'|[[:blank:]]","_",$nomFichier);
                $nomTemporaire = $_FILES["FileSelection1"]["tmp_name"] ;
                //chargement du fichier
                copy($nomTemporaire,"../json_files/".$_SESSION['login'].".ics");

                //test de la presence du fichier uploade
                if (file_exists("../json_files/".$_SESSION['login'].".ics"))
                    {
                    require_once('../Includes/parsical/SG_iCal.php');
                    setlocale(LC_TIME,"french");
                   $ICS = "../json_files/".$_SESSION['login'].".ics";
                        $ical = new SG_iCalReader($ICS);
                        $query = new SG_iCal_Query();
                        $evts = $ical->getEvents();
                        $delta=0;//incremente l'id des evenements repetitifs
                        foreach($evts as $id => $ev) {
                                $mat_tronc=mb_split(':',$ev->getDescription());
                                $mat_tronc[1]=preg_replace ( "/[\r\n]+/", "@", $mat_tronc[1]);
                                $mat_tronquee=mb_split("@",$mat_tronc[1]);
                                $mat_finale=$mat_tronquee[0];
                                $phase=mb_split("- ",$ev->getProperty('summary'));
                                $classe=$phase[count($phase)-1];
                                $jsEvt = array(
                                        "id" => ($id+$offset+$delta+1),
                                        "title" => $classe,
                                        "matiere"=> $mat_finale,
                                        "start" => $ev->getStart(),
                                        "end"   => $ev->getEnd()-1,
                                        "allDay" => $ev->isWholeDay(),
                                        "url" => 'cahier_texte_prof.php?id='.($id+1).'&start='.$ev->getStart()

                                );
                                if (isset($ev->recurrence)) {
                                        $count = 0;
                                        $start = $ev->getStart();
                                        $freq = $ev->getFrequency();
                                        if ($freq->firstOccurrence() == $start)
                                                $data[] = $jsEvt;
                                        while (($next = $freq->nextOccurrence($start)) > 0 ) {
                                                if (!$next or $count >= 1000) break;
                                                $count++;$delta++;
                                                $start = $next;
                                                $jsEvt["id"] =$id+$offset+$delta;
                                                $jsEvt["start"] = $start;
                                                $jsEvt["end"] = $start + $ev->getDuration()-1;
                                                 $jsEvt["url"] ='cahier_texte_prof.php?id='.($id+$offset+$delta).'&start='.$start;
                                                $data[] = $jsEvt;
                                        }
                                } else
                                        $data[] = $jsEvt;

                        }


                            $nom_fichier="../json_files/".$_SESSION['login'].".json";
                            if ($_POST['choice']==2)
                                {
                                $ev_existants = json_decode(file_get_contents($nom_fichier));
                                $all_events=  array_merge($ev_existants,$data);
                                }
                            elseif ($_POST['choice']==1 ||  !isset($_POST['choice']))  $all_events=$data;
                            $events = json_encode($all_events);
                            $fichier=  fopen($nom_fichier,"w");
                            fputs($fichier, $events);
                            fclose($fichier);
                            $mess2= ($_POST['choice']==1 ||  !isset($_POST['choice'])) ? " cr&#233;&#233;s " : " ajout&#233;s " ;
                            $mess1="<h3 class='ok'> ".count($data)." cours ont &#233;t&#233; ".$mess2." <br /></h3>";
                            unlink($ICS);
                    }
                else $mess1= "<h3 class='ko'>1. Erreur dans le transfert du fichier <br /></h3>";
                }
            else $mess1= "<h3 class='ko'>2. Erreur dans l'importation du fichier  <br /></h3>";
            }
        else $mess1= "<h3 class='ko'>2. Pas de fichier s&#233;lectionn&#233;  <br /></h3>";
        }
        elseif ($_POST['choice']==3) unlink("../json_files/".$_SESSION['login'].".json");
    }

?>
<form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="post" enctype="multipart/form-data">
<div><input name="TA" type="hidden"  value="<?php echo md5($_SESSION['RT'].htmlentities($_SERVER['PHP_SELF'])); ?>" />
<fieldset id="field7">
<legend id="legende">Import d'emploi du temps</legend>

<?php
//affichage du formulaire
if (!isset($_POST['Valider']))
    {
    echo ' <p>Ce formulaire vous permet d\'importer votre emploi du temps &#224; partir d\'un fichier ical (fichier .ics). 
        Vous devez donc pr&#233;alablement r&#233;cup&#233;rer un export de votre <b>E</b>mploi <b>D</b>u <b>T</b>emps au <u>format ical</u>
        <ul><li> soit dans Pronote -> emploi du temps personnel -> ic&#244;ne en haut &#224; droite </li>
        <li> soit aupr&#232;s de la direction de votre &#233;tablissement </li></ul></p>';
    //echo '<p></p>';
    echo '<ol>';
    echo '<li>S&#233;lectionner le fichier .ics  ( ';
    echo ini_get( 'upload_max_filesize');
    echo '  maxi) : <br /><input type="file" name="FileSelection1" size="40" /></li>';
    if (file_exists("../json_files/".$_SESSION['login'].".json")) echo '<li>Choisir une option<ul><li><input type="radio" name="choice" value="1" checked="checked" >  Remplacer l\'emploi du temps actuel par les cours du fichier joint</li>
    <li><input type="radio" name="choice" value="2" /> Ajouter les cours du fichier joint &#224; l\'emploi du temps actuel</li>
    <li> <input type="radio" name="choice" value="3" /> Ne plus utiliser l\'emploi du temps (suppression des donn&#233;es actuelles)</li></ul></li>';
    echo '</ol>';
    echo '<input type="submit" title="Valider" name="Valider" value="" class="bt-valid" />
    <input class="bt-fermer" type="submit" name="Fermer" value="" />


    ';
    }
//affichage du resultat
else
    {
    if ($mess1!="") echo $mess1;
    echo '<input class="bt-fermer" type="submit" name="Fermer" value="" />';
    }
?>
</fieldset>
</div>
</form>
<?php
include ('../Includes/pied.inc');
?>
</body>
</html>
