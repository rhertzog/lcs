<?php
/* ==================================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 2.3 du 06/01/2011
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script d'import de données perso dans sa base-
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
//si la page est appelée par un utilisateur non identifié
if (!isset($_SESSION['login']) )exit;

//si la page est appelée par un utilisateur non prof
elseif ($_SESSION['cequi']!="prof") exit;

if (isset($_POST['Fermer'])) 
	echo '<script type="text/javascript">
                    //<![CDATA[
                    window.close();
                   //]]>
                    </script>';
					
include "/var/www/lcs/includes/headerauth.inc.php";
  list ($idpers,$log) = isauth();
  if ($idpers) { $_LCSkey = urldecode( xoft_decode($HTTP_COOKIE_VARS['LCSuser'],$key_priv) );
     
 }
 
function SansAccent($texte){

$accent='ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËéèêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ';
$noaccent='AAAAAAaaaaaaOOOOOOooooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn';
$texte = strtr($texte,$accent,$noaccent);
return $texte;

} 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html  xmlns="http://www.w3.org/1999/xhtml" >
<head>
<title>Import perso</title>
<meta name="author" content="Philippe LECLERC -TICE CAEN" />
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<link href="../style/style.css" rel="stylesheet" type="text/css"/>
	<!--[if IE]>
<link href="../style/style-ie.css"  rel="stylesheet" type="text/css"/>
<![endif]-->
	</head>
<body>
<h1 class='title'></h1>
<?php

//si clic sur le bouton Valider
if (isset($_POST['Valider']))
	{	
			//vérification de l'existence du répertoire
	if (file_exists("/home/".$_SESSION['login']."/public_html/")) 
		{
//traitement du  fichier 	
			if ((!empty($_FILES["FileSelection1"]["name"])))
			{
			if ($_FILES["FileSelection1"]["size"]>0)
				{
				$nomFichier = SansAccent($_FILES["FileSelection1"]["name"]) ;
				$nomFichier=mb_ereg_replace("'|[[:blank:]]","_",$nomFichier);
				$nomTemporaire = $_FILES["FileSelection1"]["tmp_name"] ;
				//chargement du fichier
				copy($nomTemporaire,"/home/".$_SESSION['login']."/public_html/my_cdt.tgz");
							
				//test de la présence du fichier uploadé
				if (file_exists("/home/".$_SESSION['login']."/public_html/my_cdt.tgz"))
					{
					//tester le contenu
					$test=explode("@",$nomFichier);
					$cmd="cd /home/".$_SESSION['login']."/public_html/ && tar -tzvf my_cdt.tgz > ctrl.txt && md5sum ctrl.txt | cut -b 10-20";
					exec($cmd,$retour,$rien);
					if ($retour[0] == $test[1])
						{
						//deplier l'archive
						$cmd="cd /home/".$_SESSION['login']."/public_html/ && tar -mxzvf my_cdt.tgz";
						exec($cmd,$li,$ret);
						//importer les tables
						if ($ret == 0)
							{
							$exlogin=$test[0];
							$cmd=" cd /home/".$_SESSION['login']."/public_html/ && sed -i 's/~".$exlogin."/~".$_SESSION['login']."/g' dump.sql";
							exec($cmd,$li,$ret);
							if ($ret != 0) 
							$mess1= "<h3 class='ko'>1. les liens vers les pi&#232;ces jointes  n\'ont pas pu &#234;tre mise &#224; jour avec le nouveau login"."<br /></h3>";
							$cmd=" cd /home/".$_SESSION['login']."/public_html/ && mysql -u".$_SESSION['login']." -p".$_LCSkey." ".mb_ereg_replace("\.","",$_SESSION['login'])."_db < dump.sql";
							exec($cmd,$li,$ret);
							if ($ret == 0) $mess1="<h3 class='ok'>Les donn&#233;es ont &#233;t&#233; import&#233;es.<br /></h3>";
							else $mess1 = "<h3 class='ko'>Une erreur s'est produite lors de l'import des donn&#233;es.<br /></h3>";
							}							
						else $mess1= "<h3 class='ko'> le fichier n'a pas pu &#234;tre d&#233;compress&#233; <br /></h3>";
						}
					else  $mess1= "<h3 class='ko'> le fichier n'est pas conforme <br /></h3>";
					$cmd=" cd /home/".$_SESSION['login']."/public_html/ && rm -rf dump.sql my_cdt.tgz ctrl.txt";
					exec($cmd);			
					}
				else $mess1= "<h3 class='ko'>1. Erreur dans le transfert du fichier <br /></h3>";
				}
			else $mess1= "<h3 class='ko'>2. Erreur dans l'importation du fichier  <br /></h3>";
			}
			else $mess1= "<h3 class='ko'>2. Pas de fichier s&#233;lectionn&#233;  <br /></h3>";
		}
	}

?>
<form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="post" enctype="multipart/form-data">
    <div><input name="TA" type="hidden"  value="<?php echo md5($_SESSION['RT'].htmlentities($_SERVER['PHP_SELF'])); ?>" />
<fieldset id="field7">
<legend id="legende">Import de donn&#233;es</legend>

<?php
//affichage du formulaire
if (!isset($_POST['Valider']))
	{
	echo ' <p>Ce formulaire vous permet d\'importer les donn&#233;es que vous avez pr&#233;c&#233;demment export&#233;es du cahier de textes d\'un autre &#233;tablissement. </p>';
	echo '<p>Vous pourrez les exploiter dans la partie <b>Archives personnelles </b> du  volet "Archives" </p>';
	echo '<p></p>';
	echo '<ol>';
	echo '<li>S&#233;lectionner le fichier .cdt contenant les donn&#233;es (';
	echo ini_get( 'upload_max_filesize');
	echo '  maxi) : <br /><input type="file" name="FileSelection1" size="40" /></li>';
	echo '</ol>';
	echo '<input type="submit" title="Valider" name="Valider" value="" class="bt-valid" />
		<input class="bt-fermer" type="submit" name="Fermer" value="" />';
	}
//affichage du resultat
	else 
	{
	if ($mess1!="") echo $mess1;
	echo '<input class="bt-fermer" type="submit" name="Fermer" value="" />';
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
