<?
/* =============================================
   Projet LCS
   Administration serveur LCS «Liste des Modules installes»
   AdminLCS/Modules_installes.php
   Equipe Tice academie de Caen
   maj : 10/12/2010
   Distribue selon les termes de la licence GPL
   ============================================= */

include ("/var/www/lcs/includes/headerauth.inc.php");
include ("/var/www/Annu/includes/ldap.inc.php");
$msgIntro = "<H1>Gestion des Modules LCS</H1>\n";
list ($idpers, $login)= isauth();

if (ldap_get_right("lcs_is_admin",$login)!="Y")
  die (gettext("Vous n'avez pas les droits suffisants pour acc&#233;der &#224; cette fonction")."</BODY></HTML>");
  
include("modules_commun.php");

	echo "<HTML>\n";
	echo "	<HEAD>\n";
	echo "		<TITLE>...::: Interface d'administration Serveur LCS :::...</TITLE>\n";
	echo "		<LINK  href='../Annu/style.css' rel='StyleSheet' type='text/css'>\n";
	echo "		<LINK  href='boutons_style.css' rel='StyleSheet' type='text/css'>\n";
	echo "      <link rel='stylesheet' href='../Admin/style/stylesort.css' />\n";
	echo "		<script type='text/javascript' src='../Admin/js/script.js'></script>\n";
	echo "	</HEAD>\n";
    	echo "	<BODY>\n";
	echo $msgIntro;


parsage_du_fichier_xml(); // utilisee par la fonction maj_dispo afin de ne pas parser &#224; chaque appel de la fonction
//modification du type de maj
if (isset($_GET['np']))
	{
	//on place la commande dans un fichier
	$act_file="/usr/share/lcs/Modules/action.sh";
	$job= fopen($act_file,"w");
			if (($_GET['action'])=="desact") $cmd1='echo "'.$_GET['np'].' hold" | dpkg --set-selections';
			if (($_GET['action'])=="act") $cmd1='echo "'.$_GET['np'].' install" | dpkg --set-selections';
			fputs($job,"$cmd1 \n");
			fclose($job);
			$cmd="chmod +x /usr/share/lcs/Modules/action.sh";
			exec($cmd,$l,$r);
			$cmd="/usr/bin/sudo -u root /usr/share/lcs/scripts/execution_script_plugin.sh '/usr/share/lcs/Modules/action.sh'";
			exec($cmd,$l,$r);
			$cmd="rm -f action.sh";
			exec($cmd,$l,$r);	
	}
		
//activaion/desactivation 
if (isset($_GET['a']) && isset($_GET['pid']))
	{
	$a=$_GET['a'];
	$pid=$_GET['pid'];
	mysql_query("UPDATE applis SET value='$a' WHERE id='$pid';") or die("Erreur lors de l'activation/d&#233;sactivation du plugin...");
	}
//recherche de la branche du sourceslist
$branch="";
$commande ='cat /etc/apt/sources.list | grep deb | grep Lcs | cut -d" " -f3';
exec($commande,$branche,$ret_val);
if ($ret_val== 0) 
	{
	switch ($branche[0]) 
		{
		case "Lcs":
		    $branch = 'stable';
		    break;
		case "LcsTesting":
		    $branch = 'testing';
		    break;
		case "LcsXP":
		    $branch = 'exp&#233;rimentale';
		    break;
		}
	}
//recherche urlmajmod
$url=explode('/',$urlmajmod);
switch ($url[count($url)-2]) 
	{
	case "modulesLcs":
	    $urlmaj = 'stable';
	    break;
	case "modulesLcsTesting":
	    $urlmaj = 'testing';
	    break;
	case "modulesLcsXP":
	    $urlmaj = 'exp&#233;rimentale';
	    break;
	}
	
//recherche des paquets mis en hold
$pack_hold=array();
$cmd='dpkg --get-selections | grep hold | cut  -f1 | cut -d"-" -f2';
exec($cmd,$pack_hold,$ret_val);

