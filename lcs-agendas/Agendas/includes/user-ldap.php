<?php
// Modification LCS 1/9
include_once ($basedir."Annu/includes/ldap.inc.php");
include_once ($basedir."Annu/includes/ihm.inc.php");
// Fin modification LCS  1/9
defined ( '_ISVALID' ) or die ( 'You cannot access this file directly!' );
/*
 * $Id: user-ldap.php,v 1.42.2.1 2007/08/17 14:39:00 umcesrjones Exp $
 * LDAP user functions.
 * This file is intended to be used instead of the standard user.php file.
 * I have not tested this yet (I do not have an LDAP server running yet),
 * so please provide feedback.
 *
 * This file contains all the functions for getting information about users.
 * So, if you want to use an authentication scheme other than the webcal_user
 * table, you can just create a new version of each function found below.
 *
 * Note: this application assumes that usernames (logins) are unique.
 *
 * Note #2: If you are using HTTP-based authentication, then you still need
 * these functions and you will still need to add users to webcal_user.
 */

/***************************** Config *******************************/
// Set some global config variables about your system.
// Next three are NOT yet implemented for LDAP
$user_can_update_password = false;
$admin_can_add_user = false;

// Allow admin to delete user from webcal tables
$admin_can_delete_user = true;
$admin_can_disable_user = false;

//------ LDAP General Server Settings ------//
//
// Name or address of the LDAP server
//  For SSL/TLS use 'ldaps://localhost'
//$ldap_server = 'localhost';

// Port LDAP listens on (default 389)
$ldap_port = '389';

// Use TLS for the connection (not the same as ldaps://)
$ldap_start_tls = false;

// If you need to set LDAP_OPT_PROTOCOL_VERSION
$set_ldap_version = false;
$ldap_version = '3'; // (usually 3)

// base DN to search for users
//$ldap_base_dn = 'ou=people,dc=company,dc=com';

// The ldap attribute used to find a user (login).
// E.g., if you use cn,  your login might be "Jane Smith"
//       if you use uid, your login might be "jsmith"
$ldap_login_attr = 'uid';

// Account used to bind to the server and search for information.
// This user must have the correct rights to perform search.
// If left empty the search will be made in anonymous.
//
// *** We do NOT recommend storing the root LDAP account info here ***
//$ldap_admin_dn = '';  // user DN
//$ldap_admin_pwd = ''; // user password
// LCS Modification 2/9
$ldap_admin_dn = $adminDn;
$ldap_admin_pwd = $adminPw;
// LCS Fin modification 2/9

//------ Admin Group Settings ------//
//
// A group name (complete DN) to find users with admin rights
//// LCS Modification 3/9
//$ldap_admin_group_name = 'cn=webcal_admin,ou=group,dc=company,dc=com';
$ldap_admin_group_name = "cn=lcs_is_admin,ou=rights,".$ldap_base_dn;
// What type of group do we want (posixgroup, groupofnames, groupofuniquenames)
//$ldap_admin_group_type = 'posixgroup';
$ldap_admin_group_type = 'groupofnames';
// The LDAP attribute used to store member of a group
//$ldap_admin_group_attr = 'memberuid';
$ldap_admin_group_attr = 'member';
// LCS Fin modification 3/9
//
//------ LDAP Filter Settings ------//
//
// LDAP filter used to limit search results and login authentication
$ldap_user_filter = '(objectclass=person)';

// Attributes to fetch from LDAP and corresponding user variables in the
// application. Do change according to your LDAP Schema
$ldap_user_attr = array (
  // LDAP attribute   //WebCalendar variable
  'uid',              //login
  'sn',               //lastname
  'givenname',        //firstname
  'cn',               //fullname
  'mail'              //email
);

/*************************** End Config *****************************/

// Convert group name to lower case to prevent problems
$ldap_admin_group_attr = strtolower($ldap_admin_group_attr);
$ldap_admin_group_type = strtolower($ldap_admin_group_type);

// LCS Modification  4/9
// Fonction de comparaison utilisée dans la fonction usort
// de user_get_users()
// Tri du tableau $list_login par ordre alphabétique sur
// le champ sn (Nom)

