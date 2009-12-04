<?
/* =============================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 2.0 du 25/10/2009
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script du cahier de textes ELEVE -
   			_-=-_
   ============================================= */
session_name("Cdt_Lcs");
@session_start();
if ((!isset($_SESSION['version'])) || (!isset( $_SESSION['saclasse']) && !isset($_SESSION['login'])) ) exit; 
include ('../Includes/data.inc.php');
include_once("../Includes/fonctions.inc.php");
include ("/var/www/Annu/includes/ldap.inc.php");	
include ("/var/www/lcs/includes/headerauth.inc.php");
include ("/var/www/Annu/includes/ihm.inc.php");  
include "../Includes/functions2.inc.php";

//si clic sur Planning des  devoirs  
if (isset($_POST['plan']))
	{$clas=$_POST['numrub'];
	header ("location:plan_eleves.php?classe=$clas");
	exit();
	}


//teste si $x fait partie des classes autorisées 
function is_authorized($x) {
	$flg="false";
	foreach ($_SESSION['saclasse'] as $clé => $valeur)
	  { 
	  if ($valeur==$x)
	  	 {
	  		$flg="true";
	 	 	break;
	  	 }
	  }
	return $flg;
}

//contrôle des paramètres $_GET
if ((isset($_GET['div'])) && (isset($_SESSION['saclasse'])))
	{	
	if (is_authorized($_GET['div'])=="false")   exit;
	}

if ((isset($_GET['mlec547trg2s5hy'])) && (isset($_SESSION['saclasse'])))
	{
	if (is_authorized($_GET['mlec547trg2s5hy'])=="false")  exit;
	}
		
//si clic sur un lien 	
if(isset($_GET['nvlkzei5qd1egz65'])) 
	$code=$_GET['nvlkzei5qd1egz65'];	
	else
	$code="";

//première passe
	if ($code!='cv5e8daérfz8ge69&é') 
	{//initialisation de la variable 
	if (isset($_SESSION['saclasse'][1])) $ch=$_SESSION['saclasse'][1]; 
	else $ch=$classe[0];
	} 
//initialisation 
$tsmp=0;
if(isset($_GET['div'])) 
$ch=$_GET['div'];
?>
<!-- Cahier_texte/_eleve.php version 1.3 par Ph LECLERC - Lgt "Arcisse de Caumont" 14400 BAYEUX - philippe.leclerc1@ac-caen.fr-->
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title>Cahier de textes numérique</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<LINK  href="../style/style.css" rel=StyleSheet type="text/css">
<LINK  href="../style/navlist-eleve.css" rel=StyleSheet type="text/css">
<!--[if IE]>
<link href="../style/style-ie.css"  rel="stylesheet" type="text/css"/>
<![endif]-->
</head>

<body>
<div id="container-cdt-elv">
<?
$tsmp=time();

// Connexion à la base de données
require_once ('../Includes/config.inc.php');

//récupération du paramètre classe pour mémorisation
if (isset($_GET['mlec547trg2s5hy'])) 
	{$ch=$_GET['mlec547trg2s5hy'];} 
//récupération du paramètre timestamp pour mémorisation
if (isset($_GET['tsmp'])) 
	{$tsmp=$_GET['tsmp'];} 
// si OK a été cliqué
if ((isset($_POST['valider'])) || (isset($_POST['viser'])))
	{ // Traiter le formulaire
		//la classe
		if (strlen($_POST['mlec547trg2s5hy']) > 0)
		{ 
		$ch = stripslashes($_POST['mlec547trg2s5hy']);
		}
		//la date, le timestamp 
		$date_c = $_POST['an_c'].$_POST['mois_c'].$_POST['jour_c'];
		$tsmp=mkTime(0,0,1,$_POST['mois_c'],$_POST['jour_c'],$_POST['an_c']) + 5184000;
	}
	
