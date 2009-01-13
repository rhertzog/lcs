<?php
//Projet LCS page de parmetre des modules.

include ("/var/www/lcs/includes/headerauth.inc.php");
include ("/var/www/Annu/includes/ldap.inc.php");
$msgIntro = "<H1>Gestion LCS OpenVPN</H1>\n";
list ($idpers, $login)= isauth();

if (ldap_get_right("lcs_is_admin",$login)!="Y")
  die (gettext("Vous n'avez pas les droits suffisants pour acc�der � cette fonction")."</BODY></HTML>");

function mktable($title, $content) {
        echo "<h3>$title</h3>\n";
        echo $content;
}

function aff_param_form($cat)
{
        $texte_form="<table border=\"0\">";
        $result=mysql_query("SELECT * from params WHERE cat=$cat ORDER BY `id`");
        if ($result) {
                while ($r=mysql_fetch_array($result)) {
                        $texte_form .= "<tr><td colspan=\"2\">".$r["descr"]." (<em><font color=\"red\">".$r["name"]."</font></em>)</td>";
                        $texte_form .= "<td><input TYPE=\"text\" size=\"50\" value=\"".$r["value"]."\" NAME=\"form_".$r["name"]."\"</td></tr>\n";
                }
        }
        $texte_form .= "</TABLE>";
        return $texte_form;
}


if (!isset($cat)) $cat=100;

if ((!isset($submit)) and (!isset($queri))) {
// Affichage du form de mise � jour des param�tres
        print "<form method=\"post\">\n";
        if ( $cat==0 || $cat==100 )
                mktable("Paramétres OpenVPN",aff_param_form(100));
        if ( $cat==0 || $cat==200 )
                mktable("Paramétres xxxx",aff_param_form(200));
        print "<be><dev align=\"center\"><INPUT TYPE=\"submit\" value=\"".gettext("Valider")."\"></div>";
        print "<input type=\"hidden\" value=\"$cat\" name=\"submit\">";
        print "</form>\n";
}
if (isset($submit)) {
// Traitement du Form

// Détection des paramètres modifiés et fabrication de la requete de mise a jour
        if ( ( file_exists ("/tmp/params_lcs") ) ||  !( $fp = fopen("/tmp/params_lcs", "w") ) )
                die (gettext("Création du fichier de passage des parametres impossible. Recommencez plus tard et assurez-vous qu'aucun fichier params_lcs n'est présent dans ")."/tmp.");
        $query="SELECT * from params";
        if ($submit != 0) $query .= " WHERE cat=$submit";
        $result=mysql_query($query);
        if ($result) {
                $i=0;
                while ($r=mysql_fetch_array($result)) {
                        $formname="form_".$r["name"];
                        if ($$formname!=$r["value"]) {
                        // Mise à jour de la base de données
                                $queri="UPDATE params SET value=\"".$$formname."\" WHERE name=\"".$r["name"]."\"";
                                $result1=mysql_query($queri);
                                if ($result1)
                                        print gettext("Modification du paramètre ").
                                        "<em><font color=\"red\">".$r["name"].
                                        "</font></em> ". gettext("de ")."<strong>".$r["value"].
                                        "</strong>".gettext(" en ")."<strong>".$$formname."</strong>"."<br>\n";
                                else
                                        print gettext("oops: la requete ") . "<strong>$queri</strong>" . gettext(" a provoqué une erreur");
                                // Récupération des variables qui ont changées et qui nécessitent une modification des fichiers de conf
                        }
                }

                if ($i == 0) print gettext("Aucun paramètre n'a été modifié\n");

                mysql_free_result($result);
        } else print gettext ("oops: Erreur inattendue de lecture des anciens paramétres\n");
        fclose($fp);
        // Effacement du fichier si rien n'y a été inscrit
        if (filesize("/tmp/params_lcs")!=0) {
                # execution du script de modification
                exec ("/usr/bin/sudo /usr/share/lcs/scripts/edit_params.sh");
                print gettext("les fichiers de configuration ont été modifiés afin de prendre en compte les nouveaux paramétres.");
        } else unlink ("/tmp/params_lcs");
}


include ("/var/www/lcs/includes/pieds_de_page.inc.php");

?>
