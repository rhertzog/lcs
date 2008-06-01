<?
/*===========================================
   Projet LcSE3
   Ex�cution d'une action sur le serveur
   Admin/action.php
   Equipe Tice acad�mie de Caen
   V 1.4 maj : 27/05/2004
   Distribu� selon les termes de la licence GPL
   ============================================= */

include ("../lcs/includes/headerauth.inc.php");
include ("../Annu/includes/ldap.inc.php");
include ("../Annu/includes/ihm.inc.php");

function mail_to ($to, $Subject, $Body, $From) {
	$mailHeaders = "From: $From";
	mail ($to, $Subject, $Body, $mailHeaders);
}

list ($idpers, $login)= isauth();
if ($idpers == "0")    header("Location:$urlauth");

	echo "<HTML>\n";
	echo "	<HEAD>\n";
	echo "		<TITLE>...::: Interface d'administration Serveur LCS :::...</TITLE>\n";
	echo "		<LINK  href='../Annu/style.css' rel='StyleSheet' type='text/css'>\n";
	echo "	</HEAD>\n";
    	echo "	<BODY>\n";

if (is_admin("system_is_admin",$login)=="Y") {
	// Definition des messages d'alerte
	$alerte_1=gettext("
                        <div class='error_msg'>
                           Votre demande d'action a �chou�e.
                           Si le probl�me persiste, veuillez contacter le super-utilisateur du serveur LCS.
                        </div><BR>\n");
	// Definition des messages d'info
	$info_1 = gettext("L'action que vous avez ordonnanc�e est en cours d'ex�cution...");

	echo "<H1>Effectuer une action sur le serveur $stat_srv</H1>\n";
	#------------------------------------------

	if (! isset($action)) {
		// Form de selection d'actions
		$form = "<form action=\"action.php\" method=\"post\">\n";
		$form .="<H3>S�lectionnez l'action � ex�cuter : </H3>\n";
		$form .= "<SELECT name='action' SIZE=1>\n";
		$form .= "<OPTION VALUE='settime'>Mettre � l'heure le serveur</OPTION>\n";
		$form .= "<OPTION VALUE='halt'>Arr�ter le serveur</OPTION>\n";
		$form .= "<OPTION VALUE='reboot'>Red�marrer le serveur</OPTION>\n";
		$form .= "<OPTION VALUE='update'>Faire une mise a jour syst�me</OPTION>\n";
		$form .= "<OPTION VALUE='synchro_mdp'>Synchro mot de passe admin (mode sans �chec)</OPTION>\n";
		$form .= "</SELECT>\n";
		$form.="<input type=\"submit\" value=\"Valider\">\n";
		$form.="</form>\n";
		echo $form;
	} else {
		// Traitement de l'action.
		echo "<H3>Traitement de l'action $action sur le serveur LCS</H3>\n";
		$Subject="[LCS T�che d'administration] $action\n";
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
		else 
			exec ("/usr/bin/sudo /usr/share/lcs/scripts/action.sh $action" , $AllOutput, $ReturnValue);			
		if ($DEBUG) {
			echo "$MsgD $ReturnValue ".count($AllOutput)."<br>";
			for ($loop=0;$loop<count($AllOutput);$loop++) {
				echo $AllOutput[$loop]."<br>";
			}
		}
		if ($ReturnValue==0) {
			$Body.= "sur le serveur LCS a �t� effectu�e avec succ�s.\n";
			echo $info_1;
		} else {
			$Body.= "sur le serveur LCS � �chou�e.\n";
			echo $alerte_1;
		}
		mail_to ($to, $Subject, $Body, $From);
	} // fin traitement action
}// fin is_admin
else echo "Vous n'avez pas les droits n�cessaires pour ordonner cette action...";

include ("../lcs/includes/pieds_de_page.inc.php");
?>
