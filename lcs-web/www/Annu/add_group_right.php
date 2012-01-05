<?php

include "../lcs/includes/headerauth.inc.php";
include "includes/ldap.inc.php";
include "includes/ihm.inc.php";

list ($idpers,$login)= isauth();
if ($idpers == "0") header("Location:$urlauth");

$uid=$_GET['cn'];
if ($uid=="") { $uid=$_POST['cn']; }
$action=$_POST['action'];
$delrights=$_POST['delrights'];
$newrights=$_POST['newrights'];

header_html();

$filtre = "8_".$uid;
aff_trailer ("3");

if (ldap_get_right("se3_is_admin",$login)=="Y") {

	// Ajoute un droit
	if ($action == "AddRights") {
      		// Inscription des droits dans l'annuaire
      		echo "<h3>".gettext("Inscription des droits pour")." <u>$uid</u></h3>";
      		echo "<p>".gettext("Vous avez s&#233;lectionn&#233; ") ."". count($newrights)."".gettext(" droit(s)")."<br />\n";
      		for ($loop=0; $loop < count($newrights); $loop++) {
        		$right=$newrights[$loop];
        		echo gettext("D&#233;l&#233;gation du droit")." <U>$right</U> ".gettext("&#224; l'utilisateur")." $uid<br />";
        		$cDn = "cn=$uid,$groupsRdn,$ldap_base_dn";
        		$pDn = "cn=$right,$rightsRdn,$ldap_base_dn";
        		exec ("$scriptsbinpath/groupAddEntry.pl \"$cDn\" \"$pDn\"");
        		echo "<br />";
      		}
    	}

	// Supprime un droit
	if ( $action == "DelRights" ) {
      		// Suppression des droits dans l'annuaire
      		echo "<h3>".gettext("Suppression des droits pour")." <u>$uid</u></h3>";
      		echo "<p>".gettext("Vous avez s&#233;lectionn&#233; ") ."". count($delrights)." droit(s)<br />\n";
      		for ($loop=0; $loop < count($delrights); $loop++) {
        		$right=$delrights[$loop];
        		echo gettext("Suppression du droit")." <u>$right</u> ".gettext("pour le groupe")." $uid<br />";
        		$cDn = "cn=$uid,$groupsRdn,$ldap_base_dn";
        		$pDn = "cn=$right,$rightsRdn,$ldap_base_dn";
        		exec ("$scriptsbinpath/groupDelEntry.pl \"$cDn\" \"$pDn\"");
        		echo "<br />";
      		}
    	}

    	list($user, $groups)=people_get_variables($uid, true);
    	// Affichage du nom et de la description de l'utilisateur
    	echo "<h3>".gettext("D&#233;l&#233;gation de droits &#224; ")."". $user["fullname"] ." (<u>$uid</u>)</h3>\n";
    	echo gettext("S&#233;lectionnez les droits &#224; supprimer (liste de gauche) ou &#224; ajouter (liste de droite)");
    	echo gettext("et validez &#224; l'aide du bouton correspondant.")."<br /><br />\n";
    	// Lecture des droits disponibles
    	$userDn="cn=$uid,$groupsRdn,$ldap_base_dn";
    	$list_possible_rights=search_machines("(!(member=$userDn))","rights");
    	$list_current_rights=search_machines("(member=$userDn)","rights");
    	?>

	<form method="post" action="../Annu/add_group_right.php">
  	<input TYPE="hidden" VALUE="<?php echo $uid;?>" NAME="cn">
  	<input TYPE="hidden" NAME="action">
  	<table BORDER=1 CELLPADDING=3 CELLSPACING=1 RULES=COLS><tr>
  	<th align=center><?php echo gettext("Droits actuels "); ?>

	<u onmouseover="this.T_SHADOWWIDTH=5;this.T_STICKY=1;return escape<?php echo gettext("('Les droits indiqu&#233;s dans cette liste sont les droits effectifs.<br />Tous les membres de ce groupe disposeront de ces droits.')"); ?>"><img name="action_image2"  src="../images/help-info.gif" alt="Help"></u>
  	<th align="center"><?php echo gettext("Droits disponibles"); ?>
	<u onmouseover="this.T_SHADOWWIDTH=5;this.T_STICKY=1;return escape<?php echo gettext("('<b>lcs_is_admin</b> Donne le droit d\'administration sur tout le syst&#232;me LCS. Ce droit l\'emporte sur tous les autres.<br /><b>se3_is_admin</b> Donne le droit d\'administration sur tout le syst&#232;me SE3. Ce droit l\'emporte sur tous les autres.<br /><b>Annu_is_admin</b> Donne tous les droits sur l\'annuaire (Ajouter, supprimer, modifier des utilisateurs ou des groupes).<br /><b>sovajon_is_admin</b> D&#233;l&#233;gue le droit de changer les mots de passe &#224; un professeur. Il faut que celui-ci soit professeur de la classe.<br /><b>system_is_admin</b> Donne le droit de visualiser les informations syst&#232;me du serveur.<br /><b>computers_is_admin</b> Permet de g&#233;rer les machines clientes (Cr&#233;er ou supprimer des machines des parcs, &#233;tat des machines clientes...)<br /><b>printers_is_admin</b> Gestion des files d\'impression des imprimantes.<br /><b>echange_can_administrate</b> Permet de g&#233;rer les r&#233;pertoires _echanges dans les r&#233;pertoires classes.<br /><b>inventaire_can_read</B> Permet de consulter l\'inventaire<br /><b>annu_can_read</b> Permet de consulter l\'annuaire. Par d&#233;faut les membres du groupe Profs ont ce droit.<br /><b>maintenance_can_write</b> Permet de d&#233;clarer une panne sur une machine dans l\'interface de maintenance.<br /><b>parc_can_view</b> Permet de voir les parcs.<br /><b>parc_can_manage</b> Permet de d&#233;l&#233;guer la gestion d\'un parc &#224; une personne.<br /><b>smbweb_is_open</b> Donne le droit d\'acc&#232;s depuis l\'interface smbwebclient du Slis ou du Lcs (optionnel).')"); ?>"><img name="action_image2"  src="../images/help-info.gif" alt="Help"></u>

  	</th></tr>
  	<tr><td VALIGN="TOP">

	<?php

  	if   ( count($list_current_rights)>15) $size=15; else $size=count($list_current_rights);
    	if ( $size>0) {
    		echo "<SELECT NAME=\"delrights[]\" SIZE=\"$size\" multiple=\"multiple\">";
    	  	for ($loop=0; $loop < count($list_current_rights); $loop++) {
    		      	echo "<option value=".$list_current_rights[$loop]["cn"].">".$list_current_rights[$loop]["cn"]."\n";
      		}
		?>

		</select><br /><br />
  		<input type="submit" value="Retirer ces droits" onClick="this.form.action.value ='DelRights';return true;">
		<?php
    	} else {
      		echo "<U>$uid</U> ".gettext("n'a aucun droit propre");
    	}
	?>
  	</td><td VALIGN="TOP">
	<?php
	if   ( count($list_possible_rights)>15) $size=15; else $size=count($list_possible_rights);
    	if ( $size>0) {
      		echo "<select NAME=\"newrights[]\" SIZE=\"$size\" multiple=\"multiple\">";
      		for ($loop=0; $loop < count($list_possible_rights); $loop++) {
          		echo "<option value=".$list_possible_rights[$loop]["cn"].">".$list_possible_rights[$loop]["cn"]."\n";
      		}
		?>
  		</select><br /><br />
  		<input type="submit" value="<?php echo gettext("Ajouter ces droits"); ?>" onClick="this.form.action.value ='AddRights';return true;">
		<?php
    	} else {
      		echo "<U>$uid</U>".gettext(" a tous les droits");
    	}
	?>
	</td></tr></table>
	</form>
	<?php

} else {
    	echo "<div class=error_msg>".gettext("Cette application, n&#233;cessite les droits d'administrateur du serveur LCS !")."</div>";
}

include ("../lcs/includes/pieds_de_page.inc.php");
?>
