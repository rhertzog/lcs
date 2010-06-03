<?
/* ==================================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 2.0 du 25/10/2009
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script de configuration du cahier de textes -
			_-=-_
   =================================================== */
 session_name("Cdt_Lcs");
@session_start(); 
//error_reporting(0);
//si la page est appelée par un utilisateur non identifié
if (!isset($_SESSION['login']) )exit;

//si la page est appelée par un utilisateur non admin
elseif ($_SESSION['login']!="admin") exit;

//fichiers nécessaires à l'exploitation de l'API
include "../Includes/basedir.inc.php";
include "$BASEDIR/lcs/includes/headerauth.inc.php";
include "$BASEDIR/Annu/includes/ldap.inc.php";
include "$BASEDIR/Annu/includes/ihm.inc.php";   
if (isset($_POST['Sauver']))
			{	
			header ("location: ./export.php");
			exit;}
	
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<HEAD>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1" >
	<TITLE></TITLE>
	<meta name="generator" content="Bluefish 1.0.7">
	<META NAME="CREATED" CONTENT="20051226;22304481">
	<META NAME="CHANGED" CONTENT="20051226;22565970">
	<LINK href="../style/style.css" rel="stylesheet" type="text/css">
	</HEAD>
<BODY LANG="fr-FR" DIR="LTR">
<H1 class='title'>Configuration du cahier de textes</H1>
<?
/**************************************************
* positionnement du flag d'absence dans le fichier de conf *
***************************************************/
	if (isset($_POST['res_nores']))
		{
		require_once "../Includes/config.inc.php";
		$com= 'mv /usr/share/lcs/Plugins/Cdt/Includes/config.inc.php /usr/share/lcs/Plugins/Cdt/Includes/config.inc.php.tmp && cat /usr/share/lcs/Plugins/Cdt/Includes/config.inc.php.tmp | sed -e "s/ABSENCE='.$FLAG_ABSENCE.'/ABSENCE='.$_POST['absence'].'/g" > /usr/share/lcs/Plugins/Cdt/Includes/config.inc.php && rm /usr/share/lcs/Plugins/Cdt/Includes/config.inc.php.tmp';
		exec($com,$rien,$retour);
		}
//le fichier de conf doit être ré-inclus après une modif éventuelle	
	include "../Includes/config.inc.php";

//changement du grain de sel 
if (isset($_POST['change_grain']))
		{
		
		$com= 'mv /usr/share/lcs/Plugins/Cdt/Includes/config.inc.php /usr/share/lcs/Plugins/Cdt/Includes/config.inc.php.tmp && 
		 pass=`pwgen -1`  && cat /usr/share/lcs/Plugins/Cdt/Includes/config.inc.php.tmp | sed -e "s/^\$Grain.*/\$Grain=\"$pass\";/g" > /usr/share/lcs/Plugins/Cdt/Includes/config.inc.php && rm /usr/share/lcs/Plugins/Cdt/Includes/config.inc.php.tmp';
		exec($com,$rien,$retour);
		if ($retour == 0)  $cle="ok"; else $cle="ko";
	
		}


/***********************
*  Importation de l'annuaire *
*************************/

	if (isset($_POST['Importer']))
		{
//initialisation d'un tableau
		$data=array();
		$mess1="";
		//recherche des groupes classes
		$groups=search_groups('cn=classe*');
		if (count($groups))
			{    
			for ($loop=0; $loop < count($groups); $loop++)
		        {
				//raccourci des noms pour le format court
				$short=explode("_",$groups[$loop]["cn"]);
				if ($_POST['size']=="court")
				$data[$loop]=$short[count($short)-1];//dernier "match"
				else $data[$loop]=$groups[$loop]["cn"];	//format long
		        }
			}
		else  $mess1= "<h3 class='nook'> Erreur dans l'importation <BR></h3>";
	}
		
