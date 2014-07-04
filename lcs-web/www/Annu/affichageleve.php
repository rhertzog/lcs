<?php
/* =============================================
   Projet LCS-SE3
   Consultation/ Gestion de l'annuaire LDAP
   Equipe Tice academie de Caen
   Distribue selon les termes de la licence GPL
   Derniere modification : 23/05/2014
   ============================================= */
include "includes/check-token.php";
if (!check_acces()) exit;

$login=$_SESSION['login'];
include "../lcs/includes/headerauth.inc.php";
include "includes/ldap.inc.php";
include "includes/ihm.inc.php";

$intitule="";
$filter=array();
if ( count($_POST)>0 ) {
  	//configuration objet
 	include ("../lcs/includes/htmlpurifier/library/HTMLPurifier.auto.php");
 	$config = HTMLPurifier_Config::createDefault();
 	$purifier = new HTMLPurifier($config);
    	//purification des variables
  	if ( isset($_POST['cn']))  $cn=$purifier->purify($_POST['cn']);
  	if ( isset($_POST['description']))  $description=$purifier->purify($_POST['description']);
  	if ( isset($_POST['intitule']))  $intitule=$purifier->purify($_POST['intitule']);
  	if ( isset($_POST['action'])) $action=$purifier->purify($_POST['action']);
  	if ( isset($_POST['classe_gr'])) $classe_gr=$purifier->purifyArray($_POST['classe_gr']);
  	if ( isset($_POST['equipe_gr'])) $equipe_gr=$purifier->purifyArray($_POST['equipe_gr']);
  	if ( isset($_POST['autres_gr'])) $autres_gr=$purifier->purifyArray($_POST['autres_gr']);
}

header_html();
aff_trailer ("8");

if (is_admin("Annu_is_admin",$login)=="Y") {
if (isset($classe_gr)) for ($loop=0; $loop < count ($classe_gr) ; $loop++) {
     $filter[$loop]=$classe_gr[$loop];
}
$index=$loop;
if (isset($equipe_gr))for ($loop=0; $loop < count ($equipe_gr) ; $loop++) {
    $filter[$index+$loop]=$equipe_gr[$loop];
}
$index=$index+$loop;
if (isset($autres_gr))for ($loop=0; $loop < count ($autres_gr) ; $loop++) {
    $filter[$index+$loop]=$autres_gr[$loop];
}


// Message d'erreurs de saisie
if ( $cn=="" || $description=="" ) {
    echo "<div class=error_msg>".gettext("Vous devez saisir un nom de groupe et une description !")."</div><br>\n";
    exit();
}
elseif (!verifDescription($description)) {
    echo "<div class=error_msg>".gettext("Le champ description comporte des caract&#233;res interdits !")."</div><br>\n";
    exit();
}
elseif (!verifIntituleGrp($intitule)) {
    echo "<div class=error_msg>".gettext("Le champ intitul&#233; ne doit pas commencer ou se terminer par l'expresssion : Classe, Equipe ou Matiere !")."</div><br>\n";
    exit();
}
elseif ( $filter=="") {
    echo "<div class=error_msg>".gettext("Vous devez s&#233;lectionner au moins un groupe!")."</div><br>\n";
    exit();
}

// Verification de l'existance du groupe
$groups=search_groups("(cn=$cn)");
if (count($groups)) {
echo "<div class='error_msg'>".gettext("Attention le groupe <font color='#0080ff'>$cn</font> est d&#233;ja pr&#233;sent dans la base, veuillez choisir un autre nom !")."</div><BR>\n";
exit();
}
else {

// Ajout du groupe

$intitule = enleveaccents($intitule);

exec ("$scriptsbinpath/groupAdd.pl '1' ". escapeshellarg($cn) ." ". escapeshellarg($description),$AllOutPut,$ReturnValue);
if ($ReturnValue == "0") {
echo "<div class=error_msg>".gettext("Le groupe <font color='#0080ff'>$cn</font> a &#233;t&#233; ajout&#233; avec succ&#232;s.")."</div><br>\n";
	}
else {echo "<div class=error_msg>".gettext("Echec, le groupe <font color='#0080ff'>$cn</font> n'a pas &#233;t&#233; cr&#233;&#233; !")."\n";
if ($ReturnValue) echo "(type d'erreur : $ReturnValue),&nbsp;";
echo "&nbsp;".gettext("Veuillez contacter</div> <A HREF='mailto:$MelAdminLCS?subject=PB creation groupe'>l'administrateur du syst&#232;me</A>")."<BR>\n";
exit();}
    }

echo "<B>S&#233;lectionner les personnes a mettre dans le groupe ci-dessus :</B><BR>";

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
echo"</select></TD>";
}
echo "</TR>";
echo "</table>";
echo "<BR><BR>";
echo "<input type=\"hidden\" name=\"cn\" value=\"$cn\">";
echo '<input name="jeton" type="hidden"  value="'.md5($_SESSION['token'].htmlentities("/Annu/constitutiongroupe.php")).'" />';
echo "<input type=\"submit\" value=\"valider\">
            <input type=\"reset\" value=\"R&#233;initialiser la s&#233;lection\">";
echo"</form>";

}//fin is_admin

else echo "Vous n'avez pas les droits n&#233;&#232;cessaires pour ouvrir cette page...";
include ("../lcs/includes/pieds_de_page.inc.php");
?>
