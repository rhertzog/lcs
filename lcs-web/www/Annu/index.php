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
$login=$_SESSION['login'];

  header_html();
  aff_trailer ("1");
  // Affichage des coordonnees de l'Etablissement

  $ldap_etab_attr = array(
    "ou",                 // Intitule de l'Etablissement
    "street",
    "l",
    "postOfficeBox",
    "PostalCode",
    "telephoneNumber"
  );

  $ds = @ldap_connect ( $ldap_server, $ldap_port );
  if ( $ds ) {
    $r = @ldap_bind ( $ds ); // Bind anonyme
    if ($r) {
      $result = @ldap_read ( $ds, $ldap_base_dn, "(objectclass=organizationalUnit)", $ldap_etab_attr );
      if ($result) {
        $info = @ldap_get_entries ( $ds, $result );
        if ( $info["count"]) {
          echo "<blockquote style=\"font-size: large; font-weight: bold; text-align: center\">\n";
          echo $info[0]["ou"][0]."<br />\n";
          echo $info[0]["street"][0]."<BR>\n";
          if ( $info[0]["postofficebox"][0]) {
            echo $info[0]["postofficebox"][0]."&nbsp;-&nbsp;";
          }
          echo $info[0]["postalcode"][0]." ".$info[0]["l"][0]."<br />\n";
          echo "Tel. ".$info[0]["telephonenumber"][0]."\n";
          echo"</blockquote>\n";
        }
        @ldap_free_result ( $result );
      }
    } else {
      $error = "Echec du bind anonyme";
    }
    @ldap_close ( $ds );
  } else {
    $error = "Erreur de connection au serveur LDAP";
  }

  aff_mnu_search(is_admin("Annu_is_admin",$login));
  if (ldap_get_right("lcs_is_admin",$login)=="Y") {
    echo "<ul>
    <li><a href=\"delete_right.php\">Enlever un droit d'administration</a></li>
       </ul>\n";
  }
  if (ldap_get_right("annu_is_admin",$login)=="Y") {
  echo "<ul>
    <li><a href=\"mod_shell.php\">Modification du shell des utilisateurs</a></li>
    </ul>\n";
  }
  include ("../lcs/includes/pieds_de_page.inc.php");
?>
