<?
/* ==================================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 0.3 du 19/12/06
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script de redirection -
			_-=-_
   =================================================== */
session_name("Cdt_Lcs");
@session_start();
if (isset($_SESSION['saclasse'])) unset($_SESSION['saclasse']);
// Inclusion des fonctions de l'API-LCS
include ("/var/www/lcs/includes/user_lcs.inc.php");
include ("/var/www/lcs/includes/functions.inc.php");
include ("./Includes/functions2.inc.php");

//Inclusion de la liste des classes
include ('./Includes/data.inc.php'); 

//version de PHP >= 4.3.2 ?
	ereg("^([0-9]).([0-9]).([0-9]{1,2})",phpversion(),$chif );
	If (($chif[1]>4) ||($chif[1]=4 && $chif[2]>3) ||($chif[1]=4 && $chif[2]=3 && $chif[3]>=2))
	$_SESSION['version']=">=432";
	else $_SESSION['version']="<432";
	
// r�cup�ration des donn�es de l'utilisateur

	$login = auth_lcs(); 
	
// Si $login, on r�cup�re les datas de l'utilisateur
	if ($login) 
	{
		list($user, $groups)=people_get_variables($login, true);
 		$_SESSION['login']=$login;
		$_SESSION['name']=$user["nom"];
		$_SESSION['nomcomplet']=$user["fullname"];
		
		if (is_prof($login)) { $_SESSION['cequi']="prof";}
		elseif (is_eleve($login)) 
		{ 
			$_SESSION['cequi']="eleve";
			if (count($groups))
				{
				for ($loop=0;$loop<count($groups);$loop++)
					{
					if (ereg("^Classe",$groups[$loop]["cn"]))
					//recherche d'une occurence dans le fichier des classes
					for($n=0; $n<count($classe); $n++)
						{
						if (ereg("($classe[$n])$",$groups[$loop]["cn"]))
						{
						$_SESSION['saclasse'][1]=$classe[$n];
						break;
						}
						else $_SESSION['saclasse'][1]="";
						}
					}
				}
		}
		elseif (is_administratif($login)) { $_SESSION['cequi']="administratif";}
	
			
	//redirection d'acc�s 

		if ($_SESSION['login']=="admin") 
			{ 
			header("location: ./scripts/fichier_classes.php");exit;
			}
		elseif ($_SESSION['cequi']!="prof")
			{ 
			header("location: ./scripts/cahier_text_eleve.php");exit;
			}
		else 
			{
			header("location: ./scripts/cahier_texte_prof.php");exit;
			}
	}
	//si pas de login
	
	elseif (isset($_GET['cl1']))	
		{
		for ($x = 1; $x <= 5; $x++)
			{
 			if (isset($_GET['cl'.$x]))
 				{
 				if (decripte_classe($_GET['cl'.$x]) !="" ) 
 					{
 					$_SESSION['saclasse'][$x]=decripte_classe($_GET['cl'.$x]);
 					$_SESSION['parentde'][$x]=decripte_uid($_GET['ef'.$x],$_SESSION['saclasse'][$x]);
 					}
 				}
			}
		if (isset( $_SESSION['saclasse'])) 
			{
			header("location: ./scripts/cahier_text_eleve.php");
			exit;
			}
		else
			{
			header ("location: ./scripts/accessfilter.php");
			exit;
			}
		}
	
	else 
		{		
 		header ("location: ./scripts/accessfilter.php");exit;
		}

?>