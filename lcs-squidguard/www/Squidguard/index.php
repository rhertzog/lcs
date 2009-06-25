<?php
/* ===============================================
   Projet LCS
   Gestion des balacklist squidGuard
   squidGuard/index.php
   Equipe Tice académie de Caen
   Dernière modification :02 Mai 08
   Distribué selon les termes de la licence GPL
   ============================================= */


include "../lcs/includes/headerauth.inc.php";
include "../Annu/includes/ldap.inc.php";
include "../Annu/includes/ihm.inc.php";

# constante
$path2bl="/var/lib/squidguard/db/blacklists/lcs/";
$path2wl="/var/lib/squidguard/db/whitelists/";

// Initialisation variables :
// Methode POST
$list_del = $_POST['list_del'];
$raz_db = $_POST['raz_db'];
$webmail = $_POST['webmail'];
$audiovideo =  $_POST['audiovideo'];
$blog = $_POST['blog'];
$malware = $_POST['malware'];
$ads = $_POST['ads'];
$phishing= $_POST['phishing'];
$redirecteurs = $_POST['redirecteurs'];
$ = $_POST['blog'];
$forums = $_POST['forums'];
$bl_full = $_POST['bl_full'];
$bl_lcs = $_POST['bl_lcs'];
$modif_status = $_POST['modif_status'];

function is_ip($ip) {
   $ip = trim($ip);
   if (strlen($ip) < 7) $status = "false";
   if (!@ereg("\.",$ip)) $status = "false";
   if (!@ereg("[0-9.]{" . strlen($ip) . "}",$ip)) $status = "false";
   $ip_arr = split("\.",$ip);
   if (count($ip_arr) != 4) $status = "false";
   for ($i=0;$i<count($ip_arr);$i++) {
       if ((!is_numeric($ip_arr[$i])) || (($ip_arr[$i] < 0) || ($ip_arr[$i] > 255))) $status = "false";
   }
   if (!$status) $status = "true";
   return $status;
}

function extract_domain($entry) {
	// Extraction  du nom de domaine
	$tmp=explode("/",$entry,3);
	return $tmp[0]; 
}

function read_black_list ($name) {
	global $path2bl;
	if ( file_exists($path2bl.$name) ) {
		$fd = @fopen($path2bl.$name, "r");
		$k=0;
		while ( !feof($fd) ) {
			$tmp = fgets($fd, 255);
			if ( !ereg ("^#", $tmp ) && $tmp !="" ) {
				$file_content[$k] = trim($tmp);
				$k++;
			}										
		}
		fclose($fd);			
	}
	return $file_content;
}

list ($idpers,$login)= isauth();
if ($idpers == "0") header("Location:$urlauth");

$html  = "<html>\n";
$html .= "	<hed>\n";
$html .= "		<title>Interface d'administration SquidGuard</title>\n";
$html .= "		<link  href='../Annu/style.css' rel='StyleSheet' type='text/css'>\n";
echo $html;
?>
<style>
.header {
	font-family: Verdana, Arial, Helvetica, sans-serif;
    	font-size: 15px;
    	font-weight: bold;
    	color: #f8f8ff;
    	border: 1px #333333 solid;
    	background-color: #486591;
    	clip:  rect(   )
}
.cadreg {
	border-left: 1px #333333 solid;
}
.cadred {
	border-right: 1px #333333 solid
}

.cadregb {
	border-left: 1px #333333 solid; 
	border-bottom: 1px #333333 solid;
}
.cadredb {
	border-right: 1px #333333 solid; 
	border-bottom: 1px #333333 solid;
}

.cadredg {
	border-left: 1px #333333 solid;
	border-right: 1px #333333 solid;
}
.cadredbg {
	border-right: 1px #333333 solid;
	border-bottom: 1px #333333 solid;
	border-left: 1px #333333 solid;		
}
</style>

<?
$html = "	</head>\n";
$html .= "	<body>\n";
echo $html;

