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
        $filter = "cn=lcs_is_admin*";
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
                        $admin = str_replace(',ou=People,dc=','@',$admin);
                        $admin = str_replace(',dc=','.',$admin);
                        $admin = str_replace('uid=','',$admin);
						$flux .= "<option>$admin</option>";
                }
        }
        return $flux;
        
}


	list ($idpers, $_login)= isauth();
	
	
	if (is_admin('Lcs_is_admin', $_login) != "Y") {
		redirect_2('/lcs/auth.php');
	}

	//define('URL_ROOT',"https://lcetch.crdp.ac-caen.fr/helpdesk");
	//define('URL_ROOT',"http://10.211.55.2/helpdesk");
	//define('URL_API', URL_ROOT."/LcsAPI");
	
	if (!isset($_SESSION['user'])) {
		$sess_user = base64_encode($_login);
		$_SESSION['user'] = $sess_user;
	}	
	
	$_login = base64_decode($_SESSION['user']);
	$res_array_user = people_get_variables($_login,false);
    	$array_user = $res_array_user[0];
	$HD = new HelpDeskAuth($array_user);
?>
