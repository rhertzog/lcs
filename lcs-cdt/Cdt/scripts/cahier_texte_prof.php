<?php
/* =============================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 2.3 du 06/01/2011 
   modif : 20/12/2010
      par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script du cahier de textes PROF -
			_-=-_
    "Valid XHTML 1.0 Strict"
   ============================================= */
session_name("Cdt_Lcs");
@session_start();
include "../Includes/check.php";
if (!check()) exit; 
//si la page est appelee par un utilisateur non identifie
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
include ('../Includes/config.inc.php');

// Creer la requ&egrave;ete.
$rq = "SELECT classe,matiere,id_prof FROM onglets
 WHERE login='{$_SESSION['login']}' ORDER BY classe ASC ";
 
// lancer la requ&egrave;ete
$result = @mysql_query ($rq) or die (mysql_error()); 

// si pas de rubrique, on redirige vers config_ctxt.php
if (mysql_num_rows($result)==0) 
	{
        echo '
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml">
        <head>
        <title>Cahier de textes num&eacute;rique</title>
        <meta http-equiv="content-type" content="text/html;charset=utf-8" />
        <link href="../style/style.css" rel="stylesheet" type="text/css" />
        <script type="text/javascript" src="../Includes/cdt.js"></script>
        </head>
        <body>
                <div id="first">
                <div class="prg">
                <h2>Votre cahier de textes ne comporte actuellement aucune rubrique.</h2>
                <h2>Vous devez personnaliser votre cahier de texte en cr&#233;ant au moins une rubrique.</h2>
                <a href="#" class="open_wi" onclick="open_new_win(\'http://linux.crdp.ac-caen.fr/pluginsLcs/doc_help/raccourcis.php\')"  ><img class="nobord" src="../images/planifier-cdt-aide.png" alt="Aide" title="Aide" /></a>
                <a class="bt-perso" href="config_ctxt.php"></a>
                </div></div>
         </body>
         </html>';
	mysql_close();	
	exit;
	}

include_once("/usr/share/lcs/Plugins/Cdt/Includes/fonctions.inc.php");	
if (get_magic_quotes_gpc()) require_once("../Includes/class.inputfilter_clean.php");
else require_once '../Includes/htmlpur/library/HTMLPurifier.auto.php';
//include_once ('/usr/share/lcs/Plugins/Cdt/Includes/markdown.php'); //convertisseur txt-->HTML
?>

<!-- Fin de la page de premiere utilisation -->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html  xmlns="http://www.w3.org/1999/xhtml" >
<head>
<title>Cahier de textes num&eacute;rique</title>
<meta name="author" content="Philippe LECLERC -TICE CAEN" />
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<link href="../style/style.css" rel="stylesheet" type="text/css" />
	<link  href="../style/deroulant.css" rel="stylesheet" type="text/css" />
	<link  href="../style/navlist-prof.css" rel="stylesheet" type="text/css" />
	<link  href="../style/ui.all.css" rel="stylesheet" type="text/css" />
	<link  href="../style/ui.datepicker.css" rel="stylesheet" type="text/css" />
	<link  href="../style/ui.theme.css" rel="stylesheet" type="text/css" />
<!--[if IE]>
<link href="../style/style-ie.css"  rel="stylesheet" type="text/css"/>
<![endif]-->
	
	<script type="text/javascript" src="../Includes/barre_java.js"></script>
	<script type="text/javascript" src="../Includes/alertsession.js"></script>
	<script type="text/javascript" src="../Includes/JQ/jquery-1.3.2.min.js"></script>
	<script type="text/javascript" src="../Includes/JQ/ui.core.js"></script>  
	<script type="text/javascript" src="../Includes/JQ/ui.datepicker.js"></script> 
	<script type="text/javascript" src="../Includes/JQ/cdt-script.js"></script>
	<script type="text/javascript" src="../tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
	<script type="text/javascript" src="../Includes/conf-tiny_mce.js"></script>
        <script type="text/javascript" src="../Includes/sequence.js"></script>
        <script type="text/javascript" src="../Includes/cdt.js"></script>
	<script type="text/javascript">
        // <![CDATA[
	var duree=<?echo ini_get( 'session.gc_maxlifetime' );?>;
	setTimeout("avertissement()",1);
 	setTimeout("finsession()",(duree + 2) * 1000);
        //]]>
	</script>
        </head>
