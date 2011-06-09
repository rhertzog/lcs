<?php
/* ==================================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 2.3 du 06/01/2011
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script de consultation des absences -
			_-=-_
  "Valid XHTML 1.0 Strict"
   =================================================== */
session_name("Cdt_Lcs");
@session_start();
include "../Includes/functions2.inc.php";
include "../Includes/fonctions.inc.php";
//fichiers necessaires a l'exploitation de l'API
	$BASEDIR="/var/www";
	//include "../Includes/basedir.inc.php";
	include "$BASEDIR/lcs/includes/headerauth.inc.php";
	include "$BASEDIR/Annu/includes/ldap.inc.php";
	include "$BASEDIR/Annu/includes/ihm.inc.php";  
	
//si la page est appelÃ©e par un utilisateur non identifie ou non autorise
if (!isset($_SESSION['login'])) exit;
elseif ((ldap_get_right("Cdt_is_cpe",$_SESSION['login'])=="N") &&  (ldap_get_right("Cdt_can_sign",$_SESSION['login'])=="N")) exit;

//Connexion a  la base de donnees
require_once ('../Includes/config.inc.php');

function SansAccent($texte){

$accent='ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËéèêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ';
$noaccent='AAAAAAaaaaaaOOOOOOooooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn';
$texte = strtr($texte,$accent,$noaccent);
return $texte;
} 

function date_incoherente($date1,$date2){
$splidat1=explode('/',$date1);
$newdate1=$splidat1[2].$splidat1[1].$splidat1[0];
$splidat2=explode('/',$date2);
$newdate2=$splidat2[2].$splidat2[1].$splidat2[0];
if ($newdate1>$newdate2) return true; else return false;
}
 
$tsmp=time();
$tsmp2=time() -604800;//j-7
//dates  au jour courant
if (!isset($_POST['datecren'])) {$datcreno=date('d/m/Y',$tsmp);} else 
	{
 	$datcreno=$_POST['datecren'];
 	$Morceauc=explode('/',$_POST['datecren']);
	$dtcrensql= $Morceauc[2]."/".$Morceauc[1]."/".$Morceauc[0];
 	}
if (!isset($_POST['datecl_fin'])) {$datfincla=date('d/m/Y',$tsmp);} else
	{ $datfincla=$_POST['datecl_fin'];}
	
if (!isset($_POST['datepot_fin'])) {$datfinpot=date('d/m/Y',$tsmp);} else { $datfinpot=$_POST['datepot_fin'];}

//dates Ã  j-7	
if (!isset($_POST['datecl_deb'])) {$datdebcla=date('d/m/Y',$tsmp2);} else	{$datdebcla=$_POST['datecl_deb'];}
if (!isset($_POST['datepot_deb'])) {$datdebpot=date('d/m/Y',$tsmp2);} else	{$datdebpot=$_POST['datepot_deb'];}



if (isset($_POST['division'])) 
	{
	$ch=$_POST['division'];
	$ch2=$_POST['division'];
	} 
