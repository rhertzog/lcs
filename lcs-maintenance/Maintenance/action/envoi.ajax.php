<?php
extract($_REQUEST);
	include "../Includes/basedir.inc.php";
	include "$BASEDIR/lcs/includes/headerauth.inc.php";
  	include "$BASEDIR/Annu/includes/ldap.inc.php";
  	include "$BASEDIR/Annu/includes/ihm.inc.php";
	include "../Includes/config.inc.php";
	include "../Includes/func_maint.inc.php";

      // Register Global POST
      $nom=$_POST['nom'];
      $secteur=$_POST['secteur'];
      $bat=$_POST['bat'];
      $etage=$_POST['etage'];
      $salle=$_POST['salle'];
      $marque=$_POST['marque'];
      $poste=$_POST['poste'];
      $se=$_POST['se'];
      $typpb=$_POST['typpb'];
      $mail=$_POST['mail'];
      $texte=addslashes($_POST['texte']);

//	html();
        // Traitement des variables transmises par le form 
        $tmp = explode ("&", $salle);
        $tmp1 = explode ("=", $tmp[0]);
      //  $bat = $tmp1[1];
        $tmp1 = explode ("=", $tmp[1]);
     //   $etage = $tmp1[1];       
        $tmp1 = explode ("=", $tmp[2]);
    //    $salle = $tmp1[1];       
        // Fin du traitement
	list ($idpers,$uid)= isauth();
  	if ($idpers == "0") {
		// L'utilisateur n'est pas authentifie
		table_alert ("Vous devez pr&#233;alablement vous authentifier sur votre «Espace perso  LCS» pour acc&#233;der &#233; cette application !");
	} else {
		// L'utilisateur est authentifie
		// Lecture des parametres LDAP associes a cet utilisateur
		list($user, $groups)=people_get_variables($uid, false);
		// Affichage du menu haut
     //           if (is_admin("Maint_is_admin",$uid)=="Y") $type_mnu="team"; else $type_mnu="user";
      //          Aff_mnu($type_mnu);                
		$daterequete=date("d-m-Y");
		// On commence par verifier si les champs sont vides
		if(empty($nom) OR empty($secteur) OR empty($bat) OR empty($salle) OR empty($poste) OR empty($se) OR empty($texte)) {
    			echo "<table  id=\"tabInfo\" title=\"Information\">
					<tr><td align=center>
						<h3><b>ATTENTION!</b></h3><br> <u> Vous devez compl&#233;ter tous les champs marqu&#233;s '*'!</u>
						</td></tr>
				</table>";
    		}
		//si tous les champs obligatoires sont remplis
		else {
			//on envoie les informations dans la table SQL
			/* 	structuration de la table maint_task :
			=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
			Rid : Numero unique d'identification de la demande
			Acq : Statut de la demande 0 : non acquite 1 : Acquite 2 : In progress 3 : Closed
			Host : Hote source de la demande
			Owner : Emetteur de la demande
			OwnerMail : Mel de l'emetteur
			Author : Responsable(s) du traitement du pb
			Sector : secteur
			Building : Batiment
			Room : N° de la salle
			NumComp : N° du poste
			Mark : Marque du PC
			Os : systeme d'exploitation
			Cat : categorie de la demande
			Summary : Resume dela demande
			Content : Description de la demande
			OpenTimeStamp : date heure de la demande
			CloseTimeStamp : date et heure de la cloture
			*/
        	//require "Includes/config.inc.php";
			$ladate=strtotime (date("Y-m-d H:i:s"));
			//enregistrement de la demande
        	$result=mysql_query("INSERT INTO maint_task (Host,Owner,OwnerMail,Sector,Building,Room,NumComp,Mark,Os,Cat,Content,OpenTimeStamp)
		      VALUES ('$REMOTE_ADDR','$nom','$mail','$secteur','$bat','$salle','$poste','$marque','$se','$typpb','$texte','$ladate')");
        		mysql_close();
			//et on envoie le mail
			mail(
				"$MAILMAINT,$mail",
				"[MaintInfo]".$typpb."_".date("d-m-Y H:i:s"),
				"$nom a laiss&#233; une requ&#232;te info le ".date("d-m-Y H:i:s")."\n
					Secteur d'enseignement : $secteur\n
					Salle: $bat $salle\n
					Ordinateur: Marque $marque Poste $poste SE $se\n
					Probl&#232;me: $typpb\n
					\nDescription :\n
 						stripslashes($texte)\n
					IP poste source : $REMOTE_ADDR",
				"From:$mail"
			) or die("Votre message n'a pu parvenir au technicien charg&#233; de la machine concern&#233;e.");
			// on Reaffiche la demande
			echo "  	<div  id=\"tabInfo\" style=\"font-size:.75em\" title=\"Merci  ",utf8_encode(ucwords($prenom))," ",utf8_encode(strtoupper($nom)),"\">
					<p>Nous sommes le $daterequete<br>Vous avez d&#233;clar&#233; un dysfonctionnement de l'ordinateur N&#176; <b>",strtoupper($poste),"</b> , marque <b>$marque</b> ,
						&#233;quip&#233; du syst&#232;me d'exploitation <b>$se</b> en salle <b>$bat&nbsp;$salle</b> ,secteur d'enseignement <b>$secteur</b>.
						<br></p>
						<p><b><u>Votre description du probl&#232;me :</u></b>
						<br/>".stripslashes($texte)."</p>
					</div>";
		}
	}
//include "Includes/pieds_de_page.inc.php";
?>