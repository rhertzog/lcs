<?php
/* =============================================
   Projet LCS : Linux Communication Server
   Consultation de l'annuaire LDAP
   Annu/mod_entry.php
   [LCS CoreTeam]
   « jLCF >:> » jean-luc.chretien@tice.ac-caen.fr
   « oluve » olivier.le_monnier@crdp.ac-caen.fr
   Equipe Tice académie de Caen
   V 1.3 maj : 21/11/2003
   ============================================= */
  include "../lcs/includes/headerauth.inc.php";
  include "includes/ldap.inc.php";
  include "includes/ihm.inc.php";

  list ($idpers,$login)= isauth();
  if ($idpers == "0") header("Location:$urlauth");

  // Recuperation des entrees de l'utilisateur a modifier
  $people_attr=people_get_variables ($login, false);
  $people_attr[0]["prenom"]=getprenom($people_attr[0]["fullname"],$people_attr[0]["nom"]);
  if (is_admin("Annu_is_admin",$login)=="Y") {
    // Redirection vers mod_user_entry.php
    header("Location:mod_user_entry.php?uid=$login");
  } else {
    header_html();
    aff_trailer ("4");
    // Changement uniquement du pseudo pour l'utilisateur de «base»
      if ( (!$mod_entry) || ( $mod_entry && ( !$pseudo || !verifPseudo($pseudo) ) ) ) {
      ?>
      <form action="mod_entry.php">
        <table border="0" width="90%" align="center">
	  <tbody>
	    <tr>
	      <td width="30%" >Nom :</td>
	      <td width="20%"><strong><? echo $people_attr[0]["nom"] ?></strong></td>
              <td></td>
            </tr>
	    <tr>
	      <td>Prénom :</td>
	      <td><strong><? echo $people_attr[0]["prenom"] ?></strong></td>
	      <td></td>
	    </tr>
	    <tr>
	      <td>Adresse mèl :</td>
	      <td colspan="2"><tt><strong><? echo $people_attr[0]["email"] ?></strong></tt></td>
	      <!--td></td-->
	    </tr>
	    <tr>
	      <td>Pseudo :</td>
	      <td><input type="text" name="pseudo" value="<? echo $people_attr[0]["pseudo"] ?>"size="20"></td>
	      <td>
                <font color="orange">
                <u>Attention</u> : toutes les modifications apportées
	        &nbsp;à votre pseudo sont mémorisées et accessibles
                &nbsp;par l'administrateur du système Lcs.
                </font>
              </td>
	    </tr>
	    <tr>
	      <td></td>
              <td >
                <input type="hidden" name="mod_entry" value="true">
                <input type="submit" value="Lancer la requête">
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
          $entry["givenname"]=utf8_encode($pseudo);
          if (@ldap_modify ($ds, "uid=".$people_attr[0]["uid"].",".$dn["people"],$entry)) {
            // log de la modification dans /var/log/lcs/lcs_pseudo.log
            $fp=fopen($logpath."pseudo.log","a");
            if($fp) {
              fputs($fp,$people_attr[0]["uid"]."|".$pseudo."|".date("j/m/y:H:i")."|".$people_attr[0]["fullname"]."|".$REMOTE_ADDR."\n");
              fclose($fp);
            } else {exit;}
            // fin ecriture fichier de log
            echo "<strong>La modification de votre pseudo en <font color=\"orange\">".$pseudo."</font> a réussie.</strong><BR>\n";
          } else {
            echo "<strong>Echec de la modification, veuillez contacter </strong><A HREF='mailto:$MelAdminLCS?subject=PB changement pseudo'>l'administrateur du système</A><BR>\n";
          }
        }
        @ldap_close ( $ds );
      } else {
        echo "Erreur de connection à l'annuaire, veuillez contacter </strong><A HREF='mailto:$MelAdminLCS?subject=PB connection a l'annuaire'>l'administrateur du système</A>administrateur<BR>\n";
      }
      // Fin modification pseudo
    }
  }

  include ("../lcs/includes/pieds_de_page.inc.php");
?>
