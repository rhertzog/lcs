<?php
/* ==================================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 2.3 du 06/01/2011
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script de carnet absence-
			_-=-_
   "Valid XHTML 1.0 Strict"
   =================================================== */
   
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Expires: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache"); 
session_name("Cdt_Lcs");
@session_start();
include "../Includes/check.php";
if (!check()) exit; 
//si la page est appelée par un utilisateur non identifié
if (!isset($_SESSION['login']) )exit;

//si la page est appelée par un utilisateur non prof
elseif ($_SESSION['cequi']!="prof") exit;

if (isset($_POST['Fermer'])) 
echo "<script  language='Javascript'>
					<!--
					window.close()
					// -->
					</script>";
							
include "../Includes/functions2.inc.php";
if (get_magic_quotes_gpc()) require_once("../Includes/class.inputfilter_clean.php");
else require_once '../Includes/htmlpur/library/HTMLPurifier.auto.php';
$tsmp=time();
$tsmp2=time() + 604800;//j+7
$cren_off=array();

//fichiers nécessaires à l'exploitation de l'API
$BASEDIR="/var/www";
//include "../Includes/basedir.inc.php";
include "$BASEDIR/lcs/includes/headerauth.inc.php";
include "$BASEDIR/Annu/includes/ldap.inc.php";
include "$BASEDIR/Annu/includes/ihm.inc.php";  

// Connexion à la base de données
require_once ('../Includes/config.inc.php');

function is_present($kan,$logprof,$logelev) {
	$Sql= "SELECT id_abs FROM absences WHERE  uidprof='$logprof' AND  uideleve='$logelev' AND date='$kan'";
	$res = @mysql_query ($Sql) or die (mysql_error());
	$tst=mysql_fetch_array($res, MYSQL_NUM);
	if (mysql_num_rows($res)>0) return $tst[0] ;	

}

// Recherche des classes du prof
$rq = "SELECT classe,id_prof FROM onglets
 WHERE login='{$_SESSION['login']}'  OR  cologin='{$_SESSION['login']}' GROUP BY classe ORDER BY classe ASC ";
 
// lancer la requête
$result = @mysql_query ($rq) or die (mysql_error()); 
$loop=0;
while ($row = mysql_fetch_object($result))
	{
   $data1[$loop]=$row->classe;
	$data2[$loop]=$row->id_prof;
	$loop++;
	}
	
//récupération du paramètre classe pour mémorisation
if (isset($_GET['mlec547trg2s5hy'])) 
	{
	$ch=$_GET['mlec547trg2s5hy'];
	} 
if (isset($_POST['mlec547trg2s5hy'])) 
	{
	$ch=$_POST['mlec547trg2s5hy'];
	}

//Recherche des élèves de la classe
	 
$filtre="cn=".$ch;	
$grp_cl=search_groups($filtre);
if (count($grp_cl[0])==0) $grp_cl=search_groups("cn=Classe_*".$ch); 
$uids = search_uids ("(cn=".$grp_cl[0]["cn"].")", "half");
$users = search_people_groups ($uids,"(sn=*)","cat");

//date  au jour courant
if (!isset($_POST['datejavac_dif'])) 
	{	
	$datjc_dif=getdate($tsmp);
	$jo_dif=date('d',$datjc_dif['0']);
	$mo_dif=date('m',$datjc_dif['0']);
	$ann_dif=date('Y',$datjc_dif['0']);
	$dtajac_dif=$jo_dif."/".$mo_dif."/".$ann_dif;
	$dtajac= $ann_dif."/".$mo_dif."/".$jo_dif;
	}
	else
//date a la valeur selectionnee	
	{
	$Morceauc=explode('/',$_POST['datejavac_dif']);
	$jour_c=$Morceauc[0];
	$mois_c=$Morceauc[1];
	$an_c=$Morceauc[2];
	$dtajac= $an_c."/".$mois_c."/".$jour_c;
	$dtajac_dif=$jour_c."/".$mois_c."/".$an_c;
	}

//creation tableaux "creneaux existants" et "creneaux nouveaux"
//$cren_y contient le nom des creneaux existants (Mi, Si)
//$cren_n  "        "          "       nouveaux

