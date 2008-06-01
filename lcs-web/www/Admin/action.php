<?
/*===========================================
   Projet LcSE3
   Exécution d'une action sur le serveur
   Admin/action.php
   Equipe Tice académie de Caen
   V 1.4 maj : 27/05/2004
   Distribué selon les termes de la licence GPL
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
                           Votre demande d'action a échouée.
                           Si le problème persiste, veuillez contacter le super-utilisateur du serveur LCS.
                        </div><BR>\n");
	// Definition des messages d'info
	$info_1 = gettext("L'action que vous avez ordonnancée est en cours d'exécution...");

	echo "<H1>Effectuer une action sur le serveur $stat_srv</H1>\n";
	#------------------------------------------

	if (! isset($action)) {
		// Form de selection d'actions
		$form = "<form action=\"action.php\" method=\"post\">\n";
		$form .="<H3>Sélectionnez l'action à exécuter : </H3>\n";
		$form .= "<SELECT name='action' SIZE=1>\n";
		$form .= "<OPTION VALUE='settime'>Mettre à l'heure le serveur</OPTION>\n";
		$form .= "<OPTION VALUE='halt'>Arrêter le serveur</OPTION>\n";
		$form .= "<OPTION VALUE='reboot'>Redémarrer le serveur</OPTION>\n";
		$form .= "<OPTION VALUE='update'>Faire une mise a jour système</OPTION>\n";
		$form .= "<OPTION VALUE='synchro_mdp'>Synchro mot de passe admin (mode sans échec)</OPTION>\n";
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
		else 
			exec ("/usr/bin/sudo /usr/share/lcs/scripts/action.sh $action" , $AllOutput, $ReturnValue);			
		if ($DEBUG) {
			echo "$MsgD $ReturnValue ".count($AllOutput)."<br>";
			for ($loop=0;$loop<count($AllOutput);$loop++) {
				echo $AllOutput[$loop]."<br>";
			}
		}
		if ($ReturnValue==0) {
			$Body.= "sur le serveur LCS a été effectuée avec succès.\n";
			echo $info_1;
		} else {
			$Body.= "sur le serveur LCS à échouée.\n";
			echo $alerte_1;
		}
		mail_to ($to, $Subject, $Body, $From);
	} // fin traitement action
}// fin is_admin
else echo "Vous n'avez pas les droits nécessaires pour ordonner cette action...";

include ("../lcs/includes/pieds_de_page.inc.php");
?>
