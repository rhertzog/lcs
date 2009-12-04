<?
/* =============================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 2.0 du 25/10/2009
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
// si clic sur le bouton "cr�er la rubrique"
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
	$val_classe=$classe[0];//valeur par d�faut de la variable classe
	$val_matiere='';//valeur par d�faut de la variable matiere
	$val_prefix='';//valeur par d�faut de la variable prefixe
	$cible="";//valeur par d�faut du num�ro de la rubrique
	$val_restri=0;//valeur par d�faut de la variable restriction
	
	}
session_name("Cdt_Lcs");
@session_start();
	//si la page est appl�e par un utilisateur non identifi�,
		if (!isset($_SESSION['cequi'])) {exit;}
		// autorisation d'acc�s ?  si pas prof, non autoris�
		if (($_SESSION['cequi']!="prof") && ($code !='s61lyc6uby54trh'))
			{
	        print('<p align="center"> </p> <p align="center"><font face="Arial" size="5" color="#FF6600">
			D�sol�, vous ne pouvez pas acc�der � cette page</font></p>');
    	
}
		
?>
<! config_ctxt.php version 1.0 par Ph LECLERC - Lgt "Arcisse de Caumont" 14400 BAYEUX - philippe.leclerc1@ac-caen.fr >
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<HEAD>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1" >
	<TITLE>Personnalisation du cahier textes</TITLE>
	<meta name="generator" content="Bluefish 1.0.7">
	<META NAME="CREATED" CONTENT="20041027;9273332">
	<META NAME="CHANGED" CONTENT="20041029;16402790">
	<LINK href="../style/style.css" rel="stylesheet" type="text/css">
	<link  href="../style/navlist-prof.css" rel=StyleSheet type="text/css">
	<link  href="../style/navlist-eleve.css" rel=StyleSheet type="text/css">
<!--[if IE]>
<link href="../style/style-ie.css"  rel="stylesheet" type="text/css"/>
<![endif]-->
</HEAD>
<BODY>
<?php
//recherche des groupes cours
$ind=count($classe);
include ("/var/www/lcs/includes/user_lcs.inc.php");
include ("/var/www/lcs/includes/functions.inc.php");
list($user, $groups)=people_get_variables($_SESSION['login'], true);
if ( count($groups) ) {
    for ($loop=0; $loop < count ($groups) ; $loop++) {
      if (ereg("^Cours",$groups[$loop]["cn"])){
      $classe[$ind]=$groups[$loop]["cn"];
      $ind++;
    	}
    }
}

require_once ('../Includes/config.inc.php');// Connexion � la base de donn�es		
//Traitement du formulaire

// �1.ENREGISTRER OU MODIFIER UNE RUBRIQUE
if (isset($_POST['enregistrer']) || isset($_POST['modifier']))
	{ 
	// V�rifier $matiere  et la d�barrasser de tout antislash et tags possibles
	if (strlen($_POST['matiere']) > 0)
		{ 		$matiere  = addSlashes(strip_tags(stripslashes($_POST['matiere'])));}
		else
			{ // Si aucun commentaire n'a �t� saisi
			  $matiere = "";
			}
	// V�rifier $pr�fixe et la d�barrasser de tout antislash et tags possibles
	if (strlen($_POST['prefixe']) > 0)
		{ 		$prefixe= addSlashes(strip_tags(stripslashes($_POST['prefixe'])));	}
		else
			{ // Si aucun commentaire n'a �t� saisi
			  $prefixe= "";
			}
	// nom dela classe (division) s�lectionn�e 
	$div = ($_POST['div']);
	// V�rifier $restriction et la d�barrasser de tout antislash et tags possibles
	if (strlen($_POST['restriction']) > 0)
			{
		 		if ($_POST['restriction'] =="OUI") $restriction =1 ;
				else $restriction =0;
			}
	else
			{ // Si aucune option n'a �t� s�lectionn�e
			  $restriction=0;
			}
	// Cr�er la requ�te d'�criture pour l'enregistrement des donn�es
	if (isset($_POST['enregistrer']))
			{
			$rq = "INSERT INTO onglets (login ,prof ,classe, matiere, prefix ) 
				   VALUES ( '{$_SESSION['login']}', '{$_SESSION['name']}', '$div',
				   '$matiere', '$prefixe' )";
			}
//Cr�er la requ�te pour la mise � jour des donn�es	
	if (isset($_POST['modifier']))
			{$cible= ($_POST['numrub']);
			$rq = "UPDATE  onglets SET classe='$div', matiere='$matiere', prefix='$prefixe'  
				WHERE id_prof='$cible'";
			}
		
// lancer la requ�te
		$result = mysql_query($rq); 
		if (!$result)  // Si l'enregistrement est incorrect
			{                           
			 echo "<p>Votre rubrique n'a pas pu �tre enregistr�e � cause d'une erreur syst�me".
				  "<p></p>" . mysql_error() . "<p></p>";
// refermer la connexion avec la base de donn�es
			mysql_close();
//sortir			
			exit();
			}
	}

//fin enregistrement ou modification d'une rubrique


//�2. SUPPRESSION D'UNE RUBRIQUE

//si la suppression d'une rubrique est confirm�e
if (isset($_GET['delrub'])&& isset($_GET['num'])) 
	{
	$action=$_GET['delrub'];
	$cible=$_GET['num'];
	
//la rubrique existe elle ?
	$rq = "SELECT login FROM onglets WHERE id_prof='$cible'  ";
// lancer la requ�te
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


// �3. MODIFICATION D'UNE RUBRIQUE
if (isset($_GET['modrub'])&& isset($_GET['num'])) 
	{
	$action=$_GET['modrub'];
	$cible=$_GET['num'];
	
//la rubrique existe elle ?
	$rq = "SELECT login,classe, matiere, prefix FROM onglets WHERE id_prof='$cible'  ";

// lancer la requ�te
	$result = @mysql_query ($rq) or die (mysql_error());
	$nb = mysql_num_rows($result);

//si elle existe, on r�cup�re les datas pour les afficher dans les champs
	if (($nb==1) &&($action=='erg45er5ze'))
		{while ($enrg = mysql_fetch_array($result, MYSQL_NUM)) 
		{
		$val_classe=$enrg[1];
		$val_matiere=$enrg[2];
		$val_prefix=$enrg[3];
		
		}}
	} 
	
/* recherche des onglets existants*/
// Cr�er la requ�te.
$rq = "SELECT classe,matiere,id_prof,prof,prefix FROM onglets
 WHERE login='{$_SESSION['login']}' ORDER BY id_prof ASC ";

