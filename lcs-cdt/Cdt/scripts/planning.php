<?php
/* =============================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 2.3 du 31/12/2011
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script de plannification d'un devoir par un prof -
			_-=-_
    "Valid XHTML 1.0 Strict"
   ============================================= */
session_name("Cdt_Lcs");
@session_start();
include "../Includes/check.php";
if (!check()) exit;
//si la page est appelee par un utilisateur non identifie ou non prof
if (!isset($_SESSION['login'])) exit;
elseif ($_SESSION['cequi']!='prof' ) exit;

//si clic sur le bouton Quitter
if (isset($_POST['quit']))
    {
    $numrubrique=$_POST['numrubri'];
    header ("location: cahier_texte_prof.php?rubrique=$numrubrique");
    exit();
    }
include_once("../Includes/basedir.inc.php");
include_once "$BASEDIR/lcs/includes/headerauth.inc.php";
include_once "$BASEDIR/Annu/includes/ldap.inc.php";
include_once "$BASEDIR/Annu/includes/ihm.inc.php";
mysql_close();
$cren_off=array();
include "../Includes/data.inc.php";
include "../Includes/functions2.inc.php";	
include_once("../Includes/config.inc.php");
include_once("../Includes/fonctions.inc.php");
include_once("../Includes/creneau.inc.php");
$login=$_SESSION['login'];
//duree max des devoirs en fonction des creneaux off
if (in_array("M4", $cren_off)) $duremaxmatin=3;
elseif (in_array("M5", $cren_off)) $duremaxmatin=4;
else $duremaxmatin=5;
if (in_array("S4", $cren_off)) $duremaxaprem=8;
elseif (in_array("M5", $cren_off)) $duremaxaprem=9;
else $duremaxaprem=10;
//test si les listes de diffusion sont activees
if (!isset($_SESSION['liste']))
    {
    exec ("/bin/grep \"#<listediffusionldap>\" /etc/postfix/main.cf", $AllOutPut1, $ReturnValueShareName); 
    exec ("/bin/grep \"#<listediffusionldap>\" /etc/postfix/mailing_list.cf", $AllOutPut2, $ReturnValueShareName);
    if (( count($AllOutPut1) >= 1) || ( count($AllOutPut2) >= 1)) $_SESSION['liste']=1; else $_SESSION['liste']=0;
    }
if (get_magic_quotes_gpc()) require_once("../Includes/class.inputfilter_clean.php");
else require_once '../Includes/htmlpur/library/HTMLPurifier.auto.php';
/************************	
Traitement du formulaire
*************************/	