if (isset($_POST['cren'])) 
	{$y=0;$n=0;
	for ($i = 0; $i < count($_POST['cren']); $i++) 
		{
		$tab_cren=$_POST['cren'];	
		$cr=$_POST['cren'][$i];
		$rq= "SELECT count(*) FROM absences WHERE uidprof='{$_SESSION['login']}'   AND classe='$ch' AND date ='$dtajac' AND $cr!='' ";
		$result = @mysql_query ($rq) or die (mysql_error()); 
		$nb = mysql_fetch_array($result, MYSQL_NUM); 
 		if ($nb[0]!=0) 
 		{
 		$cren_y[$y]=$cr;$y++;// creneau existant (des donnees existent pour ce creneau) 
 		} else 
 		$cren_n[$n]=$cr;$n++; //creneau nouveau
 		}
 	}
	else
	$tab_cren=array();

if (isset($_POST['Valider'])) 
	{
	$crd="";	
	//tableau des absences et retards nouveaux creneaux (checkbox) 
	if (!isset($_POST['abs_x'])) $tab_absx=array(); else $tab_absx=$_POST['abs_x'];
	if (!isset($_POST['ret_x'])) $tab_retx=array(); else $tab_retx=$_POST['ret_x'];
	
	//tableau des absences et retards creneaux existants donc a updater
 	if (count($cren_y)>0)
 		{
 		foreach ( $cren_y as $clé => $valeur)
			{
			$taba="tab_abs".$valeur;
			$tabr="tab_ret".$valeur;
			if (!isset($_POST['abs_'.$valeur.''])) $$taba=array(); else $$taba=$_POST['abs_'.$valeur.''];
			if (!isset($_POST['ret_'.$valeur.''])) $$tabr=array(); else $$tabr=$_POST['ret_'.$valeur.''];
			}
		}	

	//traitement pour chaque membre de la classe 
		for ($loop=0; $loop<count($users);$loop++) 
	 	{
	 	//traitement des nouveaux crenaux
	 	$uidpot= $users[$loop]["uid"];	 	
	 	
	 	if (in_array($uidpot, $tab_absx)) 
	 		{
			$typ="A";
			if (get_magic_quotes_gpc())
			    {
				$oMyFilter = new InputFilter($aAllowedTags, $aAllowedAttr, 0, 0, 1);
				$mot_x = $oMyFilter->process(utf8_decode($_POST['motif_x'][$uidpot]));
				}
			else
				{
				// htlmpurifier
				$mot_x = $_POST['motif_x'][$uidpot];
				$config = HTMLPurifier_Config::createDefault();
                                                                        $config->set('Core.Encoding', 'ISO-8859-15');
                                                                        $config->set('HTML.Doctype', 'HTML 4.01 Transitional');
		   		$purifier = new HTMLPurifier($config);
		   		$mot_x = $purifier->purify($mot_x);
		   		$mot_x=mysql_real_escape_string($mot_x);
		   		}
			}
		elseif (in_array($uidpot, $tab_retx)) 
	 		{
			$typ ="R";
			if (get_magic_quotes_gpc())
			    {
				$oMyFilter = new InputFilter($aAllowedTags, $aAllowedAttr, 0, 0, 1);
				$mot_x = $oMyFilter->process(utf8_decode($_POST['motif_x'][$uidpot]));
				}
			else
				{
				// htlmpurifier
				$mot_x = $_POST['motif_x'][$uidpot];
				$config = HTMLPurifier_Config::createDefault();
                                $config->set('Core.Encoding', 'ISO-8859-15');
                                $config->set('HTML.Doctype', 'HTML 4.01 Transitional');
		   		$purifier = new HTMLPurifier($config);
		   		$mot_x = $purifier->purify($mot_x);
		   		$mot_x=mysql_real_escape_string($mot_x);
		   		}
			
			}
		else 	
			{
			$typ="";
			$mot_x="";
			}
			//enregistrement eventuel dans la bdd 
			if ($typ!="")
				{
				if (count($cren_n)>0)
 					{
				 foreach ( $cren_n as $clé => $valeur)
			 		{
			 		$id_absence=is_present($dtajac,$_SESSION['login'],$uidpot);
					if (($id_absence != "")) 
						{
						$rq6 = "UPDATE  absences SET  ".$valeur."='$typ' , motif".$valeur."='$mot_x'  WHERE id_abs='$id_absence' ";
						$result6  =  mysql_query($rq6); 
						if (!$result)  {                           
		     				echo "<p>Votre commentaire n'a pas pu &#234;tre enregistré &#224; cause d'une erreur syst&#232;me". "<p></p>" . mysql_error() . "<p></p>";
			 				mysql_close();     // refermer la connexion avec la base de données
							exit();
	    					}
	    					else $crd="Okey";
	    				}
	    		else
	    			{	
					$rq6 ="INSERT INTO absences (date,uidprof, uideleve,classe,".$valeur.",motif".$valeur." ) 
					VALUES ( '$dtajac','{$_SESSION['login']}', '$uidpot', '$ch', '$typ', '$mot_x')";
					$result6  =  mysql_query($rq6);
					if (!$result)  // Si l'enregistrement est incorrect
	        			{                           
		     			echo "<p>Votre commentaire n'a pas pu &#234;tre enregistré &#224; cause d'une erreur syst&#232;me"."<p></p>" . mysql_error() . "<p></p>";
			 			mysql_close();     // refermer la connexion avec la base de données
				 		exit(); 
	    				}
	    					else $crd="Okey";
	    			}//fin inser
					}
				}
				}
			//fin des traitement des nouveaux creneaux pour un eleve
			
			//traitement des creneaux existants pour cet eleve
	if (count($cren_y)>0)
 		{	
			foreach ( $cren_y as $clé => $valeur)
		{
		$taba="tab_abs".$valeur;
		$tabr="tab_ret".$valeur;
		
		if (in_array($uidpot, $$taba)) 
	 			{
				$type="A";
				
				if (get_magic_quotes_gpc())
			    {
				$oMyFilter = new InputFilter($aAllowedTags, $aAllowedAttr, 0, 0, 1);
				$motiph = $oMyFilter->process(utf8_decode($_POST['motif_'.$valeur.''][$uidpot]));
				}
			else
				{
				// htlmpurifier
				$motiph = $_POST['motif_'.$valeur.''][$uidpot];
				$config = HTMLPurifier_Config::createDefault();
		    	$config->set('Core.Encoding', 'ISO-8859-15'); 
		    	$config->set('HTML.Doctype', 'HTML 4.01 Transitional');
		   		$purifier = new HTMLPurifier($config);
		   		$motiph = $purifier->purify($motiph);
		   		$motiph=mysql_real_escape_string($motiph);
		   		}
				
				}
		elseif (in_array($uidpot, $$tabr)) 
	 		{
			$type="R";
				if (get_magic_quotes_gpc())
				    {
					$oMyFilter = new InputFilter($aAllowedTags, $aAllowedAttr, 0, 0, 1);
					$motiph = $oMyFilter->process(utf8_decode($_POST['motif_'.$valeur.''][$uidpot]));
					}
				else
					{
					// htlmpurifier
					$motiph = $_POST['motif_'.$valeur.''][$uidpot];
					$config = HTMLPurifier_Config::createDefault();
			    	$config->set('Core.Encoding', 'ISO-8859-15'); 
			    	$config->set('HTML.Doctype', 'HTML 4.01 Transitional');
			   		$purifier = new HTMLPurifier($config);
			   		$motiph = $purifier->purify($motiph);
			   		$motiph=mysql_real_escape_string($motiph);
			   		}
				}
		else 	
				{
				$type="";
				$motiph="";
				} 
		
		
				$id_absence=is_present($dtajac,$_SESSION['login'],$uidpot);
				if (($id_absence != "")) 
						{
						$rq6 = "UPDATE  absences SET  ".$valeur."='$type' , motif".$valeur."='$motiph'  WHERE id_abs='$id_absence' ";
						$result6  =  mysql_query($rq6); 
						if (!$result) 
							{                           
		     				echo "<p>Votre commentaire n'a pas pu &#234;tre enregistré &#224; cause d'une erreur syst&#232;me". "<p></p>" . mysql_error() . "<p></p>";
			 				mysql_close();     // refermer la connexion avec la base de données
							exit();
	    					}
	    					else $crd="Okey";
	    				}
	    				
	    		elseif ($type!="")
	    			{
	    			$rq6 ="INSERT INTO absences (date,uidprof, uideleve,classe,".$valeur.",motif".$valeur." ) 
					VALUES ( '$dtajac','{$_SESSION['login']}', '$uidpot', '$ch', '$type', '$motiph')";
					$result6  =  mysql_query($rq6);
					if (!$result)  // Si l'enregistrement est incorrect
	        			{                           
		     			echo "<p>Votre commentaire n'a pas pu &#234;tre enregistré &#224; cause d'une erreur syst&#232;me"."<p></p>" . mysql_error() . "<p></p>";
			 			mysql_close();     // refermer la connexion avec la base de données
				 		exit(); 
	    				}
	    					else $crd="Okey";
					}
				}//fin foreach 
			}
	//fin traitement des creneaux existants pour un eleve
		
			
	//suppression de l'enregistrement si aucun retard	et aucune absence (suite à une modification)		
			
				$rq61="SELECT * FROM absences WHERE id_abs='$id_absence' ";
				$result61=  mysql_query($rq61);
				while ($row = mysql_fetch_object($result61))
					{
   				$m1=$row->M1;$m2=$row->M2;$m3=$row->M3;$m4=$row->M4;$m5=$row->M5;
   				$s1=$row->S1;$s2=$row->S2;$s3=$row->S3;$s4=$row->S4;$s5=$row->S5;					
					}
			
			if ($m1=="" && $m2=="" && $m3=="" && $m4=="" && $m5=="" && $s1=="" && $s2=="" && $s3=="" && $s4=="" && $s5=="" )
				{
				$rq7 = "DELETE  FROM absences WHERE id_abs='$id_absence'  LIMIT 1";
				$result7 = @mysql_query ($rq7) or die (mysql_error());
				
	       	if (!$result7)  // Si l'enregistrement est incorrect
	        		{                           
		     		echo "<p>Votre commentaire n'a pas pu &#234;tre enregistr&#233; &#224; cause d'une erreur syst&#232;me"."<p></p>" . mysql_error() . "<p></p>";
			 		mysql_close();     // refermer la connexion avec la base de données
				 	exit(); 
	    			}
	    			else $crd="Okey"; 
				}	
				
			}
			//fin traitement des eleves de la classe
			
	// traitement "touspresents"
	// pour chaque nouveau creneau enregistrer touspresents
	if (count($cren_n)>0)
	 		{
	 		foreach ( $cren_n as $clé => $valeur)
				{
				$rq= "SELECT count(*) FROM absences WHERE classe='$ch' AND date ='$dtajac' AND $valeur!='' ";
				$result = @mysql_query ($rq) or die (mysql_error()); 
				$nb_enrg = mysql_fetch_array($result, MYSQL_NUM);
				//si tous présents 
		 		if ($nb_enrg[0]==0) 
			 		{
			 		$rqq ="INSERT INTO absences (date,uidprof, uideleve,classe,".$valeur." ) VALUES ( '$dtajac','{$_SESSION['login']}', 'touspresents', '$ch', '-')";
					$resultt =  mysql_query($rqq);
					if (!$resultt)  // Si l'enregistrement est incorrect
			        	{                           
				    	echo "<p>Votre commentaire n'a pas pu &#234;tre enregistr&#233; &#224; cause d'une erreur syst&#232;me"."<p></p>" . mysql_error() . "<p></p>";
						mysql_close();     // refermer la connexion avec la base de données
						exit(); 
			    		}
	    					else $crd="Okey"; 
			 		} 
			 	else 
			 		{
			 		//effacer touspresents
			 		$rqq = "DELETE  FROM absences WHERE uideleve='touspresents' AND classe='$ch' AND date ='$dtajac' AND $valeur='-' LIMIT 1";
					$resultt = @mysql_query ($rqq) or die (mysql_error());
					if (!$resultt)  // Si l'enregistrement est incorrect
			        	{                           
				    	echo "<p>Votre commentaire n'a pas pu &#234;tre enregistr&#233; &#224; cause d'une erreur syst&#232;me"."<p></p>" . mysql_error() . "<p></p>";
						mysql_close();     // refermer la connexion avec la base de données
					 	exit(); 
			    		}
	    					else $crd="Okey";
			 		}
			 	}
			}
	//pour les creneaux existants, updater si tousprésents
	if (count($cren_y)>0)
	 		{	
			foreach ( $cren_y as $clé => $valeur)
				{
				$rq= "SELECT count(*) FROM absences WHERE uideleve!='touspresents' AND classe='$ch' AND date ='$dtajac' AND $valeur!='' ";
				$result = @mysql_query ($rq) or die (mysql_error()); 
				$nb_enrg = mysql_fetch_array($result, MYSQL_NUM); 
	 			//si tous presents
	 			if ($nb_enrg[0]==0) 
		 			{
		 			$rqt= "SELECT count(*) FROM absences WHERE uideleve='touspresents' AND classe='$ch' AND date ='$dtajac' AND $valeur='-' ";
					$resultat = @mysql_query ($rqt) or die (mysql_error()); 
					$nb_ = mysql_fetch_array($resultat, MYSQL_NUM); 
	 				//si tous presents n'est pas enregistré
	 				if ($nb_[0]==0) 
			 			{
			 			$rqq ="INSERT INTO absences (date,uidprof, uideleve,classe,".$valeur." ) VALUES ( '$dtajac','{$_SESSION['login']}', 'touspresents', '$ch', '-')";
						$resultt =  mysql_query($rqq);
						if (!$resultt)  // Si l'enregistrement est incorrect
			        		{                           
				     		echo "<p>Votre commentaire n'a pas pu &#234;tre enregistr&eacute; &#224; cause d'une erreur syst&#232;me"."<p></p>" . mysql_error() . "<p></p>";
					 		mysql_close();     // refermer la connexion avec la base de données
							exit(); 
			    			}
	    					else $crd="Okey";
			    		}
		 			}
		 		else 
		 			{
		 			// si une abseences ou retard sont ajouT, effacer touspresents
		 			$rqq = "DELETE  FROM absences WHERE uideleve='touspresents' AND classe='$ch' AND date ='$dtajac' AND $valeur='-' LIMIT 1";
					$resultt = @mysql_query ($rqq) or die (mysql_error());
					if (!$resultt)  // Si l'enregistrement est incorrect
		        		{                           
			     		echo "<p>Votre commentaire n'a pas pu &#234;tre enregistr&eacute; &#224; cause d'une erreur syst&#232;me"."<p></p>" . mysql_error() . "<p></p>";
				 		mysql_close();     // refermer la connexion avec la base de données
					 	exit(); 
		    			}
	    					else $crd="Okey";
		 			}				
				}
			}
	//fin tous presents		
}
 //fin valider 
 
