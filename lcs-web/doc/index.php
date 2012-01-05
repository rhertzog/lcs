<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <title>Index Documentation LCS</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <style type="text/css" media="all">@import "c/c.css";</style>
</head>
<body>

<h2 style="text-align:center">Documentation Linux Communication Serveur</h2>

<h3><a href="http://wwdeb.crdp.ac-caen.fr/LcsDoc/index.php/Accueil">Documentation officielle en ligne</a></h3>
<h3><a href="aide_fr/index.html">Pr&eacute;sentation du LCS</a></h3>
<h3><a href="aide_fr/sftp_lcs.html">Acc&egrave;s sftp</a></h3>
<?

if ( is_dir( "/usr/share/doc/lcs/spip" ) ) {
    echo "<h3><a href=\"spip/html/\">CMS spip</a></h3>";
}
if ( is_dir( "/usr/share/doc/lcs/smbwebclient" ) ) {
    echo "<h3><a href=\"smbwebclient/html/\">Acc&egrave;s serveur SE3 <em>smbwebclient</em></a></h3>";
}
if ( is_dir( "/usr/share/doc/lcs/clientftp" ) ) {
    echo "<h3><a href=\"clientftp/html/\">Acc&egrave;s espace personnel <em>clientftp</em></a></h3>";
}
if ( is_dir( "/usr/share/doc/lcs/monlcs" ) ) {
    echo "<h3><a href=\"monlcs/html/\">Portail de ressources p&eacute;dagogiques <em>Mon LCS</em></a></h3>";
}
?>
<hr />
<?
$lcsdoc[0][module]="base";
$lcsdoc[0][comment]="Lcs base";
$lcsdoc[1][module]="web";
$lcsdoc[1][comment]="Lcs web";
$lcsdoc[2][module]="phpsysinfo";
$lcsdoc[2][comment]="Lcs phpsysinfo";
$lcsdoc[3][module]="dns";
$lcsdoc[3][comment]="Lcs service de nom DNS Bind";
$lcsdoc[4][module]="smtp";
$lcsdoc[4][comment]="Service de courrier smtp Postfix";
$lcsdoc[5][module]="imap";
$lcsdoc[5][comment]="Service de courrier imap";
$lcsdoc[6][module]="squirrelmail";
$lcsdoc[6][comment]="Webmail squirrelmail";
$lcsdoc[7][module]="squid";
$lcsdoc[7][comment]="Cache mandataire squid";
$lcsdoc[8][module]="squidguard";
$lcsdoc[8][comment]="Filtrage d'URL squidguard";

for ( $i=0;$i <= count($lcsdoc);$i++  ) {
    if ( is_dir( "/usr/share/doc/lcs/".$lcsdoc[$i][module] ) ) {
        echo "<h3><a href=\"".$lcsdoc[$i][module]."/html/\">".$lcsdoc[$i][comment]."</a></h3>";
    } 
}
?>
</body>
</html>