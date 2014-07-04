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

if ( count($_POST)>0 ) {
  	//configuration objet
 	include ("../lcs/includes/htmlpurifier/library/HTMLPurifier.auto.php");
 	$config = HTMLPurifier_Config::createDefault();
 	$purifier = new HTMLPurifier($config);
    	//purification des variables
  	if ( isset($_POST['right']))  $right=$purifier->purify($_POST['right']);
  	if ( isset($_POST['filtrecomp']))  $filtrecomp=$purifier->purify($_POST['filtrecomp']);
  	if ( isset($_POST['delete_right']))  $delete_right=$purifier->purify($_POST['delete_right']);
  	if ( isset($_POST['type'])) $type=$purifier->purify($_POST['type']);
  	if ( count($_POST['old_rights'])>0) $old_rights=$purifier->purifyArray($_POST['old_rights']);
}


header_html();

if (ldap_get_right("lcs_is_admin",$login)=="Y") {
  aff_trailer ("1");
    // Affichage du formulaire de selection des droits
    if (!isset($right)) {
        echo "<H3>S&#233;lection du droit &#224; retirer</H3>";
        $list_rights=search_machines("objectclass=groupOfNames","rights");
        if ( count($list_rights)>0) {
            echo "<FORM action=\"delete_right.php\" method=\"post\">\n";
            echo "<SELECT NAME=\"right\" SIZE=\"1\">";
            for ($loop=0; $loop < count($list_rights); $loop++) {
                echo "<option value=".$list_rights[$loop]["cn"].">".$list_rights[$loop]["cn"]."\n";
            }
            echo "</SELECT>&nbsp;&nbsp;\n";
            echo '<input name="jeton" type="hidden"  value="'.md5($_SESSION['token'].htmlentities($_SERVER['PHP_SELF'])).'" />';
            echo "<input type=\"submit\" value=\"Valider\">\n";
            echo "</FORM>\n";
        }
    } else
    // Affichage du formulaire de remplissage des droits
    if (!$delete_right ) {
        // Filtrage des noms
        echo "<FORM action=\"delete_right.php\" method=\"post\">\n";
        echo "<P>Lister les noms contenant: ";
        echo "<INPUT TYPE=\"text\" NAME=\"filtrecomp\"\n VALUE=\"$filtrecomp\" SIZE=\"8\">";
        echo "<input type=\"hidden\" name=\"right\" value=\"$right\">\n";
        echo '<input name="jeton" type="hidden"  value="'.md5($_SESSION['token'].htmlentities($_SERVER['PHP_SELF'])).'" />';
        echo "<input type=\"submit\" value=\"Valider\">\n";
        echo "</FORM>\n";
        // Lecture des membres du droit
        $mp_all=gof_members($right,"rights",0);
        // Filtrage selon critere
        if ("$filtrecomp"=="") $mp=$mp_all;
        else {
            $lmloop=0;
            $mpcount=count($mp_all);
            for ($loop=0; $loop < count($mp_all); $loop++) {
                $mach=$mp_all[$loop];
                if (mb_ereg($filtrecomp,$mach)) $mp[$lmloop++]=$mach;
            }
        }
        if ( count($mp)>15) $size=15; else $size=count($mp);
        if ( count($mp)>0) {
            $form = "<form action=\"delete_right.php\" method=\"post\">\n";
            $form.="<p>S&#233;lectionnez les personnes ou groupes &#224; priver du droit:</p>\n";
            $form.="<p><select size=\"".$size."\" name=\"old_rights[]\" multiple=\"multiple\">\n";
            echo $form;
            for ($loop=0; $loop < count($mp); $loop++) {
                $value=extract_login($mp[$loop]);
                if (mb_ereg($groupsRdn,$mp[$loop])) {
			$type = "groupe";
			$value="$value ($type)";
		} else {
			$type = "utilisateur";
			$value="$value ($type)";
		}
		echo "<option value=".$mp[$loop].">".$value;
            }
            $form="</select></p>\n";
            $form.="<input type=\"hidden\" name=\"delete_right\" value=\"true\">\n";
            $form.="<input type=\"hidden\" name=\"right\" value=\"$right\">\n";
	    	$form.="<input type=\"hidden\" name=\"type\" value=\"$type\">\n";
            $form.="<input type=\"reset\" value=\"R&#233;initialiser la s&#233;lection\">\n";
            $form.='<input name="jeton" type="hidden"  value="'. md5($_SESSION['token'].htmlentities($_SERVER['PHP_SELF'])).'" /> ';
            $form.="<input type=\"submit\" value=\"Valider\">\n";
            $form.="</form>\n";
            echo $form;
        } else {
            $message =  gettext("Il n'y a rien &#224; supprimer !");
            echo $message;
        }
    } else {
        // Suppression des droits
            echo "<H3>Modification du droit <U>$right</U></H3>";
            echo "<P>Vous avez s&#233;lectionn&#233; ". count($old_rights)." droit(s)<BR>\n";
            for ($loop=0; $loop < count($old_rights); $loop++) {
                $pers=$old_rights[$loop];
                $pers=extract_login ($pers);
                echo "Suppression de ".$pers." du droit <U>$right</U><BR>";
                $pDn = "cn=".$right.",".$rightsRdn.",".$ldap_base_dn;
                if ($type=="utilisateur") $persDn = "uid=$pers".",".$peopleRdn.",".$ldap_base_dn;
		else $persDn = "cn=$pers".",".$groupsRdn.",".$ldap_base_dn;
                exec ("$scriptsbinpath/groupDelEntry.pl ". escapeshellarg($persDn). " ". escapeshellarg($pDn));
                echo "<BR>";
            }
        }

  }
  include ("../lcs/includes/pieds_de_page.inc.php");
?>
