<?php
/* =============================================
   Projet LCS : Linux Communication Server
   Consultation de l'annuaire LDAP
   Annu/userdirphptype.php
   Equipe Tice academie de Caen
   Derniere mise a jour : 09/10/2008
   ============================================= */
  include "../lcs/includes/headerauth.inc.php";
  include "includes/ldap.inc.php";
  include "includes/ihm.inc.php";

  $uid = $_GET[uid];
  $action= $_GET[action];
  $condition = $_GET[condition];
  $UserRemove = $_POST[UserRemove];

  $warning = "
<p>Vous vous appr&#234;tez &#224; autoriser l'utilisateur <a href='people.php?uid=$uid'>$uid</a> &#224; utiliser le type <tt class='computeroutput'>php</tt> dans son espace espace personnel de publication web.</p>
<p>L'autorisation de mise en ligne d'application web dans un espace personnel de publication engage votre responsabilit&#233; sous couvert de votre chef d'&#233;tablissement ainsi que la responsabilit&#233; de l'utilisateur en question.</p>
<p>Si vous devez autoriser l'installation d'applications web pour r&#233;pondre aux besoins de votre &#233;tablissement, soyez avertis que l'&#233;quipe TICE n'assurera aucun support sur ces applications et qu'il vous revient en collaboration avec l'utilisateur demandeur d'effectuer vous-m&#234;me dans ce cas la veille technique sur les probl&#233;matiques de s&#233;curit&#233; de l'application.</p>
";

  $links = "
<p>
<a href='userdirphptype.php?uid=$uid&action=$action&condition=Yes'>Poursuivre.</a>&nbsp;&nbsp;
<a href='userdirphptype.php?uid=$uid&action=$action&condition=No'>Abandonner.</a>
</p>
";

  $sorry = "
<p>Vous avez abandonn&#233; votre demande de modification de l'attribution du type <tt class='computeroutput'>php</tt> dans l'espace personnel de publication de l'utilisateur <a href='people.php?uid=$uid'><strong>$uid</strong></a>.</p>
<p>Aucune modification n'a &#233;t&#233; apport&#233; !</p>
";

  $information = "