function cmp ($a, $b) {
    return strcmp($a["cal_lastname"], $b["cal_lastname"]);
}
// LCS Fin modification 4/9
// Function to search the dn of a given user the error message will
// be placed in $error.
// params:
//   $login - user login
// return:
//   $dn - complete dn for the user
//   TRUE if the user is found, FALSE in other case
function user_search_dn ( $login ) {
  global $error, $ds, $ldap_base_dn, $ldap_login_attr, $ldap_user_attr, $ldap_user_filter;

  $ret = false;
  if ($r = connect_and_bind ()) {
    $sr = @ldap_search ( $ds, $ldap_base_dn,
      "(&($ldap_login_attr=$login)$ldap_user_filter )", $ldap_user_attr );
    if (!$sr) {
      $error = 'Error searching LDAP server: ' . ldap_error( $ds );
    } else {
      $info = @ldap_get_entries ( $ds, $sr );
      if ( $info['count'] != 1 ) {
        $error = 'Invalid login';
      } else {
        $dn = $info[0]['dn'];
        $ret = $dn;
      }
      @ldap_free_result ( $sr );
    }
    @ldap_close ( $ds );
  }
  return $ret;
}

// Check to see if a given login/password is valid. If invalid,
// the error message will be placed in $error.
// params:
//   $login - user login
//   $password - user password
// returns: true or false
function user_valid_login ( $login, $password ) {
  global $error, $ldap_server, $ldap_port, $ldap_base_dn, $ldap_login_attr;
  global $ldap_admin_dn, $ldap_admin_pwd, $ldap_start_tls, $set_ldap_version, $ldap_version;

  if ( ! function_exists ( "ldap_connect" ) ) {
    die_miserable_death ( "Your installation of PHP does not support LDAP" );
  }

  $ret = false;
  $ds = @ldap_connect ( $ldap_server, $ldap_port );
  if ( $ds ) {
    if ($set_ldap_version || $ldap_start_tls)
      ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, $ldap_version);

    if ($ldap_start_tls) {
      if (!ldap_start_tls($ds)) {
        $error = 'Could not start TLS for LDAP connection';
        return $ret;
      }
    }

    if ( ($dn = user_search_dn ( $login )) ) {
      $r = @ldap_bind ( $ds, $dn, $password );
      if (!$r) {
        $error = 'Invalid login';
        //$error .= ': incorrect password'; // uncomment for debugging
      } else {
        $ret = true;
      }
    } else {
      $error = 'Invalid login';
      //$error .= ': no such user'; // uncomment for debugging
    }
    @ldap_close ( $ds );
  } else {
    $error = 'Error connecting to LDAP server';
  }
  return $ret;
}

// TODO: implement this function properly for LDAP.
// Check to see if a given login/crypted password is valid. If invalid,
// the error message will be placed in $error.
// params:
//   $login - user login
//   $crypt_password - crypted user password
// returns: true or false
function user_valid_crypt ( $login, $crypt_password ) {
  return true;
}

