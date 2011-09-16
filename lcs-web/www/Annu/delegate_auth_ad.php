<?php

include("../lcs/includes/headerauth.inc.php");
include("includes/ldap.inc.php");
include("includes/ihm.inc.php");

// register globals
$uid = empty($_GET['uid']) ? "" : $_GET['uid'];
$groupcn = empty($_GET['groupcn']) ? "" : $_GET['groupcn'];
$action = $_GET['action'];
$verbose = empty($_GET['verbose']) ? 0 : $_GET['verbose'];
$all = empty($_GET['all']) ? 0 : $_GET['all'];

list($idpers, $login) = isauth();
if ($idpers == "0") {
    header("Location: $urlauth");
    exit;
}

header_html("Gestion de l'authentification d&eacute;port&eacute;e");

$isadmin = is_admin("Annu_is_admin", $login);

if ($isadmin != "Y") {
    echo "<div class='error_msg'>Cette fonctionnalit&#233;, n&#233;cessite les droits d'administrateur de l'annuaire du serveur LCS !</div>";
    include ("../lcs/includes/pieds_de_page.inc.php");
    exit;
}

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

if ($uid and $action == "enable") {
    if (user_enable_ad_auth($uid, $ds)) {
	echo "L'utilisateur $uid est d&eacute;sormais authentifi&eacute; via le " .
	     "serveur Active Directory.";
    } else {
	echo "<div class='error_msg'>Erreur lors de l'activation de " .
	     "l'authentification d&eacute;port&eacute;e...</div>";
    }
} elseif ($uid and $action == "disable") {
    if (user_disable_ad_auth($uid, $ds)) {
	echo "L'utilisateur $uid est d&eacute;sormais authentifi&eacute; par le serveur " .
	     "LCS (et pas par le serveur Active Directory).";
    } else {
	echo "<div class='error_msg'>Erreur lors de la d&eacute;sactivation " .
	     "de l'authentification d&eacute;port&eacute;e...</div>";
    }
} elseif ($groupcn) {
    echo "<h1>";
    if ($action == "enable") {
	echo "Activation ";
    } elseif ($action == "disable") {
	echo "D&eacute;sactivation ";
    } else {
	echo "<div class='error_msg'>Aucune action demand&eacute;e !</div>";
        include ("../lcs/includes/pieds_de_page.inc.php");
	exit;
    }
    echo "de l'authentification d&eacute;port&eacute;e pour le groupe $groupcn</h1>";

    $liste = search_uids("(cn=$groupcn)", NULL);
    $i = 0;
    while (isset($liste[$i])) {
	if ($verbose) {
	    echo "Traitement de " . $liste[$i]['uid'] . " : ";
	}
	if ($liste[$i]["prof"] and !$all) {
	    if ($verbose) {
		echo "ignor&eacute; car il est prof.<br/>";
	    }
	    $i++; continue;
	}

	if ($action == "enable")
	    $result = user_enable_ad_auth($liste[$i]['uid'], $ds);
	else
	    $result = user_disable_ad_auth($liste[$i]['uid'], $ds);

	if ($result and $verbose) {
	    echo "OK.<br/>";
	} elseif (! $result) {
	    if ($verbose)
		echo "&Eacute;chec";
	    else
		echo "&Eacute;chec de l'op&eacute;ration pour " .
		     $liste[$i]['uid'] . ".<br/>";
	}

	$i++;
    }
    echo "<strong>Termin&eacute; !</strong><br/>";
} else {
    echo "<div class='error_msg'>Aucune action demand&eacute;e !</div>";
}

include ("../lcs/includes/pieds_de_page.inc.php");
?>