//Enregistrement des donnees
if (isset($_POST['enregistrer']) )
    { 
    // Traiter les donnees
    // Verifier $Sujet  et la debarrasser de tout antislash et tags possibles
    if (strlen($_POST['sujet']) > 0)
        { 
        if (get_magic_quotes_gpc())
            {
            $Sujet  =htmlentities(utf8_decode($_POST['sujet']));
            $oMyFilter = new InputFilter($aAllowedTags, $aAllowedAttr, 0, 0, 1);
            $Sujet = $oMyFilter->process($Sujet);
            }
        else
            {
            // htlmpurifier
            $Sujet = utf8_decode($_POST['sujet']);
            $config = HTMLPurifier_Config::createDefault();
            $config->set('Core.Encoding', 'ISO-8859-15'); 
            $config->set('HTML.Doctype', 'XHTML 1.0 Strict');
            $purifier = new HTMLPurifier($config);
            $Sujet = $purifier->purify($Sujet);
            $Sujet=mysql_real_escape_string($Sujet);
            }
        }
    else
        { // Si aucun sujet n'a ete saisi
        $Sujet = "";
        }

    // Verifier $duree et la debarrasser de tout antislash et tags possibles
    if ((strlen($_POST['duree'])> 0) && is_numeric($_POST['duree']))
        { 
        $message="";
        $duree= addSlashes(strip_tags(stripslashes($_POST['duree'])));
        //limitation de duree
        if ((($_POST['creneau']<=4) && ( ($_POST['creneau'] + $duree) <= $duremaxmatin))
        //si la fin d'un devoir de l'apres-midi 
        || (($_POST['creneau']> 4) && ( ($_POST['creneau'] + $duree) <= $duremaxaprem) ))
            {//pour chaque creneau du devoir 
            $lezard="false";
            for ($loop = $_POST['creneau']; $loop <= $_POST['creneau'] + $duree -1; $loop++)
            {//test s'i les 2 creneaux sont occupes pour la classe active 
            $rq ="SELECT count(*)  FROM devoir WHERE date = '{$_POST['data']}' AND classe= '{$_POST['classe']}' AND creneau+ dur\351e-1 >= '$loop' AND `creneau` <='$loop'"; 
            $res = mysql_query($rq);
            while ($row = mysql_fetch_array($res, MYSQL_NUM)) 
                {
                if ($row[0] == 2)
                    { 
                    $lezard="true";
                    $message=$_POST['classe'].",";
                    }
                }
            //test s'i les 2 creneaux sont occupes pour les autres classes 
            if ((isset($_SESSION['sel_cl']) ))
                {
                for ($a=0; $a < count ($_SESSION['sel_cl'])  ; $a++)
                    {
                    $groupe=$_SESSION['sel_cl'][$a];
                    $rq ="SELECT count(*)  FROM devoir WHERE date = '{$_POST['data']}' AND classe= '$groupe' AND creneau+ dur\351e-1 >= '$loop' AND `creneau` <='$loop'"; 
                    $res = mysql_query($rq);
                    while ($row = mysql_fetch_array($res, MYSQL_NUM)) 
                        {
                        if ($row[0] == 2) 
                            {
                            $lezard="true";
                            $message.=$groupe.",";
                            }
                        }
                    }
                }
            } 				

            if ($lezard=="true") $duree="";

            } 
        else $duree= "";
        }
    else
    // Si aucune duree n'a ete saisie
    { 
    $duree= "";
    }
    //echo "duree=".$duree ;exit;
    // Creer la requete d'ecriture pour l'enregistrement des donnees
    if ((isset($_POST['enregistrer'])) && ($duree!="") && ($Sujet!=""))
        {
        $rq = "INSERT INTO devoir (date, creneau,login, matiere, sujet, classe, dur\351e ) 
        VALUES ( '{$_POST['data']}','{$_POST['creneau']}', '$login', '".utf8_decode($_POST['matiere'])."', '$Sujet',
        '{$_POST['classe']}', '$duree')";

        // lancer la requete
        $result = mysql_query($rq);
        // Si l'enregistrement est incorrect 
        if (!$result)  
            {                           
            echo "<p>Votre devoir n'a pas pu \352tre enregistr\351 \340 cause d'une erreur syst\350me".
            "<p></p>" . mysql_error() . "<p></p>";
            // refermer la connexion avec la base de donnees
            mysql_close();    
            exit();
            }
        else 
            {	
            //envoi d'un mail si les liste de difusion sont activees
            if ($_SESSION['liste']==1)
                {	
                // destinataire
                //recherche du groupe classe dans l'annuaire
                $classe_dest=$_POST['classe'];
                $grp_cl=search_groups("cn=".$_POST['classe']);
                if (count($grp_cl[0]==0)) $grp_cl=search_groups("cn=Classe_*".$classe_dest); 

                //on ferme la connexion lcs_db et on se reconnecte sur la bdd Cdt
                mysql_close();
                include("../Includes/config.inc.php");

                //mise en forme de la date du devoir
                if ($_SESSION['version']==">=432") 
                setlocale(LC_TIME,"french");
                else
                setlocale("LC_TIME","french");
                //on evalue le timestamp de la date du devoir	
                $dat_new=strToTime($_POST['data']);	
                //Le destinataire 

                if  (!mb_ereg("^Cours",$_POST['classe'])) 
                $mailTo = $grp_cl[0]["cn"];
                else $mailTo = $_POST['classe'];
                //Le sujet
                $mailSubject = "Nouveau devoir";
                //Le message
                $mailBody = " CECI EST UN MESSAGE AUTOMATIQUE, MERCI DE NE PAS REPONDRE.  \n \n Un devoir de "
                . utf8_decode($_POST['matiere'])." a \351t\351 programm\351 le ".  strftime("%A %d %B %Y",$dat_new);
                //l'expediteur
                $mailHeaders = "From: Cahier\ de\ textes\n";
                //envoi du mail
                if ($classe_dest!="") mail($mailTo, $mailSubject, $mailBody, $mailHeaders);
                }
            //enregistrement dans le travail a faire
            $Dev= "Pr&#233;parer le Devoir surveill&#233; : ".$Sujet;
            $date_c=date("Ymd");
            $rq = "INSERT INTO cahiertxt (id_auteur,login,date,afaire,datafaire ) 
            VALUES ( '{$_POST['numrubri']}','{$_SESSION['login']}', '$date_c',  '$Dev', '{$_POST['data']}')";
            // lancer la requete
            $result = mysql_query($rq);
            if (!$result)  
                {                           
                echo "<p>Votre devoir n'a pas pu \352tre enregistr\351 \340 cause d'une erreur syst\350me".
                "<p></p>" . mysql_error() . "<p></p>";
                // refermer la connexion avec la base de donnees
                mysql_close();    
                exit();
                }
            }

        //traitement de devoirs multiples
        if ((isset($_SESSION['sel_cl']) ))
            {
            for ($a=0; $a < count ($_SESSION['sel_cl'])  ; $a++)
                {
                $gr=explode('#',$_SESSION['sel_cl'][$a]);
                $groupe=$gr[0];
                $idr=$gr[1];
                $rq = "INSERT INTO devoir (date, creneau,login, matiere, sujet, classe, dur\351e ) 
                VALUES ( '{$_POST['data']}','{$_POST['creneau']}', '$login', '".utf8_decode($_POST['matiere'])."',
                '$Sujet', '$groupe', '$duree')";

                // lancer la requete
                $result = mysql_query($rq);
                // Si l'enregistrement est incorrect 
                if (!$result)  
                    {                           
                    echo "<p>Votre devoir n'a pas pu \352tre enregistr\351 \340 cause d'une erreur syst\350me".
                    "<p></p>" . mysql_error() . "<p></p>";
                    // refermer la connexion avec la base de donnees
                    mysql_close();    
                    exit();
                    }
                else 
                    {
                    if (!mb_ereg("^Cours",$_POST['classe'])) 
                        {
                        //enregistrement dans le travail a faire pour les autres classes
                        $Dev= "Pr&#233;parer le Devoir surveill&#233; : ".$Sujet ;
                        $date_c=date("Ymd");
                        $rq = "INSERT INTO cahiertxt (id_auteur,login,date,afaire,datafaire ) 
                        VALUES ( '$idr','{$_SESSION['login']}', '$date_c',  '$Dev', '{$_POST['data']}')";
                        // lancer la requete
                        $result = mysql_query($rq);
                        if (!$result)  
                            {                           
                            echo "<p>Votre devoir n'a pas pu \352tre enregistr\351 \340 cause d'une erreur syst\350me".
                            "<p></p>" . mysql_error() . "<p></p>";
                            // refermer la connexion avec la base de donnees
                            mysql_close();    
                            exit();
                            }
                        }
                    //modif			
                    //envoi d'un mail si les liste de difusion sont activees
                    if (($_SESSION['liste']==1) && (!mb_ereg("^Cours",$_POST['classe']))) 
                        {	
                        // destinataire
                        //recherche du groupe classe dans l'annuaire
                        $classe_dest="";
                        $classe_courte=explode('_',$groupe);
                        $classe_dest=$classe_courte[count($classe_courte)-1];
                        $grp_cl=search_groups("cn=".$groupe);
                        if (count($grp_cl[0]==0)) $grp_cl=search_groups("cn=Classe_*".$classe_dest); 
                        //on ferme la connexion lcs_db et on se reconnecte sur la bdd Cdt
                        mysql_close();
                        include("../Includes/config.inc.php");

                        //mise en forme de la date du devoir
                        if ($_SESSION['version']==">=432") 
                        setlocale(LC_TIME,"french");
                        else
                        setlocale("LC_TIME","french");
                        //on evalue le timestamp de la date du devoir	
                        $dat_new=strToTime($_POST['data']);	
                        //Le destinataire 
                        $mailTo = $grp_cl[0]["cn"];
                        //Le sujet
                        $mailSubject = "Nouveau devoir";
                        //Le message
                        $mailBody = " CECI EST UN MESSAGE AUTOMATIQUE, MERCI DE NE PAS REPONDRE.  \n \n Un devoir de "
                        .utf8_decode($_POST['matiere'])." a \351t\351 programm\351 le ".  strftime("%A %d %B %Y",$dat_new);
                        //l'expediteur
                        $mailHeaders = "From: Cahier\ de\ textes\n";
                        //envoi du mail
                        if ($classe_dest!="") mail($mailTo, $mailSubject, $mailBody, $mailHeaders);
                        }
                    }
                }
            }
        //fin devoirs multiples  
        unset ($_SESSION['sel_cl']);
        }
    }

