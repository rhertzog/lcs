<?php

/* =============================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 2.0 du 25/10/2009
      par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script du cahier de textes PROF -
			_-=-_
   ============================================= */
session_name("Cdt_Lcs");
@session_start();

//si la page est appelee par un utilisateur non identifiee
if (!isset($_SESSION['login']) )exit;

//si la page est appelee par un utilisateur non prof
elseif ($_SESSION['cequi']!="prof") exit;

//si clic sur Personnaliser le cahier de textes
if (isset($_POST['personnaliser'])) {header("location: config_ctxt.php");exit;}

//si clic sur planifier un devoir
if (isset($_POST['planning']))
	{
	$cible=$_POST['rubrique'];
	header("location: planning.php?rubrique=$cible");
	exit;
	}

//redirection vers la config du cahier &agrave; la premi&egrave;ere utilisation
			
// Connexion a la base de donnees
require_once ('../Includes/config.inc.php');

// Creer la requ&egrave;ete.
$rq = "SELECT classe,matiere,id_prof FROM onglets
 WHERE login='{$_SESSION['login']}' ORDER BY classe ASC ";
 
// lancer la requ&egrave;ete
$result = @mysql_query ($rq) or die (mysql_error()); 

// si pas de rubrique, on redirige vers config_ctxt.php
if (mysql_num_rows($result)==0) 
	{
	?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title>Cahier de textes num&eacute;erique</title>
<meta http-equiv="content-type" content="text/html;charset=iso-8859-1" >
	<link href="../style/style.css" rel=StyleSheet type="text/css">
</head>

<body>
	<div id="first">
	<div class="prg">
		<h2>Votre cahier de textes ne comporte actuellement aucune rubrique.</h2>
		<h2>Vous devez personnaliser votre cahier de texte en cr&#233;ant au moins une rubrique.</h2>
<?php
	echo ("<a class='bt-perso' href='config_ctxt.php'></a>");exit;
?>
	</div></div>
<?php
	mysql_close();	
	exit;
	}
include_once("/usr/share/lcs/Plugins/Cdt/Includes/fonctions.inc.php");	
require_once("/usr/share/lcs/Plugins/Cdt/Includes/class.inputfilter_clean.php");
include_once ('/usr/share/lcs/Plugins/Cdt/Includes/markdown.php'); //convertisseur txt-->HTML
?>

<!-- Fin de la page de première utilisation -->
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title>Cahier de textes num&eacute;erique</title>
<meta http-equiv="content-type" content="text/html;charset=iso-8859-1" >
	<link href="../style/style.css" rel=StyleSheet type="text/css">
	<link  href="../style/deroulant.css" rel=StyleSheet type="text/css">
	<link  href="../style/navlist-prof.css" rel=StyleSheet type="text/css">
	<link  href="../style/ui.all.css" rel=StyleSheet type="text/css">
	<link  href="../style/ui.datepicker.css" rel=StyleSheet type="text/css">
	<link  href="../style/ui.theme.css" rel=StyleSheet type="text/css">
<!--[if IE]>
<link href="../style/style-ie.css"  rel="stylesheet" type="text/css"/>
<![endif]-->
	<script language="javascript" type="text/javascript" src="../tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
	<script language="javascript" type="text/javascript" src="../Includes/conf-tiny_mce.js"></script>
	<script language="Javascript" type="text/javascript" src="../Includes/barre_java.js"></script>
	<script language="Javascript" type="text/javascript" src="../Includes/alertsession.js"></script>
	<script language="Javascript" type="text/javascript" src="../Includes/JQ/jquery-1.3.2.min.js"></script>
	<script language="Javascript" type="text/javascript" src="../Includes/JQ/ui.core.js"></script>  
	<script language="Javascript" type="text/javascript" src="../Includes/JQ/ui.datepicker.js"></script> 
	<script language="Javascript" type="text/javascript" src="../Includes/JQ/cdt-script.js"></script>
	<script type="text/javascript" language="JavaScript">
	var duree=<?echo ini_get( 'session.gc_maxlifetime' );?>;
	setTimeout("avertissement()",1);
 	setTimeout("finsession()",(duree + 2) * 1000);
	</script>
