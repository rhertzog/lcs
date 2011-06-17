<?php

include("../lcs/includes/headerauth.inc.php");
include("../Annu/includes/ihm.inc.php");
include("../Annu/includes/ldap.inc.php");

// register globals
$action = $_POST['action'];
if (! $action) $action = "index";

list($idpers, $login) = isauth();
if ($idpers == "0") {
    header("Location: $urlauth");
    exit;
}

echo "<html>\n";
echo "  <head>\n";
echo "          <title>...::: Interface d'administration Serveur LCS :::...</title>\n";
echo "          <link  href='../Annu/style.css' rel='StyleSheet' type='text/css'>\n";
echo "  </head>\n";
echo "  <body>\n";
echo "  <h1>Synchronisation avec un annuaire Active Directory</h1>\n";

$isadmin = is_admin("lcs_is_admin", $login);

if ($isadmin != "Y") {
    echo "<div class='error_msg'>Cette fonctionnalit&#233;, n&#233;cessite les droits d'administrateur du serveur LCS !</div>";
    include ("../lcs/includes/pieds_de_page.inc.php");
    exit;
}

function display_update_form() {
    global $ad_server, $ad_base_dn, $ad_bind_dn, $ad_bind_pw;
    ?><form method="post" action="manage_sync_ad.php">
    Adresse du serveur Active Directory : <input type="text"
    name="ad_server" value="<?php echo $ad_server; ?>" /><br/>
    DN de base : <input type="text" name="ad_base_dn" value="<?php echo
    $ad_base_dn; ?>" /><br/>
    DN de connexion : <input type="text" name="ad_bind_dn" value="<?php
    echo $ad_bind_dn; ?>" /><br/>
    Mot de passe : <input type="password" name="ad_bind_pw" value="<?php
    echo $ad_bind_pw; ?>" /><br/>
    <input type="hidden" name="action" value="update-params"/>
    <input type="submit" name="submit" value="Enregistrer les param&egrave;tres de connexion"/>
    </form>
    <?php
}

