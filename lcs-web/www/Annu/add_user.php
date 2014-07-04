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
include "../lcs/includes/jlcipher.inc.php";

header_crypto_html("Creation utilisateur");
aff_trailer ("7");
$userpwd=$naissance=$nom=$prenom=false;
if ( count($_POST)>0 ) {
  	//configuration objet
 	include ("../lcs/includes/htmlpurifier/library/HTMLPurifier.auto.php");
 	$config = HTMLPurifier_Config::createDefault();
 	$purifier = new HTMLPurifier($config);
    	//purification des variables
  	if ( isset($_POST['nom'])) $nom=$purifier->purify($_POST['nom']);
  	if ( isset($_POST['prenom'])) $prenom=$purifier->purify($_POST['prenom']);
  	if ( isset($_POST['naissance'])) $naissance=$purifier->purify($_POST['naissance']);
  	if ( isset($_POST['sexe'])) $sexe=$purifier->purify($_POST['sexe']);
  	if ( isset($_POST['categorie'])) $categorie=$purifier->purify($_POST['categorie']);
  	if ( isset($_POST['add_user'])) $add_user=$purifier->purify($_POST['add_user']);
  	$string_auth=( isset($_POST['string_auth'])) ? $purifier->purify($_POST['string_auth']) :"";
  	$string_auth1=( isset($_POST['string_auth1'])) ? $purifier->purify($_POST['string_auth1']) :"";
  	if ( isset($_POST['dummy'])) $dummy=$purifier->purify($_POST['dummy']);
  	if ( isset($_POST['dummy1'])) $dummy1=$purifier->purify($_POST['dummy1']);
}

  if (is_admin("Annu_is_admin",$login)=="Y") {
	// Decryptage des champs cryptes
	if ( isset($add_user) && (isset($string_auth) || isset($string_auth1)) ) {
	if ($string_auth !="")	$naissance = decodekey($string_auth);
        	if ($string_auth1!="") $userpwd = decodekey($string_auth1);
	}
    // Ajout d'un utilisateur
    if (    ( !$nom || !$prenom )    // absence de nom ou de prenom
         || ( !$naissance && ( !$userpwd || ( $userpwd && !verifPwd($userpwd) ) ) ) // pas de date de naissance et mot de passe absent ou invalide
         || ( $naissance && !verifDateNaissance($naissance) )  // date de naissance invalide
         || ( ($naissance && verifDateNaissance($naissance)) && ($userpwd && !verifPwd($userpwd)) )  // date de naissance mais password invalide
       ) {
      ?>
      <form name="auth" action="add_user.php" method="post" onSubmit="encrypt(document.auth)">
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
                 <input name="jeton" type="hidden"  value="<?php echo md5($_SESSION['token'].htmlentities($_SERVER['PHP_SELF'])); ?>" />
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
        if (isset($add_user)) {
          if ( (!$nom)||(!$prenom)) {
            echo "<div class='error_msg'>Vous devez obligatoirement renseigner les champs : nom, pr&eacute;nom !</div>\n<br />\n";
          } elseif ( !$naissance && !$userpwd ) {
            echo "<div class='error_msg'>
                    Vous devez obligatoirement renseigner un des deux champs ?mot de passe? ou ?date de naissance?.
                  </div>\n<br />\n";
          } else {
            if ( ($userpwd) && !verifPwd($userpwd) ){
              echo "<div class='error_msg'>
                    Vous devez proposer un mot de passe d'une longueur comprise entre 4 et 8 caract&egrave;res
                    compos&eacute; de lettre(s) et de chiffre(s) avec &eacute;ventuellement les caract&egrave;res sp&eacute;ciaux suivants&nbsp;(".$char_spec.")&nbsp;
                    ou &agrave; d&eacute;faut, laisser le champ mot de passe vide et dans ce cas c'est la date de naissance qui sera utilis&eacute;e.
                  </div><br />\n";
            }
            if ( ($naissance) && !verifDateNaissance($naissance) ){
              echo "<div class='error_msg'>
                    Le champ date de naissance doit &ecirc;tre obligatoirement au format
                    Ann&eacute;eMoisJour (YYYYMMDD).
                  </div><br />\n";
            }
          }
        }

    } else {
      // Verification si ce nouvel utilisateur n'existe pas deja
      $cn =stripslashes($prenom." ".$nom);
      $people_exist=search_people("(cn=$cn)");
      if (count($people_exist)) {
        echo "<div class='error_msg'>
                Echec de cr&eacute;ation : L'utilisateur <font color=\"black\"> $cn </font> est d&eacute;ja
                pr&eacute;sent dans l'annuaire.
              </div><br />\n";
      } else {
        // Positionnement de la date de naissance ou du mot de passe par defaut
        if (!$naissance ) $naissance="00000000";
        if (!$userpwd ) $userpwd=$naissance;
	$nom = stripslashes($nom); $prenom = stripslashes($prenom);
        // Creation du nouvel utilisateur
        exec ("$scriptsbinpath/userAdd.pl ". escapeshellarg($prenom) . " ". escapeshellarg($nom) ." ". escapeshellarg($userpwd) ." ". escapeshellarg($naissance) ." ". escapeshellarg($sexe) ." ". escapeshellarg($categorie),$AllOutPut,$ReturnValue);
        ### DEBUG echo" \"$prenom\" \"$nom\" \"$userpwd\" \"$naissance\" \"$sexe\" \"$categorie\"<br />";
        // Compte rendu de creation
        if ($ReturnValue == "0") {
		// Recherche du login de cet utilisateur
		$users = search_people ("(cn=$cn)");
          	echo "L'utilisateur $prenom $nom a &eacute;t&eacute; cr&eacute;&eacute; avec succes";
		if ( count ($users) ) {
			echo ", l'identifiant&nbsp;<b>".$users[0]["uid"]."</b>&nbsp; lui a &eacute;t&eacute; attribu&eacute;";
			echo "<ul><li><a href='add_user_group.php?uid=".$users[0]["uid"]."&jeton=".md5($_SESSION['token'].htmlentities("/Annu/add_user_group.php"))."'>Ajouter &agrave; des groupes...</a></ul>\n";
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
