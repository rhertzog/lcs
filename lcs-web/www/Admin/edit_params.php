<?
/* =============================================
   Projet LCS-SE3
   Administration serveur LCS �Editions des parametres�
   AdminLCS/edit_params.php
   Equipe Tice acad�mie de Caen
   derniere mise a jour : 23/05/2008
   Distribu� selon les termes de la licence GPL
   ============================================= */

if ($SCRIPT_NAME != "/setup/index.php") {
	include ("../lcs/includes/headerauth.inc.php");
	include ("../Annu/includes/ldap.inc.php");
	$msgIntro = "<H1>Param�trage g�n�ral LCS</H1>\n";
	list ($idpers, $login)= isauth();
	if (ldap_get_right("lcs_is_admin",$login)!="Y")
        die (gettext("Vous n'avez pas les droits suffisants pour acc�der � cette fonction")."</BODY></HTML>");
} else {
	// mode sans echec
	include ("../lcs/includes/headerauth.inc.php");
	$msgIntro = "<H1>Param�trage g�n�ral LCS (mode sans �chec)</H1>\n";
	@mysql_select_db($DBAUTH) or die("Impossible de se connecter � la base $DBAUTH.");
}

	echo "<HTML>\n";
	echo "	<HEAD>\n";
	echo "		<TITLE>...::: Interface d'administration Serveur LCS :::...</TITLE>\n";
	echo "		<LINK  href='../Annu/style.css' rel='StyleSheet' type='text/css'>\n";
	echo "	</HEAD>\n";
    	echo "	<BODY>\n";
	echo $msgIntro;

function mktable($title, $content) {
	echo "<H3>$title</H3>\n";
	echo $content;
}

// Affiche le formulaire des param�tres correspondant � la cat�gorie $cat
function aff_param_form($cat)
{
	$texte_form="<TABLE BORDER=\"1\">";
	$result=mysql_query("SELECT * from params WHERE cat=$cat ORDER BY `id`");
	if ($result) {
		while ($r=mysql_fetch_array($result)) {
			$texte_form .= "<TR><TD COLSPAN=\"2\">".$r["descr"]." (<EM><FONT color=\"red\">".$r["name"]."</FONT></EM>)</TD>";
			if ( $r["name"] == "ldap_port" ) {
				$texte_form .= "<TD>".$r["value"]."</TD></TR>\n";
				$texte_form .= "<INPUT TYPE=\"hidden\" SIZE=\"25\" VALUE=\"".$r["value"]."\" NAME=\"form_".$r["name"]."\">";
			} else
				$texte_form .= "<TD><INPUT TYPE=\"text\" SIZE=\"50\" VALUE=\"".$r["value"]."\" NAME=\"form_".$r["name"]."\"</TD></TR>\n";
		}
	}
	$texte_form .= "</TABLE>";
	return $texte_form;
}

if (!isset($cat)) $cat=0;

if ((!isset($submit)) and (!isset($queri))) {
// Affichage du form de mise � jour des param�tres
	print "<FORM METHOD=\"post\">\n";
	if ( $cat==1 )
		mktable("Param�tres serveur",aff_param_form(1));
	if ( $cat==2 )
		mktable("Param�tres LDAP",aff_param_form(2));
	if ( $cat==3 )
		mktable("Param�tres R�seau",aff_param_form(3));
	if ( $cat==4 )
        	mktable("Param�tres VLANS",aff_param_form(5));
	if ( $cat==5 )
        	mktable("Certificats SSL",aff_param_form(5));
	if ( $cat==10 )
        	mktable("Param�tres cach�s",aff_param_form(10));

	print "<BR><DIV ALIGN=\"center\"><INPUT TYPE=\"submit\" VALUE=\"".gettext("Valider")."\"></DIV>";
	print "<INPUT TYPE=\"hidden\" VALUE=\"$cat\" NAME=\"submit\">";
	print "</FORM>\n";
}

