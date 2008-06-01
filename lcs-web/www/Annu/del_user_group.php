<?php
/* =============================================
   Projet LCS : Linux Communication Server
   Consultation de l'annuaire LDAP
   Annu/del_user_group.php
   [LCS CoreTeam]
   « jLCF >:> » jean-luc.chretien@tice.ac-caen.fr
   « oluve » olivier.le_monnier@crdp.ac-caen.fr
   Equipe Tice académie de Caen
   V 1.3 maj : 10/10/2003
   ============================================= */
  include "../lcs/includes/headerauth.inc.php";
  include "includes/ldap.inc.php";
  include "includes/ihm.inc.php";

  list ($idpers,$login)= isauth();
  if ($idpers == "0") header("Location:$urlauth");

  header_html();
  aff_trailer ("31");
  if (is_admin("Annu_is_admin",$login)=="Y") {
    if ( $cn !="Eleves" && $cn !="Profs" && $cn !="Administratifs" ) {
      $uids = search_uids ("(cn=".$cn.")", "half");
      $people = search_people_groups ($uids,"(sn=*)","cat");
      echo "<h4>Modification des membres du groupe $cn</h4>\n";
      if ( !$group_del_user || ( $group_del_user && !count($members) ) ) {
        ?>
        <form action="del_user_group.php" method="post">
          <p>Sélectionnez les membres à supprimer :</p>
          <p><select size="5" name="<? echo "members[]"; ?>" multiple="multiple">
              <?
                for ($loop=0; $loop < count($people); $loop++) {
                  echo "<option value=".$people[$loop]["uid"].">".$people[$loop]["fullname"];
                }
              ?>
            </select></p>
            <input type="hidden" name="cn" value="<? echo $cn ?>">
            <input type="hidden" name="group_del_user" value="true">
            <input type="reset" value="Réinitialiser la sélection">
	    <input type="submit" value="Valider">
          </p>
        </form>
        <?
        // Affichage message d'erreur
        if ($group_del_user && !count($members) ) {
          echo "<div class=error_msg>
                  Vous devez sélectionner au moins un membre à supprimer !
                </div>\n";
        }
      } else {
        // suppression des utilisateurs selectionnes
        for ($loop=0; $loop < count($members); $loop++  ) {
          exec ("$scriptsbinpath/groupDelUser.pl $members[$loop] $cn",$AllOutPut,$ReturnValue);
          $ReturnCode =  $ReturnCode + $ReturnValue;
        }
        // Compte rendu de suppression
        if ($ReturnCode == "0") {
          echo "<div class=error_msg>
                      Les membres sélectionnés ont été supprimé du groupe
                      <font color='#0080ff'><A href='group.php?filter=$cn'>$cn</A></font>
                      avec succès.
                    </div><br>\n";
        } else {
          echo "<div class=error_msg>
                    Echec, les membres sélectionnés n'ont pas été supprimé du groupe
                    <font color='#0080ff'>$cn</font>
                    &nbsp;!<BR> (type d'erreur : $ReturnValue), veuillez contacter
                    &nbsp;<A HREF='mailto:$MelAdminLCS?subject=PB creation groupe'>l'administrateur du système</A>
                </div><BR>\n";
        }
      }
    } else {
      echo "<div class=error_msg>La suppression d'un utilisateur des son  groupe principal (Eleves, Profs, Administratifs) n'est pas autorisée !</div>";
    }
  } else {
    echo "<div class=error_msg>Cette application, nécessite les droits d'administrateur du serveur LCS !</div>";
  }
  include ("../lcs/includes/pieds_de_page.inc.php");
?>
