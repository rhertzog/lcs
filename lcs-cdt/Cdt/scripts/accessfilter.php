<?
/* ==================================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 2.3 du 06/01/2011
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script acces parents  -
			_-=-_
    "Valid XHTML 1.0 Strict"
   =================================================== */
   
//fichiers nécessaires à l'exploitation de l'API
include "../Includes/basedir.inc.php";
include "$BASEDIR/lcs/includes/headerauth.inc.php";
include "$BASEDIR/Annu/includes/ldap.inc.php";
include "$BASEDIR/Annu/includes/ihm.inc.php";
include "../Includes/config.inc.php";  
include "../Includes/functions2.inc.php";
include '../Includes/data.inc.php';
include "../Includes/phpqrcode/qrlib.php";
require_once "../Includes/class.inputfilter_clean.php";

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
  echo '<select name="'.$var_j.'">';
  foreach ($jours as $cle => $valeur)
      {
      echo "<option value=\"$valeur\"";
      if ($valeur==$jo) {echo ' selected="selected"';}
      echo ">$valeur</option>\n";
      }
  echo "</select>\n";
//les mois
  echo  '<select name="'.$var_m.'">';
      foreach ($mois as $cle => $valeur)
      {
      echo "<option value=\"$valeur\"";
      if ($valeur==$mo) {echo ' selected="selected"';}
      echo ">$valeur</option>\n";
      }
  echo "</select>\n";
