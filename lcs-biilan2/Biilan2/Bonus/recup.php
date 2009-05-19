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
 <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<link rel="stylesheet" href="../Style/style.css">
</head>
<body>

<?php
include "../Includes/basedir.inc.php";
include "$BASEDIR/lcs/includes/headerauth.inc.php";
include "$BASEDIR/Annu/includes/ldap.inc.php";
include "$BASEDIR/Annu/includes/ihm.inc.php";


list ($idpers,$username) = isauth();
if (is_admin("Biilan2_is_admin",$username)!="Y")
{die("<div class=\"remarque\"><br><br>L'accès à Biilan est ouvert, toutefois<br>n'&eacute;tant pas administrateur, vos droits sont restreints<br><br></div>");}
  


require ("../../Biilan/config.php");

print "<div class=\"texte\"><h1>Récupération des données à partir de Biilan</h1></div>";
$dbserver1=$dbserver;
$dbuser1=$dbuser;
$dbpass1=$dbpass;
$dbbase1=$dbbase;


$db1=@mysql_connect($dbserver1,$dbuser1,$dbpass1) or die ("erreur de connexion");
mysql_select_db($dbbase1,$db1) or die ("erreur de connexion base");

print "<h2>&nbsp;&nbsp;&nbsp;&nbsp;ETAPE 1 : Création du fichier de transfert</h2>";

print "<div class=\"affichok\">1 - Copier les lignes générées ci dessous.<br>";
print "2 - Coller la sélection dans un fichier texte.<br>";
print "3 - Vérifier que la première ligne correspond à  : #*********DEBUT********* et que la dernière correspond à :  #*********FIN*********<br>";
print "4 - Enregistrer le fichier au format sql.<br><br></div>";

print "#*********DEBUT*********<br>";
print "TRUNCATE TABLE `bii_tbl`;<br>";

// recup des items

