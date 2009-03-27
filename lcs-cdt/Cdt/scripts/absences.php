<?
/* ==================================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 1.0 du 29/10/2008
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script de carnet absence-
			_-=-_
   =================================================== */
   
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Expires: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache"); 
session_name("Cdt_Lcs");
@session_start();

//si la page est appel�e par un utilisateur non identifi�
if (!isset($_SESSION['login']) )exit;

//si la page est appel�e par un utilisateur non prof
elseif ($_SESSION['cequi']!="prof") exit;

if (isset($_POST['Fermer'])) 
echo "<SCRIPT language='Javascript'>
					<!--
					window.close()
					// -->
					</script>";
							
include "../Includes/functions2.inc.php";
require_once("../Includes/class.inputfilter_clean.php");

$tsmp=time();
$tsmp2=time() + 604800;//j+7

//fichiers n�cessaires � l'exploitation de l'API
	$BASEDIR="/var/www";
	//include "../Includes/basedir.inc.php";
	include "$BASEDIR/lcs/includes/headerauth.inc.php";
	include "$BASEDIR/Annu/includes/ldap.inc.php";
	include "$BASEDIR/Annu/includes/ihm.inc.php";  

// Connexion � la base de donn�es
require_once ('../Includes/config.inc.php');

function is_present($kan,$logprof,$logelev) {
	$Sql= "SELECT id_abs FROM absences WHERE  uidprof='$logprof' AND  uideleve='$logelev' AND date='$kan'";
	$res = @mysql_query ($Sql) or die (mysql_error());
	$tst=mysql_fetch_array($res, MYSQL_NUM);
	if (mysql_num_rows($res)>0) return $tst[0] ;	

}

// Recherche des classes du prof
$rq = "SELECT classe,id_prof FROM onglets
 WHERE login='{$_SESSION['login']}'  GROUP BY classe ORDER BY classe ASC ";
 
// lancer la requ�te
$result = @mysql_query ($rq) or die (mysql_error()); 
$loop=0;
while ($row = mysql_fetch_object($result))
	{
   $data1[$loop]=$row->classe;
	$data2[$loop]=$row->id_prof;
	$loop++;
	}
	
//r�cup�ration du param�tre classe pour m�morisation
if (isset($_GET['mlec547trg2s5hy'])) 
	{
	$ch=$_GET['mlec547trg2s5hy'];
	} 
if (isset($_POST['mlec547trg2s5hy'])) 
	{
	$ch=$_POST['mlec547trg2s5hy'];
	}

//Recherche des �l�ves de la classe
	 
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
	$dtajac_dif=$jo_dif."-".$mo_dif."-".$ann_dif;
	$dtajac= $ann_dif."-".$mo_dif."-".$jo_dif;
	}
	else
//date a la valeur selectionnee	
	{
	$Morceauc=split('-',$_POST['datejavac_dif']);
	$jour_c=$Morceauc[0];
	$mois_c=$Morceauc[1];
	$an_c=$Morceauc[2];
	$dtajac= $an_c."-".$mois_c."-".$jour_c;
	$dtajac_dif=$jour_c."-".$mois_c."-".$an_c;
	}

//creation tableaux "creneaux existants" et "creneaux nouveaux"
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
 		$cren_n[$n]=$cr;$n++;//creneau nouveau
 		}
 	}
	else
	$tab_cren=array();