if ( isset($_POST['viser']))
	{
	 $dat_vis= date('Y-m-d');
	 $rub_vis=$_POST['rub_activ'];
	 //echo $dat_vis." ".$rub_vis."-";
	 $rq = "UPDATE  onglets SET visa='1', datevisa='$dat_vis'  
				WHERE id_prof='$rub_vis'"; 
				
	// lancer la requête
		$result = mysql_query($rq); 
		if (!$result)  // Si l'enregistrement est incorrect
			{                           
			 echo "<p>Votre rubrique n'a pas pu être enregistrée à cause d'une erreur système".
			"<p></p>" . mysql_error() . "<p></p>";
			}
	 $dat_la=date('Y-m-d');
	 $rq = "UPDATE cahiertxt SET on_off='1' WHERE id_auteur='$rub_vis' AND date<'$dat_la' AND datevisibi<='$dat_la'";
	 // lancer la requête
		$result = mysql_query($rq); 
		if (!$result)  // 
			{                           
			 echo "<p>L'apposition du visa n'a pu être réalisée à cause d'une erreur système".
			"<p></p>" . mysql_error() . "<p></p>";
			}					
	}	
	
//on récupère le paramètre de la rubrique (prof/matiere/classe)
if (isset($_GET['rubrique'])) 
	$cible=$_GET['rubrique'];
	else $cible="";
	
if ((isset($_POST['rub_activ'])) &&  (isset($_POST['viser']))) 
	{
	$cible=$_POST['rub_activ'];
	}

//fin traitement formulaire

//Traitement de la barre des menus

// Créer la requête (Récupérer les rubriques de la classe) 
$rq = "SELECT id_prof FROM onglets
 WHERE classe='$ch' ORDER BY 'id_prof' asc ";

 // lancer la requête
$result = @mysql_query ($rq) or die (mysql_error());
$nb = mysql_num_rows($result);  // Combien y a-t-il d'enregistrements ?
if ($nb>0)
	{
	//on récupère les données
	$loop=0;
	while ($enrg = mysql_fetch_array($result, MYSQL_NUM)) 
		{
		$num_ero[$loop]=$enrg[0];//numéro de l'onglet
		$loop++;
		}
	if($cible==""){$cible=($num_ero[0]);}	
		}

//affichage du formulaire
?>
<form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>?nvlkzei5qd1egz65=cv5e8daérfz8ge69&é" method="post">
<SCRIPT language="Javascript" src="../Includes/barre_java.js"></SCRIPT>
<fieldset id="field7">
<legend id="legende">Cahier de textes élève</legend>
Contenu du cahier de textes de 
<?
//si on ne  connait pas la classe , on affiche un menu déroulant  
if (!isset($_SESSION['saclasse']))
	{
	echo "<select name='mlec547trg2s5hy'style='background-color:#E6E6FA'>";
	foreach ($classe as $clé => $valeur)
	  { echo "<option valeur=\"$valeur\"";
	  if ($valeur==$ch) {echo 'selected';}
	  echo ">$valeur</option>\n";
	  }
	echo "</select>";
	}
	//sinon on affiche uniquement la classe de l'élève 
	elseif (count($_SESSION['saclasse'])==1) echo '<B>'.$_SESSION['saclasse'][1].'</B>';
	else 
	{
	echo "<select name='mlec547trg2s5hy'style='background-color:#E6E6FA'>";
	foreach ($_SESSION['saclasse'] as $clé => $valeur)
	  { echo "<option valeur=\"$valeur\"";
	  if ($valeur==$ch) {echo 'selected';} 
	  echo ">$valeur</option>\n";
	  }
	echo "</select>";
	}
  ?>

depuis le :	<?calendrier_auto(-60,'jour_c','mois_c','an_c',$tsmp);?>
<input type="hidden" name="numrub" value= "<? echo $ch ; ?>">
<input type="hidden" name="rub_activ" value= "<? echo $cible; ?>">
<input type="submit" name="valider" value="" class="bt-valid"> 
<INPUT TYPE="button" VALUE="" class="bt-annul" onClick="history.back()">
<div id="services">
<INPUT TYPE="SUBMIT" NAME="plan" VALUE="" class="bt-plan-dev">
<?
//Affichage du bouton pour apposer le visa  
if (ldap_get_right("Cdt_can_sign",$_SESSION['login'])=="Y")
	{
	echo '<INPUT TYPE="SUBMIT" NAME="viser" VALUE="" class="bt-visa">';
	}