<body onload="tinyMCE.execCommand('mceFocus',false,'coursfield')">
<?php
$tsmp=time();
$tsmp2=time() + 604800;
$cours="";//variable temporaire de $Cours (avec une majuscule) pour un UPDATE
$afaire="";//variable temporaire de $Afaire (avec une majuscule) pour un UPDATE
$article="";
if (isset ($_POST['sequence']))
    $Seq= $_POST['sequence'] ;
    else $Seq="";


//on recupere le parametre de la rubrique
if (isset($_GET['rubrique']))
	{
	$cible=$_GET['rubrique'];
	} 
elseif (isset($_POST['rubriq']))
	{
	$cible=$_POST['rubriq'];
	}

//rubrique edt si agenda present
elseif (is_dir("../../Agendas"))
	{
	//connexion 
	require_once ("../Includes/connect_agendas.php");
	$aujourdhui=date('Ymd');
	$date=gmdate('His');
	//entrees edt ?
	$rq="SELECT cal_time,cal_duration,cal_name,cal_description FROM webcal_entry WHERE cal_date = '".$aujourdhui
	."' AND cal_id IN 
	(SELECT cal_id FROM webcal_entry_categories WHERE cat_owner='".$_SESSION['login']."' AND cat_id IN 
	(SELECT cat_id FROM webcal_categories WHERE cat_owner='".$_SESSION['login']."' AND cat_name='EDT')) ";
	
	// lancer la requete
	$result = @mysql_query ($rq);
	if ($result)
	 {	
	//on recupere les donnees
        $tab=array();
	$loop=0;
	while ($enrg = mysql_fetch_array($result, MYSQL_NUM))
		{
		//calcul heure de fin
		$s=substr($enrg[0],-2,2);
		$m=substr($enrg[0],-4,2);
		$h=substr($enrg[0],0,-4);
		$heure=mktime($h,$m,$s);
		$heurefin=mktime($h,$m+$enrg[1],$s);
		if ($date > date('His',$heure) && $date < date('His',$heurefin))
			{
			$mati= explode(":",$enrg[3]);
			$mati[1]=preg_replace ( "/[\r\n]+/", "", $mati[1] );
			if ( $enrg[2]!="" && $mati[1]!="")
				{
				$match= $enrg[2].":".$mati[1];
				//recherche du cours suivant
				$rq="SELECT cal_date,cal_id FROM webcal_entry WHERE cal_date > '".$aujourdhui
				."'  AND cal_name='".$enrg[2]."' AND cal_description LIKE '%".$mati[1]."%' AND cal_id IN 
				(SELECT cal_id FROM webcal_entry_categories WHERE cat_owner='".$_SESSION['login']."' AND cat_id IN 
				(SELECT cat_id FROM webcal_categories WHERE cat_owner='".$_SESSION['login']."' AND cat_name='EDT')) 
				ORDER BY cal_date ASC limit 1";
				//echo $rq;exit;	
				$result = @mysql_query ($rq);
				if ($result)
					{	
					$row = mysql_fetch_object($result);
					$tsmp2=mkTime(0,0,1,substr($row->cal_date,2,2),substr($row->cal_date,-2,2),substr($row->cal_date,0,4));
                                        }			
				break;
				}
			}
	   }
	}
	mysql_close();
	// Connexion a la base de donnees Cdt
	include ('../Includes/config.inc.php');
	$rq = "SELECT id_prof FROM onglets
	 WHERE login='{$_SESSION['login']}' AND edt='".$match."'";
	$result = @mysql_query ($rq) or die (mysql_error());
	if (mysql_num_rows($result) >0) {
	while ($idr = mysql_fetch_array($result, MYSQL_NUM)) 
		{
		$cible=$idr[0];
		}
	}
	else $cible="";
	}
else $cible="";

/*================================
   -      Traitement du formulaire  -
   ================================*/
   
  //Suppression d'un commentaire apres confirmation
