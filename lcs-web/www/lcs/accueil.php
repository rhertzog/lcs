<?php
/* =============================================
   Projet LCS : Linux Communication Server
   accueil.php
   jLCF : jean-luc.chretien@tice.ac-caen.fr
   Equipe Tice académie de Caen
   derniere mise a jour : 24/06/2008
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

echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">\n";
echo "<html>\n
      <head>\n
        <title>Accueil LCS</title>
        <meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">\n
        <link  href='../style.css' rel='StyleSheet' type='text/css'>\n
      </head>\n
      <body>\n";

echo "<h4>Bonjour&nbsp;" . $user["fullname"] ."</h4>\n
<div align='center'>\n
    <h5 style='text-align:left'>\n
        <img src='images/home.jpg' widht='40' height='40' alt='Home' align='middle' border='0'>
        Bienvenue sur votre espace perso LCS
    </h5>\n
</div>\n
<ul>\n";

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
  echo "<br>&nbsp;\n";

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
        if ( $ftpclient ) echo "<img src=\"images/bt-V1-2.jpg\" alt=\"ftpclient\" align=\"middle\"><a href=\"statandgo.php?use=clientftp\">Espace web « LCS »</a><br>\n";
        if ( $pma ) echo "<img src=\"images/bt-V1-3.jpg\" alt=\"pma\" align=\"middle\"><a href=\"statandgo.php?use=pma\">Gestion base de données « LCS »</a><br>\n";
        if ( $se3netbios != "" && $se3domain != "" && $smbwebclient )
	   echo "<img src=\"images/bt-V1-4.jpg\" alt=\"smbwebclient\" align=\"middle\"><a href=\"statandgo.php?use=smbwebclient\">Accès au serveur de fichiers «Se3»</A><br>\n";
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
