<?php
/* ==================================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
  VERSION 2.3 du 06/01/2011
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script de diffusion -
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

include "../Includes/functions2.inc.php";
$tsmp=time();
$tsmp2=time() + 604800;//j+7


// Connexion à la base de donnees
require_once ('../Includes/config.inc.php');

if (isset($_GET['rubrique']))
	{ $ru=$_GET['rubrique'];
	$rq = "SELECT classe,matiere FROM onglets
	WHERE id_prof='$ru'";
	// lancer la requete
	$result = @mysql_query ($rq) or die (mysql_error());
	// Combien y a-t-il d'enregistrements ?
	$nb = mysql_num_rows($result); 
	//on recupère les donnees
	$enrg = mysql_fetch_array($result, MYSQL_NUM); 
	if (mb_ereg("^(Classe)",$enrg[0]))
	{
	//recherche du niveau de la classe pour le format long
	$classe_courte=explode('_',$enrg[0]);
	//on isole le nom court de la classe
	$niveau=$classe_courte[count($classe_courte)-1];
	$FirstCar="";
	//on recolle les premiers morceaux du nom long
	for ($loop=0; $loop <= count($classe_courte)-2  ; $loop++)
			{
			$FirstCar.=$classe_courte[$loop]."_";
			}
	//on ajoute le premier caractere du nom court
	$FirstCar.=$niveau[0];		
	}
		else
	{
	//cas du format court
	//premier caractere de la classe active
	$FirstCar=$enrg[0][0];
	}
	//classe active 
	$clas_act=$enrg[0];
	//matière de la classe active
	$mat=$enrg[1];
	
	}
	
// Recherche des classes de même niveau dans la matière
$rq = "SELECT classe,id_prof FROM onglets
 WHERE login='{$_SESSION['login']}'  AND classe != '$clas_act' AND  matiere = '$mat' ORDER BY classe ASC ";
 
// lancer la requête
$result = @mysql_query ($rq) or die (mysql_error()); 
$loop=0;
while ($row = mysql_fetch_object($result))
	{
        $data1[$loop]=$row->classe;
	$data2[$loop]=$row->id_prof;
	$loop++;
	}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html  xmlns="http://www.w3.org/1999/xhtml" >
<head>
<title>Module bichaouaoua</title>
<meta name="author" content="Philippe LECLERC -TICE CAEN" />
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<link href="../style/style.css" rel="stylesheet" type="text/css" />
	<link  href="../style/ui.all.css" rel="stylesheet" type="text/css" />
	<link  href="../style/ui.datepicker.css" rel="stylesheet" type="text/css" />
	<link  href="../style/ui.theme.css" rel="stylesheet" type="text/css" />
		<!--[if IE]>
<link href="../style/style-ie.css"  rel="stylesheet" type="text/css"/>
<![endif]-->
	<script type="text/javascript" src="../Includes/JQ/jquery-1.3.2.min.js"></script>
	<script type="text/javascript" src="../Includes/JQ/ui.core.js"></script>  
	<script type="text/javascript" src="../Includes/JQ/ui.datepicker.js"></script>
	<script type="text/javascript" src="../Includes/JQ/cdt-script.js"></script>
</head>
<body>
<h1 class='title'>Enregistrer plusieurs classes</h1>
<?php
//si clic sur le bouton Valider
if (isset($_POST['Valider']))
	{
	//initialisation des variables de session
	unset ($_SESSION['grp']);
	unset ($_SESSION['dif_c']);
	unset ($_SESSION['dif_af']);
	unset ($_SESSION['dif_vi']);
        unset ($_SESSION['dif_sq']);

	//création des tableaux de date des cours et "a faire"
	for ($loop=0; $loop < count ($_POST['grp'])  ; $loop++)
            {
            $diffuse_c[$loop]=$_POST['datejavac_dif'][$_POST['grp'][$loop]];
            $diffuse_af[$loop]=$_POST['datejavaf_dif'][$_POST['grp'][$loop]];
            $diffuse_vi[$loop]=$_POST['datejavavi_dif'][$_POST['grp'][$loop]];
            $diffuse_sq[$loop]=$_POST['sequence_dif'][$_POST['grp'][$loop]];
            }

	//mémorisation des données dans des variables de session		
	$_SESSION['grp']=$_POST['grp'];
	$_SESSION['dif_c']=$diffuse_c;
	$_SESSION['dif_af']=$diffuse_af;
	$_SESSION['dif_vi']=$diffuse_vi;
	$_SESSION['dif_sq']=$diffuse_sq;

        //fermeture du popup
	echo '<script type="text/javascript">
                    //<![CDATA[
                    window.close()
                   //]]>
                    </script>';
	}

?>
<form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="post" >
<div><input name="TA" type="hidden"  value="<?php echo md5($_SESSION['RT'].htmlentities($_SERVER['PHP_SELF'])); ?>" />
<fieldset id="field7">
<legend id="legende">S&#233;lection des classes</legend>

<?php
//affichage du formulaire
//date "cours" au jour courant
$datjc_dif=getdate($tsmp);
$jo_dif=date('d',$datjc_dif['0']);
$mo_dif=date('m',$datjc_dif['0']);
$ann_dif=date('Y',$datjc_dif['0']);
$dtajac_dif=$jo_dif."/".$mo_dif."/".$ann_dif;
//date "a faire" a j+7
$datjf_dif=getdate($tsmp2);
$jof_dif=date('d',$datjf_dif['0']);
$mof_dif=date('m',$datjf_dif['0']);
$annf_dif=date('Y',$datjf_dif['0']);
$dtajaf_dif=$jof_dif."/".$mof_dif."/".$annf_dif;

$dtajavi_dif="idem Cours";
if (!isset($_POST['Valider']))
	{
	//si pas de classe de meme niveau dans la matiere
	if (mysql_num_rows($result)==0) 
		{
		echo '<h4> Apparemment, vous n\'avez pas d\'autre classe  en <b>'.$mat.'</b> !<br /> V&#233;rifiez que la mati&#232;re n\'est
		pas orthographi&#233;e diff&#233;remment pour les autres classes .</h4>';
		echo '<input type="submit" name="Valider" value="OK" class="bt50" >';
		}
	//sinon, affichage des classes de meme niveau
	else
		{
		echo '<h4>Lorsque vous enregistrerez votre commentaire de <b>'.$mat.'</b> en <b>'.$clas_act.'</b>, il sera diffus&#233; vers les classes que vous aurez sélectionn&#233;es ci-dessous </h4><br />';
		echo '<div id="classes">';
		echo '<table id="pop" cellspacing="2"> ';
		echo '<tr><td ></td><td>Classe</td><td>Date du cours</td><td>Date "&#224; faire" </td><td>Date visiblit&#233; </td><td>S&#233;quence</td></tr>';
		for ($loop=0; $loop < count ($data1)  ; $loop++)
			{
			echo '<tr><td><input type="checkbox" name="grp[]" value="'.$data2[$loop];
			echo '" /></td><td>'.$data1[$loop].'</td> <td><input id="datejavac'.$loop.'" size="9" name="datejavac_dif['.$data2[$loop].']" value="'.$dtajac_dif.'" readonly="readonly" style="cursor: text" />';
			
			echo '</td><td> <input id="datejaf'.$loop.'" size="9" name="datejavaf_dif['.$data2[$loop].']" value="'.$dtajaf_dif.'" readonly="readonly" style="cursor: text" />';
			echo '</td><td> <input id="datejavav'.$loop.'" size="9" name="datejavavi_dif['.$data2[$loop].']" value="'.$dtajavi_dif.'" readonly="readonly" style="cursor: text" />';
			echo '</td><td class="sele" >';
                        //sequences
                        $rq = "SELECT id_seq,titrecourt FROM sequences
	WHERE id_ong='$data2[$loop]' order by ordre ASC";
	// lancer la requete
	$resulta = @mysql_query ($rq) or die (mysql_error());
	// Combien y a-t-il d'enregistrements ?
	$nbs = mysql_num_rows($resulta);
	//on recupere les donnees
        echo "<select name='sequence_dif[".$data2[$loop]."]' style='background-color:#E6E6FA' >";
        echo '<option value="0">-----------------</option>';
        //foreach ($TitreCourSeq as $cle => $valeur)
        if ($nbs>0)
        {
           while ($rows = mysql_fetch_object($resulta))
          {
            echo '<option value="'.$rows->id_seq.'"';
            echo ">$rows->titrecourt</option>";
           }
         }
        echo "</select></td></tr>";


                        //
                        //echo '</tr>';
			}
		echo '</table></div>';
		echo '<input type="submit" name="Valider" value="" class="bt-valid-sel" />';
		echo '<div class="notabene"> N\'oubliez pas apr&egrave;s avoir "Valider la s&eacute;lection", de cliquer sur <b>Enregistrer </b>dans le cahier de texte</div>';
		}
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
