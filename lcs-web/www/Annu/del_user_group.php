<?php
/* =============================================
   Projet LCS : Linux Communication Server
   Consultation de l'annuaire LDAP
   Annu/del_user_group.php
   [LCS CoreTeam]
   « jLCF >:> » jean-luc.chretien@tice.ac-caen.fr
   Equipe Tice academie de Caen
    26/05/2009
   ============================================= */
  include "../lcs/includes/headerauth.inc.php";
  include "includes/ldap.inc.php";
  include "includes/ihm.inc.php";

  list ($idpers,$login)= isauth();
  if ($idpers == "0") header("Location:$urlauth");

//modif register
$members=$_POST['members'];
if ( isset($_POST['cn']))  $cn = $_POST['cn'];
elseif ( isset($_GET['cn'])) $cn = $_GET['cn'];
$group_del_user=$_POST['group_del_user'];


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
          <p>S&#233;lectionnez les membres &#224; supprimer :</p>
          <p><select size="5" name="<? echo "members[]"; ?>" multiple="multiple">
              <?
                for ($loop=0; $loop < count($people); $loop++) {
                  echo "<option value=".$people[$loop]["uid"].">".$people[$loop]["fullname"];
                }
              ?>
            </select></p>
            <input type="hidden" name="cn" value="<? echo $cn ?>">
            <input type="hidden" name="group_del_user" value="true">
            <input type="reset" value="R&#233;initialiser la s&#233;lection">
	    <input type="submit" value="Valider">
          </p>
        </form>
        <?
        // Affichage message d'erreur
        if ($group_del_user && !count($members) ) {
          echo "<div class=error_msg>
                  Vous devez s&#233;lectionner au moins un membre &#224; supprimer !
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
                      Les membres s&#233;lectionn&#233;s ont &#233;t&#233; supprim&#233;s du groupe
                      <font color='#0080ff'><A href='group.php?filter=$cn'>$cn</A></font>
                      avec succ&#232;s.
                    </div><br>\n";
        } else {
          echo "<div class=error_msg>
                    Echec, les membres s&#233;lectionn&#233;s n'ont pas &#233;t&#233; supprim&#233; du groupe
                    <font color='#0080ff'>$cn</font>
                    &nbsp;!<BR> (type d'erreur : $ReturnValue), veuillez contacter
                    &nbsp;<A HREF='mailto:$MelAdminLCS?subject=PB creation groupe'>l'administrateur du syst&#232;me</A>
                </div><BR>\n";
        }
      }
    } else {
      echo "<div class=error_msg>La suppression d'un utilisateur des son  groupe principal (Eleves, Profs, Administratifs) n'est pas autoris&#233;e !</div>";
    }
  } else {
    echo "<div class=error_msg>Cette application, n&#233;cessite les droits d'administrateur du serveur LCS !</div>";
  }
  include ("../lcs/includes/pieds_de_page.inc.php");
?>
