<?php /* spip/spip_session_lcs.php dernière revision : 03/11/2008 */
$DBG = false;
//register global
$action=$_GET['action'];
function fich_debug($texte){
        global $DBG;
	if($DBG){
		$fich=fopen("/tmp/debug_spiplcs.txt","a+");
		fwrite($fich,$texte);
		fclose($fich);
	}
}
include "/var/www/lcs/includes/headerauth.inc.php";
list ($idpers,$login)= isauth();

if ( $action=="login" ) {
    if ($idpers != "0") {
        // ou est l'espace prive ?
        @define('_DIR_RESTREINT_ABS', 'ecrire/');
        include_once _DIR_RESTREINT_ABS.'inc_version.php';

        include_spip('inc/cookie');
        include_spip('inc/auth_ldap');

        spip_connect();
        ### DBG
        fich_debug("DBG >> login : $login\n");
        // Si l'utilisateur figure deja dans la base, y recuperer les infos
        $result = spip_query("SELECT * FROM spip_auteurs WHERE login=" . spip_abstract_quote($login) . " AND source='ldap'");
        if (!spip_num_rows($result)) { 
            ### DBG
            fich_debug("DBG >> Login : $login n'est pas dans la base.\n");
            fich_debug("DBG >> statut : ".$GLOBALS['meta']["ldap_statut_import"]."\n");
            fich_debug("DBG >> ldap_base : ".$GLOBALS['ldap_base']."\n");
            // sinon importer les infos depuis LDAP,
            // avec le statut par defaut a l'install
            // Inserer les infos dans la base spip
            spip_connect_ldap();
            auth_ldap_inserer("uid=$login,ou=People,".$GLOBALS['ldap_base'], $GLOBALS['meta']["ldap_statut_import"]);
            $result = spip_query("SELECT * FROM spip_auteurs WHERE login=" . spip_abstract_quote($login) . " AND source='ldap'");
        }
        $row_auteur = spip_fetch_array($result);
        ### DBG
        fich_debug("DBG >> statut :".$row_auteur['statut']."\n");
        if ($row_auteur['statut'] == '0minirezo')
	   $cookie_admin = "@".$session_login;

        $var_f = charger_fonction('session', 'inc');
        $cookie_session = $var_f($row_auteur);
        ### DBG
        fich_debug("DBG >> $cookie_session\n");
        // On poste le cookie de session
        spip_setcookie('spip_session', $cookie_session);
        // On loge l'authentification
        spip_log("login de $login depuis LCS");
    }
} elseif ( $action=="logout" ) {
    close_session($idpers);
    @define('_DIR_RESTREINT_ABS', 'ecrire/');
    include_once _DIR_RESTREINT_ABS.'inc_version.php';

    include_spip('inc/cookie');
    include_spip('inc/auth_ldap');
    include_spip('inc/session');

    $result = spip_query("SELECT id_auteur FROM spip_auteurs WHERE login='$login' AND source='ldap'");
    $row_auteur = spip_fetch_array($result);
    ### DBG
    fich_debug("DBG >> id auteur : ".$row_auteur['id_auteur']."\n");
    supprimer_sessions($row_auteur['id_auteur']);
    spip_setcookie('spip_session', '', 0);
} else fich_debug("DBG >> No login No logout\n");

#On redirige vesr la page d'accueil dans tous les cas
echo "<script language=\"JavaScript\" type=\"text/javascript\">\n";
echo "<!--\n";
echo "top.location.href = '../lcs/index.php?url_redirect=accueil.php';\n";
echo "//-->\n";
echo "</script>\n";
?>