// Load info about a user (first name, last name, admin) and set globally.
// params:
//   $user - user login
//   $prefix - variable prefix to use
function user_load_variables ( $login, $prefix ) {
  global $ds, $error, $ldap_base_dn, $ldap_login_attr, $ldap_user_attr,
  $ldap_user_filter, $NONUSER_PREFIX, $PUBLIC_ACCESS_FULLNAME, $cached_user_var;

  if ( ! empty ( $cached_user_var[$login][$prefix] ) )
    return  $cached_user_var[$login][$prefix];
  $cached_user_var = array ();

  if ($NONUSER_PREFIX && substr ($login, 0, strlen ($NONUSER_PREFIX) ) == $NONUSER_PREFIX ) {
    nonuser_load_variables ( $login, $prefix );
    return true;
  }

  if ( $login == '__public__' ) {
    $GLOBALS[$prefix . 'login'] = $login;
    $GLOBALS[$prefix . 'firstname'] = '';
    $GLOBALS[$prefix . 'lastname'] = '';
    $GLOBALS[$prefix . 'is_admin'] = 'N';
    $GLOBALS[$prefix . 'email'] = '';
    $GLOBALS[$prefix . 'fullname'] = $PUBLIC_ACCESS_FULLNAME;
    $GLOBALS[$prefix . 'password'] = '';
    return true;
  }

  $ret =  false;
  if ($r = connect_and_bind ()) {
    $sr = @ldap_search ( $ds, $ldap_base_dn,
      "(&($ldap_login_attr=$login)$ldap_user_filter )", $ldap_user_attr );

    if (!$sr) {
      $error = 'Error searching LDAP server: ' . ldap_error( $ds );
    } else {
      $info = @ldap_get_entries ( $ds, $sr );
      if ( $info['count'] != 1 ) {
        $error = 'Invalid login';
      } else {
        // LCS Modification 5/9 Determination du prenom "firstname" et conversion UTF8
        $tmp_cn =  utf8_decode($info[0]["cn"][0]);
        $tmp_sn =  " ".utf8_decode($info[0]["sn"][0]);
        $tmp_cal_firstname =  ereg_replace($tmp_sn,"",$tmp_cn);

        $GLOBALS[$prefix . 'login'] = $login;
        $GLOBALS[$prefix . 'firstname'] = $tmp_cal_firstname;
        $GLOBALS[$prefix . 'lastname'] = utf8_decode($info[0]["sn"][0]);
        $GLOBALS[$prefix . 'email'] = $info[0][$ldap_user_attr[4]][0];
        $GLOBALS[$prefix . 'fullname'] = utf8_decode($info[0]["cn"][0]);
        $GLOBALS[$prefix . 'is_admin'] = user_is_admin($login,get_admins());
        $ret = true;
		// fin de modif 5/9
      }
      @ldap_free_result ( $sr );
    }
    @ldap_close ( $ds );
  }
  //save these results
  $cached_user_var[$login][$prefix] = $ret;
  return $ret;
}

// Add a new user.
// params:
//   $user - user login
//   $password - user password
//   $firstname - first name
//   $lastname - last name
//   $email - email address
//   $admin - is admin? ("Y" or "N")
function user_add_user ( $user, $password, $firstname, $lastname, $email, 
  $admin, $enabled ) {
  global $error;

  $error = 'Not yet supported.';
  return false;
}

// Update a user
// params:
//   $user - user login
//   $firstname - first name
//   $lastname - last name
//   $email - email address
//   $admin - is admin?
function user_update_user ( $user, $firstname, $lastname, $email, 
  $admin, $enabled ) {
  global $error;

  $error = 'Not yet supported.';
  return false;
}

// Update user password
// params:
//   $user - user login
//   $password - last name
function user_update_user_password ( $user, $password ) {
  global $error;

  $error = 'Not yet supported';
  return false;
}

/**
 * Delete a user from the webcalendar tables. (NOT from LDAP)
 *
 * This will also delete any of the user's events in the system that have
 * no other participants. Any layers that point to this user
 * will be deleted. Any views that include this user will be updated.
 *
 * @param string $user User to delete
 */
