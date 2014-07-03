<?php
/* ==================================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 2.5 du 03/032014
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script de mise a jour du fichier json-
			_-=-_
   =================================================== */

header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Expires: " . gmdate("D, d M Y H:i:s") . " GMT");
session_name("Lcs");
@session_start();
//include "../Includes/check.php";
//if (!check()) exit;
//si la page est appeleee par un utilisateur non identifie
if (!isset($_SESSION['login']) )exit;
//si la page est appelee par un utilisateur non prof
elseif ($_SESSION['cequi']!="prof") exit;
//indique que le type de la reponse renvoyee au client sera du Texte
header("Content-Type: text/plain" );
//anti Cache pour HTTP/1.1
header("Cache-Control: no-cache , private");
//anti Cache pour HTTP/1.0
header("Pragma: no-cache");
function my_array_search($needle, $haystack) {
        if (empty($needle) || empty($haystack)) {
            return false;
        }

        foreach ($haystack as $key => $value) {
            $exists = 0;
            foreach ($needle as $nkey => $nvalue) {
                if (!empty($value[$nkey]) && $value[$nkey] == $nvalue) {
                    $exists = 1;
                } else {
                    $exists = 0;
                }
            }
            if ($exists) return $key;
        }

        return false;
    }

 function new_id_search( ) {
     $filename = "../json_files/".$_SESSION['login'].".json";
        $evenements=  json_decode(file_get_contents($filename),true);
        foreach ($evenements as $key => $row) {
        $id_array[$key]  = $row['id'];
        }
        sort($id_array);
        return (end($id_array)+1);
 }

 if (isset($_POST['check_id'])) {
     if ($_POST['check_id']=='new') echo new_id_search();
     exit;
 }
  /*----------------------
creation cours
  ------------------------*/
include '../Includes/htmlpur/library/HTMLPurifier.auto.php';

if(!isset($_POST['di']) && isset($_POST['title']) && isset($_POST['ereitam']) && isset($_POST['trats'])  && isset($_POST['dne']) && isset($_POST['dayall']) && isset($_POST['lru'])  && isset($_POST['action'])  )
    {
    $config = HTMLPurifier_Config::createDefault();
    $config->set('Core.Encoding', 'ISO-8859-15');
    $config->set('HTML.Doctype', 'XHTML 1.0 Strict');
    $purifier = new HTMLPurifier($config);
    $_id = $purifier->purify($_POST['di']);
    $_title = $purifier->purify($_POST['title']);
    $_matiere = $purifier->purify($_POST['ereitam']);
    $_start = $purifier->purify($_POST['trats']);
    $_end = $purifier->purify($_POST['dne']);
    $_url = $purifier->purify($_POST['lru']);

    $filename = "../json_files/".$_SESSION['login'].".json";
    $evenements=  json_decode(file_get_contents($filename));
    if ($_POST['action']== "new_event")   {
        $newEvt = array(
                    "id" => new_id_search(),
                    "title" =>$_title,
                    "matiere"=> $_matiere,
                    "start" =>(int) $_start,
                    "end"   => (int) $_end,
                    "allDay" =>false,
                    "url" =>  $_url
            );
        array_push( $evenements,$newEvt);
        $events = json_encode($evenements);
        exec( "/usr/bin/sudo /usr/share/lcs/scripts/chaccess_cdt.sh Writable jsonfiles ".$_SESSION['login']);
        $fichier=fopen($filename,"w");
        fputs($fichier, $events);
        fclose($fichier);
        exec( "/usr/bin/sudo /usr/share/lcs/scripts/chaccess_cdt.sh NoWritable jsonfiles");
        echo 'OK';
        }
    exit;
    }


  /*----------------------
   suppression cours
   */

  if(isset($_POST['di']) && isset($_POST['action'])&& !isset($_POST['dne']))
        {
        if ($_POST['action']=="del_event")
            {
            $filename = "../json_files/".$_SESSION['login'].".json";
            $evenements=  json_decode(file_get_contents($filename),true);
            //chercher un event depuis son id
            $needle = array('id' =>$_POST['di']);
            $key = my_array_search($needle, $evenements);
            if ($key!=false) unset ($evenements[$key]);
            //on refait un tableau avec un liste de keys continue
            foreach ($evenements as $key => $value)
                {
                $evenementsbis[]=$value;
                }
            $datas= json_encode($evenementsbis);
            exec( "/usr/bin/sudo /usr/share/lcs/scripts/chaccess_cdt.sh Writable jsonfiles ". $_SESSION['login']);
	if (file_put_contents($filename,  $datas)) echo 'OK';
	exec( "/usr/bin/sudo /usr/share/lcs/scripts/chaccess_cdt.sh NoWritable jsonfiles");
            }
        exit;
        }

