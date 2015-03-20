<?php
/*===========================================
   Projet LcSE3
   Equipe Tice academie de Caen
   Distribue selon les termes de la licence GPL
   Derniere modification : 19/03/2015
   ============================================= */
// Cle privee pour cryptage du cookie LCSuser dans fonction open_session()
include ("/var/www/lcs/includes/private_key.inc.php");
include ("/var/www/lcs/includes/xoft.php");

    #############
    # CAS Section

    function get_rand_letters($length=29){
       $MAX = 4294619050;
       mt_srand(time());
       $r = (integer) time();
       $r .= 'r';
       for ($x=0;$x<8;$x++) {
           $r .= mb_strtoupper(dechex($r % mt_rand(1,$MAX)));
       }
       return mb_substr($r,0,$length);
    }


    function redirect_2($url) {

  	echo "<script language=\"JavaScript\" type=\"text/javascript\">\n";
        echo "<!--\n";
        echo "top.location.href = '$url';\n";
        echo "//-->\n";
        echo "</script>\n";

	}

    function redirect2($url)
	{
		@MYSQL_CLOSE();

		echo '
		<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
		   "http://www.w3.org/TR/html4/loose.dtd">
		<html>
		<head>
		<title>..::LCS::..</title
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta http-equiv="refresh" content="0; url='.$url.'">
		</head>
		<body>
                </body>
		</html>';
		exit();
	}
    ######### END CAS section

    function decodekey($key)
    {
        exec ("/usr/bin/python /usr/share/lcs/privatekey/decode.py '$key' 2>/dev/null",$result,$ReturnValue);
        return $result[0];
    }

    function remote_ip()
    {
      if(getenv("HTTP_CLIENT_IP")) {
        $ip = getenv("HTTP_CLIENT_IP");
      } elseif (getenv("HTTP_X_FORWARDED_FOR")) {
        $ip = getenv("HTTP_X_FORWARDED_FOR");
      } else {
        $ip = getenv("REMOTE_ADDR");
      }
      return $ip;
    }

    function is_eleve ($login) {
        global $ldap_server, $ldap_port, $dn;
        global $error;
        $error="";

        $filter = "(&(cn=eleves*)(memberUid=$login))";
        $ldap_groups_attr = array (
        // LDAP attribute
                "cn",
                "memberUid"    // Membre du Group Profs, Eleves, Administration
        );

        /*-----------------------------------------------------*/
        $ds = @ldap_connect ( $ldap_server, $ldap_port );
        if ( $ds ) {
                $r = @ldap_bind ( $ds );
                if (!$r) {
                        $error = "Echec du bind anonyme";
                } else {
                        // Recherche du groupe d'appartenance de l'utilisateur connect�
                        $result=@ldap_list ($ds, $dn["groups"], $filter, $ldap_groups_attr);
                        if ($result) {
                                $info = @ldap_get_entries( $ds, $result );
                                if ($info["count"]) {
                                        $is_eleve = true;
                                } else {
                                        $is_eleve = false;
                                }
                        }
                }
        }
        @ldap_unbind ($ds);
        @ldap_close ($ds);
        return $is_eleve;
    }
    


    
    function open_session($login, $passwd, $cryptpasswd)
    { 
    	/* Verifie le login et le mot de passe sur l'annuaire ldap
          ouvre une session et cree le home et la bdd en cas de succes
          Renvoie :
                * true en cas de succes de la creation du home et de la bdd
                * false dans les autres cas
		*/
		
		global $urlauth, $scriptsbinpath, $authlink, $DBAUTH, $key_priv, $auth_mod;
		
		if ( user_valid_passwd ( $login , $passwd ) ) { 
			/* Le mot de passe de $login est valide
			   On ouvre une session LCS */
			
			// Ouverture de la session php Lcs
			session_name("Lcs");
			@session_start();
			$_SESSION['login'] = $login;
			$_SESSION['token'] = rand();
			// Creation du cookie LCSuser pour roundcube, pma...
			setcookie("LCSuser", xoft_encode( urlencode($passwd) ,$key_priv), 0,"/","",0);
			// Creation Espace Perso Utilisateur
			if ( !@is_dir("/home/".$login) ||  (@is_dir("/home/".$login) && ( !@is_dir("/home/".$login."/public_html") || !@is_dir("/home/".$login."/Maildir") || !@is_dir("/home/".$login."/Documents") || !@is_dir("/home/".$login."/Profile"))) ) {
				#system ("echo \"DBG >> Creation Espace perso\" >> /tmp/log.lcs");
				$group=strtolower(people_get_group ($login));
				exec ("/usr/bin/sudo /usr/share/lcs/scripts/mkhdir.sh ".escapeshellarg($login)." '$group' '$cryptpasswd' > /dev/null 2>&1");
			} else {
				// Verification acces bdd et reinitialisation le cas echeant
                #system ("echo \"DBG >> Verif. acces mysql $login $passwd\" >> /tmp/log.lcs");
                @mysql_close();
                @mysql_connect("localhost", $login, $passwd );
                if ( mysql_error() ) {
					exec ( escapeshellarg("$scriptsbinpath/mysqlPasswInit.pl")." ". escapeshellarg($login) ." ". escapeshellarg($passwd) );
                    #system ("echo \"DBG >> Reinit mdp mysql $login $passwd\" >> /tmp/log.lcs");
				}
				@mysql_close();
            }
            return true;
		} 
		return false;
    } 
    
    
    
    function close_session()
    {
    	
		/* Ferme la session LCS */
		
		global $authlink, $DBAUTH,$Nom_Appli, $VER;
				
		if (!@mysql_select_db($DBAUTH, $authlink))
			die ("S&#233;lection de base de donn&#233;es impossible.");
			

		//Destruction session php Lcs
		session_name("Lcs");
		@session_start();
		// On detruit toutes les variables de session
		$_SESSION = array();
		// On detruit la session sur le serveur.
		session_destroy();
			
		// Destruction des cookies LCSuser
		setcookie("LCSuser","", 0,"/","",0);
		// Destruction du cookie smbwebclient
		setcookie("SmbWebClientID","", 0,"/","",0);
		// Destruction cookie tgt service CAS
		$t=$_COOKIE['tgt'];
		if ( isset($t) ) {
			$t= mysql_real_escape_string($t);
			$query="DELETE from casserver.casserver_tgt where ticket='$t'";
			$result=@mysql_query($query) or die($query);
			setcookie("lt","", 0,"/","",0);
			setcookie("tgt","", 0,"/","",0);
		}
		// Destruction des cookies Plugins LCS
		$query="SELECT chemin from applis where ( type='P' OR type='N' ) and value='1'";
		$result=@mysql_query($query);
		if ($result) {
			while ($r=@mysql_fetch_object($result)) {
				$close_session_require = "/usr/share/lcs/Plugins/".$r->chemin."/Includes/close_session_plugin.php";
				if ( file_exists($close_session_require) )  {
					require ($close_session_require);
				}
			}
		}
    }