$req=mysql_query("SELECT login FROM bii_tbl");
while ( $resultat = mysql_fetch_array($req))
{
$login=$resultat[login] ;

																																//    recuperation du nom complet  et du nom à partir du login de l'eleve 
list($user, $groups)=people_get_variables($login, true);
$nomcomplet="$user[fullname]";
$nom="$user[nom]"; 

	
  	$req1=mysql_query("SELECT cp11,val11,cp12,val12,cp13,val13,cp14,val14,cp15,val15,cp16,val16,cp21,val21,cp22,val22,cp23,val23,cp24,val24,cp25,val25,cp26,val26,cp27,val27,cp31,val31,cp32,val32,cp33,val33,cp34,val34,cp35,val35,cp36,val36,cp37,val37,cp41,val41,cp42,val42,cp43,val43,cp44,val44,cp45, val45,cp51,val51,cp52,val52,cp53,val53,cp54,val54 FROM bii_tbl WHERE login LIKE '$login' ") or die ("erreur req ".mysql_error());
	while ( $resultat1 = mysql_fetch_array($req1))
				//recup nom du prof et date de validation
			{
			$date11=$resultat1[cp11] ; 														
			$fullprof11=$resultat1[val11] ; 													
			$date12=$resultat1[cp12] ; 															
			$fullprof12=$resultat1[val12] ;
			$date13=$resultat1[cp13] ; 															
			$fullprof13=$resultat1[val13] ;	
			$date14=$resultat1[cp14] ; 															
			$fullprof14=$resultat1[val14] ;
			$date15=$resultat1[cp15] ; 															
			$fullprof15=$resultat1[val15] ;
			$date16=$resultat1[cp16] ; 															
			$fullprof16=$resultat1[val16] ;
			$date21=$resultat1[cp21] ; 														
			$fullprof21=$resultat1[val21] ; 													
			$date22=$resultat1[cp22] ; 															
			$fullprof22=$resultat1[val22] ;
			$date23=$resultat1[cp23] ; 															
			$fullprof23=$resultat1[val23] ;	
			$date24=$resultat1[cp24] ; 															
			$fullprof24=$resultat1[val24] ;
			$date25=$resultat1[cp25] ; 															
			$fullprof25=$resultat1[val25] ;
			$date26=$resultat1[cp26] ; 															
			$fullprof26=$resultat1[val26] ;
			$date27=$resultat1[cp27] ; 															
			$fullprof27=$resultat1[val27] ;
			$date31=$resultat1[cp31] ; 														
			$fullprof31=$resultat1[val31] ; 													
			$date32=$resultat1[cp32] ; 															
			$fullprof32=$resultat1[val32] ;
			$date33=$resultat1[cp33] ; 															
			$fullprof33=$resultat1[val33] ;	
			$date34=$resultat1[cp34] ; 															
			$fullprof34=$resultat1[val34] ;
			$date35=$resultat1[cp35] ; 															
			$fullprof35=$resultat1[val35] ;
			$date36=$resultat1[cp36] ; 															
			$fullprof36=$resultat1[val36] ;
			$date37=$resultat1[cp37] ; 															
			$fullprof37=$resultat1[val37] ;
			$date41=$resultat1[cp41] ; 														
			$fullprof41=$resultat1[val41] ; 													
			$date42=$resultat1[cp42] ; 															
			$fullprof42=$resultat1[val42] ;
			$date43=$resultat1[cp43] ; 															
			$fullprof43=$resultat1[val43] ;	
			$date44=$resultat1[cp44] ; 															
			$fullprof44=$resultat1[val44] ;
			$date45=$resultat1[cp45] ; 															
			$fullprof45=$resultat1[val45] ;
			$date51=$resultat1[cp51] ; 														
			$fullprof51=$resultat1[val51] ; 													
			$date52=$resultat1[cp52] ; 															
			$fullprof52=$resultat1[val52] ;
			$date53=$resultat1[cp53] ; 															
			$fullprof53=$resultat1[val53] ;	
			$date54=$resultat1[cp54] ; 															
			$fullprof54=$resultat1[val54] ;
	
				
			
			if ($fullprof11=='') {} else {
			$req11=mysql_query("SELECT discip FROM discip_tbl WHERE prof LIKE '$fullprof11' ") or die ("erreur req ".mysql_error());
			$presencdiscip = mysql_num_rows($req11);
			if($presencdiscip==0)
				{
				print "INSERT INTO bii_tbl  values ('', '$login', '$nom', '$nomcomplet', 'C.1.1', '$date11', '$fullprof11', '-----');	<br>";
				}
			else
				{
					while ( $resultat11 = mysql_fetch_array($req11))
					{
					$discipline11=$resultat11[discip] ;
					print "INSERT INTO bii_tbl  values ('', '$login', '$nom', '$nomcomplet', 'C.1.1', '$date11', '$fullprof11', '$discipline11');	<br>";
					}	
				}}	
			
			if ($fullprof12=='') {} else {
			$req12=mysql_query("SELECT discip FROM discip_tbl WHERE prof LIKE '$fullprof12' ") or die ("erreur req ".mysql_error());
			$presencdiscip = mysql_num_rows($req12);
			if($presencdiscip==0)
				{
				print "INSERT INTO bii_tbl  values ('', '$login', '$nom', '$nomcomplet', 'C.1.2', '$date12', '$fullprof12', '-----');	<br>";
				}
			else
				{
				while ( $resultat12 = mysql_fetch_array($req12))
					{
					$discipline12=$resultat12[discip] ;
					print "INSERT INTO bii_tbl  values ('', '$login', '$nom', '$nomcomplet', 'C.1.2', '$date12', '$fullprof12', '$discipline12');	<br>";
					}
				}}		
			
			if ($fullprof13=='') {} else {
			$req13=mysql_query("SELECT discip FROM discip_tbl WHERE prof LIKE '$fullprof13' ") or die ("erreur req ".mysql_error());
			$presencdiscip = mysql_num_rows($req13);
			if($presencdiscip==0)
				{
				print "INSERT INTO bii_tbl  values ('', '$login', '$nom', '$nomcomplet', 'C.1.3', '$date13', '$fullprof13', '-----');	<br>";
				}
			else
				{
				while ( $resultat13 = mysql_fetch_array($req13))
					$discipline13=$resultat13[discip] ;
					print "INSERT INTO bii_tbl  values ('', '$login', '$nom', '$nomcomplet', 'C.1.3', '$date13', '$fullprof13', '$discipline13');	<br>";
					}
				}}		

			if ($fullprof14=='') {} else {			
			$req14=mysql_query("SELECT discip FROM discip_tbl WHERE prof LIKE '$fullprof14' ") or die ("erreur req ".mysql_error());
			$presencdiscip = mysql_num_rows($req14);
			if($presencdiscip==0)
				{
				print "INSERT INTO bii_tbl  values ('', '$login', '$nom', '$nomcomplet', 'C.1.4', '$date14', '$fullprof14', '-----');	<br>";
				}
			else
				{
				while ( $resultat14 = mysql_fetch_array($req14))
					{
					$discipline14=$resultat14[discip] ;
					print "INSERT INTO bii_tbl  values ('', '$login', '$nom', '$nomcomplet', 'C.1.4', '$date14', '$fullprof14', '$discipline14');	<br>";
					}
				}}	
			

			if ($fullprof15=='') {} else {
			$req15=mysql_query("SELECT discip FROM discip_tbl WHERE prof LIKE '$fullprof15' ") or die ("erreur req ".mysql_error());
			$presencdiscip = mysql_num_rows($req15);
			if($presencdiscip==0)
				{
				print "INSERT INTO bii_tbl  values ('', '$login', '$nom', '$nomcomplet', 'C.1.5', '$date15', '$fullprof15', '-----');	<br>";
				}
			else
				{
				while ( $resultat15 = mysql_fetch_array($req15))
					{
					$discipline15=$resultat15[discip] ;
					print "INSERT INTO bii_tbl  values ('', '$login', '$nom', '$nomcomplet', 'C.1.5', '$date15', '$fullprof15', '$discipline15');	<br>";
					}	
				}}	
			
			if ($fullprof16=='') {} else {
			$req16=mysql_query("SELECT discip FROM discip_tbl WHERE prof LIKE '$fullprof16' ") or die ("erreur req ".mysql_error());
			$presencdiscip = mysql_num_rows($req16);
			if($presencdiscip==0)
				{
				print "INSERT INTO bii_tbl  values ('', '$login', '$nom', '$nomcomplet', 'C.1.6', '$date16', '$fullprof16', '-----');	<br>";
				}
			else
				{
				while ( $resultat16 = mysql_fetch_array($req16))					
					{
					$discipline16=$resultat16[discip] ;
					print "INSERT INTO bii_tbl  values ('', '$login', '$nom', '$nomcomplet', 'C.1.6', '$date16', '$fullprof16', '$discipline16');	<br>";
					}
				}}		
			
			if ($fullprof21=='') {} else {
			$req21=mysql_query("SELECT discip FROM discip_tbl WHERE prof LIKE '$fullprof21' ") or die ("erreur req ".mysql_error());
			$presencdiscip = mysql_num_rows($req21);
			if($presencdiscip==0)
				{
				print "INSERT INTO bii_tbl  values ('', '$login', '$nom', '$nomcomplet', 'C.2.1', '$date21', '$fullprof21', '-----');	<br>";
				}
			else
				{
				while ( $resultat21 = mysql_fetch_array($req21))
					{
					$discipline21=$resultat21[discip] ;
					print "INSERT INTO bii_tbl  values ('', '$login', '$nom', '$nomcomplet', 'C.2.1', '$date21', '$fullprof21', '$discipline21');	<br>";
					}	
				}}	
			
			if ($fullprof22=='') {} else {
			$req22=mysql_query("SELECT discip FROM discip_tbl WHERE prof LIKE '$fullprof22' ") or die ("erreur req ".mysql_error());
			$presencdiscip = mysql_num_rows($req22);
			if($presencdiscip==0)
				{
				print "INSERT INTO bii_tbl  values ('', '$login', '$nom', '$nomcomplet', 'C.2.2', '$date22', '$fullprof22', '-----');	<br>";
				}
			else
				{
				while ( $resultat22 = mysql_fetch_array($req22))				
					{
					$discipline22=$resultat22[discip] ;
					print "INSERT INTO bii_tbl  values ('', '$login', '$nom', '$nomcomplet', 'C.2.2', '$date22', '$fullprof22', '$discipline22');	<br>";
					}
				}}		
			
			if ($fullprof23=='') {} else {
			$req23=mysql_query("SELECT discip FROM discip_tbl WHERE prof LIKE '$fullprof23' ") or die ("erreur req ".mysql_error());
			$presencdiscip = mysql_num_rows($req23);
			if($presencdiscip==0)
				{
				print "INSERT INTO bii_tbl  values ('', '$login', '$nom', '$nomcomplet', 'C.2.3', '$date23', '$fullprof23', '-----');	<br>";
				}
			else
				{
				while ( $resultat23 = mysql_fetch_array($req23))
					{
					$discipline23=$resultat23[discip] ;
					print "INSERT INTO bii_tbl  values ('', '$login', '$nom', '$nomcomplet', 'C.2.3', '$date23', '$fullprof23', '$discipline23');	<br>";
					}
				}}		
			
			if ($fullprof24=='') {} else {
			$req24=mysql_query("SELECT discip FROM discip_tbl WHERE prof LIKE '$fullprof24' ") or die ("erreur req ".mysql_error());
			$presencdiscip = mysql_num_rows($req24);
			if($presencdiscip==0)
				{
				print "INSERT INTO bii_tbl  values ('', '$login', '$nom', '$nomcomplet', 'C.2.4', '$date24', '$fullprof24', '-----');	<br>";
				}
			else
				{
				while ( $resultat24 = mysql_fetch_array($req24))
					{
					$discipline24=$resultat24[discip] ;
					print "INSERT INTO bii_tbl  values ('', '$login', '$nom', '$nomcomplet', 'C.2.4', '$date24', '$fullprof24', '$discipline24');	<br>";
					}
				}}		
			
			if ($fullprof25=='') {} else {
			$req25=mysql_query("SELECT discip FROM discip_tbl WHERE prof LIKE '$fullprof25' ") or die ("erreur req ".mysql_error());
			$presencdiscip = mysql_num_rows($req25);
			if($presencdiscip==0)
				{
				print "INSERT INTO bii_tbl  values ('', '$login', '$nom', '$nomcomplet', 'C.2.5', '$date25', '$fullprof25', '-----');	<br>";
				}
			else
				{
				while ( $resultat25 = mysql_fetch_array($req25))
					{
					$discipline25=$resultat25[discip] ;
					print "INSERT INTO bii_tbl  values ('', '$login', '$nom', '$nomcomplet', 'C.2.5', '$date25', '$fullprof25', '$discipline25');	<br>";
					}
				}	}	
			
			if ($fullprof26=='') {} else {
			$req26=mysql_query("SELECT discip FROM discip_tbl WHERE prof LIKE '$fullprof26' ") or die ("erreur req ".mysql_error());
			$presencdiscip = mysql_num_rows($req26);
			if($presencdiscip==0)
				{
				print "INSERT INTO bii_tbl  values ('', '$login', '$nom', '$nomcomplet', 'C.2.6', '$date26', '$fullprof26', '-----');	<br>";
				}
			else
				{
				while ( $resultat26 = mysql_fetch_array($req26))
					{
					$discipline26=$resultat26[discip] ;
					print "INSERT INTO bii_tbl  values ('', '$login', '$nom', '$nomcomplet', 'C.2.6', '$date26', '$fullprof26', '$discipline26');	<br>";
					}
				}}
			
			if ($fullprof27=='') {} else {
			$req27=mysql_query("SELECT discip FROM discip_tbl WHERE prof LIKE '$fullprof27' ") or die ("erreur req ".mysql_error());
			$presencdiscip = mysql_num_rows($req27);
			if($presencdiscip==0)
				{
				print "INSERT INTO bii_tbl  values ('', '$login', '$nom', '$nomcomplet', 'C.2.7', '$date27', '$fullprof27', '-----');	<br>";
				}
			else
				{
				while ( $resultat27 = mysql_fetch_array($req27))
					{
					$discipline27=$resultat27[discip] ;
					print "INSERT INTO bii_tbl  values ('', '$login', '$nom', '$nomcomplet', 'C.2.7', '$date27', '$fullprof27', '$discipline27');	<br>";
					}	
				}}	
			
			if ($fullprof31=='') {} else {
			$req31=mysql_query("SELECT discip FROM discip_tbl WHERE prof LIKE '$fullprof31' ") or die ("erreur req ".mysql_error());
			$presencdiscip = mysql_num_rows($req31);
			if($presencdiscip==0)
				{
				print "INSERT INTO bii_tbl  values ('', '$login', '$nom', '$nomcomplet', 'C.3.1', '$date31', '$fullprof31', '-----');	<br>";
				}
			else
				{
				while ( $resultat31 = mysql_fetch_array($req31))
					{
					$discipline31=$resultat31[discip] ;
					print "INSERT INTO bii_tbl  values ('', '$login', '$nom', '$nomcomplet', 'C.3.1', '$date31', '$fullprof31', '$discipline31');	<br>";
					}	
				}	}
			
			if ($fullprof32=='') {} else {
			$req32=mysql_query("SELECT discip FROM discip_tbl WHERE prof LIKE '$fullprof32' ") or die ("erreur req ".mysql_error());
			$presencdiscip = mysql_num_rows($req32);
			if($presencdiscip==0)
				{
				print "INSERT INTO bii_tbl  values ('', '$login', '$nom', '$nomcomplet', 'C.3.2', '$date32', '$fullprof32', '-----');	<br>";
				}
			else
				{
				while ( $resultat32 = mysql_fetch_array($req32))
					{
					$discipline32=$resultat32[discip] ;
					print "INSERT INTO bii_tbl  values ('', '$login', '$nom', '$nomcomplet', 'C.3.2', '$date32', '$fullprof32', '$discipline32');	<br>";
					}	
				}}	
			
			if ($fullprof33=='') {} else {
			$req33=mysql_query("SELECT discip FROM discip_tbl WHERE prof LIKE '$fullprof33' ") or die ("erreur req ".mysql_error());
			$presencdiscip = mysql_num_rows($req33);
			if($presencdiscip==0)
				{
				print "INSERT INTO bii_tbl  values ('', '$login', '$nom', '$nomcomplet', 'C.3.3', '$date33', '$fullprof33', '-----');	<br>";
				}
			else
				{
				while ( $resultat33 = mysql_fetch_array($req33))
					{
					$discipline33=$resultat33[discip] ;
					print "INSERT INTO bii_tbl  values ('', '$login', '$nom', '$nomcomplet', 'C.3.3', '$date33', '$fullprof33', '$discipline33');	<br>";
					}
				}}		
			
			if ($fullprof34=='') {} else {
			$req34=mysql_query("SELECT discip FROM discip_tbl WHERE prof LIKE '$fullprof34' ") or die ("erreur req ".mysql_error());
			$presencdiscip = mysql_num_rows($req34);
			if($presencdiscip==0)
				{
				print "INSERT INTO bii_tbl  values ('', '$login', '$nom', '$nomcomplet', 'C.3.4', '$date34', '$fullprof34', '-----');	<br>";
				}
			else
				{
				while ( $resultat34 = mysql_fetch_array($req34))
					{
					$discipline34=$resultat34[discip] ;
					print "INSERT INTO bii_tbl  values ('', '$login', '$nom', '$nomcomplet', 'C.3.4', '$date34', '$fullprof34', '$discipline34');	<br>";
					}
				}}		
			
			if ($fullprof35=='') {} else {
			$req35=mysql_query("SELECT discip FROM discip_tbl WHERE prof LIKE '$fullprof35' ") or die ("erreur req ".mysql_error());
			$presencdiscip = mysql_num_rows($req35);
			if($presencdiscip==0)
				{
				print "INSERT INTO bii_tbl  values ('', '$login', '$nom', '$nomcomplet', 'C.3.5', '$date35', '$fullprof35', '-----');	<br>";
				}
			else
				{
				while ( $resultat35 = mysql_fetch_array($req35))
					{
					$discipline35=$resultat35[discip] ;
					print "INSERT INTO bii_tbl  values ('', '$login', '$nom', '$nomcomplet', 'C.3.5', '$date35', '$fullprof35', '$discipline35');	<br>";
					}	
				}}	
			
			if ($fullprof36=='') {} else {
			$req36=mysql_query("SELECT discip FROM discip_tbl WHERE prof LIKE '$fullprof36' ") or die ("erreur req ".mysql_error());
			$presencdiscip = mysql_num_rows($req36);
			if($presencdiscip==0)
				{
				print "INSERT INTO bii_tbl  values ('', '$login', '$nom', '$nomcomplet', 'C.3.6', '$date36', '$fullprof36', '-----');	<br>";
				}
			else
				{
				while ( $resultat36 = mysql_fetch_array($req36))
					{
					$discipline36=$resultat36[discip] ;
					print "INSERT INTO bii_tbl  values ('', '$login', '$nom', '$nomcomplet', 'C.3.6', '$date36', '$fullprof36', '$discipline36');	<br>";
					}
				}}
			
			if ($fullprof37=='') {} else {
			$req37=mysql_query("SELECT discip FROM discip_tbl WHERE prof LIKE '$fullprof37' ") or die ("erreur req ".mysql_error());
			$presencdiscip = mysql_num_rows($req37);
			if($presencdiscip==0)
				{
				print "INSERT INTO bii_tbl  values ('', '$login', '$nom', '$nomcomplet', 'C.3.7', '$date37', '$fullprof37', '-----');	<br>";
				}
			else
				{
				while ( $resultat37 = mysql_fetch_array($req37))
					{
					$discipline37=$resultat37[discip] ;
					print "INSERT INTO bii_tbl  values ('', '$login', '$nom', '$nomcomplet', 'C.3.7', '$date37', '$fullprof37', '$discipline37');	<br>";
					}	
				}	}
			
			if ($fullprof41=='') {} else {
			$req41=mysql_query("SELECT discip FROM discip_tbl WHERE prof LIKE '$fullprof41' ") or die ("erreur req ".mysql_error());
			$presencdiscip = mysql_num_rows($req41);
			if($presencdiscip==0)
				{
				print "INSERT INTO bii_tbl  values ('', '$login', '$nom', '$nomcomplet', 'C.4.1', '$date41', '$fullprof41', '-----');	<br>";
				}
			else
				{
				while ( $resultat41 = mysql_fetch_array($req41))
					{
					$discipline41=$resultat41[discip] ;
					print "INSERT INTO bii_tbl  values ('', '$login', '$nom', '$nomcomplet', 'C.4.1', '$date41', '$fullprof41', '$discipline41');	<br>";
					}	
				}	}
			
			if ($fullprof42=='') {} else {
			$req42=mysql_query("SELECT discip FROM discip_tbl WHERE prof LIKE '$fullprof42' ") or die ("erreur req ".mysql_error());
			$presencdiscip = mysql_num_rows($req42);
			if($presencdiscip==0)
				{
				print "INSERT INTO bii_tbl  values ('', '$login', '$nom', '$nomcomplet', 'C.4.2', '$date42', '$fullprof42', '-----');	<br>";
				}
			else
				{
				while ( $resultat42 = mysql_fetch_array($req42))
					{
					$discipline42=$resultat42[discip] ;
					print "INSERT INTO bii_tbl  values ('', '$login', '$nom', '$nomcomplet', 'C.4.2', '$date42', '$fullprof42', '$discipline42');	<br>";
					}	
				}}	
			
			if ($fullprof43=='') {} else {
			$req43=mysql_query("SELECT discip FROM discip_tbl WHERE prof LIKE '$fullprof43' ") or die ("erreur req ".mysql_error());
			$presencdiscip = mysql_num_rows($req43);
			if($presencdiscip==0)
				{
				print "INSERT INTO bii_tbl  values ('', '$login', '$nom', '$nomcomplet', 'C.4.3', '$date43', '$fullprof43', '-----');	<br>";
				}
			else
				{
				while ( $resultat43 = mysql_fetch_array($req43))
					{
					$discipline43=$resultat43[discip] ;
					print "INSERT INTO bii_tbl  values ('', '$login', '$nom', '$nomcomplet', 'C.4.3', '$date43', '$fullprof43', '$discipline43');	<br>";
					}	
				}	}
			
			if ($fullprof44=='') {} else {
			$req44=mysql_query("SELECT discip FROM discip_tbl WHERE prof LIKE '$fullprof44' ") or die ("erreur req ".mysql_error());
			$presencdiscip = mysql_num_rows($req44);
			if($presencdiscip==0)
				{
				print "INSERT INTO bii_tbl  values ('', '$login', '$nom', '$nomcomplet', 'C.4.4', '$date44', '$fullprof44', '-----');	<br>";
				}
			else
				{
				while ( $resultat44 = mysql_fetch_array($req44))
					{
					$discipline44=$resultat44[discip] ;
					print "INSERT INTO bii_tbl  values ('', '$login', '$nom', '$nomcomplet', 'C.4.4', '$date44', '$fullprof44', '$discipline44');	<br>";
					}	
				}	}
			
			if ($fullprof45=='') {} else {
			$req45=mysql_query("SELECT discip FROM discip_tbl WHERE prof LIKE '$fullprof45' ") or die ("erreur req ".mysql_error());
			$presencdiscip = mysql_num_rows($req45);
			if($presencdiscip==0)
				{
				print "INSERT INTO bii_tbl  values ('', '$login', '$nom', '$nomcomplet', 'C.4.5', '$date45', '$fullprof45', '-----');	<br>";
				}
			else
				{
				while ( $resultat45 = mysql_fetch_array($req45))
					{
					$discipline45=$resultat45[discip] ;
					print "INSERT INTO bii_tbl  values ('', '$login', '$nom', '$nomcomplet', 'C.4.5', '$date45', '$fullprof45', '$discipline45');	<br>";
					}	
				}	}
			
			if ($fullprof51=='') {} else {
			$req51=mysql_query("SELECT discip FROM discip_tbl WHERE prof LIKE '$fullprof51' ") or die ("erreur req ".mysql_error());
			$presencdiscip = mysql_num_rows($req51);
			if($presencdiscip==0)
				{
				print "INSERT INTO bii_tbl  values ('', '$login', '$nom', '$nomcomplet', 'C.5.1', '$date51', '$fullprof51', '-----');	<br>";
				}
			else
				{
				while ( $resultat51 = mysql_fetch_array($req51))
					{
					$discipline51=$resultat51[discip] ;
					print "INSERT INTO bii_tbl  values ('', '$login', '$nom', '$nomcomplet', 'C.5.1', '$date51', '$fullprof51', '$discipline51');	<br>";
					}	
				}	}
			
			if ($fullprof52=='') {} else {
			$req52=mysql_query("SELECT discip FROM discip_tbl WHERE prof LIKE '$fullprof52' ") or die ("erreur req ".mysql_error());
			$presencdiscip = mysql_num_rows($req52);
			if($presencdiscip==0)
				{
				print "INSERT INTO bii_tbl  values ('', '$login', '$nom', '$nomcomplet', 'C.5.2', '$date52', '$fullprof52', '-----');	<br>";
				}
			else
				{
				while ( $resultat52 = mysql_fetch_array($req52))
					{
					$discipline52=$resultat52[discip] ;
					print "INSERT INTO bii_tbl  values ('', '$login', '$nom', '$nomcomplet', 'C.5.2', '$date52', '$fullprof52', '$discipline52');	<br>";
					}	
				}	}
			
			if ($fullprof53=='') {} else {
			$req53=mysql_query("SELECT discip FROM discip_tbl WHERE prof LIKE '$fullprof53' ") or die ("erreur req ".mysql_error());
			$presencdiscip = mysql_num_rows($req53);
			if($presencdiscip==0)
				{
				print "INSERT INTO bii_tbl  values ('', '$login', '$nom', '$nomcomplet', 'C.5.3', '$date53', '$fullprof53', '-----');	<br>";
				}
			else
				{
				while ( $resultat53 = mysql_fetch_array($req53))
					{
					$discipline53=$resultat53[discip] ;
					print "INSERT INTO bii_tbl  values ('', '$login', '$nom', '$nomcomplet', 'C.5.3', '$date53', '$fullprof53', '$discipline53');	<br>";
					}	
				}	}
			
			if ($fullprof54=='') {} else {
			$req54=mysql_query("SELECT discip FROM discip_tbl WHERE prof LIKE '$fullprof54' ") or die ("erreur req ".mysql_error());
			$presencdiscip = mysql_num_rows($req54);
			if($presencdiscip==0)
				{
				print "INSERT INTO bii_tbl  values ('', '$login', '$nom', '$nomcomplet', 'C.5.4', '$date54', '$fullprof54', '-----');	<br>";
				}
			else
				{
				while ( $resultat54 = mysql_fetch_array($req54))
					{
					$discipline54=$resultat54[discip] ;
					print "INSERT INTO bii_tbl  values ('', '$login', '$nom', '$nomcomplet', 'C.5.4', '$date54', '$fullprof54', '$discipline54');	<br>";
					}
				}}
			}	

			
						//recup du score

