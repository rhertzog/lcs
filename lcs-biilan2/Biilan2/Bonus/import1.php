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
<link rel="stylesheet" href="../Style/style.css">
</head>
<body>

<div class="texte"><h1>Restauration des donn�es</h1><BR></div>

<?php
require ("../config.php");
$db=@mysql_connect($dbserver , $dbuser, $dbpass) or die ("erreur de connexion");
mysql_select_db($dbbase,$db) or die ("erreur de connexion base");

if(empty($_POST["certain"]))      // verif etes vous sur ?
{print "<div class=avertissement><br><br><br>Veuillez r�pondre � la question.</div>";}
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
			if($ret_val=="0") {print "<div class=confirm><br><br><br>Op�ration r�alis�e avec succ�s</div>";} else {print "<div class=avertissement><br><br><br>Echec</div>"; }
  			}
		}
    
 	else {print '<div class=avertissement><br><br><br>Aucune action r�alis�e.</div>';}
}


mysql_close();
<br><br><br>

<h2>&nbsp;&nbsp;&nbsp;&nbsp;V�rification </h1>

<div class="affichok">Ce contr�le permet de s'assurer que le r�sultat de chaque �l�ve correspond bien au nombre d'items valid�s. <b><br><br></div>


<table border="0" cellspacing="0" cellpadding="0" align="center">
  <tr><form name="form1" method="post" action="ctrlresult.php">
    <td width="200" height="17" align ="center"><input type="submit" name="button" value="&nbsp;&nbsp;&nbsp;Contr�ler&nbsp;&nbsp;&nbsp;"><br><br></td>
  </tr></form>
  

</body>
</html>
