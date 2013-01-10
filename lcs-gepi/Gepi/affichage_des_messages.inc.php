<?php
// suppression d'un message du panneau d'affichage
if (isset($_POST['supprimer_message']))
	{
	$r_sql="DELETE FROM `messages` WHERE `id`='".$_POST['supprimer_message']."'";
	mysql_query($r_sql);
	}


// ----- Affichage des messages -----
$today=mktime(0,0,0,date("m"),date("d"),date("Y"));
$now=time();

// on supprime les messages obsolètes
$sql="DELETE FROM `messages` WHERE ((`date_fin`+86400 <= ".$today.") && (`statuts_destinataires`='_') && (`login_destinataire`='".$_SESSION['login']."'));";
@mysql_query($sql);

$sql="SELECT id, texte, date_debut, date_fin, date_decompte, auteur, statuts_destinataires, login_destinataire FROM messages
	WHERE (
	texte != '' and
	date_debut <= '".$now."' and
	date_fin >= '".$now."'
	)
    order by date_debut DESC, id DESC;";
$appel_messages = mysql_query($sql);


$nb_messages = mysql_num_rows($appel_messages);
$ind = 0;
$texte_messages = '';
$texte_messages_simpl_prof = ''; // variable uniquement utilisée dans accueil_simpl_prof.php
$affiche_messages = 'no';
while ($ind < $nb_messages) {
	$statuts_destinataires1 = mysql_result($appel_messages, $ind, 'statuts_destinataires');
	$login_destinataire1 = mysql_result($appel_messages, $ind, 'login_destinataire');
	$autre_message = "";

	if ((strpos($statuts_destinataires1, mb_substr($_SESSION['statut'], 0, 1))) || ($_SESSION['login']==$login_destinataire1)) {
		if ($affiche_messages == 'yes') {
			$autre_message = "hr";
			$texte_messages_simpl_prof .= "<hr />";
		}
		$affiche_messages = 'yes';
		$content = mysql_result($appel_messages, $ind, 'texte');

		// _DECOMPTE_
		if(strstr($content, '_DECOMPTE_')) {
			$nb_sec=mysql_result($appel_messages, $ind, 'date_decompte')-time();
			if($nb_sec>0) {
				$decompte_remplace="";
			}
			elseif($nb_sec==0) {
				$decompte_remplace=" <span style='color:red'>Vous êtes à l'instant T</span> ";
			}
			else {
				$nb_sec=$nb_sec*(-1);
				$decompte_remplace=" <span style='color:red'>date dépassée de</span> ";
			}

			$decompte_j=floor($nb_sec/(24*3600));
			$decompte_h=floor(($nb_sec-$decompte_j*24*3600)/3600);
			$decompte_m=floor(($nb_sec-$decompte_j*24*3600-$decompte_h*3600)/60);

			if($decompte_j==1) {$decompte_remplace.=$decompte_j." jour ";}
			elseif($decompte_j>1) {$decompte_remplace.=$decompte_j." jours ";}

			if($decompte_h==1) {$decompte_remplace.=$decompte_h." heure ";}
			elseif($decompte_h>1) {$decompte_remplace.=$decompte_h." heures ";}

			if($decompte_m==1) {$decompte_remplace.=$decompte_m." minute";}
			elseif($decompte_m>1) {$decompte_remplace.=$decompte_m." minutes";}

			$content=preg_replace("/_DECOMPTE_/",$decompte_remplace,$content);
		}
		// fin _DECOMPTE_
		
		// gestion du token (csrf_alea)
		// si elle est présente la variable _CRSF_ALEA_ est remplacée lors de l'affichage du message
		// par la valeur du token de l'utilisateur, par exemple on peut ainsi inclure dans un message
		// un lien appelant un script : <a href="module/script.php?id=33&csrf_alea=_CRSF_ALEA_">Vers le script</a>
		$pos_crsf_alea=strpos($content,"_CRSF_ALEA_");
		if($pos_crsf_alea!==false)
			$content=preg_replace("/_CRSF_ALEA_/",$_SESSION['gepi_alea'],$content);

		//$tbs_message[]=array("suite"=>$autre_message,"message"=>$content);
		
		// dans accueil.php
		if (isset($afficheAccueil) && is_object($afficheAccueil)) $afficheAccueil->message[]=array("suite"=>$autre_message,"message"=>$content);
		// dans accueil_simpl_prof.php
		$texte_messages_simpl_prof .= $content;
	}
	$ind++;
}
// pour accueil_simpl_prof.php
if ((basename($_SERVER['SCRIPT_NAME'])=="accueil_simpl_prof.php") && ($affiche_messages == 'yes')) {
	echo "<table id='messagerie' summary=\"Ce tableau contient les informations sur lesquelles on souhaite attirer l'attention\">\n";
	echo "<tr><td>".$texte_messages_simpl_prof;
	echo "</td></tr></table>\n";
	}
?>