<?php
/* =============================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 2.5 du 20/04/2012
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - fonctions -
   			_-=-_
  "Valid XHTML 1.0 Strict"
   ============================================= */
Function DebutSemaine($J = "") {
// Retourne un timestamp du lundi à 8 h de la semaine contenant le jour $J
// Si $J est vide c'est la semaine en cours qui est prise en compte.
	if ($J=="") $J=mktime(8,0,0);
	return (mktime(8,0,0,date("m",$J),date("d",$J),date("Y",$J))-((date("w",$J)+6) % 7)*86400);
}
Function LeJour ($J = "") {
// Retourne le nom du jour de la semaine en français correspondant au timestamp passé en paramètre
// Si $J est vide c'est la semaine en cours qui est prise en compte.
	if ($_SESSION['version']==">=432") setlocale(LC_TIME,"french");
	else setlocale("LC_TIME","french");
	if ($J=="") {
		$JourSem=strftime("%A");
	} else {
		$JourSem=strftime("%A", $J);
	}
		Return $JourSem;
}

Function datefr($tstamp="") {
//retourne la date en français
	if ($_SESSION['version']==">=432") setlocale(LC_TIME,"french");
	else setlocale("LC_TIME","french");
	$datfr=strFTime("%d %b %Y", $tstamp);
	Return htmlentities($datfr);
}

Function datefr2($tstamp="") {
//retourne le jour et le mois en français
	if ($_SESSION['version']==">=432") setlocale(LC_TIME,"french");
	else setlocale("LC_TIME","french");
	$datfr=strFTime("%d %b ", $tstamp);
	Return htmlentities($datfr);
}

Function moisanfr($tstamp="") {
//retourne le mois et l'année  en français
	if ($_SESSION['version']==">=432") setlocale(LC_TIME,"french");
	else setlocale("LC_TIME","french");
	$moisanfr=strFTime("%b %Y", $tstamp);
	Return $moisanfr;
}

function Affiche_seance($param) {
    //affiche une seance dans le contenu du cdt
    global $cible,$dlm1,$dlm2;
    $rq = "SELECT DATE_FORMAT(date,'%d/%m/%Y'),contenu,afaire,DATE_FORMAT(datafaire,'%d/%m/%Y'),id_rubrique,date,on_off,DATE_FORMAT(datevisibi,'%d/%m/%Y') FROM cahiertxt
 WHERE (id_rubrique=$param) ";
// lancer la requete
$result = @mysql_query ($rq) or die (mysql_error());
if (mysql_num_rows($result) >0) {
    while ($ligne = mysql_fetch_array($result, MYSQL_NUM))
	  {
	  //$textcours=stripslashes(markdown($ligne[1]));
	  $textcours=utf8_encode($ligne[1]);
	  //$textafaire=stripslashes(markdown($ligne[2]));
	  $textafaire= utf8_encode($ligne[2]) ;
	  //$day="1,0,0,12,1,2007";echo $day;
	  $jour=LeJour (strToTime($ligne[5]));
	  //debut
	  if ($ligne[1]!="") {
	  echo '<tbody><tr><th colspan="2"></th></tr></tbody>';
	  echo '<tbody>';
	  echo '<tr>';
	  //affichage de la seance
	  echo '<td class="seance">S&eacute;ance du <br/>'.$jour.'&nbsp;'.$ligne[0].'<br />Visible le '.$ligne[7].' </td>';
	  if($ligne[1]!="" && $ligne[6]==1) echo '<td class="contenu2">';
	  elseif($ligne[1]!="" && $ligne[6]==2) echo '<td class="contenu3">';
	  else echo '<td class="contenu">';
	  echo $textcours.'</td></tr>';
	  //affichage, s'il existe, du travail a effectuer
	  if ($ligne[2]!="") {
	  echo '<tr><td class="afaire">A faire pour le :<br />'.$ligne[3].'</td><td class="contenu">';
	  echo $textafaire.'</td></tr>';
	  }
	  //fin

	  echo '</tbody>';
	   echo '<tbody><tr><th class="bas" colspan="2">';
           if ($_SESSION['cequi']=="prof" && (mb_ereg('prof\.', $_SERVER['REQUEST_URI'])))
               {
              echo '<form action="';
              echo 'cahier_texte_prof.php';
              echo '" method="post"><div><input type="hidden" name="number" value="';
              echo $ligne[4];
              echo '" /><input type="hidden" name="rubriq" value="';
              echo $cible;
              echo '" /><input name="date" type="hidden"  value="'.$date.'" />';
              if($ligne[6]=="0") echo '<input type="submit" name="modif" value="" class="bt-modifier" />&nbsp;
              <input type="submit" name="suppr" value="" class="bt-supprimer" />';
              echo '<input name="TA" type="hidden"  value="'. md5($_SESSION['RT'].htmlentities($_SERVER['PHP_SELF'])).'" />';
              echo '</div></form>';
               }
	  echo '</th></tr>';
	  echo '</tbody>';
	  echo '<tbody><tr><th colspan="2"><hr /></th></tr></tbody>';
	  }
	  else {
	  echo '<tbody><tr><th colspan="2"></th></tr></tbody>';
	  echo '<tbody>';
	  echo '<tr>';
	  //affichage de la seance
	  echo '<td class="afaire">Donn&eacute; le :&nbsp;'.$ligne[0].'<br />Visible le '.$ligne[7];
	  //affichage, s'il existe, du travail a effectuer
	  if ($ligne[2]!="") {
	  echo '<br/>Pour le :&nbsp;'.$ligne[3].'</td>';
	  if($ligne[6]==1) echo '<td class="contenu2">';
	  elseif($ligne[6]==2) echo '<td class="contenu3">';
	  else echo '<td class="contenu">';
	  echo $textafaire.'</td></tr>';
	  }
	  //fin
          echo '</tbody>';
	  echo '<tbody><tr><th class="bas" colspan="2">';
	  if ($_SESSION['cequi']=="prof" && (mb_ereg('prof\.', $_SERVER['REQUEST_URI'])))
              {
              echo  '<form action="';
              echo 'cahier_texte_prof.php';
              echo '" method="post"><div><input type="hidden" name="number" value="';
              echo $ligne[4];
               echo '" /><input type="hidden" name="rubriq" value="';
              echo $cible;
              echo '"/><input name="date" type="hidden" value="'.$date.'" />';
              if($ligne[6]=="0") echo '<input type="submit" name="modif" value="" class="bt-modifier" />&nbsp;
              <input type="submit" name="suppr" value="" class="bt-supprimer" />';
              echo '<input name="TA" type="hidden"  value="'. md5($_SESSION['RT'].htmlentities($_SERVER['PHP_SELF'])).'" />';
              echo '</div></form>';
              }
	  echo '</th></tr>';
	  echo '</tbody>';
	  echo '<tbody><tr><th colspan="2"><hr /></th></tr></tbody>';
	  }
        }
    }
}

