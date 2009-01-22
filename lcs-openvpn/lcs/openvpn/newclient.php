<?php
/* =============================================
   Projet LCS-SE3
   Liste des profs pour openvpn
   Equipe Tice académie de Caen
   Derniere modifications : 21/01/2009
   Distribué selon les termes de la licence GPL
   ============================================= */
include ("/var/www/lcs/includes/headerauth.inc.php");
include ("/var/www/Annu/includes/ldap.inc.php");
include ("/var/www/Annu/includes/ihm.inc.php");
header_html();

$msgIntro = "<H1>Gestion LCS OpenVPN</H1>\n";
list ($idpers, $login)= isauth();

if (ldap_get_right("lcs_is_admin",$login)!="Y")
  die (gettext("Vous n'avez pas les droits suffisants pour acc&egrave;der &agrave; cette fonction")."</BODY></HTML>");

function mktable($title, $content) {
        echo "<h3>$title</h3>\n";
        echo $content;
}
if (!isset($_POST['login']))
{
  list ($idpers,$login)= isauth();
  if ($idpers == "0") header("Location:$urlauth");
  $uids = search_uids ("(cn=Profs)", "half");
  $people = search_people_groups ($uids,"(sn=*)","cat");
  echo "<div align=center>";
  if (count($people)) {
    echo "Il y a ".count($people)." membre";
    if ( count($people) >1 ) echo "s";
    echo " dans le groupe Profs</br>\n" ;
    echo "<form method=post>";
    echo "<select name=login size=1>";
    for ($loop=0; $loop < count($people); $loop++) {
        echo "<option value=".$people[$loop]["uid"].">".$people[$loop]["fullname"]." ".$people[$loop]["sexe"]."</option>";
    }
        echo "</select></br><input type='submit' value='Valider' /> ";
     echo "</form></div>";
     }
     else {
        echo " <strong>Pas de membres</strong> dans le groupe Profs.<br>";
     }
}

else {
	echo "<div align=center>";
        $login=$_POST['login'];
        exec("/usr/share/lcs/sbin/lcs-openvpn-generclient.sh $login");
        echo "g&eacute;n&eacute;ration du certificat pour ".$login termin&eacute;;
        echo "l'utilisateur a re&ccedil;u la proc&eacutedure par e-mail pour utiliser le VPN";
	echo "</div>";
}

include ("/var/www/lcs/includes/pieds_de_page.inc.php");
?>