// Modif de la configuration squidGuard.conf
if ( $modif_status ) {
	if ( $audiovideo == "audiovideoOn" ) $cmd = $audiovideo; else $cmd = "audiovideoOff";
        exec ("/usr/bin/sudo /usr/share/lcs/scripts/squidGuard.sh $cmd");
	if ( $malware == "malwareOn" ) $cmd = $malware; else $cmd = "malwareOff";
        exec ("/usr/bin/sudo /usr/share/lcs/scripts/squidGuard.sh $cmd");
        if ( $ads == "adsOn" ) $cmd = $ads; else $cmd = "adsOff";
        exec ("/usr/bin/sudo /usr/share/lcs/scripts/squidGuard.sh $cmd");
        if ( $redirecteurs == "redirecteursOn" ) $cmd = $redirecteurs; else $cmd = "redirecteursOff";
        exec ("/usr/bin/sudo /usr/share/lcs/scripts/squidGuard.sh $cmd");
        if ( $marketingware == "marketingwareOn" ) $cmd = $marketingware; else $cmd = "marketingwareOff";
        exec ("/usr/bin/sudo /usr/share/lcs/scripts/squidGuard.sh $cmd");
        if ( $phishing == "phishingOn" ) $cmd = $phishing; else $cmd = "phishingOff";
        exec ("/usr/bin/sudo /usr/share/lcs/scripts/squidGuard.sh $cmd");
	if ( $blog == "blogOn" ) $cmd = $blog; else $cmd = "blogOff";
        exec ("/usr/bin/sudo /usr/share/lcs/scripts/squidGuard.sh $cmd");
	if ( $webmail == "webmailOn" ) $cmd = $webmail; else $cmd = "webmailOff";
	exec ("/usr/bin/sudo /usr/share/lcs/scripts/squidGuard.sh $cmd");
 	if ( $forums == "forumsOn" ) $cmd = $forums; else $cmd = "forumsOff";
	exec ("/usr/bin/sudo /usr/share/lcs/scripts/squidGuard.sh $cmd");       
	if ( $bl == "bl_full" ) $cmd = $bl; else $cmd = "bl_lcs";
	exec ("/usr/bin/sudo /usr/share/lcs/scripts/squidGuard.sh $cmd");
 	if ( $raz_db == "1" ) exec ("/usr/bin/sudo /usr/share/lcs/scripts/squidGuard.sh raz_db");
       	// Recharger la configuration squid
        exec ("/usr/bin/sudo /usr/share/lcs/scripts/squidGuard.sh reload");	
}