//Affichage du lien des absences 
if (ldap_get_right("Cdt_is_cpe",$_SESSION['login'])=="Y")
	{
	echo '<A href="./cpe.php"  target="_blank" id="bt-consult"></A>';
	}

if (( isset($_SESSION['parentde']))  && (!isset($_SESSION['login'])))
	{
	if ($FLAG_ABSENCE==1)	{
	
	foreach ( $_SESSION['parentde'] as $cle => $valcla)
		{
		if ($valcla[2]==$ch) {
		echo '<A href="#" id="bt-abs" title="Absences de '.$valcla[1].'" onClick="abs_popup(\''.$valcla[0].'\',\''.$valcla[1].'\'); return false" ></A>';
		$uid_actif=$valcla[0];
		}
		}
	}
	else
	{
	foreach ( $_SESSION['parentde'] as $cle => $valcla)
		{
		if ($valcla[2]==$ch) {
		$uid_actif=$valcla[0];
		}
		}	
		}
	
	}
//------	
if ($_SESSION['cequi']=="eleve") $uid_actif=$_SESSION['login'];	
$cmd="hostname -f";
exec($cmd,$hn,$retour);
$hostn= $hn[0];
$clcryt=substr(md5(crypt($ch,$Grain)),2);
//$clcryt=substr($clcryt,2,12);
?>
<A href="#" id="bt-taf" title="Travail A Faire des 15 prochains jours" onClick="taf_popup('<?echo $ch;?>'); return false" >&nbsp; </A>
<A href="http://fusion.google.com/add?source=atgs&feedurl=<?echo 'http://'.$hostn.'/Plugins/Cdt/scripts/flux_rss.php?div='.$ch.':'.$clcryt;?>" id="bt-google">&nbsp; </A>
<A href="flux_rss.php?div=<?echo $ch.':'.$clcryt;?>" id="bt-rss">&nbsp; </A>
</div>
</fieldset>
</FORM>


<?
// Créer la requête (Récupérer les rubriques de la classe) 
$rq = "SELECT prof,matiere,id_prof,prefix,visa,visa,DATE_FORMAT(datevisa,'%d/%m/%Y') FROM onglets
 WHERE classe='$ch' ORDER BY 'id_prof' asc ";

 // lancer la requête