print "TRUNCATE TABLE `resultat_tbl`;<br>";


$req0=mysql_query("SELECT login FROM bii_tbl");
while ( $resultat0 = mysql_fetch_array($req0))
	{
				//recup login
	$login=$resultat0[login] ;

	$req01=mysql_query("SELECT result FROM bii_tbl WHERE login LIKE '$login' ") or die ("erreur req ".mysql_error());
	while ( $resultat01 = mysql_fetch_array($req01))
				//recup resultat
			{
			$resultat=$resultat01[result] ;
			if ($resultat=='0')
				{}
			else
				{print "INSERT INTO resultat_tbl  values ('', '$login', '$resultat');	<br>";}
			}
	}

print "#*********FIN*********<br>";


print "<h2>&nbsp;&nbsp;&nbsp;&nbsp;ETAPE 2 : Import du fichier dans Billan2</h2>";

print "<div class=\"affichok\">Il suffit maintenant d'importer le fichier sql créé à l'étape 1 en cliquant sur le bouton <b><i>Importer</i></b> ci dessous.<br><br></div>";
print "<div class=\"affichko\">Avertissement : Toutes les données élèves contenues dans Biilan v2 seront remplacées par celle contenues dans Biilan v1.4</div><br><br><br>";





mysql_close();

require ("../config.php");
$db=@mysql_connect($dbserver,$dbuser,$dbpass) or die ("erreur de connexion");
mysql_select_db($dbbase,$db) or die ("erreur de connexion base");