if (ldap_get_right("lcs_is_admin",$login)=="Y") {
	echo "<div align='center'><h2>Modification de la «Liste Noire» LCS</h2></div>\n";
	if ( !$action || ( $action=="Ajouter" && !$list_add ) || ( $action=="Supprimer" && count($list_del) == 0 ) ) {	
		// lecture des fichiers domains pour affichage de l'etat de la liste LCS
		$file_domains=read_black_list ("domains");
		// lecture des fichiers urls pour affichage de l'etat de la liste LCS		
		$file_urls=read_black_list ("urls");
		// Affichage du rormulaire d'ajout/suppression
		$html  = "<form method='POST' action='index.php'>\n";
		$html .= "<table border='0' cellspacing='0' style='margin-left: 1px;'>\n";
  		$html .= "<thead>\n";
    		$html .= "  <tr>\n";
      		$html .= "    <th class='header' colspan='2'>Ajout</th>\n";
		$html .= "    <th>&nbsp;</th>\n";
      		$html .= "    <th class='header'>Suppression</th>\n";
    		$html .= "  </tr>\n";
  		$html .= "</thead>\n";		
		$html .= "<tr>\n<td class='cadreg'>\n";
		$html .= "		<b>DOMAINE ou URL</b> de la forme :<br><br>\n";
		$html .= "		<center>\n";
		$html .= "		www.domaine.extension<br>\n";
		$html .= "		ou<br>\n";
		$html .= "		adresse IP<br>\n";
		$html .= "		ou<br>\n";								
		$html .= "		www.domaine.ext/répertoire/fichier&nbsp;\n";
		$html .= "		</center>\n";
		$html .= "	</td>\n";
		$html .= "	<td class='cadred'><textarea name='list_add' rows='12' cols='30'></textarea></td>\n";
		$html .= "	<td></td>\n";
		$html .= "	<td align='top' class='cadredg'>\n";
        	$html .= "        <select size='10' name='list_del[]' multiple='multiple'>\n";
		$html .= "          <optgroup label='Domaines'>\n";
   		for ($loop=0; $loop < count($file_domains); $loop++) {
          		$html .=  "           <option value='".$file_domains[$loop]."'>".$file_domains[$loop]."\n";
   		}
		$html .= "          </optgroup>\n";
        	$html .= "          <optgroup label='Urls'>\n";
        	
   		for ($loop=0; $loop < count($file_urls); $loop++) {
          		$html .=  "           <option value='".$file_urls[$loop]."'>".$file_urls[$loop]."\n";
   		}
		$html .= "          </optgroup>\n";
        	$html .= "        </select>\n";				
		$html .= "      </td>\n";
		$html .= "</tr>\n";
		$html .= "<tr>\n";
		$html .= "  <td align='right' class='cadregb'>\n";		
		$html .= "    <input type='reset' value='R&eacute;initialiser'>\n";
		$html .= "  </td>\n";
		$html .= "  <td align='center' class='cadredb'>\n";
		$html .= "    <input type='submit' name='action' value='Ajouter'>\n";
		$html .= "  </td>\n";	
		$html .= "	<td></td>\n";	
		$html .= "  <td align='center' class='cadredbg'>\n";
		$html .= "    <input type='submit' name='action' value='Supprimer'>\n";
		$html .= "  </td>\n";		
		$html .= "</tr>\n";
		$html .= "</table>\n";
		$html .= "</form>\n";
		echo $html;
		if ( $action == "Ajouter" && !$list_add ) echo "<div class='error_msg'>Vous devez compléter le champ de saisie avec au moins un nom de domaine une url ou une adresse IP à ajouter !</div>";
		if ( $action == "Supprimer" && count($list_del) == 0 ) echo "<div class='error_msg'>Vous devez choisir dans la liste au moins un élément à supprimer !</div>";
		
		// Section formulaire de choix type de liste noire et liste noir webmail webmail
		exec ("/usr/bin/sudo /usr/share/lcs/scripts/squidGuard.sh status", $AllOutPut);
		$status=explode(" ",$AllOutPut[0]); 
		$html = "<div align='center'><h2>Configuration des «Listes Noires»</h2></div>\n";
		$html .= "<form method='POST' action='index.php'>\n";
                $html .= "RAZ liste noire LCS : <input type='checkbox' value='1' name='raz_db'><br>\n";
		$html .= "Validation liste noire webmail : <input type='checkbox' value='webmailOn' name='webmail'";
		if ( $status[0] == "webmailOn") $html.="CHECKED><br>\n"; else $html.="><br>\n";
		$html .= "Validation liste noire forums : <input type='checkbox' value='forumsOn' name='forums'";
		if ( $status[1] == "forumsOn") $html.="CHECKED><br>\n"; else $html.="><br>\n";
		$html .= "Validation liste noire audio et video (YouTube, DailyMotion, Deezer,...): <input type='checkbox' value='audiovideoOn' name='audiovideo'";
                if ( $status[2] == "audiovideoOn") $html.="CHECKED><br>\n"; else $html.="><br>\n";
    		$html .= "Validation liste noire blog (Skyblog,...): <input type='checkbox' value='blogOn' name='blog'";
                if ( $status[7] == "blogOn") $html.="CHECKED><br>\n"; else $html.="><br>\n";
		$html .= "Validation liste noire publicité : <input type='checkbox' value='adsOn' name='ads'";
		if ( $status[3] == "adsOn") $html.="CHECKED><br>\n"; else $html.="><br>\n";
		$html .= "Validation liste noire logiciels Malveillants : <input type='checkbox' value='malwareOn' name='malware'";
		if ( $status[4] == "malwareOn") $html.="CHECKED><br>\n"; else $html.="><br>\n";
		$html .= "Validation liste noire marketingware : <input type='checkbox' value='marketingwareOn' name='marketingware'";
		if ( $status[5] == "marketingwareOn") $html.="CHECKED><br>\n"; else $html.="><br>\n";
		$html .= "Validation liste noire phishing : <input type='checkbox' value='phishingOn' name='phishing'";
		if ( $status[6] == "phishingOn") $html.="CHECKED><br>\n"; else $html.="><br>\n";
		$html .= "Validation liste noire redirecteurs (proxy en ligne,....) : <input type='checkbox' value='redirecteursOn' name='redirecteurs'";
		if ( $status[8] == "redirecteursOn") $html.="CHECKED><br>\n"; else $html.="><br>\n";
		$html .= "Liste noire LCS : <input type='checkbox' value='On' name='bl_lcs' CHECKED disabled><br>\n"; 
		if ( $status[9] == "bl_full") $html.="CHECKED><br>\n"; else $html.="
<br>\n";	
		$html .= "Liste noire nationale : <input type='checkbox' value='bl_full' name='bl'>";
		$html .= "	<input type='hidden' value='1' name='modif_status'>\n";
		$html .= "	<input type='submit' value='Modifier'></td>\n";	
		$html .= "</form>\n";
		echo $html;
	} elseif ( $action == "Ajouter" ) {
		// AJOUT
		// Verification des entrées saisies
		// ================================
		// Mise en tableau des entrées saisies
		$result = split ("[\r]|[\ \]",$list_add,256);
		// Vérifications de l'existance des entrées
		$i=0;$j=0;
		for ($loop=0; $loop < count($result); $loop++) {
			// suppression des espaces
			$tmp = trim($result[$loop]);
			// suppression de http:// 
			$result[$loop] = ereg_replace ("^http://","",$tmp);
			// $result[$loop] ne comporte pas de slash => Transfert dans $domains_list[$loop] si le domain est valide
			if ( strpos($result[$loop],"/") === False  ) {
				// Verification validité
				if ( is_ip($result[$loop]) == "true" ) {
				        // L'entree est une adresse ip
					if ( gethostbyaddr($result[$loop]) != $result[$loop] ) {
						$domains_list[$i] = $result[$loop];$i++;	
					}						
				} else {
				        // L'entree n'est pas une adresse ip
					if ( is_ip( gethostbyname($result[$loop]) ) == "true" ) {
						$domains_list[$i] = $result[$loop];$i++;
					}
				}
			}  else {
			// $result[$loop] comporte des slash => Transfert dans $urls_list[$loop] si le domain est valide
				// Verification validité
				if ( is_ip( extract_domain($result[$loop]) ) == "true" ) {
				        // L'entree est une adresse ip
					if ( gethostbyaddr( extract_domain($result[$loop]) ) != $result[$loop] ) {
						$urls_list[$j] = $result[$loop];$j++;
					}
				} else {
				        // L'entree n'est pas une adresse ip
					if ( is_ip( gethostbyname( extract_domain($result[$loop]) ) ) == "true" ) {
						$urls_list[$j] = $result[$loop];$j++;
					}
				}				
			}
		}
		if ( count($domains_list) !=0 ) {
			echo "<h4>Les domaines ci-dessous sont </h4>\n";
			// Lecture du fichier domains
			$file_domains=read_black_list ("domains");
			// Recherche si les éléments de $domains_list sont présents dans $file_domains
			// et constitution des fichiers domains et domains.diff
			$fp=@fopen($path2bl."domains.diff","w");
			$fp1=@fopen($path2bl."domains","a");
			for ( $loop=0; $loop < count($domains_list); $loop++ ) {
				if ( @in_array ($domains_list[$loop], $file_domains) )
   					print "<li>".$domains_list[$loop]." déja dans la liste noire<br>\n";
  				else {
					if ($fp) fwrite($fp,"+".$domains_list[$loop]."\n");
					if ($fp1) fputs($fp1,$domains_list[$loop]."\n");
					print "<li>".$domains_list[$loop]." ajouté à la liste noire<br>\n";
				}
			}
			@fclose($fp);	
			@fclose($fp1);				
			$add_domains=true;
		}
		if ( count($urls_list) !=0 ) {
			echo "<h4>Les urls ci-dessous sont </h4>";
			// Lecture du fichier domains
			$file_urls=read_black_list ("urls");
			// Recherche si les éléments de $domains_list sont présents dans $file_domains
			// et constitution des fichiers domains et domains.diff
			$fp=@fopen($path2bl."urls.diff","w");
			$fp1=@fopen($path2bl."urls","a");
			for ( $loop=0; $loop < count($urls_list); $loop++ ) {
				if ( @in_array ($urls_list[$loop], $file_urls) )
   					print "<li>".$urls_list[$loop]." déja dans la liste noire<br>\n";
  				else {
					if ($fp) fwrite($fp,"+".$urls_list[$loop]."\n");
					if ($fp1) fputs($fp1,$urls_list[$loop]."\n");
					print "<li>".$urls_list[$loop]." ajouté à la liste noire<br>\n";
				}
			}
			@fclose($fp);	
			@fclose($fp1);				
			$add_urls=true;
		}
		if ( $add_domains || $add_urls ) {			
			// Modification de la blacklist LCS pour AJOUTs
			exec ("/usr/bin/sudo /usr/share/lcs/scripts/squidGuard.sh lcs");
                        exec ("/usr/bin/sudo /usr/share/lcs/scripts/squidGuard.sh reload");		
		} else {
			echo "<h4>Aucune modification n'a été apportée à la base de données squidguard .</h4>\n";
			echo "<div class='error_msg'>Vous devez compléter le champ de saisie avec au moins un nom de domaine une URL ou une adresse IP valide !</div>\n";
		}		
		
	} else {
		// SUPPRIMER
		echo "<h4>Les domaines, adresses IP ou urls ci-dessous :</h4>\n<ul>\n";
		$i=0;$j=0;
		$fp=@fopen($path2bl."domains.diff","w");
		$fp1=@fopen($path2bl."urls.diff","w");
		for ( $loop=0; $loop < count($list_del); $loop++ ) {
			if ( strpos($list_del[$loop],"/") === False  ) {
				if ($fp) fwrite($fp,"-".$list_del[$loop]."\n");
				$list_del_domains[$i] = $list_del[$loop];
				$i++;
				echo "<li>".$list_del[$loop]."<br>";
			} else {
				if ($fp1) fwrite($fp1,"-".$list_del[$loop]."\n");
				$list_del_urls[$j] = $list_del[$loop];
				$j++;				
				echo "<li>".$list_del[$loop]."<br>";
			}
		}
		@fclose($fp);	
		@fclose($fp1);	
		echo "</ul>\n<h4>ont été supprimées de la «liste noire» LCS.</h4>\n";
		if ( count($list_del_domains) > 0 ) {
			$domain_file = read_black_list ("domains");
			$result_domains = array_diff($domain_file, $list_del_domains);
			unlink ($path2bl."domains");
			$fp=@fopen($path2bl."domains","a");
			fputs($fp,"# base domains lcs\n");
			for ( $loop=0; $loop < count($domain_file); $loop++ ) {
				if ( $result_domains[$loop] != "" )  fputs($fp,$result_domains[$loop]."\n");
			}
			@fclose($fp);
		}
		if ( count($list_del_urls) > 0 ) {
			$urls_file = read_black_list ("urls");		
			$result_urls = array_diff($urls_file, $list_del_urls);
			unlink ($path2bl."urls");
			$fp=@fopen($path2bl."urls","a");
			fputs($fp,"# base urls lcs\n");
			for ( $loop=0; $loop < count($urls_file); $loop++ ) {
				if ( $result_urls[$loop] != "" )  fputs($fp,$result_urls[$loop]."\n");
			}
			@fclose($fp);
		}
		// Modification de la blacklist LCS pour SUPPRESSIONs
		exec ("/usr/bin/sudo /usr/share/lcs/scripts/squidGuard.sh lcs");
                exec ("/usr/bin/sudo /usr/share/lcs/scripts/squidGuard.sh reload");		
										
	}

} else {
	echo "<div class=error_msg>Cette application, nécessite les droits d'administrateur du serveur LCS !</div>\n";
}
include ("../lcs/includes/pieds_de_page.inc.php");
?>
