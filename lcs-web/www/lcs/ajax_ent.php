<?php
/*===========================================
   Projet LcSE3
   Equipe Tice academie de Caen
   Distribue selon les termes de la licence GPL
   Derniere modification : 04/04/2014
   ============================================= */
include "../Annu/includes/check-token.php";
if (!check_acces(1)) exit;
$login=$_SESSION['login'];
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

if (count($_POST)>0) {
  //configuration objet
  include ("../Annu/lcs/includes/htmlpurifier/library/HTMLPurifier.auto.php");
  $config = HTMLPurifier_Config::createDefault();
  $purifier = new HTMLPurifier($config);
  //purification des variables
  if (isset($_POST['string_mdp'])) $string_mdp=$purifier->purify($_POST['string_mdp']);
  if (isset($_POST['string_login'])) $string_login=$purifier->purify($_POST['string_login']);
  if (isset($_POST['string_lilie'])) $string_lilie=$purifier->purify($_POST['string_lilie']);
  if ( isset($_POST['string_old_mdp'])) $string_old_mdp=$purifier->purify($_POST['string_old_mdp']);
  if (isset($_POST['string_new_mdp'])) $string_new_mdp=$purifier->purify($_POST['string_new_mdp']);
  if (isset($_POST['string_renew_mdp'])) $string_renew_mdp=$purifier->purify($_POST['string_renew_mdp']);
}
//ckeck Lcs account
if (isset($_POST['string_mdp']) && (isset($_POST['string_login']))&& (isset($_POST['string_lilie'])))
	{
	// Verif login / password on LCS LDAP
    $password = decodekey($string_mdp);
    if ( user_valid_passwd ( $string_login, $password ) )
    	{
    	// If password account OK
		// Create user home folder and data base
		$login = $string_login;
		$cryptpasswd = $string_mdp;
		if ( !@is_dir("/home/".$login) ||  (@is_dir("/home/".$login) && ( !@is_dir("/home/".$login."/public_html") || !@is_dir("/home/".$login."/Maildir") || !@is_dir("/home/".$login."/Documents"))) )
			{
			$group=strtolower(people_get_group ($login));
			exec ("/usr/bin/sudo /usr/share/lcs/scripts/mkhdir.sh ".escapeshellarg($login)." '$group' '$cryptpasswd' > /dev/null 2>&1");
			}
        //Compare with date of birth
		if ( ! pwdMustChange ($string_login) )
			{
            //If password account is different than date of birth
			// Insert data in ent_lcs table
			if (!@((bool)mysqli_query( $authlink, "USE " . $DBAUTH)))
    				die ("S&#233;lection de base de donn&#233;es impossible.");
			// Verification si une entree login existe dans la table ent_lcs.login_lcs
			$login=((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $login) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));
			$string_lilie=((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $tring_lilie) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));
			$query="SELECT id FROM ent_lcs WHERE login_lcs='$login_escp'";
			$result=@mysqli_query($authlink, $query);
			if ( mysqli_num_rows($result) == "0" ) {
				// Creation
				$query="INSERT INTO ent_lcs (id_ent, login_lcs, token) VALUES ('".$string_lilie."', '".$login."', '$token')";
			} else {
				// Update
				$query="UPDATE ent_lcs SET id_ent='".$string_lilie."', token='$token' WHERE login_lcs='".$login."'";
			}
        	$result=mysqli_query($authlink, $query);

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
  if ( isset($_POST['string_old_mdp']) && (isset($_POST['string_new_mdp'])) && (isset($_POST['string_renew_mdp'])) && (isset($string_login)) )
    {
    // Must return "OK" if succes, "NOK" if unsucces and "ERROR" if system error

	$login = $string_login;
    // Decode crypt string
    $old_password = decodekey($string_old_mdp);
    $new_password = decodekey($string_new_mdp);
    $verif_password = decodekey($string_renew_mdp);
    if ( verifPwd($new_password) && ($new_password == $verif_password) && (user_valid_passwd ( $string_login, $old_password )) && ($new_password!=$old_password) )
		{
		if ( userChangedPwd($string_login, $new_password, $old_password ) )
			{
			$cr1='OK';
			// verify if password data base of the user must change
			@((is_null($___mysqli_res = mysqli_close($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);
            @($GLOBALS["___mysqli_ston"] = mysqli_connect("localhost",  $login,  $new_password ));
            if ( ((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)) )
            	exec ( escapeshellarg("$scriptsbinpath/mysqlPasswInit.pl")." ". escapeshellarg($login) ." ". escapeshellarg($passwd) );
			@((is_null($___mysqli_res = mysqli_close($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);
			}
    	else $cr1='NOK';
		}
    else $cr1='NOK';

    if ( $cr1 != "")
    echo $cr1;
    exit;
    } else echo "ERROR";
?>