// lancer la requ�te
$result = @mysql_query ($rq) or die (mysql_error());

// Combien y a-t-il d'enregistrements ?	
$nb = mysql_num_rows($result); 	
//FIN TRAITEMENT FORMULAIRE	

/*================================
   -      Affichage du formulaire  -
   ================================*/ 
?>
<div id="cfg-container">
<div id="cfg-contenu">
<form id="cfg-form" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>?bn16drgv2r=s61lyc6uby54trh" method="post">
<H1 class='title'><U>Personnalisation du cahier de textes de</U> &nbsp<FONT COLOR="#990000"><?echo $_SESSION['nomcomplet'];?></FONT></h1>
<ol>
	<li>Cr&eacute;ation des rubriques (onglets) <BR>
		<ul>
			<li>Choisissez votre classe ou votre groupe : 
			<?//liste de selection de la classe
			  echo "<select name='div'>\n";
			  foreach ($classe as $cl� => $valeur)
			    { echo "<option valeur=\"$valeur\"";
			      if ($valeur==$val_classe) {echo 'selected';}
			        echo ">$valeur</option>\n";
  				}
  			echo "</select>\n";?></li>
			<li>Indiquez la mati&egrave;re enseign&eacute;e : <INPUT TYPE=TEXT NAME="matiere" VALUE="<? echo $val_matiere ?>" SIZE=14 MAXLENGTH=14></li>
		</ul>
	<li>Sur	les onglets du <B>cahier de textes de vos &eacute;l&egrave;ves</B> apparaitront la mati&egrave;re enseign&eacute;e et votre nom
		pr&eacute;c&eacute;d&eacute; d&rsquo;un &quot;&nbsp;pr&eacute;fixe&nbsp;&quot;
		<ul>
			<li>Indiquez le <B>pr&eacute;fixe </B> que vous souhaitez voir appara&icirc;tre (M, Mme, pr&eacute;nom abr&eacute;g&eacute;, ....) : <INPUT TYPE=TEXT NAME="prefixe" Value="<? echo $val_prefix ?>"SIZE=10></li>
		</ul>