if (isset($_POST['Valider'])) 
	{	
	//tableau des absences et retards nouveaux creneaux (checkbox) 
	if (!isset($_POST['abs_x'])) $tab_absx=array(); else $tab_absx=$_POST['abs_x'];
	if (!isset($_POST['ret_x'])) $tab_retx=array(); else $tab_retx=$_POST['ret_x'];
	
	//tableau des absences et retards creneaux existants donc a updater
 	if (count($cren_y)>0)
 		{
 		foreach ( $cren_y as $cl� => $valeur)
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
			$oMyFilter = new InputFilter($aAllowedTags, $aAllowedAttr, 0, 0, 1);
			$mot_x = $oMyFilter->process($_POST['motif_x'][$uidpot]);
			}
		elseif (in_array($uidpot, $tab_retx)) 
	 		{
			$typ ="R";
			$oMyFilter = new InputFilter($aAllowedTags, $aAllowedAttr, 0, 0, 1);
			$mot_x = $oMyFilter->process($_POST['motif_x'][$uidpot]);
			}
		else 	
			{
			$typ="";
			$mot_x="";
			}
			//enregistrement eventuel dans la bdd 
			if ($typ!="")
				{
				 foreach ( $cren_n as $cl� => $valeur)
			 		{
			 		$id_absence=is_present($dtajac,$_SESSION['login'],$uidpot);
					if (($id_absence != "")) 
						{
						$rq6 = "UPDATE  absences SET  ".$valeur."='$typ' , motif".$valeur."='$mot_x'  WHERE id_abs='$id_absence' ";
						$result6  =  mysql_query($rq6); 
						if (!$result)  {                           
		     				echo "<p>Votre commentaire n'a pas pu �tre enregistr� � cause d'une erreur syst�me". "<p></p>" . mysql_error() . "<p></p>";
			 				mysql_close();     // refermer la connexion avec la base de donn�es
	    					}
	    				}
	    		else
	    			{	
					$rq6 ="INSERT INTO absences (date,uidprof, uideleve,classe,".$valeur.",motif".$valeur." ) 
					VALUES ( '$dtajac','{$_SESSION['login']}', '$uidpot', '$ch', '$typ', '$mot_x')";
					$result6  =  mysql_query($rq6);
					if (!$result)  // Si l'enregistrement est incorrect
	        			{                           
		     			echo "<p>Votre commentaire n'a pas pu �tre enregistr� � cause d'une erreur syst�me"."<p></p>" . mysql_error() . "<p></p>";
			 			mysql_close();     // refermer la connexion avec la base de donn�es
	    				}
	    			}//fin inser
					}
				}//fin des traitement des nouveaux creneaux
			
//traitemnt des creneaux existants
	if (count($cren_y)>0)
 		{	
			foreach ( $cren_y as $cl� => $valeur)
		{
		$taba="tab_abs".$valeur;
		$tabr="tab_ret".$valeur;
		
		if (in_array($uidpot, $$taba)) 
	 			{
				$type="A";
				$oMyFilter = new InputFilter($aAllowedTags, $aAllowedAttr, 0, 0, 1);
				$motiph = $oMyFilter->process($_POST['motif_'.$valeur.''][$uidpot]);
				}
		elseif (in_array($uidpot, $$tabr)) 
	 			{
				$type="R";
				$oMyFilter = new InputFilter($aAllowedTags, $aAllowedAttr, 0, 0, 1);
				$motiph = $oMyFilter->process($_POST['motif_'.$valeur.''][$uidpot]);
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
		     				echo "<p>Votre commentaire n'a pas pu �tre enregistr� � cause d'une erreur syst�me". "<p></p>" . mysql_error() . "<p></p>";
			 				mysql_close();     // refermer la connexion avec la base de donn�es
	    					}
	    				}
	    				
	    		elseif ($type!="")
	    			{
	    			$rq6 ="INSERT INTO absences (date,uidprof, uideleve,classe,".$valeur.",motif".$valeur." ) 
					VALUES ( '$dtajac','{$_SESSION['login']}', '$uidpot', '$ch', '$type', '$motiph')";
					$result6  =  mysql_query($rq6);
					if (!$result)  // Si l'enregistrement est incorrect
	        			{                           
		     			echo "<p>Votre commentaire n'a pas pu �tre enregistr� � cause d'une erreur syst�me"."<p></p>" . mysql_error() . "<p></p>";
			 			mysql_close();     // refermer la connexion avec la base de donn�es
	    				}
					}
				}//fin foreach 
			}//fin if count $cren_y
		
			
//suppression de l'enregistrement si aucun retard	et aucune absence (suite � une modification)		
			
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
		     		echo "<p>Votre commentaire n'a pas pu �tre enregistr� � cause d'une erreur syst�me"."<p></p>" . mysql_error() . "<p></p>";
			 		mysql_close();     // refermer la connexion avec la base de donn�es
	    			} 
				}	
				
			}//fin liste
		
	

if (isset($_POST['cren'])) 
	{$y=0;$n=0;
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
 		} else 
 		$cren_n[$n]=$cr;$n++;
 		}
 	}
	else
	$tab_cren=array();
  
 }//fin valider 
 
 
