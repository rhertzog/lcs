<?php
/* =============================================
   Projet LCS : Linux Communication Server
   functions.inc.php
   jean-luc.chretien@tice.ac-caen.fr
   Equipe Tice academie de Caen
   Derniere mise a jour 24/09/2009
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
           $r .= strtoupper(dechex($r % mt_rand(1,$MAX)));
       }
       return substr($r,0,$length);
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

    function dispstats($idpers)
    {
       global $authlink, $DBAUTH;

        if ($idpers):
            /* Renvoie le nombre de connexions */
            $result=mysql_db_query("$DBAUTH","SELECT stat FROM personne WHERE id=$idpers", $authlink);
            if ($result && mysql_num_rows($result)):
                $stat=mysql_result($result,0,0);
                mysql_free_result($result);
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
            $result=mysql_db_query("$DBAUTH","SELECT DATE_FORMAT(last_log,'%d/%m/%Y à %T' ) FROM personne WHERE id=$idpers", $authlink);
            if ($result && mysql_num_rows($result)):
                $der_log=mysql_result($result,0,0);
                mysql_free_result($result);
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
            $result=mysql_db_query("$DBAUTH","UPDATE personne SET act_log=$date WHERE id=$idpers", $authlink);
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
            $result=@mysql_db_query("$DBAUTH","SELECT remote_ip, idpers FROM sessions WHERE sess='$sess'", $authlink);
            if ($result && mysql_num_rows($result) ) {
		// Split ip variable too take first ip only
		// (BDD field is too short on multiple proxy case)
                list($ip_session,$null) = split(",",mysql_result($result,0,0),2);
                list($first_remote_ip,$null) = split(",",remote_ip(),2);
                if ( $ip_session == $first_remote_ip ) {
                        $idpers =  mysql_result($result,0,1);
                        // Recherche du login a partir de l'idpers
                        $query="SELECT login FROM personne WHERE id=$idpers";
                        $result=@mysql_db_query("$DBAUTH",$query, $authlink);
                        if ($result && mysql_num_rows($result)) $login=@mysql_result($result,0,0);
                }
                @mysql_free_result($result);
            }
        }
        return array ($idpers,$login);
    }

    function mksessid()
    {
        /* Fabrique un N° de session aléatoire */
        global $Pool, $SessLen,$authlink, $DBAUTH;

        $count=10;
        do
        {
            $sid="";
            $count--;
            for ($i = 0; $i < $SessLen ; $i++)
                $sid .= substr($Pool, (mt_rand()%(strlen($Pool))),1);
            $query="SELECT id FROM sessions WHERE sess='$sid'";
	    $result=@mysql_db_query("$DBAUTH",$query, $authlink);
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
                        // Recherche du groupe d'appartenance de l'utilisateur connecté
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
      /* Vérifie le login et le mot de passe sur l'annuaire ldap
          ouvre une session et crée le home et la bdd en cas de succès
          Renvoie :
                * true en cas de succès de la création du home et de la bdd
                * false dans les autres cas
      */
        global $urlauth, $authlink, $DBAUTH, $key_priv;
        // Verifie le couple login/password sur l'annuaire ldap
        $auth_ldap = user_valid_passwd ( $login , $passwd );
        if ($auth_ldap) :
                // Ouvre une session et la stocke dans la table sessions de lcs_db
                $query="SELECT id, stat FROM personne WHERE login='$login'";
                $result=mysql_db_query("$DBAUTH",$query, $authlink);
                if ($result && mysql_num_rows($result)):
                        $idpers=mysql_result($result,0,0);
                        $stat=mysql_result($result,0,1)+1;
                        mysql_free_result($result);
                else :
                       // le login n'est pas encore dans la base... creation de l'entrée
                        $result=mysql_db_query("$DBAUTH","INSERT INTO personne  VALUES ('', '', '', '$login', '')", $authlink);
                        $query="SELECT id, stat FROM personne WHERE login='$login'";
                        $result=mysql_db_query("$DBAUTH",$query, $authlink);
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
                $result=mysql_db_query("$DBAUTH","INSERT INTO sessions  VALUES ('', '$sessid', '','$idpers','$ip')", $authlink);
                $result=mysql_db_query("$DBAUTH","UPDATE personne SET stat=$stat WHERE id=$idpers");
                set_act_login($idpers);
                // Création Espace Perso Utilisateur 
                if ( !@is_dir("/home/".$login) ||  (@is_dir("/home/".$login) && 
                                                  ( !@is_dir("/home/".$login."/public_html")
                                                  || !@is_dir("/home/".$login."/Maildir")
                                                  || !@is_dir("/home/".$login."/Documents"))) ) {
                      #system ("echo \"DBG >> Creation Espace perso\" >> /tmp/log.lcs"); 
		      if ( is_eleve($login) ) $group="eleves"; else $group="profs";
		      exec ("/usr/bin/sudo /usr/share/lcs/scripts/mkhdir.sh $login $group $cryptpasswd > /dev/null 2>&1");
                } else { 
                      // Vérification acces bdd et réinitialisation le cas échéant
                      #
                      #system ("echo \"DBG >> Vérif. acces mysql $login $passwd\" >> /tmp/log.lcs");
                      @mysql_close();
                      @mysql_connect("localhost", $login, $passwd );
                      if ( mysql_error() ) {
                          exec ("$scriptsbinpath/mysqlPasswInit.pl $login $passwd");
                          #system ("echo \"DBG >> Réinit mdp mysql $login $passwd\" >> /tmp/log.lcs");
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
            mysql_db_query("$DBAUTH","DELETE FROM sessions WHERE idpers=$idpers", $authlink);
        endif;
        /* update last_log */
        // lecture de act_log
        $result=mysql_db_query("$DBAUTH","SELECT act_log FROM personne WHERE id=$idpers", $authlink);
        if ($result && mysql_num_rows($result)):
          $act_log=mysql_result($result,0,0);
          mysql_free_result($result);
        endif;
        // transfert dans last_log
        $result=mysql_db_query("$DBAUTH","UPDATE personne SET last_log=$act_log WHERE id=$idpers", $authlink);
	
    }

function ldap_get_right_search ($type,$search_filter,$ldap)
// Recherche si $nom est présent dans le droit $type
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
// Determine si $login à le droit $type
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

?>