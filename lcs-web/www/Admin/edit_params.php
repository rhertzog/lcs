 <?
/* Admin/edit_params.php derniere mise a jour : 16/03/2012 */

if ($_SERVER['SCRIPT_NAME'] != "/setup/index.php") {
	include ("../lcs/includes/headerauth.inc.php");
	include ("../Annu/includes/ldap.inc.php");
	$msgIntro = "<h1>Param&#233;trage g&#233;n&#233;ral LCS</h1>\n";
	list ($idpers, $login)= isauth();
	if (ldap_get_right("lcs_is_admin",$login)!="Y")
        die (gettext("Vous n'avez pas les droits suffisants pour acc&#233;der &#224; cette fonction")."</body>\n</html>\n");
} else {
	// mode sans echec
	include ("../lcs/includes/headerauth.inc.php");
	$msgIntro = "<h1>Param&#233;trage g&#233;n&#233;ral LCS (mode sans &#233;chec)</h1>\n";	
	@mysql_select_db($DBAUTH) or die("Impossible de se connecter &#224; la base $DBAUTH.");
}

if ( isset($_POST['cat']))  $cat = $_POST['cat'];
elseif ( isset($_GET['cat'])) $cat = $_GET['cat'];
$submit= $_POST['submit'];

	echo "<html>\n";
	echo "	<head>\n";
	echo "		<title>...::: Interface d'administration Serveur LCS :::...</title>\n";
    echo "		<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />\n";
	echo "		<link  href='../Annu/style.css' rel='StyleSheet' type='text/css'>\n";
	echo "	</head>\n";
    	echo "	<body>\n";
	echo $msgIntro;

function mktable($title, $content) {
	echo "<h3>$title</h3>\n";
	echo $content;
}

// Affiche le formulaire des parametres correspondant a la categorie $cat
function aff_param_form($cat)
{
        if ( $cat==0 || $cat==11 )
            $texte_form = "<p><a href='../openvpn/config.php' title='Configuration Open VPN'>Configuration Open VPN</a></p>";
	$texte_form .= "<table border=\"1\">\n";
	$result=mysql_query("SELECT * from params WHERE cat=$cat ORDER BY `id`");
	if ($result) {
		while ($r=mysql_fetch_array($result)) {
			$texte_form .= "<tr><td colspan=\"2\">".utf8_encode($r["descr"])." (<em><font color=\"red\">".$r["name"]."</font></em>)</td>";
			if ( $r["name"] == "ldap_port" || $cat == 3 || $cat == 11 || $cat == 5 ) {
				$texte_form .= "<td>".$r["value"]."</td></tr>\n";
				$texte_form .= "<input type=\"hidden\" size=\"25\" value=\"".$r["value"]."\" name=\"form_".$r["name"]."\">";
			} else
				$texte_form .= "<td><input type=\"text\" size=\"50\" value=\"".$r["value"]."\" name=\"form_".$r["name"]."\"</td></tr>\n";
		}
	}
	$texte_form .= "</table>\n";

	return $texte_form;
}

if (!isset($cat)) $cat=0;

if ((!isset($submit)) and (!isset($queri))) {
// Affichage du form de mise a jour des parametres
	print "<form  method=\"post\">\n";
	if ( $cat==0 || $cat==1 )
		mktable("Param&#232;tres serveur",aff_param_form(1));
	if ( $cat==0 || $cat==2 )
		mktable("Param&#232;tres LDAP",aff_param_form(2));
	if ( $cat==0 || $cat==6 )
		mktable("Param&#232;tres ENT",aff_param_form(2));
	if ( $cat==0 || $cat==3 )
		mktable("Param&#232;tres R&#233;seau",aff_param_form(3));
	if ( $cat==0 || $cat==4 )
        	mktable("Param&#232;tres VLANS",aff_param_form(4));
	if ( $cat==0 || $cat==10 ) 
        	mktable("Param&#232;tres cach&#233;s",aff_param_form(10));
	if ( $cat==0 || $cat==11 )
        	mktable("Param&#232;tres Open VPN",aff_param_form(11));
	if ( $cat==0 || $cat==5 )
        	mktable("Certificats SSL",aff_param_form(5));

	print "<br /><div align=\"center\"><input type=\"submit\" value=\"".gettext("Valider")."\"></div>";
	print "<input type=\"hidden\" value=\"$cat\" name=\"submit\">";
	print "</form>\n";
}

