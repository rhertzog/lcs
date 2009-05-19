<?php
/* ==========================================================
   Projet LCS : Linux Communication Server
   Plugin "Biilan2 : Gestion administrative du B2i"
   par Jean-Louis ROSSIGNOL <jean-louis.rossignol@ac-caen.fr>
   et Gilles HILAIRE <gilles.hilaire@ac-caen.fr>   
   ========================================================== */
?>

<html>
<head>
<title>Biilan</title>
<link rel="stylesheet" href="Style/style.css">
</head>
<body>

<div class="texte"><h1>Restauration des données</h1><BR></div>

<?php
require ("config.php");
$db=@mysql_connect($dbserver , $dbuser, $dbpass) or die ("erreur de connexion");
mysql_select_db($dbbase,$db) or die ("erreur de connexion base");

if(empty($_POST["certain"]))      // verif etes vous sur ?
{print "<div class=avertissement><br><br><br>Veuillez répondre à la question.</div>";}
else
{
$variable = $_POST["certain"];
    if($variable=='oui')
		{
		//import
		if ($fichier_name!="") 
			{
  			copy($fichier,"/tmp/sauvbii.sql");
			$cmd="/usr/bin/sudo -H -u root /usr/share/lcs/scripts/execution_script_plugin.sh /usr/share/lcs/Plugins/Biilan2/restaur.sh";   
			exec($cmd,$retour,$ret_val);
			if($ret_val=="0") {print "<div class=confirm><br><br><br>Opération réalisée avec succès</div>";} else {print "<div class=avertissement><br><br><br>Echec</div>"; }
  			}
		}
    
 	else {print '<div class=avertissement><br><br><br>Aucune action réalisée.</div>';}
}


mysql_close();?>
</body>
</html>

