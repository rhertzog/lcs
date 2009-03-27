<?
/* ==================================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 1.0 du 29/10/2008
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script de mise a jour du post-it-
			_-=-_
   =================================================== */
   
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header("Expires: " . gmdate("D, d M Y H:i:s") . " GMT");
	header("Cache-Control: no-cache, must-revalidate");
	header("Pragma: no-cache");
   
session_name("Cdt_Lcs");
@session_start();
//si la page est appelée par un utilisateur non identifié
if (!isset($_SESSION['login']) )exit;

//si la page est appelée par un utilisateur non prof
elseif ($_SESSION['cequi']!="prof") exit;
?>
<HTML>
<BODY LANG="fr-FR" DIR="LTR">
<?
//mise a jour du commentaire dans la bdd (2eme passe) 
if (isset($_GET['contenu']) AND isset($_GET['clas_activ'])) 
	 {
  	// Connexion à la base de données
	require_once ('../Includes/config.inc.php');
	//Créer la requête pour la mise à jour des données	
	
			$cible= ($_GET['clas_activ']);
			$cont=$_GET['contenu'];
			$rq = "UPDATE  onglets SET postit='$cont'
				WHERE id_prof='$cible'";
		
	// lancer la requête
		$result = mysql_query($rq); 
		if (!$result)  // Si l'enregistrement est incorrect
			{                           
			 echo "<p>Votre rubrique n'a pas pu être enregistrée à cause d'une erreur système".
			 "<p></p>" . mysql_error() . "<p></p>";
			// refermer la connexion avec la base de données
			mysql_close();
			//sortir	
			exit();
			}
			echo ' OK ';
		echo ' <SCRIPT language="Javascript">
					<!--	
					window.close() 
					// -->
					</script>';		
  
	} 
	else
	{
	//1ere passe 
  	if (isset($_GET['rubrique']))
		{
		$ru=($_GET['rubrique']);	
	  echo "<script type=\"text/javascript\">\n";
	  echo "  location.href=\"${_SERVER['SCRIPT_NAME']}?${_SERVER['QUERY_STRING']}"
            . "&contenu=\" + window.opener.document.forms['aidememory'].monpostit.value + \"&clas_activ=\" + '".$ru."' ;\n";
     echo "</script>\n";
     exit();
     }
	}
			
?>

</BODY>
</HTML>



