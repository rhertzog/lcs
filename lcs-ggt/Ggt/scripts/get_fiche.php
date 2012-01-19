<?php
/* ==================================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 2.3 du 31/12/2011
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script d action sur les fiches-
			_-=-_
   =================================================== */

header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Expires: " . gmdate("D, d M Y H:i:s") . " GMT");
session_name("gestap_Lcs");
@session_start();
//si la page est appeleee par un utilisateur non identifie
if (!isset($_SESSION['login']) )exit;
//si la page est appelee par un utilisateur non prof
elseif ($_SESSION['cequi']!="prof" && $_SESSION['cequi']!="eleve" && $_SESSION['login'] != "admin") exit;
//indique que le type de la reponse renvoyee au client sera du Texte
header("Content-Type: text/plain" );
//anti Cache pour HTTP/1.1
header("Cache-Control: no-cache , private");
//anti Cache pour HTTP/1.0
header("Pragma: no-cache");
// Connexion a la base de donnees
require_once ('../Includes/config.inc.php');
if (get_magic_quotes_gpc()) require_once("../Includes/class.inputfilter_clean.php");
else require_once '../Includes/htmlpur/library/HTMLPurifier.auto.php';

// actions  fiche
if  (isset($_POST['actionF']))
    {
    if ($_POST['actionF'] == "lire" || $_POST['actionF'] == "lire2")
        {
        $ru=$_POST['num_fich'];
        if  ($_POST['actionF'] == "lire") $rq = "SELECT id_at,nom,description,is_propose FROM ateliers WHERE id_at='$ru' AND prof='".$_SESSION['login']."'";
        if  ($_POST['actionF'] == "lire2") $rq = "SELECT id_at,nom,description,is_propose FROM ateliers WHERE id_at='$ru'";
        // lancer la requete
        $result = @mysql_query ($rq) or die (mysql_error());
        // Combien y a-t-il d'enregistrements ?
        if (mysql_num_rows($result)>0)
            {
            $row = mysql_fetch_array($result, MYSQL_NUM);//)
            echo "<span id='id_fic'>" . $row[0] . "</span>\n";
            echo "<span id='n_fic'>" .$row[1] . "</span>\n";
            echo "<span id='d_fic'>" . $row[2] . "</span>\n";
            echo "<span id='is_fic'>" . $row[3] . "</span>\n";
            }
        else echo "Erreur de lecture";
        exit;
        }

    //suppression d'une fiche
    if ($_POST['actionF'] == "delete")
        {
        $ru=$_POST['num_fich'];
        $rq= "DELETE FROM ateliers WHERE id_at ='$ru' ";
        $result = mysql_query($rq);
        if (!$result)  // Si l'enregistrement est incorrect
            {
            echo "Votre commentaire n'a pas pu \352tre enregistr\351 \340 cause d'une erreur syst\350me"."\n\n" . mysql_error() ;
            mysql_close();     // refermer la connexion avec la base de donnees
            }
        else echo "OK";
        exit;
        }

    //enregistrement/mise a jour d'une fiche
    if (isset($_POST['titre']) && (isset($_POST['statut'])) && (isset($_POST['descript'])) && (isset($_POST['actionF'])))
        {
        if (get_magic_quotes_gpc())
            {
            $Title1  =$_POST['titre'];
            $Title2  =$_POST['statut'];
            $Desc  =htmlentities($_POST['descript']);
            $oMyFilter = new InputFilter($aAllowedTags, $aAllowedAttr, 0, 0, 1);
            $cont1 = $oMyFilter->process($Title1);
            $cont2 = $oMyFilter->process($Title2);
            $cont2=($cont2=='true')? 1: 0;
            $cont3 = $oMyFilter->process($Desc);
            }
        else
            {
            // htlmpurifier
            $Title1  = $_POST['titre'];
            $Title2  =$_POST['statut'];
            $Desc = htmlentities($_POST['descript']);
            $config = HTMLPurifier_Config::createDefault();
            $config->set('Core.Encoding', 'UTF-8');
            $config->set('HTML.Doctype', 'XHTML 1.0 Strict');
            $purifier = new HTMLPurifier($config);
            //$Cours = addSlashes($Cours);
            $cont1 = $purifier->purify($Title1);
            $cont1 = mysql_real_escape_string($cont1);
            $cont2 = $purifier->purify($Title2);
            $cont2 = mysql_real_escape_string($cont2);
            $cont2=($cont2=='true')? 1: 0;
            $cont3= $purifier->purify($Desc);
            $cont3 = mysql_real_escape_string($cont3);
            }
        if ($_POST['actionF'] == "save")
            {
            $rq = "INSERT INTO ateliers (nom,description,prof,niveau,is_propose) VALUES ( '$cont1',  '$cont3','{$_SESSION["login"]}','{$_POST["level"]}','$cont2')";
            }
        if ($_POST['actionF'] == "update")
            {
            $cible=$_POST["num_fic"];
            $rq = "UPDATE  ateliers SET nom='$cont1', is_propose=$cont2, description='$cont3' WHERE id_at='$cible'";
            }
        $result = mysql_query($rq);
        if (!$result)  // Si l'enregistrement est incorrect
            {
            echo "Votre commentaire n'a pas pu &eacute;tre enregistr\351 \340 cause d'une erreur syst\350me"."\n\n" . mysql_error() ;
            mysql_close();     // refermer la connexion avec la base de donnees
            }
        else echo "OK";
        exit;
        }
    }