function ldap_get_right_search ($type,$search_filter,$ldap)
// Recherche si $nom est present dans le droit $type
{
	global $dn;
	$ret="N";
	$base_search="cn=$type," . $dn["rights"];
	$search_attributes=array("cn");
	$result = ldap_read($ldap, $base_search, $search_filter, $search_attributes);
	if ($result) {
		if (ldap_count_entries ($ldap,$result) == 1) $ret="Y";
		ldap_free_result($result);
	}
	return $ret;
}


function ldap_get_right($type,$login)
// Determine si $login a le droit $type
{
    global $ldap_server, $ldap_port, $adminDn, $adminPw, $dn;

    $nom="uid=" . $login . "," . $dn["people"];
    $ret="N";
    $ldap = ldap_connect ($ldap_server, $ldap_port);
    if ( !$ldap ) {
        echo "Error connecting to LDAP server";
    } else {
        if ( $adminDn != "") {
            $r = ldap_bind ( $ldap, $adminDn, $adminPw );     // bind as administrator
        } else {
            $r = ldap_bind ( $ldap ); // bind as anonymous
        }
        if (!$r) {
            echo "Invalid Admin's login for LDAP Server";
        } else {

            // Recherche du nom exact
            $search_filter = "(member=$nom)";
            $ret=ldap_get_right_search ($type,$search_filter,$ldap);
            if ($ret=="N") {
                // Recherche sur les Posixgroups d'appartenance
            	$result1 = @ldap_list ( $ldap, $dn["groups"], "memberUid=$login", array ("cn") );
            	if ($result1) {
                    $info = @ldap_get_entries ( $ldap, $result1 );
                    if ( $info["count"]) {
                        $loop=0;
                        while (($loop < $info["count"]) && ($ret=="N")){
                            $search_filter = "(member=cn=".$info[$loop]["cn"][0].",".$dn["groups"].")";
                            $ret=ldap_get_right_search ($type,$search_filter,$ldap);
                            $loop++;
                	}
                    }
                    @ldap_free_result ( $result1 );
                }
            }
            if ($ret=="N") {
                // Recherche sur les GroupsOfNames d'appartenance
            	$result1 = @ldap_list ( $ldap, $dn["groups"], "member=uid=$login,".$dn["people"], array ("cn") );
            	if ($result1) {
                    $info = @ldap_get_entries ( $ldap, $result1 );
                    if ( $info["count"]) {
                        $loop=0;
                        while (($loop < $info["count"]) && ($ret=="N")){
                            $search_filter = "(member=cn=".$info[$loop]["cn"][0].",".$dn["groups"].")";
                            $ret=ldap_get_right_search ($type,$search_filter,$ldap);
                            $loop++;
                	}
            	   }
            	   @ldap_free_result ( $result1 );
            	}
            }
        }
        ldap_close ($ldap);
    }
    return $ret;
}

