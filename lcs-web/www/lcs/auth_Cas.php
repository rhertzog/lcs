<?php
/* lcs/auth_ent.php version du :  28/03/2012 Renomme lcs/auth_Cas.php*/
include ("./includes/headerauth.inc.php");
include ("../Annu/includes/ldap.inc.php");
include ("./includes/jlcipher.inc.php");

//Authentication on  ENT CAS service
include_once('/usr/share/php/CAS/CAS.php');
phpCAS::setDebug();

// initialise phpCAS
phpCAS::client(CAS_VERSION_2_0,$ent_hostname,intval($ent_port),$ent_uri);

// no SSL validation for the CAS server
phpCAS::setNoCasServerValidation();

// force CAS authentication
phpCAS::forceAuthentication();

// at this step, the user has been authenticated by the CAS server

// logout if desired
if (isset($_REQUEST['logout'])) {
	phpCAS::logout();
}

// If authenticate
if (!mysql_select_db($DBAUTH, $authlink)) 
	die ("S&#233;lection de base de donn&#233;es impossible.");
$user_escp=mysql_real_escape_string(phpCAS::getUser());	
$query="select login_lcs from ent_lcs where id_ent='$user_escp'";
$result=mysql_query($query,$authlink);

if ($result) {
    if (mysql_num_rows($result)==0)
        {
       // Link form account ENT LCS 
       echo "<script type='text/javascript'>
        // <![CDATA[
       setTimeout(function(){
       $( '#dialog-form' ).dialog('open' );
       },1000);
               //]]>
        </script>\n";
        }
elseif (mysql_num_rows($result)==1)
{
	//Open LCS session
	$retour= mysql_fetch_array($result);
	$login=$retour[0];
	$login_escp=mysql_real_escape_string($login);
	$new_password = decodekey($_POST['string_new_mdp']);
	// Open session and write in sessions table of lcs_db
	$query="SELECT id, stat FROM personne WHERE login='$login_escp'";
	$result=@mysql_query($query,$authlink);
	if ($result && mysql_num_rows($result)) 
		{
		$idpers=mysql_result($result,0,0);
		$stat=mysql_result($result,0,1)+1;
		mysql_free_result($result);
		} 
	else 
		{
		// The login is not in the base... Create entry
		$query="INSERT INTO personne  VALUES ('', '', '', '$login_escp', '')";
		$result=@mysql_query($query,$authlink);		
		$query="SELECT id, stat FROM personne WHERE login='$login_escp'";
		$result=@mysql_query($query,$authlink);
		if ($result && mysql_num_rows($result))
			{
			$idpers=mysql_result($result,0,0);
			$stat=mysql_result($result,0,1)+1;
			mysql_free_result($result);
         }
		}
	$sessid=mksessid();
	//Post LCS cookie
	setcookie("LCSAuth", "$sessid", 0,"/","",0);
	// Read client IP
	$ip = remote_ip();
	// Write session and release table personne with stats
	$ip_escp=mysql_real_escape_string($ip);
	$idpers_escp=mysql_real_escape_string($idpers);
	$query="INSERT INTO sessions  VALUES ('', '$sessid', '','$idpers_escp','$ip_escp')";
	$result=@mysql_query($query,$authlink);
	$query="UPDATE personne SET stat=$stat WHERE id=$idpers_escp";
	$result=@mysql_query($query,$authlink);
	// Generate token for local CAS service 
	$token = substr(sha1(uniqid('', TRUE)),0,30);
	$query="UPDATE ent_lcs SET token='$token' WHERE login_lcs='$login_escp'";
	$result=@mysql_query($query,$authlink);
	// Log acces authentication in /var/log/lcs/acces.log
	set_act_login($idpers);
    $fp=fopen($logpath."/acces.log","a");
    if($fp) 
		{
		fputs($fp,date("M j H:i:s ")." ENT authentication succes for $login from ".remote_ip()."\n");
        fclose($fp);
		}
	// Statusage
	// Detect from where ? LAN or WAN
	list ($ip_client_prefix) = explode (".", remote_ip()); 
   list ($ip_serv_prefix) = explode (".",getenv("SERVER_ADDR"));
   if ( $ip_client_prefix == $ip_serv_prefix) $source="lan"; else $source="wan";
	$date=date("YmdHis");
	// record in statusage table
	if (!@mysql_select_db($DBAUTH, $authlink)) 
		die ("S&#233;lection de base de donn&#233;es impossible.");
	$query="INSERT INTO statusages VALUES ('Nogroup', 'auth_ok', '$date', '$source','$login_escp')";
	$result=@mysql_query($query,$authlink);
	// Open Spip session if spip is install
	if ( file_exists ("/usr/share/lcs/spip/spip_session_lcs.php") )
		header("Location:../spip/spip_session_lcs.php?action=login");
	else 
	{
	echo "<script language=\"JavaScript\" type=\"text/javascript\">\n";
	echo "<!--\n";
	echo "top.location.href = '../lcs/index.php?url_redirect=accueil.php';\n";
	echo "//-->\n";
	echo "</script>\n";
	}   
  }
else echo 'ERREUR';
}
@mysql_free_result($result);                        


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html  xmlns="http://www.w3.org/1999/xhtml" >
	<head>
		<title>test ent</title>
		<meta http-equiv="content-type" content="text/html;charset=utf-8" />
		<link rel="stylesheet" href="../libjs/jquery-ui/css/cupertino/jquery-ui.css" />
		<link rel="stylesheet" href="../c/ent.css" />
		<script  type="text/javascript" src="../libjs/jquery/jquery.js"></script>
		<script  type="text/javascript" src="../libjs/jquery-ui/jquery-ui.js"></script>
		<script  type="text/javascript" src="crypto.js"></script>
		<script  type="text/javascript" src="public_key.js"></script>
		<script  type="text/javascript" src="ent.js"></script>
	</head>
    <body>
        