function display_enable_form() {
    global $ad_auth_delegation;
    if ($ad_auth_delegation == "true") {
    ?>
    <p>Attention, la d&eacute;sactivation de la synchronisation implique la
    r&eacute;initialisation des mots de passe des utilisateurs dont
    l'authentification &eacute;tait d&eacute;port&eacute;e &agrave; leur
    date de naissance (YYYYMMDD).</p>
    <form method="post" action="manage_sync_ad.php">
    <input type="hidden" name="action" value="disable-sync"/>
    <input type="submit" name="submit" value="D&eacute;sactiver la synchronisation Active Directory"/>
    </form>
    <?php
    } else {
    ?>
    <p>En activant la synchronisation ci-dessous vous configurez le
    serveur LDAP local pour qu'il puisse d&eacute;l&eacute;guer
    l'authentification au serveur Active Directory list&eacute; ci-dessus. 
    Cette d&eacute;l&eacute;gation se g&egrave;re ensuite compte par
    compte (via l'application Annuaire).</p>
    <form method="post" action="manage_sync_ad.php">
    <input type="hidden" name="action" value="enable-sync"/>
    <input type="submit" name="submit" value="Activer la synchronisation Active Directory"/>
    </form>
    <?php
    }
}

function update_param($name, $value) {
    $query = sprintf("UPDATE params SET value='%s' WHERE name='%s'",
		mysql_real_escape_string($value),
		mysql_real_escape_string($name));
    $result = mysql_query($query);
    if (! $result) {
	echo "<p>Echec de la requete $query: " . mysql_error() . "</p>\n";
    }
}

function validate_ad_params() {
    global $ad_server, $ad_base_dn, $ad_bind_dn, $ad_bind_pw;
    $ds = ldap_connect($ad_server);
    if (! $ds) {
	echo "<p>PROBLEME: Echec de la connexion à ldap://$ad_server...</p>";
	return false;
    }
    $r = ldap_bind($ds, $ad_bind_dn, $ad_bind_pw);
    if (! $r) {
	echo "<p>PROBLEME: Echec de la connexion/authentification sur ldap://$ad_server " .
	     "avec l'identifiant '$ad_bind_dn' et le mot de passe '$ad_bind_pw'.</p>";
	return false;
    }
    $sr = ldap_read($ds, $ad_base_dn, '(objectclass=*)');
    if (! $sr) {
	echo "<p>PROBLEME: Impossible de r&eacute;cup&eacute;rer l'entr&eacute;e $ad_base_dn sur " .
	     "le serveur Active Directory.</p>";
	return false;
    }
    return true;
}

function connect_to_ldap() {
    global $ldap_server, $ldap_port, $MelAdminLcs, $adminDn, $adminPw;
    $ds = ldap_connect($ldap_server, $ldap_port);
    if (! $ds) {
	echo "<div class='error_msg'>Erreur de connection &#224; l'annuaire, veuillez contacter <A HREF='mailto:$MelAdminLCS?subject=PB connection a l'annuaire'>l'administrateur du syst&#232;me</A>.</div>\n";
	include ("../lcs/includes/pieds_de_page.inc.php");
	exit;
    }

    $r = ldap_bind($ds, $adminDn, $adminPw); // Bind en admin
    if (! $r) {
	echo "<div class='error_msg'>Erreur d'authentification &#224; l'annuaire, veuillez contacter <A HREF='mailto:$MelAdminLCS?subject=PB authentification &agrave; l'annuaire'>l'administrateur du syst&#232;me</A>.</div>\n";
	include ("../lcs/includes/pieds_de_page.inc.php");
	exit;
    }
    return $ds;
}

if ($action == "index") {
    display_update_form();
    echo "<hr/>";
    display_enable_form();
} elseif ($action == "update-params") {
    $ad_server = $_POST["ad_server"];
    $ad_base_dn = $_POST["ad_base_dn"];
    $ad_bind_dn = $_POST["ad_bind_dn"];
    $ad_bind_pw = $_POST["ad_bind_pw"];
    if ($ad_auth_delegation != "true" || validate_ad_params()) {
	update_param("ad_server", $_POST["ad_server"]);
	update_param("ad_base_dn", $_POST["ad_base_dn"]);
	update_param("ad_bind_dn", $_POST["ad_bind_dn"]);
	update_param("ad_bind_pw", $_POST["ad_bind_pw"]);
	$handle = popen("/usr/bin/sudo /usr/sbin/ldapedu-server-config-ad-auth", "w");
	fwrite($handle, "$ad_server\n");
	fwrite($handle, "$ad_base_dn\n");
	fwrite($handle, "$ad_bind_dn\n");
	fwrite($handle, "$ad_bind_pw\n");
	$status = pclose($handle) >> 8;
	if ($status != 0) {
	    echo "<p>Erreur lors de l'execution de ldapedu-server-config-ad-auth.</p>";
	} else {
	    echo "<p><strong>Les param&egrave;tres ont &eacute;t&eacute; sauvegard&eacute;s.</strong></p>";
	}
    } else {
	echo "<p><strong>Les param&egrave;tres suivants n'ont pas " .
	     "&eacute;t&eacute; enregistr&eacute;s " .
	     "car ils ne sont pas fonctionnels.</strong></p>";
	display_update_form();
    }
    echo "<hr/>";
    display_enable_form();
} elseif ($action == "enable-sync") {
    if (validate_ad_params()) {
	update_param("ad_auth_delegation", "true");
	system("sudo /usr/share/lcs/scripts/lcs2ldapedu.sh >/dev/null");
	echo "<p>La d&eacute;l&eacute;gation a &eacute;t&eacute; " .
	     "activ&eacute;e, il vous reste &agrave; l'activer " .
	     "sur les comptes concern&eacute;s.</p>";
    } else {
	echo "<p>La d&eacute;l&eacute;gation n'a pas &eacute;t&eacute; " .
	     "activ&eacute;e car la connexion avec le serveur Active " .
	     "Directory ne fonctionne pas.</p>";
    }
} elseif ($action == "disable-sync") {
    update_param("ad_auth_delegation", "false");
    $ds = connect_to_ldap();
    $sr = ldap_search($ds, $peopleDn, "(&(userPassword=*)(uid=*))", array("uid", "userPassword"));
    if (! $sr) {
	echo "<div class='error_msg'>Erreur dans la recherche LDAP.</div>";
        include ("../lcs/includes/pieds_de_page.inc.php");
	exit;
    }
    $entries = ldap_get_entries($ds, $sr);
    $changed = 0;
    for ($i = 0; $i < $entries["count"]; $i++) {
	if (preg_match("/^{sasl}/", $entries[$i]["userpassword"][0])) {
	    user_disable_ad_auth($entries[$i]["uid"][0], $ds);
	    $changed++;
	}
    }
    echo "<p>La d&eacute;l&eacute;gation a &eacute;t&eacute; " .
         "d&eacute;sactiv&eacute;e, " . $changed . " comptes " .
	 "ont &eacute;t&eacute; affect&eacute;s.</p>";
} else {
    echo "<div class='error_msg'>Impossible d'identifier l'action &agrave; effectuer.</div>";
}

include ("../lcs/includes/pieds_de_page.inc.php");
?>