</head>

<body onload="tinyMCE.execCommand('mceFocus',false,'coursfield')">
<!--
<div style="position:absolute; top:0px; left:100px; z-index:3;">
<table class="ds_box" cellpadding="0" cellspacing="0" id="ds_conclass" style="display: none;">
<tr><td id="ds_calclass">
</td></tr>
</table>
</div>
-->
<?
$tsmp=time();
$tsmp2=time() + 604800;
$cours="";//variable temporaire de $Cours (avec une majuscule) pour un UPDATE
$afaire="";//variable temporaire de $Afaire (avec une majuscule) pour un UPDATE
$article="";


//on recupere le parametre de la rubrique
if (isset($_GET['rubrique']))
	{
	$cible=$_GET['rubrique'];
	} 
elseif (isset($_POST['rubriq']))
	{
	$cible=$_POST['rubriq'];
	}
else $cible="";

// Connexiona la base de donnees
require_once ('../Includes/config.inc.php');

/*================================
   -      Traitement du formulaire  -
   ================================*/
   
  //Suppression d'un commentaire apr&egrave;es confirmation
if (isset($_GET['com'])&& isset($_GET['rubrique'])) 
	{
	//l'article existe il ?
	$rq = "SELECT login  FROM cahiertxt	WHERE id_rubrique='{$_GET['com']}' AND  (login='{$_SESSION['login']}')";
	//$rq = "SELECT login FROM cahiertxt WHERE id_rubrique = 'toto' AND  (login='{$_SESSION['login']}')";
 // lancer la requ&egrave;ete
	$result = @mysql_query ($rq) or die (mysql_error());
	$nb = mysql_num_rows($result);
	if ($nb==1) 
		{
		$rq = "DELETE  FROM cahiertxt WHERE id_rubrique='{$_GET['com']}' AND  (login='{$_SESSION['login']}') LIMIT 1";
		$result2 = @mysql_query ($rq) or die (mysql_error());
		
		}
	}
//fin de suppression d'un commentaire

