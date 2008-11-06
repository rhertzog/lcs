<?php
/* =============================================
   Projet LCS : Linux Communication Server
   accueil.php
   jLCF : jean-luc.chretien@tice.ac-caen.fr
   Equipe Tice académie de Caen
   derniere mise a jour : 25/10/2008
   ============================================= */
include ("./includes/headerauth.inc.php");
include ("../Annu/includes/ldap.inc.php");
include ("../Annu/includes/ihm.inc.php");
include ("./includes/jlcipher.inc.php");

list ($idpers, $login)= isauth();
if ($idpers == "0")    header("Location:$urlauth");
// Recherche du nom a partir du login
list($user, $groups)=people_get_variables ($login, false);
// Recherche si l'utilisateur connecte possede le droit lcs_is_admin
$is_admin = is_admin("Lcs_is_admin",$login);

// Recherche si monlcs est present
$result=@mysql_db_query("$DBAUTH","SELECT value from applis where name='monlcs'", $authlink);
if ($result)
    while ($r=@mysql_fetch_array($result)) 
               $monlcs=$r["value"];
else
    die ("paramètres absents de la base de données");
@mysql_free_result($result);

$html = "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\"
    \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n";
$html .= "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"fr\">\n";
$html .= "<head>\n";
$html .= "  <title>Accueil LCS</title>\n";
$html .= "  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\"/>\n";
$html .= "  <link  href='c/lcs.css' rel='StyleSheet' type='text/css'/>\n";
$html .= "</head>\n";
$html .= "<body class='accueil'>\n";
$html .= "<h4>Bonjour&nbsp;" . $user["fullname"] ."</h4>\n";
$html .= "<div align='center'>\n";
$html .= "<h5 style='text-align:left'>Bienvenue sur votre espace perso LCS</h5>\n";
$html .= "</div>\n";
$html .= "<ul>\n";
echo $html;

  if (!displogin($idpers)) {
    echo "<li><tt>Félicitation, vous venez de vous connecter pour la 1ère fois sur votre
          espace perso Lcs. Afin de garantir la confidentialité de vos données, nous
          vous encourageons, a changer votre mot de passe <a href=\"../Annu/mod_pwd.php\">en suivant ce lien... </a>
          </tt></li>\n";
  } else {
    $accord == "";
    if ($user["sexe"] == "F") $accord="e";
    echo "<li><tt>Derni\xe8re connexion le : " . displogin($idpers) . "</tt></li>\n";
    /* Affichage des stats user */
    echo "<li><tt>Vous vous \xeates connect\xe9".$accord." " . dispstats($idpers) . " fois \xe0 votre espace perso.</tt></li>\n";
  }

  echo "</ul>\n";
  echo "<br />&nbsp;\n";

  // Affichage d'un message d'alerte pour le renouvellement des clés d'authentification
  if ( $is_admin=="Y" && detect_key_orig() ) {
        echo "<div class='alert_msg'>
                        Veuillez renouveler votre<b> jeu de clés d'authentification</b> «LCS» en suivant ce&nbsp;
                        <a href='setup_keys.php'>lien...</a>
        </div>\n";
  }

  // Affichage des Menus users non privilégiés

  // lecture lcs_applis 
  $query="SELECT  name, value from applis where type='M' order by name";
  $result=@mysql_query($query);
  if ($result) {
        while ( $r=@mysql_fetch_object($result) ) {
            if ( $r->name == "clientftp" ) $ftpclient = true;
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
      if ( $ftpclient ) {
        $html .= "<tr>\n";
        $html .= "  <td width='80'><img src='images/bt-V1-2.jpg' alt='ftpclient' align='middle' /></td>\n";
        $html .= "  <td><a href='statandgo.php?use=clientftp'>Espace web « LCS »</a></td>\n";
        $html .= "</tr>\n";
      }
      if ( $pma ) {
        $html .= "<tr>\n";
        $html .= "  <td width='80'><img src='images/bt-V1-3.jpg' alt='phpmyadmin' align='middle' /></td>\n";
        $html .= "  <td><a href='statandgo.php?use=pma'>Gestion base de données « LCS »</a></td>\n";
        $html .= "</tr>\n";
      }
      if ( $se3netbios != "" && $se3domain != "" && $smbwebclient ) {
        $html .= "<tr>\n";
        $html .= "  <td width='80'><img src='images/bt-V1-4.jpg' alt='smbwebclient' align='middle' /></td>\n";
        $html .= "  <td><a href='statandgo.php?use=smbwebclient'>Accès au serveur de fichiers «Se3»</a></td>\n";
        $html .= "</tr>\n";
      }
      $html .= "</table>\n";
      echo $html;
    }
  }   else {
          // Pb sur espace perso utilisateur
          echo "<P><B><font color=\"orange\">La création de votre espace personnel (/home/$login) a échoué ou son arborescence comporte une anomalie de structure !</B>
          , veuillez contacter <a href='mailto:$MelAdminLCS?subject=PB creation Espace perso LCS de $login'>l'administrateur du système</a>
           </font></P>\n";
  }
  echo "</blockquote>\n";

  include ("./includes/pieds_de_page.inc.php");

?>