function getmenuarray()
{
    global $liens;

    // Chargement du tableau menu.d
    $path2menud="/var/www/lcs/includes/mnu.d";
    $folders =  array();
    $folders[0]=""; // 1ere element vide pour rester compatible avec les boucles de menuprint
    $namesfolders = scandir($path2menud);
    for ($t=0; $t < count($namesfolders); $t++ ) {
        if ( ! mb_ereg ( "^\.", $namesfolders[$t] ) ) {
            $countfiles=scandir($path2menud."/".$namesfolders[$t]);
            if ( count($countfiles) > 3 ) // On retiend le nom du repertoire si il y a des fichiers sous menu dedans
                $folders[] = $namesfolders[$t];
        }
    }

    $liens[0]="";
    for ($i=1; $i<count($folders); $i++) {
        $filesdd = array();
        $dh2  = opendir($path2menud."/".$folders[$i]);
        while (false !== ($namesfiles = readdir($dh2)))
            if ( ! mb_ereg ( "^\.", $namesfiles ) )
                $filesdd[] = $namesfiles;
        sort($filesdd);

        $loop=0;
        for ($j=0; $j< count($filesdd); $j++) {
            $fd = fopen($path2menud."/".$folders[$i]."/".$filesdd[$j], "r");
            while ( !feof($fd) ) {
                $tmp=fgets($fd, 125);
                if ( mb_strlen($tmp) > 0 ) {
                    $element = explode(",",$tmp);
                    for ($k=0; $k < count($element); $k++) {
                        if ($loop>0) {
                            if ( $k==1 && strstr($element[$k], "?")) {
                                $url=  explode("?", $element[$k]);
                                $liens[$i][$loop] = $element[$k].'&jeton='.md5($_SESSION['token'].htmlentities("/Admin/".$url[0]));
                                }
                             else    $liens[$i][$loop] = $element[$k];
                        }
                        else   $liens[$i][$loop] = $element[$k];
                        $loop++;
                    }
                }
            }
        }
    }
} // Fin function getmenuarray()