if (isset($_POST['Classe'])) 
	{
	$ch=$_POST['Classe'];
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html  xmlns="http://www.w3.org/1999/xhtml" >
<head>
<title>Cahier de textes num&eacute;rique</title>
<meta name="author" content="Philippe LECLERC -TICE CAEN" />
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<link rel="stylesheet" type="text/css" href="../style/style.css"  media="screen"/>
	<link  href="../style/ui.all.css" rel="stylesheet" type="text/css" />
	<link  href="../style/ui.datepicker.css" rel="stylesheet" type="text/css" />
	<link  href="../style/ui.theme.css" rel="stylesheet" type="text/css" />
	<link rel="stylesheet" href="../style/style_imp.css" type="text/css" media="print" />
		<!--[if IE]>
<link href="../style/style-ie.css"  rel="stylesheet" type="text/css"/>
<![endif]-->
	<script type="text/javascript" src="../Includes/JQ/jquery-1.3.2.min.js"></script>
	<script type="text/javascript" src="../Includes/JQ/ui.core.js"></script>  
	<script type="text/javascript" src="../Includes/JQ/ui.datepicker.js"></script> 
	<script type="text/javascript" src="../Includes/JQ/cdt-script.js"></script>
</head>
<body>
<h1 class='title'>Consultation des absences</h1>
<?php
$tab_cren=array();	
if ((isset($_POST['OK'])) && count($_POST['cren']))
	{
	$tab_cren=$_POST['cren'];
	$kreno="";
	for ($i = 0; $i < count($_POST['cren']); $i++) 
		{
		if ($i>0) $kreno.="-";
		$kreno.=$_POST['cren'][$i];
		}		
		echo '<script type="text/javascript">
                //<![CDATA[
                window.open("./bilancpe2.php?kr='.$kreno.'&dkr='.$_POST['datecren'].'","","menubar=no, status=no, scrollbars=yes , width=900, height=600");
		//]]>
                </script>';
	}// fin OK
	
if (isset($_POST['OKclasse']))
	{
	$mess_dat="";
	if (date_incoherente($datdebcla,$datfincla)) $mess_dat=" Incoh&eacute;rence dans les dates ";
	else {
	echo '<script type="text/javascript">
                //<![CDATA[
                window.open("./bilancpe.php?kl='.$ch.'&dd='.$datdebcla.'&df='.$datfincla.'","","menubar=no, status=no, scrollbars=yes , width=900, height=600");
		//]]>
                </script>';
	}
	}//fin OKclasse

if (isset($_POST['OKeleve']))
	{
		$nom_propre = SansAccent(utf8_decode($_POST['nom']));
		 $nom_propre = mb_ereg_replace("^[[:blank:]]","",$nom_propre);
		 $nom_propre = mb_ereg_replace("[[:blank:]]$","",$nom_propre);
		 $nom_propre = mb_ereg_replace("'|[[:blank:]]","_",$nom_propre);
		 $nom_propre = StrToLower($nom_propre);
		 $nom_propre = strip_tags(stripslashes($nom_propre));
		 $prenom_propre = SansAccent(utf8_decode($_POST['prenom']));
		 $prenom_propre = mb_ereg_replace("^[[:blank:]]","",$prenom_propre);
		 $prenom_propre= mb_ereg_replace("[[:blank:]]$","",$prenom_propre);
		 $prenom_propre = mb_ereg_replace("'|[[:blank:]]","_",$prenom_propre);
		 $prenom_propre = StrToLower($prenom_propre);
		 $prenom_propre = strip_tags(stripslashes($prenom_propre));
		  
		  
		  //verifier la presence des eleves dans les classes
		  //1.recherche de la classe dans le ldap
		  $groups=search_groups('cn=classe*');

		  if (count($groups))
		  	{    
			for ($loup=0; $loup < count($groups); $loup++)
			        {
			        $cla = $_POST['division'];
						if (mb_ereg("($cla)$",$groups[$loup]["cn"]))
							{
							$full_classe =$groups[$loup]["cn"];
							break;
							}
						}
			}
			
		  //2.recherche d'une occurence du cn parmi le eleves de la classe --> uid 
			//recherches des uids des eleves  
		  $membres = search_uids ("(cn=".$full_classe.")", "half");

		//recherche de l' uid correspondants au nom saisi 
		  $le_cn = $prenom_propre." ".$nom_propre;
		  $filtre ="(cn=$le_cn)";
		  $gus= search_people ($filtre);
			$uidpotache="";
		  if (count($gus)) 
   		 						{
    								for ($loop=0; $loop < count($gus); $loop++) 
    									{	 
    									$uidgus= $gus[$loop]["uid"];
    									for ($iteration = 0; $iteration <= count($membres); $iteration++) 
    										{
 											if ($membres[$iteration]["uid"] ==  $uidgus) 
 												{
 												$uidpotache= $gus[$loop]["uid"];
 												break;								
 												}//uids diffÃ©rents (endif)
 											}//fin de recherche de correspondance dans la classe pour un uid(iteration)

										}//fin de recherche pour tous les uids(loop)
    									
    				}	//ya pas de gus avec cet uid dans la classe;(endif count)
    		$mess_error="";		
    		if ($uidpotache=="" || (date_incoherente($datdebpot,$datfinpot)))	
    			{
    			if  ($uidpotache=="")
    				{
    				$mess_error="&nbsp;&nbsp; Aucune correspondance n'a &eacute;t&eacute; trouv&eacute;e ";
    				if ($le_cn !=" ") $mess_error.= "pour ".$le_cn;
    				}
    			 if (date_incoherente($datdebpot,$datfinpot))
    				{
    				$mess_error.= " Incoh&eacute;rence dans les dates ";
    				}
    		}
    		else
    		echo    '<script type="text/javascript">
                             //<![CDATA[
                            window.open("./bilancpe.php?uid='.$uidpotache.'&fn='.$le_cn.'&dd='.$datdebpot.'&df='.$datfinpot.'","","menubar=no, status=no, scrollbars=yes , width=900, height=600");
                            //]]>
                            </script>';
	}//fin OKeleve
	
?>
<form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="post" >
<fieldset id="field7">
<legend id="legende">Par cr&eacute;neau horaire</legend>

<?php
/**********************
 affichage du formulaire *
************************/
        echo '<div>';
	echo '<p>Lister toutes les absences/retards pour les  creneaux s&eacute;lectionn&eacute;s.</p>';
	echo '<ul>';
	echo '<li>Date : <input id="cpe-abs" size="10" name="datecren" value="'.$datcreno.'" readonly="readonly" style="cursor: text" /></li>';
	echo '<li>Creneau(x) : ';
	$horaire = array("M1","M2","M3","M4","M5","S1","S2","S3","S4","S5");
        for ($h=0; $h<=9; $h++) 
		{
		echo ' <input type="checkbox" name="cren[]"   value="'.$horaire[$h].'"';if (in_array($horaire[$h], $tab_cren)) echo ' checked="checked"';
		echo ' />'.$horaire[$h];
		}
		echo '</li></ul>';
		echo '<input type="submit" name="OK" value="" class="bt-valid" />';
                echo '</div>';
?>

</fieldset>
<fieldset id="fields8">
<legend id="legend2">Par classe</legend>

<?php
        echo '<div>';
        include ("../Includes/data.inc.php");
	echo '<p>Lister les absences/retards d&acute;une classe pour une p&eacute;riode donn&eacute;e </p>';
	//affichage de la liste des classes
	echo '<ul>';
	echo '<li>Classe : <select name="Classe" style="background-color:#E6E6FA">';
	foreach ($classe as $cle => $valeur)
		  { 
		  echo '<option value="'.$valeur.'"';
		  if ($valeur==$ch) echo ' selected="selected"';
		  echo '>'.$valeur.'</option>';
		  }
	echo '</select></li>';
	echo '<li>p&eacute;riode du <input id="deb-abs" size="10" name="datecl_deb" value="'.$datdebcla.'" readonly="readonly" style="cursor: text"/>';
	echo '&nbsp;&nbsp;&nbsp;au &nbsp;&nbsp;&nbsp;<input id="fin-abs" size="10" name="datecl_fin" value="'.$datfincla.'" readonly="readonly" style="cursor: text"/></li>';
	echo '</ul>';
	echo '<input type="submit" name="OKclasse" value="" class="bt-valid-sel" />';
	if ($mess_dat!="") echo '<p class="erreur"> '.$mess_dat.'</p>';
        echo '</div>';
?>
</fieldset>
<fieldset id="field9">
<legend id="legendter">Par &eacute;l&egrave;ve</legend>

<?
        echo '<div>';
	echo '<p>Lister les absences/retards d&acute;un &eacute;l&egrave;ve pour une p&eacute;riode donn&eacute;e </p>';
	echo '<ul>';
	echo '<li>Nom : <input type = "text" style="background:#E6E6FA;" name ="nom" value = "'.$nom_propre.'" />&nbsp;&nbsp;&nbsp;Pr&eacute;nom : <input type = "text" style="background:#E6E6FA;" name ="prenom" value = "'.$prenom_propre.'" /></li>';
	echo '<li>Classe : <select name="division" style="background-color:#E6E6FA">';
		foreach ($classe as $cle => $valeur)
		  { 
		  echo '<option value="'.$valeur.'"';
		  if ($valeur==$ch2) echo 'selected="selected"';
		  echo '>'.$valeur.'</option>';
		  }
	echo '</select></li>';
	echo '<li>P&eacute;riode du <input id="datepot_deb" size="10" name="datepot_deb" value="'.$datdebpot.'" readonly="readonly" style="cursor: text"/>';
	echo '&nbsp;&nbsp;&nbsp;au &nbsp;&nbsp;&nbsp;<input id="datepot_fin" size="10" name="datepot_fin" value="'.$datfinpot.'" readonly="readonly" style="cursor: text"/></li>';
	echo '</ul>';
	echo '<input type="submit" name="OKeleve" value="" class="bt-valid"/>';
	if ($mess_error!="") echo '<p class="erreur"> '.$mess_error.'<p>';
        echo '</div>';
?>
</fieldset>
</form>
</body>
</html>
