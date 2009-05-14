<?php
/* =============================================
   Projet LCS : Linux Communication Server
   Consultation de l'annuaire LDAP
   Annu/add_user.php
   [LCS CoreTeam]
   « jLCF >:> » jean-luc.chretien@tice.ac-caen.fr
   Equipe Tice academie de Caen
   derniere mise a jour : 14 Mai 2009
   ============================================= */
  include "../lcs/includes/headerauth.inc.php";
  include "includes/ldap.inc.php";
  include "includes/ihm.inc.php";
  include "../lcs/includes/jlcipher.inc.php";

  list ($idpers,$login)= isauth();
  if ($idpers == "0") header("Location:$urlauth");

  header_crypto_html("Creation utilisateur");
  aff_trailer ("7");
  // register global
  $nom=$_POST['nom'];
  $prenom=$_POST['prenom'];
  $naissance=$_POST['naissance'];
  $sexe=$_POST['sexe'];
  $categorie=$_POST['categorie'];
  $add_user=$_POST['add_user'];
  $string_auth=$_POST['string_auth'];
  $string_auth1=$_POST['string_auth1'];
  $dummy=$_POST['dummy'];
  $dummy1=$_POST['dummy1'];

  if (is_admin("Annu_is_admin",$login)=="Y") {
    // Decryptage des champs cryptes
     if ( $add_user && ($string_auth || $string_auth1) ) {
	    exec ("/usr/bin/python ".$basedir."lcs/includes/decode.py '$string_auth'",$Res);
        $naissance = $Res[0];
        exec ("/usr/bin/python ".$basedir."lcs/includes/decode.py '$string_auth1'",$Res1);
        $userpwd = $Res1[0];
     }
    // Ajout d'un utilisateur
    if (    ( !$nom || !$prenom )    // absence de nom ou de prenom
         || ( !$naissance && ( !$userpwd || ( $userpwd && !verifPwd($userpwd) ) ) ) // pas de date de naissance et mot de passe absent ou invalide
         || ( $naissance && !verifDateNaissance($naissance) )  // date de naissance invalide
         || ( ($naissance && verifDateNaissance($naissance)) && ($userpwd && !verifPwd($userpwd)) )  // date de naissance mais password invalide
       ) {
      ?>
      <form name = "auth" action="add_user.php" onSubmit = "encrypt(document.auth)">
        <table border="0">
          <tbody>
            <tr>
              <td>Nom :</td>
              <td colspan="2" valign="top"><input type="sn" name="nom" value="<? echo $nom ?>" size="20"></td>

            </tr>
            <tr>
              <td>Pr&eacute;nom :</td>
              <td colspan="2" valign="top"><input type="cn" name="prenom" value="<? echo $prenom ?>" size="20"></td>

            </tr>
            <tr>
              <td>Date de naissance :</td>
              <td>
			  		<input type="texte" name="dummy" value="<? echo $naissance ?>" size="8">
					<input type="hidden" name="string_auth" value="">
			  </td>
              <td>
                <font color="orange">
                  &nbsp;(YYYYMMDD) ce champ est optionnel mais si il n'est pas renseign&eacute;, le champ mot de passe est obligatoire.
                </font>
              </td>
            </tr>
            <tr>
              <td>Mot de passe :</td>
              <td>
					<input type= "password" value="" name="dummy1" size='8'  maxlength='8'>
					<input type="hidden" name="string_auth1" value="">
		      </td>
              <td>
                <font color="orange">
                  &nbsp;Si le champ mot de passe est laiss&eacute; vide, c'est la date de naissance qui sera utilis&eacute;e.
                </font>
              </td>
            </tr>
            <tr>
              <td>Sexe :</td>
              <td colspan="2">
                <img src="images/eleve_f_bleu.jpg" alt="F&eacute;minin" width="24" height="24" hspace="4" border="0">
                <?php
                  echo "<input type=\"radio\" name=\"sexe\" value=\"F\"";
                  if (($sexe=="F")||(!$add_user)) echo " checked";
                  echo ">&nbsp;\n";
                ?>
                <img src="images/eleve_g_bleu.jpg" alt="Masculin" width=24 height=24 hspace=4 border=0>
                <?php
                  echo "<input type=\"radio\" name=\"sexe\" value=\"M\"";
                  if ($sexe=="M") echo " checked";
                  echo ">&nbsp;\n";
                ?>
              </td>
            </tr>
            <tr>
              <td>Groupe Principal</td>
              <td colspan="2" valign="top">
                <select name="categorie">
                  <?php
                    echo "<option value=\"Eleves\"";
                    if ($categorie  == "Eleves" ) echo "SELECTED";
                    echo ">El&egrave;ves</option>\n";
                    echo "<option value=\"Profs\"";
                    if ($categorie  == "Profs" ) echo "SELECTED";
                    echo ">Profs</option>\n";
                    echo "<option value=\"Administratifs\"";
                    if ($categorie  == "Administratifs" ) echo "SELECTED";
                    echo ">Administratifs</option>\n";
                  ?>
                </select>
              </td>
            </tr>
            <tr>
              <td></td>
              <td></td>
	      <td >
                <input type="hidden" name="add_user" value="true">
                <input type="submit" value="Lancer la requ&ecirc;te">
              </td>
            </tr>
          </tbody>
        </table>
      </form>
      <?php
	     // Affichage logo crypto
        crypto_nav();
        if ($add_user) {
          if ( (!$nom)||(!$prenom)) {
            echo "<div class='error_msg'>Vous devez obligatoirement renseigner les champs : nom, pr&eacute;nom !</div>\n<br />\n";
          } elseif ( !$naissance && !$userpwd ) {
            echo "<div class='error_msg'>
                    Vous devez obligatoirement renseigner un des deux champs «mot de passe» ou «date de naissance».
                  </div>\n<br />\n";
          } else {
            if ( ($userpwd) && !verifPwd($userpwd) ){
              echo "<div class='error_msg'>
                    Vous devez proposer un mot de passe d'une longueur comprise entre 4 et 8 caract&egrave;res
                    alphanum&eacute;riques avec &eacute;ventuellement les caract&egrave;res sp&eacute;ciaux suivants&nbsp;(".$char_spec.")&nbsp;
                    ou à d&eacute;faut laisser le champ mot de passe vide et dans ce cas c'est la date de naissance
                    qui sera utilis&eacute;e.
                  </div><BR>\n";
            }
            if ( ($naissance) && !verifDateNaissance($naissance) ){
              echo "<div class='error_msg'>
                    Le champ date de naissance doit &ecirc;tre obligatoirement au format
                    Ann&eacute;eMoisJour (YYYYMMDD).
                  </div><BR>\n";
            }
          }
        }

    } else {
      // Verification si ce nouvel utilisateur n'existe pas deja
      $cn =stripslashes( utf8_encode($prenom." ".$nom) );
      $people_exist=search_people("(cn=$cn)");
      if (count($people_exist)) {
        echo "<div class='error_msg'>
                Echec de cr&eacute;ation : L'utilisateur <font color=\"black\"> $cn </font> est d&eacute;ja
                pr&eacute;sent dans l'annuaire.
              </div><BR>\n";
      } else {
        // Positionnement de la date de naissance ou du mot de passe par defaut
        if (!$naissance ) $naissance="00000000";
        if (!$userpwd ) $userpwd=$naissance;
	$nom = stripslashes($nom); $prenom = stripslashes($prenom);
        // Creation du nouvel utilisateur
        exec ("$scriptsbinpath/userAdd.pl \"$prenom\" \"$nom\" \"$userpwd\" \"$naissance\" \"$sexe\" \"$categorie\"",$AllOutPut,$ReturnValue);
        ### DEBUG echo" \"$prenom\" \"$nom\" \"$userpwd\" \"$naissance\" \"$sexe\" \"$categorie\"<BR>";
        // Compte rendu de cr&eacute;ation
        if ($ReturnValue == "0") {
		// Recherche du login de cet utilisateur
		$users = search_people ("(cn=$cn)");
          	echo "L'utilisateur $prenom $nom a &eacute;t&eacute; cr&eacute;&eacute; avec succes";
		if ( count ($users) ) {
			echo ", l'identifiant&nbsp;<b>".$users[0]["uid"]."</b>&nbsp; lui a &eacute;t&eacute; attribu&eacute;";
			echo "<ul><li><a href='add_user_group.php?uid=".$users[0]["uid"]."'>Ajouter à des groupes...</a></ul>\n";
		}
		echo "<br />\n";
        } else {
          echo "<div class='error_msg'>
                  Erreur lors de la cr&eacute;ation du nouvel utilisateur $prenom $nom
                  <font color='black'>
                    (type d'erreur : $ReturnValue)
                  </font>, veuillez contacter
                  <a href='mailto:$MelAdminLCS?subject=PB creation nouvel utilisateur LCS'>l'administrateur du syst&egrave;me</a>
                </div>\n<br />\n";
        }
      }
    }
  } else {
    echo "<div class='error_msg'>Cette application, n&eacute;cessite les droits d'administrateur du serveur LCS !</div>";
  }

  include ("../lcs/includes/pieds_de_page.inc.php");
?>
