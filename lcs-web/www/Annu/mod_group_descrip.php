<?php
/* =============================================
   Projet LCS-SE3
   Consultation de l'annuaire LDAP
   Annu/Search.php
   � jLCF >:> � jean-luc.chretien@tice.ac-caen.fr
   � oluve � olivier.le_monnier@crdp.ac-caen.fr
   � wawa �  olivier.lecluse@crdp.ac-caen.fr
   Equipe Tice acad�mie de Caen
   V 1.3 maj : 10/10/2003
   Distribu� selon les termes de la licence GPL
   ============================================= */
  include "../lcs/includes/headerauth.inc.php";
  include "includes/ldap.inc.php";
  include "includes/ihm.inc.php";

  list ($idpers,$login)= isauth();
  if ($idpers == "0") header("Location:$urlauth");

  header_html();
  aff_trailer ("3");
  if (is_admin("Annu_is_admin",$login)=="Y") {
    $group=search_groups("cn=".$cn);
    if (
         (!$mod_descrip) ||
         ( $mod_descrip && (!$description || !verifDescription($description)) )
       ) {
      echo "Modification de la description du groupe : <b>".$group[0]["cn"]."</b>\n";
      ?>
      <form action="mod_group_descrip.php" method="post">
        <table border="0" width="90%" align="center">
          <tbody>
	    <tr>
	      <td>Description :</td>
	      <td width="73%" colspan="2"><input type="text" name="description" value="<?php echo $group[0]["description"] ?>" size="60"></td>
	      <td></td>
	    </tr>
	      <td align="left">
                <input type="hidden" name="cn" value="<? echo $cn ?>">
                <input type="hidden" name="mod_descrip" value="true">
                <input type="submit" value="Lancer la requ�te">
              </td>
	    </tr>
	  </tbody>
        </table>
      </form>
      <?
      if ( $mod_descrip ) {
        if ( !$description ) {
          echo "<div class=\"error_msg\">Vous devez saisir une description pour ce groupe !</div><BR>\n";
        } elseif (!verifDescription($description)) {
          echo "<div class=error_msg>Le champ description comporte des caract�res interdits !</div><br>\n";
        }
      }
    } else {
      #DEBUG
      #echo "Debug : ".$group[0]["cn"]." ".$description."<BR>\n";
      $entry["description"]=utf8_encode(stripslashes($description));
      // Modification de la description
      $ds = @ldap_connect ( $ldap_server, $ldap_port );
      if ( $ds ) {
        $r = @ldap_bind ( $ds, $adminDn, $adminPw ); // Bind en admin
        if ($r) {
          if (@ldap_modify ($ds, "cn=".$group[0]["cn"].",".$dn["groups"],$entry)) {

            echo "La description du groupe&nbsp;<strong>".$group[0]["cn"]."</strong>&nbsp;� �t� modifi�e avec succ�s.</br>\n";
            echo "<u>Nouvelle description</u> :&nbsp;".stripslashes($description)."<BR>\n";

          } else {
            echo "<strong>Echec de la modification du groupe".$group[0]["cn"].", veuillez contacter </strong><A HREF='mailto:$MelAdminLCS?subject=PB modification de la description d'un groupe>l'administrateur du syst�me</A><BR>\n";
          }
        }
        @ldap_close ( $ds );
      } else {
        echo "Erreur de connection � l'annuaire, veuillez contacter </strong><A HREF='mailto:$MelAdminLCS?subject=PB connection a l'annuaire'>l'administrateur du syst�me</A>administrateur<BR>\n";
      }
    }

  } else {
    echo "<div class=error_msg>Cette fonctionnalit�, n�cessite les droits d'administrateur du serveur LCS !</div>";
  }

  include ("../lcs/includes/pieds_de_page.inc.php");
?>