if (isset($_GET['com'])&& isset($_GET['rubrique']) && $_GET['TA']==md5($_SESSION['RT'].htmlentities($_SERVER['PHP_SELF'])) ) 
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

if ((isset($_POST['enregistrer']) || isset($_POST['modifier'])) && $_POST['TA']==md5($_SESSION['RT'].htmlentities($_SERVER['PHP_SELF'])))
{ 
//require_once '../Includes/htmlpur/library/HTMLPurifier.auto.php';
	// Traiter les donnees

	// Verifier $Cours  
	if (strlen($_POST['Cours']) > 0)
		{ 
		
		if (get_magic_quotes_gpc())
		    {
			$Cours  =htmlentities($_POST['Cours']);
			$oMyFilter = new InputFilter($aAllowedTags, $aAllowedAttr, 0, 0, 1);
			$Cours = $oMyFilter->process($Cours);
			}
		else
			{
			// htlmpurifier
			$Cours = $_POST['Cours'];
			$config = HTMLPurifier_Config::createDefault();
                        $config->set('Core.Encoding', 'ISO-8859-15');
                        $config->set('HTML.Doctype', 'HTML 4.01 Transitional');
	   		$purifier = new HTMLPurifier($config);
	   		$Cours = $purifier->purify($Cours);
	   		$Cours=mysql_real_escape_string($Cours);
	   		}
		
		}
	else
		{ // Si aucun commentaire n'a ete saisi
		$Cours = "";
		}

	// Verifier $Afaire et la debarrasser de tout antislash et tags possibles
	if (strlen($_POST['Afaire']) > 0)
		{ 
		//$Afaire= addSlashes(strip_tags(stripslashes($_POST['Afaire'])));
		if (get_magic_quotes_gpc())
		    {
			$Afaire  = htmlentities($_POST['Afaire']);
			$oMyFilter = new InputFilter($aAllowedTags, $aAllowedAttr, 0, 0, 1);
			$Afaire = $oMyFilter->process($Afaire);
			}
		else
			{
			$Afaire = $_POST['Afaire'];
			$Afaire = $purifier->purify($Afaire);
			$Afaire = mysql_real_escape_string($Afaire);
			}	
		}
	else
		{ // Si aucun commentaire n'a ete saisi
		 $Afaire= "";
		}

	//Traitement de dates
	$Morceauc=explode('/',$_POST['datejavac']);
	$jour_c=$Morceauc[0];
	$mois_c=$Morceauc[1];
	$an_c=$Morceauc[2];
	$date_c = $an_c.$mois_c.$jour_c;
	$Morceauf=explode('/',$_POST['datejaf']);
	$jour_f=$Morceauf[0];
	$mois_f=$Morceauf[1];
	$an_f=$Morceauf[2];
	$date_af = $an_f.$mois_f.$jour_f;

	if  ($_POST['datejav']!="idem Cours")
	{
	$Morceauv=explode('/',$_POST['datejav']);
	$jour_v=$Morceauv[0];
	$mois_v=$Morceauv[1];
	$an_v=$Morceauv[2];
	$date_v = $an_v.$mois_v.$jour_v;
	}
	else 
	$date_v=$date_c;

	// Creer la requete d'ecriture pour l'enregistrement des donnees
	if (isset($_POST['enregistrer']) && ((strlen($Cours) > 1) || (strlen($Afaire) > 1)))
			{
                         $Sequ=($_POST['sequence']);
			$rq = "INSERT INTO cahiertxt (id_auteur,login,date,contenu,afaire,datafaire,datevisibi,seq_id )
                         VALUES ( '$cible','{$_SESSION['login']}', '$date_c', '$Cours', '$Afaire', '$date_af', '$date_v', '$Sequ')";
			}
			
	//Creer la requete pour la mise a  jour des donnees
	if (isset($_POST['modifier']))
			{
			$artic= ($_POST['numart']);
                        $Sequ=($_POST['sequence']);
			$rq = "UPDATE  cahiertxt SET date='$date_c', contenu='$Cours', afaire='$Afaire',datafaire='$date_af',datevisibi='$date_v',seq_id='$Sequ'
			WHERE id_rubrique='$artic'";
			}		
	// lancer la requete
	    $result = mysql_query($rq); 
	    if (!$result)  // Si l'enregistrement est incorrect
		    {                           
		     echo "Votre commentaire n'a pas pu \352tre enregistr\351 \340 cause d'une erreur syst\350me"."\n\n" . mysql_error() ;
                    mysql_close();     // refermer la connexion avec la base de donnees
                     exit();
                    }
	 
	//diffusion des commentaires vers les classes de meme niveau
	if ((isset($_SESSION['grp']) ) && (isset($_SESSION['dif_c']) ) && (isset($_SESSION['dif_af']) ) && (isset($_SESSION['dif_vi']) ) 
	 && ((isset($_POST['enregistrer'] ) || isset($_POST['modifier'])) && ((strlen($Cours) > 1) || (strlen($Afaire) > 1))))
		{
		for ($loop=0; $loop < count ($_SESSION['grp'])  ; $loop++)
					{
					$cibledif=$_SESSION['grp'][$loop];
                                        $seqdif=$_SESSION['dif_sq'][$loop];
					$Morceauc_dif=explode('/',$_SESSION['dif_c'][$loop]);
					$jour_c_dif=$Morceauc_dif[0];
					$mois_c_dif=$Morceauc_dif[1];
					$an_c_dif=$Morceauc_dif[2];
					$date_c_dif = $an_c_dif.$mois_c_dif.$jour_c_dif;
					$Morceauf_dif=explode('/',$_SESSION['dif_af'][$loop]);
					$jour_f_dif=$Morceauf_dif[0];
					$mois_f_dif=$Morceauf_dif[1];
					$an_f_dif=$Morceauf_dif[2];
					$date_af_dif = $an_f_dif.$mois_f_dif.$jour_f_dif; 
					if 	($_SESSION['dif_vi'][$loop]!="idem Cours")
					{
					$Morceauv_dif=explode('/',$_SESSION['dif_vi'][$loop]);
					$jour_v_dif=$Morceauv_dif[0];
					$mois_v_dif=$Morceauv_dif[1];
					$an_v_dif=$Morceauv_dif[2];
					$date_v_dif = $an_v_dif.$mois_v_dif.$jour_v_dif;
					}
					else 
					$date_v_dif=$date_c_dif;
					
					$rq = "INSERT INTO cahiertxt (id_auteur,login,date,contenu,afaire,datafaire,datevisibi,seq_id )
					VALUES ( '$cibledif','{$_SESSION['login']}', '$date_c_dif', '$Cours', '$Afaire', '$date_af_dif','$date_v_dif',$seqdif )";
					// lancer la requete
					$result = mysql_query($rq); 
					if (!$result)  // Si l'enregistrement est incorrect
						{                           
						 echo "Votre commentaire n'a pas pu \352tre enregistr\351 \340 cause d'une erreur syst\350me"."\n\n" . mysql_error() ;
                                                mysql_close();     // refermer la connexion avec la base de donnees
						exit();
						}
					}
		//desruction des variables de session			
		unset ($_SESSION['grp']);
		unset ($_SESSION['dif_c']);
		unset ($_SESSION['dif_af']);
                unset ($_SESSION['dif_sq']);
		}
	}

