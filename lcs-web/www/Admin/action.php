<?
/*===========================================
   Projet LcSE3 Ex&#233;cution d'une action sur le serveur
   Admin/action.php
   Equipe Tice acad&#233;mie de Caen
   Derniere mis a jour : 16/10/2008
   Distribue selon les termes de la licence GPL
   ============================================= */

include ("../lcs/includes/headerauth.inc.php");
include ("../Annu/includes/ldap.inc.php");
include ("../Annu/includes/ihm.inc.php");

$action = $_POST[action];

function mail_to ($to, $Subject, $Body, $From) {
	$mailHeaders = "From: $From";
	mail ($to, $Subject, $Body, $mailHeaders);
}

list ($idpers, $login)= isauth();
if ($idpers == "0")    header("Location:$urlauth");

	echo "<html>\n";
	echo "	<head>\n";
	echo "		<title>...::: Interface d'administration Serveur LCS :::...</title>\n";
	echo "		<link  href='../Annu/style.css' rel='StyleSheet' type='text/css'>\n";
	echo "	</head>\n";
    	echo "	<body>\n";

if (is_admin("system_is_admin",$login)=="Y") {
	// Definition des messages d'alerte
	$alerte_1=gettext("
                        <div class='error_msg'>
                           Votre demande d'action a &#233;chou&#233;e.
                           Si le probl&#232;me persiste, veuillez contacter le super-utilisateur du serveur LCS.
                        </div><BR>\n");
	// Definition des messages d'info
	$info_1 = gettext("L'action que vous avez ordonnanc&#233;e est en cours d'ex&#233;cution...");

	echo "<H1>Effectuer une action sur le serveur $stat_srv</H1>\n";
	#------------------------------------------

	if (! isset($action)) {
		// Form de selection d'actions
		$form = "<form action=\"action.php\" method=\"post\">\n";
		$form .="<H3>S&#233;lectionnez l'action &#224; ex&#233;cuter : </H3>\n";
		$form .= "<SELECT name='action' SIZE=1>\n";
		$form .= "<OPTION VALUE='settime'>Mettre &#224; l'heure le serveur</OPTION>\n";
		$form .= "<OPTION VALUE='halt'>Arr&#234;ter le serveur</OPTION>\n";
		$form .= "<OPTION VALUE='reboot'>Red&#233;marrer le serveur</OPTION>\n";
		$form .= "<OPTION VALUE='update'>Faire une mise a jour syst&#232;me</OPTION>\n";
		$form .= "<OPTION VALUE='synchro_mdp'>Synchro mot de passe admin (mode sans &#233;chec)</OPTION>\n";
		$form .= "</SELECT>\n";
		$form.="<input type=\"submit\" value=\"Valider\">\n";
		$form.="</form>\n";
		echo $form;
	} else {
		// Traitement de l'action.
		echo "<H3>Traitement de l'action $action sur le serveur LCS</H3>\n";
		$Subject="[LCS Tâche d'administration] $action\n";
		list($user,$groups)=people_get_variables("admin", true);
		$to = $user["email"];
		$From = "root@$domain";
		$Subject ="\nCR d'action sur serveur LCS\n";
		$Body = $Subject;
		$Body.= "L'action $action \n";
		$Body.= $commandes."\n";
		// Execution de l'action sur le serveur
		if ( $action == "settime" )
			exec ("/usr/bin/sudo /usr/share/lcs/scripts/action.sh $action $ntpserv" , $AllOutput, $ReturnValue);
		elseif ( $action == "synchro_mdp" ) {
                        $pass = urldecode( xoft_decode($HTTP_COOKIE_VARS['LCSuser'],$key_priv) );
			exec ("/usr/bin/sudo /usr/share/lcs/scripts/action.sh $action $pass" , $AllOutput, $ReturnValue);
                } else
			exec ("/usr/bin/sudo /usr/share/lcs/scripts/action.sh $action" , $AllOutput, $ReturnValue);			
		if ($DEBUG) {
			echo "$MsgD $ReturnValue ".count($AllOutput)."<br>";
			for ($loop=0;$loop<count($AllOutput);$loop++) {
				echo $AllOutput[$loop]."<br>";
			}
		}
		if ($ReturnValue==0) {
			$Body.= "sur le serveur LCS a ete effectuee avec succes.\n";
			echo $info_1;
		} else {
			$Body.= "sur le serveur LCS a echouee.\n";
			echo $alerte_1;
		}
		mail_to ($to, $Subject, $Body, $From);
	} // fin traitement action
}// fin is_admin
else echo "Vous n'avez pas les droits n&#233;cessaires pour ordonner cette action...";

include ("../lcs/includes/pieds_de_page.inc.php");
?>
