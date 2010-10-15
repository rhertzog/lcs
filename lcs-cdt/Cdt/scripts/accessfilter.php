<?
/* ==================================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 2.1 du 4/6/2010
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script acces parents  -
			_-=-_
   =================================================== */
   
   
//fichiers nécessaires à l'exploitation de l'API
include "../Includes/basedir.inc.php";
include "$BASEDIR/lcs/includes/headerauth.inc.php";
include "$BASEDIR/Annu/includes/ldap.inc.php";
include "$BASEDIR/Annu/includes/ihm.inc.php";
include "../Includes/config.inc.php";  
include ("../Includes/functions2.inc.php");
include ('../Includes/data.inc.php');
require_once("../Includes/class.inputfilter_clean.php");
function SansAccent($texte){

$accent='ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËéèêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ';
$noaccent='AAAAAAaaaaaaOOOOOOooooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn';
$texte = strtr($texte,$accent,$noaccent);
return $texte;
} 

function calendar_auto_1($offset,$var_j,$var_m,$var_a,$tsmp)
//offset=nbre de jours / au timestmp ,var_j,var_m,var_a=nom des variables associées pour la bd ,$tsmp=timestamp
{ 
// Tableau indexé des jours
$jours = array (1 => '01', '02', '03', '04', '05','06', '07', '08', '09', '10', '11','12','13','14','15',
						'16','17','18','19','20','21','22','23','24','25','26','27','28','29','30','31');

// Tableau indexé des mois
  $mois = array (1 => '01', '02', '03', '04', '05', 
          '06', '07', '08', '09', '10', '11','12');
$dateinfo=getdate($tsmp);
$jo=date('d',$dateinfo['0']+($offset*86400));
$mo=date('m',$dateinfo['0']+($offset*86400));
$ann=date('Y',$dateinfo['0']+($offset*86400)); 
// Création des menus déroulants
 //les jours
  echo "<select name=$var_j>\n";
  foreach ($jours as $clé => $valeur)
  { echo "<option valeur=\"$clé\"";
  if ($clé==$jo) {echo 'selected';}
  echo ">$valeur</option>\n";
  }
  echo "</select>\n";
//les mois
  echo "<select name=$var_m>";
  foreach ($mois as $clé => $valeur)
  { echo "<option valeur=\"$clé\"";
  if ($clé==$mo) {echo 'selected';}
  echo ">$valeur</option>\n";
  }
  echo "</select>\n";
//les années
  echo "<select name=$var_a>";
  $annee =  (date('Y') - 25);
  while ($annee <= (date('Y') - 8))
  { echo "<option valeur=\"$annee\"";
  if ($annee==$ann) {echo 'selected';}
  echo ">$annee</option>\n";
    $annee++;
  }
  echo "</select>\n";
}


include ('../Includes/data.inc.php');
if (get_magic_quotes_gpc()) require_once("../Includes/class.inputfilter_clean.php");
else require_once '../Includes/htmlpur/library/HTMLPurifier.auto.php';

$tsmp=time();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<HEAD>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1" >
	<TITLE></TITLE>
	<meta name="generator" content="Bluefish 1.0.7">
	<META NAME="CREATED" CONTENT="20051226;22304481">
	<META NAME="CHANGED" CONTENT="20051226;22565970">
	<LINK href="../style/style.css" rel="stylesheet" type="text/css">
	</HEAD>
<BODY LANG="fr-FR" DIR="LTR">
<H1 class='title'>Acc&egrave;s au cahier de texte en ligne</H1>
<?

?>
<form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="post" >
<fieldset id="field7">
<legend id="legende">G&eacute;n&eacute;ration d'un lien d'acc&egrave;s direct</legend>

<?

//affichage du formulaire

	$text1 = ' <p>La consultation publique du cahier de texte en ligne est r&eacute;serv&eacute;e aux parents ayant des enfants scolaris&eacute;s dans l\'&eacute;tablissement</p>