//Actions niveau
if  (isset($_POST['actionN']))
    {
     //nouveau niveau
    if ($_POST['actionN'] == "new_level")
        {
        $dest=$_POST['name_level'];
        $prof_resp=    $_POST['innateur'];
        $rq = "INSERT INTO niveaux (nom,coordinateur)  VALUES ( '$dest','$prof_resp')";
        $result = mysql_query($rq);
       if (!$result)  // Si l'enregistrement est incorrect
            {
            echo "Votre commentaire n'a pas pu \352tre enregistr\351 \340 cause d'une erreur syst\350me"."\n\n" . mysql_error() ;
            mysql_close();     // refermer la connexion avec la base de donnees
            }
        else echo "OK";
        exit;
        }

    //maj niveau
    if ($_POST['actionN'] == "update_niveau")
        {
        $idnivo=$_POST['num_niveau'];
        $dest=$_POST['titre'];
        $prof_resp=    $_POST['statut'];
        $rq = "UPDATE  niveaux  SET nom='$dest', coordinateur='$prof_resp' WHERE id_niv='$idnivo'";
        $result = mysql_query($rq);
        if (!$result)  // Si l'enregistrement est incorrect
            {
            echo "Votre commentaire n'a pas pu \352tre enregistr\351 \340 cause d'une erreur syst\350me"."\n\n" . mysql_error() ;
            mysql_close();     // refermer la connexion avec la base de donnees
            }
        else echo "OK";
        exit;
        }

//update ordre niveau
    if ($_POST['actionN'] == "up_ordre" )
        {
        $ru=$_POST['num_level'];
        $ord=$_POST['posission'];
        $rq = "UPDATE  niveaux SET ordre='$ord' WHERE id_niv='$ru'";
        $result = mysql_query($rq);
      if (!$result)  // Si l'enregistrement est incorrect
        {
        echo "Votre commentaire n'a pas pu \352tre enregistr\351 \340 cause d'une erreur syst\350me"."\n\n" . mysql_error() ;
        mysql_close();     // refermer la connexion avec la base de donnees
        }
        else echo "OK";
        exit;
        }

    //suppression d'un niveau
    if ($_POST['actionN'] == "deleteniv")
        {
        $ru=$_POST['num_niv'];
        $rq= "DELETE FROM niveaux WHERE id_niv ='$ru' ";
        $result = mysql_query($rq);
        if (!$result)  // Si l'enregistrement est incorrect
            {
            echo "Votre commentaire n'a pas pu \352tre enregistr\351 \340 cause d'une erreur syst\350me"."\n\n" . mysql_error() ;
            mysql_close();     // refermer la connexion avec la base de donnees
            }
        else echo "OK";
        exit;
        }

    //test si des ateliers sont associes a un niveau
    if ($_POST['actionN'] == "test")
        {
         $ru=$_POST['num_seq'];
         $rq = "SELECT count(*) FROM ateliers WHERE niveau='$ru'";
         // lancer la requete
        $result = @mysql_query ($rq) or die (mysql_error());
        // Combien y a-t-il d'enregistrements ?
        if (mysql_num_rows($result)>0)
            {
            $rowt = mysql_fetch_array($result, MYSQL_NUM);
            echo $rowt[0];
            }
         else echo "NOK";
         exit;
        }

    //lecture donnees d'un niveau
    if ($_POST['actionN'] == "lireniveau" )
        {
        $ru=$_POST['num_niv'];
        $rq = "SELECT nom,ordre,coordinateur  FROM niveaux WHERE id_niv='$ru'";
        // lancer la requete
        $result = @mysql_query ($rq) or die (mysql_error());
        // Combien y a-t-il d'enregistrements ?
        if (mysql_num_rows($result)>0)
            {
            $row = mysql_fetch_array($result, MYSQL_NUM);//)
            echo "<span id='nom_nivo'>" . $row[0] . "</span>\n";
            echo "<span id='ordre_nivo'>" . $row[1] . "</span>\n";
            echo "<span id='coord_nivo'>" .$row[2] . "</span>\n";
            }
        else echo "Erreur de lecture";
        exit;
        }
    }


