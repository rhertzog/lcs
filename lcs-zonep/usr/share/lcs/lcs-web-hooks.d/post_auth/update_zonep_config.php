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

    // Check arguments
    if(!is_string($login) or !is_string($password))
	return false;

    // Ensure we have an ActiveDirectory server to contact
    if (empty($ad_server))
	return false;

    // Connect to LDAP
    $ds = ldap_connect($ad_server);
    if(!$ds)
	return false;

    // Bind en admin
    $r = ldap_bind($ds, $ad_bind_dn, $ad_bind_pw);
    if(!$r)
	return false;

    // Fetch UNC from Active Directory
    $attributes = array('homeDirectory');

    $sr = ldap_search($ds, $ad_base_dn, "(sAMAccountName=$login)", $attributes);
    if (! $sr)
	return false;

    $entries = ldap_get_entries($ds, $sr);

    if(empty($entries[0]['homedirectory'][0]))
	return false;

    $smb_share = str_replace('\\', '/', $entries[0]['homedirectory'][0]);

    // Call sudo wrapper to create autofs configuration file
    $handle = popen('sudo lcs-zonep-update-credentials', 'w');
    fwrite($handle, "$login\n$password\n$smb_share\n");
    $status = pclose($handle) >> 8;
    if ($status != 0)
	return false;

    return true;
}

?>
