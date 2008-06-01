<?php
/* se3orlcs_import_sconet.php Derniere version : 11/10/07 */
// Detection LCS ou SE3
if ( file_exists("/var/www/se3") ) {
    // Section SE3
    $user_web = "www-se3";
    // Chemin:
    $pathlcsorse3 = "";
    $path_crob_ldap_functions = "";
    $racine_www= "/var/www/se3";
    $dossier_tmp_import_comptes= "/var/lib/se3/import_comptes";
    $chemin_csv="/setup/csv";
    $php="/usr/bin/php";
    $chemin="/usr/share/se3/scripts";
    $chemin_fich="$dossier_tmp_import_comptes";
    // Style du rapport
    $background ="/elements/images/fond_SE3.png";
    $stylecss="/elements/style_sheets/sambaedu.css";
    $helpinfo="../elements/images/help-info.gif";

    include "entete.inc.php";
    include "ldap.inc.php";
    include "ihm.inc.php";

    require_once ("lang.inc.php");
    bindtextdomain('se3-annu',"/var/www/se3/locale");
    textdomain ('se3-annu');

    echo "<h1>".gettext("Annuaire")."</h1>\n";
    $_SESSION["pageaide"]="Annuaire";

} else {
    // Section LCS
    $user_web = "www-data";
    // Chemin:
    $pathlcsorse3 = "../lcs/includes/";
    $path_crob_ldap_functions = "includes/";
    $racine_www= "/var/www";
    $dossier_tmp_import_comptes= "/var/lib/lcs/import_comptes";
    $chemin_csv="/setup/csv";
    $php="/usr/bin/php";
    $chemin="/usr/share/lcs/scripts";
    $chemin_fich="$dossier_tmp_import_comptes";
    // Style du rapport
    $background ="Images/espperso.jpg";
    $stylecss="style/style.css";
    $helpinfo="../images/help-info.gif";

    include "../lcs/includes/headerauth.inc.php";
    include "includes/ldap.inc.php";
    include "includes/ihm.inc.php";

    list ($idpers,$login)= isauth();
    if ($idpers == "0") header("Location:$urlauth");

    header_html();
}
?>