</ol>

		
<div id="cfg-btn">	
		<input type="hidden" name="numrub" value= "<? echo $cible ; ?>" />
		
		<? if (isset($_GET['modrub'])&& isset($_GET['num'])) 
		{echo ('<INPUT class="modif" TYPE=SUBMIT NAME="modifier" VALUE="">');}
		else {echo ('<INPUT class="enreg" TYPE=SUBMIT NAME="enregistrer" VALUE="">');}
		if ($nb>0) {echo('<INPUT class="retour" TYPE=SUBMIT NAME="retour" VALUE="">');}
		?>
<INPUT TYPE="button" class="annul" VALUE="" onClick="history.back()"/> 
</div>	
</FORM>
<?php 

//AFFICHAGE DES ONGLETS EXISTANTS


//on r�cup�re les donn�es
$loop=0;
while ($enrg = mysql_fetch_array($result, MYSQL_NUM)) 
	{
	$clas[$loop]=$enrg[0]; //classe
	$mat[$loop]=$enrg[1]; // matiere
	$numero[$loop]=$enrg[2];//num�ro de la rubrique
	$prof[$loop]=$enrg[3]; // nom du prof
	$pref[$loop]=$enrg[4]; //pr�fixe
	$loop++;
	}

	
//cr�ation du tableau , onglets prof
echo '<div id="cfg-onglet">';
echo '<H4 class="perso">Aspect des onglets de votre cahier de textes (cliquer sur un onglet pour <B><FONT SIZE="3" color="#660000">Modifier</FONT></B> la rubrique associ�e )</H4>';
echo ("<ul id='cfg-navlist'>");
for($x=0;$x < $nb;$x++){
	if ($x==0) 
	{
		echo ("<li><a href='config_ctxt.php?modrub=erg45er5ze&num=$numero[$x]'title='Modifier cette rubrique'>$mat[$x]<br>$clas[$x]"."</a></li>");
	}
	else 
	{
		echo("<li><a href='config_ctxt.php?modrub=erg45er5ze&num=$numero[$x]' title='Modifier cette rubrique' >$mat[$x]<br>$clas[$x]</a></li>");
	}
}
	echo("</ul>");


echo ('<H4 class="perso"> Aspect des onglets du cahier de textes des �l�ves
	( cliquer sur un onglet pour <B><FONT SIZE="3" color="#660000">Supprimer</FONT></B> la rubrique associ�e )</H4>');

	
//cr�ation du tableau , onglets �l�ves
echo ("<ul id='cfg-navlist-elv'>");
	for($x=0;$x < $nb;$x++)
		{
		if ($x==0) 
			{
			echo("<li><a href='config_ctxt.php?suppr=yes&numong=$numero[$x]' title='Supprimer cette rubrique'>".$mat[$x]." (".$clas[$x].")<br>".$pref[$x]."&nbsp;".$prof[$x]."</a></li>");
			}
			else 
			{
			echo("<li><a href='config_ctxt.php?suppr=yes&numong=$numero[$x]' title='Supprimer cette rubrique'>".$mat[$x]." (".$clas[$x].")<br>".$pref[$x]."&nbsp;".$prof[$x]."</a></li>");
			}
		}
	echo("</ul>");	
echo '</div>';
	
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
  echo '</div>'; 	//fin du contenu
echo '</div>'; 	//fin du container
	include ('../Includes/pied.inc'); 
echo '</div>';
?>

</BODY>
</HTML>