//fin traitement formulaire

/*
   ================================
   - Traitement de la barre des menus  -
   ================================
*/

// Creer la requete (Recuperer les rubriques de l'utilisateur) 
$rq = "SELECT classe,matiere,id_prof,postit,visa,DATE_FORMAT(datevisa,'%d/%m/%Y') FROM onglets
 WHERE login='{$_SESSION['login']}' ORDER BY id_prof ASC ";

 // lancer la requ&egrave;ete
$result = @mysql_query ($rq) or die (mysql_error());
$nb = mysql_num_rows($result);  // Combien y a-t-il d'enregistrements ?

//on recupere les donnees
$loop=0;
while ($enrg = mysql_fetch_array($result, MYSQL_NUM)) 
	{
        $clas[$loop]=$enrg[0];
	$mat[$loop]=$enrg[1];
	$numero[$loop]=$enrg[2];
	$com[$loop]=$enrg[3];
	$visa[$loop]=$enrg[4];// 
	$datvisa[$loop]=$enrg[5];
	$loop++;
	}
	
//on calcule la largeur des onglets
if($cible==""){$cible=($numero[0]);}
$nmax=$nb;
 
//cr&eacute;ation de la barre de menu 
echo '<div id="entete">';

echo '<div id="navcontainer">';
echo '<div id="switch-barreLcs" class="swup"></div>';
echo ("<ul id='navlist'>");
for($x=0;$x < $nmax;$x++)
	{
        if ($cible == ($numero[$x]))
                {//cellule active
                echo '<li id="select"><a href="cahier_texte_prof.php?rubrique='.$numero[$x].'"
                onmouseover="window.status=\'\';return true" id="courant">'.htmlentities($mat[$x]).'<br />'.$clas[$x].' </a></li>';
                $contenu_postit = stripslashes($com[$x]);
                if ($visa[$x])
                        {
                        $vis=$visa[$x];
                        $datv=$datvisa[$x];
                        }

                }
        else
                {
                if (!isset($mat[$x])) $mat[$x]="&nbsp;";
                if (!isset($clas[$x])) $clas[$x]="&nbsp;";
                if (!isset($numero[$x]))
                echo '<li><a href="#">$clas[$x]'.'</a></li>';
                else
                {
                echo '<li><a href="cahier_texte_prof.php?rubrique='.$numero[$x].'" onmouseover="window.status=\'\';return true">'.htmlentities($mat[$x]).'<br />'.$clas[$x].'</a></li>';
                }
                }
	}
