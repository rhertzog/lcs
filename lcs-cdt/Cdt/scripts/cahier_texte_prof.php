<?php

/* =============================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 1.0 du 29/10/2008
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script du cahier de textes PROF -
			_-=-_
   ============================================= */
session_name("Cdt_Lcs");
@session_start();

//si la page est appelée par un utilisateur non identifié
if (!isset($_SESSION['login']) )exit;

//si la page est appelée par un utilisateur non prof
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

//redirection vers la config du cahier à la première utilisation
			
// Connexion à la base de données
require_once ('../Includes/config.inc.php');

// Créer la requête.
$rq = "SELECT classe,matiere,id_prof FROM onglets
 WHERE login='{$_SESSION['login']}' ORDER BY classe ASC ";
 
// lancer la requête
$result = @mysql_query ($rq) or die (mysql_error()); 

// si pas de rubrique, on redirige vers config_ctxt.php
if (mysql_num_rows($result)==0) 
	{
	echo("Votre cahier de textes ne comporte actuellement aucune rubrique. Cliquez sur le lien suivant pour ");
	echo ("<a href='config_ctxt.php'>Personnaliser votre cahier de textes</a>");exit;
	mysql_close();	
	exit;
	}
include_once("../Includes/fonctions.inc.php");	
require_once("../Includes/class.inputfilter_clean.php");
include_once ('../Includes/markdown.php'); //convertisseur txt-->HTML

?>

<! cahier_textes_prof.php version 1.0 par Ph LECLERC - Lgt "Arcisse de Caumont" 14400 BAYEUX - philippe.leclerc1@ac-caen.fr >
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title>Cahier de textes numérique</title>
<meta http-equiv="content-type" content="" content; charset=ISO-8859-2" >
<LINK  href="../style/style.css" rel=StyleSheet type="text/css">
<script language="javascript" type="text/javascript" src="../tinymce/jscripts/tiny_mce/tiny_mce.js" src="../Includes/barre_java.js"></script>
<script language="javascript" type="text/javascript">

// Creates a new plugin class and a custom listbox
tinymce.create('tinymce.plugins.ExamplePlugin', {
    createControl: function(n, cm) {
        switch (n) {
            case 'mylistbox':
                var mlb = cm.createListBox('mylistbox', {
                     title : 'Insérer',
                     onselect : function(v) {
                         if ( v == 'val1' ) image_popup();
                         else if ( v == 'val2' ) joint_popup();				                         
                         else if  (v == 'val3' ) lien_popup();
                         else if ( v== 'val4' ) form_popup();
                     }
                });

                // Add some values to the list box
                mlb.add('une image', 'val1');
                mlb.add('une piece jointe', 'val2');
                mlb.add('un lien', 'val3');
                mlb.add('une expr math', 'val4');

                // Return the new listbox instance
                return mlb;

            
        }

        return null;
    }
});

// Register plugin with a short name
tinymce.PluginManager.add('example', tinymce.plugins.ExamplePlugin);

// Initialize TinyMCE with the new plugin and listbox



tinyMCE.init({
    //  plugins : '-example',- tells TinyMCE to skip the loading of the plugin
    mode : "textareas",
	theme : "advanced",
	editor_selector : 'mceAdvanced',
	skin : "o2k7",
	skin_variant : "black",
	theme_advanced_disable : "hr,visualaid,removeformat,separator, cleanup,help ",
	plugins : "example,safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,iespell,media,emotions,searchreplace,print,contextmenu,paste,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
	theme_advanced_buttons1 : "mylistbox,bold,italic,underline,strikethrough,|,sub,sup,|,charmap,emotions,hr,|,,justifyleft,justifycenter,justifyright,justifyfull,|,cut,copy,paste,pastetext,|,search,replace,fontselect",
	theme_advanced_buttons2 : "bullist,numlist,|,undo,redo,|,link,unlink,forecolor,backcolor,|,tablecontrols,|,fullscreen,code,fontsizeselect",
	theme_advanced_buttons3 : "",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	extended_valid_elements : "a[name|href|target|title|onclick],img[class|src|border|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name|style],hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style]",
	template_external_list_url : "example_template_list.js",
	language : "fr",
	file_browser_callback : "myFileBrowser",
	setup : function(ed) {
        // Add a custom button
        ed.addButton('mybutton', {
            title : 'Insérer une image',
            image : 'jscripts/tiny_mce/plugins/example/img/example.gif',
            onclick : function() {
		// Add you own code to execute something on click
					image_popup();
               
            }
          
        });
         
        
    }
});