if (isset($submit)) {
// Traitement du Formulaire
// Detection des parametres modifies et fabrication de la requete de mise a jour
	if ( ( file_exists ("/tmp/params_lcs") ) ||  !( $fp = fopen("/tmp/params_lcs", "w") ) )
		die (gettext("Cr&#233;ation du fichier de passage des parametres impossible. Recommencez plus tard et assurez-vous qu'aucun fichier params_lcs n'est pr&#233;sent dans ")."/tmp.");
	$query="SELECT * from params";
	if ($submit != 0) $query .= " WHERE cat=$submit";
	$result=mysql_query($query);
	if ($result) {
		$i=0;
		while ($r=mysql_fetch_array($result)) {
			$formname="form_".$r["name"];
			if ($_POST[$formname]!=$r["value"] ) {
			// Mise a jour de la base de donneees
				$queri="UPDATE params SET value=\"".$_POST[$formname]."\" WHERE name=\"".$r["name"]."\"";
				$result1=mysql_query($queri);
				if ($result1) 
					print gettext("Modification du param&#232;tre ").
					"<em><font color=\"red\">".$r["name"].
					"</font></em> ". gettext("de ")."<strong>".$r["value"].
					"</strong>".gettext(" en ")."<strong>".$_POST[$formname]."</strong>"."<br />\n";

				else
					print gettext("oops: la requete ") . "<strong>$queri</strong>" . gettext(" a provoqu&#233; une erreur");
				// Recuperation des variables qui ont change et qui necessitent une modification des fichiers de conf
				if (($r["cat"]==2) && ($r["name"] != "pla_bind")) {
                    			if ($r["name"]=="adminPw") {
                        			$ldappass_old=$r["value"];
                        			$ldappass_new=$_POST[$formname];
						fwrite ($fp,"adminPw	$ldappass_old	$ldappass_new\n");
                    			}
					if ($r["name"]=="ldap_server") {
						$ldap_server_old = $r["value"];
						$ldap_server_new = $_POST[$formname];
						fwrite ($fp,"ldap_server	$ldap_server_old	$ldap_server_new\n");
					} else if ($r["name"]=="ldap_port") {
						$ldap_port_old = $r["value"];
						$ldap_port_new = $_POST[$formname];
						fwrite ($fp,"ldap_port	$ldap_port_old	$ldap_port_new\n");
					} else if ($r["name"]=="ldap_base_dn") {
						$ldap_base_dn_old = $r["value"];
						$ldap_base_dn_new = $_POST[$formname];
						fwrite ($fp,"ldap_base_dn	$ldap_base_dn_old	$ldap_base_dn_new \n");
					} else if ($r["name"]=="adminRdn") {
						$ldap_adminRdn_old = $r["value"];
						$ldap_adminRdn_new = $_POST[$formname];
						fwrite ($fp,"adminRdn	$ldap_adminRdn_old	$ldap_adminRdn_new\n");
					}
				}

				// olecam 15 dec 2006: signaler les modifs sur se3Ip et se3netbios afin de mettre
                                // a jour /etc/samba/lmhosts
				if ($r["cat"]==1) {
                                        if ($r["name"]=="se3Ip") {
                                                $se3Ip_old = $r["value"];
                                                $se3Ip_new = $_POST[$formname];
                                                fwrite ($fp,"se3Ip      $se3Ip_old      $se3Ip_new\n");
                                        } else if ($r["name"]=="se3netbios") {
                                                $se3netbios_old = $r["value"];
                                                $se3netbios_new = $_POST[$formname];
                                                fwrite ($fp,"se3netbios $se3netbios_old      $se3netbios_new\n");
                                        }
                                }
				// Mise a jour des variables du config
				$$r["name"]=$_POST[$formname];
				$i++;
			}
		}
		if ($i == 0) print gettext("Aucun param&#232;tre n'a &#233;t&#233; modifi&#233;\n");
		mysql_free_result($result);
	} else print gettext ("Oops: Erreur inattendue de lecture des anciens param&#232;tres\n");
	fclose($fp);
	// Effacement du fichier si rien n'y a ete inscrit
	if (filesize("/tmp/params_lcs")!=0) {
	        # execution du script de modification
		exec ("/usr/bin/sudo /usr/share/lcs/scripts/edit_params.sh");
		print gettext("Les fichiers de configuration ont &#233;t&#233; modifi&#233;s afin de prendre en compte les nouveaux param&#232;tres.");
 	} else unlink ("/tmp/params_lcs");
}

include ("../lcs/includes/pieds_de_page.inc.php");
?>
