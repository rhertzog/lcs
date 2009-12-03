<?php
/*
 * $Id: index.php 3317 2009-07-30 19:41:37Z jjacquard $
 *
 * Copyright 2001, 2007 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Gabriel Fischer
 *
 * This file is part of GEPI.
 *
 * GEPI is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * GEPI is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with GEPI; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

// Initialisations files
$niveau_arbo = "public";
require_once("../lib/initialisations.inc.php");

require_once("../lib/mincals.inc");
require_once("../lib/transform_functions.php");
require_once("lib/functions.inc");
// On v�rifie si l'acc�s est restreint ou non
require_once("lib/auth.php");


unset($id_classe);
$id_classe = isset($_POST["id_classe"]) ? $_POST["id_classe"] : (isset($_GET["id_classe"]) ? $_GET["id_classe"] : NULL);
unset($day);
$day = isset($_POST["day"]) ? $_POST["day"] : (isset($_GET["day"]) ? $_GET["day"] : date("d"));
unset($month);
$month = isset($_POST["month"]) ? $_POST["month"] : (isset($_GET["month"]) ? $_GET["month"] : date("m"));
unset($year);
$year = isset($_POST["year"]) ? $_POST["year"] : (isset($_GET["year"]) ? $_GET["year"] : date("Y"));
unset($id_matiere);
$id_matiere = isset($_POST["id_matiere"]) ? $_POST["id_matiere"] : (isset($_GET["id_matiere"]) ? $_GET["id_matiere"] : -1);
unset($id_groupe);
$id_groupe = isset($_POST["id_groupe"]) ? $_POST["id_groupe"] :(isset($_GET["id_groupe"]) ? $_GET["id_groupe"] :NULL);
if (is_numeric($id_groupe)) {
    $current_group = get_group($id_groupe);
    if ($id_classe == NULL) $id_classe = $current_group["classes"]["list"][0];
} else {
    $current_group = false;
}

// Pour le multisite, on doit r�cup�rer le RNE de l'�tablissement
$rne = isset($_GET['rne']) ? $_GET['rne'] : (isset($_POST['rne']) ? $_POST['rne'] : 'aucun');
$aff_input_rne = NULL;
if ($rne != 'aucun') {
	$aff_input_rne = '<input type="hidden" name="rne" value="' . $rne . '" />' . "\n";
}

// Nom complet de la classe
$appel_classe = mysql_query("SELECT classe FROM classes WHERE id='$id_classe'");
$classe_nom = @mysql_result($appel_classe, 0, "classe");
// Nom complet de la mati�re
$matiere_nom = $current_group["matiere"]["nom_complet"];
$matiere_nom_court = $current_group["matiere"]["matiere"];
// V�rification
settype($month,"integer");
settype($day,"integer");
settype($year,"integer");
$minyear = strftime("%Y", getSettingValue("begin_bookings"));
$maxyear = strftime("%Y", getSettingValue("end_bookings"));
if ($day < 1) $day = 1;
if ($day > 31) $day = 31;
if ($month < 1) $month = 1;
if ($month > 12) $month = 12;
if ($year < $minyear) $year = $minyear;
if ($year > $maxyear) $year = $maxyear;
# Make the date valid if day is more then number of days in month
while (!checkdate($month, $day, $year)) $day--;
$today=mktime(0,0,0,$month,$day,$year);
//**************** EN-TETE *****************
$titre_page = "Cahier de textes";
$page_accueil = "index.php?id_classe=-1";
require_once("lib/header.inc");
//**************** FIN EN-TETE *************
//On v�rifie si le module est activ�
if (getSettingValue("active_cahiers_texte")!='y') {
    echo("<center><p class='grand'>Le cahier de textes n'est pas accessible pour le moment.</p></center>");
    require ("../lib/footer.inc.php");
    die();
}
if (getSettingValue("cahier_texte_acces_public")!='yes') {
    echo("<center><p class='grand'>Le cahier de textes n'est pas en acc�s public.</p></center>");
    require ("../lib/footer.inc.php");
    die();
}
// On v�rifie que la date demand�e est bien comprise entre la date de d�but des cahiers de texte et la date de fin des cahiers de texte :
if ($today < getSettingValue("begin_bookings")) {
   $today = getSettingValue("begin_bookings");
} else if ($today > getSettingValue("end_bookings")) {
   $today = getSettingValue("end_bookings");
}
echo "<script type=\"text/javascript\" SRC=\"../lib/clock_fr.js\"></SCRIPT>";
//-----------------------------------------------------------------------------------
echo "<table width=\"98%\" cellspacing=0 align=\"center\"><tr>";
echo "<td valign='top'>";
echo "<p>Nous sommes le :&nbsp;<br />\n";
echo "<script type=\"text/javascript\">\n";
echo "<!--\n";
echo "new LiveClock();\n";
echo "//-->";
echo "</SCRIPT></p>\n";
echo make_classes_select_html('index.php', $id_classe, $year, $month, $day);
if ($id_classe!=-1) echo make_matiere_select_html('index.php', $id_classe, $id_groupe, $year, $month, $day);
$test_login = mysql_result(mysql_query("SELECT count(login) FROM utilisateurs WHERE ((statut = 'responsable' OR statut = 'eleve') AND etat = 'actif')"), 0);
if ($test_login > 0) {
	echo "<p>Si vous disposez d'un identifiant et d'un mot de passe, <a href='../login.php'>connectez-vous !</a></p>";
}
echo "</td>\n";
echo "<td style=\"text-align:center;\">\n";
echo "<p><span class='grand'>Cahier de textes";
if ($id_classe != -1) echo "<br />$classe_nom";
if ($id_matiere != -1) echo "- $matiere_nom ($matiere_nom_court)";
echo "</span>\n";
// Test si le cahier de texte est partag�
if ($current_group) {
  echo "<br />\n<b>(";
  $i=0;
  foreach ($current_group["profs"]["users"] as $prof) {
    if ($i != 0) echo ", ";
    echo substr($prof["prenom"],0,1) . ". " . $prof["nom"];
    $i++;
  }
  echo ")</b>\n";
}
echo "</p></td>\n";
echo "<td align=\"right\">\n";
echo "<form action=\"./index.php\" method=\"post\" style=\"width: 100%;\">\n";
genDateSelector("", $day, $month, $year,'');
echo "<input type=hidden name=id_groupe value=$id_groupe />\n";
echo "<input type=hidden name=id_classe value=$id_classe />\n";
echo $aff_input_rne . "\n";
echo "<input type=\"submit\" value=\"OK\"/></form>\n";
//Affiche le calendrier
minicals($year, $month, $day, $id_groupe, 'index.php?');
echo "</td>";
echo "</tr></table>\n<hr />\n";
$test_cahier_texte = mysql_query("SELECT contenu  FROM ct_entry WHERE (id_groupe='$id_groupe')");
$nb_test = mysql_num_rows($test_cahier_texte);
$delai = getSettingValue("delai_devoirs");
//Affichage des devoirs globaux s'il n'y a pas de notices dans ct_entry � afficher
if (($nb_test == 0) and ($id_classe != -1) and ($delai != 0)) {
    if ($delai == "") die("Erreur : D�lai de visualisation du travail personnel non d�fini. Contactez l'administrateur de GEPI de votre �tablissement.");
    $nb_dev = 0;
    for ($i = 0; $i <= $delai; $i++) {
        $aujourhui = $aujourdhui = mktime(0,0,0,date("m"),date("d"),date("Y"));
        $jour = mktime(0, 0, 0, date('m',$aujourhui), (date('d',$aujourhui) + $i), date('Y',$aujourhui) );
        $appel_devoirs_cahier_texte = mysql_query("SELECT ct.contenu, g.id, g.description, ct.date_ct, ct.id_ct " .
            "FROM ct_devoirs_entry ct, groupes g, j_groupes_classes jc WHERE (" .
            "ct.id_groupe = jc.id_groupe and " .
            "g.id = jc.id_groupe and " .
            "jc.id_classe = '" . $id_classe . "' and " .
            "ct.contenu != '' and " .
            "ct.date_ct = '$jour')");
        $nb_devoirs_cahier_texte = mysql_num_rows($appel_devoirs_cahier_texte);
        $ind = 0;
        if ($nb_devoirs_cahier_texte != 0) {
            $nb_dev++;
            if ($nb_dev == '1') {
                echo "<br /><center>Date s�lectionn�e : ".strftime("%A %d %B %Y", $today)."</center>\n";
                echo "<br /><center><b><font style='font-variant: small-caps;'>Travaux personnels des $delai jours suivant le ".strftime("%d %B %Y", $today)." pour la classe de $classe_nom</font></b></center><br />\n";
                echo "<table style=\"border-style:solid; border-width:0px; border-color: ".$couleur_bord_tableau_notice.";\" width = '100%' cellpadding='5'><tr><td>\n";
            }
            echo "<div style=\"border-style:solid; border-width:1px; border-color: ".$couleur_bord_tableau_notice."; background-color: ".$color_fond_notices["f"].";\"><font color='".$color_police_travaux."' style='font-variant: small-caps;'><b>Travaux personnels pour le ".strftime("%a %d %b", $jour)."</b></font>\n";
            // Affichage des devoirs dans chaque mati�re
            while ($ind < $nb_devoirs_cahier_texte) {
                $content = mysql_result($appel_devoirs_cahier_texte, $ind, 'contenu');
                // Mise en forme du texte
                include "../lib/transform.php";
                $date_devoirs = mysql_result($appel_devoirs_cahier_texte, $ind, 'date_ct');
                $id_devoirs =  mysql_result($appel_devoirs_cahier_texte, $ind, 'id_ct');
                $id_groupe_devoirs = mysql_result($appel_devoirs_cahier_texte, $ind, 'id');
                $matiere_devoirs = mysql_result($appel_devoirs_cahier_texte,$ind, 'description');
                $test_prof = "SELECT nom, prenom FROM j_groupes_professeurs j, utilisateurs u WHERE (j.id_groupe='".$id_groupe_devoirs."' and u.login=j.login) ORDER BY nom, prenom";
                $res_prof = sql_query($test_prof);
                $chaine = "";
                for ($k=0;$prof=sql_row($res_prof,$k);$k++) {
                  if ($k != 0) $chaine .= ", ";
                  $chaine .= htmlspecialchars($prof[0])." ".substr(htmlspecialchars($prof[1]),0,1).".";
                }
                $html = "<div style=\"border-style:solid; border-width:1px; border-color: ".$couleur_bord_tableau_notice."; background-color: ".$couleur_cellule["f"]."; padding: 2px; margin: 2px;\"><font color='".$color_police_matieres."' style='font-variant: small-caps;'><small><b><u>".$matiere_devoirs." (".$chaine."):</u></b></small></font>".$html;
                // fichier joint
                $html .= affiche_docs_joints($id_devoirs,"t");
                $html .="</div>";
                if ($nb_devoirs_cahier_texte != 0)
                    echo $html;
                $ind++;
            }
        echo "</div><br />";
        }
    }
    if ($nb_dev != 0) echo "</td></tr></table><br />";
	require("../lib/footer.inc.php");
    die();
    //AFfichage page de garde
} elseif ($nb_test == 0) {
    echo "<center><h3 class='gepi'>".getSettingValue("gepiSchoolName"). " - ann�e scolaire " . getSettingValue("gepiYear")."</H3>\n";
    echo "<h3 class='gepi'><font color='red'>Choisissez une classe et une mati�re.</font></h3>\n";
    $nom_fic_logo = getSettingValue("logo_etab");
    $nom_fic_logo_c = "../images/".$nom_fic_logo;
    if (($nom_fic_logo != '') and (file_exists($nom_fic_logo_c))) {
        echo "<img src=\"".$nom_fic_logo_c."\" style=\"border: 0;\" ALT=\"\" /><br />\n";
    }
    echo "<br /><br /><p>Ce cahier de textes est g�r� sous <a href='http://gepi.mutualibre.org/'>GEPI</a>.\n<br />GEPI est un outil de gestion, de suivi, et de visualisation graphique des r�sultats scolaires (�coles, coll�ges, lyc�es).\n<br />GEPI est un logiciel libre et gratuit diffus� sous la licence GPL.</p><br />\n";
    echo "<br /><a href='http://www.php.net'><img src=\"../php4.gif\" alt=\"Powered by php4\" width=\"88\" height=\"31\" style=\"border: 0;\" />\n</a><a href='http://www.mysql.org'> <img src=\"../mysqllogo.gif\" alt=\"Powered by MySQL\" width=\"88\" height=\"31\" style=\"border: 0;\" /></a>\n";
    //echo "</center></body></html>";
    echo "</center>";
	require("../lib/footer.inc.php");
    die();
}

// Affichage des compte-rendus et des travaux � faire.
echo "<table width=\"98%\" border = 0 align=\"center\">\n";

// Premi�re colonne : affichage du 'travail � faire' � venir
echo "<tr><td width = \"30%\" valign=\"top\">\n";
echo "<a href='see_all.php?id_classe=$id_classe&amp;id_groupe=$id_groupe'>Voir l'ensemble du cahier de textes</a><br /><br />\n";
// Affichage des devoirs
if ($delai == "") die("Erreur : D�lai de visualisation des devois non d�fini. Contactez l'administrateur de GEPI de votre �tablissement.");
// Si l'affichage des devoirs est activ�e, on affiche les devoirs
if ($delai != 0) {
// Affichage de la semaine en cours
    $nb_dev = 0;
    for ($i = 0; $i <= $delai; $i++) {
        $jour = mktime(0, 0, 0, date('m',$today), (date('d',$today) + $i), date('Y',$today) );
        // On regarde pour chaque jour, s'il y a des devoirs dans � faire
        $appel_devoirs_cahier_texte = mysql_query("SELECT ct.contenu, g.id, g.description, ct.date_ct, ct.id_ct " .
                "FROM ct_devoirs_entry ct, groupes g, j_groupes_classes jgc WHERE (" .
                "ct.id_groupe = jgc.id_groupe and " .
                "g.id = jgc.id_groupe and " .
                "jgc.id_classe = '" . $id_classe . "' and " .
                "ct.contenu != '' and " .
                "ct.date_ct = '$jour')");
        $nb_devoirs_cahier_texte = mysql_num_rows($appel_devoirs_cahier_texte);
        $ind = 0;
        if ($nb_devoirs_cahier_texte != 0) {
            $nb_dev++;
            if ($nb_dev == '1') {
                if ((strftime("%a",$today) == "lun") or (strftime("%a",$today) == "lun.")) {$debutsemaine = $today;}
                if ((strftime("%a",$today) == "mar") or (strftime("%a",$today) == "mar.")) {$debutsemaine = mktime(0, 0, 0, date('m',$today), (date('d',$today) - 1), date('Y',$today) );}
                if ((strftime("%a",$today) == "mer") or (strftime("%a",$today) == "mer.")) {$debutsemaine = mktime(0, 0, 0, date('m',$today), (date('d',$today) - 2), date('Y',$today) );}
                if ((strftime("%a",$today) == "jeu") or (strftime("%a",$today) == "jeu.")) {$debutsemaine = mktime(0, 0, 0, date('m',$today), (date('d',$today) - 3), date('Y',$today) );}
                if ((strftime("%a",$today) == "ven") or (strftime("%a",$today) == "ven.")) {$debutsemaine = mktime(0, 0, 0, date('m',$today), (date('d',$today) - 4), date('Y',$today) );}
                if ((strftime("%a",$today) == "sam") or (strftime("%a",$today) == "sam.")) {$debutsemaine = mktime(0, 0, 0, date('m',$today), (date('d',$today) - 5), date('Y',$today) );}
                if ((strftime("%a",$today) == "dim") or (strftime("%a",$today) == "dim.")) {$debutsemaine = mktime(0, 0, 0, date('m',$today), (date('d',$today) - 6), date('Y',$today) );}
                $finsemaine = mktime(0, 0, 0, date('m',$debutsemaine), (date('d',$debutsemaine) + 6), date('Y',$debutsemaine) );
                echo "<p><strong><font color='blue' style='font-variant: small-caps;'>Semaine du ".strftime("%d %B", $debutsemaine)." au ".strftime("%d %B %Y", $finsemaine)."</font></strong></p>\n";
                echo "<b>Travaux personnels des $delai prochains jours</b>\n";
                echo "<table style=\"border-style:solid; border-width:0px; border-color: ".$couleur_bord_tableau_notice.";\" width = '100%' cellpadding='2'><tr><td>\n";
            }
            echo "<div style=\"border-style:solid; border-width:1px; border-color: ".$couleur_bord_tableau_notice."; background-color: ".$color_fond_notices["f"].";\"><div style='color: ".$color_police_travaux."; font-variant: small-caps; text-align: center; font-weight: bold;'>Travaux personnels<br />pour le ".strftime("%a %d %b", $jour)."</div>\n";
            // Affichage des devoirs dans chaque mati�re
            while ($ind < $nb_devoirs_cahier_texte) {
                $content = mysql_result($appel_devoirs_cahier_texte, $ind, 'contenu');
                // Mise en forme du texte
                include "../lib/transform.php";
                $date_devoirs = mysql_result($appel_devoirs_cahier_texte, $ind, 'date_ct');
                $id_devoirs =  mysql_result($appel_devoirs_cahier_texte, $ind, 'id_ct');
                $id_groupe_devoirs = mysql_result($appel_devoirs_cahier_texte, $ind, 'id');
                $matiere_devoirs = mysql_result($appel_devoirs_cahier_texte, $ind, 'description');
                $test_prof = "SELECT nom, prenom FROM j_groupes_professeurs j, utilisateurs u WHERE (j.id_groupe='".$id_groupe_devoirs."' and u.login=j.login) ORDER BY nom, prenom";
                $res_prof = sql_query($test_prof);
                $chaine = "";
                for ($k=0;$prof=sql_row($res_prof,$k);$k++) {
                  if ($k != 0) $chaine .= ", ";
                  $chaine .= htmlspecialchars($prof[0])." ".substr(htmlspecialchars($prof[1]),0,1).".";
                }
                $html = "<div style=\"border-style:solid; border-width:1px; border-color: ".$couleur_bord_tableau_notice."; background-color: ".$couleur_cellule["f"]."; padding: 2px; margin: 2px;\"><font color='".$color_police_matieres."' style='font-variant: small-caps;'><small><b><u>".$matiere_devoirs." (".$chaine.") :</u></b></small></font>\n".$html;
                // fichier joint
                $html .= affiche_docs_joints($id_devoirs,"t");
                $html .="</div>\n";
                if ($nb_devoirs_cahier_texte != 0) echo $html;
                $ind++;
            }
        echo "</div><br />\n";
        }
    }
    if ($nb_dev != 0) echo "</td></tr></table>\n";
}
// Affichage des informations g�n�rales
$appel_info_cahier_texte = mysql_query("SELECT contenu, id_ct  FROM ct_entry WHERE (id_groupe='$id_groupe' and date_ct='')");

$nb_cahier_texte = mysql_num_rows($appel_info_cahier_texte);
$content = @mysql_result($appel_info_cahier_texte, 0, 'contenu');
$id_ct = @mysql_result($appel_info_cahier_texte, 0, 'id_ct');
include "../lib/transform.php";
// documents joints
$html .= affiche_docs_joints($id_ct,"c");
if ($html != '')
echo "<b>Informations G�n�rales</b><table style=\"border-style:solid; border-width:1px; border-color: ".$couleur_bord_tableau_notice."; background-color: ".$color_fond_notices["i"]."; padding: 2px; margin: 2px;\" width = '100%' cellpadding='5'><tr style=\"border-style:solid; border-width:1px; border-color: ".$couleur_bord_tableau_notice."; background-color: ".$couleur_cellule["i"]."; padding: 2px; margin: 2px;\"><td>".$html."</td></tr></table><br />\n";
echo "</td>\n";
// Fin de la premi�re colonne


// D�but de la deuxi�me colonne
echo "<td valign=\"top\">";

echo "<table border=0 width = 100%>";
// Premi�re ligne
echo "<tr><td style=\"width:50%\"><b>" . strftime("%A %d %B %Y", $today) . "</b>";
#y? sont les ann�e, mois et jour pr�c�dents
#t? sont les ann�e, mois et jour suivants
$i= mktime(0,0,0,$month,$day-1,$year);
$yy = date("Y",$i);
$ym = date("m",$i);
$yd = date("d",$i);
$i= mktime(0,0,0,$month,$day+1,$year);
$ty = date("Y",$i);
$tm = date("m",$i);
$td = date("d",$i);
echo "</td><td><a title=\"Aller au jour pr�c�dent\" href=\"index.php?year=$yy&amp;month=$ym&amp;day=$yd&amp;id_classe=$id_classe&amp;id_groupe=$id_groupe\">&lt;&lt;</a></td><td align=center><a href=\"index.php?id_classe=$id_classe&amp;id_groupe=$id_groupe\">Aujourd'hui</a></td><td align=right><a title=\"Aller au jour suivant\" href=\"index.php?year=$ty&amp;month=$tm&amp;day=$td&amp;id_classe=$id_classe&amp;id_groupe=$id_groupe\">&gt;&gt;</a></td></tr>\n";
// affichage du texte
echo "<tr><td colspan=\"4\">\n";
echo "<center><b>les dix derni�res s�ances jusqu'au ".strftime("%A %d %B %Y", $today)." :</b></center></td></tr>\n";
//echo "<tr><td colspan=\"4\" style=\"border-style:solid; border-width:1px; border-color: ".$couleur_bord_tableau_notice."; background: rgb(199, 255, 153); padding: 2px; margin: 2px;\">";
echo "<tr><td colspan=\"4\" style=\"border-style:solid; border-width:0px; border-color: ".$couleur_bord_tableau_notice."; padding: 2px; margin: 2px;\">\n";




$current_time = time();
$req_notices =
    "select 'c' type, contenu, date_ct, id_ct
    from ct_entry
    where (contenu != ''
    and id_groupe='$id_groupe'
    and date_ct <= '$today'
    and date_ct <= '$current_time'
    and date_ct != ''
    and date_ct >= '".getSettingValue("begin_bookings")."')
    ORDER BY date_ct DESC, heure_entry DESC limit 10";

$res_notices = mysql_query($req_notices);
$notice = mysql_fetch_object($res_notices);

$req_devoirs =
    "select 't' type, contenu, date_ct, id_ct
    from ct_devoirs_entry
    where (contenu != ''
    and id_groupe = '".$id_groupe."'
    and date_ct != ''
    and date_ct <= '$today'
    and date_ct >= '".getSettingValue("begin_bookings")."'
    and date_ct <= '".getSettingValue("end_bookings")."'
    ) order by date_ct DESC limit 10";
$res_devoirs = mysql_query($req_devoirs);
$devoir = mysql_fetch_object($res_devoirs);

// Boucle d'affichage des notices dans la colonne de gauche
$date_ct_old = -1;
while (true) {
    // On met les notices du jour avant les devoirs � rendre aujourd'hui
    if ($notice && (!$devoir || $notice->date_ct >= $devoir->date_ct)) {
        // Il y a encore une notice et elle est plus r�cente que le prochain devoir, o� il n'y a plus de devoirs
        $not_dev = $notice;
        $notice = mysql_fetch_object($res_notices);
    } elseif($devoir) {
        // Plus de notices et toujours un devoir, ou devoir plus r�cent
        $not_dev = $devoir;
        $devoir = mysql_fetch_object($res_devoirs);
    } else {
        // Plus rien � afficher, on sort de la boucle
        break;
    }
    // Passage en HTML
    $content = &$not_dev->contenu;
    include ("../lib/transform.php");
    $html .= affiche_docs_joints($not_dev->id_ct,$not_dev->type);
    $titre = "";
    if ($not_dev->type == "t") {
        $titre .= "<strong>A faire pour le : </strong>\n";
    }
    $titre .= "<b>" . strftime("%a %d %b %y", $not_dev->date_ct) . "</b>\n";
    // Num�rotation des notices si plusieurs notice sur la m�me journ�e
    if ($not_dev->type == "c") {
      if ($date_ct_old == $not_dev->date_ct) {
        $num_notice++;
        $titre .= " <b><i>(notice N� ".$num_notice.")</i></b>";
      } else {
        // on afffiche "(notice N� 1)" uniquement s'il y a plusieurs notices dans la m�me journ�e
        $nb_notices = sql_query1("SELECT count(id_ct) FROM ct_entry WHERE (id_groupe='" . $current_group["id"] ."' and date_ct='".$not_dev->date_ct."')");
        if ($nb_notices > 1)
            $titre .= " <b><i>(notice N� 1)</i></b>";
        // On r�initialise le compteur
        $num_notice = 1;
      }
    }
    echo "<table cellspacing='0' style=\"border-style:solid; border-width:0px; border-color: ".$couleur_bord_tableau_notice."; padding: 0px; margin: 0px; width: 100%;'\">
    <tr>
    <td style=\"border-style:solid; border-width: 0px 0px 0px 0px; border-color: #000000; background: ".$color_fond_notices[$not_dev->type]."; padding: 2px; margin: 0px;\">
    ".$titre."</td>
    <tr>
    <td style=\"border-style:solid; border-width:0px; border-color: ".$couleur_bord_tableau_notice."; background-color: ".$couleur_cellule_gen."; padding: 0.5px 10px 0.5px 10px; margin: 0px;\">".$html."</td>
    </tr>
    </table><br />\n";

    if ($not_dev->type == "c") $date_ct_old = $not_dev->date_ct;
}


echo "</td></tr>";
echo "</table>";
echo "</td></tr></table>";
require("../lib/footer.inc.php");
?>