//Suppression d'un devoir apres confirmation
if (isset($_GET['delrub']) && isset($_GET['numd'])  && $_GET['TA']==md5($_SESSION['RT'].htmlentities($_SERVER['PHP_SELF']))) 
    {
    $action=$_GET['delrub'];
    $cible=$_GET['numd'];

    //le devoir existe-t-il ?
    $rq = "SELECT id_ds,login,date,matiere,creneau,sujet,dur\351e,classe FROM devoir WHERE id_ds='$cible' and login='$login' ";
    // lancer la requete
    $result = @mysql_query ($rq) or die (mysql_error());
    $nb = mysql_num_rows($result);
    while ($row = mysql_fetch_array($result, MYSQL_NUM)) 
        {
        $dat_supp=$row[2];
        $mat_supp=$row[3];
        $cren_supp=$row[4];
        $suj_supp=addslashes($row[5]);
        $dur_supp=$row[6];
        $cla_supp=$row[7];
        }
    //effacement de l'enregistrement
    if (($nb==1) &&($action==1245))
            {
            if (!mb_ereg("^Cours",$cla_supp))	
            $rq = "DELETE  FROM devoir WHERE id_ds='$cible' and login='$login' LIMIT 1";
            else
            $rq = "DELETE  FROM devoir WHERE  login='$login' and date='$dat_supp' and matiere='$mat_supp' and creneau='$cren_supp' and 
            sujet='$suj_supp' and dur\351e= '$dur_supp'";

            $result2 = @mysql_query ($rq) or die (mysql_error());
            //envoi d'un mail
            if ($_SESSION['liste']==1)
                {	
                //recherche du groupe classe dans l'annuaire
                $classe_dest="";
                $classe_courte=explode('_',$_GET['klasse']);
                $classe_dest=$classe_courte[count($classe_courte)-1];
                //include "$BASEDIR/lcs/includes/headerauth.inc.php";
                //include "$BASEDIR/Annu/includes/ldap.inc.php";
                $grp_cl=search_groups("cn=Classe_*".$classe_dest);
                $dest=$grp_cl[0]["cn"];
                mysql_close();
                include("../Includes/config.inc.php");	
                //mise en forme de la date du devoir
                if ($_SESSION['version']==">=432") setlocale(LC_TIME,"french");
                else setlocale("LC_TIME","french");	
                $dat_new=strToTime($dat_supp);	
                //destinataire
                $mailTo = $grp_cl[0]["cn"];
                //Le sujet
                $mailSubject = "Devoir supprim\351";
                //Le  message
                $mailBody = " CECI EST UN MESSAGE AUTOMATIQUE, MERCI DE NE PAS REPONDRE.  \n \n Le devoir de "
                .$mat_supp." pr\351vu le  " .strftime("%A %d %B %Y",$dat_new)." a \351t\351 supprim\351  ";
                //l'expediteur
                $mailHeaders = "From: Cahier\ de\ textes\n";
                //envoi mail
                if ($classe_dest!="") mail($mailTo, $mailSubject, $mailBody, $mailHeaders);
                }
            //suppression de l'enregistrement dans le cdt
            $suj_dev= "Pr&#233;parer le Devoir surveill&#233; : ".$suj_supp;			
            $rq = "DELETE  FROM cahiertxt WHERE id_auteur='{$_GET['numrubr']};' and  login='{$_SESSION['login']}' and afaire='$suj_dev' 
            and datafaire ='$dat_supp' LIMIT 1";
            $result2 = @mysql_query ($rq) or die (mysql_error());	
            }
    }
