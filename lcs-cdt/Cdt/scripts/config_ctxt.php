<?php
/* =============================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 2.3 du 06/01/2011
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script de personnalisation du cahier de textes -
			_-=-_
   "Valid XHTML 1.0 Strict"
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
	$val_classe='--';//valeur par d�faut de la variable classe
	$val_matiere='';//valeur par d�faut de la variable matiere
	$val_prefix='';//valeur par d�faut de la variable prefixe
	$cible="";//valeur par d�faut du num�ro de la rubrique
	$val_edt='';	
	}
session_name("Cdt_Lcs");
@session_start();
include "../Includes/check.php";
if (!check()) exit;
	//si la page est appl�e par un utilisateur non identifi�,
		if (!isset($_SESSION['cequi'])) {exit;}
		// autorisation d'acc�s ?  si pas prof, non autoris�
		if ($_SESSION['cequi']!="prof")
			{
	        print('<p align="center"> </p> <p align="center"><font face="Arial" size="5" color="#FF6600">
			D�sol�, vous ne pouvez pas acc�der � cette page</font></p>');
			exit;
    	
}
		
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html  xmlns="http://www.w3.org/1999/xhtml" >
<head>
<title>Personnalisation du cahier de textes num&eacute;rique</title>
<meta name="author" content="Philippe LECLERC -TICE CAEN" />
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<link href="../style/style.css" rel="stylesheet" type="text/css" />
	<link  href="../style/navlist-prof.css" rel="stylesheet" type="text/css" />
	<link  href="../style/navlist-eleve.css" rel="stylesheet" type="text/css" />
<!--[if IE]>
<link href="../style/style-ie.css"  rel="stylesheet" type="text/css"/>
<![endif]-->
</head>
<body>
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
$result = @mysql_query ($rq);
if ($result) 
{
//on recupere les donnees
while ($enrg = mysql_fetch_array($result, MYSQL_NUM))
	{
	$mati= explode(":",$enrg[1]);
	$mati[1]=preg_replace ( "/[\r\n]+/", "", $mati[1] );
	$ong=$enrg[0].":".$mati[1];
	$ong=mb_ereg_replace("[[:blank:]]$","",$ong);
	if (!in_array($ong,$tab) && $enrg[0]!="" && $mati[1]!="")
		{
		$tab[$loop]=$ong;
		$loop++;
		}
	}
}
mysql_close();
}
	
// Connexion � la base de donn�es Cdt	
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
	
// �1.ENREGISTRER OU MODIFIER UNE RUBRIQUE
if (isset($_POST['enregistrer']) || isset($_POST['modifier']))
	{ 
	$rq="";
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
	
	// Cr�er la requ�te d'�criture pour l'enregistrement des donn�es
	if (isset($_POST['enregistrer']) && $div!="--")
			{
			$rq = "INSERT INTO onglets (login ,prof ,classe, matiere, prefix,edt ) 
				   VALUES ( '{$_SESSION['login']}', '{$_SESSION['name']}', '$div',
				   '$matiere', '$prefixe','$val_edt' )";
			}
//Cr�er la requ�te pour la mise � jour des donn�es	
	if (isset($_POST['modifier'])&& $div!="--")
			{
			$cible= ($_POST['numrub']);
			$rq = "UPDATE  onglets SET classe='$div', matiere='$matiere', prefix='$prefixe', edt='$val_edt'  
				WHERE id_prof='$cible'";
			}
		
// lancer la requ�te
	if ($rq!="") 
		{
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

	}

//fin enregistrement ou modification d'une rubrique


//�2. SUPPRESSION D'UNE RUBRIQUE

//si la suppression d'une rubrique est confirm�e
if (isset($_GET['delrub'])&& isset($_GET['num']) && $_GET['TA']==md5($_SESSION['RT'].htmlentities($_SERVER['PHP_SELF']))) 
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
	$rq = "SELECT login,classe, matiere, prefix,edt FROM onglets WHERE id_prof='$cible'  ";

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
		$val_edt=$enrg[4];
		}}
	} 
//FIN TRAITEMENT FORMULAIRE
/* recherche des onglets existants*/
// Cr�er la requ�te.
$rq = "SELECT classe,matiere,id_prof,prof,prefix,edt FROM onglets
 WHERE login='{$_SESSION['login']}' ORDER BY id_prof ASC ";

// lancer la requ�te
$result = @mysql_query ($rq) or die (mysql_error());

// Combien y a-t-il d'enregistrements ?	
$nb = mysql_num_rows($result); 	
	
//on r�cup�re les donn�es
$loop=0;
while ($enrg = mysql_fetch_array($result, MYSQL_NUM)) 
	{
	$clas[$loop]=$enrg[0]; //classe
	$mat[$loop]=$enrg[1]; // matiere
	$numero[$loop]=$enrg[2];//num�ro de la rubrique
	$prof[$loop]=$enrg[3]; // nom du prof
	$pref[$loop]=$enrg[4]; //pr�fixe
	$edt[$loop]=$enrg[5];//edt
	$loop++;
	}
/*================================
   -      Affichage du formulaire  -
   ================================*/ 
