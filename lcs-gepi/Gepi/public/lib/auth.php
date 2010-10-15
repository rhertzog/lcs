<?php
/*
    R�alisation     : BIOT Nicolas pour PHPindex
    Contact        : BIOT Nicolas <nicolas@globalis-ms.com>

    ----------------------------------------------------------
    Fichier        : auth.inc.php
    Description    : Script d'authentification
    Date cr�ation    : 14/05/2001
    Date de modif    : 10/11/2001 Antoine Bajolet
*/
$user = getSettingValue("cahiers_texte_login_pub");
$pwd = getSettingValue("cahiers_texte_passwd_pub");

if (isset($_SERVER['PHP_AUTH_USER'])) {
    $PHP_AUTH_USER = $_SERVER['PHP_AUTH_USER'];
}
if (isset($_SERVER['PHP_AUTH_PW'])) {
    $PHP_AUTH_PW = $_SERVER['PHP_AUTH_PW'];
}

function phpdigAuth(){
    Header("WWW-Authenticate: Basic realm=\"Cahiers de texte\"");
    Header("HTTP/1.0 401 Unauthorized");

    require("secure/connect.inc.php");
    //**************** EN-TETE *****************
    $titre_page = "Cahiers de texte";
    require_once("lib/header.inc");

    //**************** FIN EN-TETE *****************
    echo "<H3><center>En raison du caract�re personnel du contenu, ce site est soumis � des restrictions utilisateurs.
    <br />Pour acc�der aux cahiers de texte, vous devez demander aupr�s de l'administrateur,
    <br />le nom d'utilisateur et le mot de passe.</center></H3>";
    echo "</body></html>";
    exit();
}

if (($user!='') and ($pwd!=''))
{
if( !isset($PHP_AUTH_USER) && !isset($PHP_AUTH_PW) ) {
    phpdigAuth();
}
else {
    if( $PHP_AUTH_USER==$user && $PHP_AUTH_PW==$pwd ) {
        // la suite du script sera ex�cut�e
    }
    else{
        // rappel de la fonction d'identification
        phpdigAuth();
    }
}
}
?>