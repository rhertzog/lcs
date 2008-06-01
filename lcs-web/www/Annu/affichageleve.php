<?
/* =============================================
   Projet LCS : Linux Communication Server
   Consultation de l'annuaire LDAP
   Annu/affichageleve.php
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
for ($loop=0; $loop < count ($classe_gr) ; $loop++) {    
     $filter[$loop]=$classe_gr[$loop];
}
$index=$loop;
for ($loop=0; $loop < count ($equipe_gr) ; $loop++) {
    $filter[$index+$loop]=$equipe_gr[$loop];
}
$index=$index+$loop;
for ($loop=0; $loop < count ($autres_gr) ; $loop++) {
    $filter[$index+$loop]=$autres_gr[$loop];
}
    

// Message d'erreurs de saisie
if ( $cn=="" || $description=="" ) {
    echo "<div class=error_msg>".gettext("Vous devez saisir un nom de groupe et une description !")."</div><br>\n";
    exit();
}
elseif (!verifDescription($description)) {
    echo "<div class=error_msg>".gettext("Le champ description comporte des caractères interdits !")."</div><br>\n";
    exit();
}
elseif (!verifIntituleGrp($intitule)) {
    echo "<div class=error_msg>".gettext("Le champ intitulé ne doit pas commencer ou se terminer par l'expresssion : Classe, Equipe ou Matiere !")."</div><br>\n";
    exit();
}
elseif ( $filter=="") {
    echo "<div class=error_msg>".gettext("Vous devez sélectionner au moins un groupe!")."</div><br>\n"; 
    exit();
}
	
// Verification de l'existance du groupe    
$groups=search_groups("(cn=$cn)");
if (count($groups)) {
echo "<div class='error_msg'>".gettext("Attention le groupe <font color='#0080ff'>$cn</font> est déja présent dans la base, veuillez choisir un autre nom !")."</div><BR>\n";
exit();
}
else {
	
// Ajout du groupe

$intitule = enleveaccents($intitule);
    
exec ("$scriptsbinpath/groupAdd.pl \"1\" $cn \"$description\"",$AllOutPut,$ReturnValue);
if ($ReturnValue == "0") {
echo "<div class=error_msg>".gettext("Le groupe <font color='#0080ff'>$cn</font> a été ajouté avec succès.")."</div><br>\n";
	}
else {echo "<div class=error_msg>".gettext("Echec, le groupe <font color='#0080ff'>$cn</font> n'a pas été créé !")."\n";
if ($ReturnValue) echo "(type d'erreur : $ReturnValue),&nbsp;";
echo "&nbsp;".gettext("Veuillez contacter</div> <A HREF='mailto:$MelAdminLCS?subject=PB creation groupe'>l'administrateur du système</A>")."<BR>\n";
exit();}
    }
    
echo "<B>Sélectionner les personnes a mettre dans le groupe ci-dessus :</B><BR>";
									    
echo "<form action=\"constitutiongroupe.php\" method=\"post\">";

echo "<table border=\"0\" cellspacing=\"10\">";    
echo "<TR>";
for ($loop=0; $loop < count($filter); $loop++) {
    echo "<TD>$filter[$loop]</TD>";
}
echo "</TR>";    
echo "<TR>";
for ($filt=0; $filt < count($filter); $filt++) {
      $uids=search_uids("(cn=".$filter[$filt].")","full");
      $people=search_people_groups($uids,"(sn=*)","cat");
      echo "<td valign=\"top\">";
      echo "<select name=\"eleves[]\" size=\"10\"  multiple=multiple>";
      for ($loop=0; $loop < count($people); $loop++) {
      echo "<option value=".$people[$loop]["uid"].">".$people[$loop]["fullname"];
       }
echo"</TD></select>";
}
echo "</TR>";    
echo "</table>";    
echo "<BR><BR>";    
echo "<input type=\"hidden\" name=\"cn\" value=\"$cn\">
      <input type=\"submit\" value=\"valider\">
      <input type=\"reset\" value=\"Réinitialiser la sélection\">";
	
echo"</form>";

}//fin is_admin
	
else echo "Vous n'avez pas les droits nécessaires pour ouvrir cette page...";
include ("../lcs/includes/pieds_de_page.inc.php");
?>