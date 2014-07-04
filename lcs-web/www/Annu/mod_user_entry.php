<?php
/* =============================================
   Projet LCS-SE3
   Consultation/ Gestion de l'annuaire LDAP
   Equipe Tice academie de Caen
   Distribue selon les termes de la licence GPL
   Derniere modification : 23/05/2014
   ============================================= */
include "includes/check-token.php";
if (!check_acces()) exit;

$login=$_SESSION['login'];
include "../lcs/includes/headerauth.inc.php";
include "includes/ldap.inc.php";
include "includes/ihm.inc.php";
include ("../lcs/includes/jlcipher.inc.php");

$user_entry=false;
$html="";
if ( count($_GET)>0 || count($_POST)>0 ) {
  	//configuration objet
 	include ("../lcs/includes/htmlpurifier/library/HTMLPurifier.auto.php");
 	$config = HTMLPurifier_Config::createDefault();
 	$purifier = new HTMLPurifier($config);
    	//purification des variables
	if ( count($_GET)>0) $uid=$purifier->purify($_GET['uid']);
                  if (count($_POST)>0 ) {
                    $uid=$purifier->purify($_POST['uid']);
                    $user_entry=$purifier->purify($_POST['user_entry']);
                    $telephone=$purifier->purify($_POST['telephone']);
                    $nom=$purifier->purify($_POST['nom']);
                    $prenom=$purifier->purify($_POST['prenom']);
                    $description=$purifier->purify($_POST['description']);
                    $userpwd=@$purifier->purify($_POST['userpwd']);
                    $shell=$purifier->purify($_POST['shell']);
                    $password=@$purifier->purify($_POST['password']);
                    $string_auth=$purifier->purify($_POST['string_auth']);
                    $pseudo=$purifier->purify($_POST['pseudo']);
                  }
}