if (isset($_POST['cren'])) 
	{
	$y=0;$n=0;
	$cren_y=array();
	$cren_n=array();
	for ($i = 0; $i < count($_POST['cren']); $i++) 
		{
		$tab_cren=$_POST['cren'];	
		$cr=$_POST['cren'][$i];
		$rq= "SELECT count(*) FROM absences WHERE uidprof='{$_SESSION['login']}'   AND classe='$ch' AND date ='$dtajac' AND $cr!='' ";
		$result = @mysql_query ($rq) or die (mysql_error()); 
		$nb = mysql_fetch_array($result, MYSQL_NUM); 
		if ($nb[0]!=0) 
	 		{
	 		$cren_y[$y]=$cr;$y++;
	 		} 
	 	else 
 		$cren_n[$n]=$cr;$n++;
 		}
 	}
	else
	$tab_cren=array(); 
 
//existe-il déjà un enregistrement ?
$rq2 = "SELECT id_abs FROM absences 
 		WHERE uidprof='{$_SESSION['login']}'  AND classe='$ch' AND date ='$dtajac' ";
 // lancer la requête
		$result2 = @mysql_query ($rq2) or die (mysql_error()); 
		$test=mysql_fetch_object($result2);
		if (mysql_num_rows($result2)==0)	$exist="false"; else $exist="true";		