//fin de suppression d'un devoir

//memorisation des parametres POST classe  matiere et numero de rubrique renvoyes par le formulaire
if (isset($_POST['classe'])) $clas= $_POST['classe'];
if (isset($_POST['matiere'])) $mati= $_POST['matiere'];
if (isset($_POST['numrubri'])) $numrub= $_POST['numrubri'];

//recherche de la matiere et la classe de la rubrique active du cahier de textes
if (isset($_GET['rubrique']))
    { 
    $rq = "SELECT classe,matiere FROM onglets
    WHERE id_prof='{$_GET['rubrique']}'  ";
    // lancer la requete
    $result = @mysql_query ($rq) or die (mysql_error());
    // Combien y a-t-il d'enregistrements ?
    $nb = mysql_num_rows($result);  
    //on recupere les donnees
    while ($enrg = mysql_fetch_array($result, MYSQL_NUM)) 
        {$clas=$enrg[0];//classe
        $mati=utf8_encode($enrg[1]);//matiere
        }
    $numrub=$_GET['rubrique'];
    }

//memorisation des parametres GET classe matiere et numero de rubrique renvoyes par le formulaire	
if (isset($_GET['ladate']))
    {
    $clas=$_GET['klasse'];
    $mati=$_GET['matiere'];
    $numrub=$_GET['numrubr'];
    }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html  xmlns="http://www.w3.org/1999/xhtml" >
<head>
<title>Cahier de textes num&eacute;rique</title>
<meta name="author" content="Philippe LECLERC -TICE CAEN" />
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<link href="../style/style.css" rel="stylesheet" type="text/css" />
<!--[if IE]>
<link href="../style/style-ie.css"  rel="stylesheet" type="text/css"/>
<![endif]-->
</head>
<body>
<div class="plan-titre">Planning des Devoirs Surveill&#233;s de <?php echo $clas;?></div>
<?
//Modif groupes
//si la classe est un groupe
if (mb_ereg("^Cours",$clas)) 
    {
    $ct1cours=true;
    $groupe=$clas;
    $uids = search_uids ("(cn=".$groupe.")", "half");
    $liste_classe=array();
    $i=0;
    for ($loup=0; $loup < count($uids); $loup++)
        {
        $logun= $uids[$loup]["uid"];
        if (is_eleve($logun)) 
            {
            $groups=people_get_classe ($logun);
            if (count($groups))
                {
                for($n=0; $n<count($classe); $n++)
                    { 
                    if ((mb_ereg("(_$classe[$n])$",$groups)) || ($classe[$n]==$groups))
                        {
                        if (!in_array($classe[$n], $liste_classe)) 
                            {	
                            $liste_classe[$i]=$classe[$n];
                            $i++;
                            }
                        break;
                        }
                    }
                }
            }
        }
    //initialisation des variables de session
    unset ($_SESSION['sel_cl']);

    //memorisation des donnees dans des variables de session		
    $_SESSION['sel_cl']=$liste_classe;
   }
	
//eom	
//Affichage conditionnel d'un message d'erreur
if ((isset($_POST['enregistrer'])) && ($duree==""))
 echo '<h6 class="erreur">'.$message. ' : Dur&#233;e erron&#233;e</h6>';

//Affichage du formulaire de saisie du sujet du devoir et de la duree
if (isset($_GET['ladate']) &&(isset($_GET['inser'])))
    {
    echo '<form id="form-ajt-dev" action="'.$_SERVER["PHP_SELF"].'" method="post">
    <div><script type="text/javascript" src="../Includes/barre_java.js"></script>
    <input name="JourCourant" type="hidden"  value="'. $_GET["jc"].'"/>
    <input name="classe" type="hidden" value="'. $_GET['klasse'].'"/>
    <input name="matiere" type="hidden"  value="'. $_GET['matiere'].'"/>
    <input name="creneau" type="hidden"  value="'. $_GET['creneau'].'"/>
    <input name="login" type="hidden" value="'. $login.'"/>
    <input name="data" type="hidden" value="'.$_GET["ladate"].'"/>
    <input name="numrubri" type="hidden" value="'.$numrub.'"/>
    <input name="TA" type="hidden"  value="'. md5($_SESSION['RT'].htmlentities($_SERVER['PHP_SELF'])).'"/>
    <p>Intitul&#233; du devoir :
    <input class="text" type="text" name="sujet" size="28" maxlength="28" />Dur&#233;e :
    <input class="text" type="text" name="duree" size="1" maxlength="1"/>heure(s)&nbsp;';
    if ($ct1cours) 
        {
        echo '</p><p> Ce devoir sera diffus&#233; en  ';
        for ($j=0;$j<count($liste_classe);$j++)
            {
            echo $liste_classe[$j]." ";	
            }
        }
    else
    echo '<a id="bt-select" href="diffuse_devoirs.php?rubrique='.$numrub.'" onclick="diffusedev_popup('.$numrub.'); return false" ></a>';
    echo'	<input class="bt" type="submit" name="enregistrer" value=""/></p>
    </div></form>';
    }