//les années
  echo '<select name="'.$var_a.'">';
  $annee =  (date('Y') - 25);
  while ($annee <= (date('Y') - 8))
      {
      echo "<option value=\"$annee\"";
      if ($annee==$ann) {echo ' selected="selected"';}
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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html  xmlns="http://www.w3.org/1999/xhtml" >
<head>
        <meta name="author" content="Philippe LECLERC -TICE CAEN" />
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<title></title>
	<link href="../style/style.css" rel="stylesheet" type="text/css" />
	
	<!--[if IE]>
<link href="../style/style-ie.css"  rel="stylesheet" type="text/css"/>
<![endif]-->
	</head>
<body>
<h1 class='title'>Acc&egrave;s au cahier de texte en ligne</h1>
<form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="post" >
<fieldset id="field7">
<legend id="legende">G&eacute;n&eacute;ration d'un lien d'acc&egrave;s direct</legend>

<?php

//affichage du formulaire

$text1 = ' <p>La consultation publique du cahier de texte en ligne est r&eacute;serv&eacute;e aux parents ayant des enfants scolaris&eacute;s dans l\'&eacute;tablissement</p>
<p>Apr&egrave;s l\'avoir compl&eacute;t&eacute;, ce formulaire vous fournira un lien d\'acc&egrave;s direct valable toute l\'ann&eacute;e scolaire.</p>
<p>Vous devrez <strong>enregistrer ce lien </strong> dans votre marque-pages ou vos favoris, pour ne pas avoir &agrave; refaire cette proc&eacute;dure.</p>';
$text2 = '<p>A combien de cahiers de texte de classes, voulez vous acc&eacute;der ?<br />
<i>(Vous devrez fournir le nom,&nbsp;pr&eacute;nom et date de naissance d\'un enfant scolaris&eacute; dans chacune de ces classes.)</i></p>';
if (!isset($_POST['Valider']))
	{
	echo '<div class="agauche">' . $text1.$text2.'
	<div>
	<select name="howmany">';
        $x=array(1=> "1","2","3","4","5");
        while(list($key, $val)=each($x)) {
                if (isset($howmany) && ($howmany == $val)) {
                        echo ' <option value="'.$key.'" selected ="selected">'.$val.'</option>';
                } else {
                        echo '<option value="'.$key.'">'.$val.'</option>';
                }
        }
        echo '
            </select>
            </div>
            <p><input type="submit" name="Valider" value="Valider " /></p>
            </div>';
	}
//si clic sur le bouton Valider
if (isset($_POST['Valider']))
	{
	echo '<div class="agauche">' . $text1.'
	<div>';
	
	//si howmany (1er formulaire)
	if (isset($_POST['howmany']))
		{
		// afficher x selection d'eleves
				for ($loop = 1; $loop <= $_POST['howmany']; $loop++) 
				{
 				echo '
 				<input type="hidden" name="nbenrg" value= "'.$_POST['howmany'].'" />
 				Nom : <input type = "text" style="background:#E6E6FA;" name ="nom'.$loop.'" value = ""/>
 				 &nbsp; Pr&eacute;nom&nbsp;:&nbsp;<input type = "text" style="background:#E6E6FA;" name ="prenom'.$loop.'" value = ""/> ';  				 
  				echo "&nbsp;Date de naissance : ";calendar_auto_1(-9132,'jour_dn'.$loop,'mois_dn'.$loop,'an_dn'.$loop,$tsmp);
			 	//liste de selection de la classe
				echo "Classe&nbsp;:&nbsp;<select name='division".$loop."'>\n";
 				 foreach ($classe as $cle => $valeur)
  				{ echo "<option value=\"$valeur\"";
 				 if ($valeur==$val_classe) {echo ' selected="selected"';}
  				echo ">$valeur</option>\n";
  				}
  				echo "</select>\n";
			 	echo '<br />';
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
		  $nom_propre[$indecs]  =utf8_decode($_POST['nom'.$indecs]);
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
                        $config->set('HTML.Doctype', 'XHTML 1.0 Strict');
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
		  $prenom_propre[$indecs]  =utf8_decode($_POST['prenom'.$indecs]);
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
                        $config->set('HTML.Doctype', 'XHTML 1.0 Strict');
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
                                if (mb_ereg("(_$cla)$",$groups[$loup]["cn"]) || ($cla == $groups[$loup]["cn"]))
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

                                                        }//uids différents (endif)
                                                }//fin de recherche de correspondance dans la classe pour un uid(iteration)
                                }//fin de recherche pour tous les uids(loop)
                        }
                        //ya pas de gus avec cet uid dans la classe;(endif count)
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
                                    echo "<br /><br />Veuillez v&eacute;rifier les donn&eacute;es saisies dans le formulaire  : <a href='javascript:history.back()'>Retour au formulaire</a></div></div>";
                            }
                        else
                            {
                            echo '<span id="legend2">';
                           echo "<a href= '../index.php";
                            for ($a = 1; $a <= $_POST['nbenrg']; $a++)
                                    {
                                    if ($a==1) $UrlParent.= "?cl".$a."=".$clcrypt[$a];
                                    else $UrlParent.= "&amp;cl".$a."=".$clcrypt[$a];
                                    $UrlParent.= "&amp;ef".$a."=".$uidcrypt[$a];
                                    }
                           echo  $UrlParent;
                           echo "'> Acc&egrave;s direct au cahier de texte </a> ";
                           echo '</span>';
                            echo "<br /><br /><span class='reserve'> &nbsp;( ajoutez le lien ci-dessus &#224; vos favoris/marques-pages en faisant un clic droit dessus )&nbsp; </span> </div> ";
                            
                            $content= $baseurl.'Plugins/Cdt/index.php'.$UrlParent;
                            $content=str_replace("&amp;", "*amp*", $content);
                            $filename = 'qrcode.png';
                            $errorCorrectionLevel ='L';
                            $matrixPointSize = 3;
                            //QRcode::png($content, $filename, $errorCorrectionLevel, $matrixPointSize, 2);
                            echo "<div>  </br>";
                            echo '<img src="../Includes/phpqrcode/genRqrCode.php?qrurl='.$content.'" alt="" />';
                             echo "<br /><br /><span class='reserve'> &nbsp;( Acc&#233dez au cahier de textes avec votre smartphone ou votre tablette, en scannant le QRcode ci-dessus  )&nbsp; </span> </div> ";
                            echo " </div></div>";
                            
                            }
        }//fin du traitement du formulaire
	if (($Pb!="true") && (isset($_POST['howmany']))) 
        echo '</div><p><input type="submit" name="Valider" value="Valider " /></p></div>';
	}
 ?>
</fieldset></form>
<?php
include ('../Includes/pied.inc');
?>
</body>
</html>
<?php
//if (file_exists($filename)) unlink($filename);
?>