function user_delete_user ( $user ) {
  // Get event ids for all events this user is a participant
  $events = get_users_event_ids ( $user );

  // Now count number of participants in each event...
  // If just 1, then save id to be deleted
  $delete_em = array ();
  for ( $i = 0; $i < count ( $events ); $i++ ) {
    $res = dbi_execute ( 'SELECT COUNT(*) FROM webcal_entry_user ' .
      'WHERE cal_id = ?', array ( $events[$i] ) );
    if ( $res ) {
      if ( $row = dbi_fetch_row ( $res ) ) {
        if ( $row[0] == 1 )
   $delete_em[] = $events[$i];
      }
      dbi_free_result ( $res );
    }
  }
  // Now delete events that were just for this user
  for ( $i = 0; $i < count ( $delete_em ); $i++ ) {
    dbi_execute ( 'DELETE FROM webcal_entry_repeats WHERE cal_id = ?',
      array ( $delete_em[$i] ) );
    dbi_execute ( 'DELETE FROM webcal_entry_repeats_not WHERE cal_id = ?',
      array ( $delete_em[$i] ) );
    dbi_execute ( 'DELETE FROM webcal_entry_log WHERE cal_entry_id = ?',
      array ( $delete_em[$i] )  );
    dbi_execute ( 'DELETE FROM webcal_import_data WHERE cal_id = ?',
      array ( $delete_em[$i] )  );
    dbi_execute ( 'DELETE FROM webcal_site_extras WHERE cal_id = ?',
      array ( $delete_em[$i] )  );
    dbi_execute ( 'DELETE FROM webcal_entry_ext_user WHERE cal_id = ?',
      array ( $delete_em[$i] )  );
    dbi_execute ( 'DELETE FROM webcal_reminders WHERE cal_id = ?',
      array ( $delete_em[$i] )  );
    dbi_execute ( 'DELETE FROM webcal_blob WHERE cal_id = ?',
      array ( $delete_em[$i] )  );
    dbi_execute ( 'DELETE FROM webcal_entry WHERE cal_id = ?',
      array ( $delete_em[$i] )  );
  }

  // Delete user participation from events
  dbi_execute ( 'DELETE FROM webcal_entry_user WHERE cal_login = ?',
    array ( $user ) );
  // Delete preferences
  dbi_execute ( 'DELETE FROM webcal_user_pref WHERE cal_login = ?',
    array ( $user ) );
  // Delete from groups
  dbi_execute ( 'DELETE FROM webcal_group_user WHERE cal_login = ?',
    array ( $user ) );
  // Delete bosses & assistants
  dbi_execute ( 'DELETE FROM webcal_asst WHERE cal_boss = ?',
    array ( $user ) );
  dbi_execute ( 'DELETE FROM webcal_asst WHERE cal_assistant = ?',
    array ( $user ) );
  // Delete user's views
  $delete_em = array ();
  $res = dbi_execute ( 'SELECT cal_view_id FROM webcal_view WHERE cal_owner = ?',
    array ( $user ) );
  if ( $res ) {
    while ( $row = dbi_fetch_row ( $res ) ) {
      $delete_em[] = $row[0];
    }
    dbi_free_result ( $res );
  }
  for ( $i = 0; $i < count ( $delete_em ); $i++ ) {
    dbi_execute ( 'DELETE FROM webcal_view_user WHERE cal_view_id = ?',
      array ( $delete_em[$i] ) );
  }
  dbi_execute ( 'DELETE FROM webcal_view WHERE cal_owner = ?',
    array ( $user ) );
  //Delete them from any other user's views
  dbi_execute ( 'DELETE FROM webcal_view_user WHERE cal_login = ?',
    array ( $user ) );
  // Delete layers
  dbi_execute ( 'DELETE FROM webcal_user_layers WHERE cal_login = ?',
    array ( $user ) );
  // Delete any layers other users may have that point to this user.
  dbi_execute ( 'DELETE FROM webcal_user_layers WHERE cal_layeruser = ?',
    array ( $user ) );
  // Delete user
  dbi_execute ( 'DELETE FROM webcal_user WHERE cal_login = ?',
    array ( $user ) );
  // Delete function access
  dbi_execute ( 'DELETE FROM webcal_access_function WHERE cal_login = ?',
    array ( $user ) );
  // Delete user access
  dbi_execute ( 'DELETE FROM webcal_access_user WHERE cal_login = ?',
    array ( $user ) );
  dbi_execute ( 'DELETE FROM webcal_access_user WHERE cal_other_user = ?',
    array ( $user ) );
  // Delete user's categories
  dbi_execute ( 'DELETE FROM webcal_categories WHERE cat_owner = ?',
    array ( $user ) );
  dbi_execute ( 'DELETE FROM webcal_entry_categories WHERE cat_owner = ?',
    array ( $user ) );
  // Delete user's reports
  $delete_em = array ();
  $res = dbi_execute ( 'SELECT cal_report_id FROM webcal_report WHERE cal_login = ?',
    array ( $user ) );
  if ( $res ) {
    while ( $row = dbi_fetch_row ( $res ) ) {
      $delete_em[] = $row[0];
    }
    dbi_free_result ( $res );
  }
  for ( $i = 0; $i < count ( $delete_em ); $i++ ) {
    dbi_execute ( 'DELETE FROM webcal_report_template WHERE cal_report_id = ?',
      array ( $delete_em[$i] ) );
  }
  dbi_execute ( 'DELETE FROM webcal_report WHERE cal_login = ?',
    array ( $user ) );
    //not sure about this one???
  dbi_execute ( 'DELETE FROM webcal_report WHERE cal_user = ?',
    array ( $user ) );
  // Delete user templates
  dbi_execute ( 'DELETE FROM webcal_user_template WHERE cal_login = ?',
    array ( $user ) );
}