//Determination du jour courant
if (isset($_POST['JourCourant'])) 
    {
    $JourCourant = $_POST['JourCourant'];

    //mois suivant
    if (isset($_POST['msuiv'])) 
            {
            $Lundi=DebutSemaine($JourCourant + 28 * 86400);
            } 
    //semaine suivante
    elseif (isset($_POST['suiv'])) 
            {
            $Lundi=DebutSemaine($JourCourant + 7 * 86400);
            } 
            //semaine precedente
    elseif (isset($_POST['mprec']))
            {			
            $Lundi=DebutSemaine($JourCourant - 28 * 86400);
            }		
            elseif (isset($_POST['prec']))
            {
                    //tableau hebdomadaire commençant au Lundi
            $Lundi=DebutSemaine($JourCourant - 7 * 86400);
            } 
            else 
            {
            $Lundi=DebutSemaine($JourCourant);
            }
    }
else 
{
        $Lundi=DebutSemaine();
        }
//memorisation de la date courante
if (isset($_GET['ladate'])) {$Lundi=$_GET['jc'];}

//fin de la semaine
$Samedi = $Lundi + 432000; //5 * 86400 
//Date du debut de la semaine courante
$datdebut=date('Ymd',$Lundi);
//Date de la fin de la semaine courante
$datfin=date('Ymd',$Samedi);
//Recherche des devoirs programmes pour la semaine courante
$rq = "SELECT id_ds, DATE_FORMAT(date,'%d'),DATE_FORMAT(date,'%m'),DATE_FORMAT(date,'%Y'), creneau, matiere, sujet,  login, dur\351e FROM devoir 
WHERE date>='$datdebut' AND date<='$datfin'  AND classe= '$clas' ORDER BY date asc, creneau asc";
$res = mysql_query($rq);
$nb = mysql_num_rows($res);
$num=$plan=$mat=$suj=$log=$dur=array();
//si des devoirs ont ete programmes
if ($nb>0) 
    {
    //pour chaque devoir programme
    while ($row = mysql_fetch_array($res, MYSQL_NUM)) 
        {
        //determination du timestamp du jour du devoir
        $tsmp=mkTime(8,0,0,$row[2],$row[1],$row[3]);
        // on parcourt les jours de la semaine
        for ($j=0; $j<=5; $j++)
            {
            $jour = $Lundi + $j * 86400;
            // on parcourt les heures de la  journee
            for ($h=0; $h<=9; $h++)
                {
                //si un devoir est programme pour ce creneau
                if (($jour==$tsmp) &&($row[4] == $h))
                    {
                    $col = 0;
                    if ((isset($plan[$j][$h][$col]))) $col = 1 ;
                    //on memorise les donnees dans des tableaux a 3 dimensions
                    $num[$j][$h][$col] = $row[0];//numero
                    $plan[$j][$h] [$col]= "R";//on pose une marque (Reserve) pour le creneau
                    $mat[$j][$h][$col] = utf8_encode($row[5]);//matiere
                    $Suj_=str_replace ( '"','&quot;',$row[6]);
                    if (get_magic_quotes_gpc()) $suj[$j][$h][$col] = utf8_encode(stripslashes($Suj_));//sujet
                    else $suj[$j][$h][$col] = utf8_encode($Suj_);
                    $log[$j][$h][$col] = $row[7];//login de l'auteur
                    $dur[$j][$h][$col] = $row[8];//duree
                    //on marque les autres creneaux utilises par le devoir
                    for ($x=1; $x<$row[8]; $x++) {$plan[$j][$h+$x][$col] = "O";}
                    }
                $col = 0;
                }
            }
        }
    }
	
?>
<div id="cfg-container">
<div id="cfg-contenu">
<!-- affichage du calendrier hebdomadaire -->
<form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="post"  id="planning">
<div><input name="TA" type="hidden"  value="<?php echo md5($_SESSION['RT'].htmlentities($_SERVER['PHP_SELF'])); ?>" />
<!-- Affichage des boutons -->	
<input name="JourCourant" type="hidden" id="JourCourant" value="<?php echo $Lundi;?>" />
<input name="classe" type="hidden" id="classe" value="<?php echo $clas;?>" />
<input name="matiere" type="hidden" id="matiere" value="<?php echo $mati;?>" />
<input name="numrubri" type="hidden" id="numrubri" value="<?php echo $numrub;?>" />
<table id="btn-plan-cdt">
<tr>
<td ><input type="submit" name="mprec" value="&lt;&lt;" title="Mois pr&#233;c&#233;dent" class="bt50" /></td>
<td><input type="submit" name="prec" value="&lt;" title="Semaine pr&#233;c&#233;dente" class="bt50" /></td>
<td><input type="submit" name="quit" id="quit" value="" class="bt-exit" /></td>
<td><input type="submit" name="suiv" value="&gt;" title="Semaine suivante" class="bt50" /></td>
<td><input type="submit" name="msuiv" value="&gt;&gt;" title="Mois suivant" class="bt50" /></td>
</tr>
</table>
<table id="plan-cdt" cellpadding="1" cellspacing="2">
<thead> 
<tr><th><img src="../images/livre.gif" alt="+" /> </th>
<?php
// Affichage des jours et dates de la semaine en haut du tableau"j-M-Y",
for ($i=0; $i<=5; $i++) 
    {
    $TS = $Lundi+$i*86400;
    echo '<td colspan="2">'.LeJour($TS)."<br />".datefr($TS)."</td>\n";
    }