//existe-il d�j� un enregistrement ?
$rq2 = "SELECT id_abs FROM absences 
 		WHERE uidprof='{$_SESSION['login']}'  AND classe='$ch' AND date ='$dtajac' ";
 // lancer la requ�te
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
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<HEAD>
	<META HTTP-EQUIV="CONTENT-TYPE" CONTENT="text/html; charset=windows-1252">
	<TITLE>module <(+_-)/></TITLE>
	<meta name="generator" content="Bluefish 1.0.7">
	<META NAME="CREATED" CONTENT="20051226;22304481">
	<META NAME="CHANGED" CONTENT="20051226;22565970">
	<LINK href="../style/style.css" rel="stylesheet" type="text/css">
	<style type="text/css">div#fixe   { position: fixed; top: 20%; left: 1%;}</style>
</HEAD>
<BODY LANG="fr-FR" DIR="LTR">
<SCRIPT language="Javascript" src="../Includes/barre_java.js"></SCRIPT>
<div style="position:absolute; top:0px; left:100px;z-index:3;">
<table class="ds_box" cellpadding="0" cellspacing="0" id="ds_conclass" style="display: none;">
<tr><td id="ds_calclass">
</td></tr>
</table>
</div>
<SCRIPT language="Javascript" src="../Includes/calend.js"></script>
<H1 class='title'>Carnet d'absences</H1>
<form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="post" >
<fieldset>
<legend>
<FONT FACE="Trebuchet MS, sans-serif" color="#009933">  Saisie / visualisation des absences   </FONT>
</legend>
<?
//affichage du formulaire
if (!isset($_GET['mlec547trg2s5hy'])) 
	{	
echo '<DIV id="fixe" ><input type="submit" name="Valider" value="Enregistrer " ><BR><BR>
<input type="submit" name="Fermer" value="&nbsp;Fermer " >
</DIV>';
	}

	//si pas de classe de m�me niveau dans la mati�re
	if (!mysql_num_rows($result)==0) 
		{
		echo '<H4> Ce carnet vous permet de consigner les <B>A</B>bsences/<b>R</b>etards de vos &eacute;l&egrave;ves, de sortir un bilan pour une p&eacute;riode donn&eacute;e et 
		de consulter par semaine, les absences/retards dans toutes les mati&egrave;res.
		 </H4>';
		 
		echo '<H4>Date : <input onclick="ds_sh(this);" size="9" name="datejavac_dif" value="'.$dtajac_dif.'"readonly="readonly" style="cursor: text" />'; 
		echo " Classe <select name='mlec547trg2s5hy'style='background-color:#E6E6FA' >";
	foreach ( $data1 as $cl� => $valeur)
	  { echo "<option valeur=\"$valeur\"";
	  if ($valeur==$ch) {echo 'selected';}
	  echo ">$valeur</option>\n";
	  }
	  echo "</select>&nbsp;&nbsp;";
	  
	echo "<input type='submit' name='OK' value='Valider la selection' /><BR>";
	$horaire = array("M1","M2","M3","M4","M5","S1","S2","S3","S4","S5");
	for ($h=0; $h<=9; $h++) 
		{
		echo '<input type="checkbox" name="cren[]"   value="'.$horaire[$h].'"'; if (in_array($horaire[$h], $tab_cren)) echo 'checked';
		echo ' >'.$horaire[$h];
		}
		echo'<BR>';
//pas de tableau avant la s�lection des creneaux	

if (!isset($_GET['mlec547trg2s5hy'])) 
	{	
		echo '<TABLE  border="1">
		<BR><TR >
		<TD  colspan="2" class="abs"> El&egrave;ve </TD><TD  class="abs">&nbsp;&nbsp;&nbsp; Cumul  au&nbsp;&nbsp;&nbsp; <BR><FONT FACE= "cursive" size="2"color="#E6E6FA"> '.$dtajac_dif.'</FONT><BR>  A&nbsp;&nbsp;
		R</TD>';
		 
		 if (count($cren_n)>0)
		 {
		 echo ' <TD  class="abs">';
		 foreach ( $cren_n as $cl� => $valeur) echo $valeur ." ";
		 echo' <BR><BR> A&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		 if (count($cren_n)==1) echo 'R';
		 echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
		 Motif &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</TD>';
		 }
		 
		if (count($cren_y)>0) 
		  {
		 foreach ( $cren_y as $cl� => $valeur) 
		 {
		 echo ' <TD  class="absmod">' ;
		 echo $valeur ." ";		 
		 echo '<BR><BR> A&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		 R&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
		 Motif &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</TD>';
		 }
		 }
		echo '</TR>';
		
      for ($loop=0; $loop<count($users);$loop++) {
      
			//recherche des absences et retards
			$potache=$users[$loop]["uid"];
			$nbabs=0;$nbrtd=0;
			foreach ( $horaire as $cl� => $val)
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
			
			//existe t-il des donn�es 
			for ($a = 0; $a <= count($uid_eleve); $a++) 
				{
 				if ($users[$loop]["uid"]==$uid_eleve[$a])
					{
					if ($_m1[$a]=="A") $absm1="A";
					if ($_m1[$a]=="R") $rm1="R";
					$motm1=$_motifm1[$a];
					if ($_m2[$a]=="A") $absm2="A";
					if ($_m2[$a]=="R") $rm2="R";
					$motm2=$_motifm2[$a];
					if ($_m3[$a]=="A") $absm3="A";
					if ($_m3[$a]=="R") $rm3="R";
					$motm3=$_motifm3[$a];
					if ($_m4[$a]=="A") $absm4="A";
					if ($_m4[$a]=="R") $rm4="R";
					$motm4=$_motifm4[$a];
					if ($_m5[$a]=="A") $absm5="A";
					if ($_m5[$a]=="R") $rm5="R";
					$motm5=$_motifm5[$a];
					if ($_s1[$a]=="A") $abss1="A";
					if ($_s1[$a]=="R") $rs1="R";
					$mots1=$_motifs1[$a];
					if ($_s2[$a]=="A") $abss2="A";
					if ($_s2[$a]=="R") $rs2="R";
					$mots2=$_motifs2[$a];
					if ($_s3[$a]=="A") $abss3="A";
					if ($_s3[$a]=="R") $rs3="R";
					$mots3=$_motifs3[$a];
					if ($_s4[$a]=="A") $abss4="A";
					if ($_s4[$a]=="R") $rs4="R";
					$mots4=$_motifs4[$a];
					if ($_s5[$a]=="A") $abss5="A";
					if ($_s5[$a]=="R") $rs5="R";
					$mots5=$_motifs5[$a];
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
        echo "<TR "; if (($loop %2)==0 ) echo 'bgcolor="#E6E6FA"';echo "><TD><A href=\"bilan.php?uid=".$users[$loop]["uid"]."&fn=".$users[$loop]["fullname"].'" target="_blank" title="Bilan des absences et retards">'.$users[$loop]["fullname"].'</A></td>';
		echo '<TD><A href="#" title="Aper�u hebdomadaire" onClick="abs_popup(\''.$uid_e.'\',\'' .$users[$loop]["fullname"].'\'); return false" > <img src="../images/b_calendar.png"  alt="hebdo"border="0"/></A></td>'; 
			 
			//affichage des cumuls 
			echo '<TD align="center">'. $nbabs.'h  - '.$nbrtd.' </td>';
			//affichage nouveaux creneaux
			 
		 if (count($cren_n)>0)
		 {
		 echo ' <TD> <input type="checkbox" name="abs_x[]" value="'.$users[$loop]["uid"].'">&nbsp;&nbsp;&nbsp;';
       if (count($cren_n)==1) echo'<input type="checkbox" name="ret_x[]" value="'.$users[$loop]["uid"].'" >';else echo '&nbsp;';
        echo'<INPUT TYPE=TEXT NAME="motif_x['.$uid_e.']" SIZE=30 MAXLENGTH=30> </TD>';
		 
		 }
		 if (count($cren_y)>0)
		 {
		 foreach ( $cren_y as $cl� => $valeur) 
		 {
		 $testabs="abs".strtolower($valeur);
		 $testret="r".strtolower($valeur);
		 $testmotif="mot".strtolower($valeur);		 
			//affichage checkbox creneaux existants      
        echo ' <TD> <input type="checkbox" name="abs_'.$valeur.'[]" value="'.$users[$loop]["uid"].'"';if ($$testabs=="A") echo'checked';echo ' >&nbsp;&nbsp;&nbsp; 
         <input type="checkbox" name="ret_'.$valeur.'[]" value="'.$users[$loop]["uid"].'"';if ($$testret=="R") echo'checked';echo ' >
        <INPUT TYPE=TEXT NAME="motif_'.$valeur.'['.$uid_e.']" value="'.$$testmotif.'"SIZE=30 MAXLENGTH=30> </TD>'; 
        }
        }
      
      }
      echo'<TR></tr><TR>
      </tr><TD><A href="bilan.php?cl='.$ch.' " target="_blank" > Bilan pour la classe </A></td></TR>'; 
     	echo '</TABLE>';
		
		}
 }
?>

</fieldset>	
</FORM>
</BODY>
</HTML>


