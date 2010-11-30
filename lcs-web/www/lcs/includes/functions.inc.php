<?php
/* functions.inc.php Derniere mise a jour 01/10/2010  */

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

    function dispstats($idpers)
    {
       global $authlink, $DBAUTH;

        if ($idpers):
            /* Renvoie le nombre de connexions */
            //$result=mysql_db_query("$DBAUTH","SELECT stat FROM personne WHERE id=$idpers", $authlink);
	    if (!@mysql_select_db($DBAUTH, $authlink)) 
    		die ("S&#233;lection de base de donn&#233;es impossible.");
	    $query="SELECT stat FROM personne WHERE id=$idpers";
	    $result=@mysql_query($query,$authlink);
            if ($result && @mysql_num_rows($result)):
                $stat=@mysql_result($result,0,0);
                @mysql_free_result($result);
            else:
                $stat="0";
            endif;
            return $stat;
        endif;
     }

     function displogin ($idpers)
     {
       global $authlink, $DBAUTH;

        if ($idpers):
            /* Renvoie le timestamp du dernier login */
            //$result=mysql_db_query("$DBAUTH","SELECT DATE_FORMAT(last_log,'%d/%m/%Y &agrave; %T' ) FROM personne WHERE id=$idpers", $authlink);
	    if (!@mysql_select_db($DBAUTH, $authlink)) 
    		die ("S&#233;lection de base de donn&#233;es impossible.");
	    $query="SELECT DATE_FORMAT(last_log,'%d/%m/%Y &agrave; %T' ) FROM personne WHERE id=$idpers";
            $result=@mysql_query($query,$authlink);
            if ($result && @mysql_num_rows($result)):
                $der_log=@mysql_result($result,0,0);
                @mysql_free_result($result);
            else:
                $der_log="";
            endif;
            return $der_log;
        endif;
     }

     function set_act_login ($idpers)
     {
       global $authlink, $DBAUTH;
        if ($idpers):
            $date=date("YmdHis");
            //$result=mysql_db_query("$DBAUTH","UPDATE personne SET act_log=$date WHERE id=$idpers", $authlink);
	    if (!@mysql_select_db($DBAUTH, $authlink)) 
    		die ("S&#233;lection de base de donn&#233;es impossible.");
	    $query="UPDATE personne SET act_log=$date WHERE id=$idpers";
            $result=@mysql_query($query,$authlink);
        endif;
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

    function isauth()
    {
        /* Teste si une session est en cours
                - Si non, renvoie un idpers=0
                - Si oui, renvoie idpers : Id de la personne
        */

        global $authlink, $DBAUTH;

        $idpers=0;$login="";
        if (! empty($_COOKIE["LCSAuth"])) {
            $sess=$_COOKIE["LCSAuth"];
            //$result=@mysql_db_query("$DBAUTH","SELECT remote_ip, idpers FROM sessions WHERE sess='$sess'", $authlink);
	    if (!@mysql_select_db($DBAUTH, $authlink)) 
    		die ("S&#233;lection de base de donn&#233;es impossible.");
	    $query="SELECT remote_ip, idpers FROM sessions WHERE sess='$sess'";
            $result=@mysql_query($query,$authlink);
            if ($result && @mysql_num_rows($result) ) {
		// Split ip variable too take first ip only
		// (BDD field is too short on multiple proxy case)
                list($ip_session,$null) = preg_split("/,/",mysql_result($result,0,0),2);
                list($first_remote_ip,$null) = preg_split("/,/",remote_ip(),2);
                if ( $ip_session == $first_remote_ip ) {
                        $idpers =  mysql_result($result,0,1);
                        // Recherche du login a partir de l'idpers
                        $query="SELECT login FROM personne WHERE id=$idpers";
                        //$result=@mysql_db_query("$DBAUTH",$query, $authlink);
			$result=@mysql_query($query,$authlink);
                        if ($result && @mysql_num_rows($result)) $login=str_replace(" ", "", @mysql_result($result,0,0));
                }
                @mysql_free_result($result);
            }
        }
        return array ($idpers,$login);
    }

    function mksessid()
    {
        /* Fabrique un Num de session aleatoire */
        global $Pool, $SessLen,$authlink, $DBAUTH;

        $count=10;
        do
        {
            $sid="";
            $count--;
            for ($i = 0; $i < $SessLen ; $i++)
                $sid .= mb_substr($Pool, (mt_rand()%(mb_strlen($Pool))),1);
	    if (!@mysql_select_db($DBAUTH, $authlink)) 
    		die ("S&#233;lection de base de donn&#233;es impossible.");
            $query="SELECT id FROM sessions WHERE sess='$sid'";
	    //$result=@mysql_db_query("$DBAUTH",$query, $authlink);
            $result=@mysql_query($query,$authlink);
            $res=mysql_num_rows($result);
        }
        while ($res>0 && $count);
        return $sid;
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
        global $urlauth, $scriptsbinpath, $authlink, $DBAUTH, $key_priv;
        // Verifie le couple login/password sur l'annuaire ldap
        $auth_ldap = user_valid_passwd ( $login , $passwd );
        if ($auth_ldap) :
	        if (!@mysql_select_db($DBAUTH, $authlink)) 
    			die ("S&#233;lection de base de donn&#233;es impossible.");
                // Ouvre une session et la stocke dans la table sessions de lcs_db
                $query="SELECT id, stat FROM personne WHERE login='$login'";
                //$result=mysql_db_query("$DBAUTH",$query, $authlink);
                $result=@mysql_query($query,$authlink);
                if ($result && mysql_num_rows($result)):
                        $idpers=mysql_result($result,0,0);
                        $stat=mysql_result($result,0,1)+1;
                        mysql_free_result($result);
                else :
                       // le login n'est pas encore dans la base... creation de l'entree
                        //$result=mysql_db_query("$DBAUTH","INSERT INTO personne  VALUES ('', '', '', '$login', '')", $authlink);
			$query="INSERT INTO personne  VALUES ('', '', '', '$login', '')";
			$result=@mysql_query($query,$authlink);
                        $query="SELECT id, stat FROM personne WHERE login='$login'";
                        //$result=mysql_db_query("$DBAUTH",$query, $authlink);
			$result=@mysql_query($query,$authlink);
                        if ($result && mysql_num_rows($result)):
                                $idpers=mysql_result($result,0,0);
                                $stat=mysql_result($result,0,1)+1;
                                mysql_free_result($result);
                        endif;
                endif;
                $sessid=mksessid();
                //Poste du cookie LCS
                setcookie("LCSAuth", "$sessid", 0,"/","",0);
                //Poste du cookie LCSuser
                setcookie("LCSuser", xoft_encode( urlencode($passwd) ,$key_priv), 0,"/","",0);
                // lecture IP du client
                $ip = remote_ip();
                // Stocke la session et met a jour la table personne avec les stats
                //$result=mysql_db_query("$DBAUTH","INSERT INTO sessions  VALUES ('', '$sessid', '','$idpers','$ip')", $authlink);
                //$result=mysql_db_query("$DBAUTH","UPDATE personne SET stat=$stat WHERE id=$idpers");
		$query="INSERT INTO sessions  VALUES ('', '$sessid', '','$idpers','$ip')";
                $result=@mysql_query($query,$authlink);
                $query="UPDATE personne SET stat=$stat WHERE id=$idpers";
                $result=@mysql_query($query,$authlink);
                set_act_login($idpers);
                // Creation Espace Perso Utilisateur 
                if ( !@is_dir("/home/".$login) ||  (@is_dir("/home/".$login) && 
                                                  ( !@is_dir("/home/".$login."/public_html")
                                                  || !@is_dir("/home/".$login."/Maildir")
                                                  || !@is_dir("/home/".$login."/Documents"))) ) {
                      #system ("echo \"DBG >> Creation Espace perso\" >> /tmp/log.lcs"); 
		      if ( is_eleve($login) ) $group="eleves"; else $group="profs";
		      exec ("/usr/bin/sudo /usr/share/lcs/scripts/mkhdir.sh $login $group $cryptpasswd > /dev/null 2>&1");
                } else { 
                      // Verification acces bdd et reinitialisation le cas echeant
                      #
                      #system ("echo \"DBG >> Verif. acces mysql $login $passwd\" >> /tmp/log.lcs");
                      @mysql_close();
                      @mysql_connect("localhost", $login, $passwd );
                      if ( mysql_error() ) {
                          exec ("$scriptsbinpath/mysqlPasswInit.pl $login $passwd");
                          #system ("echo \"DBG >> Reinit mdp mysql $login $passwd\" >> /tmp/log.lcs");
                      }
                      @mysql_close();
                }      
                return true;
        endif;
        return false;
    }


    function close_session($idpers)
    {
		/* Ferme la session de idpers */
		global $authlink, $DBAUTH,$Nom_Appli, $VER;
	        if (!@mysql_select_db($DBAUTH, $authlink)) 
    			die ("S&#233;lection de base de donn&#233;es impossible.");
		// Destruction des cookies LCS
                setcookie("LCSAuth","", 0,"/","",0);
                setcookie("LCSuser","", 0,"/","",0);
                // Destruction du cookie spip_admin
                setcookie("spip_admin","", 0,"/spip/","",0);
                // Destruction du cookie spip_session
                setcookie("spip_session","", 0,"/spip/","",0);
		// Destruction du cookie admin du Forum
                setcookie(md5($Nom_Appli.$VER."_admin"),"",0,"/","",0);
                // Destruction du cookie smbwebclient
                setcookie("SmbWebClientID","", 0,"/","",0);
                // Destruction cookie tgt service CAS
                $t=$_COOKIE['tgt'];
                if ( isset($t) ) {
	           $query="DELETE from casserver.casserver_tgt where ticket='$t'";
	           $result=@mysql_query($query) or die($query);
	           setcookie("lt","", 0,"/","",0);
	           setcookie("tgt","", 0,"/","",0);
                }
		// Destruction des cookies squirrelmail
	        setcookie("SQMSESSID","", 0,"/","",0);
		setcookie("key","", 0,"/squirrelmail/","",0);
		// Destruction des cookies Plugins LCS
		$query="SELECT chemin from applis where ( type='P' OR type='N' ) and value='1'";
		$result=@mysql_query($query);
		if ($result) {
			while ($r=@mysql_fetch_object($result)) {
				$close_session_require = "/usr/share/lcs/Plugins/".$r->chemin."/Includes/close_session_plugin.php";
				if ( file_exists($close_session_require) )  {
					require ($close_session_require);
					### DBG ###
					### exec ("echo \"$close_session_require\" >> /tmp/lcslog.log");
				}
			}
		}
		// Nettoyage de la session 
        if ($idpers) :
            //mysql_db_query("$DBAUTH","DELETE FROM sessions WHERE idpers=$idpers", $authlink);
	    $query="DELETE FROM sessions WHERE idpers=$idpers";
	    $result=@mysql_query($query,$authlink);
        endif;
        /* update last_log */
        // lecture de act_log
        //$result=mysql_db_query("$DBAUTH","SELECT act_log FROM personne WHERE id=$idpers", $authlink);
	$query="SELECT act_log FROM personne WHERE id=$idpers";
	$result=@mysql_query($query,$authlink);
        if ($result && mysql_num_rows($result)):
          $act_log=@mysql_result($result,0,0);
          @mysql_free_result($result);
        endif;
        // transfert dans last_log
        //$result=mysql_db_query("$DBAUTH","UPDATE personne SET last_log=$act_log WHERE id=$idpers", $authlink);
	$query="UPDATE personne SET last_log=$act_log WHERE id=$idpers";
	$result=@mysql_query($query,$authlink);
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
            $ret=ldap_get_right_search ($type,$search_filter,$ldap,$base_search);
            if ($ret=="N") {
                // Recherche sur les Posixgroups d'appartenance
            	$result1 = @ldap_list ( $ldap, $dn["groups"], "memberUid=$login", array ("cn") );
            	if ($result1) {
                    $info = @ldap_get_entries ( $ldap, $result1 );
                    if ( $info["count"]) {
                        $loop=0;
                        while (($loop < $info["count"]) && ($ret=="N")){
                            $search_filter = "(member=cn=".$info[$loop]["cn"][0].",".$dn["groups"].")";
                            $ret=ldap_get_right_search ($type,$search_filter,$ldap,$base_search,$search_attributes);
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
                            $ret=ldap_get_right_search ($type,$search_filter,$ldap,$base_search,$search_attributes);
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
                        $liens[$i][$loop] = $element[$k];
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

function acces_btn_admin ($idpers_recu, $login_recu) 
// Test si l'utilisateur authentifie possede les droits pour acceder au bouton d'administration
{
    global $liens;

    getmenuarray();
	
    if ( $idpers_recu == "0" ) { // pas d'identifiant : pas d'acces
        return ("N");
    }
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
	} // boucle sur les sous-menus
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
