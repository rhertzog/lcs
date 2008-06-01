<?php
/* =============================================
   Projet LCS : Linux Communication Server
   Consultation de l'annuaire LDAP
   Annu/mod_pwd.php
   [LCS CoreTeam]
   « jLCF >:> » jean-luc.chretien@tice.ac-caen.fr
   « oluve » olivier.le_monnier@crdp.ac-caen.fr
   Equipe Tice académie de Caen
   V 1.4-2 maj : 08/11/2004
   ============================================= */
  include "../lcs/includes/headerauth.inc.php";
  include "includes/ldap.inc.php";
  include "includes/ihm.inc.php";
  include ("../lcs/includes/jlcipher.inc.php");
  $DEBUG="0";

  list ($idpers,$login)= isauth();
  if ($idpers == "0") header("Location:$urlauth");
  #header_crypto_html("Modification mot de passe");
  #aff_trailer ("5");
  if ($mod_pwd) {
        // decryptage des mdp
        exec ("/usr/bin/python ".$basedir."lcs/includes/decode.py '$string_auth'",$Res);
        $new_password = $Res[0];
        exec ("/usr/bin/python ".$basedir."lcs/includes/decode.py '$string_auth1'",$Res1);
        $verif_password = $Res1[0];
        #DEBUG
        if ($DEBUG=="1") {
                echo "crypto new mdp  :  $string_auth<br>crypto verif mdp  : $string_auth1<br>";
                echo "old_mdp : $old_password new mdp  : $new_password verif mdp  : $verif_password<br>";
        }
  }
  // teste si il faut resservir le formulaire de saisie
  if ( (!$mod_pwd) ||
        (($mod_pwd)&&(!verifPwd($new_password))) ||
        (($mod_pwd)&&($new_password != $verif_password)) ||
        (($mod_pwd)&&(!user_valid_passwd ( $login, $old_password )))
     ) {
        header_crypto_html("Modification mot de passe");
        aff_trailer ("5");         
    ?>
      <h3>Changement de mot de passe</h3>
      <form name = "auth" action="mod_pwd.php" method="post" onSubmit = "encrypt(document.auth)">
        <table border="0">
          <tr>
            <td>Mot de passe actuel : </td>
            <td><input type="password" name="old_password" size="8"></td>
          </tr>
          <tr>
            <td>Nouveau mot de passe : </td>
            <td>
                    <input type= "password" value="" name="dummy" size='8'  maxlength='10'>
                    <input type="hidden" name="string_auth" value="">
            </td>
          </tr>
          <tr>
            <td>Ressaisir nouveau mot de passe : </td>
            <td>
                    <input type= "password" value="" name="dummy1" size='8'  maxlength='10'>
                    <input type="hidden" name="string_auth1" value="">
            </td>
          <tr>
          <tr>
            <td colspan=2 align=center>
              <input type="hidden" name="mod_pwd" value="true">
              <input type="submit" value="Valider">
            </td>
          </tr>
        </table>
      </form>
    <?
    // Affichage logo crypto
    crypto_nav();
    // Affichage des erreurs
    if( $mod_pwd )  {
      // Affichage des messages d'alerte
      // Vérification de l'ancien mot de passe
      if (!user_valid_passwd ( $login, $old_password ) ) {
        echo gettext("<div class='error_msg'>Votre mot de passe actuel est erroné !</div><BR>\n");
       }
      // Vérification du nouveau mot de passe
       elseif ( !verifPwd($new_password)  ) {
         echo gettext("<div class='error_msg'>Vous devez proposer un mot de passe d'une longueur comprise entre 4 et 8 caractères alphanumériques avec éventuellement les caractères spéciaux suivants $char_spec</div><BR>\n");
      }
      // Vérification de la cohérence des deux mots de passe
       elseif ( $new_password != $verif_password ) {
        echo gettext("<div class='error_msg'>La vérification de votre nouveau mot de passe a échouée !</div><BR>\n");
      }
    }
  } else {    
    // Changement du mot de passe
    if ( userChangedPwd($login, $new_password) ) {
      // On reposte le cookie LCSuser en cas de succes du changement du mot de passe
      setcookie("LCSuser", xoft_encode( urlencode($new_password) ,$key_priv), 0,"/","",0);
      $html = "<strong>Votre mot de passe a été modifié avec succès.</strong><br>\n";
    } else $html = "<div class='error_msg'>Echec de la modification de votre mot de passe, veuillez contacter <A HREF='mailto:$MelAdminLCS?subject=PB changement mot de passe'>l'administrateur du système</A></div><BR>\n";
    header_crypto_html("Modification mot de passe");
    aff_trailer ("5");    
    echo $html;
  }
  include ("../lcs/includes/pieds_de_page.inc.php");
?>
