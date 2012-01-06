<?php
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Expires: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Content-Type: text/plain" );
// No Cache for HTTP/1.1
header("Cache-Control: no-cache , private");
//No Cache for HTTP/1.0
header("Pragma: no-cache");

include ("./includes/headerauth.inc.php");
include ("../Annu/includes/ldap.inc.php");
include ("../Annu/includes/ihm.inc.php");
include ("./includes/jlcipher.inc.php");
  
//ckeck Lcs account
if (isset($_POST['string_mdp']) && (isset($_POST['string_login']))&& (isset($_POST['string_lilie'])))
	{
	// Verif login / password on LCS LDAP 
    $password = decodekey($_POST['string_mdp']);
    if ( user_valid_passwd ( $_POST['string_login'], $password ) ) 
    	{
    	// If password account OK
		// Create user home folder and data base
		$login = $_POST['string_login'];
		$cryptpasswd = $_POST['string_mdp'];
        if ( !@is_dir("/home/".$login) ||  (@is_dir("/home/".$login) && ( !@is_dir("/home/".$login."/public_html") || !@is_dir("/home/".$login."/Maildir") || !@is_dir("/home/".$login."/Documents"))) )
			{
			if ( is_eleve($login) ) $group="eleves"; else $group="profs";
				exec ("/usr/bin/sudo /usr/share/lcs/scripts/mkhdir.sh $login $group $cryptpasswd > /dev/null 2>&1");
			}		
        //Compare with date of birth 
		if ( ! pwdMustChange ($_POST['string_login']) ) 
			{
            //If password account is different than date of birth
			// Insert data in ent_lcs table
			if (!@mysql_select_db($DBAUTH, $authlink)) 
    				die ("S&#233;lection de base de donn&#233;es impossible.");    				
			$query="INSERT INTO ent_lcs (id_ent, login_lcs, token) VALUES ('".$_POST['string_lilie']."', '".$_POST['string_login']."', '$token')";
        	$result=mysql_query($query,$authlink);				
			// And return string OK
			$cr='OK';
			// If password account is egal than date of birth then return string "MustChange"
			} else $cr='MustChange';
     	// The password account is wrong, return string "NOK"
		} else $cr='NOK';
	// Post CR report
    echo $cr;
    exit;
    }

  //check password account
  if ( isset($_POST['string_old_mdp']) && (isset($_POST['string_new_mdp'])) && (isset($_POST['string_renew_mdp'])) && (isset($_POST['string_login'])) )
    {
    // Must return "OK" if succes, "NOK" if unsucces and "ERROR" if system error
    
	$login = $_POST['string_login'];
    // Decode crypt string
    $old_password = decodekey($_POST['string_old_mdp']);
    $new_password = decodekey($_POST['string_new_mdp']);
    $verif_password = decodekey($_POST['string_renew_mdp']);
    if ( verifPwd($new_password) && ($new_password == $verif_password) && (user_valid_passwd ( $_POST['string_login'], $old_password )) && ($new_password!=$old_password) )
		{ 
		if ( userChangedPwd($_POST['string_login'], $new_password, $old_password ) ) 
			{
			$cr1='OK';
			// verify if password data base of the user must change
			@mysql_close();
            @mysql_connect("localhost", $login, $new_password );
            if ( mysql_error() ) 
				exec ("$scriptsbinpath/mysqlPasswInit.pl $login $new_password");
			@mysql_close();
			}
    	else $cr1='NOK';
		}
    else $cr1='NOK';  	
   
    if ( $cr1 != "") 
    echo $cr1;
    exit;
    } else echo "ERROR";
?>
