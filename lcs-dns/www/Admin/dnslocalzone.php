<?php
/*===========================================
   Projet LcSE3
   Administration du serveur LCS
   Equipe Tice academie de Caen
   Distribue selon les termes de la licence GPL
   Derniere modification : 23/01/2015
   ============================================= */
include "../Annu/includes/check-token.php";
if (!check_acces()) exit;

$login=$_SESSION['login'];


include ("../lcs/includes/headerauth.inc.php");
include ("../Annu/includes/ldap.inc.php");
include ("../Annu/includes/ihm.inc.php");

// Purifier
if (count($_GET)>0) {
  //configuration objet
  include ("../lcs/includes/htmlpurifier/library/HTMLPurifier.auto.php");
  $config = HTMLPurifier_Config::createDefault();
  $purifier = new HTMLPurifier($config);
  //purification des variables
  $do=$purifier->purify($_GET['do']);
}
// Messages d'aide
function msgaide($msg) {
    return ("&nbsp;<u onmouseover=\"this.T_SHADOWWIDTH=5;this.T_STICKY=1;return escape".gettext("('".$msg."')")."\"><img name=\"action_image2\"  src=\"../images/help-info.gif\" ALT=\"Infos\"></u>");
}
$msg1="G&#233;n&#232;re un fichier CSV de la zone DNS locale disponible dans le r&#233;pertoire <b>Documents > ZoneDNS</b> du compte administrateur.";
$msg2="Met &#224; jour la zone DNS locale &#224; partir du fichier CSV.";
?>
<!doctype html>
<head>
<meta charset="utf-8">
<title>...::: Gestion zone DNS locale LCS  :::...</title>
<link  href='../Annu/style.css' rel='StyleSheet' type='text/css'>
<link rel='stylesheet' href='../libjs/jquery-ui/css/redmond/jquery-ui.css'>
<script type='text/javascript' src='../libjs/jquery/jquery.js'></script>
<script type='text/javascript' src='../libjs/jquery-ui/jquery-ui.js'></script>
<script type='text/javascript' src='./js/script_dns.js'></script>
</head>
<body>
<div id='container'><h2>Gestion zone locale DNS LCS</h2>
<?php
$mesg='<p>Le fichier de zone que vous pouvez éditer est au format "csv" : &lt;nom machine&gt;,&lt;adresse IP&gt;</p>
<p>
En règle générale, les noms de host et de domaine ont des restrictions sur certains caractères.
</p>
<p>Seuls les caractères suivants sont autorisés \'a-z\', \'A-Z\', \'0-9\', \'-\'
</p>
<p>
Il est important de garder <b>toutes les lignes </b> et de ne modifier que celles voulues.
</p>
<p>
Exemple : il existe dans le fichier de zone une ligne : machine6, XXX.XXX.XXX.6
Vous possédez un serveur NAS à cette adresse.
</p>
<p>
vous modifiez cette ligne ainsi : NAS,XXX.XXX.XXX.6
</p>
<p>
Votre NAS sera accessible avec le nom suivant : NAS, ou NAS.domaine.fr où domaine est le nom de domaine de votre établissement.
</p>
<p>
Une fois validé, le dispositif vérifie l\'intégrité de vos modifications et le cas échéant, met à jour la zone DNS.</p>';

if (is_admin("system_is_admin",$login)=="Y") {
echo $mesg;
echo ' <form id="form1" action="'. htmlentities($_SERVER['PHP_SELF']).'" method="post">
    </form>
    <button id="genere">Editer le fichier  de la zone DNS locale</button>
    <div id="dialog" title="Edition du fichier de la zone DNS locale">
    <form>
    <fieldset class="ui-helper-reset">
    <p class="validateTips"></p>
    <textarea id="contenu" cols="50" rows="20"></textarea>
    <input type="hidden" name="jeton" id="jeton" value="'.md5($_SESSION['token'].htmlentities("/Admin/dns_ajax.php")).'"/>
    </fieldset>
    </form>
    </div>';

}// fin is_admin
else echo "Vous n'avez pas les droits n&#233;cessaires pour ordonner cette action...";
echo "</div><!-- Fin container-->\n";
include ("../lcs/includes/pieds_de_page.inc.php");
?>