//on recupere les donnees
if ($exist=="true")
	{
	$rq3= "SELECT uideleve, M1, motifM1, M2, motifM2, M3, motifM3, M4, motifM4,M5, motifM5 , S1,motifS1, S2, motifS2, S3 ,motifS3 ,S4 ,motifS4 ,S5,motifS5
	FROM absences WHERE uidprof='{$_SESSION['login']}'  AND classe='$ch' AND date ='$dtajac' ";
	$result3 = @mysql_query ($rq3) or die (mysql_error()); 
	$loop=0;
	while ($retour = mysql_fetch_object($result3))
		{
   	$uid_eleve[$loop]=$retour->uideleve;
		$_m1[$loop]=$retour->M1;
		$_motifm1[$loop]=$retour->motifM1;
		$_m2[$loop]=$retour->M2;
		$_motifm2[$loop]=$retour->motifM2;
		$_m3[$loop]=$retour->M3;
		$_motifm3[$loop]=$retour->motifM3;
		$_m4[$loop]=$retour->M4;
		$_motifm4[$loop]=$retour->motifM4;
		$_m5[$loop]=$retour->M5;
		$_motifm5[$loop]=$retour->motifM5;
		$_s1[$loop]=$retour->S1;
		$_motifs1[$loop]=$retour->motifS1;
		$_s2[$loop]=$retour->S2;
		$_motifs2[$loop]=$retour->motifS2;
		$_s3[$loop]=$retour->S3;
		$_motifs3[$loop]=$retour->motifS3;
		$_s4[$loop]=$retour->S4;
		$_motifs4[$loop]=$retour->motifS4;
		$_s5[$loop]=$retour->S5;
		$_motifs5[$loop]=$retour->motifS5;
		$loop++;
		}
		
	}
	

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
        <meta name="author" content="Philippe LECLERC -TICE CAEN" />
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<title>module &#139;(+_-)/&#155;</title>
	<link href="../style/style.css" rel="stylesheet" type="text/css"/>
	<link  href="../style/navlist-prof.css" rel="stylesheet" type="text/css" />
	<link  href="../../../libjs/jquery-ui/css/ui-lightness/jquery-ui.css" rel="stylesheet" type="text/css" />
	<!--[if IE]>
        <link href="../style/style-ie.css"  rel="stylesheet" type="text/css"/>
        <![endif]-->
	<script  type="text/javascript" src="../Includes/barre_java.js"></script>
	<script type="text/javascript" src="../../../libjs/jquery/jquery.js"></script>
	<script type="text/javascript" src="../../../libjs/jquery-ui/jquery-ui.js"></script>  
	<script  type="text/javascript" src="../Includes/JQ/cdt-script.js"></script>
                  <script type="text/javascript" src="../Includes/cdt.js"></script>