if (isset($_POST['Créer']))
		{		
//traitement du fichier texte		
		if (!empty($_FILES["FileSelection3"]["name"]))
			{
		
			if (($_FILES["FileSelection3"]["size"]>0) && ($_FILES["FileSelection3"]["type"]=="text/plain"))
				{
				$nomFichier = $_FILES["FileSelection3"]["name"] ;
				$nomTemporaire = $_FILES["FileSelection3"]["tmp_name"] ;
				//chargement du fichier
				copy($nomTemporaire,"../Includes/".$nomFichier);
				//renommage du fichier
				rename("../Includes/".$nomFichier,"../Includes/flist.txt");
				//Ouverture du fichier		
				if($fichier_ok = fopen("../Includes/flist.txt", "r"))
					{
					$loop=0;
					//extraction des données
					while(!feof($fichier_ok))
						{
							$données_extraites = fgetcsv($fichier_ok, 2048);
							for($n=0; $n<count($données_extraites); $n++)
							{				
							$data[$loop]=$données_extraites[$n];
							$loop++;
							}
											}
			
					fclose($fichier_ok);
					}	
				//effacement du fichier
				unlink ("../Includes/flist.txt");
				
				//message
				if (count($data)>0) $mess1= "<h3 class='cok'>1. Le traitement du fichier texte s'est terminé avec succès."."<BR></h3>";
				else $mess1= "<h3 class='nook'> Erreur dans le traitement du fichier texte (Fichier erroné)"."<BR></h3>";
				}
				else $mess1= "<h3 class='nook'> Erreur dans l'importation du fichier texte "."<BR></h3>";
				}
		}
	
//création du fichier
if (isset($_POST['Créer']) || isset($_POST['Importer']))
		{
			$nom_fichier="../Includes/data.inc.php";
			$fichier=fopen($nom_fichier,"w");
			fputs($fichier, "<? \n");
			for ($index=0;$index<count($data);$index++)
			{fputs($fichier,"\$classe[$index]=\"$data[$index]\";\n");}
			fputs($fichier, " ?>\n");
			fclose($fichier);
		}
	
//si clic sur Enregistrer	
if (isset($_POST['Enregistrer']))
	{
		$nom_fichier="../Includes/creneau.inc.php";
		$fichier=fopen($nom_fichier,"w");
		fputs($fichier, "<? \n");
		for ($index=0;$index<10;$index++)
		{
			fputs($fichier,"\$deb[$index]=".mkTime($_POST["hd".$index],$_POST["md".$index],0).
			";\$fin[$index]=".mkTime($_POST["hf".$index],$_POST["mf".$index],0).
			";\$dif[$index]=".(mkTime($_POST["hd".$index],$_POST["md".$index],0)- mkTime(8,0,0)).";\n");
		}
		fputs($fichier, " ?>\n");
		fclose($fichier);
	}
	
/******************
* test de la cohérence *
*******************/
	
if (isset($_POST['test']))
	{
	//recherche des groupes classes
	$groups=search_groups('cn=classe*');
		
	//Pour chaque classe, recherche d'une occurence unique dans l'annuaire
		include "../Includes/data.inc.php"; 
		//initialisation des variables
		$index=0;
		$error=array();
		//recherche d'occurence pour chaque classe
		for($n=0; $n<count($classe); $n++)
			{
			$occ=0;
			for ($loop=0; $loop < count($groups); $loop++)
				{
				if ((mb_ereg("(_$classe[$n])$",$groups[$loop]["cn"])) || ($classe[$n]==$groups[$loop]["cn"]))
				$occ++;
				}
			//si le nb d'occurence est <> de 1, -->problème	
			if ($occ!=1) 
				{
				$error[$index]=$classe[$n];
				$index++;
				}
			}
		if (count($error)==0)
			{
			echo "<script type='text/javascript'>
			<!--
			alert('La liste des classes est cohérente avec l\'annuaire');
			// -->
			</script>";
			}
			else
				{
				echo "<script type='text/javascript'>";
				echo "alert('Incohérence pour : ";
				for($n=0; $n<count($error); $n++)
					{
					echo $error[$n].' ';
					}
				echo "')</script>";
				}	
	}
