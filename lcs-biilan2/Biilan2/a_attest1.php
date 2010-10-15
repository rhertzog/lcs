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
<title>Gestion Administrative du B2I test</title>
 <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<link rel="stylesheet" href="Style/style.css">
</head>

<body>
<form method="post" action="a_attest2.php">
<div class="texte"><h1>Délivrance d'une attestation B2I</h1></div>

<?php
include "Includes/basedir.inc.php";
include "$BASEDIR/lcs/includes/headerauth.inc.php";
include "$BASEDIR/Annu/includes/ldap.inc.php";
include "$BASEDIR/Annu/includes/ihm.inc.php";

require ("config.php");
require ("config2.php");

list ($idpers,$username) = isauth();
if (is_admin("Biilan2_is_admin",$username)!="Y")
{die("<div class=\"remarque\">Page réservée à l'administrateur ...<br><br></div>");}
$site = "$etab.php";

if ($etab=="Lycee") {$typetab="Lycée"; $i_etab="L";}
if ($etab=="Ecole") {$typetab="École"; $i_etab="E";}
if ($etab=="College") {$typetab="Collège"; $i_etab="C";}


$db=@mysql_connect($dbserver , $dbuser, $dbpass) or die ("erreur de connexion");
mysql_select_db($dbbase,$db) or die ("erreur de connexion base");

//determination du seuil de competences
//$req1 = mysql_query("SELECT id,result FROM resultat_tbl WHERE login LIKE '$uid'") or die ("erreur req ".mysql_error());
$req2=mysql_query("SELECT * FROM $items");
$res2=mysql_num_rows($req2);
$attest1 = $pourcentage * $res2;
$attest2 = floor($attest1);

if(empty($_POST["classe"]))
  {die ("<div class=\"avertissement\"><br><br><br>Veuillez choisir une classe<br><br><br></div>");}
  else
  {

  
	print "<div class=\"texte\"><center><br><br><b>Sélectionner l'élève :<b><br><br></center></div>";
	print "<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">
  	<td><select size=\"5\" STYLE=\"background:#F0E4C8;color:#000080\" NAME=\"sel[]\" MULTIPLE>";
	$users = search_uids ("(cn=$_POST[classe])","half");
   	for ( $loop=0; $loop<count($users); $loop++ )
      {
    	$login = "".$users[$loop]["uid"].""; // on stocke le login de l'eleve dans la variable $login
      	list($user, $groups)=people_get_variables($login, true); // on stocke le nom de l'eleve dans la variable $dbeleve
      	$nom="$user[fullname]"; 
   		//test 80 %
		$req = mysql_query("SELECT result from resultat_tbl WHERE  (login LIKE '$login' AND result >= '$attest2')") or die ("erreur sql ".mysql_error());
		$res= mysql_numrows($req);
		if (!$res)
			{}
			else
  				{
  					while ( $resultat = mysql_fetch_array($req))
       					{
							 //test d'un minimum de 50 % des items par Domaine
							//On cherche le nombre de compétence par niveau 
							$req10 = mysql_query("SELECT id,num,domaine FROM domaines_tbl ORDER BY num ASC") or die ("erreur req ".mysql_error());
							$ok = 0; // test pour savoir si 50 % sur tous les domaines
							
							while ( $resultat10 = mysql_fetch_array($req10))
								{ 		
									$totalitem = 0;
									$req20 = mysql_query("SELECT id,domaine,num,val FROM $items WHERE domaine LIKE $resultat10[num] ORDER BY num  ASC") or die ("erreur req ".mysql_error());
									
									$d = 0; // on initialise le nombre de compétence pour le domaine à 0
									while ( $resultat20 = mysql_fetch_array($req20))
										{
										   
										    $totalitem = $totalitem + 1;
										    $affich ="$i_etab.$resultat10[num].$resultat20[num]"; // on stocke la compétence
										    ;
											//recherche si cpt est validée
											$req21 = mysql_query("SELECT login,cpt FROM bii_tbl WHERE (login LIKE '$login' and cpt like '$affich')") or die ("erreur req ".mysql_err());
											$resultat21 = mysql_fetch_array($req21);
											$exist = mysql_numrows($req21);
											if ($exist)
												{
													$d = $d + 1; 					
												}	
															
										}
										
										if 	($d < $totalitem * 0.5)
												{$ok= 1;}
								
  
	    
	       						
       						}
       						if ($ok==0)
	       						{
	       							echo "<option value=\"" . $login . "\">".$resultat[result]." / $res2 &nbsp;&nbsp;&nbsp;" . $nom . "</option>";
	       						}
       					}
				}
		}
	print "</td></table><br>";
  }

?>

<br>

<table WIDTH=100%>
        <tr>
        <td ALIGN="MIDDLE"><IMG  SRC="Images/ping.png"><BR>
        <input type="submit" name="button" value="Valider">
        </td>
        </tr>
</table>


</form></body>
</html>
