<?php
# /var/www/Admin/listesdiff.php derniere version du : 23/01/09
include ("../lcs/includes/headerauth.inc.php");
include ("../Annu/includes/ldap.inc.php");
include ("../Annu/includes/ihm.inc.php");

$valideffliste=$_POST['valideffliste'] ;
$valideaddliste=$_POST['valideaddliste'] ;


list ($idpers, $login)= isauth();
if ($idpers == "0") header("Location:$urlauth");
$html = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">\n 
	  <head>\n
	  <title>...::: S&#233;lection briques fonctionnelles LCS  :::...</title>\n
	  <link  href='../Annu/style.css' rel='StyleSheet' type='text/css'>\n
	  </head>\n
	  <body>\n";
$html .= "<div id='container'><h2>Services intranet LCS</h2>\n";
echo $html;
if (is_admin("system_is_admin",$login)=="Y") {
    #liste de diffusion ldap
    exec ("/bin/grep \"#<listediffusionldap>\" /etc/postfix/mailing_list.cf", $AllOutPut, $ReturnValueShareName);
    $listediff = 0;
    if ( count($AllOutPut) >= 1) $listediff = 1;
    echo "<form name =\"web\" action=\"listesdiff.php\" method=\"post\">
	   Liste de diffusion ldap : &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
    if (($listediff == 1 && !isset($valideffliste)) || isset($valideaddliste)) {
	echo "<input type=\"hidden\" name=\"valideffliste\" value=\"true\">
	       <input type=\"submit\" value=\"D&#233;sactivation\">
	       </form>";
        echo "<u onmouseover=\"return escape('<strong>D&#233;sactivation des listes de  diffusion <em>LDAP</em></strong> : D&#233;sactive les listes de diffusion <em>LDAP</em>, les utilisateurs <em>LCS</em>, ne pourront plus poster des messages &#224;des groupes.')\"><img src='../images/help-info.gif'></u>\n";
    }
    if (($listediff == 0 && !isset($valideaddliste)) || isset($valideffliste)) {
	echo "<input type=\"hidden\" name=\"valideaddliste\" value=\"true\">
	       <input type=\"submit\" value=\"Activation\">
	       </form>";
        echo "<u onmouseover=\"return escape('<strong>Mise en place des listes de diffusion <em>LDAP</em></strong> : Les listes de diffusion <strong><em>LDAP</em></strong> permettent &#224;un utilisateur de poster un mail &#224;l\'ensemble des membres d\'un groupe. <br /><u>Exemple</u> : Il existe dans l\'annuaire un groupe <strong>Classe_2A</strong>, il sera posssible adresser un mail &#224;l\'ensemble des membres de ce groupe en postant un mail &#224;l\'adresse suivante : <strong><em>Classe_2A@lcs</em></strong> .')\"><img src='../images/help-info.gif' alt='infos'></u>\n";
    }
    if (isset($valideffliste)) {
        exec("/usr/bin/sudo /usr/share/lcs/scripts/eff_liste_diff.sh ");
    }
    if (isset($valideaddliste)){
	$main  = "#<listediffusionldap>\n";
	$main .= "server_host = $ldap_server\n";
	$main .= "search_base = $ldap_base_dn\n";
	$main .= "query_filter = (cn=%s)\n";
	$main .= "result_attribute = memberUid,mail\n";
        $main .= "bind = no\n";
	$main .= "special_result_attribute = member\n";
        $main .= "alias_maps = ldap:ldapsource\n";
        $main .= "#</listediffusionldap>";
         exec("/usr/bin/sudo /usr/share/lcs/scripts/add_liste_diff.sh \"$main\" ");
    }
    #fin liste de diffusion ldap
    
}// fin is_admin
else echo "Vous n'avez pas les droits n&#233;cessaires pour ordonner cette action...";
echo "</div><!-- Fin container-->\n";
include ("../lcs/includes/pieds_de_page.inc.php");
?>