if (isset($_POST['enregistrer']) || isset($_POST['modifier']))
{ 
	// Traiter les données

	// Vérifier $Cours  et la débarrasser de tout antislash et tags possibles
	if (strlen($_POST['Cours']) > 0)
		{ 
		//$Cours  = addSlashes(strip_tags(stripslashes($_POST['Cours'])));
		$Cours  =htmlentities($_POST['Cours']);
		$oMyFilter = new InputFilter($aAllowedTags, $aAllowedAttr, 0, 0, 1);
		$Cours = $oMyFilter->process($Cours);
	

		
		}
	else
		{ // Si aucun commentaire n'a été saisi
		$Cours = "";
		}

	// Vérifier $Afaire et la débarrasser de tout antislash et tags possibles
	if (strlen($_POST['Afaire']) > 0)
		{ 
		//$Afaire= addSlashes(strip_tags(stripslashes($_POST['Afaire'])));
		$Afaire  = $_POST['Afaire'];
		$oMyFilter = new InputFilter($aAllowedTags, $aAllowedAttr, 0, 0, 1);
		$Afaire = $oMyFilter->process($Afaire);
		}
	else
		{ // Si aucun commentaire n'a été saisi
		 $Afaire= "";
		}

	//Traitement de dates
	$Morceauc=split('/',$_POST['datejavac']);
	$jour_c=$Morceauc[0];
	$mois_c=$Morceauc[1];
	$an_c=$Morceauc[2];
	$date_c = $an_c.$mois_c.$jour_c;
	$Morceauf=split('/',$_POST['datejaf']);
	$jour_f=$Morceauf[0];
	$mois_f=$Morceauf[1];
	$an_f=$Morceauf[2];
	$date_af = $an_f.$mois_f.$jour_f;

	if 	($_POST['datejav']!="idem Cours")
	{
	$Morceauv=split('/',$_POST['datejav']);
	$jour_v=$Morceauv[0];
	$mois_v=$Morceauv[1];
	$an_v=$Morceauv[2];
	$date_v = $an_v.$mois_v.$jour_v;
	}
	else 
	$date_v=$date_c;

	// Créer la requęte d'écriture pour l'enregistrement des données
	if (isset($_POST['enregistrer']) && ((strlen($Cours) > 1) || (strlen($Afaire) > 1)))
			{
	
			$rq = "INSERT INTO cahiertxt (id_auteur,login,date,contenu,afaire,datafaire,datevisibi ) 
	        VALUES ( '$cible','{$_SESSION['login']}', '$date_c', '$Cours', '$Afaire', '$date_af', '$date_v')";
			}
			
	//Créer la requęte pour la mise ŕ jour des données	
	if (isset($_POST['modifier']))
			{
			$artic= ($_POST['numart']);
			$rq = "UPDATE  cahiertxt SET date='$date_c', contenu='$Cours', afaire='$Afaire',datafaire='$date_af',datevisibi='$date_v'  
			WHERE id_rubrique='$artic'";
			}		
	// lancer la requęte
	    $result = mysql_query($rq); 
	    if (!$result)  // Si l'enregistrement est incorrect
		    {                           
		     echo "<p>Votre commentaire n'a pas pu ętre enregistré a cause d'une erreur systčme".
			 "<p></p>" . mysql_error() . "<p></p>";
			 mysql_close();     // refermer la connexion avec la base de données
			 exit();
			}
	 
	//diffusion des commentaires vers les classes de męme niveau
	if ((isset($_SESSION['grp']) ) && (isset($_SESSION['dif_c']) ) && (isset($_SESSION['dif_af']) ) && (isset($_SESSION['dif_vi']) ) 
	 && (isset($_POST['enregistrer']) && ((strlen($Cours) > 1) || (strlen($Afaire) > 1))))
		{
		for ($loop=0; $loop < count ($_SESSION['grp'])  ; $loop++)
					{
					$cibledif=$_SESSION['grp'][$loop];
					//$date_cdif=$_SESSION['dif_c'][$loop];
					//$date_afdif=$_SESSION['dif_af'][$loop];
					//Traitement de dates
					$Morceauc_dif=split('/',$_SESSION['dif_c'][$loop]);
					$jour_c_dif=$Morceauc_dif[0];
					$mois_c_dif=$Morceauc_dif[1];
					$an_c_dif=$Morceauc_dif[2];
					$date_c_dif = $an_c_dif.$mois_c_dif.$jour_c_dif;
					$Morceauf_dif=split('/',$_SESSION['dif_af'][$loop]);
					$jour_f_dif=$Morceauf_dif[0];
					$mois_f_dif=$Morceauf_dif[1];
					$an_f_dif=$Morceauf_dif[2];
					$date_af_dif = $an_f_dif.$mois_f_dif.$jour_f_dif; 
					if 	($_SESSION['dif_vi'][$loop]!="idem Cours")
					{
					$Morceauv_dif=split('/',$_SESSION['dif_vi'][$loop]);
					$jour_v_dif=$Morceauv_dif[0];
					$mois_v_dif=$Morceauv_dif[1];
					$an_v_dif=$Morceauv_dif[2];
					$date_v_dif = $an_v_dif.$mois_v_dif.$jour_v_dif;
					}
					else 
					$date_v_dif=$date_c_dif;
					
					$rq = "INSERT INTO cahiertxt (id_auteur,login,date,contenu,afaire,datafaire,datevisibi ) 
					VALUES ( '$cibledif','{$_SESSION['login']}', '$date_c_dif', '$Cours', '$Afaire', '$date_af_dif','$date_v_dif' )";
					// lancer la requęte
					$result = mysql_query($rq); 
					if (!$result)  // Si l'enregistrement est incorrect
						{                           
						echo "<p>Votre commentaire n'a pas pu ętre enregistré ŕ cause d'une erreur systčme".
						"<p></p>" . mysql_error() . "<p></p>";
						mysql_close();     // refermer la connexion avec la base de données
						exit();
						}
					}
		//desruction des variables de session			
		unset ($_SESSION['grp']);
		unset ($_SESSION['dif_c']);
		unset ($_SESSION['dif_af']);
		}
	}