$isadmin=is_admin("Annu_is_admin",$login);
if (($isadmin=="Y") or ((tstclass($login,$uid)==1) and (ldap_get_right("sovajon_is_admin",$login)=="Y"))) {
    // Recuperation des entrees de l'utilisateur a modifier
    $user=people_get_variables ($uid, false);
    // Decryptage du mot de passe
    if ( $user_entry && $string_auth)
        $userpwd = decodekey($string_auth);
    // Modification des entrees
    if ( !$user_entry || ($user_entry && (!verifPseudo($pseudo) || !verifTel($telephone) || !verifEntree($nom) || !verifEntree($prenom) || !verifDescription($description) || ($userpwd && !verifPwd($userpwd)) ) ) ) {
        header_crypto_html("Modification fiche utilisateur");
        aff_trailer ("4");
        ?>
        <form name = "auth" action="mod_user_entry.php" onSubmit = "encrypt(document.auth)" method="post">
        <table align="center" border="0" width="90%">
            <tbody>
            <tr>
                <td width="27%">Login :&nbsp;</td>
                <td width="73%" colspan="2"><tt><strong><?php echo $user[0]["uid"]?></strong></tt></td>
	    </tr>
	    <tr>
                <td width="27%">Pr&#233;nom :&nbsp;</td>
                <td width="73%" colspan="2"><input type="text" name="prenom" value="<?php  echo $user[0]['prenom'];?>" size="20"></td>
	    </tr>
	    <tr>
	      <td>Nom&nbsp;:&nbsp;</td>
	      <td colspan="2"><input type="text" name="nom" value="<?php echo $user[0]["nom"]?>" size="20"></td>
	    </tr>
            <?php if ($isadmin=="Y") {
	    ?>
	    <tr>
	      <td>Adresse m&#233;l&nbsp;:&nbsp;</td>
	      <td colspan="2"><tt><strong><?php echo $user[0]["email"]?></strong></tt></td>
	    </tr>
	    <tr>
	      <td>Pseudo&nbsp;:&nbsp;</td>
	      <td colspan="2"><input type="text" name="pseudo" value="<?php echo $user[0]['pseudo']?>" size="20"></td>
	    </tr>
	    <tr>
	      <td><em>Shell&nbsp;</em> :&nbsp;</td>
	      <td>
                <select name="shell">
                  <option <?php if ($user[0]["shell"] == "/bin/bash") echo "selected" ?>>/bin/bash</option>
                  <option <?php if ($user[0]["shell"] == "/bin/true") echo "selected" ?>>/bin/true</option>
	        </select>
	      </td>
              <td>
                <font color="orange">
                  <u>Attention</u> : Si vous choisissez /bin/bash,
	          &nbsp;cet utilisateur disposera d'un shell sur le serveur Lcs.
                </font>
              </td>
	    </tr>
	    <tr>
	      <td valign="center">Profil :&nbsp;</td>
	      <td valign="bottom" colspan="2"><textarea name="description" rows="2" cols="40"><?php echo $user[0]["description"]; ?></textarea></td>
	    </tr>
	    <tr>
	      <td>T&#233;l&#233;phone :&nbsp;</td>
	      <td colspan="2"><input type="text" name="telephone" value="<?php echo $user[0]['tel']; ?>" size="10"></td>
	    </tr>
	    <?php } else {
	    	echo "<input type=\"hidden\" name=\"pseudo\" value=\"".$user[0]["pseudo"]."\" size=\"20\">";
	       }
	    ?>
	    <tr>
	      <td>Mot de passe :&nbsp;</td>
	      <td>
                        <input type= "password" value="" name="dummy" size='8'  maxlength='10'>
                        <input type="hidden" name="string_auth" value="">
            </td>
            <td>
                <font color="orange">
                  <u>Attention</u> : Si vous laissez ce champ vide,
	          &nbsp;c'est l'ancien mot de passe qui sera conserv&#233;.
                </font>
            </td>
	    </tr>
	    <tr>
	      <td></td>
	      <td align="left">
                 <input name="jeton" type="hidden"  value="<?php echo md5($_SESSION['token'].htmlentities($_SERVER['PHP_SELF'])); ?>" />
                <input type="hidden" name="uid" value="<?php echo $uid ?>">
                <input type="hidden" name="user_entry" value="true">
                <input type="submit" value="Lancer la requ&#234;te">
              </td>
	    </tr>
	  </tbody>
        </table>
      </form>
      <?php
      // Affichage logo crypto
      crypto_nav();
      // Affichage des erreurs
      if ($user_entry) {
        // verification des saisies
        // nom prenom
        if ( !verifEntree($nom) || !verifEntree($prenom) ) {
          echo "<div class=\"error_msg\">Les champs nom et pr&eacute;nom, doivent comporter au minimum 1 et au maximum 20 caract&#232;res compos&eacute; de lettre(s) et de chiffre(s).</div><br />\n";
        }
        //pseudo
        if ( !verifPseudo($pseudo) ) {
          echo "<div class=\"error_msg\">Un pseudo ne peut contenir ni espace, ni virgule, ni anti-slash (\), ni symbole pipe (|).</div><br />\n";
        }
        // profil
        if ( $description && !verifDescription($description) ) {
          echo "<div class=\"error_msg\">Veuillez reformuler le champ description.</div><br />\n";
        }
        // tel
        if ( !verifTel($telephone) ) {
          echo "<div class=\"error_msg\">Le num&#233;ro de t&#233;l&#233;phone que vous avez saisi, n'est pas conforme.</div><br />\n";
        }
        // mot de passe
        if ( $userpwd && !verifPwd($userpwd) ) {
          echo "<div class='error_msg'>
                 Vous devez proposer un mot de passe d'une longueur comprise entre 4 et 8 caract&#232;res compos&eacute; de lettre(s) et de chiffre(s) avec &#233;ventuellement les caract&#232;res sp&#233;ciaux suivants ($char_spec)
                </div><br />\n";
        }
        // fin verification des saisies
      }
    } else {
      // Changement du mot de passe
      if ( $userpwd && verifPwd($userpwd) ) {
        if ( userChangedPwd($uid, $userpwd, '') ) {
          $html = "<strong>Le mot de passe a &#233;t&#233; modifi&#233; avec succ&#232;s.</strong><br>\n";
          if ( $login == $uid )
            // Cas du changement de son propre mot de passe, on reposte le cookie LCSuser
            setcookie("LCSuser", xoft_encode( urlencode($userpwd) ,$key_priv), 0,"/","",0);
        } else
          $html = "<div class='error_msg'>Echec de la modification du mot de passe, veuillez contacter <A HREF='mailto:$MelAdminLCS?subject=PB changement mot de passe'>l'administrateur du syst&#232;me</A></div><br />\n";
      }
      header_crypto_html("Modification fiche utilisateur");
      aff_trailer ("4");
      echo $html;
      // Positionnement des entrees a modifier
      // Nettoyage des accents
      $prenom = ucfirst(mb_strtolower(unac_string_with_space($prenom)));
      $nom = ucfirst(mb_strtolower(unac_string_with_space($nom)));
      $description = ucfirst(mb_strtolower(unac_string_with_space($description)));
      // Nettoyage accents et remplacement espace par underscore
      $pseudo = ucfirst(mb_strtolower(unac_string_with_underscore($pseudo)));

      $entry["sn"] = stripslashes ($nom);
      $entry["cn"] = stripslashes ($prenom." ".$nom);
      $entry["givenname"] = stripslashes ($prenom);

      if($user[0]["gecos"]!="") {
         $tab_gecos=explode(",",$user[0]["gecos"]);
         $entry["gecos"]=ucfirst(mb_strtolower(unac_string_with_underscore($prenom)))." ".ucfirst(mb_strtolower(unac_string_with_underscore($nom))).",".$tab_gecos[1].",".$tab_gecos[2].",".$tab_gecos[3];
      }

      if ( $shell ) {
		$entry["loginshell"] = $shell;
		$dnToModify = "uid=".$uid.",".$dn['people'];
		exec ("$scriptsbinpath/toggleShell.pl ". escapeshellarg($dnToModify) ." ". escapeshellarg($shell));
	}
      if ( $pseudo && verifPseudo($pseudo) )
          $entry["initials"]=$pseudo;
      if ( $telephone && verifTel($telephone) )
          $entry["telephonenumber"]=$telephone ;
      if ( $description && verifDescription($description) ) $entry["description"]=stripslashes($description);

      // Modification des entrees
      $ds = @ldap_connect ( $ldap_server, $ldap_port );
      if ( $ds ) {
          $r = @ldap_bind ( $ds, $adminDn, $adminPw ); // Bind en admin
          if ($r) {
              if (ldap_modify ($ds, "uid=".$uid.",".$dn["people"],$entry)) {
                  if ( $pseudo != $user[0]["pseudo"] ) {
                      // log de la modification dans /var/log/lcs/lcs_pseudo.log
                      if (getenv("HTTP_CLIENT_IP")) $ip = getenv("HTTP_CLIENT_IP");
                        else if (getenv("HTTP_X_FORWARDED_FOR")) $ip = getenv("HTTP_X_FORWARDED_FOR");
                        else if (getenv("REMOTE_ADDR")) $ip = getenv("REMOTE_ADDR");
                        else $ip = "UNKNOWN";
                      $fp=fopen($logpath."pseudo.log","a");
                      if($fp) {
                          fputs($fp,$uid."|".$pseudo."|".date("j/m/y:H:i")."|".$nom." ".$prenom."|".$ip."\n");
                          fclose($fp);
                       } else exit;
                       // fin ecriture fichier de log
                   }
                   echo "<strong>Les entr&#233;es ont &#233;t&#233; modifi&#233;es avec succ&#232;s.</strong><br />\n";
              } else {
                echo "<strong>Echec de la modification, veuillez contacter </strong><A HREF='mailto:$MelAdminLCS?subject=PB modification entrees utilisateur'>l'administrateur du syst&#232;me</A><br />\n";
              }
          }
          @ldap_close ( $ds );
      } else {
          echo "Erreur de connection  &#224;l'annuaire, veuillez contacter </strong><A HREF='mailto:$MelAdminLCS?subject=PB connection a l'annuaire'>l'administrateur du syst&#232;me</A>administrateur<br />\n";
      }
      // Fin modification des entrees
    }
  } else {
    echo "<div class=error_msg>Cette fonctionnalit&#233;, n&#233;cessite les droits d'administrateur de l'annuaire du serveur LCS !</div>";
  }
  include ("../lcs/includes/pieds_de_page.inc.php");
?>