</head>
<body>
<h1 class='title'>Carnet d'absences</h1>
<form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="post" >
    <div><input name="TA" type="hidden"  value="<?php echo md5($_SESSION['RT'].htmlentities($_SERVER['PHP_SELF'])); ?>" /></div>
<fieldset id="field7">
<legend id="legende">Saisie / visualisation des absences</legend>
<?php
//affichage du formulaire
echo '<div id="abs-contenu">';
//si pas de classe de même niveau dans la matière
if (!mysql_num_rows($result)==0) 
        {
        echo '<p>Ce carnet vous permet de consigner les <b>A</b>bsences/<b>R</b>etards de vos &eacute;l&egrave;ves, de sortir un bilan pour une p&eacute;riode donn&eacute;e et de consulter par semaine, les absences/retards dans toutes les mati&egrave;res.</p>';
        echo '<div id="crenos">';
        echo '<p>Date : <input id="cpe-abs" size="10" name="datejavac_dif" value="'.$dtajac_dif.'" readonly="readonly" style="cursor: text" />';
        echo ' Classe :<select name="mlec547trg2s5hy" class="cdt-select">';
        foreach ( $data1 as $clé => $valeur)
          {
           echo "<option value=\"$valeur\"";
          if ($valeur==$ch) {echo ' selected="selected"';}
          echo ">$valeur</option>\n";
          }
          echo "</select></p>";
          echo "<p>Cr&#233;neaux horaires :";
          include "../Includes/creneau.inc.php";
        $horaire = array("M1","M2","M3","M4","M5","S1","S2","S3","S4","S5");
        $creno_prec="";
        for ($h=0; $h<=9; $h++)
                {
                if (in_array($horaire[$h], $cren_off)) 
                {
                    echo "&nbsp;";
                    continue;
                }
                echo '<span class="cadrecheck" ><input type="checkbox"  name="cren[]"   value="'.$horaire[$h].'"'; if (in_array($horaire[$h], $tab_cren)) echo ' checked="checked"';
                echo ' />'.$horaire[$h].'</span>';
                //creneau precedent le premier creneau selectionne
                if ( $creno_prec=="" && in_array($horaire[$h], $tab_cren) && $h>0) 
                    {
                    $creno_prec=$horaire[$h-1];
                    $in10_creno=$h-1;
                    }
                }
        echo "<input  class='bt-valid-sel' type='submit' name='OK' value='' />";
        echo '</p></div>';

    //affichage des boutons fermer et enregistrer
    //pas de tableau avant la sélection des creneaux
    if (!isset($_GET['mlec547trg2s5hy'])  && count($_POST['cren'])>0)
	{	
	echo '<div id="bt-fixe" ><input class="bt-enregistre" type="submit" name="Valider" value="" />';
	echo '<div >';
	if ($crd=="Okey") 
	{
	echo '
		<script type="text/javascript">
        // <![CDATA[
		setTimeout("vider()",2000);
 	    //]]>
		</script>
		';
	echo '<span id="crd" class="retard"> &nbsp;Enregistrement effectu&eacute;&nbsp; </span>';
	}
	 else echo '<br />';
	echo '</div>';
	echo '<input class="bt-fermer" type="submit" name="Fermer" value="" /></div>';
        echo '<h5 class="perso"> Si tous les &eacute;l&egrave;ves sont pr&eacute;sents, cliquez sur Enregistrer pour indiquer que l\'appel a &eacute;t&eacute; effectu&eacute;.</h5>';
        echo '<table id="abs" border="0">';
        echo '<thead>';
        echo '<tr>';
        echo '<td colspan="2" class="elv">El&egrave;ve</td><td><table class="motif" border="0"><tr><td colspan="2">Cumul au</td></tr><tr><td colspan="2">'.$dtajac_dif.'</td></tr><tr><td class="AR">A</td><td class="AR">R</td></tr></table></td>
            <td><table class="motif" border="0"><tr><td colspan="2"> Absences </td></tr><tr><td colspan="2"> du jour </td></tr><tr><td colspan="2"> </td></td></tr></table></td>';
		 
        if (count($cren_n)>0)
             {
             echo '<td  class="enbas">';
             echo '<table border="0"  class="motif"><tr><td colspan="3" class="h-motif">';
             foreach ( $cren_n as $clé => $valeur) echo $valeur ." ";
             echo '</td></tr><tr><td colspan="3" >&nbsp;</td></tr><tr><td class="AR">A</td>';
             if (count($cren_n)==1) echo '<td class="AR">R</td>';
             echo '<td>Motif</td></tr></table></td>';
             }
		 
            if (count($cren_y)>0)
                {
                 foreach ( $cren_y as $clé => $valeur)
                     {
                     echo '<td >' ;
                     echo '<table border="0" class="motif"><tr><td colspan="3">'.$valeur .'</td></tr><tr><td colspan="3" >&nbsp;</td></tr>';
                     echo '<tr><td class="AR">A</td><td class="AR">R</td><td>Motif</td></tr></table></td>';
                     }
                }
            echo '</tr></thead>';
            echo '<tfoot><tr><td colspan="4"><a href="#" onclick="open_new_win(\'bilan.php?cl='.$ch.'\')"  > Bilan pour la classe </a></td></tr></tfoot>';
            echo '<tbody>';
		
          for ($loop=0; $loop<count($users);$loop++) {
            if ($users[$loop]["uid"]!=$_SESSION['login']) {
			//recherche des absences et retards
			$potache=$users[$loop]["uid"];
			$nbabs=0;$nbrtd=0;
			foreach ( $horaire as $clé => $val)
	  			{
				$rq4= "SELECT count(*) FROM absences WHERE  uidprof='{$_SESSION['login']}'  AND $val='A'  AND uideleve='$potache' AND date<='$dtajac' ";
				$result4 = @mysql_query ($rq4) or die (mysql_error()); 
				while ($nb = mysql_fetch_array($result4, MYSQL_NUM)) 
 					{
 					 $nbabs+=$nb[0];
 					}
 			
 				$rq5= "SELECT count(*) FROM absences WHERE  uidprof='{$_SESSION['login']}'  AND $val='R'  AND uideleve='$potache' AND date<='$dtajac' ";
				$result5 = @mysql_query ($rq5) or die (mysql_error()); 
				while ($nb = mysql_fetch_array($result5, MYSQL_NUM)) 
 					{
 					 $nbrtd+=$nb[0];
 					}	
				}//fin foreach $horaire	
			
			//existe t-il des données 
			for ($a = 0; $a <= count($uid_eleve); $a++) 
				{
 				if ($users[$loop]["uid"]==$uid_eleve[$a])
					{
					if ($_m1[$a]=="A") $absm1="A";
					if ($_m1[$a]=="R") $rm1="R";
					$motm1=utf8_encode($_motifm1[$a]);
					if ($_m2[$a]=="A") $absm2="A";
					if ($_m2[$a]=="R") $rm2="R";
					$motm2=utf8_encode($_motifm2[$a]);
					if ($_m3[$a]=="A") $absm3="A";
					if ($_m3[$a]=="R") $rm3="R";
					$motm3=utf8_encode($_motifm3[$a]);
					if ($_m4[$a]=="A") $absm4="A";
					if ($_m4[$a]=="R") $rm4="R";
					$motm4=utf8_encode($_motifm4[$a]);
					if ($_m5[$a]=="A") $absm5="A";
					if ($_m5[$a]=="R") $rm5="R";
					$motm5=utf8_encode($_motifm5[$a]);
					if ($_s1[$a]=="A") $abss1="A";
					if ($_s1[$a]=="R") $rs1="R";
					$mots1=utf8_encode($_motifs1[$a]);
					if ($_s2[$a]=="A") $abss2="A";
					if ($_s2[$a]=="R") $rs2="R";
					$mots2=utf8_encode($_motifs2[$a]);
					if ($_s3[$a]=="A") $abss3="A";
					if ($_s3[$a]=="R") $rs3="R";
					$mots3=utf8_encode($_motifs3[$a]);
					if ($_s4[$a]=="A") $abss4="A";
					if ($_s4[$a]=="R") $rs4="R";
					$mots4=utf8_encode($_motifs4[$a]);
					if ($_s5[$a]=="A") $abss5="A";
					if ($_s5[$a]=="R") $rs5="R";
					$mots5=utf8_encode($_motifs5[$a]);
					break;
					}			
				else
					{
					$absm1="";$rm1="";$motm1="";$absm2="";$rm2="";$motm2="";$absm3="";$rm3="";$motm3="";$absm4="";$rm4="";$motm4="";$absm5="";$rm5="";$motm5="";
					$abss1="";$rs1="";$mots1="";$abss2="";$rs2="";$mots2="";$abss3="";$rs3="";$mots3="";$abss4="";$rs4="";$mots4="";$abss5="";$rs5="";$mots5="";
					}
				}
			$uid_e=$users[$loop]['uid'];
                        
                       
                
				//affichage des noms
        echo "<tr ";
        if (($loop %2)==0 ) 
            { echo 'class="lgn0"';} 
            else 
            {echo 'class="lgn1"';}
        echo '><td><a href="#" class="open_wi" title="Bilan des absences et retards"
            onclick="open_new_win(\'bilan.php?uid='.$users[$loop]["uid"].'&amp;fn='.$users[$loop]["fullname"].'\')">'
        .$users[$loop]["fullname"].'</a></td>';
        echo '<td><a href="#" title="Aper&ccedil;u hebdomadaire" onclick="abs_popup(\''.$uid_e.'\',\'' .$users[$loop]["fullname"].'\'); return false" > <img src="../images/b_calendar.png"  alt="hebdo"/></a></td>';
	 //affichage des cumuls
        echo '<td><table border="0" class="motif"><tr><td class="nbA">'. $nbabs.'h</td><td class="nbR">'.$nbrtd.'</td></tr></table></td>';
        echo '<td ><table border="0" class="motif"><tr><td class="cren">'; 
        for ($h=0; $h<$in10_creno; $h++)
                {
             //absences creneau precedent
                        //$abs_prec=false;
                        if ($creno_prec!="") {
                            $rq= "SELECT count(*) FROM absences WHERE uideleve='$uid_e'   AND date ='$dtajac' AND $horaire[$h]='A'";
                            $result = @mysql_query ($rq) or die (mysql_error()); 
                            $nb = mysql_fetch_array($result, MYSQL_NUM); 
                             if ($nb[0]!=0) echo $horaire[$h].' ';
 		                   }
                }
               
        echo '</td></tr></table></td>';
         //affichage nouveaux creneaux
	if (count($cren_n)>0)
		 {
		 echo '<td><table border="0" class="motif"><tr><td class="boxA"><input type="checkbox" name="abs_x[]" value="'.$users[$loop]["uid"].'" /></td>';
                if (count($cren_n)==1) echo '<td class="boxR"><input type="checkbox" name="ret_x[]" value="'.$users[$loop]["uid"].'" /></td>';else echo '<td></td>';
                echo'<td><input type="text"  name="motif_x['.$uid_e.']" size="25" maxlength="40" /> </td></tr></table></td>';
		  }
	 if (count($cren_y)>0)
		 {
		 foreach ( $cren_y as $cle => $valeur) 
                     {
                     $testabs="abs".strtolower($valeur);
                     $testret="r".strtolower($valeur);
                     $testmotif="mot".strtolower($valeur);
                    //affichage checkbox creneaux existants
                    echo '<td><table border="0" class="motif"><tr><td class="boxA"><input type="checkbox" name="abs_'.$valeur.'[]" value="'.$users[$loop]["uid"].'"';if ($$testabs=="A") echo' checked="checked"';echo ' /></td><td class="boxR"><input type="checkbox" name="ret_'.$valeur.'[]" value="'.$users[$loop]["uid"].'"';if ($$testret=="R") echo ' checked="checked"';echo ' /></td>
                    <td><input type="text"  name="motif_'.$valeur.'['.$uid_e.']" value="'.$$testmotif.'" size="30" maxlength="30" /> </td></tr></table></td>';
                    }
                    echo '</tr>';
                }
            }
      }
     echo '</tbody>';
     
     echo '</table>';
      }
 echo '</div>';
}
?>
</fieldset>	
</form>
<?php
include ('../Includes/pied.inc');
?>
</body>
</html>