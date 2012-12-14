<?php
/* =============================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 2.5 du 20/04/2012
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script d'impression -
			_-=-_
  "Valid XHTML 1.0 Strict"
   ============================================= */
session_name("Cdt_Lcs");
@session_start();
include "../Includes/check.php";
if (!check()) exit;

//si la page est appelee par un utilisateur non identifie
if (!isset($_SESSION['login']) )exit;
//si la page est appelee par un utilisateur non prof
elseif ($_SESSION['cequi']!="prof") exit;
//si prof, connexion a la base de donnees
require_once ('../Includes/config.inc.php');

function Imprime_seance($param) {
    //affiche une seance dans le contenu du cdt
    global $cible,$dlm1;
    $rq = "SELECT DATE_FORMAT(date,'%d/%m/%Y'),contenu,afaire,DATE_FORMAT(datafaire,'%d/%m/%Y'),id_rubrique,date,on_off,DATE_FORMAT(datevisibi,'%d/%m/%Y') FROM cahiertxt
    WHERE (id_rubrique=$param) ";
    // lancer la requete
    $result = @mysql_query ($rq) or die (mysql_error());
    if (mysql_num_rows($result) >0)
        {
        while ($ligne = mysql_fetch_array($result, MYSQL_NUM))
          {
          $textcours=stripslashes($ligne[1]);
          $textafaire=stripslashes($ligne[2]);
          $jour=LeJour (strToTime($ligne[5]));
          //debut
          if ($ligne[1]!="")
            {
            echo '<tbody><tr><th class="seance">S&eacute;ance du '.$ligne[0].'</th></tr>';
            echo '<tr><td>'.$textcours.'</td></tr>';
            //affichage, s'il existe, du travail a effectuer
            if ($ligne[2]!="")
              {
              echo '<tr><td  class="afR">A faire pour le :'.$ligne[3].'</td></tr >';
              echo '<tr><td>'.$textafaire.'</td></tr>';
              }
            //fin
            echo '</tbody>';
            echo ' <tbody><tr><td ><hr /></td></tr></tbody>';
            }
          else
            {
            echo '<tbody><tr><th class="seance">Donn&eacute; le :&nbsp;'.$ligne[0].'</th></tr>';
            echo '<tr>';
            //affichage de la seance
            //affichage, s'il existe, du travail a effectuer
            if ($ligne[2]!="")
              {
              echo '<td><br/>Pour le :&nbsp;'.$ligne[3].'</td></tr>';
              echo '<tr><td >';
              echo $textafaire.'</td></tr>';
              }
            //fin
            echo '</tbody>';
            echo '<tbody><tr><th><hr /></th></tr></tbody>';
            }
        }
    }
}

function Imprime_seq($param) {
    global $dlm1;
    //affiche une sequence et son contenu dans le cdt
    $rqs = "SELECT titrecourt,titre,contenu FROM sequences WHERE id_seq='$param'";
    // lancer la requete
    $results = @mysql_query ($rqs) or die (mysql_error());
    // Combien y a-t-il d'enregistrements ?
    if (mysql_num_rows($results)>0)
        {
        $rows = mysql_fetch_array($results, MYSQL_NUM);
        echo '<tbody><tr><th  >'.utf8_encode($rows[1])."</th></tr></tbody>";
        echo '<tbody><tr><td class="description"  colspan="2" >'.utf8_encode($rows[2]);
        echo ' </td></tr></tbody>';
        $rq2 = "SELECT id_rubrique FROM cahiertxt  WHERE seq_id='$param'".$dlm1." order by date asc ";
        $result2 = @mysql_query ($rq2) or die (mysql_error());
        while ($ligne = mysql_fetch_array($result2, MYSQL_NUM))
            {
            Imprime_seance_seq ($ligne[0]);
            }
        echo '<tbody><tr><th><hr /></th></tr></tbody>';
        }
}

