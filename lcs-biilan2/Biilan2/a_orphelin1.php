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
<title>Gestion Administrative du B2I</title>
<link rel="stylesheet" href="Style/style.css">
</head>
<body>


<?php
include "Includes/basedir.inc.php";
include "$BASEDIR/lcs/includes/headerauth.inc.php";
include "$BASEDIR/Annu/includes/ldap.inc.php";
include "$BASEDIR/Annu/includes/ihm.inc.php";

require ("config.php");
require ("config2.php");

list ($idpers,$username) = isauth();
if (is_admin("Biilan2_is_admin",$username)!="Y")
{die("<div class=\"remarque\"><br><br>L'acc�s � Biilan est ouvert, toutefois<br>n'&eacute;tant pas administrateur, vos droits sont restreints<br><br></div>");}

$db=@mysql_connect($dbserver,$dbuser,$dbpass) or die ("erreur de connexion");
mysql_select_db($dbbase,$db) or die ("erreur de connexion base"); 
 
if ($etab=="Lycee") {$typetab="Lyc�e";}
if ($etab=="Ecole") {$typetab="�cole";}
if ($etab=="College") {$typetab="Coll�ge";}
print "<div class=\"texte\"><h1>Mise � Jour Annuelle de Biilan $typetab --- Suppression des comptes orphelins</h1></div>"; 

if(empty($_POST["certain"]))
	{print "<div class=avertissement><br><br>Veuillez r�pondre � la question.<br><br></div>";}
else
	{
  	$variable = $_POST["certain"];
  		if($variable=='oui')
   		 {
				$req = mysql_query("SELECT login,id from bii_tbl") or die ("erreur req ".mysql_error());
				$res = mysql_numrows($req);
				print '<div class=confirm><br><br>Op�ration en cours ...<br><br></div>';
				 while ( $resultat = mysql_fetch_array($req))
      			{
					$login = "$resultat[login]";           // on stocke le login de l'�l�ve � partir de la base biilan
					
					list($user, $groups)=people_get_variables($resultat[login], true);
					$dbnom = "$user[fullname]"; 	            // on stocke le nom complet de l'�l�ve
					$dblogin = "$user[uid]"; 	                // on stocke le login de l'�l�ve
					$log2 = "$resultat[login]";
					$log3 = "$resultat[id]";  
     
     				 if ($login=$dblogin)
                                            //cas eleve present dans l'annuaire
						{ print  "<center><font color=\"#0E8A21\">OK</font color></center>";}
				 	else                                  //cas eleve plus present dans l'annuaire
						{
       		 		mysql_query("Delete from bii_tbl where id ='$log3' ");
      		 		mysql_query("Delete from dmd_tbl where login ='$log2' ");
						mysql_query("Delete from resultat_tbl where login ='$log2' ");
      		  		print  "<center><font color=\"#FF0000\">X</font color></center>";
       		 		}
					}
																								//Affichage action r�alis�e																												
    			print '<div class=confirm><br><br>Fin de l\'op�ration</div>';
    			mysql_close();
    		}
  		else
    	{print '<div class=avertissement><br><br>Aucune action r�alis�e.</div>';}
  }



?>

</body>
</html>