/**************	
*  gestion de la bd *
***************/
//si clic sur le bouton Archiver
if (isset($_POST['Archiver']))
	{	
	// Vérifier $name_arch et la débarrasser de tout antislash et tags possibles
	if (strlen($_POST['name_arch']) > 0)
		{ 
		$name_arch= addSlashes(strip_tags(stripslashes($_POST['name_arch'])));
		}
	else
		{ // Si aucun commentaire n'a été saisi
		$name_arch= date("Y",mktime()-31536000)."_".date("Y");
		}
	
//Vérification d'une archive existante
	$exist=0;
	$TablesExist= mysql_query("show tables");
	while ($table=mysql_fetch_row($TablesExist))
	if (mb_ereg("$name_arch$",$table[0])) $exist=1;
	if ($exist==0)
		{
		// Créer la requête.
		$rq1 = "create table onglets$name_arch as select * from onglets";
		$rq2= "create table cahiertxt$name_arch as select * from cahiertxt";
 
		// lancer la requête
		$result1 = @mysql_query ($rq1) or die (mysql_error());
		$result2 = @mysql_query ($rq2) or die (mysql_error());
				
		if (!($result1&&$result2)) $mess2="<h3 class='nook'>  l'achive n'a pu être créée";
		}
	else
		{
		echo "<script type='text/javascript'>";
		echo" if (confirm('Remplacer l\'archive existante ? ')){";	        
		echo ' location.href = "';echo $_SERVER['PHP_SELF'];echo'"+ "?delarch=" + "yes" + "&nom_arch=" + "'.$name_arch.'#bdd" ;} else {';
		echo ' location.href = "';echo $_SERVER['PHP_SELF'];echo'"   ;} </script> ';}
		}
	
//confirmation de remplacement de l'archive	
if (isset($_GET['delarch']))
	{
	if ($_GET['delarch']=='yes')
		{
	// Créer la requête.
		$name_arch = $_GET['nom_arch'];
		$rq1 = "DROP TABLE IF EXISTS onglets$name_arch ";
		$rq2 = "DROP TABLE IF EXISTS cahiertxt$name_arch";
		$rq3 = "create table onglets$name_arch as select * from onglets";
		$rq4 = "create table cahiertxt$name_arch as select * from cahiertxt";
 
		// lancer la requête
		$result1 = @mysql_query ($rq1) or die (mysql_error());
		$result2 = @mysql_query ($rq2) or die (mysql_error());
		$result3 = @mysql_query ($rq3) or die (mysql_error());
		$result4= @mysql_query ($rq4) or die (mysql_error());
		
		if (!($result1&&$result2&&$result3&&$result4)) $mess2="<h3 class='nook'>  l'achive n'a pu être créée";
		}
	}
//initilisation des tables
	if (isset($_POST['Vider']))
			{
			echo "<script type='text/javascript'>";
			echo" if (confirm('Confirmer l\'effacement du contenu des tables cahiertxt, absences, devoir et onglets ? ')){";	        
			echo ' location.href = "';echo $_SERVER['PHP_SELF'];echo'"+ "?vidtab=" + "yes" ;} else {';
			echo ' location.href = "';echo $_SERVER['PHP_SELF'];echo'"   ;} </script> ';
			}
	
//confirmation d'initialisation des tables	
if (isset($_GET['vidtab']))
	{
	if ($_GET['vidtab']=='yes')
		{
		// Créer la requête.
		$rq1 = "TRUNCATE TABLE onglets";
		$rq2 = "TRUNCATE TABLE cahiertxt";
 		$rq3 = "TRUNCATE TABLE devoir";
 		$rq4 = "TRUNCATE TABLE absences";
 		
		// lancer la requête
		$result1 = @mysql_query ($rq1) or die (mysql_error());
		$result2 = @mysql_query ($rq2) or die (mysql_error());
		$result3 = @mysql_query ($rq3) or die (mysql_error());
		$result4 = @mysql_query ($rq4) or die (mysql_error());
		if ($result1 && $result2 && $result3 && $result4) $mess2="<h3 class='cok'> les tables onglets et cahiertxt ont été vidées";
		else $mess2="<h3 class='nook'> Une erreur s'est produite lors de l'effacement des données";
		
		
		}
	}