// Get a list of users and return info in an array.
// returns: array of users
function user_get_users ( $publicOnly=false ) {
  global $error, $ds, $ldap_base_dn, $ldap_user_attr, $ldap_user_filter;
  global $PUBLIC_ACCESS, $PUBLIC_ACCESS_FULLNAME;

  $Admins = get_admins ();
  $count = 0;
  $ret = array ();
  if ( $PUBLIC_ACCESS == 'Y' )
    $ret[$count++] = array (
       'cal_login' => '__public__',
       'cal_lastname' => '',
       'cal_firstname' => '',
       'cal_is_admin' => 'N',
       'cal_email' => '',
       'cal_password' => '',
       'cal_fullname' => $PUBLIC_ACCESS_FULLNAME );
  if ( $publicOnly ) return $ret;
  if ($r = connect_and_bind ()) {
    $sr = @ldap_search ( $ds, $ldap_base_dn, $ldap_user_filter, $ldap_user_attr );
    if (!$sr) {
      $error = 'Error searching LDAP server: ' . ldap_error( $ds );
    } else {
      if ( (float)substr (PHP_VERSION,0,3) >= 4.2 ) ldap_sort ( $ds, $sr, $ldap_user_attr[3]);
      $info = @ldap_get_entries( $ds, $sr );
      for ( $i = 0; $i < $info['count']; $i++ ) {
        $ret[$count++] = array (
          'cal_login' => $info[$i][$ldap_user_attr[0]][0],
          'cal_lastname' => utf8_decode($info[$i][$ldap_user_attr[1]][0]),
          'cal_firstname' =>utf8_decode( $info[$i][$ldap_user_attr[2]][0]),
          'cal_email' => $info[$i][$ldap_user_attr[4]][0],
          'cal_is_admin' => user_is_admin ($info[$i][$ldap_user_attr[0]][0],$Admins),
          'cal_fullname' => utf8_decode($info[$i][$ldap_user_attr[3]][0])
          );
      }
      @ldap_free_result($sr);
    }
    @ldap_close ( $ds );
  }
  // LCS Modification 7/9 Tri du tableau
  usort($ret,cmp);
  //fin modif 7
  return $ret;
}

// Test if a user is an admin, that is: if the user is a member of a special
// group in the LDAP Server
// params:
//   $values - the login name
// returns: Y if user is admin, N if not
function user_is_admin ($values,$Admins) {
  if ( ! $Admins ) {
    return 'N';
  } else if (in_array ($values, $Admins)) {
    return 'Y';
  } else {
    return 'N';
  }
}

// Searches $ldap_admin_group_name and returns an array of the group members.
// Do this search only once per request.
// returns: array of admins
function get_admins () {
  global $error, $ds, $cached_admins;
  global $ldap_admin_group_name,$ldap_admin_group_attr,$ldap_admin_group_type;

  if ( ! empty ( $cached_admins ) ) return $cached_admins;
  $cached_admins = array ();

  if ($r = connect_and_bind ()) {
    $search_filter = "($ldap_admin_group_attr=*)";
    $sr = @ldap_search ( $ds, $ldap_admin_group_name, $search_filter, array ($ldap_admin_group_attr) );
    if (!$sr) {
      $error = 'Error searching LDAP server: ' . ldap_error( $ds );
    } else {
      $admins = ldap_get_entries( $ds, $sr );
      for( $x = 0; $x < $admins[0][$ldap_admin_group_attr]['count']; $x ++ ) {
       if ($ldap_admin_group_type != 'posixgroup') {
          $cached_admins[] = stripdn($admins[0][$ldap_admin_group_attr][$x]);
        } else {
          $cached_admins[] = $admins[0][$ldap_admin_group_attr][$x];
        }
      }
      @ldap_free_result($sr);
    }
    @ldap_close ( $ds );
  }
  return $cached_admins;
}