$query="SELECT * from applis where type='M' OR type='N' OR type='S'";
$result=mysql_query($query);
if ($result) 
	{
          if ( mysql_num_rows($result) !=0 ) {      
          // Affichage des Modules installes
          echo "<H3>Modules install&#233;s </H3>\n";
          //Affichage des infos
          if ($branch!="" || $urlmaj!="") 
          {
          echo '<div class="mesg"> Avec la configuration actuelle du LCS,<br />
          <ul><li>l\'autorisation de la mise &#224; jour  d\'un module est relative &#224; la branche :<ul>';
          if ($branch!="") echo '<li><u>'.$branch.'</u> pour les mises &#224; jour automatiques (nocturnes)';
		  if ($urlmaj!="") echo '<li><u>'.$urlmaj.'</u> pour les mises &#224; jour manuelles ';
		  echo '</ul>';
          if ($urlmaj!="") echo '<li>la disponiblit&#233; affich&#233;e d\'une mise &#224; jour est relative &#224; la branche <u>'.$urlmaj.'</u>';
          echo '</ul></div>';
          }
          
          echo '<div id="wrapper">
			<table cellpadding="0" cellspacing="0"  class="sortable" id="sorter">';
          echo '<th>Nom</th><Th>Description</Th><Th class="nosort">Version</Th><Th class="nosort">Aide</Th><Th class="nosort">Activation</Th><Th class="nosort">Autorisation</Th>
          <Th class="nosort">Disponibilit&#233;</Th><Th class="nosort">Action</Th></TR>';
          while ($r=mysql_fetch_object($result))
	  		{ 		  	  list ($v,$plug) = maj_dispo($r->name);
			  echo "<TR>\n";
			  echo "<TD>" . $r->name . "</TD>\n";
			  echo "<TD>" . $r->descr . "</TD>\n";
			  echo "<TD>" . $r->version . "</TD>\n";
	          echo "<TD class=\"centr\"><A HREF=\"../../doc/" . $r->name . "/html/index.html\" TITLE=\"Aide\"><IMG SRC=\"../Plugins/Images/plugins_help.png\" ALT=\"Aide\" BORDER=\"0\" WIDTH=\"29\" HEIGHT=\"28\" /></A></TD>\n";
	          if ($r->type =='N' && $r->value != "0")
			  	echo "<TD class=\"centr\"><A HREF=\"modules_installes.php?pid=" . $r->id . "&a=0\"><IMG SRC=\"../Plugins/Images/plugins_desactiver.png\" TITLE=\"D&#233;sactiver\" BORDER=\"0\" WIDTH=\"29\" HEIGHT=\"28\"/></A></TD>\n";
			  elseif ($r->type =='N' && $r->value != "1")
			  	echo "<TD class=\"centr\"><A HREF=\"modules_installes.php?pid=" . $r->id . "&a=1\"><IMG SRC=\"../Plugins/Images/plugins_activer.png\" TITLE=\"Activer\" BORDER=\"0\" WIDTH=\"29\" HEIGHT=\"28\"/></A></TD>\n";
			  else
			  	echo "<TD>&nbsp;</TD>\n";
	          $nom_paquet= "lcs-".mb_strtolower($r->name);
	          if ($r->type =='N' && (!in_array ($r->name, $pack_hold)))
	          echo '<TD class="buttons"><a href="modules_installes.php?np='.$nom_paquet.'&action=desact" class="positive" title="Interdire la mise &#224; jour de ce module ">&nbsp;OUI&nbsp;</a></TD>';
	          elseif  ($r->type =='N' && (in_array ($r->name, $pack_hold)))
	          echo '<TD class="buttons "><a href="modules_installes.php?np='.$nom_paquet.'&action=act" class="negative"title="Autoriser la mise &#224; jour de ce module">&nbsp;NON&nbsp;</a></TD>';
	          else
	          echo "<TD>&nbsp;</TD>\n";
			  if ($v != false)
			  	echo "<TD><A HREF=\"modules_install.php?p=" . $plug["serveur"] . "&n=" .$r->name  . "\"><IMG SRC=\"../Plugins/Images/plugins_maj.png\" TITLE=\"Mettre &#224; jour\" BORDER=\"0\" WIDTH=\"29\" HEIGHT=\"28\"/></A></TD>\n";
				else
			  	echo "<TD title='Pas de mise &#224; jour disponible'>&nbsp;</TD>\n";
			  echo "<TD class=\"centr\"><A HREF=\"modules_installes.php?dpid=" . $r->id . "&nommod=".$r->name."\"><IMG SRC=\"../Plugins/Images/plugins_desinstall.png\" TITLE=\"Desinstaller\" BORDER=\"0\" WIDTH=\"29\" HEIGHT=\"28\"/></A></TD>\n";
			  echo "</TR>\n";
			}
          echo "</TABLE>";
          echo '</div>
			<script type="text/javascript">
			var sorter=new table.sorter("sorter");
			sorter.init("sorter",0);
			</script>';
          } else {
              echo "<H3>Pas de module install&#233;.</H3>\n";      
          }
        }
        
mysql_free_result($result);
if (isset($_GET['dpid']))
		{	echo "<script type='text/javascript'>";
		echo " if (confirm('Confirmez vous la suppression du module ".$_GET['nommod']." ? ')){";	        
		echo ' location.href = "modules_desinstall.php';
		echo '"+ "?dpid=" + "'.$_GET['dpid'].'" ;} else {';
		echo ' location.href = "';
		echo $_SERVER['PHP_SELF'];
		echo '"   ;} </script> ';}
include ("/var/www/lcs/includes/pieds_de_page.inc.php");
?>
