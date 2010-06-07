<?
/* =============================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 2.1 du 4/6/2010
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
if (isset($_GET['bn16drgv2r'])) 
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
	$val_classe='--';//valeur par défaut de la variable classe
	$val_matiere='';//valeur par défaut de la variable matiere
	$val_prefix='';//valeur par défaut de la variable prefixe
	$cible="";//valeur par défaut du numéro de la rubrique
	$val_edt='';	
	}
session_name("Cdt_Lcs");
@session_start();
	//si la page est applée par un utilisateur non identifié,
		if (!isset($_SESSION['cequi'])) {exit;}
		// autorisation d'accés ?  si pas prof, non autorisé
		if ($_SESSION['cequi']!="prof")
			{
	        print('<p align="center"> </p> <p align="center"><font face="Arial" size="5" color="#FF6600">
			Désolé, vous ne pouvez pas accéder à cette page</font></p>');
			exit;
    	
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
    for ($loop=0; $loop < count ($groups) ; $loop++) 
    	{
      	if (mb_ereg("^Cours",$groups[$loop]["cn"]))
      		{
      		$classe[$ind]=$groups[$loop]["cn"];
      		$ind++;
    		}
    	}
}

//lecture EDT agenda
if (is_dir("../../Agendas"))
{
$aujourdhui=date('Ymd');
$tab=array();
$loop=0;
//connexion 
require_once ("../Includes/connect_agendas.php");

//entrees edt ?
$rq="SELECT cal_name,cal_description FROM webcal_entry WHERE cal_date >= ".$aujourdhui." AND cal_id IN 
(SELECT cal_id FROM webcal_entry_categories WHERE cat_owner='".$_SESSION['login']."' AND cat_id IN 
(SELECT cat_id FROM webcal_categories WHERE cat_owner='".$_SESSION['login']."' AND cat_name='EDT')) ";

// lancer la requete
$result = @mysql_query ($rq) or die (mysql_error());

//on recupere les donnees
while ($enrg = mysql_fetch_array($result, MYSQL_NUM))
	{
	$mati= explode(":",$enrg[1]);
	$mati[1]=preg_replace ( "/[\r\n]+/", "", $mati[1] );
	$ong=$enrg[0].":".$mati[1];
	if (!in_array($ong,$tab) && $enrg[0]!="" && $mati[1]!="")
		{
		$tab[$loop]=$ong;
		$loop++;
		}
	}
mysql_close();
}


	
// Connexion à la base de données Cdt	
require_once ('../Includes/config.inc.php');
	
//Traitement du formulaire

if (isset($_POST['cours_edt'])) 
	{
	$val_edt=$_POST['cours_edt'];
	$donnee=explode(":",$_POST['cours_edt']);
	//recherche correspondance classe
	if (in_array($donnee[0],$classe))
	$val_classe =$donnee[0];
	$val_matiere =$donnee[1];
	}
	
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
	
	// Créer la requête d'écriture pour l'enregistrement des données
	if (isset($_POST['enregistrer']) && $div!="--")
			{
			$rq = "INSERT INTO onglets (login ,prof ,classe, matiere, prefix,edt ) 
				   VALUES ( '{$_SESSION['login']}', '{$_SESSION['name']}', '$div',
				   '$matiere', '$prefixe','$val_edt' )";
			}
//Créer la requête pour la mise à jour des données	
	if (isset($_POST['modifier'])&& $div!="--")
			{$cible= ($_POST['numrub']);
			$rq = "UPDATE  onglets SET classe='$div', matiere='$matiere', prefix='$prefixe', edt='$val_edt'  
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
if (isset($_GET['delrub'])&& isset($_GET['num']) && $_GET['TA']==$_SESSION['RT']) 
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
	$rq = "SELECT login,classe, matiere, prefix,edt FROM onglets WHERE id_prof='$cible'  ";

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
		$val_edt=$enrg[4];
		}}
	} 
//FIN TRAITEMENT FORMULAIRE
/* recherche des onglets existants*/
// Créer la requête.
$rq = "SELECT classe,matiere,id_prof,prof,prefix,edt FROM onglets
 WHERE login='{$_SESSION['login']}' ORDER BY id_prof ASC ";

// lancer la requête
$result = @mysql_query ($rq) or die (mysql_error());

// Combien y a-t-il d'enregistrements ?	
$nb = mysql_num_rows($result); 	
	
