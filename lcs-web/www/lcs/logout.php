<?
/* lcs/logout.php version du :  06/02/2007 implanté par lcs-spip_1.07*/
if ( ! is_dir('/usr/share/lcs/spip') ) {
    require ("./includes/headerauth.inc.php");
    list ($idpers,$login)= isauth();
    close_session($idpers);
    echo "<script language=\"JavaScript\" type=\"text/javascript\">\n";
    echo "<!--\n";
    echo "top.location.href = '../lcs/index.php?url_redirect=accueil.php';\n";
    echo "//-->\n";
    echo "</script>\n";
} else
    header("Location:../spip/spip_session_lcs.php?action=logout");
?>