function Imprime_seance_seq ($param) {
    //affiche une sequence associee a une sequence
    global $cible;
    $rq = "SELECT DATE_FORMAT(date,'%d/%m/%Y'),contenu,afaire,DATE_FORMAT(datafaire,'%d/%m/%Y'),id_rubrique,date,on_off,DATE_FORMAT(datevisibi,'%d/%m/%Y') FROM cahiertxt
    WHERE (id_rubrique=$param) ";

    $result = @mysql_query ($rq) or die (mysql_error());
    if (mysql_num_rows($result) >0)
        {
        while ($ligne = mysql_fetch_array($result, MYSQL_NUM))
            {
            $textcours=utf8_encode(stripslashes($ligne[1]));
            $textafaire=utf8_encode(stripslashes($ligne[2]));
            $jour=LeJour (strToTime($ligne[5]));
            //debut
            if ($ligne[1]!="")
                {
                echo '<tbody><tr>';
                //affichage de la seance
                echo '<td><span  class="cours">S&eacute;ance du '.$jour.'&nbsp;'.$ligne[0].' </span></td></tr>';
                echo '<tr ><td>';
                echo $textcours.'</td></tr>';
                //affichage, s'il existe, du travail a effectuer
                if ($ligne[2]!="")
                    {
                    echo '<tr><td class="afR">A faire pour le : '.$ligne[3].'</td><tr>';
                    echo '<tr><td>'.$textafaire.'</td></tr></tbody>';
                    }
                }
            else
                {
                echo '<tbody><tr>';
                //affichage de la seance
                echo '<td><span  class="cours">Donn&eacute; le :&nbsp;'.$ligne[0].' </span></td></tr>';
                //affichage, s'il existe, du travail a effectuer
                if ($ligne[2]!="")
                    {
                    echo '<tr><td >Pour le :&nbsp;'.$ligne[3].'</td></tr>';
                    echo '<tr><td >';
                    echo $textafaire.'</td></tr>';
                    }
                }
            echo ' <tr><td ><hr /></td></tr></tbody>';
            }
        }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html  xmlns="http://www.w3.org/1999/xhtml" >
<head>
<title>Impression Cdt</title>
<meta name="author" content="Philippe LECLERC -TICE CAEN" />
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<link rel="stylesheet" type="text/css" href="../style/style.css"  media="screen" />
<link rel="stylesheet" href="../style/style_imp.css" type="text/css" media="print" />
<!--[if IE]>
<link href="../style/style-ie.css"  rel="stylesheet" type="text/css"/>
<![endif]-->
<script type="text/x-mathjax-config">
                    MathJax.Hub.Config({
                    tex2jax: {inlineMath: [['$','$'], ['\\(','\\)']]},
                    MMLorHTML: { prefer: { Firefox: "HTML" } }
                    });
                   </script>
                    <script type="text/javascript" src="../../../libjs/MathJax/MathJax.js?config=TeX-AMS-MML_HTMLorMML"> </script>
</head>
<body style="background:#fff">

<?php
$tsmp=time();
$tsmp2=time();
$cours="";//variable temporaire de $Cours (avec une majuscule) pour un UPDATE
$afaire="";//variable temporaire de $Afaire (avec une majuscule) pour un UPDATE
$article="";

// Cette fonction cree un menu calendier : mois, jours et annees, initialise a la date du timestamp precise  + offset
function calendrier_auto($offset,$var_j,$var_m,$var_a,$tsmp)
//offset=nbre de jours / au timestmp ,var_j,var_m,var_a=nom des variables associees pour la bd ,$tsmp=timestamp
    {
    // Tableau indexe des jours
    $jours = array (1 => '01', '02', '03', '04', '05','06', '07', '08', '09', '10', '11','12','13','14','15','16','17','18','19','20','21','22','23','24','25','26','27','28','29','30','31');

    // Tableau indexe des mois
    $mois = array (1 => '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11','12');
    $dateinfo=getdate($tsmp);
    $jo=date('d',$dateinfo['0']+($offset*86400));
    $mo=date('m',$dateinfo['0']+($offset*86400));
    $ann=date('Y',$dateinfo['0']+($offset*86400));

    // Creation des menus deroulants
    //les jours
    echo "<select name=\"$var_j\">\n";
    foreach ($jours as $cle => $valeur)
      {
      echo "<option value=\"$cle\"";
      if ($cle==$jo) {echo ' selected="selected"';}
      echo ">$valeur</option>\n";
      }
    echo "</select>\n";
    //les mois
    echo "<select name=\"$var_m\">";
    foreach ($mois as $cle => $valeur)
        {
        echo "<option value=\"$cle\"";
        if ($cle==$mo) {echo ' selected="selected"';}
        echo ">$valeur</option>\n";
        }
    echo "</select>\n";
    //les annees
    echo "<select name=\"$var_a\">";
    $annee = 2005;
    while ($annee <= 2015)
        {
        echo "<option value=\"$annee\"";
        if ($annee==$ann) {echo ' selected="selected"';}
        echo ">$annee</option>\n";
        $annee++;
        }
    echo "</select>\n";
    }
//Fin de la fonction calendrier_auto().

$sens="";
//on recupere le nom de l'archive
if (isset($_GET['arch'])) {
$arch=$_GET['arch'];}
elseif
(isset($_POST['archi'])) {
$arch=$_POST['archi'];}
else
{$arch="";}
//on recupere le parametre de la rubrique
if (isset($_GET['rubrique'])) {
$cible=$_GET['rubrique'];}
elseif
(isset($_POST['rubriq'])) {
$cible=$_POST['rubriq'];}
else
{$cible="";}

//recherche de la matiere et la classe de la rubrique active du cahier de textes
if (isset($cible))
    {
    $rq = "SELECT classe,matiere,visa,DATE_FORMAT(datevisa,'%d/%m/%Y') FROM onglets$arch
    WHERE id_prof='$cible'  ";
    // lancer la requete
    $result = @mysql_query ($rq) or die (mysql_error());
    // Combien y a-t-il d'enregistrements ?
    $nb = mysql_num_rows($result);
    //on recupere les donnees
    while ($enrg = mysql_fetch_array($result, MYSQL_NUM))
        {$classe_active=$enrg[0];//classe
        $mati_active=utf8_encode($enrg[1]);//matiere
        $tampon=$enrg[2];
        $datetampon=$enrg[3];
        }
    }

//recuperation du parametre timestamp pour memorisation
if (isset($_GET['tsmp']))  $tsmp=$_GET['tsmp'];
// si OK a ete cliquer
if (isset($_POST['valider']))
    {
    // Traiter le formulaire
    if (isset($_POST['Option1'])) $sens=$_POST['Option1'];
    //la date, le timestamp
    $date_c = $_POST['an_c'].$_POST['mois_c'].$_POST['jour_c'];
    $tsmp=mkTime(0,0,1,$_POST['mois_c'],$_POST['jour_c'],$_POST['an_c']) + 2592000;

    // Connexion a la base de donnees
    require_once ('../Includes/config.inc.php');

    //creer la requete
    if ($cible!="")
        {
        //elaboration de la date limite a partir de la date selectionnee
        $dat=date('Ymd',$tsmp-2592000);//2592000=nbre de secondes dans 30 jours
        }
    /*************************************************************************
    creation d'un tableau trie contenant les donnees a afficher
    **************************************************************************/
    include_once("/usr/share/lcs/Plugins/Cdt/Includes/fonctions.inc.php");
    $cib=$cible; $i=$j=$k=0;$list_art=$list_seq=$Sekance=array();
    $dlm1=" AND date>='".$dat."'";
    // Connexion a la base de donnees Cdt
    include ('../Includes/config.inc.php');
    $senseq=($sens=="asc") ? "desc" :"asc";
    // recuperer les sequences dans l'ordre d affichage
    $rq = "SELECT id_seq  FROM sequences WHERE id_ong='$cib' order by ordre ".$senseq;
    $result = @mysql_query ($rq) or die (mysql_error());
    if (mysql_num_rows($result) >0)
        {
        while ($idr = mysql_fetch_object($result))
            {
            $Sek[$i]= $idr ->id_seq ;
            $i++;
            }
        mysql_free_result($result);
        }
    //dates debut&fin des sequences non vides et comprises ente les dates limites
    for ($index = 0; $index < count($Sek); $index++) {
    $rq = "SELECT MAX(date),MIN(date) from cahiertxt  WHERE seq_id='$Sek[$index]'".$dlm1;
    $result = @mysql_query ($rq) or die (mysql_error());
    if (mysql_num_rows($result) >0)
        {
        $r = mysql_fetch_array($result);
        if ($r[0]!=null)
            {
            $der_seq[$k] = $r[0] ;
            $prem_seq[$k] =$r[1] ;
            $Sekance[$k]=$Sek[$index];
            $k++;
            }
        }
    }
    mysql_free_result($result);

    for ($index2 = 0; $index2 < count($der_seq); $index2++) {
    if ($sens=="desc")
        {
        //seances + recentes que la derniere sequence
        $Critere1=" AND date >= '".$der_seq[$index2]."'".$dlm1." order by date desc";
        //seances entre deux sequences
        $Critere2=" AND date >= '".$der_seq[$index2]."' AND date < '".$prem_seq[$index2-1]."'".$dlm1." order by date desc";
        }
    else
        {
        //seances + anciennes que la premiere sequence
        $Critere1=" AND date <= '".$prem_seq[$index2]."'".$dlm1." order by date asc";
        //seances entre deux sequences
        $Critere2=" AND date <= '".$prem_seq[$index2]."' AND date > '".$der_seq[$index2-1]."'".$dlm1." order by date asc";
        }
    if ($index2==0) $rq="SELECT id_rubrique from cahiertxt where seq_id='0' AND id_auteur='$cib'" .$Critere1;
    else $rq="SELECT id_rubrique from cahiertxt where seq_id='0' AND id_auteur='$cib'" .$Critere2;
    $result = @mysql_query ($rq) or die (mysql_error());
    if (mysql_num_rows($result) >0)
        {
        while ($row = mysql_fetch_array($result))
            {
            if ( ! in_array($row[0], $list_art))
                {
                $list_art[$j]=$row[0];
                $list_seq[$j]='0';
                $j++;
                }
            }
        }
    $list_art[$j]='0';
    $list_seq[$j]=$Sekance[$index2];
    $j++;
    }

    if ($sens=="desc")
        {
        //seances +anciennes que la pemiere sequence
        if (isset($prem_seq[$index2-1]))
        $rq="SELECT id_rubrique from cahiertxt where seq_id='0' AND id_auteur='$cib' AND  date < '".$prem_seq[$index2-1]."'".$dlm1." order by date desc";
        else
            {
            $rq="SELECT id_rubrique from cahiertxt where seq_id='0' AND id_auteur='$cib'".$dlm1."  order by date desc";
            }
        }
    if ($sens=="asc")
        {
        //seances  + recentes que la derniere sequence
        if (isset($der_seq[$index2-1]))
        $rq="SELECT id_rubrique from cahiertxt where seq_id='0' AND id_auteur='$cib' AND  date > '".$der_seq[$index2-1]."'".$dlm1." order by date asc";
        else
            {
            $rq="SELECT id_rubrique from cahiertxt where seq_id='0' AND id_auteur='$cib'".$dlm1."  order by date asc";
            }
        }
    $result = @mysql_query ($rq) or die (mysql_error());
    if (mysql_num_rows($result) >0)
        {
        while ($row = mysql_fetch_array($result))
            {
            if ( ! in_array($row[0], $list_art))
                {
                $list_art[$j]=$row[0];
                $list_seq[$j]='0';
                $j++;
                }
            }
        }
    //Affichage des donnees
    echo '<table id="impr" cellpadding="1" cellspacing="2">';
    echo '<caption>';
    echo "Classe : <b>".$classe_active ."</b>&nbsp;&nbsp;&nbsp;Mati&egrave;re : &nbsp;<b> ".$mati_active."</b>";
    //if ($tampon == 1) echo '<div id="visa-cdt">'.$datetampon.'</div>';
    echo '</caption>';
    for ($index1 = 0; $index1 < count($list_seq); $index1++)
        {
        if ($list_art[$index1]!='0')Imprime_seance ($list_art[$index1]); else Imprime_seq( $list_seq[$index1]);
        }
    echo '</table>';
    include ('../Includes/pied.inc');
    echo '</body>
    </html>';exit;
    }
/*================================
   -      Affichage du formulaire  -
   ================================*/
?>
<form id="impression" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>?rubrique=<?echo $cible;?>&amp;arch=<?echo $arch;?>" method="post"  >
<div><input name="TA" type="hidden"  value="<?php echo md5($_SESSION['RT'].htmlentities($_SERVER['PHP_SELF'])); ?>" />
<h1 class='title'>Impression du Cahier de Textes</h1>
<fieldset id="field7">
<legend id="legende">Classe  de <b> <?echo $classe_active .'</b> en <b>'. $mati_active ?></b></legend>
<p>Imprimer le contenu du cahier de textes de la classe  de <b> <?echo $classe_active .'</b> en <b>'. $mati_active ?></b>
 depuis le : <?calendrier_auto(-365,'jour_c','mois_c','an_c',$tsmp);?><input type="hidden" name="numrub" value= "<?php echo $ch ; ?>" /></p>
<div id="chrono">
<input type="radio" name="Option1" value="asc" <?php IF (($sens=="asc")||($sens=="")) echo ' checked="checked"' ; ?> />par date croissante &nbsp;&nbsp;&nbsp;&nbsp;
<input type="radio" name="Option1" value="desc"<?php IF ($sens=="desc") echo ' checked="checked"' ; ?> />par	date d&eacute;croissante
</div>
<ul>
<li>En cliquant sur OK, le contenu du cahier de texte appara&icirc;t dans un nouvel onglet. </li>
<li>Avec Firefox, cocher l'option &quot;Imprimer le fond de page&quot; du menu mise en page.</li>
<li>Avec Internet Explorer, cliquez sur Outils, options Internet, onglet  &quot;avanc&eacute;&quot; et cocher la case &quot;imprimer les couleurs et les images&quot;</li>
<li>Utiliser la fonction Imprimer de votre navigateur pour imprimer le contenu de la fen&ecirc;tre</li>
<li>Fermer l'onglet pour revenir au cahier de texte</li>
</ul>
<input type="submit" name="valider" value="OK" class="bt50" />
</fieldset>
</div>
</form>
<?php
include ('../Includes/pied.inc');
?>
</body>
</html>