//fin traitement formulaire

/*
   ================================
   - Traitement de la barre des menus  -
   ================================
*/

// Creer la requete (Recuperer les rubriques de l'utilisateur) 
$rq = "SELECT classe,matiere,id_prof,postit FROM onglets
 WHERE login='{$_SESSION['login']}' ORDER BY id_prof ASC ";

 // lancer la requ&egrave;ete
$result = @mysql_query ($rq) or die (mysql_error());
$nb = mysql_num_rows($result);  // Combien y a-t-il d'enregistrements ?

//on recupere les donnees
$loop=0;
while ($enrg = mysql_fetch_array($result, MYSQL_NUM)) 
	{$clas[$loop]=$enrg[0];
	$mat[$loop]=$enrg[1];
	$numero[$loop]=$enrg[2];
	$com[$loop]=$enrg[3];
	$loop++;
	}
	
//on calcule la largeur des onglets
if($cible==""){$cible=($numero[0]);}
$nmax=$nb;
 
//cr&eacute;ation de la barre de menu 
echo '<div id="entete">';

echo '<div id="navcontainer">';
echo ("<ul id='navlist'>");
for($x=0;$x < $nmax;$x++)
	{
		if ($cible == ($numero[$x]))
			{//cellule active	
			echo ("<li id='select'><a href='cahier_texte_prof.php?rubrique=$numero[$x]'
			'onmouseover=\"window.status='';return true\" id='courant'>$mat[$x]<br>$clas[$x] "."</a></li>");	
			$contenu_postit=$com[$x];
			}
		else 
			{
			if (!isset($mat[$x])) $mat[$x]="&nbsp;";
			if (!isset($clas[$x])) $clas[$x]="&nbsp;";
			if (!isset($numero[$x]))
			echo ("<li><a href='#'>$clas[$x]"."</a></li>");
			else
			{
			echo ("<li><a href='cahier_texte_prof.php?rubrique=$numero[$x]'
			'onmouseover=\"window.status='';return true\">$mat[$x]<br>$clas[$x]"."</a></li>");
			}
			}
	}
echo '</ul>';
echo '<div id="switch-barreLcs" class="swup"></div>';
echo '</div>';
echo '</div>';


//Affichage d'une boite de confirmation d'effacement
if (isset($_POST["suppr"]))
	{
	echo "<script type='text/javascript'>";
	echo "if (confirm('Confirmer la suppression de ce commentaire')){";
	echo ' location.href = "';echo $_SERVER['PHP_SELF'];echo'" + "?com=" +'." '"; echo $_POST["number"] ;echo "'"
	.' + "&rubrique=" +'." '";echo $_POST['rubriq']."'".' ;}</script>';
	}
	

// 3. Modification d'un article
if (isset($_POST['modif'])&& isset($_POST['number'])) 
	{
	$action='erg45er5ze';
	$article=$_POST['number'];
	
//l'article existe il ?
	$rq = "SELECT DATE_FORMAT(date,'%d'),DATE_FORMAT(date,'%m'),DATE_FORMAT(date,'%Y'),contenu,afaire,
	DATE_FORMAT(datafaire,'%d'),DATE_FORMAT(datafaire,'%m'),DATE_FORMAT(datafaire,'%Y'),
	DATE_FORMAT(datevisibi,'%d'),DATE_FORMAT(datevisibi,'%m'),DATE_FORMAT(datevisibi,'%Y') FROM cahiertxt
	WHERE id_rubrique=$article ";
 // lancer la requęte
	$result = @mysql_query ($rq) or die (mysql_error());
	$nb = mysql_num_rows($result);
	//s'il existe, on récupčre les datas pour les afficher dans les champs
	if (($nb==1) &&($action=='erg45er5ze'))
		{while ($enrg = mysql_fetch_array($result, MYSQL_NUM)) 
		{
		$val_jour=$enrg[0];
		$val_mois=$enrg[1];
		$val_annee=$enrg[2];
		$cours=$enrg[3];
		$afaire=$enrg[4];
		$val_jouraf=$enrg[5];
		$val_moisaf=$enrg[6];
		$val_anneeaf=$enrg[7];
		$val_moisv=$enrg[9];
		$val_jourv=$enrg[8];
		$val_anneev=$enrg[10];
		//calcul du timestamp correspondant a la date de l'article
		$tsmp=mkTime(0,0,1,$val_mois,$val_jour,$val_annee);
		//calcul du timestamp correspondant a la date "a faire" de l'article
		$tsmp2=mkTime(0,0,1,$val_moisaf,$val_jouraf,$val_anneeaf);// - 604800 604800 pour compenser offset 7 jours
		//calcul du timestamp correspondant a la date de vsisbilité
		$tsmp3=mkTime(0,0,1,$val_moisv,$val_jourv,$val_anneev);
		
		}
		}
	} 	
//recherche de la mati&egrave;ere et la classe de la rubrique active du cahier de textes
if (isset($cible))
	{ 
	$rq = "SELECT classe,matiere FROM onglets
	WHERE id_prof='$cible'  ";
	// lancer la requ&egrave;ete
	$result = @mysql_query ($rq) or die (mysql_error());
	// Combien y a-t-il d'enregistrements ?
	$nb = mysql_num_rows($result); 
	//on recupere les donnees
	while ($enrg = mysql_fetch_array($result, MYSQL_NUM)) 
		{$classe_active=$enrg[0];//classe
		$mati_active=$enrg[1];//mati&egrave;ere
		}
	
	}	
/*================================
   -      Affichage du formulaire  -
   ================================*/

$datjc=getdate($tsmp);
$jo=date('d',$datjc['0']);
$mo=date('m',$datjc['0']);
$ann=date('Y',$datjc['0']);
$dtajac=$jo."/".$mo."/".$ann;
$datjf=getdate($tsmp2);
$jof=date('d',$datjf['0']);
$mof=date('m',$datjf['0']);
$annf=date('Y',$datjf['0']);
$dtajaf=$jof."/".$mof."/".$annf;
if (isset($tsmp3))
	{
	$datjv=getdate($tsmp3);
	$jov=date('d',$datjv['0']);
	$mov=date('m',$datjv['0']);	
	$annv=date('Y',$datjv['0']);
	$dtajav=$jov."/".$mov."/".$annv;
	}
	else
	$dtajav="idem Cours";
   
?>
<!--bloc qui contient la saisie et le contenu du cahier de texte-->
<div id="container">

<!--bloc en haut de page qui contient la saisie et le menu deroulant à droite-->
<div id="secondaire">

<!--bloc de gauche pour la saisie-->
<div id="saisie">
	<form id="form-saisie" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>?rubrique=<?echo $cible;?>" method="post" name="cahtxt" >
	
	<div id="boite3">
		<div id="boite1">  
			<div id="crsdu">Cours  du :
 				<input id="datejavac" size="10" name="datejavac" value="<?echo $dtajac?>" readonly="readonly" style="cursor: text">
 				Visible le :
 				<input id="datejavav" size="10" name="datejav" value="<?echo $dtajav?>" readonly="readonly" style="cursor: text" title="Par défaut, le commentaire sera visible a partir de la date du cours">
 			</div>
			<TEXTAREA id="coursfield"  NAME="Cours"  class="mceAdvanced" COLS=1 ROWS=1 WRAP=virtual style="background-color:#F0F0FA"><?echo $cours;?> </TEXTAREA>
			<input type="hidden" name="numart" value= "<? echo $article ; ?>"<br />
		</div><!--fin du div boite 1-->
  
		<div id="boite2">
			<div id="afRle">A faire pour le :
				<input id="datejaf" size="10" name="datejaf" value="<?echo $dtajaf?>" readonly="readonly" style="cursor: text" >
			</div>
			<TEXTAREA id="afairefield"  NAME="Afaire" class="mceAdvanced" COLS=1 ROWS=1 WRAP=virtual style="background-color:#F0F0FA"><?echo $afaire;?></TEXTAREA><br />
		</div><!--fin du div boite 2-->
		
	</div><!--fin de la boite 3-->
	
	<div id="boite4">
		<?  if (isset($_POST['modif'])&& isset($_POST['number'])) {
			echo ('<INPUT name="rubrique" type="hidden" id="rubrique" value="'.$cible.'">
				<div id="bouton">
				<input type="submit" title="Enregistrer les modifications" name="modifier" value="" class="submit-modif">
				<INPUT name="date" type="hidden" id="date" value="'.$date.'"/>
				</div>');
			}
		
			else {
			echo ('<INPUT name="rubrique" type="hidden" id="rubrique" value="'.$cible.'"/>
				<INPUT name="date" type="hidden" id="date" value="'.$date.'">
				<div id="boutons">
				<ul>
				<li><input class="submit-perso" type="submit" name="personnaliser" value="" title="Personnaliser son cahier de texte"></li>
				<li><input type="button" title="Annuler" value="" onClick="history.back()" class="submit-del"></li>
				<li><input type="submit" name="planning" value="" title="Planifier un devoir en '.$classe_active.'" class="submit-plan"></li>
				<li><a class="a-imprime"title="Imprimer" href="imprim.php?rubrique='.$cible.'  " target="_blank"></a></li>
				<li><a href="#" title="Enregistrements multiples" onClick="diffuse_popup('.$cible.'); return false" class="a-saveplus"></a></li>
				<li><br/><br/></li>
				<li><input type="submit" title="Enregistrer" name="enregistrer" value="" class="submit-save"></li>
				</ul>
				</div>');
			}
		?>
	</div><!--fin du div boite 4-->

</form>

</div><!--fin du div saisie-->

<!--bloc deroulant a droite de la saisie-->
<div id="deroul-bloc">
<div id="deroulants"> 
<div id="deroul-menu"> 
    <ul> 
        <li><a href="#deroulant_1"><img src="../images/memory-cdt2.png" alt="Post-it" title="Post-it"></a></li>
        <li><a href="#deroulant_2"><img src="../images/archiver-cdt2.png" alt="Archives" title="Archives"></a></li>
        <li><a href="#deroulant_3"><img src="../images/internet-cdt2.png" alt="Liens" title="Liens"></a></li>
        <li><a href="#deroulant_4"><img src="../images/mail-cdt2.png" alt="Mail" title="Mail"></a></li>
        <li><a href="#deroulant_5"><img src="../images/aide-cdt2.png" alt="Aide" title="Aide"></a></li>
    </ul>
</div><!--fin du div deroul-menu-->
<div id="deroul-contenu">
    <div class="deroulant" id="deroulant_1">
    	<div class="t3">Aide m&eacute;moire</div>
        <form id="aide-memoire-contenu" name="aidememory">
        	<textarea id="aide-memoire" name="monpostit"  class="MYmceAdvanced" style="width:100%" rows="15" cols="22">
        	<?if ($contenu_postit !="") echo $contenu_postit;
			else echo "Penser &agrave; ...";?></textarea><br />
			<input name="button"  type="button" onClick="go(<? echo $cible; ?>);" value="Enregistrer le post-it" title="Enregistrer le post-it" />			
		</form>
	</div><!--fin du div deroulant_1-->

    <div class="deroulant" id="deroulant_2">
    	<div class="t3">Archives</div>
      		<?	/* Affichage des archives */ 
				echo (' <dd><B><FONT COLOR="#0000FF" style="align:center">Archives disponibles :<br></font></B> ');
 				//recherche du  nom des archives
				$TablesExist= mysql_list_tables(DB_NAME);
				$x=0;
				while ($table=mysql_fetch_row($TablesExist))
				if (ereg("^onglets[[:alnum:]]",$table[0])) {
					$archive=split('s',$table[0]);
					$x++;
					$archnum=$archive[1];
					echo '<a href="#" onClick="arch_popup(\''.$archive[1].'\'); return false" >- '.$archive[1].' </a> <br>  ';
	
				}
				//s'il n'esiste pas d'archive
				if ($x==0) { echo '<B><FONT COLOR="#CC0000" >Aucune </FONT></B>';}
			?>
	</div><!--fin du div deroulant_2-->
	
    <div class="deroulant" id="deroulant_3">
    	<div class="t3">Liens</div>
       		<p>Ces liens s'ouvrent dans une nouvelle fen&ecirc;tre</p>
			<p><?php echo '<a href="cahier_text_eleve.php';
			if (!ereg("^Cours",$classe_active)) echo '?mlec547trg2s5hy='.$classe_active;
			echo '" target="_blank" width="900" height="800">'; ?> - CAHIER DE TEXTE ELEVES	</a></p>
			<p><?echo '<a href="absences.php';
			echo '?mlec547trg2s5hy='.$classe_active;
			echo '" target="_blank"  ';?> > - CARNET D'ABSENCES - </a></p>
        </div><!--fin du div deroulant_3-->
     
     <div class="deroulant" id="deroulant_4">
     	<div class="t3">Contacts</div>
     		<p>Pour toutes remarques ou suggestions:</p>
     			<p>. <a href="mailto:philippe.leclerc1@ac-caen.fr"><b> Philippe LECLERC </B><I>Misterphi</I></a></p>
     			<p>. <a href="mailto:yannick.chistel@crdp.ac-caen.fr"><b> Yannick CHISTEL </B></a></p>
     			<p>. <a href="mailto:lcs-devel@listes.tice.ac-caen.fr"><b> Equipe-TICE-CRDP-CAEN</b></a></p>
     			
     	</div><!--fin du div deroulant_4-->
     
      <div class="deroulant" id="deroulant_5">
     	<div class="t3">Aide</div>
     		<p><?echo '<a href="http://linux.crdp.ac-caen.fr/pluginsLcs/doc_help/raccourcis.php" target="_blank" ';?> >Aide pour bien utiliser le cahier de textes numerique</a></p>
     </div><!--fin du div deroulant_5-->

</div><!--fin du div deroul-contenu-->
</div><!--fin du div deroulants-->
</div><!--fin du div deroul-bloc--> 

</div>	<!--fin du div secondaire-->

<?

/*========================================================================
   - Affichage du contenu du cahier de textes  -
   =======================================================================*/
//include ("../Includes/fonctions.inc.php");  
include_once ('../Includes/markdown.php'); //convertisseur txt-->HTML

//cr&eacute;er la requ&egrave;te
if ($_SESSION['version']=">=432") setlocale(LC_TIME,"french");
	else setlocale("LC_TIME","french");
$rq = "SELECT DATE_FORMAT(date,'%d/%m/%Y'),contenu,afaire,DATE_FORMAT(datafaire,'%d/%m/%Y'),id_rubrique,date,on_off,DATE_FORMAT(datevisibi,'%d/%m/%Y') FROM cahiertxt
 WHERE (id_auteur=$cible) AND (login='{$_SESSION['login']}') ORDER BY date desc ,id_rubrique desc";
 
// lancer la requete
$result = @mysql_query ($rq) or die (mysql_error());

// Combien y a-t-il d'enregistrements ?
$nb2 = mysql_num_rows($result); 

echo '<div id="boite5">';
echo '<TABLE id="tb-cdt" CELLPADDING=1 CELLSPACING=2>';
while ($ligne = mysql_fetch_array($result, MYSQL_NUM)) 
	  { 
	  $textcours=stripslashes(markdown($ligne[1]));
	  //$textcours=$ligne[1];
	  $textafaire=stripslashes(markdown($ligne[2]));
	  //$day="1,0,0,12,1,2007";echo $day;
	  $jour=LeJour(strToTime($ligne[5]));
	  //debut
	  if ($ligne[1]!="") {
	  echo '<tbody><tr><th colspan=2></th></tr></tbody>';
	  echo '<tbody>';
	  echo '<tr>';
	  //affichage de la seance
	  echo '<td class="seance">S&eacute;ance du <br/>'.$jour.'&nbsp;'.$ligne[0].'<br />Visible le '.$ligne[7].' </td>';
	  if($ligne[1]!="" && $ligne[6]=="1") echo '<td class="contenu2">';else echo '<td class="contenu">';
	  echo $textcours.'</td></tr>';
	  //affichage, s'il existe, du travail a effectuer
	  if ($ligne[2]!="") {
	  echo '<tr><td class="afaire">A faire pour le :<br/>'.$ligne[3].'</td><td class="contenu">';
	  echo $textafaire.'</td></tr>';
	  }
	  //fin

	  echo '</tbody>';
	   echo '<tbody><tr><th class="bas" colspan=2>';
	  echo "<FORM ACTION='";
	  echo htmlentities($_SERVER["PHP_SELF"]);
	  echo "' METHOD='POST'><input type='hidden' name='number' value= '";
	  echo $ligne[4];
	  echo "' ><input type='hidden' name='rubriq' value= '";
	  echo $cible ;
	  echo "' ><INPUT name='date' type='hidden' id='date' value='".$date."'>";
	  if($ligne[6]=="0") echo "<INPUT TYPE='SUBMIT' NAME='modif' VALUE='' class='bt-modifier'>&nbsp;
	  <INPUT TYPE='SUBMIT' NAME='suppr' VALUE='' class='bt-supprimer'>";
	  echo "</form>";
	  echo '</th></tr>';
	  echo '</tbody>';
	  echo '<tbody><tr><th colspan=2><hr></th></tr></tbody>';
	  }
	  else {
	  echo '<tbody><tr><th colspan=2></th></tr></tbody>';
	  echo '<tbody>';
	  echo '<tr>';
	  //affichage de la seance
	  echo '<td class="afaire">Donn&eacute; le :&nbsp;'.$ligne[0].'<br />Visible le '.$ligne[7];
	  //affichage, s'il existe, du travail a effectuer
	  if ($ligne[2]!="") {
	  echo '<br/>Pour le :&nbsp;'.$ligne[3].'</td>';
	  if($ligne[6]=="1") echo '<td class="contenu2">';else echo '<td class="contenu">';
	   echo $textafaire.'</td></tr>';
	  }
	  //fin

	  echo '</tbody>';
	   echo '<tbody><tr><th class="bas" colspan=2>';
	  echo "<FORM ACTION='";
	  echo htmlentities($_SERVER["PHP_SELF"]);
	  echo "' METHOD='POST'><input type='hidden' name='number' value= '";
	  echo $ligne[4];
	  echo "' ><input type='hidden' name='rubriq' value= '";
	  echo $cible ;
	  echo "' ><INPUT name='date' type='hidden' id='date' value='".$date."'>";
	  if($ligne[6]=="0") echo "<INPUT TYPE='SUBMIT' NAME='modif' VALUE='' class='bt-modifier'>&nbsp;
	  <INPUT TYPE='SUBMIT' NAME='suppr' VALUE='' class='bt-supprimer'>";
	  echo "</form>";
	  echo '</th></tr>';
	  echo '</tbody>';
	  echo '<tbody><tr><th colspan=2><hr></th></tr></tbody>';
	  }
}
echo '</table>';
echo "</div>";  //fin du div de la boite 5 : contenu du cahier de texte
echo '</div>'; //fin du div container
echo '<div id="musique"></div>';
include ('../Includes/pied.inc');
?>
</body>
</html>