<p>Pour ajouter un membre &#224; cette liste, <a href='search.php'>Recherchez</a> un utilisateur et suivez le lien <tt class='computeroutput'>&#171;Autoriser le type php&#187;</tt></p>
";

  list ($idpers,$login)= isauth();
  if ($idpers == "0") header("Location:$urlauth");
  header_html();
  aff_trailer ("3");
  $html="<h3>Gestion du type <tt class='computeroutput'>php</tt> des <tt class='computeroutput'>espaces de publication web</tt> :</h3>\n";

  if ( ! isset ($condition) && $action == "add" ) {
    $html .= $warning;
    $html .= $links;
  } elseif ( ( $condition == "Yes" && $action == "add") || $action == "rm" ) {
    if ( $action == "add" || $action == "rm" ) {
          // Add or Rm
          exec ("/usr/bin/sudo /usr/share/lcs/scripts/userdirphp.sh $action $uid", $ReturnAddOrRm);
          // Exist ?
          exec ("/usr/bin/sudo /usr/share/lcs/scripts/userdirphp.sh exist $uid ", $ReturnExist);
          // Number ?
          exec ("/usr/bin/sudo /usr/share/lcs/scripts/userdirphp.sh nbr", $ReturnNbr);
          // Affichage resultat de la commande add ou rm
          $html .= "<p>L'utilisateur <a href='people.php?uid=$uid'><strong>$uid</strong></a>&nbsp;";
          if ( $ReturnExist[0] == "Yes" ) 
            $html .= "est actuellement autoris&#233; &#224; utiliser le type <tt class='computeroutput'>php</tt> dans son espace personnel de publication.";
          else 
            $html .= "n'est actuellement plus autoris&#233; &#224; utiliser le type <tt class='computeroutput'>php</tt> dans son espace personnel de publication.</p>";
          $html .= "<p>Il existe actuellement sur votre <i class='emphasis'>LCS</i> <span class='important'>".$ReturnNbr[0]."</span> utilisateur";
          if ( $ReturnNbr[0] > "1" ) $html .="s";
          $html.= "&nbsp;sur <span class='important'>5</span> <tt>(maximum)</tt> qui poss&#232;de";
          if ( $ReturnNbr[0] >= "1" ) $html .="nt";
          $html .= "&nbsp;l'autorisation d'utiliser le type <tt class='computeroutput'>php</tt>.</p>";
          // Links : List, Reset 
          if ( $ReturnNbr[0] > "1" ) {
            $html.= "<h4>\n";
            $html.="  <span><a href='userdirphptype.php?action=list'>Liste des utilisateurs</a></span>";
            $html.="  <span> | <a href='userdirphptype.php?action=reset'>Remise &#224; z&#233;ro de la liste</a></span>";
            $html.= "</h4>\n";
          }
    }
  } elseif ( $action == "reset" ) {
        // Reset
        exec ("/usr/bin/sudo /usr/share/lcs/scripts/userdirphp.sh reset", $ReturnReset);
        $html .= "<h4>Liste des utilisateurs ayant le type <tt class='computeroutput'>php</tt></h4>\n";
        $html .= "<p>Il n'y a plus aucun utilisateur ayant le type <tt class='computeroutput'>php</tt> de valid&#233; sur votre <i class='emphasis'>LCS</i>.</p>\n";
        $html .= $informations;

  } elseif ( $action == "list" ) {
        // Remove List
        if ( count ($UserRemove) ) {
          $drop = "rmlist";
          for ($loop=0; $loop < count ($UserRemove) ; $loop++)
            $drop .= " ".$UserRemove[$loop];
          exec ("/usr/bin/sudo /usr/share/lcs/scripts/userdirphp.sh $drop", $ReturnRmList);
        }
        // List
        exec ("/usr/bin/sudo /usr/share/lcs/scripts/userdirphp.sh list", $ReturnList);
        $html .= "<h4>Liste des utilisateurs ayant le type <tt class='computeroutput'>php</tt></h4>\n";
        if ( count ($ReturnList) > 0 ) {
          $html .= "<form action='userdirphptype.php?action=list' method='post'>\n";
          $html .= "  <select name='UserRemove[]' size='5' multiple='multiple'>\n";
          for ($loop=0; $loop < count ($ReturnList) ; $loop++)
            $html .= "    <option value='".$ReturnList[$loop]."'>".$ReturnList[$loop]."\n";
          $html .= "  </select>\n";
          $html .= "  <br /><br /><input type='submit' value='Supprimer'>\n";
          $html .= "</form>\n";
          $html .= $information;
        } else {
          $html .= "<p>Il n'y a actuellement sur votre <i class='emphasis'>LCS</i> aucun utilisateur ayant le type <tt class='computeroutput'>php</tt> de valid&#233;.</p>\n";
          $html .= $information;
        }

  } elseif ( $condition == "No" ) {
    $html .= $sorry;
    // Exist ?
    exec ("/usr/bin/sudo /usr/share/lcs/scripts/userdirphp.sh exist $uid ", $ReturnExist);
    // Number ?
    exec ("/usr/bin/sudo /usr/share/lcs/scripts/userdirphp.sh nbr", $ReturnNbr);
    $html .= "<p>L'utilisateur <a href='people.php?uid=$uid'><strong>$uid</strong></a>&nbsp;";
    if ( $ReturnExist[0] == "Yes" ) 
      $html .= "est actuellement autoris&#233; &#224; utiliser le type <tt class='computeroutput'>php</tt> dans son espace personnel de publication.";
    else 
      $html .= "n'est actuellement pas autoris&#233; &#224; utiliser le type <tt class='computeroutput'>php</tt> dans son espace personnel de publication.</p>";
    $html .= "<p>Il existe actuellement sur votre <i class='emphasis'>LCS</i> <span class='important'>".$ReturnNbr[0]."</span> utilisateur";
    if ( $ReturnNbr[0] >= "1" ) $html .="s";
    $html.= "&nbsp;sur <span class='important'>5</span> <tt>(maximum)</tt> qui poss&#232;de";
    if ( $ReturnNbr[0] >= "1" ) $html .="nt";
    $html.= "nbsp;l'autorisation d'utiliser le type <tt class='computeroutput'>php</tt>.</p>";
    // Links : List, Reset 
    if ( $ReturnNbr[0] > "1" ) {
      $html.= "<h4>\n";
      $html.="  <span><a href='userdirphptype.php?action=list'>Liste des utilisateurs</a></span>";
      $html.="  <span> | <a href='userdirphptype.php?action=reset'>Remise &#224; z&#233;ro de la liste</a></span>";
      $html.= "</h4>\n";
     }
  }
  echo $html;
  include ("../lcs/includes/pieds_de_page.inc.php");
?>