function menuprint($login)
{
    global $liens,$menu;

    for ($idmenu=0; $idmenu<count($liens); $idmenu++)
    {
        echo "<div id=\"menu$idmenu\" style=\"position:absolute; left:10px; top:12px; width:205px; z-index:" . $idmenu ." ";
        if ($idmenu!=$menu) {
            echo "; visibility: hidden";
        }
        echo "\">\n";

        echo "
        <table width=\"205\" border=\"0\" cellspacing=\"3\" cellpadding=\"6\">\n";
		$ldapright["lcs_is_admin"]=ldap_get_right("lcs_is_admin",$login);

        for ($menunbr=1; $menunbr<count($liens); $menunbr++)
        {
		// Test des droits pour affichage
			$afftest=$ldapright["lcs_is_admin"]=="Y";
			$rightname=$liens[$menunbr][1];
			if (($rightname=="") or ($afftest)) $afftest=1==1;
			else {
				if ($ldapright[$rightname]=="") $ldapright[$rightname]=ldap_get_right($rightname,$login);
				$afftest=($ldapright[$rightname]=="Y");
			}
			if ($afftest)
                        if (($idmenu==$menunbr)&&($idmenu!=0)) {
	            echo "
                <tr>
                    <td class=\"menuheader_up\">
                        <p>
			  <a class=\"menuheader_up\" href=\"javascript:;\"
			     onClick=\"P7_autoLayers('menu0');return false;\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$liens[$menunbr][0]."
			  </a>
			</p>
                    </td>
                    </tr>
                    <tr>
                    <td class=\"menucell\">";
                for ($i=2; $i<count($liens[$menunbr]); $i=$i+3) {
			// Test des droits pour affichage
					$afftest=$ldapright["lcs_is_admin"]=="Y";
					$rightname=$liens[$menunbr][$i+2];
					if (($rightname=="") or ($afftest)) $afftest=1==1;
					else {
						if ($ldapright[$rightname]=="") $ldapright[$rightname]=ldap_get_right($rightname,$login);
						$afftest=($ldapright[$rightname]=="Y");
					}
					if ($afftest)
                    echo "
                        <img src=\"../lcs/images/menu/typebullet.gif\" width=\"30\" height=\"11\">
                            <a href=\"" . $liens[$menunbr][$i+1] . "\" TARGET='main'>" . $liens[$menunbr][$i]  . "</a><br>\n";
                } // for i : boucle d'affichage des entrees de sous-menu
                echo "
                    </td></tr>\n";
            } else
            {
                echo "
                <tr>
                    <td class=\"menuheader_down\">
                    <p>
		      <a class=\"menuheader_down\" href=\"javascript:;\"
			onClick=\"P7_autoLayers('menu" . $menunbr .  "');return false;\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$liens[$menunbr][0]."
		      </a>
		    </p>
                    </td></tr>\n";
            }
        } //for menunbr : boucle d'affichage des entrees de menu principales

        echo "
        </table>
</div>\n";
    } // for idmenu : boucle d'affichage des differents calques
} // function menuprint

