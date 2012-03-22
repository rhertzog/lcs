<?php
/* ==================================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 2.4 du 22/03/2012
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script de configuration du cahier de textes -
			_-=-_
  "Valid XHTML 1.0 Strict"
   =================================================== */
session_name("Cdt_Lcs");
@session_start(); 
include "../Includes/check.php";
if (!check()) exit;
//error_reporting(0);
//si la page est appelee par un utilisateur non identifie
if (!isset($_SESSION['login']) )exit;

//si la page est appelee par un utilisateur non admin
elseif ($_SESSION['login']!="admin") exit;

//fichiers necessaires a l'exploitation de l'API
include "../Includes/basedir.inc.php";
include "$BASEDIR/lcs/includes/headerauth.inc.php";
include "$BASEDIR/Annu/includes/ldap.inc.php";
include "$BASEDIR/Annu/includes/ihm.inc.php"; 
$cren_off=array();
if (isset($_POST['Sauver']))
    {	
    header ("location: ./export.php");
    exit;
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
<h1 class='title'>Configuration du cahier de textes</h1>
<?php
/**************************************************
* positionnement du flag d'absence dans le fichier de conf *
***************************************************/
if (isset($_POST['res_nores']))
    {
    require_once "../Includes/config.inc.php";
    $com= 'mv /usr/share/lcs/Plugins/Cdt/Includes/config.inc.php /usr/share/lcs/Plugins/Cdt/Includes/config.inc.php.tmp && cat /usr/share/lcs/Plugins/Cdt/Includes/config.inc.php.tmp | sed -e "s/ABSENCE='.$FLAG_ABSENCE.'/ABSENCE='.$_POST['absence'].'/g" > /usr/share/lcs/Plugins/Cdt/Includes/config.inc.php && rm /usr/share/lcs/Plugins/Cdt/Includes/config.inc.php.tmp';
    exec($com,$rien,$retour);
    }
//le fichier de conf doit etre re-inclus apres une modif eventuelle	
include "../Includes/config.inc.php";

//changement du grain de sel 
if (isset($_POST['change_grain']))
    {
    $com= 'mv /usr/share/lcs/Plugins/Cdt/Includes/config.inc.php /usr/share/lcs/Plugins/Cdt/Includes/config.inc.php.tmp && 
     pass=`pwgen -1`  && cat /usr/share/lcs/Plugins/Cdt/Includes/config.inc.php.tmp | sed -e "s/^\$Grain.*/\$Grain=\"$pass\";/g" > /usr/share/lcs/Plugins/Cdt/Includes/config.inc.php && rm /usr/share/lcs/Plugins/Cdt/Includes/config.inc.php.tmp';
    exec($com,$rien,$retour);
    if ($retour == 0)  $cle="ok"; else $cle="ko";
    }

/***********************
*  Importation de l'annuaire *
*************************/

if (isset($_POST['Importer']))
    {
    //initialisation d'un tableau
    $data=array();
    $mess1="";
    //recherche des groupes classes
    $groups=search_groups('cn=classe*');
    if (count($groups))
        {    
        for ($loop=0; $loop < count($groups); $loop++)
            {
            //raccourci des noms pour le format court
            $short=explode("_",$groups[$loop]["cn"]);
            if ($_POST['size']=="court")
            $data[$loop]=$short[count($short)-1];//dernier "match"
            else $data[$loop]=$groups[$loop]["cn"];	//format long
            }
        }
    else  $mess1= "<h3 class='nook'> Erreur dans l'importation <br /></h3>";
    }
		
if (isset($_POST['Creer']))
    {		
    //traitement du fichier texte		
    if (!empty($_FILES["FileSelection3"]["name"]))
        {
        if (($_FILES["FileSelection3"]["size"]>0) && ($_FILES["FileSelection3"]["type"]=="text/plain"))
            {
            $nomFichier = $_FILES["FileSelection3"]["name"] ;
            $nomTemporaire = $_FILES["FileSelection3"]["tmp_name"] ;
            //chargement du fichier
            copy($nomTemporaire,"../Includes/".$nomFichier);
            //renommage du fichier
            rename("../Includes/".$nomFichier,"../Includes/flist.txt");
            //Ouverture du fichier		
            if($fichier_ok == fopen("../Includes/flist.txt", "r"))
                {
                $loop=0;
                //extraction des donnees
                while(!feof($fichier_ok))
                    {
                    $donnees_extraites = fgetcsv($fichier_ok, 2048);
                    for($n=0; $n<count($donnees_extraites); $n++)
                        {				
                        $data[$loop]=$donnees_extraites[$n];
                        $loop++;
                        }
                    }
                fclose($fichier_ok);
                }	
            //effacement du fichier
            unlink ("../Includes/flist.txt");

            //message
            if (count($data)>0) $mess1= "<h3 class='cok'>1. Le traitement du fichier texte s'est termin&#233; avec succ&#233;s."."<br /></h3>";
            else $mess1= "<h3 class='nook'> Erreur dans le traitement du fichier texte (Fichier erron&#233;)"."<br /></h3>";
            }
        else $mess1= "<h3 class='nook'> Erreur dans l'importation du fichier texte "."<br /></h3>";
        }
    }
	
//creation du fichier
if (isset($_POST['Creer']) || isset($_POST['Importer']))
    {
    $nom_fichier="../Includes/data.inc.php";
    $fichier=fopen($nom_fichier,"w");
    fputs($fichier, "<?php \n");
    for ($index=0;$index<count($data);$index++)
        {
        fputs($fichier,"\$classe[$index]=\"$data[$index]\";\n");
        }
    fputs($fichier, " ?>\n");
    fclose($fichier);
    }
	
//si clic sur Enregistrer	
if (isset($_POST['Enregistrer']))
    {
    $crenoff=$_POST['crenoff'];
    $nom_fichier="../Includes/creneau.inc.php";
    $fichier=fopen($nom_fichier,"w");
    fputs($fichier, "<?php \n");
    for ($index=0;$index<10;$index++)
        {
        fputs($fichier,"\$deb[$index]=".mkTime($_POST["hd".$index],$_POST["md".$index],0).
        ";\$fin[$index]=".mkTime($_POST["hf".$index],$_POST["mf".$index],0).
        ";\$dif[$index]=".(mkTime($_POST["hd".$index],$_POST["md".$index],0)- mkTime(8,0,0)).";\n");
        }

    $i=0;
    if (in_array("M4", $crenoff)) 
        {
        fputs($fichier,"\$cren_off[$i]=\"M4\";\n");$i++;
        fputs($fichier,"\$cren_off[$i]=\"M5\";\n");$i++;
        }
    elseif (in_array("M5", $crenoff)) 
        {
        fputs($fichier,"\$cren_off[$i]=\"M5\";\n");$i++;
        }
    if (in_array("S4", $crenoff)) 
        {
        fputs($fichier,"\$cren_off[$i]=\"S4\";\n");$i++;
        fputs($fichier,"\$cren_off[$i]=\"S5\";\n");$i++;
        }
    elseif (in_array("S5", $crenoff)) 
        {
        fputs($fichier,"\$cren_off[$i]=\"S5\";\n");
        }
    fputs($fichier, " ?>\n");
    fclose($fichier);
    }
	
/******************
* test de la coherence *
*******************/
	
if (isset($_POST['test']))
    {
    //recherche des groupes classes
    $groups=search_groups('cn=classe*');
    //Pour chaque classe, recherche d'une occurence unique dans l'annuaire
    include "../Includes/data.inc.php"; 
    //initialisation des variables
    $index=0;
    $error=array();
    //recherche d'occurence pour chaque classe
    for($n=0; $n<count($classe); $n++)
        {
        $occ=0;
        for ($loop=0; $loop < count($groups); $loop++)
            {
            if ((mb_ereg("(_$classe[$n])$",$groups[$loop]["cn"])) || ($classe[$n]==$groups[$loop]["cn"]))
            $occ++;
            }
        //si le nb d'occurence est <> de 1, -->probl�me	
        if ($occ!=1) 
            {
            $error[$index]=$classe[$n];
            $index++;
            }
        }
    if (count($error)==0)
        {
        echo "<script type='text/javascript'>
        <!--
        alert('La liste des classes est coherente avec l\'annuaire');
        // -->
        </script>";
        }
    else
        {
        echo "<script type='text/javascript'>";
        echo "alert('Incoherence pour : ";
        for($n=0; $n<count($error); $n++)
            {
            echo $error[$n].' ';
            }
        echo "')</script>";
        }	
    }
/**************	
*  gestion de la bd *
***************/
//si clic sur le bouton Archiver
if (isset($_POST['Archiver']))
    {	
    // Verifier $name_arch et la debarrasser de tout antislash et tags possibles
    if (strlen($_POST['name_arch']) > 0)
        { 
        $name_arch= addSlashes(strip_tags(stripslashes($_POST['name_arch'])));
        }
    else
        { 
        $name_arch= date("Y",mktime()-31536000)."_".date("Y");
        }
    //Verification d'une archive existante
    $exist=0;
    $TablesExist= mysql_query("show tables");
    while ($table=mysql_fetch_row($TablesExist))
    if (mb_ereg("$name_arch$",$table[0])) $exist=1;
    if ($exist==0)
        {
        // Creer la requete.
        $rq1 = "create table onglets$name_arch as select * from onglets";
        $rq2= "create table cahiertxt$name_arch as select * from cahiertxt";
        // lancer la requete
        $result1 = @mysql_query ($rq1) or die (mysql_error());
        $result2 = @mysql_query ($rq2) or die (mysql_error());
        if (!($result1&&$result2)) $mess2="<h3 class='nook'>  l'achive n'a pu &#234;tre cr&#233;&#233;e";
        }
    else
        {
        echo "<script type='text/javascript'>";
        echo" if (confirm('Remplacer l\'archive existante ? ')){";	        
        echo ' location.href = "';
        echo $_SERVER['PHP_SELF'];
        echo '"+ "?delarch=" + "yes" + "&TA=" + "'.md5($_SESSION['RT'].htmlentities($_SERVER['PHP_SELF'])).'"+ "&nom_arch=" + "'.$name_arch.'#bdd" ;} else {';
        echo ' location.href = "';echo $_SERVER['PHP_SELF'];echo'"  ;} </script> ';
        }
    }
	
//confirmation de remplacement de l'archive	
if (isset($_GET['delarch']) && $_GET['TA']==md5($_SESSION['RT'].htmlentities($_SERVER['PHP_SELF'])))
    {
    if ($_GET['delarch']=='yes')
        {
        // Creer la requete.
        $name_arch = $_GET['nom_arch'];
        $rq1 = "DROP TABLE IF EXISTS onglets$name_arch ";
        $rq2 = "DROP TABLE IF EXISTS cahiertxt$name_arch";
        $rq3 = "create table onglets$name_arch as select * from onglets";
        $rq4 = "create table cahiertxt$name_arch as select * from cahiertxt";
        // lancer la requete
        $result1 = @mysql_query ($rq1) or die (mysql_error());
        $result2 = @mysql_query ($rq2) or die (mysql_error());
        $result3 = @mysql_query ($rq3) or die (mysql_error());
        $result4= @mysql_query ($rq4) or die (mysql_error());
        if (!($result1&&$result2&&$result3&&$result4)) $mess2="<h3 class='nook'>  l'achive n'a pu &#234;tre cr&#233;&#233;e";
        }
    }
	
//initilisation des tables
if (isset($_POST['Vider']))
    {
    echo "<script type='text/javascript'>";
    echo " if (confirm('Confirmer l\'effacement du contenu des tables cahiertxt, absences, devoir, onglets,post-it eleves, et sequences? ')){";
    echo ' location.href = "';
    echo $_SERVER['PHP_SELF'].'#bdd';
    echo '"+ "?vidtab=" + "yes" + "&TA=" + "'.md5($_SESSION['RT'].htmlentities($_SERVER['PHP_SELF'])).'";} else {';
    echo ' location.href = "';echo $_SERVER['PHP_SELF'];echo'"   ;} </script> ';
    }
	
//confirmation d'initialisation des tables	
if (isset($_GET['vidtab']) && $_GET['TA']==md5($_SESSION['RT'].htmlentities($_SERVER['PHP_SELF'])))
    {
    if ($_GET['vidtab']=='yes')
        {
        // Creer la requete.
        $rq1 = "TRUNCATE TABLE onglets";
        $rq2 = "TRUNCATE TABLE cahiertxt";
        $rq3 = "TRUNCATE TABLE devoir";
        $rq4 = "TRUNCATE TABLE absences";
        $rq5 = "TRUNCATE TABLE postit_eleve";
        $rq6 = "TRUNCATE TABLE sequences";
        // lancer la requete
        $result1 = @mysql_query ($rq1) or die (mysql_error());
        $result2 = @mysql_query ($rq2) or die (mysql_error());
        $result3 = @mysql_query ($rq3) or die (mysql_error());
        $result4 = @mysql_query ($rq4) or die (mysql_error());
        $result5 = @mysql_query ($rq5) or die (mysql_error());
        $result6 = @mysql_query ($rq6) or die (mysql_error());
        if ($result1 && $result2 && $result3 && $result4 && $result5 && $result6) $mess2="<span class='cok'> les tables  ont &#233;t&#233; vid&#233;es </span>";
        else $mess2="<span class='nook'> Une erreur s'est produite lors de l'effacement des donn&#233;es <br /></span>";
        }
    }

if (isset($_POST['Restaurer']))
    {	
    function get_key() 
        {
        $val=array();
        $cmd = "cat /etc/LcSeConfig.ph | grep 'mysqlServerPw ' | cut -d\"'\" -f2";
        exec($cmd,$val,$ret_val);
        return $val[0];
        }	
    //traitement du fichier texte		
    if (!empty($_FILES["FileSelection4"]["name"]))
        {
         if (($_FILES["FileSelection4"]["size"]>0) && ($_FILES["FileSelection4"]["type"]=="text/x-sql"))
            {
            $res = get_key();
            $cmd2="mysql -uroot -p".$res." ". DB_NAME ." < ".$_FILES['FileSelection4']['tmp_name'];
            exec($cmd2,$rien,$retour);
            if ($retour == 0)  $mess2= "<h3 class='cok'>  La restauration de la base de donn&#233;es a r&#233;ussi</h3>";
            else $mess2 ="<h3 class='nook'>  La restauration de la base de donn&#233;es a &#233;chou&#233; !"."</h3>";
            //$mess2= $cmd;
            }
        else $mess2= "<h3 class='nook'> Erreur dans l'importation du fichier de sauvegarde "."<br /></h3>";
        }
    else $mess2= "<h3 class='nook'> Vous devez s&#233;lectionner un fichier de sauvegarde "."<br /></h3>";
    }

//import vacances scolaires
if (isset($_POST['Actualiser']) && isset($_POST['zone']))
    {
    $zon=$_POST['zone'];
    require_once('../Includes/parsical/SG_iCal.php');
    setlocale(LC_TIME, "fr_FR");
    $texte= '<table id="plan-cdt" cellpadding="1" cellspacing="2" class="vac">
    <thead> 
    <tr><th class="vac">Dates enregistr&eacute;es</th><td class="vac"> Du </td><td class="vac"> Au </td>
    </tr></thead>
    <tbody>';
    $ICS = "http://media.education.gouv.fr/ics/Calendrier_Scolaire_Zone_".$_POST['zone'].".ics";
    $ical = new SG_iCalReader($ICS);
    $query = new SG_iCal_Query();
    $evts = $query->Between($ical,time(),time()+31190400);
    $data = array();
    $i=0;
    foreach($evts as $id => $ev) 
        {
        $jsEvt = array(
            "id" => ($id+1),
            "title" => $ev->getProperty('summary'),
            "start" => $ev->getStart(),
            "end"   => $ev->getEnd()-1,
            "allDay" => $ev->isWholeDay()
        );
        $data[] = $jsEvt;
        if (mb_ereg(("oussaint|[Nn]o|rintem|hiv"), utf8_decode ($jsEvt["title"])))
            {
            $vac=mb_split('-',$jsEvt["title"],2);
            $texte.= '<tr><th> '.utf8_decode($vac[0]).' </th>'. "\n". '<td class="encours">'.utf8_encode(strftime('%A %d %B %Y',$jsEvt["start"])).'</td><td class="encours">'.utf8_encode(strftime('%A %d %B %Y',$jsEvt["end"])).'</td></tr>'. "\n";
            $Dbut[$i]=$jsEvt["start"];
            $F1[$i]=$jsEvt["end"];
            $i++;
            }
        }   
    $texte.= '</tbody></table>';
    $text=addslashes($texte);
    $nom_fichier="../Includes/vac.inc.php";
    $fichier=fopen($nom_fichier,"w");
    fputs($fichier, "<?php \n");    
    fputs($fichier,"\$zaune=\"$zon\";\n");
    for ($index=0;$index<count($Dbut);$index++)
        {
        fputs($fichier,"\$debv[$index]=\"$Dbut[$index]\";\n");
        fputs($fichier,"\$finv[$index]=\"$F1[$index]\";\n");
        }
    fputs($fichier,"\$texte=\"$text\";\n");
    fputs($fichier, " ?>\n");
    fclose($fichier);
    }
?>
<form action="<?php echo htmlentities($_SERVER['PHP_SELF']).'#liste'; ?>" method="post" enctype="multipart/form-data">
<div><input name="TA" type="hidden"  value="<?php echo md5($_SESSION['RT'].htmlentities($_SERVER['PHP_SELF'])); ?>" />
<a name="liste"></a>
<fieldset class="field7d">
<legend class="legended"> Cr&eacute;ation du fichier &quot;Liste des classes&quot;  &nbsp;
<?php
//affichage de la liste des classes
include ("../Includes/data.inc.php");
$jo="";
echo "<select name='CLASSE' style='background-color:#E6E6FA'>";
foreach ($classe as $clef => $valeur)
  { 
  echo "<option value=\"$valeur\"";
  echo ">$valeur</option>\n";
  }
echo "</select>\n";
?>
 </legend>
<?php
/**********************
 affichage du formulaire *
************************/
echo '
<h4 class="perso">La liste des classes sous forme de menu d&eacute;roulant est &eacute;labor&eacute;e &#224; partir d\'un fichier Php.<br /> 
Ce fichier peut &#234;tre g&eacute;n&eacute;r&eacute; soit : <br /></h4>
<ul class="perso">
<li> &agrave; partir de l\'annuaire du LCS
<p><input type="radio" name="size" value="long" /> Format long (Classe_LT_2DE1) <br />
<input type="radio" name="size" value="court" checked="checked" /> Format court (2DE1) Attention, pour utiliser ce format, le nom de la classe ne doit pas comporter de tiret bas _ ( exemple : 3_A )
<br /> <br /><input type="submit" name="Importer" value="Importer" />
</p>
</li>
<li> &agrave; partir d\'un fichier texte contenant la liste des classes s&eacute;par&eacute;es par une virgule.(ex
: 2nde1,1ereS,TermS,... TS1) <br />
(Attention : pas de virgule ni de caract&#232;re CR &#224; la fin de la derni&#232;re ligne)<br />
Indiquez le chemin du fichier texte :
<br /><input type="file" name="FileSelection3" size="30" />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="submit" name="Creer" value="Cr&#233;er le fichier Php" />
<br /><br />
<a href="./edit.php" onclick="auth_popup(); return false" title="Modifier">EDITER
</a>&nbsp; le fichier csv actuel pour le modifier
</li>
</ul>
<h4 class="perso"><input type="submit" name="test" value="Tester la coh&#233;rence" />  du fichier classe avec l\'annuaire pour un bon fonctionnement.
</h4>';
	
//affichage du resultat
if ($mess1!="") echo $mess1;
?>
</fieldset>
 </div>
</form>
<form action="<?php echo htmlentities($_SERVER['PHP_SELF']).'#key'; ?>" method="post">
    <div><input name="TA" type="hidden"  value="<?php echo md5($_SESSION['RT'].htmlentities($_SERVER['PHP_SELF'])); ?>" />
<a name="key"></a>
<fieldset class="field7d">
<legend class="legended"> Cryptage</legend>

<?php
echo '
<h4 class="perso"> L\'acc&eacute;s au cahier de texte par les parents se fait par un lien avec le nom des classes crypt&eacute;es. Le crytage doit &#234;tre 
renouvel&eacute; en d&eacute;but de chaque ann&eacute;e pour que les liens de l\'ann&eacute;e pr&eacute;c&eacute;dente ne soient plus valides.</h4>
<h4 class="perso"><input type="submit" name="change_grain" value="Changer" /> la cl&#233; de cryptage </h4>';
if ( $cle=="ok") echo  "<h4 class='cok'> La cl&#233; a &eacute;t&eacute; chang&eacute;e </h4>";
elseif ( $cle=="ko") echo " <h4 class='nook'> Echec du changement de cl&eacute</h4>";
?>
</fieldset>
 </div>
</form>
<form action="<?php echo htmlentities($_SERVER['PHP_SELF']).'#abs'; ?>" method="post">
<div ><input name="TA" type="hidden"  value="<?php echo md5($_SESSION['RT'].htmlentities($_SERVER['PHP_SELF'])); ?>" />
<a name="abs"></a>
<fieldset class="field7d">
<legend class="legended"> Visualisation des absences </legend>

<?php
echo '
<h4 class="perso"><br /> Le carnet d\'absences pour les &eacute;l&egrave;ves et les parents doit &ecirc;tre activ&eacute; uniquement si les informations affich&eacute;es sont significatives.
Cela implique que tous les professeurs saisissent les absences via le carnet d\'absences du Cdt. </h4>
<ul class="perso">
<li>Actuellement, la visualisation des absences est';
if ($FLAG_ABSENCE==0) echo ' : <span class="nook">D&#233;sactiv&#233;e</span>'; 
else echo ' :  <span class="cok">Activ&#233;e </span>';
echo '<p><input type="radio" name="absence" value="0" ';
if ($FLAG_ABSENCE==1) echo ' checked="checked"';
echo '/> D&#233;sactiver la visualisation des absences<br />
<input type="radio" name="absence" value="1"';
if ($FLAG_ABSENCE==0) echo ' checked="checked"';
echo ' /> Activer la visualisation des absences<br /><br />
<input type="submit" name="res_nores" value="Valider" />&nbsp;&nbsp;&nbsp;&nbsp;
</p>
</li>
</ul>';
?>
</fieldset>
</div>
</form>
<form action="<?php echo htmlentities($_SERVER['PHP_SELF']).'#bdd'; ?>" method="post" enctype="multipart/form-data">
<div><input name="TA" type="hidden"  value="<?php echo md5($_SESSION['RT'].htmlentities($_SERVER['PHP_SELF'])); ?>" />
<a name="bdd"></a>
<fieldset class="field7d">
<legend class="legended"> Gestion de la base de donn&#233;es</legend>
<?php
if ($mess2!="") echo $mess2;
echo '
<div><ol class="perso">
<li><b>Archivage</b><p>En fin d\'ann&#233;e scolaire, vous pouvez archiver le cahier de textes, ce qui permettra aux profs de consulter leur cahier des ann&#233;es ant&#233;rieures.<br /> Indiquez ci dessous le nom que vous voulez donner &#224; l\'archive. Ce nom servant &#224; renommer les tables de donn&#233;es, ne mettez pas de caract&#232;res -  :  / \\ etc ... Il est conseill&#233; de simplement remplacer les chiffres dans le nom propos&#233;.<br /><br />
<input type="submit" name="Archiver" value="Cr&#233;er une archive nomm&#233;e" />&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="name_arch" value="'.date("Y",mktime()-31536000)."_".date("Y").'" size="9" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
En d&#233;but d\'ann&#233;e, &nbsp; <input type="submit" name="Vider" value="Vider les tables" />&nbsp;  de donn&#233;es du cahier de texte.<br /><br />
Archives existantes : ';
$TablesExist= mysql_query("show tables");
while ($table=mysql_fetch_row($TablesExist))
if (mb_ereg("^onglets[[:alnum:]]",$table[0]))
    {
    $archive=explode('s',$table[0]);
    echo ($archive[1]). ' &nbsp; ';
    }
echo '</p></li>';
echo '<li><b> Sauvegarde</b><p>Le bouton ci dessous permet de g&#233;n&#233;rer un fichier de sauvegarde compl&#233;te de la base de donn&#233;es (structure + donn&#233;es)<br /><br />
<input type="submit" name="Sauver" value="Sauvegarder la base de donn&#233;es" />	</p></li>
<li><b> Restauration</b><p>Le bouton ci dessous permet d\'importer les donn&#233;es &#224; partir d\'un fichier de sauvegarde. Attention, la structure et  les donn&#233;es pr&#233;sentes dans la base, seront SUPPRIMEES et REMPLACEES par celles du fichier s&#233;lectionn&#233; <br />
S&#233;lectionner le fichier de sauvegarde : 
<br /><input type="file" name="FileSelection4" size="30" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="submit" name="Restaurer" value="Restaurer la base de donn&#233;es" />
</p>
</li>
</ol></div>';

?>
</fieldset>
</div>
</form>
<form action="<?php echo htmlentities($_SERVER['PHP_SELF']).'#creneau'; ?>" method="post" >
<div><input name="TA" type="hidden"  value="<?php echo md5($_SESSION['RT'].htmlentities($_SERVER['PHP_SELF'])); ?>" />
<a name="creneau"></a>
<fieldset class="field7d">
<legend class="legended"> Param&egrave;trage des cr&eacute;neaux horaires </legend>

<?php
//Affichage du formulaire de parametrage des creneaux horaires
include "../Includes/creneau.inc.php";
$horaire = array("M1","M2","M3","M4","M5","S1","S2","S3","S4","S5");
//pour les dix creneaux
echo '<ul class="perso">';
for ($index=0;$index<10;$index++)
    {
    //creneaux du matin
    if ($index<5)
        {
        echo "<li> M ".($index+1)." commence &#224;  " ;
        }
    //creneaux de l'apres-midi
    if ($index>4)
        {
        echo "<li> S ".($index-4)." commence &#224;  " ;
        }
    //affichage d'un menu deroulant heure debut 8h 17h
    echo "<select name='hd".$index."'\n >";
    $heure = 8;
    while ($heure <= 17)
        { 
        echo "<option value=\"$heure\"";
        if ($heure==date('G',$deb[$index])) {echo ' selected="selected"';}
        echo ">$heure</option>\n";
        $heure++;
        }
    echo "</select>\n h ";
    //affichage d'un menu deroulant minutes debut0 55 min
    echo "<select name='md".$index."' \n >";
    $min = 00;
    while ($min <= 55)
        { 
        echo "<option value=\"$min\"";
        if ($min==date('i',$deb[$index])) {echo ' selected="selected"';}
        echo ">$min</option>\n";
        $min+=5;
        }
    echo "</select>\n min "."&nbsp;et se termine &#224; &nbsp;";
    //affichage d'un menu deroulant heure fin 8h 18h
    echo "<select name='hf".$index."' \n >";
    $heure = 8;
    while ($heure <= 18)
        { 
        echo "<option value=\"$heure\"";
        if ($heure==date('G',$fin[$index])) {echo ' selected="selected"';}
        echo ">$heure</option>\n";
        $heure++;
        }
    echo "</select>\n h ";
    //affichage d'un menu deroulant minutes fin 0 55 min
    echo "<select name='mf".$index."' \n >";
    $min = 0;
    while ($min <= 55)
        { 
        echo "<option value=\"$min\"";
        if ($min==date('i',$fin[$index])) {echo ' selected="selected"';}
        echo ">$min</option> \n";
        $min+=5;
        }
    echo "</select> min \n";
    if ($index==3 || $index==4  || $index==8 || $index==9) 
        {
        echo '<span ><input type="checkbox"  name="crenoff[]"   value="'.$horaire[$index].'"'; 
        if (in_array($horaire[$index], $cren_off)) echo ' checked="checked"';
        echo ' /> masquer ce cr&eacute;neau</span>';
        }
    echo"</li> ";
    }
//fin de boucle 
echo '</ul>';
//affichage du bouton
echo '<div ><input type="submit" name="Enregistrer" value="Enregistrer" /></div>';
//affichage du resultat de parametrage
for ($h=0; $h<9; $h++) 
    {
    if ($deb[$h]>=$deb[$h+1] || $deb[$h]>=$fin[$h]) $mess3="  Il y a une incoh&#233;rence dans les horaires ! ";
    }
if (isset ($mess3)) echo "<h3 class='nook'>".$mess3;
?>
</fieldset>
</div>	
</form>
<form action="<?php echo htmlentities($_SERVER['PHP_SELF']).'#vacances'; ?>" method="post" >
<div><input name="TA" type="hidden"  value="<?php echo md5($_SESSION['RT'].htmlentities($_SERVER['PHP_SELF'])); ?>" />
<a name="vacances"></a>
<fieldset class="field7d">
<legend class="legended"> Param&egrave;trage des vacances scolaires </legend>
<?php
//
$texte="";
if (is_file("../Includes/vac.inc.php")) include ("../Includes/vac.inc.php");
echo '<h4 class="perso"> Permet aux utilistateurs de voir le <b>T</b>ravail <b>A</b> <b>F</b>aire pour les 15 prochains jours <b>ouvrables</b><br />
<br />S&eacute;lectionnez votre zone : 
<input type="radio" name="zone" value="A" '; 
if ($zaune=="A") echo ' checked="checked"';
echo ' />Zone A
<input type="radio" name="zone" value="B" '; 
if ($zaune=="B") echo ' checked="checked"';
echo '/>Zone B
<input type="radio" name="zone" value="C"'; 
if ($zaune=="C") echo ' checked="checked"';
echo ' />Zone C
<br /></h4><br /><br /><br /><br />';
echo stripslashes($texte).'<input type="submit" name="Actualiser" value="Actualiser" />';
?>
</fieldset>
</div>	
</form>
<?php
include ('../Includes/pied.inc');
?>
</body>
</html>