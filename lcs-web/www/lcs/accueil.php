<?php
/* lcs/accueil Derniere version : 20/12/2013 */

include ("./includes/headerauth.inc.php");
include ("../Annu/includes/ldap.inc.php");
include ("../Annu/includes/ihm.inc.php");
include ("./includes/jlcipher.inc.php");

list ($idpers, $login)= isauth();
if ($idpers == "0")
    {
    header("Location:$urlauth");
    exit;}
elseif ( pwdMustChange($login) ) header("Location:../Annu/must_change_default_pwd.php");
// Recherche du nom a partir du login
list($user, $groups)=people_get_variables ($login, false);
// Recherche si l'utilisateur connecte possede le droit lcs_is_admin
$is_admin = is_admin("Lcs_is_admin",$login);
// Recherche si monlcs est present
if (!@mysql_select_db($DBAUTH, $authlink))
    die ("S&#233;lection de base de donn&#233;es impossible.");
$query="SELECT value from applis where name='monlcs'";
$result = @mysql_query($query, $authlink);
if ($result)
    while ($r=@mysql_fetch_array($result))
               $monlcs=$r["value"];
else
    die ("Param&#232;tres absents de la base de donn&#233;es.");
@mysql_free_result($result);


$html = "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\"
    \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n";
$html .= "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"fr\">\n";
$html .= "<head>\n";
$html .= "  <title>Accueil LCS</title>\n";
$html .= "  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"/>\n";
$html .= "  <link  href='../c/lcs.css' rel='StyleSheet' type='text/css'/>\n";
$html .= "</head>\n";
$html .= "<body class='accueil'>\n";
$html .= "<h4>Bonjour&nbsp;" . $user["fullname"] ."</h4>\n";
$html .= "<div align='center'>\n";
$html .= "<h5 style='text-align:left'>Bienvenue sur votre espace perso LCS</h5>\n";
$html .= "</div>\n";
$html .= "<ul>\n";
echo $html;

  if (!displogin($idpers)) {
    echo "<li><tt>F&#233;licitation, vous venez de vous connecter pour la 1&#232;re fois sur votre
          espace perso Lcs. Afin de garantir la confidentialit&#233; de vos donn&#233;es, nous
          vous encourageons, a changer votre mot de passe <a href=\"../Annu/mod_pwd.php\">en suivant ce lien... </a>
          </tt></li>\n";
  } else {
    $accord = "";
    if ($user["sexe"] == "F") $accord="e";
    echo "<li><tt>Derni&#232;re connexion le : " . displogin($idpers) . "</tt></li>\n";
    /* Affichage des stats user */
    echo "<li><tt>Vous vous &#234;tes connect&#233;".$accord." " . dispstats($idpers) . " fois &#224; votre espace perso.</tt></li>\n";
  }

  echo "</ul>\n";
  echo "<br />&nbsp;\n";

  // Affichage d'un message d'alerte pour le renouvellement des cles d'authentification
  if ( $is_admin=="Y" && detect_key_orig() ) {
        echo "<div class='alert_msg'>
                        Veuillez renouveler votre<b> jeu de cl&#233;s d'authentification</b> &#171;LCS&#187; en suivant ce&nbsp;
                        <a href='setup_keys.php'>lien...</a>
        </div>\n";
  }

  // Affichage des Menus users non privilegies
//Initialisation
$clientftp = $elfinder = $pma = $smbwebclient = false;
  // lecture lcs_applis
  $query="SELECT  name, value from applis where type='M' order by name";
  $result=@mysql_query($query);
  if ($result) {
        while ( $r=@mysql_fetch_object($result) ) {
            if ( $r->name == "clientftp" ) $clientftp = true;
            if ( $r->name == "elfinder" ) $elfinder = true;
            if ( $r->name == "pma" ) $pma = true;
            if ( $r->name == "smbwebclient" ) $smbwebclient = true;
        }
    }
    @mysql_free_result($result);

  echo "<blockquote>\n";
  // Affichage du menu espace web si l'espace perso existe
  if ( is_dir("/home/".$login) && is_dir("/home/".$login."/public_html") && is_dir("/home/".$login."/Documents")) {
    if ( !isset($monlcs) ){
      $html = "<table width='100%' border='0' cellspacing='10'>\n";
      if ( $clientftp ) {
        $html .= "<tr>\n";
        $html .= "  <td width='80'><img src='images/bt-V1-2.jpg' alt='ftpclient' align='middle' /></td>\n";
        $html .= "  <td><a href='statandgo.php?use=clientftp'>Explorateur de fichiers</a></td>\n";
        $html .= "</tr>\n";
      }
      if ( $elfinder ) {
        $html .= "<tr>\n";
        $html .= "  <td width='80'><img src='images/bt-V1-2.jpg' alt='elfinder' align='middle' /></td>\n";
        $html .= "  <td><a href='statandgo.php?use=elfinder'>Explorateur de fichiers</a></td>\n";
        $html .= "</tr>\n";
      }
      if ( $pma ) {
        $html .= "<tr>\n";
        $html .= "  <td width='80'><img src='images/bt-V1-3.jpg' alt='phpmyadmin' align='middle' /></td>\n";
        $html .= "  <td><a href='statandgo.php?use=pma'>Gestion base de donn&#233;es &#171; LCS &#187;</a></td>\n";
        $html .= "</tr>\n";
      }
      if ( $se3netbios != "" && $se3domain != "" && $smbwebclient ) {
        $html .= "<tr>\n";
        $html .= "  <td width='80'><img src='images/bt-V1-4.jpg' alt='smbwebclient' align='middle' /></td>\n";
        $html .= "  <td><a href='statandgo.php?use=smbwebclient'>Acc&#232;s au serveur de fichiers &#171;Se3&#187;</a></td>\n";
        $html .= "</tr>\n";
      }
      $html .= "</table>\n";
      echo $html;
    }
  }   else {
          // Pb sur espace perso utilisateur
          echo "<P><B><font color=\"orange\">La cr&#233;ation de votre espace personnel (/home/$login) a &#233;chou&#233; ou son arborescence comporte une anomalie de structure !</B>
          , veuillez contacter <a href='mailto:$MelAdminLCS?subject=PB creation Espace perso LCS de $login'>l'administrateur du syst&#232;me</a>
           </font></P>\n";
  }
  echo "</blockquote>\n";

  include ("./includes/pieds_de_page.inc.php");

?>