// Strip everything but the username (uid) from a dn.
//  params:
//    $dn - the dn you want to strip the uid from.
//  returns: string - userid
//
//  ex: stripdn(uid=jeffh,ou=people,dc=example,dc=com) returns jeffh
function stripdn($dn){
  list ($uid,$trash) = split (',', $dn, 2);
  list ($trash,$user) = split ('=', $uid);
  return($user);
}

// Connects and binds to the LDAP server
// Tries to connect as $ldap_admin_dn if we set it.
//  returns: bind result or false
function connect_and_bind () {
  global $ds, $error, $ldap_server, $ldap_port, $ldap_version;
  global $ldap_admin_dn, $ldap_admin_pwd, $ldap_start_tls, $set_ldap_version;

  if ( ! function_exists ( 'ldap_connect' ) ) {
    die_miserable_death ( 'Your installation of PHP does not support LDAP' );
  }

  $ret = false;
  $ds = @ldap_connect ( $ldap_server, $ldap_port );
  if ( $ds ) {
    if ($set_ldap_version || $ldap_start_tls)
      ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, $ldap_version);

    if ($ldap_start_tls) {
      if (!ldap_start_tls($ds)) {
        $error = 'Could not start TLS for LDAP connection';
        return $ret;
      }
    }

    if ( $ldap_admin_dn != '') {
      $r = @ldap_bind ( $ds, $ldap_admin_dn, $ldap_admin_pwd );
    } else {
      $r = @ldap_bind ( $ds );
    }

    if (!$r) {
      $error = 'Invalid Admin login for LDAP Server';
    } else {
      $ret = $r;
    }
  } else {
    $error = 'Error connecting to LDAP server';
    $ret = false;
  }
  return $ret;
}
// LCS Modification 9/9 Recjerche des doublons dans un tableau
function doublon($tab) {
  $tab1 = array();

  $k=0;
  for ($i=0; $i<count ($tab); $i++) {
    // 1er element
    if ($i==0) {
      $tab1[$k] = $tab[$i];
      $k++;
    } else {
      for ($j=0; $j<count($tab1);$j++) {
        $doublon=0;
        if ($tab[$i]["uid"] == $tab1[$j]["uid"]) {
           $doublon= 1;
           break;
        }
      }
      if ($doublon==0) {
        $tab1[$k]=$tab[$i];
        $k++;
      }
    }
  }
  return $tab1;
}

