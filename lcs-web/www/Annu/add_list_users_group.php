<?php
/* =============================================
   Projet LCS : Linux Communication Server
   Consultation de l'annuaire LDAP
   Annu/add_list_users_group.php
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

    if ( !$add_list_users_group ) {
      echo "<H4>Ajouter des membres au groupe : $cn</H4>\n";
      // cas d'un groupe de type Equipe
      if ( ereg ("Equipe_", $cn) ) {
        // Recherche de la liste des uid  des membres de ce groupe
        $uids_act = search_uids ("(cn=$cn)","half");
        // Reherche de la liste des professeurs
        $uids_profs = search_uids ("(cn=Profs)","half");
        // Constitution d'un tableau excluant les membres actuels
        $k=0;
        for ($i=0; $i < count($uids_profs); $i++ ) {
            for ($j=0; $j < count($uids_act); $j++ ) {
              if ( $uids_profs[$i]["uid"] == $uids_act[$j]["uid"] )  {
                $exist = true;
                break;
              } else { $exist = false; }
            }
            if (!$exist) {
              $uids_new_members[$k]["uid"] = $uids_profs[$i]["uid"];
              $k++;
            }
        }
         $people_new_members=search_people_groups ($uids_new_members,"(sn=*)","cat");
      } elseif   ( ereg ("Classe_", $cn) ) {
        // Recherche de la liste des Eleves appartenant a une classe
        $uids_eleves_classes =   search_uids ("(cn=Classe_*)", "half");
        ##DEBUG
        #echo "Eleves Classes>".  count($uids_eleves_classes)."<BR>";
        #for ($i=0; $i < count($uids_eleves_classes ); $i++ ) {
        #echo $uids_eleves_classes[$i]["uid"]."<BR>";
        #}
        ##DEBUG
        // Recherche de la liste des Eleves
        $uids_eleves = search_uids ("(cn=Eleves)", "half");
        ##DEBUG
        #echo "Eleves >".  count($uids_eleves)."<BR>";
        #for ($i=0; $i < count($uids_eleves); $i++ ) {
        #echo $uids_eleves[$i]["uid"]."<BR>";
        #}
        ##DEBUG
        // Recherche des Eleves qui ne sont pas affectés à une classe
        $k=0;
        for ($i=0; $i < count($uids_eleves); $i++ ) {
          $affect = false;
          for ($j=0; $j < count($uids_eleves_classes); $j++ ) {
            if ( $uids_eleves[$i]["uid"] == $uids_eleves_classes[$j]["uid"] ) {
              $affect = true;
              break;
            }
          }
            if ($affect==false )  {
                $uids_eleves_no_affect[$k]["uid"]=$uids_eleves[$i]["uid"];
                $k++;
            }
        }
        $people_new_members = search_people_groups ($uids_eleves_no_affect,"(sn=*)","cat");
        ##DEBUG
        #echo "---->".  count($uids_eleves_no_affect)."<BR>";
        #for ($i=0; $i < count($uids_eleves_no_affect); $i++ ) {
        # echo $uids_eleves_no_affect[$i]["uid"]."<BR>";
        # echo $people_new_members[$i]["fullname"]."<BR>";
        #}
        ##DEBUG
      }
      // Affichage de la liste dans une boite de sélection
      if   ( count($people_new_members)>15) $size=15; else $size=count($people_new_members);
      if ( count($people_new_members)>0) {
        $form = "<form action=\"add_list_users_group.php\" method=\"post\">\n";
        $form.="<p>Sélectionnez les membres à ajouter au groupe :</p>\n";
        $form.="<p><select size=\"".$size."\" name=\"new_uids[]\" multiple=\"multiple\">\n";
        echo $form;
        for ($loop=0; $loop < count($people_new_members); $loop++) {
          echo "<option value=".$people_new_members[$loop]["uid"].">".$people_new_members[$loop]["fullname"];
         }
        $form="</select></p>\n";
        $form.="<input type=\"hidden\" name=\"cn\" value=\"$cn\">\n";
        $form.="<input type=\"hidden\" name=\"add_list_users_group\" value=\"true\">\n";
        $form.="<input type=\"reset\" value=\"Réinitialiser la sélection\">\n";
        $form.="<input type=\"submit\" value=\"Valider\">\n";
        $form.="</form>\n";
        echo $form;
      } else {
        echo "<font color=\"orange\">Vous ne pouvez pas ajouter d'élèves car il n'existe plus d'élèves non affectés à des classes !!</font><BR>";
      }
    }   else {
      // Ajout des membres au groupe
       echo "<H4>Ajout des membres au groupe : <A href=\"group.php?filter=$cn\">$cn</A></H4>\n";
       for ($loop=0; $loop < count ($new_uids) ; $loop++) {
          exec("$scriptsbinpath/groupAddUser.pl  $new_uids[$loop] $cn" ,$AllOutPut,$ReturnValue);
          echo  "Ajout de l'utilisateur&nbsp;".$new_uids[$loop]."&nbsp;";
          if ($ReturnValue == 0 ) {
            echo "<strong>Réussi</strong><BR>";
          } else { echo "</strong><font color=\"orange\">Echec</font></strong><BR>"; $err++; }
       }
    }
  } else {
    echo "<div class=error_msg>Cette application, nécessite les droits d'administrateur du serveur LCS !</div>";
  }
  include ("../lcs/includes/pieds_de_page.inc.php");
?>