if  (isset($_POST['actionP']))
    {
     //proposition
      if ($_POST['actionP'] == "lire" )
        {
          $rq="SELECT DATE_FORMAT(Dbut,'%d/%m/%Y') , DATE_FORMAT(F1,'%d/%m/%Y')  FROM niveaux WHERE id_niv='".$_POST['num_niv']."' LIMIT 1";
            $result = mysql_query ($rq) or die (mysql_error());
             if (mysql_num_rows($result)>0) {
            $row = mysql_fetch_array($result);
            $ddbut_d=($row[0] == "00/00/0000" ) ? '' : $row[0];
            $dfin_d=($row[1] == "00/00/0000" ) ? '' : $row[1];
             }
        //liste ateliers
        $list_at="";
        $rq = "SELECT id_at,nom,description,prof,niveau FROM ateliers WHERE  niveau='".$_POST['num_niv']."' AND is_propose ='1' order by id_at ASC";
        $result = @mysql_query ($rq) or die (mysql_error());
        $nb = mysql_num_rows($result);
        $j=0;
        $TitreAt=array();
        while ($row = mysql_fetch_object($result))
            {
            $IdAt[$j]=$row->id_at;
            $TitreAt[$j]=$row->nom;
            $ContenuAt[$j]=$row->description;
            $Prof[$j]=$row->prof;
            $j++;
            }

        for ($i=0; $i< count ($TitreAt); $i++)
            {
            $list_at.= '<li class="ui-state-default sequ propose';
            $list_at.= '" liindex="'.$IdAt[$i].'"  title="'.$Prof[$i].'">
            - '.$TitreAt[$i].'
            </li>';
            }
        //liste classe
            include ("/var/www/lcs/includes/user_lcs.inc.php");
            include ("/var/www/lcs/includes/functions.inc.php");
            $liste_classes=search_groups('cn=classe*');
            $list_cl="";$cla_chk=array();
            mysql_close();
            include ('../Includes/config.inc.php');
            $rq="SELECT classe FROM proposes WHERE id_nivo='".$_POST['num_niv']."'";
            $result = @mysql_query ($rq) or die (mysql_error());
            while ($row = mysql_fetch_object($result))
                {
                $cla_chk=mb_split(',', $row->classe);
                }
           for ($loop=0; $loop <count($liste_classes); $loop++)
                {
                $classe_courte=mb_split("_",$liste_classes[$loop]["cn"],2);
               $list_cl.= '<p class="flottante ui-state-highlight"> <input type="checkbox" name="cl_proposed" value="'.$classe_courte[1].'"';
               if (in_array($classe_courte[1], $cla_chk)) $list_cl.= ' checked="checked"';
               $list_cl.= '>'.$classe_courte[1]. '</p>';
                }
        //dates
        $rq="SELECT DATE_FORMAT(frome,'%d/%m/%Y') , DATE_FORMAT(too,'%d/%m/%Y')  FROM proposes WHERE id_nivo='".$_POST['num_niv']."' LIMIT 1";
            $result = @mysql_query ($rq) or die (mysql_error());
            while ($row = mysql_fetch_array($result))
                {
                $ddbut=$row[0];
                $dfin=$row[1];
                }
        echo "<span id='li_at'><ul  class=\"connectedSortable ui-helper-reset  \">" . $list_at . "</ul></span>\n";
        echo "<span id='li_cl'>" . $list_cl . "</span>\n";
        echo "<span id='dbu'>" .$ddbut. "</span>\n";
        echo "<span id='fin'>" .$dfin. "</span>\n";
        echo "<span id='dbu_d'>" .$ddbut_d. "</span>\n";
        echo "<span id='fin_d'>" .$dfin_d. "</span>\n";
        exit;
        }

        if ($_POST['actionP'] == "save" )
        {
          //taitement dates
          $date1=mb_split('/', $_POST['var3']) ;
          $date2=mb_split('/', $_POST['var4']) ;
          $date3=mb_split('/', $_POST['var6']) ;
          $date4=mb_split('/', $_POST['var7']) ;
          $tmpdebut= mktime(0,0 ,1,  $date1[1], $date1[0], $date1[2]);
          $tmpfin= mktime(0,0 ,1,  $date2[1], $date2[0], $date2[2]);
          $tmpdebut_d= mktime(0,0 ,1,  $date3[1], $date3[0], $date3[2]);
          $tmpfin_d= mktime(0,0 ,1,  $date4[1], $date4[0], $date4[2]);
          $datedebut=date('Y-m-d',$tmpdebut);
          $datefin=date('Y-m-d',$tmpfin);
          $datedebut_d=date('Y-m-d',$tmpdebut_d);
          $datefin_d=date('Y-m-d',$tmpfin_d);
          //
          $ru=$_POST['var1'];
           $rq= "DELETE FROM proposes WHERE id_nivo ='$ru' ";
           $result = mysql_query($rq);
            if (!$result)  // Si l'enregistrement est incorrect
                {
                echo "Votre commentaire n'a pas pu \352tre enregistr\351 \340 cause d'une erreur syst\350me"."\n\n" . mysql_error() ;
                mysql_close();     // refermer la connexion avec la base de donnees
                exit;
                }
            else
            {
             $rq = "INSERT INTO proposes (id_nivo,id_atelier,frome,too,classe) VALUES ( '{$_POST["var1"]}', '{$_POST["var2"]}','$datedebut','$datefin','{$_POST["var5"]}')";
             $result = mysql_query($rq);
            if (!$result)  // Si l'enregistrement est incorrect
                {
                echo "Votre commentaire n'a pas pu \352tre enregistr\351 \340 cause d'une erreur syst\350me"."\n\n" . mysql_error() ;
                mysql_close();     // refermer la connexion avec la base de donnees
                }
            else
            {
                $rq = "UPDATE  niveaux SET Dbut='$datedebut_d',F1=' $datefin_d' WHERE id_niv='".$ru."' LIMIT 1";
                 $result = mysql_query($rq);
            if (!$result)  // Si l'enregistrement est incorrect
                {
                echo "Votre commentaire n'a pas pu \352tre enregistr\351 \340 cause d'une erreur syst\350me"."\n\n" . mysql_error() ;
                mysql_close();     // refermer la connexion avec la base de donnees
                }
            else echo "OK";
            }


            } exit;
        }

     //repartition initialisation form
     if ($_POST['actionP'] == "liste" || $_POST['actionP'] == "init_liste")
        {
        if ( $_POST['actionP'] == "liste") {
        $rq = "SELECT id_liste FROM listes where niveau='".$_POST['num_niv']."'";
        $result = @mysql_query ($rq) or die (mysql_error());
             if (mysql_num_rows($result)>0) {
          $rq3 = "SELECT html FROM listes where niveau='".$_POST['num_niv']."'";
          $result3 = @mysql_query ($rq3) or die (mysql_error());
          $enrg3 = mysql_fetch_array($result3, MYSQL_NUM);
          echo "<span id='dbu'>" .$enrg3[0]. "</span>\n";
          exit;
             }
        }
        //liste ateliers proposes
        $rq = "SELECT id_atelier FROM proposes WHERE   id_nivo='".$_POST['num_niv']."'";
        $result = mysql_query ($rq) or die (mysql_error());

        if ( mysql_num_rows($result)>0)
            {
            $enrg = mysql_fetch_array($result, MYSQL_NUM);
            $liste_ateliers=mb_split(",",$enrg[0]) ;
            }

        $j=0;
        $TitreAt=array();
        $at_col=intval(count ( $liste_ateliers)/3);
        $contenu='<div class="column ui-sortable">';
        for ($k=0; $k< count ($liste_ateliers); $k++)
            {
            $list_at="";
            $rq = "SELECT id_at,nom FROM ateliers WHERE  id_at='".$liste_ateliers[$k]."'";
            $result = @mysql_query ($rq) or die (mysql_error());
            //$nb = mysql_num_rows($result);

            while ($row = mysql_fetch_object($result))
                {
                $IdAt[$j]=$row->id_at;
                $TitreAt[$j]=$row->nom;
                $Cl[$row->id_at]=c.($j+1);
                $j++;
                }
            }
         for ($i=0; $i< count ($TitreAt); $i++)
            {
             $contenu.= '<div class="portlet" id=at'.$IdAt[$i].'><div class="portlet-header">
                 '.substr($TitreAt[$i],0,60).' <span class="'.$Cl[$IdAt[$i]].'">&nbsp</span></div>
            <div class="portlet-content">';
             //liste eleve
             $rq = "SELECT eleve,login,classe,v1,v2,v3 FROM inscriptions WHERE  v1='".$IdAt[$i]."'";
             $result = @mysql_query ($rq) or die (mysql_error());
             $ind=0;$identite=$nomf=$pre=$classe=$v1=$v2=$v3=array();
            while ($row = mysql_fetch_object($result))
            {
             //$elev[$ind]   =$row->eleve;
             $identite=mb_split( ' ',$row->eleve);
             $pre[$ind]=$identite[0];
             $nomf[$ind]=$identite[1];
             $classe[$ind]=$row->classe;
             $v1[$ind]=$row->v1;
             $v2[$ind]=$row->v2;
             $v3[$ind]=$row->v3;
             $log[$ind]=$row->login;
             $ind++;
            }
            //on trie le tableau
            array_multisort($nomf,$pre,$classe,$v1,$v2,$v3,$log);
            //
                //on affiche les eleves
              for ($l=0; $l< count ($nomf); $l++)
                {
                $contenu.= '<li class="ui-state-default at"><span class="log" id="'.$log[$l].'"></span><span class="rang">'.($l+1).'</span> '.$pre[$l].' '.$nomf[$l].' &nbsp; &nbsp;'.$classe[$l]
                .'<span class="'.$Cl[$v3[$l]].'">&nbsp;</span>&nbsp;<span class="'.$Cl[$v2[$l]].'">&nbsp;</span>&nbsp;<span class="'.$Cl[$v1[$l]].'">&nbsp;</span></li>';
                }
              //
           $contenu.= '</div></div>';
           if ((($i +1)% $at_col) == 0 && count ($TitreAt)!=$i+1) $contenu.= '</div><div class="column">';
            }
            $contenu.='</div>';
            echo "<span id='dbu'>" .$contenu. "</span>\n";
            exit;
        }


         if ($_POST['actionP'] == "saveliste" )
        {
             //existant ?
              $rq = "SELECT id_liste FROM listes where niveau='".$_POST['num_niv']."'";
              $result = @mysql_query ($rq) or die (mysql_error());
             if (mysql_num_rows($result)>0) $rq2 = "UPDATE  listes  SET html='".addslashes($_POST['cont'])."'  WHERE niveau='".$_POST['num_niv']."'";
             else  $rq2 = "INSERT INTO listes (niveau,html) VALUES ('{$_POST['num_niv']}', '".addslashes($_POST['cont'])."')";

             $result = mysql_query($rq2);
        if (!$result)  // Si l'enregistrement est incorrect
            {
            echo "Votre commentaire n'a pas pu \352tre enregistr\351 \340 cause d'une erreur syst\350me"."\n\n" . mysql_error() ;
            mysql_close();     // refermer la connexion avec la base de donnees
            }
        else echo "OK";
        exit;
         }

          if ($_POST['actionP'] == "export" )
        {
          $ru=$_POST['var_niv'];
          $atelier=substr($_POST["var_at"],2);
          //dates deroulement
          $rq="SELECT Dbut, F1  FROM niveaux WHERE id_niv='".$ru."' LIMIT 1";
            $result = mysql_query ($rq) or die (mysql_error());
             if (mysql_num_rows($result)>0) {
            $row = mysql_fetch_array($result);
            $ddbut_d=$row[0];
            $dfin_d=$row[1];
             }
          //enregistrement
          $rq="SELECT id FROM repartition WHERE niveau ='$ru' AND eleve_id='".$_POST['var_log']."'";
              $result = @mysql_query ($rq) or die (mysql_error());
             if (mysql_num_rows($result)>0)
           $rq=  "UPDATE  repartition  SET at_id='".$atelier."', deb='". $ddbut_d."' , fin='". $dfin_d."' WHERE niveau ='$ru' AND eleve_id='".$_POST['var_log']."'";
           else   $rq = "INSERT INTO repartition (niveau,at_id,eleve_id,deb,fin) VALUES ( '{$_POST["var_niv"]}', '$atelier','{$_POST["var_log"]}', $ddbut_d, $dfin_d)";
           $result = mysql_query($rq);
            if (!$result)  // Si l'enregistrement est incorrect
                {
                echo "Votre commentaire n'a pas pu \352tre enregistr\351 \340 cause d'une erreur syst\350me"."\n\n" . mysql_error() ;
                mysql_close();     // refermer la connexion avec la base de donnees
                }
            else echo "OK";
            exit;
            }
            if ($_POST['actionP'] == "init" )
                {
                $ru=$_POST['var1'];
                $rq = "UPDATE  niveaux SET Dbut='',F1='' WHERE id_niv='".$ru."' LIMIT 1";
                $result = mysql_query($rq);
                if (!$result)  // Si l'enregistrement est incorrect
                    {
                    echo "Votre commentaire n'a pas pu \352tre enregistr\351 \340 cause d'une erreur syst\350me"."\n\n" . mysql_error() ;
                    mysql_close();     // refermer la connexion avec la base de donnees
                    }
                else
                    {
                    $rq= "DELETE FROM inscriptions WHERE niveau ='$ru' ";
                    $result = mysql_query($rq);
                    if (!$result)  // Si l'enregistrement est incorrect
                        {
                        echo "Votre commentaire n'a pas pu \352tre enregistr\351 \340 cause d'une erreur syst\350me"."\n\n" . mysql_error() ;
                        mysql_close();     // refermer la connexion avec la base de donnees
                        }
                    else
                        {
                        $rq= "DELETE FROM proposes WHERE id_nivo ='$ru' ";
                        $result = mysql_query($rq);
                        if (!$result)  // Si l'enregistrement est incorrect
                            {
                            echo "Votre commentaire n'a pas pu \352tre enregistr\351 \340 cause d'une erreur syst\350me"."\n\n" . mysql_error() ;
                            mysql_close();     // refermer la connexion avec la base de donnees
                            }
                        else  echo "OK";
                        }
                    }
                exit;
                }


    }

