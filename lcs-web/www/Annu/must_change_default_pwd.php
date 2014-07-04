<?php
/* =============================================
   Projet LCS-SE3
   Consultation/ Gestion de l'annuaire LDAP
   Equipe Tice academie de Caen
   Distribue selon les termes de la licence GPL
   Derniere modification : 04/04/2014
   ============================================= */
include "includes/check-token.php";
if (!check_acces()) exit;

$login=$_SESSION['login'];
include "../lcs/includes/headerauth.inc.php";
include "includes/ldap.inc.php";
include "includes/ihm.inc.php";
include ("../lcs/includes/jlcipher.inc.php");

if ( count($_POST)>0 ) {
  	//configuration objet
 	include ("../lcs/includes/htmlpurifier/library/HTMLPurifier.auto.php");
 	$config = HTMLPurifier_Config::createDefault();
 	$purifier = new HTMLPurifier($config);
    //purification des variables
	$string_auth=$purifier->purify($_POST['string_auth']);
	$dummy=$purifier->purify($_POST['dummy']);
	$string_auth1=$purifier->purify($_POST['string_auth1']);
	$dummy1=$purifier->purify($_POST['dummy1']);
	$string_auth2=$purifier->purify($_POST['string_auth2']);
	$dummy2=$purifier->purify($_POST['dummy2']);
	$mod_pwd=$purifier->purify($_POST['mod_pwd']);
}

  if ($mod_pwd) {
        // decryptage des mdp
        $old_password = decodekey($string_auth);
        $new_password = decodekey($string_auth1);
        $verif_password = decodekey($string_auth2);
        #DEBUG
        if ($DEBUG) {
                echo "crypto old pass : $string_auth<br />crypto new pass  :  $string_auth1<br />crypto verif pass  : $string_auth2<br />";
                echo "old_mdp : $old_password new mdp  : $new_password verif mdp  : $verif_password<br/>";
        }
  }
  // teste si il faut resservir le formulaire de saisie
  if ( (!$mod_pwd) ||
        (($mod_pwd)&&(!verifPwd($new_password))) ||
        (($mod_pwd)&&($new_password != $verif_password)) ||
        (($mod_pwd)&&(!user_valid_passwd ( $login, $old_password ))) ||
        (($mod_pwd)&&($new_password==$old_password))
     ) {
        header_crypto_html("Modification mot de passe");
    ?>
    <div class="cadrepwd">
        <h3>Changement de votre mot de passe par d&eacute;faut</h3>

        <p class="warn">
            Pour assurer la confidentialit&eacute; de vos donn&eacute;es, vous devez modifier votre mot de passe par d&eacute;faut.
        </p>
      <form name = "auth" action="must_change_default_pwd.php" method="post" onSubmit = "encrypt(document.auth)">
        <table border="0">
          <tr>
            <td>Mot de passe actuel : </td>
            <td>
                    <input type="password" name="dummy" size="8">
                    <input type="hidden" name="string_auth" value="">
            </td>
          </tr>
          <tr>
            <td>Nouveau mot de passe : </td>
            <td>
                    <input type= "password" value="" name="dummy1" size='8'  maxlength='10'>
                    <input type="hidden" name="string_auth1" value="">
            </td>
          </tr>
          <tr>
            <td>Ressaisir nouveau mot de passe : </td>
            <td>
                    <input type= "password" value="" name="dummy2" size='8'  maxlength='10'>
                    <input type="hidden" name="string_auth2" value="">
            </td>
          <tr>
          <tr>
            <td colspan=2 align=center>
              <input type="hidden" name="mod_pwd" value="true">
              <input name="jeton" type="hidden"  value="<?php echo md5($_SESSION['token'].htmlentities($_SERVER['PHP_SELF'])); ?>" />
              <input type="submit" value="Valider">
            </td>
          </tr>
        </table>
      </form>
    <?
    // Affichage logo crypto
    crypto_nav();
    echo "</div>\n";
    // Affichage des erreurs
    if( $mod_pwd )  {
        // Affichage des messages d'alerte
        if (!user_valid_passwd ( $login, $old_password ) ) {
            // Verification de l'ancien mot de passe
            echo gettext("<div class='error_msg'>Votre mot de passe actuel est erron&#233; !</div><br />\n");
        } elseif ( !verifPwd($new_password)  ) {
            // Verification du nouveau mot de passe
            echo gettext("<div class='error_msg'>Vous devez proposer un mot de passe d'une longueur comprise entre 4 et 8 caract&#232;res, compos&eacute; de lettre(s) et de chiffre(s) avec &#233;ventuellement les caract&#232;res sp&#233;ciaux suivants : $char_spec</div><br />\n");
        } elseif ( $new_password != $verif_password ) {
            // Verification de la coherence des deux mots de passe
            echo gettext("<div class='error_msg'>La v&#233;rification de votre nouveau mot de passe a &#233;chou&#233; !</div><br />\n");
        } elseif ( $new_password == $old_password ) {
            // Verification si le nouveau pasword est différent de l'ancien
            echo gettext("<div class='error_msg'>Le nouveau mot de passe doit &ecirc;tre diff&eacute;rent de l'ancien !</div><br />\n");
        }
    }
  } else {
    // Changement du mot de passe
    if ( userChangedPwd($login, $new_password, $old_password) ) {
        // On reposte le cookie LCSuser en cas de succes du changement du mot de passe
        setcookie("LCSuser", xoft_encode( urlencode($new_password) ,$key_priv), 0,"/","",0);
        $html = "<strong>Votre mot de passe a &#233;t&#233; modifi&#233; avec succ&#232;s.</strong><br>\n";
    } else $html = "<div class='error_msg'>Echec de la modification de votre mot de passe, veuillez contacter <A href='mailto:$MelAdminLCS?subject=PB changement mot de passe'>l'administrateur du syst&#232;me</A></div><br />\n";
    header_crypto_html("Modification mot de passe");
    aff_trailer ("5");
    echo $html;
  }
  include ("../lcs/includes/pieds_de_page.inc.php");
?>