if (isset($_POST['Restaurer']))
		{	
function get_key() 
		  	{
			$val=array();
		    $cmd = "cat /etc/LcSeConfig.ph | grep 'mysqlServerPw ' | cut -d\"'\" -f2";
		    exec($cmd,$val,$ret_val);
			return $val[0];
			}	
						
//traitement du fichier texte		
		if (!empty($_FILES["FileSelection4"]["name"]))
			{
		//
			if (($_FILES["FileSelection4"]["size"]>0) && ($_FILES["FileSelection4"]["type"]=="text/x-sql"))
				{
				//$nomFichier = $_FILES["FileSelection3"]["name"] ;
				//$nomTemporaire = $_FILES["FileSelection3"]["tmp_name"] ;
				$res = get_key();
				$cmd2="mysql -uroot -p".$res." ". DB_NAME ." < ".$_FILES['FileSelection4']['tmp_name'];
				exec($cmd2,$rien,$retour);
				if ($retour == 0)  $mess2= "<h3 class='cok'>  La restauration de la base de données a réussi"; 
				else $mess2 ="<h3 class='nook'>  La restauration de la base de données a échoué !"."<BR></h3>";
				//$mess2= $cmd;
				}
				else $mess2= "<h3 class='nook'> Erreur dans l'importation du fichier de sauvegarde "."<BR></h3>";
			}
			else $mess2= "<h3 class='nook'> Vous devez sélectionner un fichier de sauvegarde "."<BR></h3>";
		}

?><form action="<?php echo htmlentities($_SERVER['PHP_SELF']).'#liste'; ?>" method="post" enctype="multipart/form-data">
<a name="liste"></a>
<fieldset id="field7">
<legend id="legende"> Cr&eacute;ation du fichier &quot;Liste des classes&quot;  &nbsp  
<?
//affichage de la liste des classes
include ("../Includes/data.inc.php");
			$jo="";
			echo "<select name='CLASSE' style='background-color:#E6E6FA'></h4>";
			foreach ($classe as $clé => $valeur)
			  { 
			  echo "<option valeur=\"$valeur\"";
			  if ($valeur==$jo) {echo 'selected';}
			  echo ">$valeur</option>\n";
			  }
			  echo "</select>\n";
?>
 
</legend>
<DIV ALIGN=LEFT>
<?
/**********************
 affichage du formulaire *
************************/

			echo '<H4 class="perso">La liste des classes sous forme de menu d&eacute;roulant est &eacute;labor&eacute;e à partir d\'un fichier Php.<br> 
			Ce fichier peut être g&eacute;n&eacute;r&eacute; soit : </H4>
			<ul>
			<li> &agrave; partir de l\'annuaire du LCS
					<P>
						<input type="radio" name="size" value="long"> Format long (Classe_LT_2DE1) <br>
						<input type="radio" name="size" value="court" checked> Format court (2DE1) Attention, pour utiliser ce format, le nom de la classe ne doit pas comporter de tiret bas _ ( exemple : 3_A ) </P>
						<P> &nbsp;&nbsp;<input type="submit" name="Importer" value="Importer" > 
					</P></LI>	
					
					<li> &agrave; partir d\'un fichier texte contenant la liste des classes s&eacute;par&eacute;es par une virgule.(ex
					: 2nde1,1ereS,TermS,... TS1) <BR>	
					(Attention : pas de virgule ni de caractère CR à la fin de la dernière ligne)
						<br>
						Indiquez le chemin du fichier texte : 
						<BR><INPUT TYPE=FILE NAME="FileSelection3" SIZE=30>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<input type="submit" name="Créer" value="Créer le fichier Php" >
						<br><br>
					<a HREF="./edit.php" onClick="auth_popup(); return false" title="Modifier">EDITER  
					</A>&nbsp le fichier csv actuel pour le modifier
					</li>
					</UL>
					<H4 class="perso"><input type="submit" name="test" value="Tester la cohérence" >  du fichier classe avec l\'annuaire pour un bon fonctionnement.
					</H4>';
	
