<?php
/* =============================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 2.2 du 25/10/2010
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script de génération du flux RSS -
			_-=-_
   ============================================= */
session_name("Cdt_Lcs");
@session_start();
//if ((!isset($_SESSION['version'])) || (!isset( $_SESSION['saclasse']) && !isset($_SESSION['login'])) ) exit;
include ('../Includes/data.inc.php');
include "../Includes/functions2.inc.php";
include ("/var/www/Annu/includes/ldap.inc.php");	
include ("/var/www/lcs/includes/headerauth.inc.php");
include ("/var/www/Annu/includes/ihm.inc.php");
function xml_character_encode($string, $trans='') {
  $trans = (is_array($trans)) ? $trans : get_html_translation_table(HTML_ENTITIES, ENT_QUOTES);
  foreach ($trans as $k=>$v)
    $trans[$k]= "&#".ord($k).";";

  return strtr($string, $trans);
}  
// Connexion à la base de données
require_once ('../Includes/config.inc.php');
//initialisation 
$tsmp=time();

//contrôle des paramètres $_GET
if (!isset($_GET['div'])) exit;
else
{
$divret=explode(':',$_GET['div']);
if ($divret[1]!= substr(md5(crypt($divret[0],$Grain)),2))
exit;
}
$ch=$divret[0];
//echo "toto";
$cmd="hostname -f";
exec($cmd,$hn,$retour);
$hostn= $hn[0];


// Créer la requête (Récupérer les rubriques de la classe) 
$rq = "SELECT prof,matiere,id_prof,prefix FROM onglets
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
		$loop++;
		}
//modif grp
	}
//recherche des onglets "cours d'un eleve"
if ($uid_actif!="") {
	 $groups=people_get_cours($uid_actif);
if ( count($groups) > 0 ) {
    for ($loopo=0; $loopo < count ($groups) ; $loopo++) {
      $rq = "SELECT prof,matiere,id_prof,prefix FROM onglets
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
	
	if (count($liste_cours)>0)
		{
		for($n=0; $n<count($liste_cours); $n++)
			{
			$rq = "SELECT prof,matiere,id_prof,prefix FROM onglets
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
		$loop++;
		}
				}
			}
		}
}
//fin modif
if (count($numero)>0)
{
//eom

	

	//élaboration des dates limites
	$dat=date('Ymd',$tsmp+1209600);//J+15
	$dat2=date('YmdHis',$tsmp);
	//récupération des travaux à faire pour la classe
	$ind=0;
	for ($loop=0; $loop < count($numero) ; $loop++)
		{
		$rq = "SELECT afaire,DATE_FORMAT(datafaire,'%d/%m/%Y'),id_rubrique,datafaire FROM cahiertxt 
		WHERE (id_auteur='$numero[$loop]') AND (datafaire<='$dat') AND (datafaire>='$dat2') AND (afaire!='') AND datevisibi<='$dat2'";
		 
		// lancer la requête
		$result = @mysql_query ($rq) or die (mysql_error());

		// Combien y a-t-il d'enregistrements ?
		$nb2 = mysql_num_rows($result);
		//on fait un tableau de données
		while ($ligne = mysql_fetch_array($result, MYSQL_NUM)) 
			 { 
			$idtaf[$ind]=$ligne[2];
			$idtafcrypt[$ind]=$ligne[2].":".substr(md5(crypt($ligne[2],$Grain)),2);
			$mattaf[$ind]=$mat[$loop];
			$preftaf[$ind]=$pref[$loop];
			$proftaf[$ind]=$prof[$loop];
			$texttaf[$ind]=addSlashes(strip_tags(stripslashes($ligne[0])));
			if (strlen($texttaf[$ind])>200) $texttaf[$ind]=substr($texttaf[$ind],0,199);
			$dattaf[$ind]=$ligne[1];
			$tsmpaf[$ind]=$ligne[3];
			$ind++;
			
			}
		}
		
	//on trie les données par dates croissantes
	if (count($mattaf)>0)
	{
	array_multisort($tsmpaf,$dattaf,$mattaf,$idtaf,$proftaf,$preftaf,$texttaf,$idtafcrypt);
	}
	//fin récup
	//affichage
}
		
echo '<?xml version="1.0" encoding="ISO-8859-15" ?>'."\n";
echo '<rss version="2.0">
<channel>
  <generator>LCS RSS</generator>
  <title>Travail &#224; faire en '.$ch.'</title> 
  <link>http://'.$hostn.'/</link>
  <description>Travaux donn&#233;s &#224; la classe enti&#232;re ET &#224; tous les groupes</description>
  <image>
        <url>../images/appli22_on.png</url>     
  </image> 
  <language>fr</language>
  <ttl>5</ttl>'."\n\n";

if (count($mattaf)>0)
	{
for ($loop=0; $loop < count($dattaf) ; $loop++)
 {
   echo 
'<item>
  <title>'.$mattaf[$loop].' :  '.$preftaf[$loop] .'  '. $proftaf[$loop].'</title>
  <description> Pour le '.$dattaf[$loop]."/&lt;br /&gt;".stripslashes(xml_character_encode($texttaf[$loop])).' </description>
  <link>http://'.$hostn.'/Plugins/Cdt/scripts/poprss.php?id='.$idtafcrypt[$loop].'</link>
</item>'. "\n";

}
}
else
{
	echo 
'<item>
  <title>Rien de programmé !</title>
  <description> Mais en cherchant bien ...</description>
  <link>http://'.$hostn.'/</link>
</item>'. "\n";
	}

// et on termine le fichier 
echo '</channel>' . "\n" . '</rss>' . "\n";  
?>
