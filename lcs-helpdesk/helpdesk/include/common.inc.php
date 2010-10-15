<?php
	session_start();
	require_once('ConfigLoader.class.php');
	require_once('HelpDeskAuth.class.php');
	require_once("/var/www/lcs/includes/headerauth.inc.php");
	require_once("/var/www/Annu/includes/ldap.inc.php");
	require_once("/var/www/Annu/includes/ihm.inc.php");
	require_once("/var/www/lcs/includes/jlcipher.inc.php");



function liste_admins() {
        global $ldap_server, $ldap_port, $dn;
        global $error;
        //die(print_r($dn));
        $error="";
        $filter = "cn=system_is_admin*";
        $ldap_groups_attr = array (
        // LDAP attribute
                "cn",
                "memberUid",
                "member"
        );

        /*-----------------------------------------------------*/
        $ds = @ldap_connect ( $ldap_server, $ldap_port );
        if ( $ds ) {
                $r = @ldap_bind ( $ds );
                if (!$r) {
                        $error = "Echec du bind anonyme";
                } else {
                        // Recherche du groupe d'appartenance de l'utilisateur connect�
                        $result=@ldap_search($ds, $dn["rights"], $filter, $ldap_groups_attr);
                        if ($result) {
                                $info = @ldap_get_entries( $ds, $result );
                        }
                }
        }
        @ldap_unbind ($ds);
        @ldap_close ($ds);
        foreach ($info[0]['member'] as $admin) { 
                if (!is_numeric($admin) && !eregi('uid=admin',$admin)) {
                        //$admin = str_replace(',ou=People,dc=','@',$admin);
                        //$admin = str_replace(',dc=','.',$admin);
                        //$admin = str_replace('uid=','',$admin);
			$info = explode('uid=',$admin);
			$info2 = explode(',ou=',$info[1]);
			$flux .= "<option>$info2[0]</option>";
                }
        }
        return $flux;
        
}


	list ($idpers, $login)= isauth();
	//die($login);	
	if (is_admin('system_is_admin', $login) != "Y") {
		redirect_2('/lcs/auth.php');
	}

	if ($_SESSION['userHD'] == null ) {
		$sess_user = base64_encode($login);
		$_SESSION['userHD'] = $sess_user;
	}	
	
	$login = base64_decode($_SESSION['userHD']);
	$res_array_user = people_get_variables($login,false);
    	$array_user = $res_array_user[0];
	//die(var_dump($array_user));
	$HD = new HelpDeskAuth($array_user);
?>