//on récupère les données
$loop=0;
while ($enrg = mysql_fetch_array($result, MYSQL_NUM)) 
	{
	$clas[$loop]=$enrg[0]; //classe
	$mat[$loop]=$enrg[1]; // matiere
	$numero[$loop]=$enrg[2];//numéro de la rubrique
	$prof[$loop]=$enrg[3]; // nom du prof
	$pref[$loop]=$enrg[4]; //préfixe
	$edt[$loop]=$enrg[5];//edt
	$loop++;
	}
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
<?php
		if (count($tab)>0) 
		{
		echo '<li> Selectionnez le cours de votre emploi du temps : ';
		echo "<select name='cours_edt'";
		if (!isset($_GET['modrub'])) echo "onChange='submit()'";
		echo " >\n";
		echo "<option valeur='--'>--</option>\n";
		foreach ($tab as $clé => $valeur)
		    {
		    if (!in_array($valeur,$edt)) 
		    {
			    echo "<option valeur=\"$valeur\"";
				if ($valeur==$val_edt) {echo 'selected';}
				echo ">$valeur</option>\n";
	  			}
	  		}
  		echo "</select>\n</li>";
		}
		if (count($tab)>0)  echo '<li>Modifiez s\'il y a lieu le nom de la classe ou du groupe : ';
		else echo '<li>Choisissez votre classe ou votre groupe :'; 
		//liste de selection de la classe
		echo "<select id='laclasse' name='div'>\n";
		echo "<option valeur='--'>--</option>\n";
		foreach ($classe as $clé => $valeur)
		    {
		    echo "<option valeur=\"$valeur\"";
			if ($valeur==$val_classe) {echo 'selected';}
			echo ">$valeur</option>\n";
  			}
  		echo "</select>\n</li>";
  		if (count($tab)>0)  echo '<li>Modifiez s\'il y a lieu le nom de la matière : ';
		else echo '<li>Indiquez la mati&egrave;re enseign&eacute;e : ';
?>		
		<INPUT TYPE=TEXT id="lamat" NAME="matiere" VALUE="<? echo $val_matiere ?>" SIZE=20 MAXLENGTH=20></li>
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
//création du tableau , onglets prof

echo '<div id="cfg-onglet">';
echo '<H4 class="perso">Aspect des onglets de votre cahier de textes (cliquer sur un onglet pour le <B><FONT SIZE="3" color="#660000">Modifier</FONT></B> )</H4>';
echo ("<ul id='cfg-navlist'>");
for($x=0;$x < $nb;$x++){
	if ($x==0) 
	{
		echo ("<li><a href='config_ctxt.php?modrub=erg45er5ze&num=$numero[$x]'title='Modifier cet onglet'>$mat[$x]<br>$clas[$x]"."</a></li>");
	}
	else 
	{
		echo("<li><a href='config_ctxt.php?modrub=erg45er5ze&num=$numero[$x]' title='Modifier cet onglet' >$mat[$x]<br>$clas[$x]</a></li>");
	}
}
	echo("</ul>");


echo ('<H4 class="perso"> Aspect des onglets du cahier de textes des élèves
	( cliquer sur un onglet pour le <B><FONT SIZE="3" color="#660000">Supprimer</FONT></B> )</H4>');

	
//création du tableau , onglets élèves
echo ("<ul id='cfg-navlist-elv'>");
	for($x=0;$x < $nb;$x++)
		{
		if ($x==0) 
			{
			echo("<li><a href='config_ctxt.php?suppr=yes&numong=$numero[$x]' title='Supprimer cet onglet'>".$mat[$x]." (".$clas[$x].")<br>".$pref[$x]."&nbsp;".$prof[$x]."</a></li>");
			}
			else 
			{
			echo("<li><a href='config_ctxt.php?suppr=yes&numong=$numero[$x]' title='Supprimer cet onglet'>".$mat[$x]." (".$clas[$x].")<br>".$pref[$x]."&nbsp;".$prof[$x]."</a></li>");
			}
		}
	echo("</ul>");	
echo '</div>';
	
//ouverture d'un popup de confirmation de suppression
if (isset($_GET["suppr"]))
	{
	echo "<script type='text/javascript'>";
	echo"      if (confirm('Confirmer la suppression de cette rubrique')){";
	echo ' location.href = "';
	echo $_SERVER['PHP_SELF'];echo'" + "?num=" +'." '";
	echo $_GET["numong"] ;
	echo "'".' + "&delrub=" +'."1245";
	echo ' + "&TA=" +'." '". $_SESSION['RT']."'";
	echo " ;} else  {";
	echo ' location.href = "';
	echo $_SERVER['PHP_SELF'];
	echo '"; } </script>';
	}
echo '</div>'; 	//fin du contenu
echo '</div>'; 	//fin du container
	include ('../Includes/pied.inc'); 
echo '</div>';
?>

</BODY>
</HTML>
