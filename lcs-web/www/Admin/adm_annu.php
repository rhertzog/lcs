<?php
/* Administration LCS */
include ("../lcs/includes/headerauth.inc.php");
include ("../Annu/includes/ldap.inc.php");

list ($idpers, $login)= isauth();
if ($idpers == "0")    header("Location:$urlauth");
echo "<HTML>\n
		<HEAD>
			<TITLE>...::: Adminstration LCS :::...</TITLE>
			<LINK  href='../style.css' rel='StyleSheet' type='text/css'>\n
		</HEAD>
      <BODY>\n";
?>

  <table width="100%" border="0" cellspacing="0">
    <tr>
      <td width="80"><img src="../lcs/images/bt-V1-2.jpg" width="75" height="75"></td>
      <td><a href="../yala/" target="principale">Explorateur LDAP</a></td>
    </tr>
    <tr>
      <td width="80"><img src="../lcs/images/bt-V1-3.jpg" width="75" height="75"></td>
      <td><a href="export_ldif.php" target="principale">Sauvegarde annuaire</a></td>
    </tr>
    <tr>
      <td width="80"><img src="../lcs/images/bt-V1-4.jpg" width="75" height="75"></td>
      <td><a href="import_ldif.php" target="principale">Restauration annuaire</a></td>
    </tr>
    <tr>
      <td width="80"><img src="../lcs/images/bt-V1-2.jpg" width="75" height="75"></td>
      <td><a href="ldap_cleaner.php" target="principale">Nettoyage comptes orphelins</td>
    </tr>
    <tr>
      <td width="80"><img src="../lcs/images/bt-V1-2.jpg" width="75" height="75"></td>
      <td><a href="" target="principale">Replication de l'annuaire</a></td>
    </tr>
  </table>

<?
include ("../lcs/includes/pieds_de_page.inc.php");
?>