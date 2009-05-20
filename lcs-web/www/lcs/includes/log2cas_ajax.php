<?php

include "headerauth.inc.php";
include ("../../Annu/includes/ldap.inc.php");
include ("../../Annu/includes/ihm.inc.php");

// Ce script cree les tickets LT et TGT pour le CAS et recupere les donnees LDAP pour la suite
list ($idpers, $login)= isauth();


if ($idpers)
	$password = urldecode(xoft_decode($_COOKIE['LCSuser'], $key_priv));

if ($login)  {

    if($_SERVER['REQUEST_METHOD']==='POST') {  // REQUIRE POST OR DIE
        // INITIALIZE ALL VARS
        $ch='';
        $Rec_Data='';
        $service = "http://".$hostname.".".$domain."/lcs/index.php?url_redirect=accueil.php";
        //on casse le cookie 
        setcookie('lt',"$lt",0,"/","",0);

        $query="SELECT * FROM `casserver`.`casserver_lt` where consumed = NULL and client_hostname='$clientIP' ORDER By id DESC;";
        $result=@mysql_db_query("casserver",$query, $authlink) or die("ERREUR $query");
        if (mysql_num_rows($result) > 0 )
            $lt = (mysql_result($result,0,"ticket"));
        else {
            // Generate Login Ticket
            $cmd = "/usr/bin/ruby /var/www/lcs/includes/mkTicket.rb ''";
            exec($cmd,$AllOutPut,$ReturnValue);
            $lt='LT-'.$AllOutPut[0];

            $date = date("Y-m-d H:i:s");
            $clientIP = gethostbyaddr($_SERVER['REMOTE_ADDR']);
            $query2 = "DELETE FROM `casserver`.`casserver_lt` where ticket='$lt';";
            $r2 = mysql_query($query2) or die("Erreur suppression LT");
            $query = "INSERT INTO `casserver`.`casserver_lt` ("
                 ."`id` ,"
                 ."`ticket` ,"
                 ."`created_on` ,"
                 ."`consumed` ,"
                 ."`client_hostname`"
                 .")"
                 ."VALUES ("
                 ."NULL , '$lt', '$date', NULL , '$clientIP'"
                 .");";
            $r = mysql_query($query) or die("Erreur LT sql");

            define('POSTURL', 'https://'.$hostname.'.'.$domain.':8443/login');
            define('POSTVARS', "username=$login&password=$password&lt=$lt&service=".$_POST['service']);

            $ch = curl_init(POSTURL);
            ob_start();
            curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch,CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, POSTVARS);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
            curl_setopt($ch, CURLOPT_HEADER, 1);  // DO NOT RETURN HTTP HEADERS
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  // RETURN THE CONTENTS OF THE CALL
            $Rec_Data = curl_exec($ch);

            $Final_Out=ob_get_clean();
            if (eregi('Set-Cookie:',$Rec_Data)) {
                $infos = explode('Set-Cookie:',$Rec_Data);
		$t = explode(';',$infos[1]);
		$ticket=explode('=',$t[0]);
		setcookie('tgt',"",0,"/","",0);
		setcookie('tgt',"$ticket[1]",0,"/","",0);
		//echo("Votre serveur CAS vous a attribu&eacute; le ticket: $ticket[1] \n Vous avez d&egrave;s lors acc&egrave;s aux applications SSO.");
            } else
                echo "Attention vous n'&ecirc;tes pas identifi&eacute; sur le serveur CAS! Contactez votre administrateur.";

            curl_close($ch);
        }
    } else die('D&#233;sol&#233; cet espace ne vous concerne pas !');
 } else
	redirect_2($baseurl.'lcs/');
exit;
?>