$result = @mysql_query ($rq) or die (mysql_error());
$nb = mysql_num_rows($result);  // Combien y a-t-il d'enregistrements ? 
if ($nb>0)
	{
	//on récupère les données 
	$loop=0;
	while ($enrg = mysql_fetch_array($result, MYSQL_NUM)) 
		{
		$prof[$loop]=$enrg[0];//nom du prof
		$mat[$loop]=$enrg[1];//matière 
		$numero[$loop]=$enrg[2];//numéro de l'onglet
		$pref[$loop]=$enrg[3];// préfixe
		$restr[$loop]=$enrg[4];//restric
		$visa[$loop]=$enrg[5];// 
		$datvisa[$loop]=$enrg[6];
		$loop++;
		}
		//recherche des onglets "cours d'un eleve"
if ($uid_actif!="") {
	 $groups=people_get_cours($uid_actif);
if ( count($groups) > 0 ) {
    for ($loopo=0; $loopo < count ($groups) ; $loopo++) {
      $rq = "SELECT prof,matiere,id_prof,prefix,visa,visa,DATE_FORMAT(datevisa,'%d/%m/%Y') FROM onglets
		WHERE classe='{$groups[$loopo]["cn"]}' ORDER BY 'id_prof' asc ";
		$result = @mysql_query ($rq) or die (mysql_error());
		$nb = mysql_num_rows($result);  // Combien y a-t-il d'enregistrements ? 
		if ($nb>0)
			{
			//on récupère les données 
			while ($enrg = mysql_fetch_array($result, MYSQL_NUM)) 
				{
				$prof[$loop]=$enrg[0];//nom du prof
				$mat[$loop]=$enrg[1];//matière 
				$numero[$loop]=$enrg[2];//numéro de l'onglet
				$pref[$loop]=$enrg[3];// préfixe
				$restr[$loop]=$enrg[4];//restric
				$visa[$loop]=$enrg[5];// 
				$datvisa[$loop]=$enrg[6];
				$loop++;
				}
			}
      	}
	}

//fin onglets cours eleve 
}
else 
	{
	//recherche des onglets "Cours" de la classe
	
	if (!ereg("^Classe",$ch)) {
	$grp_cl=search_groups("cn=Classe_*".$ch);
	$grp_cl=$grp_cl[0]["cn"];
	}
	else $grp_cl=$ch;
	if ($grp_cl !="")
	{
	$uids = search_uids ("(cn=".$grp_cl.")", "half");	
	$liste_cours=array();
	$i=0;
	for ($loup=0; $loup < count($uids); $loup++)
		{
		$logun= $uids[$loup]["uid"];
		if (is_eleve($logun)) 
			{
			$groops=people_get_cours($logun);
			if (count($groops))
				{
				for($n=0; $n<count($groops); $n++)
					{ 
					if (!in_array($groops[$n]["cn"], $liste_cours)) 
						{	
						$liste_cours[$i]=$groops[$n]["cn"];
						$i++;
						}
					}
				}
			}
		}
	}
	if (count($liste_cours)>0)
		{
		for($n=0; $n<count($liste_cours); $n++)
			{
			$rq = "SELECT prof,matiere,id_prof,prefix,visa,visa,DATE_FORMAT(datevisa,'%d/%m/%Y') FROM onglets
		WHERE classe='{$liste_cours[$n]}' ORDER BY 'id_prof' asc ";
			$result = @mysql_query ($rq) or die (mysql_error());
			$nb = mysql_num_rows($result);  // Combien y a-t-il d'enregistrements ? 
			if ($nb>0)
				{
			//on récupère les données 
				while ($enrg = mysql_fetch_array($result, MYSQL_NUM)) 
					{
					$prof[$loop]=$enrg[0];//nom du prof
					$mat[$loop]=$enrg[1];//matière 
					$numero[$loop]=$enrg[2];//numéro de l'onglet
					$pref[$loop]=$enrg[3];// préfixe
					$restr[$loop]=$enrg[4];//restric
					$visa[$loop]=$enrg[5];// 
					$datvisa[$loop]=$enrg[6];
					$loop++;
					}
				}
			}
		}
	}
	
	//modif
	echo '<div id="onglev">';
	// Affichage de la colonne de gauche
	if($cible==""){$cible=($numero[0]);}	
	//création de la barre de menu , couleur de fond # pour cellule active
	echo '<ul id="navlist-elv">';	
	for($x=0;$x < $loop;$x++)
		{
		if ($cible == ($numero[$x])) 
		
			{
				echo ("<li id='active'><a href='cahier_text_eleve.php?nvlkzei5qd1egz65=cv5e8daérfz8ge69&é&rubrique=
			$numero[$x]&mlec547trg2s5hy=$ch&tsmp=$tsmp' id='courant'>&nbsp;$mat[$x]&nbsp;<br>&nbsp;$pref[$x]  $prof[$x]&nbsp;"."</a></li>");
			
			/*echo("<tr><td   bgcolor='#4169E1' color='#ffffff'><font face=\"arial\"color=\"#FFFFFF\"size=2><b><div align='center' color='#FFFFFF'>
			&nbsp $mat[$x] &nbsp<br> &nbsp $pref[$x]   $prof[$x]&nbsp".
			"</a></div></B></font></td></tr>");
			//if (($restr[$x]) &&  $_SESSION['saclasse']!=$ch && $_SESSION['cequi']!="prof" && $_SESSION['cequi']!="administratif") $noAff=true;*/	
			if ($visa[$x]) {
			$vis="ok";
			$datv=$datvisa[$x];
			}
			
			}
			else 
			{
			echo ("<li><a href='cahier_text_eleve.php?nvlkzei5qd1egz65=cv5e8daérfz8ge69&é&rubrique=
			$numero[$x]&mlec547trg2s5hy=$ch&tsmp=$tsmp'>&nbsp;$mat[$x]&nbsp;<br>&nbsp;$pref[$x]  $prof[$x]&nbsp;"."</a></li>");
			/*echo("<tr><td ><div align='center'><a href='cahier_text_eleve.php?nvlkzei5qd1egz65=cv5e8daérfz8ge69&é&rubrique=
			$numero[$x]&mlec547trg2s5hy=$ch&tsmp=$tsmp'>&nbsp $mat[$x] &nbsp <br> &nbsp $pref[$x]  $prof[$x] &nbsp "."</a></div></td></tr>");
			*/
			}
		
		
		}

	echo '</ul>';
	echo '</div>';
	}	
	else
		{ 
// Il y a eu un os !
	 		echo "<p><FONT face='Arial'size='3' COLOR='#FF0000'>Aucun enregistrement dans le cahier de textes de  $ch !</p><p></p></font>";
		}

//affichage du contenu du cahier de textes

		include_once ('../Includes/markdown.php');//convertisseur text-->HTML

		//créer la requête
		if ($cible!="") 
		{//élaboration de la date limite à partir de la date selectionnée
		$dat=date('Ymd',$tsmp-5184000);//2592000=nbre de secondes dans 30 jours
		$dat_now=date('YmdHis');
		if ($_SESSION['version']=">=432") setlocale(LC_TIME,"french");
		else setlocale("LC_TIME","french");
		$rq = "SELECT DATE_FORMAT(date,'%d/%m/%Y'),contenu,afaire,DATE_FORMAT(datafaire,'%d/%m/%Y'),id_rubrique,date,on_off FROM cahiertxt 			WHERE id_auteur=$cible AND date>=$dat AND datevisibi<=$dat_now ORDER BY date desc";
		 
		// lancer la requête
		$result = @mysql_query ($rq) or die (mysql_error());

		// Combien y a-t-il d'enregistrements ?
		$nb2 = mysql_num_rows($result);
		
		echo '<div id="boite5elv">';
		echo '<TABLE id="tb-cdt" CELLPADDING=1 CELLSPACING=2>';
		while ($ligne = mysql_fetch_array($result, MYSQL_NUM)) { 
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
	  echo '<td class="seance">S&eacute;ance du <br/>'.$jour.'&nbsp;'.$ligne[0].'</td>';
	  if($ligne[6]=="1") echo '<td class="contenu2">';else echo '<td class="contenu">';
	  echo $textcours.'</td></tr>';
	  //affichage, s'il existe, du travail a effectuer
	  if ($ligne[2]!="") 
	  echo '<tr><td class="afaire">A faire pour le :<br/>'.$ligne[3].'</td><td class="contenu">'.$textafaire.'</td></tr>';
	  //fin

	  echo '</tbody>';
	  echo '<tbody><tr><th colspan=2>(°-°)</tr></tbody>';
	  }
	  else {
	  echo '<tbody><tr><th colspan=2></th></tr></tbody>';
	  echo '<tbody>';
	  echo '<tr>';
	  //affichage de la seance
	  echo '<td class="afaire">Donn&eacute; le :&nbsp;'.$ligne[0];
	  //affichage, s'il existe, du travail a effectuer
	  if ($ligne[2]!="") 
	  echo '<br/>Pour le :&nbsp;'.$ligne[3].'</td>';
	  if($ligne[6]=="1") echo '<td class="contenu2">';else echo '<td class="contenu">';
	  echo $textafaire.'</td></tr>';
	  //fin
	  echo '<tbody><tr><th colspan=2>(°-°)</th></tr></tbody>';
	  }
} //fin du while
echo '</table>';
echo "</div>"; //fin du div boite5elv

		}
		if ($vis == "ok") echo '<div id="visa-cdt">'.$datv.'</div>';
echo '</div>'; //fin du div container	

include ('../Includes/pied.inc');
?>
</div>
</body>
</html>