?>
</tr></thead>
<tbody>
<?php
$horaire = array("M1<br />","M2<br />","M3<br />","M4<br />","M5<br />","S1<br />","S2<br />","S3<br />","S4<br />","S5<br />");
for ($h=0; $h<=9; $h++) 
    {
    if (in_array(substr($horaire[$h],0,2), $cren_off)) 
        {
        echo '<tr><td class="mi-jour" colspan="13"></td></tr>';
        continue;
        }
    //Affichage de la designation des creneaux horaires
    echo "<tr><th>".$horaire[$h].date('G:i',$deb[$h])."-".date('G:i',$fin[$h])."</th>\n";
    //Affichage du contenu des creneaux horaires 
    for ($j=0; $j<=5; $j++) 
        {//b
        $date[$j]=date('Ymd',$Lundi + $j * 86400);
        if (($Lundi + $j * 86400 + $dif[$h] < mktime()) )//Debut du creneau"h"  passe 
            {//c
            //si c'est du passe, on l'ecrit !!

            //sauf si on est dans le premier creneau d'un devoir en cours alors $plan[$j][$h]=="R"
            if ((isset($plan[$j][$h][0])) )
                {//d
                //et si on est pendant un devoir
                if (($Lundi + $j * 86400 + ($dif[$h+$dur[$j][$h][0]]) >= mktime()) && ($plan[$j][$h][0]=="R"))
                    {//e

                    // on ecrit devoir en cours
                    echo '<td  rowspan="'.$dur[$j][$h][0].'" class="encours">Devoir en cours </td>'."\n";
                    //on marque les creneaux suivants pour ne pas les marques "passe" avant la fin du devoir
                    for ($x=1; $x<$dur[$j][$h][0]; $x++) {$plan[$j][$h+$x][0] = "EC"; }

                    // s'il y a un devoir simultane 
                    if ((isset($plan[$j][$h][1])) && (isset($dur[$j][$h][1]))) 
                        {//f
                        //et si on est pendant un devoir (cas "R" "R")
                        if (($Lundi + $j * 86400 + ($dif[$h+$dur[$j][$h][1]]) >= mktime()) && ($plan[$j][$h][1]=="R"))
                            {//g
                            // on ecrit devoir en cours
                            echo '<td  rowspan="'.$dur[$j][$h][1].'" class="encours">Devoir en cours </td>'."\n";
                            //on marque les creneaux suivants pour ne pas les marques "passe" avant la fin du devoir 
                            for ($x=1; $x<$dur[$j][$h][1]; $x++) {$plan[$j][$h+$x][1] = "EC"; }
                            }//g
                        }//f

                    //cas "R" "-" ou "R" "O"	
                    if (!isset($plan[$j][$h][1]))  
                        {//h
                        echo '<td class="passe">Pass&#233;</td>' ."\n";
                        }//h
                    }	//e //fin du cas "R" pendant un devoir
                //cas d'une cellule "EC"	

                //si on est pas dans le premier creneau du devoir
                if ($plan[$j][$h][0]=="EC")	
                    {//i
                    //cas "EC"-"O" "EC"-"-" "EC"-"Rp"
                    if  (($plan[$j][$h][1]=="O" || (!isset($plan[$j][$h][1]))) ||(($Lundi + $j * 86400 + ($dif[$h+$dur[$j][$h][1]]) < mktime()) && ($plan[$j][$h][1]=="R")))
                     echo '<td   class="passe">'.$plan[$j][$h][1].'Pass&#233;</td>' ."\n";
                     //cas "EC"-"R"
                    if  (($Lundi + $j * 86400 + ($dif[$h+$dur[$j][$h][1]]) >= mktime()) && ($plan[$j][$h][1]=="R"))
                        {//gg
                        // on ecrit devoir en cours
                        echo '<td  rowspan="'.$dur[$j][$h][1].'" class="encours">Devoir en cours </td>'."\n";
                        //on marque les creneaux suivants pour ne pas les marques "passe" avant la fin du devoir
                        for ($x=1; $x<$dur[$j][$h][1]; $x++) {$plan[$j][$h+$x][1] = "EC";}
                        }//gg
                    }//i
                //creneau "R" passe 
                if ((($Lundi + $j * 86400 + ($dif[$h+$dur[$j][$h][0]]) < mktime()) && ($plan[$j][$h][0]=="R")) )
                    {//k
                    //"Rp"-"-""
                    if (!isset($plan[$j][$h][1]) ) 
                        {
                        echo '<td   rowspan="'.$dur[$j][$h][0].'" class="devoir">'.$mat[$j][$h][0].'</td>'."\n";
                        for ($x=1; $x<$dur[$j][$h][0]; $x++) {$plan[$j][$h+$x][0] = "O";}	
                        echo '<td   class="passe">Pass&#233;</td>' ."\n";															
                        }
                    //"Rp"- "R"
                    if  (($Lundi + $j * 86400 + ($dif[$h+$dur[$j][$h][1]]) >= mktime()) && ($plan[$j][$h][1]=="R"))
                        {//
                        // on ecrit devoir1 passe,devoir 2 encours 
                        echo '<td   rowspan="'.$dur[$j][$h][0].'" class="devoir">'.$mat[$j][$h][0].'</td>'."\n";
                        for ($x=1; $x<$dur[$j][$h][0]; $x++) {$plan[$j][$h+$x][0] = "O";}
                        echo '<td  rowspan="'.$dur[$j][$h][1].'" class="encours">Devoir en cours </td>'."\n";
                        //on marque les creneaux suivants pour ne pas les marques "passe" avant la fin du devoir
                        for ($x=1; $x<$dur[$j][$h][1]; $x++) {$plan[$j][$h+$x][1] = "EC";}								
                        }//
                    //"Rp"-"O"
                    if  (( isset($plan[$j][$h][1]) && $plan[$j][$h][1]=="O"))
                        {
                        echo '<td   rowspan="'.$dur[$j][$h][0].'" class="devoir">'.$mat[$j][$h][0].'</td>'."\n";
                        for ($x=1; $x<$dur[$j][$h][0]; $x++) {$plan[$j][$h+$x][0] = "O";}
                        }
                    if  (($Lundi + $j * 86400 + ($dif[$h+$dur[$j][$h][1]]) < mktime()) && ($plan[$j][$h][1]=="R"))
                        {//gg
                        //"Rp"-"Rp"
                        // on ecrit devoir2 passe
                        echo '<td   rowspan="'.$dur[$j][$h][0].'" class="devoir">'.$mat[$j][$h][0].'</td>'."\n";
                        for ($x=1; $x<$dur[$j][$h][0]; $x++) {$plan[$j][$h+$x][0] = "O";}
                        echo '<td   rowspan="'.$dur[$j][$h][1].'" class="devoir">'.$mat[$j][$h][1].'</td>'."\n";
                        for ($x=1; $x<$dur[$j][$h][1]; $x++) {$plan[$j][$h+$x][1] = "O";}
                        }//gg
                    }//k

                //creneau O passe 
                if ((($Lundi + $j * 86400 + ($dif[$h+$dur[$j][$h][0]]) < mktime()) && ($plan[$j][$h][0]=="O")) )
                    {
                    //O -
                    if (!isset($plan[$j][$h][1]) ) 
                        {
                        echo '<td   class="passe">Pass&#233;</td>' ."\n";		
                        }
                    //O-R 
                    if  (($Lundi + $j * 86400 + ($dif[$h+$dur[$j][$h][1]]) >= mktime()) && ($plan[$j][$h][1]=="R"))
                        {//
                        echo '<td  rowspan="'.$dur[$j][$h][1].'" class="encours">Devoir en cours </td>'."\n";
                        //on marque les creneaux suivants pour ne pas les marques "passe" avant la fin du devoir
                        for ($x=1; $x<$dur[$j][$h][1]; $x++) {$plan[$j][$h+$x][1] = "EC";}	
                        }
                    //O-Rp 
                    if  (($Lundi + $j * 86400 + ($dif[$h+$dur[$j][$h][1]]) < mktime()) && ($plan[$j][$h][1]=="R"))
                        {
                        echo '<td   rowspan="'.$dur[$j][$h][1].'" class="devoir">'.$mat[$j][$h][1].'</td>'."\n";
                        for ($x=1; $x<$dur[$j][$h][1]; $x++) {$plan[$j][$h+$x][1] = "O";}
                        }
                    }
                }//d
            else
            //sinon  c'est un creneau vide  
                {
                //"-"-"O" + "-"-"-"+
                if  ( !isset($plan[$j][$h][1]) )
                echo '<td colspan="2"  class="passe">Pass&#233;</td>'."\n";
                //"-"-"R"
                else echo '<td   class="passe">Pass&#233;</td>' ."\n";
                if  (($Lundi + $j * 86400 + ($dif[$h+$dur[$j][$h][1]]) >= mktime()) && ($plan[$j][$h][1]=="R"))
                    {//gg
                    // on ecrit devoir en cours
                    echo '<td  rowspan="'.$dur[$j][$h][1].'" class="encours">Devoir en cours </td>'."\n";
                    //on marque les creneaux suivants pour ne pas les marques "passe" avant la fin du devoir
                    for ($x=1; $x<$dur[$j][$h][1]; $x++) {$plan[$j][$h+$x][1] = "EC";}
                    }//gg

                //"-"-"Rp"
                if  (($Lundi + $j * 86400 + ($dif[$h+$dur[$j][$h][1]]) < mktime()) && ($plan[$j][$h][1]=="R"))
                    {//gg
                    //"
                    // on ecrit devoir2 passe
                    echo '<td   class="passe">Pass&#233;</td>'."\n";
                    echo '<td   rowspan="'.$dur[$j][$h][1].'" class="devoir">'.$mat[$j][$h][1].'</td>'."\n";
                    for ($x=1; $x<$dur[$j][$h][1]; $x++) {$plan[$j][$h+$x][1] = "Op";}
                    }//gg
                }
            }//c
        //ici ce nest plus du passe

        //si ce n'est pas du passe et si rien n'est programme on affiche un lien permettant de programmer un devoir		

        //si un devoir est programme, on remplit les cellules correspondant a la duree, on affiche la matiere et le sujet
        //plus un lien pour supprimer le devoir
        elseif ($plan[$j][$h][0]=="R") 
            {//m
            if (($Lundi + $j * 86400 + $dif[$h]) > mktime())  //MODIF
                {//n
                echo '<td  rowspan="'.$dur[$j][$h][0].'" class="reserve">'.substr($mat[$j][$h][0],0,10).
                '<br /><img alt="aide"   src="../images/planifier-cdt-aide.png"  title ="'.$suj[$j][$h][0].' " />
                <a href="'.$_SERVER['PHP_SELF'].'?ladate='.$date[$j].
                '&amp;jc='.$Lundi.'&amp;klasse='.$clas.'&amp;creneau='.$h.'&amp;matiere='.$mati.'&amp;suppr=yes&amp;numdev='
                .$num[$j][$h][0].'&amp;numrubr='.$numrub.'" title="Supprimer ce devoir">'.
                '<img alt="aide"    src="../images/planifier-cdt-supp.png"   /> </a>'."</td>\n";
                if (!isset($plan[$j][$h][1])) 
                    {//o
                    echo '<td   class="libre"><a href="'.$_SERVER['PHP_SELF'].'?ladate='.$date[$j]
                    .'&amp;inser=yes'.'&amp;jc='.$Lundi.'&amp;klasse='.$clas.'&amp;creneau='.$h.'&amp;matiere='.$mati
                    .'&amp;numrubr='.$numrub.'" title="Planifier un devoir"><img src="../images/planifier-cdt-plus.png" alt="-¤-" /></a></td>'."\n";
                    }//o
                elseif ($plan[$j][$h][1]=="R")  
                    {//p
                    echo '<td  rowspan="'.$dur[$j][$h][1].'" class="reserve">'.substr($mat[$j][$h][1],0,10).
                    '<br /><img alt="aide"   src="../images/planifier-cdt-aide.png"  title ="'.$suj[$j][$h][1].'" /> 
                    <a href="'.$_SERVER['PHP_SELF'].'?ladate='.$date[$j].
                    '&amp;jc='.$Lundi.'&amp;klasse='.$clas.'&amp;creneau='.$h.'&amp;matiere='.$mati.'&amp;suppr=yes&amp;numdev='
                    .$num[$j][$h][1].'&amp;numrubr='.$numrub.'" title="Supprimer ce devoir">'.
                    '<img alt="aide" src="../images/planifier-cdt-supp.png" /> </a>'."</td>\n";
                    }//p
                } //n
            }//m
        //cas des cellules "O"
        elseif (($plan[$j][$h][0]=="O") || ($plan[$j][$h][0]=="EC") )
            {//q
            if (($Lundi + $j * 86400 + $dif[$h]) > mktime())  //MODIF
                {//r
                if (!isset($plan[$j][$h][1])) 
                    {//s
                    echo '<td   class="libre"><a href="'.$_SERVER['PHP_SELF'].'?ladate='.$date[$j]
                    .'&amp;inser=yes'.'&amp;jc='.$Lundi.'&amp;klasse='.$clas.'&amp;creneau='.$h.'&amp;matiere='.$mati
                    .'&amp;numrubr='.$numrub.'" title="Planifier un devoir"><img src="../images/planifier-cdt-plus.png" alt="-¤-" /></a></td>'."\n";
                    }//s
                elseif ($plan[$j][$h][1]=="R")   
                    {//t
                    echo '<td  rowspan="'.$dur[$j][$h][1].'" class="reserve">'.substr($mat[$j][$h][1],0,10).
                    '<br /><img alt="aide"   src="../images/planifier-cdt-aide.png"  title ="'.$suj[$j][$h][1].'" /> 
                    <a href="'.$_SERVER['PHP_SELF'].'?ladate='.$date[$j].
                    '&amp;jc='.$Lundi.'&amp;klasse='.$clas.'&amp;creneau='.$h.'&amp;matiere='.$mati.'&amp;suppr=yes&amp;numdev='
                    .$num[$j][$h][1].'&amp;numrubr='.$numrub.'" title="Supprimer ce devoir">'.
                    '<img alt="aide"   src="../images/planifier-cdt-supp.png" /> </a>'."</td>\n";
                    }//t
                }//r
            } //q
        elseif (!isset($plan[$j][$h][0])) 
            {//l
            if (!isset($plan[$j][$h][1]))
            echo '<td colspan="2"  class="libre"><a href="'.$_SERVER['PHP_SELF'].'?ladate='.$date[$j]
            .'&amp;inser=yes'.'&amp;jc='.$Lundi.'&amp;klasse='.$clas.'&amp;creneau='.$h.'&amp;matiere='.$mati
            .'&amp;numrubr='.$numrub.'" title="Planifier un devoir"><img src="../images/planifier-cdt-plus.png" alt="-¤-" /></a></td>'."\n";
            else
            echo '<td class="libre"><a href="'.$_SERVER['PHP_SELF'].'?ladate='.$date[$j]
            .'&amp;inser=yes'.'&amp;jc='.$Lundi.'&amp;klasse='.$clas.'&amp;creneau='.$h.'&amp;matiere='.$mati
            .'&amp;numrubr='.$numrub.'" title="Planifier un devoir"><img src="../images/planifier-cdt-plus.png" alt="-¤-" /></a></td>'."\n";
            }//l

        //cellules de separation a la mi-journee			
        if (($h==4)&&($j==5)) echo '</tr><tr><td class="mi-jour" colspan="13"></td>';
        }	//b
    echo "</tr>\n";
    }//a
