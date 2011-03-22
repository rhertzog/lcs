<?php
/* =============================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 2.3 du 06/01/2011
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script du cahier de textes ELEVE -
   			_-=-_
   "Valid XHTML 1.0 Strict"
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

//destruction des variables de sessions
unset ($_SESSION['prof']);
unset ($_SESSION['mat']);
unset ($_SESSION['numero']);
unset ($_SESSION['pref']);
unset ($_SESSION['visa']);
unset ($_SESSION['datvisa']);
//echo $delta."-".count($_SESSION['prof'])."-".$ch;exit;
//si clic sur Planning des  devoirs  
if (isset($_POST['plan']))
	{
        $clas=$_POST['numrub'];
	header ("location:plan_eleves.php?classe=$clas");
	exit();
	}


//teste si $x fait partie des classes autoris�es 
function is_authorized($x) {
	$flg="false";
	foreach ($_SESSION['saclasse'] as $cl� => $valeur)
	  { 
	  if ($valeur==$x)
	  	 {
	  		$flg="true";
	 	 	break;
	  	 }
	  }
	return $flg;
}

//contr�le des param�tres $_GET
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

//premi�re passe
	if ($code!='cv5e8da�rfz8ge69') 
	{//initialisation de la variable 
	if (isset($_SESSION['saclasse'][1])) $ch=$_SESSION['saclasse'][1]; 
	else $ch=$classe[0];
	} 
//initialisation 
$tsmp=0;
if (count($_SESSION['saclasse'])==1) $ch=$_SESSION['saclasse'][1];
if(isset($_GET['div'])) $ch=$_GET['div'];

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html  xmlns="http://www.w3.org/1999/xhtml" >
<head>
<title>Cahier de textes num&eacute;rique</title>
<meta name="author" content="Philippe LECLERC -TICE CAEN" />
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<link  href="../style/style.css" rel="stylesheet" type="text/css" />
<link  href="../style/deroulant.css" rel="stylesheet" type="text/css" />
<link  href="../style/navlist-eleve.css" rel="stylesheet" type="text/css" />
<!--[if IE]>
<link href="../style/style-ie.css"  rel="stylesheet" type="text/css"/>
<![endif]-->
	<script type="text/javascript" src="../Includes/cdt_eleve.js"></script>
	<script type="text/javascript" src="../Includes/JQ/jquery-1.3.2.min.js"></script>
	<script type="text/javascript" src="../Includes/JQ/cdt-ele-script.js"></script>
	<script type="text/javascript" src="../tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
	<script type="text/javascript" src="../Includes/conf-tiny_mce.js"></script>
        <script type="text/javascript" src="../Includes/sequence.js"></script>
        <script type="text/javascript" src="../Includes/barre_java.js"></script>
        <script type="text/javascript" src="../Includes/cdt.js"></script>
</head>

<body>
<?php
// Connexion � la base de donn�es
require_once ('../Includes/config.inc.php');
// si eleve on recupere le postit
if ($_SESSION['cequi']=="eleve" ) 
	{
	$rq = "SELECT texte FROM postit_eleve WHERE login='{$_SESSION['login']}'  ";
	// lancer la requete
	$result = @mysql_query ($rq) or die (mysql_error()); 
	if (mysql_num_rows($result)>0) 
		{
		$enrg = mysql_fetch_array($result, MYSQL_NUM);
		$contenu_aide_memoire=$enrg[0];
		}
	else
	$contenu_aide_memoire="";
			
echo '
<div id="postit-eleve"> 
<div id="deroul-contenu_ele">
    <div class="deroulant" id="deroulant_1">
    	<div class="t3_ele">Aide m&eacute;moire</div>
        <div><form id="aide-memoire-contenu_ele"  action="" >
        	<div><textarea id="aide-memoire2" name="postitele"  class="MYmceAdvanced2"  rows="11" cols="5"  >';
        	if ($contenu_aide_memoire !="") echo  htmlentities($contenu_aide_memoire);
			else echo htmlentities("Penser &agrave; ...");
			echo '</textarea></div>
			<div><input name="button"  type="button" onclick="go2(\''. $_SESSION['login'].'\',\''.md5($_SESSION['RT'].htmlentities('/Plugins/Cdt/scripts/posti2.php')).'\');"
			value="Enregistrer" title="Enregistrer " /></div>
		</form></div>
	</div><!--fin du div deroulant_1-->
</div><!--fin du div deroul-contenu_ele-->
</div><!--fin du div postit-eleve-->
<div id="switch-postit" class="postup"></div>';

}
echo '<div id="container-cdt-elv">';
$tsmp=time();
//r�cup�ration du param�tre classe pour m�morisation
if (isset($_GET['mlec547trg2s5hy'])) 
	{$ch=$_GET['mlec547trg2s5hy'];} 
//r�cup�ration du param�tre timestamp pour m�morisation
if (isset($_GET['tsmp'])) 
	{$tsmp=$_GET['tsmp'];} 
// si OK a �t� cliqu�
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
				
	// lancer la requ�te
		$result = mysql_query($rq); 
		if (!$result)  // Si l'enregistrement est incorrect
			{                           
			 echo "<p>Votre rubrique n'a pas pu �tre enregistr�e � cause d'une erreur syst�me".
			"<p></p>" . mysql_error() . "<p></p>";
			}
	 $dat_la=date('Y-m-d');
	 $rq = "UPDATE cahiertxt SET on_off='1' WHERE id_auteur='$rub_vis' AND date<'$dat_la' AND datevisibi<='$dat_la'";
	 // lancer la requ�te
		$result = mysql_query($rq); 
		if (!$result)  // 
			{                           
			 echo "<p>L'apposition du visa n'a pu �tre r�alis�e � cause d'une erreur syst�me".
			"<p></p>" . mysql_error() . "<p></p>";
			}					
	}	
	
//on r�cup�re le param�tre de la rubrique (prof/matiere/classe)
if (isset($_GET['rubrique'])) 
	$cible=$_GET['rubrique'];
	else $cible="";
	
if ((isset($_POST['rub_activ'])) &&  (isset($_POST['viser']))) 
	{
	$cible=$_POST['rub_activ'];
	}

//fin traitement formulaire

//Traitement de la barre des menus

// Cr�er la requ�te (R�cup�rer les rubriques de la classe) 
$rq = "SELECT id_prof FROM onglets
 WHERE classe='$ch' ORDER BY 'id_prof' asc ";

 // lancer la requ�te
$result = @mysql_query ($rq) or die (mysql_error());
$nb = mysql_num_rows($result);  // Combien y a-t-il d'enregistrements ?
if ($nb>0)
	{
	//on r�cup�re les donn�es
	$loop=0;
	while ($enrg = mysql_fetch_array($result, MYSQL_NUM)) 
		{
		$num_ero[$loop]=$enrg[0];//num�ro de l'onglet
		$loop++;
		}
	if($cible==""){$cible=($num_ero[0]);}	
	}

//affichage du formulaire
?>
<form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>?nvlkzei5qd1egz65=cv5e8da�rfz8ge69" method="post">
<fieldset id="field7">
<legend id="legende">Cahier de textes &#233;l&#232;ve</legend>
Contenu du cahier de textes de 
<?php
//si on ne  connait pas la classe , on affiche un menu d�roulant  
if (!isset($_SESSION['saclasse']))
	{
	echo "<select name='mlec547trg2s5hy' style='background-color:#E6E6FA'>";
	foreach ($classe as $cle => $valeur)
	  { echo "<option value=\"$valeur\"";
	  if ($valeur==$ch) {echo ' selected="selected"';}
	  echo ">$valeur</option>\n";
	  }
	echo "</select>";
	}
	//sinon on affiche uniquement la classe de l'�l�ve 
	elseif (count($_SESSION['saclasse'])==1) {
            echo '<b>'.$_SESSION['saclasse'][1].'</b>';
        }
	else 
	{
	echo "<select name='mlec547trg2s5hy' style='background-color:#E6E6FA'>";
	foreach ($_SESSION['saclasse'] as $cle => $valeur)
	  { echo "<option value=\"$valeur\"";
	  if ($valeur==$ch) {echo ' selected="selected"';}
	  echo ">$valeur</option>\n";
	  }
	echo "</select>";
	}
  ?>

depuis le :	<?calendrier_auto(-60,'jour_c','mois_c','an_c',$tsmp);?>
<input type="hidden" name="numrub" value= "<?php echo $ch ; ?>" />
<input type="hidden" name="rub_activ" value= "<?php echo $cible; ?>" />
<input type="submit" name="valider" value="" class="bt-valid" />
<input type="button" value="" class="bt-annul" onclick="history.back()" />
<div id="services">
<input type="submit" name="plan" value="" class="bt-plan-dev" />
<?php
//Affichage du bouton pour apposer le visa  
//if (ldap_get_right("Cdt_can_sign",$_SESSION['login'])=="Y")
//	{
//	echo '<input TYPE="SUBMIT" name="viser" value="" class="bt-visa">';
//	}

//Affichage du lien des absences 
if (ldap_get_right("Cdt_is_cpe",$_SESSION['login'])=="Y")
	{
	echo '<a href="#" class="open_wi" onclick="open_new_win(\'cpe.php\')"  id="bt-consult"></a>';
	}

if (( isset($_SESSION['parentde']))  && (!isset($_SESSION['login'])))
	{
	if ($FLAG_ABSENCE==1)	{
	
	foreach ( $_SESSION['parentde'] as $cle => $valcla)
		{
		if ($valcla[2]==$ch) {
		echo '<a href="#" id="bt-abs" title="Absences de '.$valcla[1].'" onclick="abs_popup(\''.$valcla[0].'\',\''.$valcla[1].'\'); return false" ></a>';
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
<input type="button" value="" class="bt-taf" title="Travail A Faire des 15 prochains jours" onclick="taf_popup('<?echo $ch;?>')" />
<a href="http://fusion.google.com/add?source=atgs&amp;feedurl=<?echo 'http://'.$hostn.'/Plugins/Cdt/scripts/flux_rss.php?div='.$ch.':'.$clcryt;?>" id="bt-google">&nbsp; </a>
<a href="flux_rss.php?div=<?echo $ch.':'.$clcryt;?>" id="bt-rss">&nbsp; </a>
<?php if ($_SESSION['cequi']=="eleve") echo '<a href="#" class="open_wi" onclick="open_new_win(\'http://linux.crdp.ac-caen.fr/pluginsLcs/doc_help/aide_eleve.php\')"  ><img src="../images/planifier-cdt-aide.png" alt="Aide" title="Aide" /></a>';?>
</div>
</fieldset>
</form>
<?php
$prof=$mat=$numero=$pref=$restr=$visa=$datvisa=array();

// Cr�er la requ�te (R�cup�rer les rubriques de la classe) 
$rq = "SELECT prof,matiere,id_prof,prefix,visa,visa,DATE_FORMAT(datevisa,'%d/%m/%Y') FROM onglets
 WHERE classe='$ch' ORDER BY 'id_prof' asc ";
 // lancer la requ�te
$result = @mysql_query ($rq) or die (mysql_error());
$nb = mysql_num_rows($result);  // Combien y a-t-il d'enregistrements ? 
if ($nb>0)
	{
	//on r�cup�re les donn�es 
	$loop=0;
	while ($enrg = mysql_fetch_array($result, MYSQL_NUM)) 
		{
		$prof[$loop]=$enrg[0];//nom du prof
		$mat[$loop]=$enrg[1];//mati�re 
		$numero[$loop]=$enrg[2];//num�ro de l'onglet
		$pref[$loop]=$enrg[3];// pr�fixe
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
			//on r�cup�re les donn�es 
			while ($enrg = mysql_fetch_array($result, MYSQL_NUM)) 
				{
				$prof[$loop]=$enrg[0];//nom du prof
				$mat[$loop]=$enrg[1];//mati�re 
				$numero[$loop]=$enrg[2];//num�ro de l'onglet
				$pref[$loop]=$enrg[3];// pr�fixe
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
	
	if (!mb_ereg("^Classe",$ch)) {
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
			//on r�cup�re les donn�es 
				while ($enrg = mysql_fetch_array($result, MYSQL_NUM)) 
					{
					$prof[$loop]=$enrg[0];//nom du prof
					$mat[$loop]=$enrg[1];//mati�re 
					$numero[$loop]=$enrg[2];//num�ro de l'onglet
					$pref[$loop]=$enrg[3];// pr�fixe
					$restr[$loop]=$enrg[4];//restric
					$visa[$loop]=$enrg[5];// 
					$datvisa[$loop]=$enrg[6];
					$loop++;
					}
				}
			}
			
			
		}
	}
$_SESSION['prof']=$prof;
$_SESSION['mat']=$mat;
$_SESSION['numero']=$numero;
$_SESSION['pref']=$pref;
$_SESSION['visa']=$visa;
$_SESSION['datvisa']=$datvisa;
$delta=($loop -7) *40 > 0 ? (($loop -7) *40) :0;
echo '<script type="text/javascript" >
var offset = '.$delta.'
</script>';	

	//modif
	echo '<div id="onglev">';
	if ($delta >0) 
	{
	echo '<div id="switch-ongletsup" ></div>';
	echo '<div id="switch-ongletsdown"></div>';
	}
	echo '<div id="onglev_refresh">';
	// Affichage de la colonne de gauche
	if($cible==""){$cible=($numero[0]);}	
	//cr�ation de la barre de menu , couleur de fond # pour cellule active
	echo '<ul id="navlist-elv">';
	
	for($x=0;$x < $loop;$x++)
		{
		if ($cible == ($numero[$x])) 
		
			{
			echo '<li id="active"><a href="#" title="" onclick="refresh_cdt('. $numero[$x].','.$tsmp.')" id="courant">&nbsp;'.$mat[$x].'&nbsp;<br />&nbsp;'.$pref[$x].'  '.$prof[$x].'&nbsp;</a></li>';
			if ($visa[$x])
				{
				$vis=$visa[$x];
				$datv=$datvisa[$x];
				}
			}
			else 
			{
			echo '<li><a href="#" title="" onclick="refresh_cdt('. $numero[$x].','.$tsmp.')" >&nbsp;'.$mat[$x].'&nbsp;<br />&nbsp;'.$pref[$x].'  '.$prof[$x].'&nbsp;</a></li>';
			}
		}

	echo '</ul>';
	if ($vis) echo '<div id="visa-cdt'.$vis.'e">'.$datv.'</div>';
	echo '</div>';
	echo '</div>';
	}	
	else
		{ 
// Il y a eu un os !
	 	echo '<p class="vide">Aucun enregistrement dans le cahier de textes de '. $ch.' !</p><p></p>';
		}

//affichage du contenu du cahier de textes
//include_once ('../Includes/markdown.php');//convertisseur text-->HTML

	//cr�er la requ�te
	if ($cible!="") 
	{
       //�laboration de la date limite � partir de la date selectionn�e
      $dat=date('Ymd',$tsmp-5184000);//2592000=nbre de secondes dans 30 jours
	$dat_now=date('YmdHis');
        if ($_SESSION['version']==">=432") setlocale(LC_TIME,"french");
	else setlocale("LC_TIME","french");
	echo '<div id="boite5elv">';
        include_once  ('./contenu.php');
if (stripos($_SERVER['HTTP_USER_AGENT'], "msie"))  { include ('../Includes/pied.inc');}
echo "</div>"; //fin du div boite5elv
        }
//if ($vis) echo '<div id="visa-cdt'.$vis.'e">'.$datv.'</div>';
echo '</div>';//fin du div container
//echo '</div>';}
if (!stripos($_SERVER['HTTP_USER_AGENT'], "msie"))  
{include ('../Includes/pied.inc');}
?>
</body>
</html>
