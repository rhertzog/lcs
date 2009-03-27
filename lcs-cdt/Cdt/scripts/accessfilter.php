<?

/* ==================================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 1.0 du 29/10/2008
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script gestion absences  -
			_-=-_
   =================================================== */
   
   
//fichiers n�cessaires � l'exploitation de l'API
include "../Includes/basedir.inc.php";
include "$BASEDIR/lcs/includes/headerauth.inc.php";
include "$BASEDIR/Annu/includes/ldap.inc.php";
include "$BASEDIR/Annu/includes/ihm.inc.php";
include "../Includes/config.inc.php";  
include ("../Includes/functions2.inc.php");
function SansAccent($texte){

$accent='�����������������������������������������������������';
$noaccent='AAAAAAaaaaaaOOOOOOooooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn';
$texte = strtr($texte,$accent,$noaccent);
return $texte;
} 

function calendar_auto($offset,$var_j,$var_m,$var_a,$tsmp)
//offset=nbre de jours / au timestmp ,var_j,var_m,var_a=nom des variables associ�es pour la bd ,$tsmp=timestamp
{ 
// Tableau index� des jours
$jours = array (1 => '01', '02', '03', '04', '05','06', '07', '08', '09', '10', '11','12','13','14','15',
						'16','17','18','19','20','21','22','23','24','25','26','27','28','29','30','31');

// Tableau index� des mois
  $mois = array (1 => '01', '02', '03', '04', '05', 
          '06', '07', '08', '09', '10', '11','12');
$dateinfo=getdate($tsmp);
$jo=date('d',$dateinfo['0']+($offset*86400));
$mo=date('m',$dateinfo['0']+($offset*86400));
$ann=date('Y',$dateinfo['0']+($offset*86400)); 
// Cr�ation des menus d�roulants
 //les jours
  echo "<select name=$var_j>\n";
  foreach ($jours as $cl� => $valeur)
  { echo "<option valeur=\"$cl�\"";
  if ($cl�==$jo) {echo 'selected';}
  echo ">$valeur</option>\n";
  }
  echo "</select>\n";
//les mois
  echo "<select name=$var_m>";
  foreach ($mois as $cl� => $valeur)
  { echo "<option valeur=\"$cl�\"";
  if ($cl�==$mo) {echo 'selected';}
  echo ">$valeur</option>\n";
  }
  echo "</select>\n";
//les ann�es
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

/*********************/
function people_get_datenaissance ($uid)
{
  global $ldap_server, $ldap_port, $dn;
  global $error;
  $error="";

  // LDAP attribute
  $ldap_people_attr = array(
    "uid",				// login
    "gecos"				// Date de naissance,Sexe (F/M),
  );
  $ds = @ldap_connect ( $ldap_server, $ldap_port );
  if ( $ds ) {
    $r = @ldap_bind ( $ds ); // Bind anonyme
    if ($r) {
      $result = @ldap_read ( $ds, "uid=".$uid.",".$dn["people"], "(objectclass=posixAccount)", $ldap_people_attr );
      if ($result) {
        $info = @ldap_get_entries ( $ds, $result );
        if ( $info["count"]) {
          // Traitement du champ gecos pour extraction de date de naissance
          $gecos = $info[0]["gecos"][0];
          $tmp = split ("[\,\]",$info[0]["gecos"][0],4);
          $ddn = $tmp[1];
             }
        @ldap_free_result ( $result );
      }
       
    @ldap_close ( $ds );
  } else {
    $error = "Erreur de connection au serveur LDAP";
  }
}
  return $ddn;
}
/*********************/



//inclusion fichier liste des classes
include ('../Includes/data.inc.php');
$tsmp=time();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<HEAD>
	<meta http-equiv="content-type" content="tional//E; charset=windows-1252" >
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
<fieldset>
<legend>
<FONT FACE="Trebuchet MS, sans-serif" color="#009933"> G&eacute;n&eacute;ration d'un lien d'acc&egrave;s direct  </FONT>
</legend>

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
  				echo "&nbsp;Date de naissance : ";calendar_auto(-9132,'jour_dn'.$loop,'mois_dn'.$loop,'an_dn'.$loop,$tsmp);
			 	//liste de selection de la classe
				echo "Classe&nbsp;:&nbsp;<select name='division".$loop."'>\n";
 				 foreach ($classe as $cl� => $valeur)
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
		  $nom_propre[$indecs] = SansAccent($_POST['nom'.$indecs]);
		  $nom_propre[$indecs] = ereg_replace("^[[:blank:]]","",$nom_propre[$indecs]);
		  $nom_propre[$indecs] = ereg_replace("[[:blank:]]$","",$nom_propre[$indecs]);
		  $nom_propre[$indecs] = ereg_replace("'|[[:blank:]]","_",$nom_propre[$indecs]);
		  $nom_propre[$indecs] = StrToLower($nom_propre[$indecs]);
		  $nom_propre[$indecs] = strip_tags(stripslashes($nom_propre[$indecs]));
		  $prenom_propre[$indecs] = SansAccent($_POST['prenom'.$indecs]);
		  $prenom_propre[$indecs] = ereg_replace("^[[:blank:]]","",$prenom_propre[$indecs]);
		  $prenom_propre[$indecs] = ereg_replace("[[:blank:]]$","",$prenom_propre[$indecs]);
		  $prenom_propre[$indecs] = ereg_replace("'|[[:blank:]]","_",$prenom_propre[$indecs]);
		  $prenom_propre[$indecs] = StrToLower($prenom_propre[$indecs]);
		  $prenom_propre[$indecs] = strip_tags(stripslashes($prenom_propre[$indecs]));
		  
		  
		  //v�rifier la pr�sence des eleves dans les classes 
		  //1.recherche de la classe dans le ldap
		  $groups=search_groups('cn=classe*');

		  if (count($groups))
		  	{    
			for ($loup=0; $loup < count($groups); $loup++)
			        {
			        $cla = $_POST['division'.$indecs];
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

		//recherche de(s) uids correspondants au nom saisi 
		  $le_cn[$indecs] = $prenom_propre[$indecs]." ".$nom_propre[$indecs];
		  $filtre ="(cn=$le_cn[$indecs])";
		  $gus= search_people ($filtre);
		
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
 													$uidcrypt[$indecs]=substr(md5($uidgus),2,5).substr(md5($uidgus),-5,5);
 													}
 													else
 													{
 													//�a match pas
 													//echo "pb pour ".$indecs;
 													 //$erreur[$indecs]="true";
 													 //$Pb="true";echo $erreur[$indecs];
 													}//endif ddn
 								
 												}//uids diff�rents (endif)
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
					
 					if ($erreur[$a]=="true") echo $_POST['prenom'.$a]."  ";
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