//affichage du résultat
		if ($mess1!="") echo $mess1;
?>
</div>
</fieldset>
</form>
<form action="<?php echo htmlentities($_SERVER['PHP_SELF']).'#key'; ?>" method="post">
<a name="key"></a>
<fieldset id="field7">
<legend id="legende"> Cryptage</legend>
<DIV ALIGN=LEFT>
<?
echo '<H4 class="perso"> L\'acc&eacute;s au cahier de texte par les parents se fait par un lien avec le nom des classes crypt&eacute;es. Le crytage doit être 
renouvel&eacute; en d&eacute;but de chaque ann&eacute;e pour que les liens de l\'ann&eacute;e pr&eacute;c&eacute;dente ne soient plus valides.
		<P>
			<input type="submit" name="change_grain" value="Changer" > la clé de cryptage ';
			if ( $cle=="ok") echo  "<h3 class='cok'> La clé a &eacute;t&eacute; chang&eacute;e ";
			elseif ( $cle=="ko") echo " <h3 class='nook'> Echec du changement de cl&eacute";
			echo '</P></H4>';			
?>
</div>
</fieldset>
</form>
<form action="<?php echo htmlentities($_SERVER['PHP_SELF']).'#abs'; ?>" method="post">
<a name="abs"></a>
<fieldset id="field7">
<legend id="legende"> Visualisation des absences </legend>
<DIV ALIGN=LEFT>
<?
echo '<H4 class="perso"><br> Le carnet d\'absences pour les &eacute;l&egrave;ves et les parents doit &ecirc;tre activ&eacute uniquement si les informations affich&eacute;es sont significatives.
 Cela implique que tous les professeurs saisissent les absences via le carnet d\'absences du Cdt. 
		<P>
		Actuellement, la visualisation des absences est';if ($FLAG_ABSENCE==0) echo ' : Désactivée'; else echo ' : Activée ';		
		echo '</P><P><input type="radio" name="absence" value="0" ';
			if ($FLAG_ABSENCE==1) echo 'checked';
			echo '> Désactiver la visualisation des absences<br>
			<input type="radio" name="absence" value="1"';
			if ($FLAG_ABSENCE==0) echo 'checked';
			echo '> Activer la visualisation des absences 
			&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
			<input type="submit" name="res_nores" value="Valider" >&nbsp;&nbsp;&nbsp;&nbsp;
			</P></H4>';
?>
</div>
</fieldset>
</form>
<a name="bdd"></a>
<form action="<?php echo htmlentities($_SERVER['PHP_SELF']).'#bdd'; ?>" method="post" enctype="multipart/form-data">
<fieldset id="field7">
<legend id="legende"> Gestion de la base de données</legend>
<DIV ALIGN=LEFT>
<?
echo '
<ol><H4 class="perso">
<li><u><b>Archivage</b></u><P>En fin d\'année scolaire, vous pouvez archiver le cahier de textes, ce qui permettra aux profs de consulter leur cahier des années antérieures.<br> Indiquez ci dessous le nom que vous voulez donner à l\'archive. Ce nom servant à renommer les tables de données, ne mettez pas de caractères -  :  / \\ etc ... Il est conseillé de simplement remplacer les chiffres dans le nom proposé.<br><BR>

<input type="submit" name="Archiver" value="Créer une archive nommée" >&nbsp&nbsp&nbsp&nbsp<INPUT TYPE=TEXT NAME="name_arch" Value="'.date("Y",mktime()-31536000)."_".date("Y").'"SIZE=9>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
En début d\'année, &nbsp <input type="submit" name="Vider" value="Vider les tables" >&nbsp  de données du cahier de texte.<br><br>

Archives existantes : 
		';
$TablesExist= mysql_query("show tables");
	while ($table=mysql_fetch_row($TablesExist))
	if (mb_ereg("^onglets[[:alnum:]]",$table[0]))
	{
	$archive=explode('s',$table[0]);
	echo ($archive[1]). ' &nbsp ';
	}
	
	
	echo '</P></li>';