function get_users_in_my_groups () {
	global $ldap_server, $ldap_port, $dn, $login, $DEBUG, $MsgD;

	$count = 0; $i=0;
	$group_principal = "NUL_group";

  	// Recherche des groupes d'appartenance de l'utilisateur $login
  	list($user, $groups)=people_get_variables($login, true);

	### DEBUG
	if ( $DEBUG ) {
		for ($loop=0; $loop < count ($groups) ; $loop++) {
			echo "$MsgD liste des groupes : ".$groups[$loop]["cn"]."<br>";
		}
	}

	// Recherche du groupe principal et des groupes secondaires de l'utilisateur

	for ($loop=0; $loop < count ($groups) ; $loop++) {
		if ( $groups[$loop]["cn"] == "Administratifs" ) $group_principal = "Administratifs";
		elseif ( $groups[$loop]["cn"] == "Profs" ) $group_principal = "Profs";
		elseif ( $groups[$loop]["cn"] == "Eleves" ) $group_principal = "Eleves";
		elseif (  !ereg("Administratifs",$groups[$loop]["cn"]) && !ereg("Profs",$groups[$loop]["cn"]) && !ereg("Eleves",$groups[$loop]["cn"]) && !ereg("Cours",$groups[$loop]["cn"]) && !ereg("Matiere",$groups[$loop]["cn"])) {
			$groups_secondaires[$i] = $groups[$loop]["cn"];
			$i++;
			if ( ereg ("Classe", $groups[$loop]["cn"] ) ) {
				$groups_secondaires[$i] = ereg_replace("Classe_","Equipe_",$groups[$loop]["cn"]);
				$i++;
			}
			if ( ereg ("Equipe", $groups[$loop]["cn"] ) ) {
				$groups_secondaires[$i] = ereg_replace("Equipe_","Classe_",$groups[$loop]["cn"]);
				$i++;
			}
		}
	}

	### DEBUG
	if ($DEBUG ) {
		echo "$MsgD group_principal : $group_principal<br>";
		echo "$MsgD nbr groupes secondaires : ".count($groups_secondaires)."<br>";
		for ($loop=0; $loop < count($groups_secondaires); $loop++) {
			echo "$MsgD groups_secondaires : ".$groups_secondaires[$loop]."<br>";
		}
	}


	if ( $group_principal != "NUL_group" ) {

		// Recherche des uids des membres de ses groupes secondaires
		//echo $group_principal;exit;
		//modif 
		$index=0;
		if ($group_principal == "Administratifs" || $group_principal == "Profs") {
			$uids[$loop] = search_uids ("(cn=Administratifs)", "half");
  			$users_in_my_groups[$index] = search_people_groups ($uids[$loop],"(sn=*)","group");
  			$index++;
  			$uids[$loop] = search_uids ("(cn=Profs)", "half");
  			$users_in_my_groups[$index] = search_people_groups ($uids[$loop],"(sn=*)","group");
  			$index++;
  		//ajout de Equipes pour les administratisfs
  			
  			if ($group_principal == "Administratifs") {
	  			$groups_equipes=search_groups("cn=Equipe*");
		  			if (count ($groups_equipes) !=0){
						for ($loop=0; $loop < count($groups_equipes); $loop++) {
							$uids[$loop] = search_uids ("(cn=".$groups_equipes[$loop]['cn'].")", "half");
				  			$users_in_my_groups[$index] = search_people_groups ($uids[$loop],"(sn=*)","group");
				  			$index++;
						}
					}
	  			}
	  			
		}
		
		if (count ($groups_secondaires) !=0){
		for ($loop=0; $loop < count($groups_secondaires); $loop++) {
  			$uids[$loop] = search_uids ("(cn=".$groups_secondaires[$loop].")", "half");
  			$users_in_my_groups[$index] = search_people_groups ($uids[$loop],"(sn=*)","group");
  			$index++;
		}
		}
	} elseif (($group_principal == "Eleves" && count ($groups_secondaires) ==0) || $group_principal == "NUL_group") {
		// Aaaaaaaaargh ! L'utilisateur est dans aucun groupe secondaire ou principal...
		// On retourne uniquement son fullname et celui d'admin
		if ($DEBUG ) echo "$MsgD Cas d'1 user ne possédant pas de groupes secondaires<br>";
		$uids[0]["uid"] = "admin";
		$uids[1]["uid"] = $login;
		$users_in_my_groups[0] = search_people_groups ($uids,"(sn=*)","group");
	}

	// Transfert dans le tableau $ret
	for ( $loop0=0; $loop0 < count($users_in_my_groups); $loop0++ ) {
		for ( $loop=0; $loop < count($users_in_my_groups[$loop0]); $loop++ ) {
        $ret[$count++] = array (
			"cal_login" => $users_in_my_groups[$loop0][$loop]["uid"],
         "cal_fullname" => $users_in_my_groups[$loop0][$loop]["fullname"],
         "cal_group" => $users_in_my_groups[$loop0][$loop]["group"],
         "cal_cat" => $users_in_my_groups[$loop0][$loop]["cat"]
        );
      }
	}

	### DEBUG
	if ($DEBUG ) {
		for ($loop=0; $loop < count($ret); $loop++) {
			echo "$MsgD : ".$ret[$loop]["cal_login"]." ".$ret[$loop]["cal_fullname"]." ".$ret[$loop]["cal_group"]." ".$ret[$loop]["cal_cat"]."<br>";
		}
	}
	### FIN DEBUG

	return $ret;
}
// LCS Fin modification 9/9
?>