function acces_btn_admin ($login_recu)
// Test si l'utilisateur authentifie possede les droits pour acceder au bouton d'administration
{
	global $liens;

	getmenuarray();

	// A partir d'ici on a un identifiant
	if ( ldap_get_right("lcs_is_admin",$login_recu) == "Y" ) { // l'utilisateur a les droits lcs_is_admin : il a toujours access
		return ("Y");
	}

   // Test des droits des menus et sous-menus pour les utilisateurs sans les droits lcs_is_admin
   // L'utilisateur a acces des qu'il a un de ces droits

   // boucle sur les menus
	for ($menunbr=1; $menunbr<count($liens); $menunbr++) {
        // Test sur le menu
        $rightname=$liens[$menunbr][1];
        if ( ($rightname=="") or (ldap_get_right($rightname,$login_recu)=="Y") ) {
			// pas de droits necessaires ou alors l'utilisateur a la permission
	    	return ("Y");
        }
        //boucle sur les sous-menus
		 for ($i=2; $i<count($liens[$menunbr]); $i+=3) {
	    	// Test sur le sous-menu
	    	$rightname_smenu=$liens[$menunbr][$i+2];
	    	if ( ($rightname_smenu=="") or (ldap_get_right($rightname_smenu,$login_recu)=="Y") ) {
                // pas de droits necessaires ou alors l'utilisateur a la permission
                return ("Y");
			}
		}	 // boucle sur les sous-menus
	} // boucle sur les menus
	// on a parcouru tous les menus et sous-menus et l'utilisateur n'a pas les droits
	return ("N");
} // Fin fonction acces_btn_admin

/**
 * Recursively load hooks from /usr/share/lcs/lcs-web-hooks.d/. The directory
 * structure is hook_name/file.php. "hook_name" must be a supported valid
 * hook name, only "post_auth" is currently supported. file.php must contain at
 * least one procedural function. The function name format is
 * hook_<hook_name>_<file>().
 *
 * For example, /usr/share/lcs/lcs-web-hooks.d/post_auth/update_zonep_config.php
 * contains a function called "hook_post_auth_update_zonep_config()" that will
 * be called during "post_auth" hook.
 *
 * @param hook_name string. The name of the hook. For example "post_auth".
 *
 * @param hook_base_dir string. The directory where the hooks are stored.
 *
 * @return status bool. True on success, false otherwise
 */
function lcs_web_load_hook($hook_name = null,
			   $hook_base_dir = '/usr/share/lcs/lcs-web-hooks.d') {
	global $lcs_hooks;

	if (!isset($lcs_hooks[$hook_name]))
		$lcs_hooks[$hook_name] = array();

	// Check arguments
	if(!is_string($hook_name)
	   or empty($hook_name)
	   or !is_string($hook_base_dir)
	   or empty($hook_base_dir)
	   or !file_exists($hook_base_dir)
	   or !is_dir($hook_base_dir)
	   or !is_dir("$hook_base_dir/$hook_name"))
		return false;

	if (!$handle_hookdir = opendir("$hook_base_dir/$hook_name"))
		return false;

	// Read hook directory
	while (false !== ($hook_file = readdir($handle_hookdir))) {
		// Ignore non files entries (directories, sockets, etc.)
		if(!is_file("$hook_base_dir/$hook_name/$hook_file"))
			continue;

		// Check hook file name syntax
		if(!preg_match('/^(\w+)\.php$/', $hook_file, $matches))
			continue;

		// Load hooked function
		require_once("$hook_base_dir/$hook_name/$hook_file");

		// Add hooked function to the hook if it's correctly defined
		$hook_function = "hook_" . $hook_name . "_" . $matches[1];

		if(function_exists($hook_function))
			$lcs_hooks[$hook_name][] = $hook_function;
	}

	closedir($handle_hookdir);
	return true;
}

/**
 * Run the hook specified by $hook_name
 *
 * @param hook_name string. Defines the name of the hook to call
 *
 * @param parameters array. An array containing the parameters to call the hook
 * with.
 *
 * @return status bool. True on success, false otherwise
 */
function lcs_web_run_hook($hook_name = null, array $parameters = array()) {
	global $lcs_hooks;

	// Check parameters
	if(!is_string($hook_name) or empty($hook_name)
	   or !is_array($parameters) or empty($parameters))
		return false;

	// Load hooks if needed
	if(!isset($lcs_hooks[$hook_name]))
		lcs_web_load_hook($hook_name);

	foreach($lcs_hooks[$hook_name] as $hook)
		call_user_func_array($hook, $parameters);

	return true;
}

?>
