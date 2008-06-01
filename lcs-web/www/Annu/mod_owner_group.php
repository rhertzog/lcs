<?php
/* =============================================
   Projet LCS : Linux Communication Server
   Consultation de l'annuaire LDAP
   Annu/mod_owner_group.php
   [LCS CoreTeam]
   « jLCF >:> » jean-luc.chretien@tice.ac-caen.fr
   « oluve » olivier.le_monnier@crdp.ac-caen.fr
   Equipe Tice académie de Caen
   V 1.3 maj : 10/10/03
   ============================================= */
  include "../lcs/includes/headerauth.inc.php";
  include "includes/ldap.inc.php";
  include "includes/ihm.inc.php";

  list ($idpers,$login)= isauth();
  if ($idpers == "0") header("Location:$urlauth");

  header_html();
  aff_trailer ("3");
  if (is_admin("Annu_is_admin",$login)=="Y") {
    $uids = search_uids ("(cn=".$cn.")", "half");
    $people = search_people_groups ($uids,"(sn=*)","cat");
    if ( $owner ) {
      echo "<h4>Réaffectation du professeur principal de l'$cn</h4>";
    } else {
      echo "<h4>Affectation du professeur principal de l'$cn</h4>";
    }
    if ( !$mod_owner_group || !$new_owner ) {
      ?>
        <form action="mod_owner_group.php" method="post">
          <p>Sélectionnez le professeur principal :</p>
          <p><select size="5" name="<? echo "new_owner"; ?>">
              <?
                for ($loop=0; $loop < count($people); $loop++) {
                  if ( $owner != $people[$loop]["uid"] ) {
                    echo "<option value=".$people[$loop]["uid"].">".$people[$loop]["fullname"];
                  }
                }
              ?>
            </select></p>
            <input type="hidden" name="owner" value="<? echo $owner ?>">
            <input type="hidden" name="cn" value="<? echo $cn ?>">
            <input type="hidden" name="mod_owner_group" value="true">
            <input type="reset" value="Réinitialiser la sélection">
	    <input type="submit" value="Valider">
        </form>
      <?
      if ( $mod_owner_group && !$new_owner ) {
          echo "<div class=error_msg>
                  Vous devez sélectionner un professeur principal !
                </div>\n";
      }
    } else {

      // Positionnement de l'entrée a modifier
      $entry["owner"] = "uid=".$new_owner.",".$dn["people"];
      // if ($owner ) {
      // Reaffectation de l'entree owner
      $ds = @ldap_connect ( $ldap_server, $ldap_port );
      if ( $ds ) {
        $r = @ldap_bind ( $ds, $adminDn, $adminPw ); // Bind en admin
        if ($r) {
          if (@ldap_modify ($ds, "cn=".$cn.",".$dn["groups"],$entry)) {
            if ( $owner ) {
              echo "<strong>Le professeur principal a été réaffecté avec succès.</strong><BR>\n";
            } else {
              echo "<strong>Le professeur principal a été affecté avec succès.</strong><BR>\n";
            }
          } else {
            if ( $owner ) {
              echo "<strong>Echec de la réaffectation, veuillez contacter </strong><A HREF='mailto:$MelAdminLCS?subject=PB reaffectation professeur principal'>l'administrateur du système</A><BR>\n";
            } else {
              echo "<strong>Echec de l'affectation, veuillez contacter </strong><A HREF='mailto:$MelAdminLCS?subject=PB affectation professeur principal'>l'administrateur du système</A><BR>\n";
            }
          }
        }
        @ldap_close ( $ds );
      } else {
        echo "Erreur de connection à l'annuaire, veuillez contacter </strong><A HREF='mailto:$MelAdminLCS?subject=PB connection a l'annuaire'>l'administrateur du système</A>administrateur<BR>\n";
      }
    }
  } else {
    echo "<div class=error_msg>Cette application, nécessite les droits d'administrateur du serveur LCS !</div>";
  }
  include ("../lcs/includes/pieds_de_page.inc.php");
?>