<p>Apr&egrave;s l\'avoir compl&eacute;t&eacute;, ce formulaire vous fournira un lien d\'acc&egrave;s direct valable toute l\'ann&eacute;e scolaire.</p>
<p>Vous devrez <strong>enregistrer ce lien </strong> dans votre marque-pages ou vos favoris, pour ne pas avoir &agrave; refaire cette proc&eacute;dure.</p>
<p>&nbsp;</p>';
$text2 = '<p>A combien de cahiers de texte de classes, voulez vous acc&eacute;der ?<br>
<i>(Vous devrez fournir le nom,&nbsp;pr&eacute;nom et date de naissance d\'un enfant scolaris&eacute; dans chacune de ces classes.)</i></p>
';
if (!isset($_POST['Valider']))
	{
	echo '<H4>' . $text1.$text2.'
	<DIV ALIGN=LEFT>
	<select name="howmany">';

$x=array(1=> "1","2","3","4","5");
while(list($key, $val)=each($x)) {
	if (isset($howmany) && ($howmany == $val)) {
		echo ' <option value="'.$key.'" selected>'.$val.'</option>';
	} else {
		echo '<option value="'.$key.'">'.$val.'</option>';
	}
}

echo '</select>
	
	<input type="submit" name="Valider" value="Valider " >
	</H4></P></DIV>';
	}
//si clic sur le bouton Valider
if (isset($_POST['Valider']))
	{
	echo '<H4>' . $text1.'
	<DIV ALIGN=LEFT>';
	
	//si howmany (1er formulaire)
	if (isset($_POST['howmany']))
		{
		// afficher x selection d'eleves
				for ($loop = 1; $loop <= $_POST['howmany']; $loop++) 
				{
 				echo '
 				<input type="hidden" name="nbenrg" value= "'.$_POST['howmany'].'" >
 				Nom : <input type = "text" style="background:#E6E6FA;" name ="nom'.$loop.'" value = " ">
 				 &nbsp; Pr&eacute;nom&nbsp;:&nbsp;<input type = "text" style="background:#E6E6FA;" name ="prenom'.$loop.'" value = " ">
 				 ';  				 
  				echo "&nbsp;Date de naissance : ";calendar_auto_1(-9132,'jour_dn'.$loop,'mois_dn'.$loop,'an_dn'.$loop,$tsmp);
			 	//liste de selection de la classe
				echo "Classe&nbsp;:&nbsp;<select name='division".$loop."'>\n";
 				 foreach ($classe as $clé => $valeur)
  				{ echo "<option valeur=\"$valeur\"";
 				 if ($valeur==$val_classe) {echo 'selected';}
  				echo ">$valeur</option>\n";
  				}
  				echo "</select>\n";
			 	echo '<br>';
				}

		}
	//traitement du second formulaire 
	if (!isset($_POST['howmany']))
		{
		$clcrypt=array();
		$erreur=array();
		$Pb="false";
		 for ($indecs = 1; $indecs <= $_POST['nbenrg']; $indecs++)
		  {
		  $erreur[$indecs]="false";
		  //nettoyer les entrees
		  $nom_propre[$indecs]  =$_POST['nom'.$indecs];
		  $oMyFilter = new InputFilter($aAllowedTags, $aAllowedAttr, 0, 0, 1);
		  $nom_propre[$indecs] = $oMyFilter->process($nom_propre[$indecs]);
		  if (get_magic_quotes_gpc())
				    {
					$oMyFilter = new InputFilter($aAllowedTags, $aAllowedAttr, 0, 0, 1);
		  			$nom_propre[$indecs] = $oMyFilter->process($nom_propre[$indecs]);
					}
				else
					{
					// htlmpurifier
					$config = HTMLPurifier_Config::createDefault();
			    	$config->set('Core.Encoding', 'ISO-8859-15'); 
			    	$config->set('HTML.Doctype', 'HTML 4.01 Transitional');
			   		$purifier = new HTMLPurifier($config);
			   		$nom_propre[$indecs] = $purifier->purify($nom_propre[$indecs]);
			   		}
		  $nom_propre[$indecs] = SansAccent($nom_propre[$indecs]);
		  $nom_propre[$indecs] = mb_ereg_replace("^[[:blank:]]","",$nom_propre[$indecs]);
		  $nom_propre[$indecs] = mb_ereg_replace("[[:blank:]]$","",$nom_propre[$indecs]);
		  $nom_propre1[$indecs] = mb_ereg_replace("'|[[:blank:]]","_",$nom_propre[$indecs]);
		  $nom_propre2[$indecs] = mb_ereg_replace("'","_",$nom_propre[$indecs]);
		  $nom_propre1[$indecs] = StrToLower($nom_propre1[$indecs]);
		  $nom_propre2[$indecs] = StrToLower($nom_propre2[$indecs]);
		  $nom_propre1[$indecs] = strip_tags(stripslashes($nom_propre1[$indecs]));
		  $nom_propre2[$indecs] = strip_tags(stripslashes($nom_propre2[$indecs]));
		  $prenom_propre[$indecs]  =$_POST['prenom'.$indecs];
		  if (get_magic_quotes_gpc())
				    {
					$oMyFilter = new InputFilter($aAllowedTags, $aAllowedAttr, 0, 0, 1);
		  			$prenom_propre[$indecs] = $oMyFilter->process($prenom_propre[$indecs]);
					}
				else
					{
					// htlmpurifier
					$config = HTMLPurifier_Config::createDefault();
			    	$config->set('Core.Encoding', 'ISO-8859-15'); 
			    	$config->set('HTML.Doctype', 'HTML 4.01 Transitional');
			   		$purifier = new HTMLPurifier($config);
			   		$prenom_propre[$indecs] = $purifier->purify($prenom_propre[$indecs]);
			   		}
		  $prenom_propre[$indecs] = SansAccent($prenom_propre[$indecs]);
		  $prenom_propre[$indecs] = mb_ereg_replace("^[[:blank:]]","",$prenom_propre[$indecs]);
		  $prenom_propre[$indecs] = mb_ereg_replace("[[:blank:]]$","",$prenom_propre[$indecs]);
		  $prenom_propre[$indecs] = mb_ereg_replace("'|[[:blank:]]","_",$prenom_propre[$indecs]);
		  $prenom_propre[$indecs] = StrToLower($prenom_propre[$indecs]);
		  $prenom_propre[$indecs] = strip_tags(stripslashes($prenom_propre[$indecs]));	  
		  //vérifier la présence des eleves dans les classes 
		  //1.recherche de la classe dans le ldap
		  $groups=search_groups('cn=classe*');

		  if (count($groups))
		  	{    
			for ($loup=0; $loup < count($groups); $loup++)
			        {
			        $cla = $_POST['division'.$indecs];
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

		//recherche de(s) uids correspondants au nom saisi 
		  $le_cn[$indecs] = $prenom_propre[$indecs]." ".$nom_propre1[$indecs];
		  $filtre ="(cn=$le_cn[$indecs])";
		  $gus= search_people ($filtre);
		  //echo count($gus).$nom_propre2[$indecs].$gus[0]["uid"];
		  //echo people_get_datenaissance($gus[0]["uid"]);exit;
		//si pas d'occurence recherche avec nom2
		if (count($gus)==0){
		$le_cn[$indecs] = $prenom_propre[$indecs]." ".$nom_propre2[$indecs];
		  $filtre ="(cn=$le_cn[$indecs])";
		  $gus= search_people ($filtre);
		}		
		  if (count($gus)) 
   		 						{
    								for ($loop=0; $loop < count($gus); $loop++) 
    									{	 
    									$uidgus= $gus[$loop]["uid"];
    									for ($iteration = 0; $iteration <= count($membres); $iteration++) 
    										{
 											if ($membres[$iteration]["uid"] ==  $uidgus) 
 												{
 												if (people_get_datenaissance ($uidgus) == $_POST['an_dn'.$indecs].$_POST['mois_dn'.$indecs].$_POST['jour_dn'.$indecs])
 													{ 																				
 													$clcrypt[$indecs] =crypt(substr($full_classe,-8,8),$Grain);
 													$clcrypt[$indecs]=substr($clcrypt[$indecs],2);
 													$uidcrypt[$indecs]=substr(md5($uidgus),2,5).substr(md5($uidgus),-5,5);
 													}
 													else
 													{
 													//ça match pas
 													//echo "pb pour ".$indecs;
 													 //$erreur[$indecs]="true";
 													 //$Pb="true";echo $erreur[$indecs];
 													}//endif ddn
 								
 												}//uids différents (endif)
 											}//fin de recherche de correspondance dans la classe pour un uid(iteration)

    									}//fin de recherche pour tous les uids(loop)
    									
    								}
    								//ya pas de gus avec cet uid dans la classe;(endif count)
    								else 
    								{
    								//$erreur[$indecs]="true";
 									// $Pb="true";
    								}
    								
		 							 if (!$clcrypt[$indecs]) 
    										{
    										$erreur[$indecs]="true";
    										$Pb="true";
    										}
    									
		  		}//fin de boucle  traitement (indecs)
		  		
		  		if ($Pb=="true")
		  		{ 
		  		echo " Aucune correspondance n'a &eacute;t&eacute; trouv&eacute;e pour : ";
				for ($a = 1; $a <= $_POST['nbenrg']; $a++) 
					{
					
 					if ($erreur[$a]=="true") echo $prenom_propre[$a]."  ";
					}	
					echo "<br><br>Veuillez v&eacute;rifier les donn&eacute;es saisies dans le formulaire  : <a href='javascript:history.back()'>Retour au formulaire</a>"; 	  		
		  		}
				else
				{
				echo "<a href= '../index.php";
				for ($a = 1; $a <= $_POST['nbenrg']; $a++) 
					{
					if ($a==1) echo "?cl".$a."=".$clcrypt[$a];
					else echo "&cl".$a."=".$clcrypt[$a];
				 echo "&ef".$a."=".$uidcrypt[$a];
					}
				echo "'> Acc&egrave;s direct au cahier de texte </a>";
				}
		}//fin du traitement du formulaire
	
	if (($Pb!="true") && (isset($_POST['howmany']))) echo '<p><input type="submit" name="Valider" value="Valider " >
	</H4></P></DIV>';
	}
?>
</fieldset>	
</FORM>
</BODY>
</HTML>



