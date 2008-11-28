<?php
/* ===============================================
   Projet LCS
   Gestion des whitelists squidGuard
   squidGuard/gestwhitlst.php.php
   Equipe Tice académie de Caen
   Dernière modification :13 Juin 2008
   Distribué selon les termes de la licence GPL
   ============================================= */


include "../lcs/includes/headerauth.inc.php";
include "../Annu/includes/ldap.inc.php";
include "../Annu/includes/ihm.inc.php";
require ("/var/www/lcs/includes/config.inc.php");

# constante
$path2wl="/var/lib/squidguard/db/whitelists/lcs/";

# Fonctions
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

function read_white_list ($name) {
	global $path2wl;
	if ( file_exists($path2wl.$name) ) {
		$fd = @fopen($path2wl.$name, "r");
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

//Test si le paquet se3-internet est installé sur le se3
	if ( count(search_machines("cn=portables_eleves","parcs"))==0) {
		$html .="<div class=alert_msg>La gestion de la liste blanche nécessite l'installation du paquet se3-internet sur le serveur SE3, et une réplication d'annuaire ! </div>\n"; 
	echo $html;	exit;
	}
echo $html;	            

//Test si la config se3-internet est installée
$query="SELECT value from lcs_db.params where name='se3-internet' ";
$result=@mysql_query($query);
if (mysql_num_rows($result)==0) 
	{
//Si non, permutation du fichier squidGuard.se3 --> squidGuard.conf
$cmd = "se3_internet";
exec ("/usr/bin/sudo -H /usr/share/lcs/scripts/squidGuard.sh $cmd");
}


if (ldap_get_right("lcs_is_admin",$login)=="Y") {
	echo "<div align='center'><h2>Modification de la «Liste Blanche» LCS</h2></div>\n";
	if ( !$action || ( $action=="Ajouter" && !$list_add ) || ( $action=="Supprimer" && count($list_del) == 0 ) ) {	
		// lecture des fichiers domains pour affichage de l'etat de la liste LCS
		$file_domains=read_white_list ("domains");
		// lecture des fichiers urls pour affichage de l'etat de la liste LCS		
		$file_urls=read_white_list ("urls");
		// Affichage du rormulaire d'ajout/suppression
		$html  = "<form method='POST' action='gestwhitlst.php'>\n";
		$html .= "<table border='0' cellspacing='0' style='margin-left: 1px;'>\n";
  		$html .= "<thead>\n";
    		$html .= "  <tr>\n";
      		$html .= "    <th class='header' colspan='2'>Ajout</th>\n";
		$html .= "    <th>&nbsp;</th>\n";
      		$html .= "    <th class='header'>Suppression </th>\n";
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
		$html .= "	<td align='top' class='cadredg' >\n";
        $html .= "        <select size='10' STYLE='width:200' name='list_del[]' multiple='multiple'>\n";
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
			$file_domains=read_white_list ("domains");
			// Recherche si les éléments de $domains_list sont présents dans $file_domains
			// et constitution des fichiers domains et domains.diff
			$fp=@fopen($path2wl."domains.diff","w");
			$fp1=@fopen($path2wl."domains","a");
			for ( $loop=0; $loop < count($domains_list); $loop++ ) {
				if ( @in_array ($domains_list[$loop], $file_domains) )
   					print "<li>".$domains_list[$loop]." déja dans la liste blanche<br>\n";
  				else {
					if ($fp) fwrite($fp,"+".$domains_list[$loop]."\n");
					if ($fp1) fputs($fp1,$domains_list[$loop]."\n");
					print "<li>".$domains_list[$loop]." ajouté à la liste blanche<br>\n";
				}
			}
			@fclose($fp);	
			@fclose($fp1);				
			$add_domains=true;
		}
		if ( count($urls_list) !=0 ) {
			echo "<h4>Les urls ci-dessous sont </h4>";
			// Lecture du fichier domains
			$file_urls=read_white_list ("urls");
			// Recherche si les éléments de $domains_list sont présents dans $file_domains
			// et constitution des fichiers domains et domains.diff
			$fp=@fopen($path2wl."urls.diff","w");
			$fp1=@fopen($path2wl."urls","a");
			for ( $loop=0; $loop < count($urls_list); $loop++ ) {
				if ( @in_array ($urls_list[$loop], $file_urls) )
   					print "<li>".$urls_list[$loop]." déja dans la liste blanche<br>\n";
  				else {
					if ($fp) fwrite($fp,"+".$urls_list[$loop]."\n");
					if ($fp1) fputs($fp1,$urls_list[$loop]."\n");
					print "<li>".$urls_list[$loop]." ajouté à la liste blanche<br>\n";
				}
			}
			@fclose($fp);	
			@fclose($fp1);				
			$add_urls=true;
		}
		if ( $add_domains || $add_urls ) {			
			// Modification de la whitelist LCS pour AJOUTs
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
		$fp=@fopen($path2wl."domains.diff","w");
		$fp1=@fopen($path2wl."urls.diff","w");
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
		echo "</ul>\n<h4>ont été supprimées de la «liste blanche» LCS.</h4>\n";
		if ( count($list_del_domains) > 0 ) {
			$domain_file = read_white_list ("domains");
			$result_domains = array_diff($domain_file, $list_del_domains);
			unlink ($path2wl."domains");
			$fp=@fopen($path2wl."domains","a");
			fputs($fp,"# base domains lcs\n");
			for ( $loop=0; $loop < count($domain_file); $loop++ ) {
				if ( $result_domains[$loop] != "" )  fputs($fp,$result_domains[$loop]."\n");
			}
			@fclose($fp);
		}
		if ( count($list_del_urls) > 0 ) {
			$urls_file = read_white_list ("urls");		
			$result_urls = array_diff($urls_file, $list_del_urls);
			unlink ($path2wl."urls");
			$fp=@fopen($path2wl."urls","a");
			fputs($fp,"# base urls lcs\n");
			for ( $loop=0; $loop < count($urls_file); $loop++ ) {
				if ( $result_urls[$loop] != "" )  fputs($fp,$result_urls[$loop]."\n");
			}
			@fclose($fp);
		}
		// Modification de la whitellist LCS pour SUPPRESSION
		exec ("/usr/bin/sudo /usr/share/lcs/scripts/squidGuard.sh lcs");
                exec ("/usr/bin/sudo /usr/share/lcs/scripts/squidGuard.sh reload");		
										
	}

} else {
	echo "<div class=error_msg>Cette application, nécessite les droits d'administrateur du serveur LCS !</div>\n";
}
include ("../lcs/includes/pieds_de_page.inc.php");
?>