</script>
</head>

<body  link="#000000" vlink="#000000" alink="#000000" BACKGROUND="../images/espperso.jpg" >
 <style>
<!--
a:link {text-decoration:none; color: #000000; font-family:   arial, verdana ; font-size : 8pt }
a:visited {text-decoration: none; color: #cc0000; font-family: arial, verdana ; font-size: 8pt}
a:active {text-decoration: none; color: #000099; font-family: arial, verdana ; font-size: 10pt}
a:hover {text-decoration: none; color: #990000; background-color: #FFFFCC ;font-family: arial, verdana ; font-size: 8pt}

body { }
<?
if (eregi('msie', $HTTP_USER_AGENT) )
echo '
#boite1 { position:absolute; top:73px; left:10px;  z-index:1; }
#boite2 { position:absolute; top:175px; left:200px;height:30px;z-index:1;  }
#boite3 { position:absolute; top:255px; left:10px; z-index:1; }
#boite4 { position:absolute; top:280px; left:700px;   }
#boite5 { position:absolute; top:490px; left:10px;   }';
else echo '
#boite1 { position:absolute; top:50px; left:10px;  z-index:1; }
#boite2 { position:absolute; top:200px; left:200px;height:30px;z-index:1;  }
#boite3 { position:absolute; top:215px; left:10px; z-index:1; }
#boite4 { position:absolute; top:260px; left:720px;   }
#boite5 { position:absolute; top:425px; left:10px;   }
#bouton {
     margin-left: auto;
     margin-right: auto;
     width: 100px;  }';
?>
-->
</style>

<div style="position:absolute; top:0px; left:100px;z-index:3;">
<table class="ds_box" cellpadding="0" cellspacing="0" id="ds_conclass" style="display: none;">
<tr><td id="ds_calclass">
</td></tr>
</table>
</div>
<SCRIPT language="Javascript" src="../Includes/calend.js"></script>
<?
$tsmp=time();
$tsmp2=time() + 604800;
$cours="";//variable temporaire de $Cours (avec une majuscule) pour un UPDATE
$afaire="";//variable temporaire de $Afaire (avec une majuscule) pour un UPDATE
$article="";


//on récupère le paramètre de la rubrique
if (isset($_GET['rubrique']))
	{
	$cible=$_GET['rubrique'];
	} 
elseif (isset($_POST['rubriq']))
	{
	$cible=$_POST['rubriq'];
	}
else $cible="";

// Connexion à la base de données
require_once ('../Includes/config.inc.php');

/*================================
   -      Traitement du formulaire  -
   ================================*/
   
  //Suppression d'un commentaire après confirmation
if (isset($_GET['com'])&& isset($_GET['rubrique'])) 
	{
	//l'article existe il ?
	$rq = "SELECT login  FROM cahiertxt	WHERE id_rubrique='{$_GET['com']}' AND  (login='{$_SESSION['login']}')";	//$rq = "SELECT login FROM cahiertxt WHERE id_rubrique = 'toto' AND  (login='{$_SESSION['login']}')";
 // lancer la requête
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
		$Cours  =($_POST['Cours']);
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
	$Morceauc=split('-',$_POST['datejavac']);
	$jour_c=$Morceauc[0];
	$mois_c=$Morceauc[1];
	$an_c=$Morceauc[2];
	$date_c = $an_c.$mois_c.$jour_c;
	$Morceauf=split('-',$_POST['datejaf']);
	$jour_f=$Morceauf[0];
	$mois_f=$Morceauf[1];
	$an_f=$Morceauf[2];
	$date_af = $an_f.$mois_f.$jour_f; 

	// Créer la requête d'écriture pour l'enregistrement des données
	if (isset($_POST['enregistrer']) && ((strlen($Cours) > 1) || (strlen($Afaire) > 1)))
			{
			$rq = "INSERT INTO cahiertxt (id_auteur,login,date,contenu,afaire,datafaire ) 
	        VALUES ( '$cible','{$_SESSION['login']}', '$date_c', '$Cours', '$Afaire', '$date_af')";
			}
			
	//Créer la requête pour la mise à jour des données	
	if (isset($_POST['modifier']))
			{
			$artic= ($_POST['numart']);
			$rq = "UPDATE  cahiertxt SET date='$date_c', contenu='$Cours', afaire='$Afaire',datafaire='$date_af'  
			WHERE id_rubrique='$artic'";
			}		
	// lancer la requête
	    $result = mysql_query($rq); 
	    if (!$result)  // Si l'enregistrement est incorrect
		    {                           
		     echo "<p>Votre commentaire n'a pas pu être enregistré à cause d'une erreur système".
			 "<p></p>" . mysql_error() . "<p></p>";
			 mysql_close();     // refermer la connexion avec la base de données
			 exit();
			}
	 
	//diffusion des commentaires vers les classes de même niveau
	if ((isset($_SESSION['grp']) ) && (isset($_SESSION['dif_c']) ) && (isset($_SESSION['dif_af']) ) 
	 && (isset($_POST['enregistrer']) && ((strlen($Cours) > 1) || (strlen($Afaire) > 1))))
		{
		for ($loop=0; $loop < count ($_SESSION['grp'])  ; $loop++)
					{
					$cibledif=$_SESSION['grp'][$loop];
					//$date_cdif=$_SESSION['dif_c'][$loop];
					//$date_afdif=$_SESSION['dif_af'][$loop];
					//Traitement de dates
					$Morceauc_dif=split('-',$_SESSION['dif_c'][$loop]);
					$jour_c_dif=$Morceauc_dif[0];
					$mois_c_dif=$Morceauc_dif[1];
					$an_c_dif=$Morceauc_dif[2];
					$date_c_dif = $an_c_dif.$mois_c_dif.$jour_c_dif;
					$Morceauf_dif=split('-',$_SESSION['dif_af'][$loop]);
					$jour_f_dif=$Morceauf_dif[0];
					$mois_f_dif=$Morceauf_dif[1];
					$an_f_dif=$Morceauf_dif[2];
					$date_af_dif = $an_f_dif.$mois_f_dif.$jour_f_dif; 



					$rq = "INSERT INTO cahiertxt (id_auteur,login,date,contenu,afaire,datafaire ) 
					VALUES ( '$cibledif','{$_SESSION['login']}', '$date_c_dif', '$Cours', '$Afaire', '$date_af_dif')";
					// lancer la requête
					$result = mysql_query($rq); 
					if (!$result)  // Si l'enregistrement est incorrect
						{                           
						echo "<p>Votre commentaire n'a pas pu être enregistré à cause d'une erreur système".
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

// Créer la requête (Récupérer les rubriques de l'utilisateur) 
$rq = "SELECT classe,matiere,id_prof,postit FROM onglets
 WHERE login='{$_SESSION['login']}' ORDER BY id_prof ASC ";

 // lancer la requête
$result = @mysql_query ($rq) or die (mysql_error());
$nb = mysql_num_rows($result);  // Combien y a-t-il d'enregistrements ?

//on récupère les données
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
$pourcen = 100/$nb;

//création de la barre de menu , couleur de fond # pour l'onglet actif
echo("<table width='100%' border='1' bordercolor='#E6E6FF'  cellspacing='0' cellpadding='0'><tr bgcolor='#cccccc' >");
for($x=0;$x < $nb;$x++)
	{
		if ($cible == ($numero[$x]))
			{
			echo("<td width='$pourcen%' bgcolor='#4169E1' color='#ffffff'><font face=\"arial\"color=\"#FFFFFF\"size=2>
			<B><div align='center' color='#FFFFFF'><B> $mat[$x]<br>$clas[$x]"."</a></div></td>");
			//contenu du post-it de la rubrique active
			$contenu_postit=$com[$x];
			}
		else 
			{
			echo("<td width='$pourcen%'><div align='center'><a href='cahier_texte_prof.php?rubrique=$numero[$x]'
			'onmouseover=\"window.status='';return true\" >$mat[$x]<br>$clas[$x]</a></div></td>");
			}
	}
echo("</tr></table>");

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
	DATE_FORMAT(datafaire,'%d'),DATE_FORMAT(datafaire,'%m'),DATE_FORMAT(datafaire,'%Y') FROM cahiertxt
	WHERE id_rubrique=$article ";
 // lancer la requête
	$result = @mysql_query ($rq) or die (mysql_error());
	$nb = mysql_num_rows($result);
	//s'il existe, on récupère les datas pour les afficher dans les champs
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
		//calcul du timestamp correspondant à la date de l'article
		$tsmp=mkTime(0,0,1,$val_mois,$val_jour,$val_annee);
		//calcul du timestamp correspondant à la date "a faire" de l'article
		$tsmp2=mkTime(0,0,1,$val_moisaf,$val_jouraf,$val_anneeaf);// - 604800 604800 pour compenser offset 7 jours
		}
		}
	} 
	
//recherche de la matière et la classe de la rubrique active du cahier de textes
if (isset($cible))
	{ 
	$rq = "SELECT classe,matiere FROM onglets
	WHERE id_prof='$cible'  ";
	// lancer la requête
	$result = @mysql_query ($rq) or die (mysql_error());
	// Combien y a-t-il d'enregistrements ?
	$nb = mysql_num_rows($result); 
	//on récupère les données
	while ($enrg = mysql_fetch_array($result, MYSQL_NUM)) 
		{$classe_active=$enrg[0];//classe
		$mati_active=$enrg[1];//matière
		}
	
	}	
/*================================
   -      Affichage du formulaire  -
   ================================*/

$datjc=getdate($tsmp);
$jo=date('d',$datjc['0']);
$mo=date('m',$datjc['0']);
$ann=date('Y',$datjc['0']);
$dtajac=$jo."-".$mo."-".$ann;
$datjf=getdate($tsmp2);
$jof=date('d',$datjf['0']);
$mof=date('m',$datjf['0']);
$annf=date('Y',$datjf['0']);
$dtajaf=$jof."-".$mof."-".$annf;
   
?>

<form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>?rubrique=<?echo $cible;?>" method="post" name= "cahtxt" >
<SCRIPT language="Javascript" src="../Includes/barre_java.js"></SCRIPT>

<div id="boite1" ;>
	<I><FONT FACE="Arial"></I>  
	<FONT size="2" color="#003399">&nbsp; Cours  du :
 	<input onclick="ds_sh(this);" size="9" name="datejavac" value="<?echo $dtajac?>"readonly="readonly" style="cursor: text">
	<TEXTAREA id="coursfield"  NAME="Cours"  class="mceAdvanced" COLS=1ROWS=1 WRAP=virtual style="background-color:#F0F0FA"><?echo $cours;?> </TEXTAREA>
	<input type="hidden" name="numart" value= "<? echo $article ; ?>" ></div>
  
<div id="boite3" ;>
	<p> &nbsp A faire  	
	<FONT size="2"> pour le :</font>
	<input onclick="ds_sh(this);" size="9" name="datejaf" value="<?echo $dtajaf?>"readonly="readonly" style="cursor: text" >
	<TEXTAREA id="afairefield"  NAME="Afaire" class="mceAdvanced" COLS=1 ROWS=1 WRAP=virtual style="background-color:#F0F0FA"><?echo $afaire;?></TEXTAREA> 
	</p></div>

<div id="boite4" ;>
	
<? if (isset($_POST['modif'])&& isset($_POST['number'])) 
		{
		echo ('<INPUT name="rubrique" type="hidden" id="rubrique" value="'.$cible.'">
		<TABLE align="left"><TD><INPUT TYPE=SUBMIT NAME="modifier" VALUE="Enregistrer les modifications"
		style="background-color:#E6E6FA"> 	</TD></TABLE>
		<INPUT name="date" type="hidden" id="date" value="'.$date.'"/></TABLE>');
		}
		
	else 
		{
		echo ('<INPUT name="rubrique" type="hidden" id="rubrique" value="'.$cible.'"/>
		<INPUT name="date" type="hidden" id="date" value="'.$date.'">
		<div id="bouton" ;>
		<TABLE  width="100px" >
		<TR  >
		<TD align="center"><input type="submit" name="personnaliser" value="Personnalisation du cahier"
		style="background-color:#E6E6FA"></TD></TR><TR>
		<TD  align="center"><input type="submit" name="enregistrer" value="Enregistrer" style="background-color:#E6E6FA">
		<br><FONT size="2"> <a href="#" onClick="diffuse_popup('.$cible.'); return false" >Enregistrements multiples 
		</A></font></TD></TR><TR >
		<TD  align="center" ><INPUT TYPE="button" VALUE="Annuler" onClick="history.back()" style="background-color:#E6E6FA"></TD></TR><TR>
		<TD  align="center" ><INPUT TYPE=SUBMIT NAME="planning" VALUE="Planifier un devoir en '.$classe_active.'" 
		style="background-color:#E6E6FA"></TD></TR><TR>
		<TD  align="center" border="1"><a href="imprim.php?rubrique='.$cible.'" target="blank">
		<img src="../images/imprimante.gif"border=0 title="Imprimer"> </a></TD></TR>
		</TABLE></div>');
		}
?>

</div>
<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
<?
if (eregi('msie', $HTTP_USER_AGENT) )
echo '<br><br><br>';
?>

</FORM>
<?

/*======================================
   - Affichage du contenu du cahier de textes  -
   =======================================*/
//include ("../Includes/fonctions.inc.php");  
include_once ('../Includes/markdown.php'); //convertisseur txt-->HTML

//créer la requête
if ($_SESSION['version']=">=432") setlocale(LC_TIME,"french");
	else setlocale("LC_TIME","french");
$rq = "SELECT DATE_FORMAT(date,'%d/%m/%Y'),contenu,afaire,DATE_FORMAT(datafaire,'%d/%m/%Y'),id_rubrique,date FROM cahiertxt
 WHERE (id_auteur=$cible) AND (login='{$_SESSION['login']}') ORDER BY date desc ,id_rubrique desc";
 
// lancer la requête
$result = @mysql_query ($rq) or die (mysql_error());

// Combien y a-t-il d'enregistrements ?
$nb2 = mysql_num_rows($result); 

echo('<div  id="boite5" ><TABLE WIDTH=100% BORDER=1 CELLPADDING=1 CELLSPACING=1  >
	');
while ($ligne = mysql_fetch_array($result, MYSQL_NUM)) 
	  { 
	  $textcours=stripslashes(markdown($ligne[1]));
	  //$textcours=$ligne[1];
	  $textafaire=stripslashes(markdown($ligne[2]));
	  //$day="1,0,0,12,1,2007";echo $day;
	  $jour=LeJour(strToTime($ligne[5]));
	  //debut
	  echo ("  <TR><TD color=\"#FFFFFF\" ></TD></TR>	  
	  <TR VALIGN=middle >
	  <TD align=center WIDTH=25% BGCOLOR=\"#4169E1\">
	  <font face=\"Arial\"color=\"#FFFFFF\"size=2><B>Séance du ".$jour." $ligne[0]</B></font></TD>");
	  echo "<FORM ACTION='";
	  echo htmlentities($_SERVER["PHP_SELF"]);
	  echo "' METHOD='POST'><td align=left WIDTH=15% colspan=1>
	  <input type='hidden' name='number' value= '";
	  echo $ligne[4];
	  echo "' ><input type='hidden' name='rubriq' value= '";
	  echo $cible ;
	  echo "' ><INPUT name='date' type='hidden' id='date' value='".$date."'>
	  <INPUT TYPE='SUBMIT' NAME='modif' VALUE='Modifier' style='background-color:#E6E6FA'>&nbsp;
	  <INPUT TYPE='SUBMIT' NAME='suppr' VALUE='Supprimer' style='background-color:#E6E6FA'></td>
	  </form>"."
	  <TD color=\"#FFFFFF\" ></TD></TR>
	  <TR VALIGN=TOP >
	  <TD align=left WIDTH=100% colspan=3 BGCOLOR=\"#E6E6FA\"><font face='Arial' color='#0000cc' size=2>$textcours</font></TD></TR>";
	  if($ligne[2]!="") 
	  echo ("<TR VALIGN=TOP ><TD WIDTH=100% colspan=3 BGCOLOR=\"#E6E6FA\"><font face=\"Arial\"color=\"#cc0000\"size=2>
	  <B>A faire pour le :</B> $ligne[3] <BR> </font><font face=\"Arial\"color=\"#009900\"size=2>$textafaire</font></TR>");
	  //fin
	  echo("<TR></TR><TR></TR><TR></TR><TR></TR><TR></TR><TR></TR>");
	  }
echo "</table>";

include ('../Includes/pied.inc');
echo "</div>";  
if (eregi('msie', $HTTP_USER_AGENT) )
echo '<div  style="top:70;left:660; position:absolute;">';
else 
echo '<div  style="top:30; left:660; position:absolute;">';

?>
<SCRIPT language="Javascript" src="../Includes/barre_java.js"></SCRIPT>
<script type="text/javascript" src="../Includes/jquery.js"></script>
<script type="text/javascript" src="../Includes/interface.js"></script>
<style type="text/css" media="screen">
*
#myAccordion{
	width: 300px;
	border: 1px solid #1a2b68;
	position: absolute;
	left: 10px;
	top:10px;
}
#myAccordion dt{
	line-height: 18px;
	background-color: #415F9B;
	border-top: 2px solid #d4d0c8;
	border-bottom: 2px solid #1a2b68;
	padding: 0 10px;
	font-weight: bold;
	color: #fff;
	
}
#myAccordion dd{
	overflow: auto;
margin-left :10px;
margin-top :1px;


#myAccordion p{
	margin: 3px 3px;
}
#myAccordion dt.myAccordionHover
{
	background-color: #368dc7;
}
#myAccordion dt.myAccordionActive
{
	background-color: #1a2b68;
	border-top: 2px solid #415F9B;
	border-bottom: 2px solid #000;
	
}
</style>
<dl id="myAccordion">
	<dt class="someClass">Aide m&eacute;moire</dt>
	<dd ><form name="aidememory"><textarea  WRAP=virtual style="background:#F4F04F;align:left"  name="monpostit"  rows="3" cols="27"  >
	<? if ($contenu_postit !="") echo $contenu_postit;
	else echo "Penser &agrave; ...";?></textarea><a href='./posti1.php'  onClick='postit_popup(<? echo $cible; ?>); return false'  title="Enregistrer le post-it" > <img src="../images/Disk.ico" border="0" alt="Save" /></A>	
	</form>
	</dd>
	<dt class="someClass">Archives</dt>
	
<?
/*================================
   -      Affichage des archives  -
   ================================*/ 
	echo (' <dd><B><FONT COLOR="#0000FF" style="align:center">Archives disponibles :<br></font></B> ');
 	//recherche du  nom des archives
	$TablesExist= mysql_list_tables(DB_NAME);
	$x=0;
	while ($table=mysql_fetch_row($TablesExist))
	if (ereg("^onglets[[:alnum:]]",$table[0]))
	{
	$archive=split('s',$table[0]);
	$x++;
	$archnum=$archive[1];
	echo '<a href="#" onClick="arch_popup(\''.$archive[1].'\'); return false" >- '.$archive[1].' </a> <br>  ';
	
}
//s'il n'esiste pas d'archive
	if ($x==0) 
		{
		echo '<B><FONT COLOR="#CC0000" >Aucune </FONT></B>';
		}
?>
		
	</dd>
	<dt class="someClass">Liens</dt>
	<dd>Ces liens s'ouvrent dans une nouvelle fenêtre
	<BR><a href="cahier_text_eleve.php<?echo '?mlec547trg2s5hy='.$classe_active?> " target="blank" width="900",height="800"> - CAHIER DE TEXTE élèves 
	</B></a>
	<br> <?echo '<a href="absences.php?mlec547trg2s5hy='.$classe_active .'" target="blank"  ';?> > - CARNET D'ABSENCES -  </B></a>
	<br> <?echo '<a href="raccourcis.php" target="blank"  onMouseOver="window.status='."'".'raccourcis typo'."'".'; return true;"';?> > - AIDE - </B></a>
	
</dd>
	<dt class="someClass">A propos </dt>
	<dd>Pour toutes remarques ou suggestions:<BR>
		 <LI><a href="mailto:Philippe.Leclerc1@ac-caen.fr"><b> Philippe LECLERC </B><I>Misterphi</I>
		 <LI><a href="mailto:lcs-devel@listes.tice.ac-caen.fr"><b> Equipe-TICE-CRDP-CAEN</b>
	</dd>
	
</dl>
<script type="text/javascript">
	
	$(document).ready(
		function()
		{
			$('#myAccordion').Accordion(
				{
					headerSelector	: 'dt',
					panelSelector	: 'dd',
					activeClass		: 'myAccordionActive',
					hoverClass		: 'myAccordionHover',
					panelHeight		: 90,
					speed			: 300
				}
			);
		}
	);
</script>
	
</div>


</body>
</html>