//nettoyage qd pas de prof ou de date associé à une validation
	
	$req100=mysql_query("SELECT id FROM bii_tbl WHERE date LIKE '' and fullprof LIKE '' ") or die ("erreur req ".mysql_error());
	while ( $resultat100 = mysql_fetch_array($req100))
	{
	$id=$resultat100[id] ;	
	mysql_query("DELETE FROM bii_tbl WHERE id ='$id'");
	}

mysql_close();


?>

<table border="0" cellspacing="0" cellpadding="0" align="center">
  <tr><form name="form1" method="post" action="import.php">
    <td width="200" height="17" align ="center"><input type="submit" name="button" value="&nbsp;&nbsp;&nbsp;Importer&nbsp;&nbsp;&nbsp;"><br><br></td>
  </tr></form>
</table>

<br><br>

<h2>&nbsp;&nbsp;&nbsp;&nbsp;ETAPE 3 : Vérification </h1>

<div class="affichok">Ce contrôle permet de s'assurer que le résultat de chaque élève correspond bien au nombre d'items validés. <b><br><br></div>


<table border="0" cellspacing="0" cellpadding="0" align="center">
  <tr><form name="form1" method="post" action="ctrlresult.php">
    <td width="200" height="17" align ="center"><input type="submit" name="button" value="&nbsp;&nbsp;&nbsp;Contrôler&nbsp;&nbsp;&nbsp;"><br><br></td>
  </tr></form>
</table>

</body>
</html>