echo '</ul>';
echo '</div>';
echo '</div>';


//Affichage d'une boite de confirmation d'effacement
if (isset($_POST["suppr"]))
	{
	echo "<script type='text/javascript'>";
	echo "if (confirm('Confirmer la suppression de ce commentaire')){";
	echo ' location.href = "';echo $_SERVER['PHP_SELF'];
	echo'" + "?com=" +'." '"; echo $_POST["number"] ;
	echo "'".' + "&rubrique=" +'." '";echo $_POST['rubriq']."'";
	echo ' + "&TA=" +'." '". md5($_SESSION['RT'].htmlentities($_SERVER['PHP_SELF']))."'".' ;}</script>';
	}
	

// 3. Modification d'un article
if (isset($_POST['modif'])&& isset($_POST['number'])) 
	{
	$action='erg45er5ze';
	$article=$_POST['number'];
	
//l'article existe il ?
	$rq = "SELECT DATE_FORMAT(date,'%d'),DATE_FORMAT(date,'%m'),DATE_FORMAT(date,'%Y'),contenu,afaire,
	DATE_FORMAT(datafaire,'%d'),DATE_FORMAT(datafaire,'%m'),DATE_FORMAT(datafaire,'%Y'),
	DATE_FORMAT(datevisibi,'%d'),DATE_FORMAT(datevisibi,'%m'),DATE_FORMAT(datevisibi,'%Y'),seq_id FROM cahiertxt
	WHERE id_rubrique=$article ";
 // lancer la requete
	$result = @mysql_query ($rq) or die (mysql_error());
	$nb = mysql_num_rows($result);
	//s'il existe, on recupere les datas pour les afficher dans les champs
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
		//calcul du timestamp correspondant a la date de vsisbilite
		$tsmp3=mkTime(0,0,1,$val_moisv,$val_jourv,$val_anneev);
		$Seq=$enrg[11];
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
	
//affichage visa	
 if ($vis) echo '<div id="visa-cdt'.$vis.'">'.$datv.'</div>';

?>
<!--bloc qui contient la saisie et le contenu du cahier de texte-->
<div id="container">

<!--bloc en haut de page qui contient la saisie et le menu deroulant a droite-->
<div id="secondaire">

<!--bloc de gauche pour la saisie-->
<div id="saisie">
	<form id="form-saisie" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>?rubrique=<?echo $cible;?>" method="post">
            <div><input name="TA" type="hidden"  value="<?php echo md5($_SESSION['RT'].htmlentities($_SERVER['PHP_SELF'])); ?>" /></div>
	<div id="boite3">
		<div id="boite1">  
			<div id="crsdu">Cours  du :
 				<input id="datejavac" size="10" name="datejavac" value="<?echo $dtajac?>" readonly="readonly" style="cursor: text" />
 				Visible le :
 				<input id="datejavav" size="10" name="datejav" value="<?echo $dtajav?>" readonly="readonly" style="cursor: text" title="Par d&#233;faut, le commentaire sera visible a partir de la date du cours" />
 				S&#233;quence 
 <?php
 	$rq = "SELECT id_seq,titrecourt FROM sequences	WHERE id_ong='$cible' order by ordre ASC";
	// lancer la requete
	$result = @mysql_query ($rq) or die (mysql_error());
	// Combien y a-t-il d'enregistrements ?
	$nb = mysql_num_rows($result);
	//on recupere les donnees
        echo '<select name="sequence" style="background-color:#E6E6FA" >';
        echo '<option value="0">-----------------</option>';
        //foreach ($TitreCourSeq as $cle => $valeur)
        if ($nb>0)
        {
           while ($row = mysql_fetch_object($result))
          {
            echo "<option value=\"$row->id_seq\"";
            if ($row->id_seq==$Seq) {echo ' selected';}
            echo ">$row->titrecourt</option>\n";
           }
         }
        echo "</select>";
?>
 	</div>
			<textarea id="coursfield"  name="Cours"  class="mceAdvanced" cols="1" rows="1" style="background-color:#F0F0FA"><?echo $cours;?> </textarea>
			<input type="hidden" name="numart" value= "<?php echo $article ; ?>" /> <br />
                        </div><!--fin du div boite 1-->
  
                        <div id="boite2">
			<div id="afRle">A faire pour le :
				<input id="datejaf" size="10" name="datejaf" value="<?echo $dtajaf?>" readonly="readonly" style="cursor: text" />
			</div>
			<textarea id="afairefield"  name="Afaire" class="mceAdvanced" cols="1" rows="1"  style="background-color:#F0F0FA"><?echo $afaire;?></textarea><br />
                        </div><!--fin du div boite 2-->
		
	</div><!--fin de la boite 3-->
	<div id="boite4">
		<?php  if (isset($_POST['modif'])&& isset($_POST['number'])) {
			echo ('<input name="rubrique" type="hidden" id="rubrique" value="'.$cible.'" />
				<div id="bouton">
				<input type="submit" title="Enregistrer les modifications" name="modifier" value="" class="submit-modif" />
                                <li><a href="#" title="Enregistrements multiples" onclick="diffuse_popup('.$cible.'); return false" class="a-saveplus"></a></li>
				<input name="date" type="hidden" id="date" value="'.$date.'"/>
				</div>');
                                }
		
			else {
                                echo '<input name="rubrique" type="hidden" id="rubrique" value="'.$cible.'"/>
				<input name="date" type="hidden" id="date" value="'.$date.'" />
				<div id="boutons">
				<ul>
				<li><input class="submit-perso" type="submit" name="personnaliser" value="" title="Personnaliser son cahier de texte" /></li>
				<li><a href="#" title="Gestion des s&#233;quences" onclick="sequence_popup('.$cible.'); return false" class="submit-seq"></a></li>
				<li><input type="submit" name="planning" value="" title="Planifier un devoir en '.$classe_active.'" class="submit-plan" /></li>
				<li><a class="a-imprime open_wi" title="Imprimer" href="#" onclick="open_new_win(\'imprim.php?rubrique='.$cible.'\')"></a></li>
				<li><input type="button" value="" onclick="modeleLoad('. $cible.',\''.md5($_SESSION['RT'].htmlentities(htmlentities('/Plugins/Cdt/scripts/load_modele.php'))).'\')" class="load-model" title="Appliquer le mod&#232;le" /></li>
				<li><input type="button" value="" onclick="modeleSave('. $cible.',\''.md5($_SESSION['RT'].htmlentities(htmlentities('/Plugins/Cdt/scripts/save_modele.php'))).'\')" class="save-model" title="Enregistrer comme mod&#232;le" /></li>
				<li><a href="#" title="Enregistrements multiples" onclick="diffuse_popup('.$cible.'); return false" class="a-saveplus"></a></li>
				<li><br/><br/></li>
				<li><input type="submit" title="Enregistrer" name="enregistrer" value="" class="submit-save" /></li>
				</ul>
				</div>';
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
        <li><a href="#deroulant_1"><img src="../images/memory-cdt2.png" alt="Post-it" title="Post-it" /></a></li>
        <li><a href="#deroulant_2"><img src="../images/archiver-cdt2.png" alt="Archives" title="Archives" /></a></li>
        <li><a href="#deroulant_3"><img src="../images/internet-cdt2.png" alt="Liens" title="Liens" /></a></li>
        <li><a href="#deroulant_4"><img src="../images/mail-cdt2.png" alt="Mail" title="Mail" /></a></li>
        <li><a href="#deroulant_5"><img src="../images/aide-cdt2.png" alt="Aide" title="Aide" /></a></li>
    </ul>
</div><!--fin du div deroul-menu-->
<div id="deroul-contenu">
    <div class="deroulant" id="deroulant_1">
    	<div class="t3">Aide m&eacute;moire</div>
        <div> <form id="aide-memoire-contenu" action="">
                <div><textarea id="aide-memoire" name="monpostit"  class="MYmceAdvanced" style="width:100%" rows="17" cols="22" >
        	<?php
                if ($contenu_postit !="") echo htmlentities($contenu_postit);
		else echo htmlentities("Penser &agrave; ...");?></textarea></div>
                <div><input name="button"  id="bt-erg" type="button" onclick="go(<?php echo $cible.",'".md5($_SESSION['RT'].htmlentities('/Plugins/Cdt/scripts/posti1.php'))."'"; ?>);" value="" title="Enregistrer le post-it" /></div>
		</form></div>
	</div><!--fin du div deroulant_1-->

    <div class="deroulant" id="deroulant_2">
    	<div class="t3">Archives</div>
      		<?	/* Affichage des archives */ 
				echo (' <hr /><p class="archive">Vos anciens cahiers de textes :</p> ');
 				//recherche du  nom des archives
				$TablesExist= mysql_query("show tables");
				$x=0;
				while ($table=mysql_fetch_row($TablesExist))
				if (mb_ereg("^onglets[[:alnum:]]",$table[0])) {
					$archive=explode('s',$table[0]);
					$x++;
					$archnum=$archive[1];
					echo '<a href="#" onclick="arch_popup(\''.$archive[1].'\'); return false" >- '.$archive[1].' </a> <br /> ';
	
				}
				//s'il n'esiste pas d'archive
				if ($x==0) echo '<p class="archive_nok"> Aucun </p>';
				else echo '<hr /><p class="archive">Anciens cahiers de textes &#233;l&#232;ves</p>
				<a href="#" class="open_wi" onclick="open_new_win(\'cahier_text_eleve_arch.php\')"> -  Ils sont ici </a>';
				echo '<hr />';
				mysql_close();
				//archives perso
				echo ' <p class="archive"> Cahiers de textes import&#233;s :</p> ';
 				//recherche du  nom des archives
 				include_once "/var/www/lcs/includes/headerauth.inc.php";
 				list ($idpers,$log) = isauth();
  				if ($idpers)  $_LCSkey = urldecode( xoft_decode($HTTP_COOKIE_VARS['LCSuser'],$key_priv) );
 				$nom_bdd=mb_ereg_replace("\.","",$_SESSION['login']);
 				DEFINE ('DBP_USER', $_SESSION['login']);
				DEFINE ('DBP_PASSWORD', $_LCSkey);
				DEFINE ('DBP_HOST', 'localhost');
				DEFINE ('DBP_NAME', $nom_bdd.'_db');

				// Ouvrir la connexion et selectionner la base de donnees
				$dbcp = @mysql_connect (DBP_HOST, DBP_USER, DBP_PASSWORD); 
				//OR die ('Connexion a MySQL impossible : '.mysql_error().'<br />');
				if ($dbcp) {
				$db_selected = mysql_select_db (DBP_NAME);
           			//OR die ('Selection de la base de donnees impossible : '.mysql_error().'<br />');
				if ($db_selected) {
				$TablesExist= mysql_query("show tables");
				$x=0;
				while ($table=mysql_fetch_row($TablesExist))
				if (mb_ereg("^onglets",$table[0])) {
					$archive=explode('s',$table[0]);
					$x++;
					if ($archive[1]!="") $archnum=$archive[1]; else $archnum="An dernier";
					echo '<a href="#" onclick="arch_perso_popup(\''.$archive[1].'\'); return false" >- '.$archnum.' </a> <br />  ';
	
				}
				//s'il n'esiste pas d'archive
				if ($x==0) echo '<p class="archive_nok"> Aucun </p>';
				}
				else { echo 'Erreur : '.mysql_error();}
				}
				else { echo 'Erreur : '.mysql_error();}
				echo '<hr />';
				mysql_close();
				
			?>
			</div><!--fin du div deroulant_2-->
	
    <div class="deroulant" id="deroulant_3">
    	<div class="t3">Liens</div>
       		<p>Ces liens s'ouvrent dans une nouvelle fen&ecirc;tre</p>
			 <?php if (!mb_ereg("^Cours",$classe_active)) 
                                {
                                echo '<p><a href="# " class="open_wi" onclick="open_new_win(\'cahier_text_eleve.php?mlec547trg2s5hy='.$classe_active.'\')"> - CAHIER DE TEXTE ELEVES	</a></p>';
                                echo '<p><a href="#" class="open_wi"  onclick="open_new_win(\'absences.php?mlec547trg2s5hy='.$classe_active. ' \')" > - CARNET D\'ABSENCES - </a></p>';
                                 }
                                else
                                {
                                 echo '<p><a href="#" class="open_wi" onclick="open_new_win(\'cahier_text_eleve.php\')" > - CAHIER DE TEXTE ELEVES	</a></p> ';
                                 echo '<p><a href="#"class="open_wi"  onclick="open_new_win(\'absences.php\')" > - CARNET D\'ABSENCES - </a></p>';
                                }
			
                            echo '<p><a href="#" class="open_wi" onclick="open_new_win(\'export_perso.php\')" > - EXPORT DE SES DONNEES - </a></p>';
                            echo '<p><a href="#" class="open_wi" onclick="open_new_win(\'import_perso.php\')" > - IMPORT DE DONNEES - </a></p>';
                        ?>
        </div><!--fin du div deroulant_3-->
     
     <div class="deroulant" id="deroulant_4">
     	<div class="t3">Contacts</div>
     		<p>Pour toutes remarques ou suggestions:</p>
     			<p>. <a href="mailto:philippe.leclerc1@ac-caen.fr"><b> Philippe LECLERC </b><i>Misterphi</i></a></p>
     			<p>. <a href="mailto:yannick.chistel@crdp.ac-caen.fr"><b> Yannick CHISTEL </b></a></p>
     			<p>. <a href="mailto:lcs-devel@listes.tice.ac-caen.fr"><b> Equipe-TICE-CRDP-CAEN</b></a></p>
     			
     	</div><!--fin du div deroulant_4-->
     
      <div class="deroulant" id="deroulant_5">
     	<div class="t3">Aide</div>
     		<p><a href="#" class="open_wi"   onclick="open_new_win('http://linux.crdp.ac-caen.fr/pluginsLcs/doc_help/raccourcis.php')"> Aide pour bien utiliser le cahier de textes num&#233;rique</a></p>
     </div><!--fin du div deroulant_5-->

</div><!--fin du div deroul-contenu-->
</div><!--fin du div deroulants-->
</div><!--fin du div deroul-bloc--> 

</div>	<!--fin du div secondaire-->

<?php

/*========================================================================
   - Affichage du contenu du cahier de textes  -
   =======================================================================*/

//include_once ('../Includes/markdown.php'); //convertisseur txt-->HTML

if ($_SESSION['version'] == ">=432") setlocale(LC_TIME,"french"); else setlocale("LC_TIME","french");
echo '<div id="boite5">';
include_once  ('./contenu.php');
echo "</div>";  //fin du div de la boite 5 : contenu du cahier de texte
?>
</div>
<div id="musique"></div>
<?php
include ('../Includes/pied.inc');
?>
</body>
</html>
