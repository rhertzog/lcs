<?
/* ==================================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 1.0 du 29/10/2008
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script de consultation des absences -
			_-=-_
   =================================================== */
session_name("Cdt_Lcs");
@session_start();
include "../Includes/functions2.inc.php";
include "../Includes/fonctions.inc.php";
//fichiers nécessaires à l'exploitation de l'API
	$BASEDIR="/var/www";
	//include "../Includes/basedir.inc.php";
	include "$BASEDIR/lcs/includes/headerauth.inc.php";
	include "$BASEDIR/Annu/includes/ldap.inc.php";
	include "$BASEDIR/Annu/includes/ihm.inc.php";  
	
//si la page est appelée par un utilisateur non identifié ou non autorise
if (!isset($_SESSION['login'])) exit;
elseif ((ldap_get_right("Cdt_is_cpe",$_SESSION['login'])=="N") &&  (ldap_get_right("Cdt_can_sign",$_SESSION['login'])=="N")) exit;

//Connexion à la base de données
require_once ('../Includes/config.inc.php');

function SansAccent($texte){

$accent='ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËéèêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ';
$noaccent='AAAAAAaaaaaaOOOOOOooooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn';
$texte = strtr($texte,$accent,$noaccent);
return $texte;
}  

function date_incoherente($date1,$date2){
$splidat1=split('-',$date1);
$newdate1=$splidat1[2].$splidat1[1].$splidat1[0];
$splidat2=split('-',$date2);
$newdate2=$splidat2[2].$splidat2[1].$splidat2[0];
if ($newdate1>$newdate2) return true; else return false;
}
 
$tsmp=time();
$tsmp2=time() -604800;//j-7
//dates  au jour courant
if (!isset($_POST['datecren'])) {$datcreno=date('d-m-Y',$tsmp);} else 
	{
 	$datcreno=$_POST['datecren'];
 	$Morceauc=split('-',$_POST['datecren']);
	$dtcrensql= $Morceauc[2]."-".$Morceauc[1]."-".$Morceauc[0];
 	}
if (!isset($_POST['datecl_fin'])) {$datfincla=date('d-m-Y',$tsmp);} else
	{ $datfincla=$_POST['datecl_fin'];}
	
if (!isset($_POST['datepot_fin'])) {$datfinpot=date('d-m-Y',$tsmp);} else { $datfinpot=$_POST['datepot_fin'];}

//dates à j-7	
if (!isset($_POST['datecl_deb'])) {$datdebcla=date('d-m-Y',$tsmp2);} else	{$datdebcla=$_POST['datecl_deb'];}
if (!isset($_POST['datepot_deb'])) {$datdebpot=date('d-m-Y',$tsmp2);} else	{$datdebpot=$_POST['datepot_deb'];}



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
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<HEAD>
	<META HTTP-EQUIV="CONTENT-TYPE" CONTENT="text/html; charset=windows-1252">
	<TITLE></TITLE>
	<meta name="generator" content="Bluefish 1.0.7">
	<META NAME="CREATED" CONTENT="20051226;22304481">
	<META NAME="CHANGED" CONTENT="20051226;22565970">
	<LINK href="../style/style.css" rel="stylesheet" type="text/css">
	</HEAD>
<BODY LANG="fr-FR" DIR="LTR">
<SCRIPT language="Javascript" src="../Includes/barre_java.js"></SCRIPT>
<div style="position:absolute; top:0px; left:100px;z-index:3;">
<table class="ds_box" cellpadding="0" cellspacing="0" id="ds_conclass" style="display: none;">
<tr><td id="ds_calclass">
</td></tr>
</table>
</div>
<SCRIPT language="Javascript" src="../Includes/calend.js"></script>
<H1 class='title'>Consultation des absences</H1>
<?
/**************************************************
* positionnement du flag de restrict dans le fichier de conf *
***************************************************/
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
		echo '<SCRIPT language="javascript">
         window.open("./bilancpe2.php?kr='.$kreno.'&dkr='.$_POST['datecren'].'","","menubar=no, status=no, scrollbars=yes , width=900, height=600");
			</SCRIPT>';

	
	
	
	}// fin OK
	
if (isset($_POST['OKclasse']))
	{
	$mess_dat="";
	if (date_incoherente($datdebcla,$datfincla)) $mess_dat=" Incoh&eacute;rence dans les dates ";
	else {
	echo '<SCRIPT language="javascript">
         window.open("./bilancpe.php?kl='.$ch.'&dd='.$datdebcla.'&df='.$datfincla.'","","menubar=no, status=no, scrollbars=yes , width=900, height=600");
			</SCRIPT>';
	}
	}//fin OKclasse