echo '<li><u><b> Sauvegarde</b></u><P>Le bouton ci dessous permet de générer un fichier de sauvegarde complète de la base de données (structure + données)<BR><BR>

<input type="submit" name="Sauver" value="Sauvegarder la base de données" >	</P></li>
<li><u><b> Restauration</b></u><P>Le bouton ci dessous permet d\'importer les données à partir d\'un fichier de sauvegarde. Attention, la structure et  les données présentes dans la base, seront SUPPRIMEES et REMPLACEES par celles du fichier sélectionné <BR>
Sélectionner le fichier de sauvegarde : 
						<BR><INPUT TYPE=FILE NAME="FileSelection4" SIZE=30>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="submit" name="Restaurer" value="Restaurer la base de données" >					
						</P></li>
</ol></H4>';
	
	if ($mess2!="") echo $mess2;
	
?>
</div>
</fieldset>
</form>
<form action="<?php echo htmlentities($_SERVER['PHP_SELF']).'#creneau'; ?>" method="post" >
<a name="creneau"></a>
<fieldset id="field7">
<legend id="legende"> Param&egrave;trage des cr&eacute;neaux horaires </legend>
<DIV ALIGN=LEFT>
<?
//Affichage du formulaire de paramétrage des créneaux horaires

		include "../Includes/creneau.inc.php";
		//pour les dix créneaux
		for ($index=0;$index<10;$index++)
		{
		//créneaux du matin
		if ($index<5)
		{
		echo "<H4 class='perso'>M ".($index+1)." commence à  " ;
		}
		//créneaux de l'après-midi
		if ($index>4)
		{
		echo "<H4 class='perso'>S ".($index-4)." commence à  " ;
		}
		//affichage d'un menu déroulant heure début 8h 17h
		echo "<select name='hd".$index."'\n >";
		$heure = 8;
		while ($heure <= 17)
		{ echo "<option valeur=\"$heure\"";
		if ($heure==date('G',$deb[$index])) {echo 'selected';}
		echo ">$heure</option>\n";
		$heure++;
		}
		echo "</select>\n h ";
		
		//affichage d'un menu déroulant minutes début0 55 min
		echo "<select name='md".$index."' \n >";
		$min = 00;
		while ($min <= 55)
		{ echo "<option valeur=\"$min\"";
		if ($min==date('i',$deb[$index])) {echo 'selected';}
	    echo ">$min</option>\n";
		$min+=5;
		}
		echo "</select>\n min "."&nbsp;et se termine à &nbsp;";
		//affichage d'un menu déroulant heure fin 8h 18h
		echo "<select name='hf".$index."' \n >";
		$heure = 8;
		while ($heure <= 18)
		{ echo "<option valeur=\"$heure\"";
		if ($heure==date('G',$fin[$index])) {echo 'selected';}
		echo ">$heure</option>\n";
		$heure++;
		}
		echo "</select>\n h ";
		
		//affichage d'un menu déroulant minutes fin 0 55 min
		echo "<select name='mf".$index."' \n >";
		$min = 0;
		while ($min <= 55)
		{ echo "<option valeur=\"$min\"";
		if ($min==date('i',$fin[$index])) {echo 'selected';}
		echo ">$min</option> \n";
		$min+=5;
		}
		echo "</select> min \n </h4>";
		}
		//fin de boucle 
		
		//affichage du bouton
		echo '<div align="center"><input type="submit" name="Enregistrer" value="Enregistrer" ></div></H4></P>';
	
//affichage du résultat de paramètrage
		
	for ($h=0; $h<9; $h++) 
		{
		if ($deb[$h]>=$deb[$h+1] || $deb[$h]>=$fin[$h]) $mess3="  Il y a une incohérence dans les horaires ! ";
		}
		if (isset ($mess3)) echo "<h3 class='nook'>".$mess3;
?>
</div>
</fieldset>	
</FORM>
</BODY>
</HTML>



