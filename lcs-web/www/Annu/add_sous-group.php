<?
/* =============================================
   Projet LCS : Linux Communication Server
   Consultation de l'annuaire LDAP
   Annu/add_sous-group.php
   Equipe Tice academie de Caen
   V 1.4 maj : 15/03/2007
   ============================================= */
include "../lcs/includes/headerauth.inc.php";
include "includes/ldap.inc.php";
include "includes/ihm.inc.php";

list ($idpers,$login)= isauth();
if ($idpers == "0") header("Location:$urlauth");

header_html();
aff_trailer ("8");

if (is_admin("Annu_is_admin",$login)=="Y") {
echo "<form action=\"affichageleve.php\" method=\"post\">";
echo "<table border=\"0\">";
echo "<TR><TD><B>Nouveau groupe :</B></TD></TR>";
echo "<TR><TD>Intitul&#233; :</TD><td><input type=\"text\" name=\"cn\" value=\"$cn\" size=\"20\"></TD></TR>";
echo "<TR><TD>Description :</TD><td><input type=\"text\" name=\"description\" value=\"$description\" size=\"40\"></TD></TR>";
echo "</table><BR>";
echo "<B>S&#233;lectionner le(s) groupe(s) dans le(s)quel(s) se situent les personnes a mettre dans le groupe ci-dessus : </B><BR><BR>";

// Etablissement des listes des groupes disponibles
$list_groups=search_groups("(&(cn=*) $filter )");
// Etablissement des sous listes de groupes :
$j =0; $k =0; $m = 0;
for ($loop=0; $loop < count ($list_groups) ; $loop++) {
    // Classe
    if ( mb_ereg ("Classe_", $list_groups[$loop]["cn"]) ) {
	$classe[$j]["cn"] = $list_groups[$loop]["cn"];
	$classe[$j]["description"] = $list_groups[$loop]["description"];
	$j++;}
    // Equipe
    elseif ( mb_ereg ("Equipe_", $list_groups[$loop]["cn"]) ) {
	$equipe[$k]["cn"] = $list_groups[$loop]["cn"];
	$equipe[$k]["description"] = $list_groups[$loop]["description"];
	$k++;}
    // Autres
    elseif (!mb_ereg ("^Eleves", $list_groups[$loop]["cn"]) &&
            !mb_ereg ("^overfill", $list_groups[$loop]["cn"]) &&
            !mb_ereg ("^Cours_", $list_groups[$loop]["cn"]) &&
            !mb_ereg ("^Matiere_", $list_groups[$loop]["cn"]) &&
            !mb_ereg ("^lcs-users", $list_groups[$loop]["cn"]) &&
            !mb_ereg ("^machines", $list_groups[$loop]["cn"]) &&
            !mb_ereg ("^Profs", $list_groups[$loop]["cn"]) ) {
            $autres[$m]["cn"] = $list_groups[$loop]["cn"];
            $autres[$m]["description"] = $list_groups[$loop]["description"];
            $m++;}
  }
// Affichage des boites de selection des nouveaux groupes secondaires
?>
<table border="0" cellspacing="10">
<tr>
<td>Classes</td>
<td>Equipes</td>
<td>Autres</td>
</tr>
<tr>
<td valign="top">
<?
echo "<select name= \"classe_gr[]\"  size=\"10\" multiple=\"multiple\">\n";
    for ($loop=0; $loop < count ($classe) ; $loop++) {
	echo "<option value=".$classe[$loop]["cn"].">".$classe[$loop]["cn"];
    }
    echo "</select>";
    echo "</td>";
    echo "<td>\n";
    echo "<select name= \"equipe_gr[]\"  size=\"10\" multiple=\"multiple\">\n";
    for ($loop=0; $loop < count ($equipe) ; $loop++) {
	echo "<option value=".$equipe[$loop]["cn"].">".$equipe[$loop]["cn"];
    }
    echo "</select></td>\n";
    echo "<td valign=\"top\">
    <select name=\"autres_gr[]\"  size=\"5\" multiple=\"multiple\">";
    for ($loop=0; $loop < count ($autres) ; $loop++) {
	echo "<option value='".$autres[$loop]["cn"]."'>".$autres[$loop]["cn"];
    }
    echo "</select></td></tr></table>";
    echo " <input type=\"submit\" value=\"valider\">
	   <input type=\"reset\" value=\"R&#233;initialiser la s&#233;lection\">";
    
    echo "</form>";
    
    
	
}//fin is_admin	
else echo "Vous n'avez pas les droits n&#233;cessaires pour ouvrir cette page...";
include ("../lcs/includes/pieds_de_page.inc.php");
?>