?>
</tbody>
</table>
<h5>Pour planifier un nouveau devoir, cliquer sur le cr&#233;neau horaire correspondant au d&#233;but du devoir</h5>
</div></form>
<?php
//Affichage d'une boite de confirmation d'effacement
if (isset($_GET["suppr"]))
    {
    echo "<script type='text/javascript'>";
    echo "if (confirm('Confirmer la suppression de ce devoir (Vous ne pouvez supprimer que vos devoirs !!)')){";
    echo ' location.href = "';echo htmlentities($_SERVER['PHP_SELF']);echo'" + "?numd=" +'." '"; echo $_GET["numdev"] ;echo "'"
    .' + "&delrub=" +'."1245".' + "&ladate=" +'."'";echo $date[0];echo "'".'+ "&jc="+'."'".$Lundi."'".'
    + "&klasse="+'."'".$clas."'".'+ "&numrubr="+'."'".$numrub."'".'+ "&matiere="+'."'".$mati."'".' + "&TA=" +'." '". md5($_SESSION['RT'].htmlentities($_SERVER['PHP_SELF']))."'".' ;}</script>';
    }
  ?>
</div> <!-- fin du contenu -->
</div> <!-- fin du container-->
<?php
include ('../Includes/pied.inc'); 
?>
</body>
</html> 