function Affiche_seq($param) {
    global $dlm1,$dlm2;
    //affiche une sequence et son contenu dans le cdt
    $rqs = "SELECT titrecourt,titre,contenu FROM sequences WHERE id_seq='$param'";
    // lancer la requête
    $results = @mysql_query ($rqs) or die (mysql_error());
    // Combien y a-t-il d'enregistrements ?
    if (mysql_num_rows($results)>0)
           {
            $rows = mysql_fetch_array($results, MYSQL_NUM);
            echo '<tbody><tr><th colspan="2"></th></tr></tbody>';
            //echo '<tbody>';
            echo '<tbody><tr><td colspan="2" ><span class="malegende" ><span class="titre_seq">'.utf8_encode($rows[1])."</span></span></td></tr></tbody>";
            echo '<tbody class="field11"><tr><td colspan="2">';
            // if ($_SESSION['cequi']=="prof" && (mb_ereg('prof\.', $_SERVER['REQUEST_URI'])))
            echo '<span class="switch_seq clos" id="_s'.$param.'" title="+ de d&eacute;tails"> &nbsp;</span>
            <span class="order up" id="_ord'.$param.'" title="Afficher par date croissante">&nbsp;</span >
            <div  id="d_s'.$param.'" class="descr_seq off">'.utf8_encode($rows[2]).'</div>
            <input type="hidden" id="r_ord'.$param.'" value="'.$param.' ' .$dlm1.$dlm2 .'" />';
            if ($_SESSION['cequi']=="prof" && (mb_ereg('prof\.', $_SERVER['REQUEST_URI'])))
                {
                echo'<input type="hidden" id="b_ord'.$param.'" value="true" />
                <input name="TA" type="hidden" id="t_ord'.$param.'" value="'. md5($_SESSION['RT'].htmlentities($_SERVER['PHP_SELF'])).'" /> ';
                }
            echo ' </td></tr></tbody>';
            echo '<tbody id="c_ord'.$param.'" class="field11">';
            $rq2 = "SELECT id_rubrique FROM cahiertxt  WHERE seq_id='$param'".$dlm1.$dlm2." order by date desc ";
            $result2 = @mysql_query ($rq2) or die (mysql_error());
            while ($ligne = mysql_fetch_array($result2, MYSQL_NUM))
              {
                Affiche_seance_seq ($ligne[0],$buton);
              }
            echo '</tbody>';
            echo '<tbody><tr><th colspan="2"><hr /></th></tr></tbody>';
            }
}