if (isset($submit)) {
// Traitement du Form

// D�tection des param�tres modifi�s et fabrication de la requete de mise a jour
	if ( ( file_exists ("/tmp/params_lcs") ) ||  !( $fp = fopen("/tmp/params_lcs", "w") ) )
		die (gettext("Cr�ation du fichier de passage des parametres impossible. Recommencez plus tard et assurez-vous qu'aucun fichier params_lcs n'est pr�sent dans ")."/tmp.");
	$query="SELECT * from params";
	if ($submit != 0) $query .= " WHERE cat=$submit";
	$result=mysql_query($query);
	if ($result) {
		$i=0;
		while ($r=mysql_fetch_array($result)) {
			$formname="form_".$r["name"];
			if ($$formname!=$r["value"]) {
			// Mise � jour de la base de donn�es
				$queri="UPDATE params SET value=\"".$$formname."\" WHERE name=\"".$r["name"]."\"";
				$result1=mysql_query($queri);
				if ($result1)
					print gettext("Modification du param�tre ").
					"<EM><FONT color=\"red\">".$r["name"].
					"</FONT></EM> ". gettext("de ")."<STRONG>".$r["value"].
					"</STRONG>".gettext(" en ")."<STRONG>".$$formname."</STRONG>"."<BR>\n";
				else
					print gettext("oops: la requete ") . "<STRONG>$queri</STRONG>" . gettext(" a provoqu� une erreur");
				// R�cup�ration des variables qui ont chang�es et qui n�cessitent une modification des fichiers de conf
				if (($r["cat"]==2) && ($r["name"] != "pla_bind")) {
                    			if ($r["name"]=="adminPw") {
                        			$ldappass_old=$r["value"];
                        			$ldappass_new=$$formname;
						fwrite ($fp,"adminPw	$ldappass_old	$ldappass_new\n");
                    			}
					if ($r["name"]=="ldap_server") {
						$ldap_server_old = $r["value"];
						$ldap_server_new = $$formname;
						fwrite ($fp,"ldap_server	$ldap_server_old	$ldap_server_new\n");
					} else if ($r["name"]=="ldap_port") {
						$ldap_port_old = $r["value"];
						$ldap_port_new = $$formname;
						fwrite ($fp,"ldap_port	$ldap_port_old	$ldap_port_new\n");
					} else if ($r["name"]=="ldap_base_dn") {
						$ldap_base_dn_old = $r["value"];
						$ldap_base_dn_new = $$formname;
						fwrite ($fp,"ldap_base_dn	$ldap_base_dn_old	$ldap_base_dn_new \n");
					} else if ($r["name"]=="adminRdn") {
						$ldap_adminRdn_old = $r["value"];
						$ldap_adminRdn_new = $$formname;
						fwrite ($fp,"adminRdn	$ldap_adminRdn_old	$ldap_adminRdn_new\n");
					}
				}

				// olecam 15 dec 2006: signaler les modifs sur se3Ip et se3netbios afin de mettre
                                // � jour /etc/samba/lmhosts
				if ($r["cat"]==1) {
                                        if ($r["name"]=="se3Ip") {
                                                $se3Ip_old = $r["value"];
                                                $se3Ip_new = $$formname;
                                                fwrite ($fp,"se3Ip      $se3Ip_old      $se3Ip_new\n");
                                        }else if ($r["name"]=="se3netbios") {
                                                $se3netbios_old = $r["value"];
                                                $se3netbios_new = $$formname;
                                                fwrite ($fp,"se3netbios $se3netbios_old      $se3netbios_new\n");
                                        }
                                }
/*
				if ($r["cat"]==1) {
					if ($r["name"]=="se3Ip" || $r["name"]=="se3netbios") {
						# Il faudra mettre � jour le fichier /etc/samba/lmhosts
						$update_lmhosts=1;
					}
				}
*/
				// Mise � jour des variables du config
				$$r["name"]=$$formname;
				$i++;
			}
		}

		if ($i == 0) print gettext("Aucun param�tre n'a �t� modifi�\n");

		mysql_free_result($result);
	} else print gettext ("oops: Erreur inattendue de lecture des anciens param�tres\n");
	fclose($fp);
	// Effacement du fichier si rien n'y a �t� inscrit
	if (filesize("/tmp/params_lcs")!=0) {
	        # execution du script de modification
		exec ("/usr/bin/sudo /usr/share/lcs/scripts/edit_params.sh");
		print gettext("les fichiers de configuration ont �t� modifi�s afin de prendre en compte les nouveaux param�tres.");
	} else unlink ("/tmp/params_lcs");
}

include ("../lcs/includes/pieds_de_page.inc.php");
?>
