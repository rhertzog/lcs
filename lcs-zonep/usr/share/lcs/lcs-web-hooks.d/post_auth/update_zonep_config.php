<?php
  /*
   * Update AutoFS configuration file with user's credentials
   *
   * @param login string. The login used by the user.
   *
   * @param password string. The password of the user.
   *
   * @return status bool. True on success, false otherwise
   */
function hook_post_auth_update_zonep_config($login = null, $password = null) {
    global $ad_server, $ad_base_dn, $ad_bind_dn, $ad_bind_pw;
    global $ldap_server, $ldap_base_dn, $adminRdn, $adminPw, $se3Ip;

    // Check arguments
    if(!is_string($login) or !is_string($password))
	return false;

    // Ensure we have an ActiveDirectory server or LDAP server to contact
    if (empty($ad_server) && empty($ldap_server))
	return false;
	
	// Connect to AD or LDAP
	if (!empty($ad_server))    
		$ds = ldap_connect($ad_server);
	else
		$ds = ldap_connect($ldap_server);	

    if(!$ds)
	return false;
	
    // admin Bind on AD or LDAP
	if (!empty($ad_server))    
    	$r = ldap_bind($ds, $ad_bind_dn, $ad_bind_pw);
    else
    	$r = ldap_bind($ds, $adminRddn.$ldap_base_dn, $adminPw);    

    if(!$r)
	return false;

    // Fetch UNC from Active Directory
    $attributes = array('homeDirectory');

	if (!empty($ad_server))
    	$sr = ldap_search($ds, $ad_base_dn, "(sAMAccountName=$login)", $attributes);
    else 	
    	$sr = ldap_search($ds, $ldap_base_dn, "(uid=$login)", $attributes);

    if (! $sr)
	return false;

    $entries = ldap_get_entries($ds, $sr);

    if(empty($entries[0]['homedirectory'][0]))
	return false;

    if (!empty($ad_server))
    	$smb_share = str_replace('\\', '/', $entries[0]['homedirectory'][0]);
    else
    	$smb_share = "//$se3Ip/$login";

    // Call sudo wrapper to create autofs configuration file
    $handle = popen('sudo lcs-zonep-update-credentials', 'w');
    fwrite($handle, "$login\n$password\n$smb_share\n");
    $status = pclose($handle) >> 8;
    if ($status != 0)
	return false;

    return true;
}

?>