?>
<div id="cfg-container">
<div id="cfg-contenu">
<h1 class='title'>Personnalisation du cahier de textes de <span class="evidence"> <?echo $_SESSION['nomcomplet'];?></span></h1>
<form id="cfg-form" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>?bn16drgv2r=s61lyc6uby54trh" method="post">
<p><input name="TA" type="hidden"  value="<?php echo md5($_SESSION['RT'].htmlentities($_SERVER['PHP_SELF'])); ?>" /></p>
<ol>
<li>Cr&eacute;ation des rubriques (onglets) 
<ul>
<?php
		if (count($tab)>0) 
		{
		echo '<li> Selectionnez le cours de votre emploi du temps : ';
		echo '<select name="cours_edt"';
		if (!isset($_GET['modrub'])) echo ' onchange="submit()"';
		echo ' >';
		echo '<option value="--">--</option>';
		foreach ($tab as $cle => $valeur)
		    {
		    if (!in_array($valeur,$edt)) 
		    {
			    echo '<option value="'.htmlentities($valeur).'"';
				if (htmlentities($valeur)==htmlentities($val_edt) ){echo ' selected="selected"';}
				echo '>'.htmlentities($valeur).'</option>';
	  			}
	  		}
  		echo '</select></li>';
		}
		if (count($tab)>0)  echo '<li>Modifiez s\'il y a lieu le nom de la classe ou du groupe : ';
		else echo '<li>Choisissez votre classe ou votre groupe : ';
		//liste de selection de la classe
		echo '<select id="laclasse" name="div">';
		echo '<option value="--">--</option>';
		foreach ($classe as $cle => $valeur)
		    {
		    echo '<option value="'.$valeur.'"';
			if ($valeur==$val_classe) {echo ' selected="selected"';}
			echo '>'.$valeur.'</option>';
  			}
  		echo '</select></li>';
  		if (count($tab)>0)  echo '<li>Modifiez s\'il y a lieu le nom de la mati&egrave;re : ';
		else echo '<li>Indiquez la mati&egrave;re enseign&eacute;e : ';
		echo '<input type="text" id="lamat" name="matiere" value="'.$val_matiere.'" size="20" maxlength="20" /></li>';
?>
</ul>
</li>
	<li>Sur	les onglets du <b>cahier de textes de vos &eacute;l&egrave;ves</b> apparaitront la mati&egrave;re enseign&eacute;e et votre nom
		pr&eacute;c&eacute;d&eacute; d&rsquo;un &quot;&nbsp;pr&eacute;fixe&nbsp;&quot;
		<ul>
			<li>Indiquez le <b>pr&eacute;fixe </b> que vous souhaitez voir appara&icirc;tre (M, Mme, pr&eacute;nom abr&eacute;g&eacute;, ....) : 
                        <input type="text" name="prefixe" value="<?php echo $val_prefix ?>" size="10" /></li>
		</ul></li>
</ol>
		
<div id="cfg-btn">	
		<input type="hidden" name="numrub" value= "<?php echo $cible ; ?>" />
		<?php if (isset($_GET['modrub'])&& isset($_GET['num'])) 
		{echo ('<input class="modif" type="submit" name="modifier" value="" />');}
		else {echo ('<input class="enreg" type="submit" name="enregistrer" value="" />');}
		if ($nb>0) {echo('<input class="retour" type="submit" name="retour" value="" />');}
		?>
<input type="button" class="annul" value="" onclick="history.back()"/>
</div>	
</form>
<?php 

//AFFICHAGE DES ONGLETS EXISTANTS
//cr�ation du tableau , onglets prof
if ($nb>0) {
echo '<div id="cfg-onglet">';
echo '<h4 class="perso">Aspect des onglets de votre cahier de textes (cliquer sur un onglet pour le <span class="evidence"><b>Modifier</b></span> )</h4>';

    echo '<ul id="cfg-navlist">';

for($x=0;$x < $nb;$x++){
	if ($x==0) 
	{
		echo '<li><a href="config_ctxt.php?modrub=erg45er5ze&amp;num='.$numero[$x].'" title="Modifier cet onglet" >'.$mat[$x].'<br />'.$clas[$x].'</a></li>';
	}
	else 
	{
		echo '<li><a href="config_ctxt.php?modrub=erg45er5ze&amp;num='.$numero[$x].'" title="Modifier cet onglet" >'.$mat[$x].'<br />'.$clas[$x].'</a></li>';
	}
}
	echo '</ul>';


echo '<h4 class="perso"> Aspect des onglets du cahier de textes des &#233;l&#232;ves ( cliquer sur un onglet pour le <span class="evidence"><b>Supprimer</b></span>)</h4>';
	
//cr�ation du tableau , onglets �l�ves
echo '<ul id="cfg-navlist-elv">';
	for($x=0;$x < $nb;$x++)
		{
		if ($x==0) 
			{
			echo '<li><a href="config_ctxt.php?suppr=yes&amp;numong='.$numero[$x].'" title="Supprimer cet onglet">'.$mat[$x].' ( '.$clas[$x].' ) <br />'.$pref[$x].'&nbsp;'.$prof[$x].'</a></li>';
			}
			else 
			{
			echo '<li><a href="config_ctxt.php?suppr=yes&amp;numong='.$numero[$x].'" title="Supprimer cet onglet">'.$mat[$x].' ( '.$clas[$x].' )<br />'.$pref[$x].'&nbsp;'.$prof[$x].'</a></li>';
			}
		}
	echo '</ul>';
echo '</div>';
}

//ouverture d'un popup de confirmation de suppression
if (isset($_GET["suppr"]))
	{
	echo "<script type='text/javascript'>";
	echo"      if (confirm('Confirmer la suppression de cette rubrique')){";
	echo ' location.href = "';
	echo htmlentities($_SERVER['PHP_SELF']);echo'" + "?num=" +'." '";
	echo $_GET["numong"] ;
	echo "'".' + "&delrub=" +'."1245";
	echo ' + "&TA=" +'." '". md5($_SESSION["RT"].htmlentities($_SERVER["PHP_SELF"]))."'";
	echo " ;} else  {";
	echo ' location.href = "';
	echo htmlentities($_SERVER['PHP_SELF']);
	echo '"; } </script>';
	}
?>
</div><!-- fin du contenu -->
</div><!-- fin du container -->
<?php
include ('../Includes/pied.inc'); 
?>
</body>
</html>
