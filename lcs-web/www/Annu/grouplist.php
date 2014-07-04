<?php
/* =============================================
   Projet LCS-SE3
   Consultation/ Gestion de l'annuaire LDAP
   Equipe Tice academie de Caen
   Distribue selon les termes de la licence GPL
   Derniere modification : 04/04/2014
   ============================================= */
include "includes/check-token.php";
if (!check_acces()) exit;

$login=$_SESSION['login'];
include "../lcs/includes/headerauth.inc.php";
include "includes/ldap.inc.php";
include "includes/ihm.inc.php";

header_html();

if (count($_GET)>0) {
	//configuration objet
 	include ("../lcs/includes/htmlpurifier/library/HTMLPurifier.auto.php");
 	$config = HTMLPurifier_Config::createDefault();
 	$purifier = new HTMLPurifier($config);
    //purification des variables
  	$filter=$purifier->purify($_GET['filter']);
}

if ((is_admin("Annu_is_admin",$login)=="Y") || (is_admin("sovajon_is_admin",$login)=="Y")) {
	$group=search_groups ("(cn=".$filter.")");
	$uids = search_uids ("(cn=".$filter.")", "half");
	$people = search_people_groups ($uids,"(sn=*)","cat");
	if (count($people)) {
		// affichage des resultats
		// Nettoyage des _ dans l'intitule du groupe
		$intitule =  strtr($filter,"_"," ");
		echo "<h1><u>".gettext("Groupe")."</u> : $intitule <font size=\"-2\">".$group[0]["description"]."</font></h1>\n";
		echo gettext("Il y a ").count($people).gettext(" membre");
		if ( count($people) >1 ) echo "s";
		echo gettext(" dans ce groupe")."<br />\n";
		echo "<table border=1><tr><td align=Center>Nom</td><td align=Center>login</td><td align=Center>".gettext("Date naiss")."</td></tr>\n";
		for ($loop=0; $loop < count($people); $loop++) {
			echo "<tr><td>\n";
			if (($people[$loop]["cat"] == "Equipe") or ($people[$loop]["prof"]==1)) {
				echo "<img src=\"images/gender_teacher.gif\" alt=\"Professeur\" width=18 height=18 hspace=1 border=0>\n";

			} else {
				if ($people[$loop]["sexe"]=="F") {
					echo "<img src=\"images/gender_girl.gif\" alt=\"El&egrave;ve\" width=14 height=14 hspace=3 border=0>\n";
				} else {
					echo "<img src=\"images/gender_boy.gif\" alt=\"El&egrave;ve\" width=14 height=14 hspace=3 border=0>\n";
				}
			}
			mb_ereg("([0-9]{8})",$people[$loop]["gecos"],$naiss);
			echo $people[$loop]["fullname"]."</td><td>".$people[$loop]["uid"]."</td><td>".$naiss[0]."</td>\n";

			echo "</tr>\n";
		}
		echo "</table>\n";

		echo "<p>G&#233;n&#233;rer un <a href='grouplist_csv.php?filter=$filter&jeton=".md5($_SESSION['token'].htmlentities("/Annu/grouplist_csv.php"))."' target='blank'>export CSV du groupe</a></p>\n";
  	} else {
    		echo " <b>".gettext("Pas de membres")." </b> ".gettext(" dans le groupe")." $filter.<br />";
  	}
}
include ("../lcs/includes/pieds_de_page.inc.php");
?>
