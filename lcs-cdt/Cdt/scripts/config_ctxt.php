<?
/* =============================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 1.0 du 29/10/2008
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script de personnalisation du cahier de textes -
			_-=-_
   ============================================= */
//inclusion fichier liste des classes
include ('../Includes/data.inc.php');
   // si clic sur le bouton "retour au cahier de texte"
if (isset($_POST['retour']))
	{
	header("location: cahier_texte_prof.php");
	exit;
	}
// si clic sur le bouton "créer la rubrique"
if(isset($_POST['bn16drgv2r'])) 
	{	
	$code=$_GET['bn16drgv2r'];
	}
	else
	{
	$code='';
	}
	
//premiere passe -> initialisation des variables	
if ($code!='s61lyc6uby54trh') 
	{
	$val_classe=$classe[0];//valeur par défaut de la variable classe
	$val_matiere='';//valeur par défaut de la variable matiere
	$val_prefix='';//valeur par défaut de la variable prefixe
	$cible="";//valeur par défaut du numéro de la rubrique
	$val_restri=0;//valeur par défaut de la variable restriction
	
	}
session_name("Cdt_Lcs");
@session_start();
	//si la page est applée par un utilisateur non identifié,
		if (!isset($_SESSION['cequi'])) {exit;}
		// autorisation d'accés ?  si pas prof, non autorisé
		if (($_SESSION['cequi']!="prof") && ($code !='s61lyc6uby54trh'))
			{
	        print('<p align="center"> </p> <p align="center"><font face="Arial" size="5" color="#FF6600">
			Désolé, vous ne pouvez pas accéder à cette page</font></p>');
    	
}
		
?>
<! config_ctxt.php version 1.0 par Ph LECLERC - Lgt "Arcisse de Caumont" 14400 BAYEUX - philippe.leclerc1@ac-caen.fr >
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<HEAD>
	<META HTTP-EQUIV="CONTENT-TYPE" CONTENT="text/html; charset=windows-1252">
	<TITLE>Personnalisation du cahier textes</TITLE>
	<meta name="generator" content="Bluefish 1.0.7">
	<META NAME="CREATED" CONTENT="20041027;9273332">
	<META NAME="CHANGED" CONTENT="20041029;16402790">
	<LINK href="../style/style.css" rel="stylesheet" type="text/css">
</HEAD>
<BODY LANG="fr-FR" TEXT="#000000" BACKGROUND="../images/espperso.jpg">
<style>
<!--
a:link {text-decoration:none; color: #000000; font-family:   arial, verdana ; font-size : 8pt }
a:visited {text-decoration: none; color: #cc0000; font-family: arial, verdana ; font-size: 8pt}
a:active {text-decoration: none; color: #000099; font-family: arial, verdana ; font-size: 10pt}
a:hover {text-decoration: none; color: #990000; background-color: #FFFFCC ;font-family: arial, verdana ; font-size: 8pt}
-->
</style>

<?php 


// Connexion à la base de données
require_once ('../Includes/config.inc.php');		

//Traitement du formulaire

// §1.ENREGISTRER OU MODIFIER UNE RUBRIQUE

if (isset($_POST['enregistrer']) || isset($_POST['modifier']))
	{ 
	
// Vérifier $matiere  et la débarrasser de tout antislash et tags possibles
	if (strlen($_POST['matiere']) > 0)
		{ 		$matiere  = addSlashes(strip_tags(stripslashes($_POST['matiere'])));}
		else
			{ // Si aucun commentaire n'a été saisi
			  $matiere = "";
			}
	
// Vérifier $préfixe et la débarrasser de tout antislash et tags possibles
	if (strlen($_POST['prefixe']) > 0)
		{ 		$prefixe= addSlashes(strip_tags(stripslashes($_POST['prefixe'])));	}
		else
			{ // Si aucun commentaire n'a été saisi
			  $prefixe= "";
			}
// nom dela classe (division) sélectionnée 
	$div = ($_POST['div']);
			
// Vérifier $restriction et la débarrasser de tout antislash et tags possibles
	if (strlen($_POST['restriction']) > 0)
			{
		 		if ($_POST['restriction'] =="OUI") $restriction =1 ;
				else $restriction =0;
			}
		
		else
			{ // Si aucune option n'a été sélectionnée
			  $restriction=0;
			}
			
	
		
// Créer la requête d'écriture pour l'enregistrement des données
	if (isset($_POST['enregistrer']))
			{
			$rq = "INSERT INTO onglets (login ,prof ,classe, matiere, prefix ) 
				   VALUES ( '{$_SESSION['login']}', '{$_SESSION['name']}', '$div',
				   '$matiere', '$prefixe' )";
			}
//Créer la requête pour la mise à jour des données	
	if (isset($_POST['modifier']))
			{$cible= ($_POST['numrub']);
			$rq = "UPDATE  onglets SET classe='$div', matiere='$matiere', prefix='$prefixe'  
				WHERE id_prof='$cible'";
			}
		
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
	}

//fin enregistrement ou modification d'une rubrique


//§2. SUPPRESSION D'UNE RUBRIQUE

//si la suppression d'une rubrique est confirmée
if (isset($_GET['delrub'])&& isset($_GET['num'])) 
	{
	$action=$_GET['delrub'];
	$cible=$_GET['num'];
	
//la rubrique existe elle ?
	$rq = "SELECT login FROM onglets WHERE id_prof='$cible'  ";
// lancer la requête
	$result = @mysql_query ($rq) or die (mysql_error());
	$nb = mysql_num_rows($result); 
//si la rubrique existe, on l'efface	
	if (($nb==1) &&($action==1245))
		{
		$rq = "DELETE  FROM onglets WHERE id_prof='$cible' LIMIT 1";
		$result2 = @mysql_query ($rq) or die (mysql_error());
		}
	}
$action ="";
$num="";


// §3. MODIFICATION D'UNE RUBRIQUE
if (isset($_GET['modrub'])&& isset($_GET['num'])) 
	{
	$action=$_GET['modrub'];
	$cible=$_GET['num'];
	
//la rubrique existe elle ?
	$rq = "SELECT login,classe, matiere, prefix FROM onglets WHERE id_prof='$cible'  ";

// lancer la requête
	$result = @mysql_query ($rq) or die (mysql_error());
	$nb = mysql_num_rows($result);

//si elle existe, on récupère les datas pour les afficher dans les champs
	if (($nb==1) &&($action=='erg45er5ze'))
		{while ($enrg = mysql_fetch_array($result, MYSQL_NUM)) 
		{
		$val_classe=$enrg[1];
		$val_matiere=$enrg[2];
		$val_prefix=$enrg[3];
		
		}}
	} 
	
/* recherche des onglets existants*/
// Créer la requête.
$rq = "SELECT classe,matiere,id_prof,prof,prefix FROM onglets
 WHERE login='{$_SESSION['login']}' ORDER BY id_prof ASC ";

// lancer la requête
$result = @mysql_query ($rq) or die (mysql_error());

// Combien y a-t-il d'enregistrements ?	
$nb = mysql_num_rows($result); 	
//FIN TRAITEMENT FORMULAIRE	

/*================================
   -      Affichage du formulaire  -
   ================================*/ 
?>

<form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>?bn16drgv2r=s61lyc6uby54trh" method="post">
<P ALIGN=CENTER><H1 class='title'><U>Personnalisation
du cahier de textes de</U> &nbsp<FONT COLOR="#990000"><?echo $_SESSION['nomcomplet'];?></FONT></h1></P>

	
			<H4>1. Cr&eacute;ation
			des rubriques (onglets) <BR>&nbsp; &nbsp;   - Indiquez pour chacune de vos classes,
			la mati&egrave;re enseign&eacute;e. &nbsp; &nbsp; <B>Classe : </B>
<?
//liste de selection de la classe

echo "<select name='div'>\n";
  foreach ($classe as $clé => $valeur)
  { echo "<option valeur=\"$valeur\"";
  if ($valeur==$val_classe) {echo 'selected';}
  echo ">$valeur</option>\n";
  }
  echo "</select>\n";
  ?>
		&nbsp &nbsp	<B> Mati&egrave;re : </B><INPUT TYPE=TEXT NAME="matiere" VALUE="<? echo $val_matiere ?>" SIZE=14 MAXLENGTH=14>
		<p ALIGN=LEFT>2. Sur	les onglets du <B>cahier de textes de vos &eacute;l&egrave;ves</B>
		apparaitront la mati&egrave;re enseign&eacute;e et votre nom
		pr&eacute;c&eacute;d&eacute; d&rsquo;un &quot;&nbsp;pr&eacute;fixe&nbsp;&quot;</P>
		<DIV ALIGN=LEFT>
			<P>&nbsp; &nbsp;   - Indiquez le <B>pr&eacute;fixe </B> que vous souhaitez voir appara&icirc;tre (M,
			Mme, pr&eacute;nom abr&eacute;g&eacute;, ....) : <INPUT TYPE=TEXT NAME="prefixe" Value="<? echo $val_prefix ?>"SIZE=10><BR></H4>
	
		</DIV>
	

		<input type="hidden" name="numrub" value= "<? echo $cible ; ?>" />
		<DIV ALIGN=CENTER>
		<? if (isset($_GET['modrub'])&& isset($_GET['num'])) 
		{echo ('<STYLE="margin-left: 0.5cm"><INPUT TYPE=SUBMIT NAME="modifier" VALUE="Enregistrer les modifications">');}
		else {echo ('<P STYLE="margin-left: 0.5cm"><INPUT TYPE=SUBMIT NAME="enregistrer" VALUE="Créer la rubrique">');}
		if ($nb>0) {echo('&nbsp &nbsp <STYLE="margin-left: 0.5cm"><INPUT TYPE=SUBMIT NAME="retour" VALUE="Retour au cahier de textes">');}
		?>
		&nbsp <INPUT TYPE="button" VALUE="Annuler" onClick="history.back()"/> 
		</P>
	
	
	
</FORM>
<?php 

//AFFICHAGE DES ONGLETS EXISTANTS


//on récupère les données
$loop=0;
while ($enrg = mysql_fetch_array($result, MYSQL_NUM)) 
	{
	$clas[$loop]=$enrg[0]; //classe
	$mat[$loop]=$enrg[1]; // matiere
	$numero[$loop]=$enrg[2];//numéro de la rubrique
	$prof[$loop]=$enrg[3]; // nom du prof
	$pref[$loop]=$enrg[4]; //préfixe
	$loop++;
	}
	
//on calcule la largeur des cellules
if ($nb>0) 
	{
	$pourcen = 100/$nb;
	echo ('<H4> Aspect des onglets de votre cahier de textes
	( cliquer sur un onglet pour <B><FONT SIZE="3" color="#660000">Modifier</FONT></B> la rubrique associée ) </H4>');
	
	//création du tableau , onglets prof
	echo("<div align='left'><table width='100%' border='1' bordercolor='#E6E6FF'  cellspacing='0' cellpadding='0'><tr bgcolor='#cccccc' >");
	for($x=0;$x < $nb;$x++)
		{
		if ($x==0) 
			{
			echo("<td width='$pourcen%' bgcolor='#4169E1'><div align='center'><B><a href='config_ctxt.php?modrub=erg45er5ze&num=$numero[$x]'title='Modifier cette rubrique'>$mat[$x]<br>$clas[$x]"."</a></b></div></td>");
			}
			else 
			{
			echo("<td width='$pourcen%'><div align='center'><a href='config_ctxt.php?modrub=erg45er5ze&num=$numero[$x]'title='Modifier cette rubrique'>$mat[$x]<br>$clas[$x]</a></div></td>");
			}
		}
	echo("</tr></table></div><br>");
	echo ('<H4> Aspect des onglets du cahier de textes des élèves
	( cliquer sur un onglet pour <B><FONT SIZE="3" color="#660000">Supprimer</FONT></B> la rubrique associée )</H4>');
	
//création du tableau , onglets élèves
	echo("<div align='left'><table width='100%' border='1' bordercolor='#E6E6FF'  cellspacing='0' cellpadding='0'><tr bgcolor='#cccccc' >");
	for($x=0;$x < $nb;$x++)
		{
		if ($x==0) 
			{
			echo("<td width='$pourcen%' bgcolor='#4169E1'><div align='center'><B><a href='config_ctxt.php?suppr=yes&numong=$numero[$x]'title='Supprimer cette rubrique'>$mat[$x]<br>$pref[$x]  $prof[$x]"."</a></b></div></td>");
			}
			else 
			{
			echo("<td width='$pourcen%'><div align='center'><a href='config_ctxt.php?suppr=yes&numong=$numero[$x]'title='Supprimer cette rubrique'>$mat[$x]<br>$pref[$x]  $prof[$x]</a></div></td>");
			}
		}
	echo("</tr></table></div><br>");
	}
//ouverture d'un popup de confirmation de suppression
if (isset($_GET["suppr"])){
echo "<script type='text/javascript'>";
echo"      if (confirm('Confirmer la suppression de cette rubrique')){";
        
echo ' location.href = "';echo $_SERVER['PHP_SELF'];echo'" + "?num=" +'." '"; echo $_GET["numong"] ;echo "'".' + "&delrub=" +'."1245"." ;}
        else
        {";
echo ' location.href = "';echo $_SERVER['PHP_SELF'];echo'"   ;
		        }
    </script>	
  ';}
	
	include ('../Includes/pied.inc'); 
?>

</BODY>
</HTML>