<div id="dialog-form" title="Association compte ENT<-> compte LCS">
	<p id="validateTips">Au premier acc&#232;s depuis l'ENT, vous devez associer votre compte LCS.</p>
	<form >
	<fieldset>
		<label for="name">Login LCS </label>
		<input type="text" name="name" id="name" value="" class="text ui-widget-content ui-corner-all" />
		<label for="password">Mot de passe LCS</label>
                <input type="password" name="password" id="password" value="" class="text ui-widget-content ui-corner-all"  />
                <p id="passwd"  style="display : none" class="ui-state-highlight"></p>
                <input type="button" name="chbox" id="chbx" class="novis" value="Voir le mot de passe" />
                <input type="hidden" name="id_ent" id="id_ent"value="<?echo phpCAS::getUser();?>"/>
	</fieldset>
	</form >
       
</div>
<div id="pwd-form" title=" Modification du mot de passe">
	<p id="validateTips1">Vous devez modifier votre mot de passe par d&#233;faut.
</p>
	<form>
	<fieldset>
		<label for="pwd_actuel">Mot de passe actuel </label>
		<input type="password" name="pwd_actuel" id="pwd_actuel" value=""  class="text ui-widget-content ui-corner-all" />
                 <p id="passwd1"  style="display : none" class="ui-state-highlight"></p>
                <label for="new_password">Nouveau mot de passe</label>
		<input type="password" name="new_password" id="new_password" value="" class="text ui-widget-content ui-corner-all" />
                 <p id="passwd2"  style="display : none" class="ui-state-highlight"></p>
                <label for="renew_password">Confirmer le nouveau mot de passe</label>
		<input type="password" name="renew_password" id="renew_password" value="" class="text ui-widget-content ui-corner-all" />
                 <p id="passwd3"  style="display : none" class="ui-state-highlight"></p>
                <input type="button" name="chbox" id="chbx1" class="novis" value="Voir les mots de passe" />
         </fieldset>
	</form>

</div>
</body>
</html>