function Affiche_seance_seq ($param,$boutons=FALSE,$tick="") {
    //affiche une sequence associee a une sequence
     global $cible;
     if ($cible =="")
         {
         $my_rq="SELECT `id_auteur` FROM `cahiertxt` WHERE `id_rubrique` =".$param;
         $r = @mysql_query ($my_rq) or die (mysql_error());
         $ret=mysql_fetch_array($r, MYSQL_NUM);
         $cible=$ret[0];
         }
    if ($tick =="")
        {
        $tick=md5($_SESSION['RT'].htmlentities($_SERVER['PHP_SELF']));
        }

   $rq = "SELECT DATE_FORMAT(date,'%d/%m/%Y'),contenu,afaire,DATE_FORMAT(datafaire,'%d/%m/%Y'),id_rubrique,date,on_off,DATE_FORMAT(datevisibi,'%d/%m/%Y') FROM cahiertxt
 WHERE (id_rubrique=$param) ";

$result = @mysql_query ($rq) or die (mysql_error());
if (mysql_num_rows($result) >0) {
    while ($ligne = mysql_fetch_array($result, MYSQL_NUM))
	  {
	   //$textcours=stripslashes(markdown($ligne[1]));
	  $textcours= utf8_encode($ligne[1]);
	  //$textafaire=stripslashes(markdown($ligne[2]));
	  $textafaire= utf8_encode($ligne[2]) ;
	  //$day="1,0,0,12,1,2007";echo $day;
	  $jour=LeJour (strToTime($ligne[5]));
	  //debut
	  if ($ligne[1]!="")
                        {
                        echo '<tr>';
                        //affichage de la seance
                        echo '<td class="seance">S&eacute;ance du <br/>'.$jour.'&nbsp;'.$ligne[0].'<br />Visible le '.$ligne[7].' </td>';
                        if($ligne[1]!="" && $ligne[6]==1) echo '<td class="contenu2">';
                        elseif($ligne[1]!="" && $ligne[6]==2) echo '<td class="contenu3">';
                        else echo '<td class="contenu">';
                        echo $textcours.'</td></tr>';
                        //affichage, s'il existe, du travail a effectuer
                        if ($ligne[2]!="")
                            {
                            echo '<tr><td class="afaire">A faire pour le :<br/>'.$ligne[3].'</td><td class="contenu">';
                            echo $textafaire.'</td></tr>';
                            }
                        //fin
                        echo '<tr><th class="bas" colspan="2">';
                        if ($_SESSION['cequi']=="prof" && (mb_ereg('prof\.', $_SERVER['REQUEST_URI'])) || $boutons==true)
                                    {
                                     echo '<form action="';
                                      echo 'cahier_texte_prof.php';
                                      echo '" method="post"><div><input type="hidden" name="number" value="';
                                      echo $ligne[4];
                                       echo '" /><input type="hidden" name="rubriq" value="';
                                      echo $cible ;
                                      echo ' " /><input name="date" type="hidden" value="'.$date.'" />';
                                      if($ligne[6]=="0") echo '<input type="submit" name="modif" value="" class="bt-modifier" />&nbsp;
                                      <input type="submit" name="suppr" value="" class="bt-supprimer" />';
                                      echo '<input name="TA" type="hidden"  value="'. $tick.'" />';
                                      echo '</div></form>';
                                      }
                          echo '</th></tr>';
                          }
	  else
                           {
                        echo '<tr>';
                        //affichage de la seance
                        echo '<td class="afaire">Donn&eacute; le :&nbsp;'.$ligne[0].'<br />Visible le '.$ligne[7];
                        //affichage, s'il existe, du travail a effectuer
                        if ($ligne[2]!="")
                                {
                                echo '<br />Pour le :&nbsp;'.$ligne[3].'</td>';
                                if($ligne[6]==1) echo '<td class="contenu2">';
                                elseif($ligne[6]==2) echo '<td class="contenu3">';
                                else echo '<td class="contenu">';
                                echo $textafaire.'</td></tr>';
                                }
                        //fin
                echo '<tr><th class="bas" colspan="2">';
                if ($_SESSION['cequi']=="prof" && (mb_ereg('prof\.', $_SERVER['REQUEST_URI'])) || $boutons==true)
                    {
                    echo '<form action="';
                    echo 'cahier_texte_prof.php';
                    echo '" method="post"><div><input type="hidden" name="number" value="';
                    echo $ligne[4];
                    echo '" /><input type="hidden" name="rubriq" value="';
                    echo $cible ;
                    echo ' " /><input name="date" type="hidden"  value="'.$date.'" />';
                    if($ligne[6]=="0") echo '<input type="submit" name="modif" value="" class="bt-modifier" />&nbsp;
                    <input type="submit" name="suppr" value="" class="bt-supprimer" />';
                    echo '<input name="TA" type="hidden"  value="'. $tick.'" />';
                    echo '</div></form>';
                    }
                echo '</th></tr>';
                }
         }
    }
}
?>
