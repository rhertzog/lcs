<?php
/* =============================================
 *    Projet LCS-SE3
 *    Admin/eff_vide_groups.php
 *    David Gloux david.gloux@tice.ac-caen.fr
 *    Derniere Version  : 04/08/2007
 *    Distribué selon les termes de la licence GPL
 *    ============================================= */

include "../lcs/includes/headerauth.inc.php";
//include "includes/ldap.inc.php";
//include "includes/ihm.inc.php";
include ("../lcs/includes/headerauth.inc.php");
include ("../Annu/includes/ldap.inc.php");
include ("../Annu/includes/ihm.inc.php");

list ($idpers,$login)= isauth();
if ($idpers == "0") header("Location:$urlauth");

$html = "<HTML>\n";
$html .= "      <HEAD>\n";
$html .= "              <TITLE>...::: Interface d'administration Serveur LCS :::...</TITLE>\n";
$html .= "              <LINK  href='../Annu/style.css' rel='StyleSheet' type='text/css'>\n";
$html .= "      </HEAD>\n";
$html .= "      <BODY>\n";
echo $html;

if (is_admin("lcs_is_admin",$login)=="Y") {
    
    //validation
    if (isset ($valider)) {
	for ($loop=0; $loop < count($vides_gr); $loop++) {
	    exec ("$scriptsbinpath/groupDel.pl $vides_gr[$loop]",$AllOutPut,$ReturnValue);
	    if ($ReturnValue == "0") {
		echo "<strong>Le groupe $vides_gr[$loop] a été supprimé avec succès.</strong><br>\n";
	    } 
	    else echo "<div class='error_msg'>Echec de la suppression <font color='black'>(type d'erreur : $ReturnValue)</font>, veuillez contacter <A HREF='mailto:$MelAdminLCS?subject=PB Effacement groupe'>l'administrateur du système</A></div><BR>\n";
	    
	}
    }
    
    //traitement
    if (!isset ($valider)) {
	if ( $phase != 1 ) {
	    // Affichage du sablier
	    echo "<meta HTTP-EQUIV=\"Refresh\" CONTENT=\"3;url='$PHP_SELF?phase=1'\">\n";
	    echo $phase;
	    echo "<div align='center'><img src=\"Images/wait.gif\" title=\"Patientez...\" align=\"middle\" border=\"0\">&nbsp;Recherche des groupes vides en cours. Veuillez patienter...</div>";
	}
	else {	    
	    //recherche de tous les groupes vides
	    $filter = "(cn=*)";
	    $groups=search_groups($filter);
	    $j=0;
	    for ($loop=0; $loop < count($groups); $loop++) {
		$affiche = 1;
		$filter1=$groups[$loop]["cn"];
		$uids = search_uids ("(cn=".$filter1.")", "half");
		$people = search_people_groups ($uids,"(sn=*)","cat");
		if (count($people)==0) {
		    $listegroupesvides[$j] = $groups[$loop]["cn"];
		    $j++;
		}
	    }
	    
	    //formulaire
	    $i=0;
	    for ($loop=0; $loop < count ($listegroupesvides) ; $loop++) {
		if (!ereg ("^Eleves", $listegroupesvides[$loop]) &&
		    !ereg ("^overfill", $listegroupesvides[$loop]) &&
		    !ereg ("^lcs-users", $listegroupesvides[$loop]) &&
		    !ereg ("^machines", $listegroupesvides[$loop]) &&
		    !ereg ("^Administratifs", $listegroupesvides[$loop]) &&
		    !ereg ("^Profs", $listegroupesvides[$loop]) ) {
		    $groupevide[$i] = $listegroupesvides[$loop];
		    $i++;}
	    }
	    if ( count($groupevide) == 0 )  echo "<b>Il n'existe pas de groupes vides dans votre annuaire.</b>";
	    else {
		echo "<form action=\"eff_vide_groups.php\" method=\"post\">";
		echo "<B>Sélectionner le(s) groupe(s) vides que vous désirez effacer : </B><BR><BR>";
		echo "<select name=\"vides_gr[]\" value=\"$vides_gr\" size=\"5\" multiple=\"multiple\">";
		for ($loop=0; $loop < count ($groupevide) ; $loop++) {
		    echo "<option value=".$groupevide[$loop].">".$groupevide[$loop];
		}
		echo "</select><br><br>";
		echo " <input type=\"hidden\" name=\"valider\" value=\"$valider\">
		       <input type=\"submit\" value=\"valider\">
		       <input type=\"reset\" value=\"Réinitialiser la sélection\">";
		echo "</form>";
	    }
	}
    }
}// fin is_admin
else echo "Vous n'avez pas les droits nécessaires pour ordonner cette action...";

include ("../lcs/includes/pieds_de_page.inc.php");

?>