if (isset($_POST['OKeleve']))
	{
		$nom_propre = SansAccent($_POST['nom']);
		 $nom_propre = ereg_replace("^[[:blank:]]","",$nom_propre);
		 $nom_propre = ereg_replace("[[:blank:]]$","",$nom_propre);
		 $nom_propre = ereg_replace("'|[[:blank:]]","_",$nom_propre);
		 $nom_propre = StrToLower($nom_propre);
		 $nom_propre = strip_tags(stripslashes($nom_propre));
		 $prenom_propre = SansAccent($_POST['prenom']);
		 $prenom_propre = ereg_replace("^[[:blank:]]","",$prenom_propre);
		 $prenom_propre= ereg_replace("[[:blank:]]$","",$prenom_propre);
		 $prenom_propre = ereg_replace("'|[[:blank:]]","_",$prenom_propre);
		 $prenom_propre = StrToLower($prenom_propre);
		 $prenom_propre = strip_tags(stripslashes($prenom_propre));
		  
		  
		  //vérifier la présence des eleves dans les classes 
		  //1.recherche de la classe dans le ldap
		  $groups=search_groups('cn=classe*');

		  if (count($groups))
		  	{    
			for ($loup=0; $loup < count($groups); $loup++)
			        {
			        $cla = $_POST['division'];
						if (ereg("($cla)$",$groups[$loup]["cn"]))
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
 												}//uids différents (endif)
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
    		echo '<SCRIPT language="javascript">
         window.open("./bilancpe.php?uid='.$uidpotache.'&fn='.$le_cn.'&dd='.$datdebpot.'&df='.$datfinpot.'","","menubar=no, status=no, scrollbars=yes , width=900, height=600");
			</SCRIPT>';	

	
	}//fin OKeleve
	
?>
<form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="post" enctype="multipart/form-data">
<fieldset>
<legend>
<FONT FACE="Cursive" color="#486591">   Par cr&eacute;neau horaire&nbsp </FONT>
</legend>
<DIV ALIGN=LEFT>
<?
/**********************
 affichage du formulaire *
************************/

			echo '<H4>Lister toutes les absences/retards pour les  creneaux s&eacute;lectionn&eacute;s . </h4>
			<H4>Date : <input onclick="ds_sh(this);" size="9" name="datecren" value="'.$datcreno.'"readonly="readonly" style="cursor: text" />&nbsp;&nbsp;Creneau(x) : ';
			$horaire = array("M1","M2","M3","M4","M5","S1","S2","S3","S4","S5");
	for ($h=0; $h<=9; $h++) 
		{
		echo ' <input type="checkbox" name="cren[]"   value="'.$horaire[$h].'"';if (in_array($horaire[$h], $tab_cren)) echo 'checked';
		echo ' >'.$horaire[$h];
		}
		echo "</H4><input type='submit' name='OK' value='Valider la selection' >";
			
	

?>
</div>
</fieldset>
<fieldset>
<legend>
<FONT FACE="Cursive" color="#486591"> Par classe </FONT>
</legend>
<DIV ALIGN=LEFT>
<?
echo '<H4>Lister les absences/retards d&acute;une classe pour une p&eacute;riode donn&eacute;e </H4>';
//affichage de la liste des classes
include ("../Includes/data.inc.php");
		
			echo "<H4>Classe : <select name='Classe' style='background-color:#E6E6FA'></h4>";
			foreach ($classe as $clé => $valeur)
			  { 
			  echo "<option valeur=\"$valeur\"";
			  if ($valeur==$ch) {echo 'selected';}
			  echo ">$valeur</option>\n";
			  }
			  echo "</select>\n";
			  echo ' pour la p&eacute;riode du <input onclick="ds_sh(this);" size="9" name="datecl_deb" value="'.$datdebcla.'"readonly="readonly" style="cursor: text"/>';
		echo '&nbsp;&nbsp;&nbsp;au &nbsp;&nbsp;&nbsp;<input onclick="ds_sh(this);" size="9" name="datecl_fin" value="'.$datfincla.'"readonly="readonly" style="cursor: text"/></H4>';
		echo "&nbsp;&nbsp;<input type='submit' name='OKclasse' value='Valider ce choix' >&nbsp;:&nbsp;";
		if ($mess_dat!="") echo '<FONT color="#F71B00"> '.$mess_dat.'</font></H4>';
?>
</div>
</fieldset>
<fieldset>
<legend>
<FONT FACE="Cursive" color="#486591"> Par &eacute;l&egrave;ve </FONT>
</legend>
<DIV ALIGN=LEFT>
<?
echo '<H4>Lister les absences/retards d&acute;un &eacute;l&egrave;ve pour une p&eacute;riode donn&eacute;e </H4>';
	echo '<H4>Nom : <input type = "text" style="background:#E6E6FA;" name ="nom" value = "'.$nom_propre.' ">
 				 &nbsp; Pr&eacute;nom&nbsp;:&nbsp;<input type = "text" style="background:#E6E6FA;" name ="prenom" value = "'.$prenom_propre.' ">';
 				 echo " &nbsp; Classe : <select name='division' style='background-color:#E6E6FA'></h4>";
			foreach ($classe as $clé => $valeur)
			  { 
			  echo "<option valeur=\"$valeur\"";
			  if ($valeur==$ch2) {echo 'selected';}
			  echo ">$valeur</option>\n";
			  }
			  echo "</select>\n";
			  echo ' pour la p&eacute;riode du <input onclick="ds_sh(this);" size="9" name="datepot_deb" value="'.$datdebpot.'"readonly="readonly" style="cursor: text"/>';
		echo '&nbsp;&nbsp;&nbsp;au &nbsp;&nbsp;&nbsp;<input onclick="ds_sh(this);" size="9" name="datepot_fin" value="'.$datfinpot.'"readonly="readonly" style="cursor: text"/></H4>';
	echo "<input type='submit' name='OKeleve' value='Valider' >";
	if ($mess_error!="") echo '<FONT color="#F71B00"> '.$mess_error.'</font></H4>';
?>
</div>
</fieldset>

</FORM>
</BODY>
</HTML>



