<?php
/*===========================================
   Projet LcSE3
   Equipe Tice academie de Caen
   Distribue selon les termes de la licence GPL
   Derniere modification : 28/03/2014
   ============================================= */
include "headerauth.inc.php";
include ("../../Annu/includes/ldap.inc.php");
include ("../../Annu/includes/ihm.inc.php");
//init variables
$lt=$clientIP=$str="";
// Ce script cree les tickets LT et TGT pour le CAS et recupere les donnees LDAP pour la suite
session_name("Lcs");
@session_start();
if ( !isset($_SESSION['login'])) {
    header("Location:$urlauth");
    exit;
}
$login=$_SESSION['login'];
if ($login)  {
	if ($_SERVER['REQUEST_METHOD']==='POST') {  // REQUIRE POST OR DIE
		// Decode password
    	$password = urldecode(xoft_decode($_COOKIE['LCSuser'], $key_priv));
		// INITIALIZE ALL VARS
		$ch='';
		$Rec_Data='';
		$service = "http://".$hostname.".".$domain."/lcs/index.php?url_redirect=accueil.php";
		//on casse le cookie
		setcookie('lt',"$lt",0,"/","",0);
		if (!@mysql_select_db("casserver", $authlink))
    		die ("S&#233;lection de base de donn&#233;es impossible.");
                                    $clientIP = gethostbyaddr($_SERVER['REMOTE_ADDR']);
                                    $clientIP_escp=  mysql_real_escape_string($clientIP);
		$query="SELECT * FROM `casserver`.`casserver_lt` where consumed = NULL and client_hostname='$clientIP_escp' ORDER By id DESC;";
		$result=@mysql_query($query, $authlink);
		if (@mysql_num_rows($result) > 0 )
                                    $lt = (mysql_result($result,0,"ticket"));
		else {
                                    // Generate Login Ticket
                                    $letters = "1234567890ABCDEF";
                                    while(strlen($str)<19){
                                            $pos = rand(0,15);
                                            $str .= $letters[$pos];
                                    }
            $lt='LT-'.time()."r".$str;

            $date = date("Y-m-d H:i:s");
            $query2 = "DELETE FROM `casserver`.`casserver_lt` where ticket='$lt';";
            $r2 = @mysql_query($query2) or die("Erreur suppression LT");
            $query = "INSERT INTO `casserver`.`casserver_lt` ("
                 ."`id` ,"
                 ."`ticket` ,"
                 ."`created_on` ,"
                 ."`consumed` ,"
                 ."`client_hostname`"
                 .")"
                 ."VALUES ("
                 ."NULL , '$lt', '$date', NULL , '$clientIP_escp'"
                 .");";
            $r = @mysql_query($query) or die("Erreur LT sql");

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
            if (mb_eregi('Set-Cookie:',$Rec_Data)) {
                $infos = explode('Set-Cookie:',$Rec_Data);
				$t = explode(';',$infos[1]);
				$ticket=explode('=',$t[0]);
				$ticket_propre=mb_substr($ticket[1], 0, 33);
				#system ("echo 'ticket $ticket_propre' > /var/log/lcs/debugcas.log");
				setcookie('tgt',"",0,"/","",0);
				setcookie('tgt',"$ticket_propre",0,"/","",0);
				#echo("Votre serveur CAS vous a attribu&eacute; le ticket: $ticket[1] \n Vous avez d&egrave;s lors acc&egrave;s aux applications SSO.");
            } else
                echo "Attention vous n'&ecirc;tes pas identifi&eacute; sur le serveur CAS! Contactez votre administrateur.";

            curl_close($ch);
        }
    } else die('D&#233;sol&#233; cet espace ne vous concerne pas !');
 } else
	redirect_2($baseurl.'lcs/');
exit;
?>