//inscription eleve

    if ($_POST['actionE'] == "save" )
    {
       $rq = "SELECT id_insc FROM inscriptions WHERE login ='".$_SESSION['login']."'";
       $result = @mysql_query ($rq) or die (mysql_error());
       if (mysql_num_rows($result)>0)
            {
            $row = mysql_fetch_array($result, MYSQL_NUM);
            $id_inscription=$row[0] ;
            $rq = "UPDATE  inscriptions  SET niveau='".$_POST['var1']."', v1='".$_POST['voeu1']."', v2='".$_POST['voeu2']."' ,v3='".$_POST['voeu3']."'  WHERE id_insc='$row[0]'";
           }
        else
          $rq = "INSERT INTO inscriptions (eleve,login,classe,niveau,v1,v2,v3) VALUES ( '{$_SESSION["nomcomplet"]}', '{$_SESSION["login"]}', '{$_SESSION["saclasse"]}' ,
           '{$_POST["var1"]}','{$_POST["voeu1"]}','{$_POST["voeu2"]}','{$_POST["voeu3"]}')";
          $result = mysql_query($rq);
        if (!$result)  // Si l'enregistrement est incorrect
            {
            echo "Votre commentaire n'a pas pu \352tre enregistr\351 \340 cause d'une erreur syst\350me"."\n\n" . mysql_error() ;
            mysql_close();     // refermer la connexion avec la base de donnees
            }
        else echo "OK";
        exit;
    }

    if ($_POST['action'] == "iscoord" )
    {
       $ru=$_POST['num_niv'];
       $rq = "SELECT coordinateur  FROM niveaux WHERE id_niv='$ru' AND coordinateur='".$_SESSION["login"]."'";
       $result = @mysql_query ($rq) or die (mysql_error());
       if (mysql_num_rows($result)==1) echo 'YES'; else echo 'NO';
       exit;
    }
?>