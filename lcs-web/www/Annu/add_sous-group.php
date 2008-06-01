<?
/* =============================================
   Projet LCS : Linux Communication Server
   Consultation de l'annuaire LDAP
   Annu/add_sous-group.php
   Equipe Tice académie de Caen
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
echo "<TD><B>Nouveau groupe :</B></TD>";
echo "<TR><TD>Intitulé :</TD><td><input type=\"text\" name=\"cn\" value=\"$cn\" size=\"20\"></TD></TR><BR>";
echo "<TR><TD>Description :</TD><td><input type=\"text\" name=\"description\" value=\"$description\" size=\"40\"></TD></TR><BR><BR>";
echo "</table><BR>";
echo "<B>Sélectionner le(s) groupe(s) dans le(s)quel(s) se situent les personnes a mettre dans le groupe ci-dessus : </B><BR><BR>";

// Etablissement des listes des groupes disponibles
$list_groups=search_groups("(&(cn=*) $filter )");
// Etablissement des sous listes de groupes :
$j =0; $k =0; $m = 0;
for ($loop=0; $loop < count ($list_groups) ; $loop++) {
    // Classe
    if ( ereg ("Classe_", $list_groups[$loop]["cn"]) ) {
	$classe[$j]["cn"] = $list_groups[$loop]["cn"];
	$classe[$j]["description"] = $list_groups[$loop]["description"];
	$j++;}
    // Equipe
    elseif ( ereg ("Equipe_", $list_groups[$loop]["cn"]) ) {
	$equipe[$k]["cn"] = $list_groups[$loop]["cn"];
	$equipe[$k]["description"] = $list_groups[$loop]["description"];
	$k++;}
    // Autres
    elseif (!ereg ("^Eleves", $list_groups[$loop]["cn"]) &&
            !ereg ("^overfill", $list_groups[$loop]["cn"]) &&
            !ereg ("^Cours_", $list_groups[$loop]["cn"]) &&
            !ereg ("^Matiere_", $list_groups[$loop]["cn"]) &&
            !ereg ("^lcs-users", $list_groups[$loop]["cn"]) &&
            !ereg ("^machines", $list_groups[$loop]["cn"]) &&
            !ereg ("^Profs", $list_groups[$loop]["cn"]) ) {
            $autres[$m]["cn"] = $list_groups[$loop]["cn"];
            $autres[$m]["description"] = $list_groups[$loop]["description"];
            $m++;}
  }
// Affichage des boites de sélection des nouveaux groupes secondaires
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
echo "<select name= \"classe_gr[]\" value=\"classe_gr\" size=\"10\" multiple=\"multiple\">\n";
    for ($loop=0; $loop < count ($classe) ; $loop++) {
	echo "<option value=".$classe[$loop]["cn"].">".$classe[$loop]["cn"];
    }
    echo "</select>";
    echo "</td>";
    echo "<td>\n";
    echo "<select name= \"equipe_gr[]\" value=\"$equipe_gr\" size=\"10\" multiple=\"multiple\">\n";
    for ($loop=0; $loop < count ($equipe) ; $loop++) {
	echo "<option value=".$equipe[$loop]["cn"].">".$equipe[$loop]["cn"];
    }
    echo "</select></td>\n";
    echo "<td valign=\"top\">
    <select name=\"autres_gr[]\" value=\"$autres_gr\" size=\"5\" multiple=\"multiple\">";
    for ($loop=0; $loop < count ($autres) ; $loop++) {
	echo "<option value=".$autres[$loop]["cn"].">".$autres[$loop]["cn"];
    }
    echo "</select></td></tr></table>";
    echo " <input type=\"submit\" value=\"valider\">
	   <input type=\"reset\" value=\"Réinitialiser la sélection\">";
    
    echo "</form></small>";
    
    
	
}//fin is_admin	
else echo "Vous n'avez pas les droits nécessaires pour ouvrir cette page...";
include ("../lcs/includes/pieds_de_page.inc.php");
?>
