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

include "../lcs/includes/headerauth.inc.php";
include "includes/ldap.inc.php";
include "includes/ihm.inc.php";

if (count($_POST)>0) {
	//configuration objet
 	include ("../lcs/includes/htmlpurifier/library/HTMLPurifier.auto.php");
 	$config = HTMLPurifier_Config::createDefault();
 	$purifier = new HTMLPurifier($config);
    //purification des variables
  	$pseudo=$purifier->purify($_POST['pseudo']);
  	$telephone=$purifier->purify($_POST['telephone']);
  	$mod_entry=$purifier->purify($_POST['mod_entry']);
}


  $login=$_SESSION['login'];
  $jeton_mod_user=md5($_SESSION['token'].htmlentities("/Annu/mod_user_entry.php"));

  // Recuperation des entrees de l'utilisateur a modifier
  $people_attr=people_get_variables ($login, false);
  $people_attr[0]["prenom"]=getprenom($people_attr[0]["fullname"],$people_attr[0]["nom"]);
  if (is_admin("Annu_is_admin",$login)=="Y") {
    // Redirection vers mod_user_entry.php
    header("Location:mod_user_entry.php?uid=$login&jeton=$jeton_mod_user");exit;
  } else {
    header_html();
    aff_trailer ("4");
    // Changement uniquement du pseudo pour l'utilisateur de �base�
      if ( (!$mod_entry) || ( $mod_entry && ( !$pseudo || !verifPseudo($pseudo) ) ) ) {
      ?>
      <form action="mod_entry.php" method="post">
        <table border="0" width="90%" align="center">
	  <tbody>
	    <tr>
	      <td width="30%" >Nom :</td>
	      <td width="20%"><strong><?php echo $people_attr[0]["nom"] ?></strong></td>
              <td></td>
            </tr>
	    <tr>
	      <td>Pr&#233;nom :</td>
	      <td><strong><?php echo $people_attr[0]["prenom"] ?></strong></td>
	      <td></td>
	    </tr>
	    <tr>
	      <td>Adresse mail :</td>
	      <td colspan="2"><tt><strong><?php echo $people_attr[0]["email"] ?></strong></tt></td>
	      <!--td></td-->
	    </tr>
	    <tr>
	      <td>Pseudo :</td>
	      <td><input type="text" name="pseudo" value="<?php echo $people_attr[0]["pseudo"] ?>"size="20"></td>
	      <td>
                <font color="orange">
                <u>Attention</u> : toutes les modifications apport&#233;es
	        &nbsp;&#224; votre pseudo sont m&#233;moris&#233;es et accessibles
                &nbsp;par l'administrateur du syst&#232;me Lcs.
                </font>
              </td>
	    </tr>
	    <tr>
	       <td><?php echo gettext("T&#233;l&#233;phone"); ?> :&nbsp;</td>
	       <td colspan="2"><input type="text" name="telephone" value="<?php echo $people_attr[0]["tel"] ?>" size="10"></td>
	    </tr>
	    <tr>
	      <td></td>
              <td >
                  <input name="jeton" type="hidden"  value="<?php echo md5($_SESSION['token'].htmlentities($_SERVER['PHP_SELF'])); ?>" />
                <input type="hidden" name="mod_entry" value="true">
                <input type="submit" value="Lancer la requ&#234;te">
              </td>
	      <td></td>

	    </tr>
	  </tbody>
        </table>
      </form>
      <?php
      if ( (!verifPseudo($pseudo))&&($mod_entry) ) {
        echo "<div class=\"error_msg\">Un pseudo ne peut contenir ni espace, ni virgule, ni anti-slash (\), ni symbole pipe (|).</div><BR>\n";
      }
    } else {
      // Modification pseudo
      $ds = @ldap_connect ( $ldap_server, $ldap_port );
      if ( $ds ) {
        $r = @ldap_bind ( $ds, $adminDn, $adminPw ); // Bind en admin
        if ($r) {
          $pseudo = ucfirst(mb_strtolower(unac_string_with_underscore($pseudo)));
          $entry["initials"]=$pseudo;
          if ( $telephone && verifTel($telephone) )
            $entry["telephonenumber"]=$telephone ;
          if (@ldap_modify ($ds, "uid=".$people_attr[0]["uid"].",".$dn["people"],$entry)) {
            // log de la modification dans /var/log/lcs/lcs_pseudo.log
            $fp=fopen($logpath."pseudo.log","a");
            if($fp) {
              fputs($fp,$people_attr[0]["uid"]."|".$pseudo."|".date("j/m/y:H:i")."|".$people_attr[0]["fullname"]."|".$REMOTE_ADDR."\n");
              fclose($fp);
            } else {exit;}
            // fin ecriture fichier de log
            echo "<strong>La modification de votre pseudo en <font color=\"orange\">".$pseudo."</font> a r&#233;ussi.</strong><BR>\n";
          } else {
            echo "<strong>Echec de la modification, veuillez contacter </strong><A HREF='mailto:$MelAdminLCS?subject=PB changement pseudo'>l'administrateur du syst&#232;me</A><BR>\n";
          }
        }
        @ldap_close ( $ds );
      } else {
        echo "Erreur de connection &#224; l'annuaire, veuillez contacter </strong><A HREF='mailto:$MelAdminLCS?subject=PB connection a l'annuaire'>l'administrateur du syst&#232;me</A>administrateur<BR>\n";
      }
      // Fin modification pseudo
    }
  }

  include ("../lcs/includes/pieds_de_page.inc.php");
?>
