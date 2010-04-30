<?php

// include your comon files here

// REQUIRED: In this function you should set the 'swekey_id' field of the logged
// user row in the database with the $swekey_id value
// return true if the update is successfull
function AttachSwekeyToLoggedUser($swekey_id)
{
	// put your code here
	require "/var/www/lcs/includes/headerauth.inc.php";
	$idprs=0;$login="";
        if (! empty($_COOKIE["LCSAuth"])) {
            $sess=$_COOKIE["LCSAuth"];
            $result=@mysql_query("SELECT remote_ip, idpers FROM sessions WHERE sess='$sess'");
            if ($result && mysql_num_rows($result) ) {
                list($ip_session,$null) = split(",",mysql_result($result,0,0),2);
                list($first_remote_ip,$null) = split(",",remote_ip(),2);
                if ( $ip_session == $first_remote_ip ) {
                        $idprs =  mysql_result($result,0,1);
                        // Recherche du login a partir de l'idpers
                        $query="SELECT login FROM personne WHERE id=$idprs";
                        $result=@mysql_query($query);
                        if ($result && mysql_num_rows($result)) $login=@mysql_result($result,0,0);
                }
                @mysql_free_result($result);
            }
        }
	 $query = "SELECT login FROM swekey WHERE id_swekey='$swekey_id';";
     $result=mysql_query($query);                      
     if ($result && mysql_num_rows($result) < 2) {
	 $query = "INSERT INTO swekey (login,id_swekey) VALUES ('$login','$swekey_id');";
     $result=mysql_query($query);
     if ($result)  return true;
	 else return false;
	 }
	 else return false;
}


// OPTIONAL: Here you should return the name of the user that is attached to the
// swekey $swekey_id
function NameOfUserAttachedToSwekey($swekey_id)
{
	// put your code here
	require "/var/www/lcs/includes/headerauth.inc.php";
	$query = "SELECT login FROM swekey WHERE id_swekey='$swekey_id';";
                        $result=mysql_query($query);                        
                        if ($result && (mysql_num_rows($result ) < 2) && (mysql_num_rows($result ) > 0) ) {
                        $user=mysql_result($result,0);
                        return $user;
                        }
                        else return '';
	
}

// retourne l'id swekey de l'utilisateur
function IdSwekeyToNameOfUserAttached($user)
{
	// put your code here
	require "/var/www/lcs/includes/headerauth.inc.php";
	$query = "SELECT id_swekey FROM swekey WHERE login='$user';";
                        $result=mysql_query($query);                        
                        if ($result && mysql_num_rows($result)) {
                        $ids=mysql_result($result,0);
                        return $ids;
                        }
                        else return '';
	
}

//detache la swekey du compte user
function DetachSwekeyToLoggedUser($user)
{

	require "/var/www/lcs/includes/headerauth.inc.php";
	$query="DELETE FROM swekey WHERE login = '$user';";
	$result=mysql_query($query);
	if ($result) 
		return true; 
	else 
		return false;
}

//execution de requetes ajax

if ($_GET['swekey_action'] == 'resolve' && strlen($_GET['swekey_id']) == 32)
{
	echo NameOfUserAttachedToSwekey($_GET['swekey_id']);
    exit;
}

if ($_GET['swekey_action'] == 'attach' && strlen($_GET['swekey_id']) == 32)
{	 
	if (AttachSwekeyToLoggedUser($_GET['swekey_id']))
	    echo "OK";
	else
		echo "FAILED";
    exit;
}

if ($_GET['swekey_action'] == 'detach' && strlen($_GET['swekey_user'])!= "")
{	 

	if (DetachSwekeyToLoggedUser($_GET['swekey_user']))
	    echo "OK";
	else
		echo "FAILED";
		exit;
}

if ($_GET['swekey_action'] == 'resolve2' && strlen($_GET['user']) != "")
{
	echo IdSwekeyToNameOfUserAttached($_GET['user']);
    exit;
}
?>