/*~~~~~~~~~~~~~~~
deplacement cours
 ~~~~~~~~~~~~~~~*/

         if(isset($_POST['di']) && isset($_POST['action']) && isset($_POST['trats']) && isset($_POST['dne'])&& !isset($_POST['title']) && isset($_POST['lru']))
            {
            if ($_POST['action']=="update_event")
                {
                $config = HTMLPurifier_Config::createDefault();
                $config->set('Core.Encoding', 'ISO-8859-15');
                $config->set('HTML.Doctype', 'XHTML 1.0 Strict');
                $purifier = new HTMLPurifier($config);
                $_id = $purifier->purify($_POST['di']);
                $_title = $purifier->purify($_POST['title']);
                $_matiere = $purifier->purify($_POST['ereitam']);
                $_start = $purifier->purify($_POST['trats']);
                $_end = $purifier->purify($_POST['dne']);
                $_url = $purifier->purify($_POST['lru']);
                $filename = "../json_files/".$_SESSION['login'].".json";
                $evenements=  json_decode(file_get_contents($filename),true);
                 //chercher un event depuis son id
                $needle = array('id' =>$_id);
                $key = my_array_search($needle, $evenements);
                if ($key!=false)
                    {
                    $evenements[$key]['start']=$_start;
                    $evenements[$key]['end']=$_end;
                    $evenements[$key]['url']=$_url;
                    }
                $datas= json_encode($evenements);
		exec( "/usr/bin/sudo /usr/share/lcs/scripts/chaccess_cdt.sh Writable jsonfiles ". $_SESSION['login']);
                if (file_put_contents($filename,  $datas)) echo 'OK';
		exec( "/usr/bin/sudo /usr/share/lcs/scripts/chaccess_cdt.sh NoWritable jsonfiles");
                }
                exit;
            }

 /*~~~~~~~~~~~~~~~
modification cours
 ~~~~~~~~~~~~~~~*/
    if(isset($_POST['di']) && isset($_POST['title']) && isset($_POST['ereitam']) && isset($_POST['trats'])  && isset($_POST['dne']) && isset($_POST['dayall']) && isset($_POST['lru'])  && isset($_POST['action']) )
    {
    if ($_POST['action']=="modif_event")
                {
                $config = HTMLPurifier_Config::createDefault();
                $config->set('Core.Encoding', 'ISO-8859-15');
                $config->set('HTML.Doctype', 'XHTML 1.0 Strict');
                $purifier = new HTMLPurifier($config);
                $_id = $purifier->purify($_POST['di']);
                $_title = $purifier->purify($_POST['title']);
                $_matiere = $purifier->purify($_POST['ereitam']);
                $_start = $purifier->purify($_POST['trats']);
                $_end = $purifier->purify($_POST['dne']);
                $_url = $purifier->purify($_POST['lru']);
                $filename = "../json_files/".$_SESSION['login'].".json";
                $evenements=  json_decode(file_get_contents($filename),true);
                //chercher un event depuis son id
                $needle = array('id' =>$_POST['di']);
                $key = my_array_search($needle, $evenements);
                    if ($key!=false)
                        {
                        $evenements[$key]['title']=$_title;
                        $evenements[$key]['matiere']=$_matiere;
                        $evenements[$key]['start']=$_start;
                        $evenements[$key]['end']=$_end;
                        $evenements[$key]['allDay']=false;
                        $evenements[$key]['url']=$_url;
                    }
                $datas= json_encode($evenements);
		exec( "/usr/bin/sudo /usr/share/lcs/scripts/chaccess_cdt.sh Writable jsonfiles ". $_SESSION['login']);
                if (file_put_contents($filename,  $datas)) echo 'OK';
		exec( "/usr/bin/sudo /usr/share/lcs/scripts/chaccess_cdt.sh NoWritable jsonfiles");
                }
                exit;
    }

?>
