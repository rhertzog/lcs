<?php
 /* =============================================
   Projet LcSE3 : Gestion des comptes orphelins
   Admin/ldap_cleaner.php
   Equipe Tice académie de Caen
   derniere mise a jour : 26/03/2008
   Distribué selon les termes de la licence GPL
   ============================================= */

include ("../lcs/includes/headerauth.inc.php");
include ("../Annu/includes/ldap.inc.php");
include ("../Annu/includes/ihm.inc.php");
include ( "../Annu/includes/crob_ldap_functions.php");

function search_sambadomain () {
  global $ldap_server, $ldap_port, $ldap_base_dn, $adminDn, $adminPw;

  $filter="(objectClass=sambaDomain)";
  $ldap_search_domain = array( "sambadomainname");

  $ds = @ldap_connect ( $ldap_server, $ldap_port );
  if ( $ds ) {
    $r = @ldap_bind ( $ds,$adminDn, $adminPw );
    if ($r) {
        $result = @ldap_search ( $ds,$ldap_base_dn , $filter, $ldap_search_domain );
        if ($result) $info = @ldap_get_entries ( $ds, $result );
        return $info["count"];
        ldap_free_result($result);
    }
  }
  ldap_close($ds);
}

function search_people_trash ($filter) {
  global $ldap_server, $ldap_port, $dn, $adminDn, $adminPw;
  global $error;
  $error="";
  global $sambadomain;

  //LDAP attributes
  $ldap_search_people_attr = array(
	"acctFlags",
	"pwdMustChange",
	"ntPassword",
	"lmPassword",
	"primarygroupid",
	"rid",
	"userPassword",
	"gecos",
        "employeenumber",
	"homedirectory",
	"gidNumber",
	"uidNumber",
	"loginShell",
	"objectClass",
	"mail",
	"sn",
	"givenName",
	"cn",
	"uid"
  );

  $ldap_search_people_attr_smb3 = array(
	"sambaacctFlags",
	"sambapwdMustChange",
	"sambantPassword",
	"sambalmPassword",
        "sambaSID",
        "sambaPrimaryGroupSID",
	"userPassword",
	"gecos",
        "employeenumber",
	"homedirectory",
	"gidNumber",
	"uidNumber",
	"loginShell",
	"objectClass",
	"mail",
	"sn",
	"givenName",
	"cn",
	"uid"
  );


  $ds = @ldap_connect ( $ldap_server, $ldap_port );
  if ( $ds ) {
    $r = @ldap_bind ( $ds,$adminDn, $adminPw );
    if ($r) {
      // Recherche dans la branche trash
      if ( $sambadomain!="0" )
        $result = @ldap_search ( $ds, $dn["trash"], $filter, $ldap_search_people_attr_smb3 );
      else
        $result = @ldap_search ( $ds, $dn["trash"], $filter, $ldap_search_people_attr );
      if ($result) {
        $info = @ldap_get_entries ( $ds, $result );
        if ( $info["count"]) {
          for ($loop=0; $loop<$info["count"];$loop++) {
            if ( isset($info[$loop]["employeenumber"][0]) ) {
                if ( $sambadomain!="0" ) {
                    $ret[$loop] = array (
		      "sambaacctflags"      => $info[$loop]["sambaacctflags"][0],
		      "sambapwdmustchange"  => $info[$loop]["sambapwdmustchange"][0],
		      "sambantpassword"     => $info[$loop]["sambantpassword"][0],
		      "sambalmpassword"     => $info[$loop]["sambalmpassword"][0],
                      "sambasid"            => $info[$loop]["sambasid"][0],
                      "sambaprimarygroupsid"   => $info[$loop]["sambaprimarygroupsid"][0],
		      "userpassword"        => $info[$loop]["userpassword"][0],
		      "gecos"               => $info[$loop]["gecos"][0],
                      "employeenumber"      => $info[$loop]["employeenumber"][0],
		      "homedirectory"       => $info[$loop]["homedirectory"][0],
		      "gidnumber"           => $info[$loop]["gidnumber"][0],
		      "uidnumber"           => $info[$loop]["uidnumber"][0],
		      "loginshell"          => $info[$loop]["loginshell"][0],
		      "mail"                => $info[$loop]["mail"][0],
		      "sn"                  => $info[$loop]["sn"][0],
		      "givenname"           => $info[$loop]["givenname"][0],
		      "cn"                  => $info[$loop]["cn"][0],
		      "uid"                 => $info[$loop]["uid"][0], 
                    );
                } else {
                    $ret[$loop] = array (
		      "acctflags"	  => $info[$loop]["acctflags"][0],
		      "pwdmustchange"	  => $info[$loop]["pwdmustchange"][0],
		      "ntpassword"      => $info[$loop]["ntpassword"][0],
		      "lmpassword"      => $info[$loop]["lmpassword"][0],
		      "userpassword"    => $info[$loop]["userpassword"][0],
		      "primarygroupid"  => $info[$loop]["primarygroupid"][0],
		      "rid"             => $info[$loop]["rid"][0],
		      "gecos"           => $info[$loop]["gecos"][0],
                      "employeenumber"  => $info[$loop]["employeenumber"][0],
		      "homedirectory"   => $info[$loop]["homedirectory"][0],
		      "gidnumber"       => $info[$loop]["gidnumber"][0],
		      "uidnumber"       => $info[$loop]["uidnumber"][0],
		      "loginshell"      => $info[$loop]["loginshell"][0],
		      "mail"            => $info[$loop]["mail"][0],
		      "sn"              => $info[$loop]["sn"][0],
		      "givenname"       => $info[$loop]["givenname"][0],
		      "cn"              => $info[$loop]["cn"][0],
		      "uid"             => $info[$loop]["uid"][0], 
                    );
                }
            } else {
               if ( $sambadomain!="0" ) {
                    $ret[$loop] = array (
		      "sambaacctflags"      => $info[$loop]["sambaacctflags"][0],
		      "sambapwdmustchange"  => $info[$loop]["sambapwdmustchange"][0],
		      "sambantpassword"     => $info[$loop]["sambantpassword"][0],
		      "sambalmpassword"     => $info[$loop]["sambalmpassword"][0],
                      "sambasid"            => $info[$loop]["sambasid"][0],
                      "sambaprimarygroupsid"   => $info[$loop]["sambaprimarygroupsid"][0],
		      "userpassword"        => $info[$loop]["userpassword"][0],
		      "gecos"               => $info[$loop]["gecos"][0],
		      "homedirectory"       => $info[$loop]["homedirectory"][0],
		      "gidnumber"           => $info[$loop]["gidnumber"][0],
		      "uidnumber"           => $info[$loop]["uidnumber"][0],
		      "loginshell"          => $info[$loop]["loginshell"][0],
		      "mail"                => $info[$loop]["mail"][0],
		      "sn"                  => $info[$loop]["sn"][0],
		      "givenname"           => $info[$loop]["givenname"][0],
		      "cn"                  => $info[$loop]["cn"][0],
		      "uid"                 => $info[$loop]["uid"][0], 
                    );
                } else {
                    $ret[$loop] = array (
		      "acctflags"	  => $info[$loop]["acctflags"][0],
		      "pwdmustchange"     => $info[$loop]["pwdmustchange"][0],
		      "ntpassword"        => $info[$loop]["ntpassword"][0],
		      "lmpassword"        => $info[$loop]["lmpassword"][0],
		      "userpassword"      => $info[$loop]["userpassword"][0],
		      "primarygroupid"    => $info[$loop]["primarygroupid"][0],
		      "rid"               => $info[$loop]["rid"][0],
		      "gecos"             => $info[$loop]["gecos"][0],
		      "homedirectory"     => $info[$loop]["homedirectory"][0],
		      "gidnumber"         => $info[$loop]["gidnumber"][0],
		      "uidnumber"         => $info[$loop]["uidnumber"][0],
		      "loginshell"        => $info[$loop]["loginshell"][0],
		      "mail"              => $info[$loop]["mail"][0],
		      "sn"                => $info[$loop]["sn"][0],
		      "givenname"         => $info[$loop]["givenname"][0],
		      "cn"                => $info[$loop]["cn"][0],
		      "uid"               => $info[$loop]["uid"][0], 
                    );
                }
            }
          }
        }
        @ldap_free_result ( $result );
      } else $error = "Erreur de lecture dans l'annuaire LDAP";
    } else $error = "Echec du bind en admin";
    @ldap_close ( $ds );
  } else $error = "Erreur de connection au serveur LDAP";
  // Tri du tableau par ordre alphabétique
  if (count($ret)) usort($ret, "cmp_name");
  return $ret;
} // Fin function search_people_trash

function draw_table_result ( $msg_cat, $type1, $type2, $type3 ) {
	$html="<table style='margin-left: 200px; margin-bottom: 10px; text-align: left; width: 450px;' border='1' cellpadding='1' cellspacing='1'>\n";
  	$html.="<tbody>\n";
    	$html.="<tr>\n";
      	$html.="<td style='text-align: center; width: 300px; height: 20px; ' colspan='1' rowspan='2'>Utilisateur</td>\n";
      	$html.="<td style='text-align: center; width: 150px; height: 20px; ' colspan='3' rowspan='1'>$msg_cat</td>\n";
    	$html.="</tr>\n";
    	$html.="<tr>\n";
      	$html.="<td style='text-align: center; width: 50px; height: 20px; '>$type1</td>\n";
      	$html.="<td style='text-align: center; width: 50px; height: 20px;'>$type2</td>\n";
      	$html.="<td style='text-align: center; width: 50px;  height: 20px;'>$type3</td>\n";
    	$html.="</tr>\n";
    	$html.="<tr>\n";
	echo $html;
} // Fin function draw_table_result 
// Messages d'aide
function msgaide($msg) {
    return ("&nbsp;<u onmouseover=\"this.T_SHADOWWIDTH=5;this.T_STICKY=1;return escape".gettext("('".$msg."')")."\"><img name=\"action_image2\"  src=\"../images/help-info.gif\"></u>");
}
$msg1="Recherche les comptes des utilisateurs qui ne sont plus affectés à un groupe principal et transfert ces comptes à la corbeille.";
$msg2="Visualise la liste des comptes transférés dans la corbeille.";
$msg3="Permet de réactiver un ou des comptes sous reserve que les uid et/ou les uidNumber de ces comptes soient encore libre.";
$msg4="Efface les répertoitres homes des utilsiteurs situés dans la corbeille.";
$msg5="Supprime les comptes de la corbeille !<br/><strong>ATTENTION</strong> : Ne supprimer les comptes de la corbeille que lorsque vous avez effectué l\'effacement des homes sur l\'ensemble des serveurs qui partagent votre annuaire avec votre LCS ou votre SE3.";
$msg6="Ce compte, n\'est pas récupérable car il possède un uid ou un uidnumber désormais occupé.";
// Messages 
$msg_confirm = "Avant de vider la corbeille, assurez vous d\'avoir préalablement nettoyé les homes des comptes orphelins sur l'ensemble des serveurs qui partagent votre annuaire avec le LCS.<br>";
$msg_confirm .= "<a href=\"ldap_cleaner.php?do=4&phase=1\" target=\"main\">Nettoyage !</a>";

// Vérification de l'authentification 
list ($idpers, $login)= isauth();
if ($idpers == "0") header("Location:$urlauth");

$sambadomain=search_sambadomain ();

echo "<HTML>\n";
echo "	<HEAD>\n";
echo "		<TITLE>...::: Interface d'administration Serveur LCS :::...</TITLE>\n";

// Initialisation variables :
$PHP_SELF = $_SERVER['PHP_SELF'];
// Methode POST
$filtre = $_POST['filtre'];
$filter_type = $_POST['filter_type'];
$nbr = $_POST['nbr'];
$cat = $_POST['cat'];
// Methodes POST ou GET
if ( isset($_POST['phase']) ) 
    $phase = $_POST['phase'];
elseif ( isset($_GET['phase']) )
    $phase = $_GET['phase'];

if ( isset($_POST['do']) ) 
    $do = $_POST['do'];
elseif ( isset($_GET['do']) )  
    $do = $_GET['do'];

// Redirection vers phase suivante, gestion du «sablier»
### DEBUG echo "debug1 do:$do phase:$phase<br>";
// Cas 1 : Transfert des utilisateurs dans la Trash
if( $do==1 && $phase!=1 ) {
	### DEBUG echo "debug2 do:$do phase:$phase<br>";
	echo "<meta HTTP-EQUIV=\"Refresh\" CONTENT=\"1;url='$PHP_SELF?do=1&phase=1'\">\n";
}
// Cas 2 : Examiner le contenu de la poubelle
if( $do==2 && $phase!=1 ) {
	### DEBUG echo "debug2 do:$do phase:$phase<br>";
	echo "<meta HTTP-EQUIV=\"Refresh\" CONTENT=\"1;url='$PHP_SELF?do=2&phase=1'\">\n";
}
// Cas 3 : Effacer les «homes» des comptes orphelins
if( $do==3 && $phase!=1 ) {
	### DEBUG echo "debug2 do:$do phase:$phase<br>";
	echo "<meta HTTP-EQUIV=\"Refresh\" CONTENT=\"1;url='$PHP_SELF?do=3&phase=1'\">\n";
}
// Cas 4 : Vider la corbeille
if( $do==4 && $phase==1 ) {
	### DEBUG echo "debug2 do:$do phase:$phase<br>";
	echo "<meta HTTP-EQUIV=\"Refresh\" CONTENT=\"1;url='$PHP_SELF?do=4&phase=2'\">\n";
}
// Cas 10 : Récupération des utilisateurs de Trash vers People
if( $do==10 && $phase==2 ) {
	### DEBUG echo "debug2 $do $phase<br>";
	echo "<meta HTTP-EQUIV=\"Refresh\" CONTENT=\"1;url='$PHP_SELF?do=10&phase=3'\">\n";
}
// Fin traitement des redirections

echo "		<LINK  href='../Annu/style.css' rel='StyleSheet' type='text/css'>\n";
echo "	</HEAD>\n";
echo "	<BODY>\n";

if (is_admin("lcs_is_admin",$login)=="Y") {

	$html = "<div style=\"margin-bottom: 15%\"><H1>Gestion des comptes orphelins</H1>\n";

	if ($do !="1") 	$html .= "<li><a href=\"ldap_cleaner.php?do=1\" target=\"main\">Transfert des comptes orphelins dans la corbeille</a>".msgaide($msg1)."</li>\n";
	if ($do !="2") 	$html .= "<li><a href=\"ldap_cleaner.php?do=2\" target=\"main\">Examiner le contenu de la corbeille</a>".msgaide($msg2)."</li>\n";
			$html .= "<li><a href=\"ldap_cleaner.php?do=10\" target=\"main\">Récupération de comptes orphelins depuis la corbeille</a>".msgaide($msg3)."</li>\n";
	if ($do !="3") 	$html .= "<li><a href=\"ldap_cleaner.php?do=3\" target=\"main\">Effacer les «homes» des comptes orphelins</a>".msgaide($msg4)."</li>\n";
	if ($do !="4") 	$html .= "<li><a href=\"ldap_cleaner.php?do=4\" target=\"main\">Vider la corbeille</a>".msgaide($msg5)."</li>\n";
	$html .="<p></p>";
	echo $html;

	// Actions
	switch ($do) {
		case 1:
			// Transfert des comptes orphelins dans la corbeille
			if ( $phase != 1 )
				// Affichage du sablier
				echo "<div align='center'><img src=\"Images/wait.gif\" title=\"Patientez...\" align=\"middle\" border=\"0\">&nbsp;Transfert des comptes orphelins dans la corbeille en cours. Veuillez patienter...</div>";
			else {
				// Transfert des comptes orphelins dans la corbeille
        			exec ("$scriptsbinpath/searchAndDelete.pl" ,$AllOutPut,$ReturnValue);
        			if ($ReturnValue == "0")
					echo "Le transfert des  comptes orphelins dans la corbeille s'est déroulé avec succès.<br>";
				else
          				echo "<div class=error_msg>Echec du tansfert des  comptes orphelins dans la corbeille !</div>";
			}		
			break;
		case 2 :
			//Examiner le contenu de la corbeille
			if ( $phase != 1 )
				// Affichage du sablier
				echo "<div align='center'><img src=\"Images/wait.gif\" title=\"Patientez...\" align=\"middle\" border=\"0\">&nbsp;Examiner le contenu de la poubelle. Veuillez patienter...</div>";
			else {			
				$users = search_people_trash ("cn=*");
				echo "<p><img src=\"Images/";
				if (count($users) == 0 ) echo "Poubelle_vide.png";
				else echo "Poubelle_pleine.png";
				echo "\" alt=\"Corbeille\" width=\"51\" height=\"65\" align=\"middle\" border=\"0\">&nbsp;Il y a <STRONG>".count($users)."</STRONG> utilisateur";
				if (count($users) > 1 ) echo "s"; 
				echo "&nbsp;dans la corbeille.</p>\n";
      		echo "<UL>\n";
      		for ($loop=0; $loop<count($users);$loop++)
        			echo "<LI>".utf8_decode($users[$loop]["cn"])."</LI>\n";		
      		echo "</UL>\n";
      	}				
			break;
		case 3 :
			// Nettoyage des répertoires home
			if ( $phase != 1 )
				// Affichage du sablier
				echo "<div align='center'><img src=\"Images/wait.gif\" title=\"Patientez...\" align=\"middle\" border=\"0\">&nbsp;Le nettoyage des répertoires «homes» est en cours. Veuillez patienter...</div>";
			else {		
        			exec ("/usr/bin/sudo $scriptsbinpath/delHome.pl" ,$AllOutPut,$ReturnValue);
        			if ($ReturnValue == "0")
					echo "Le nettoyage des répertoires «home» s'est déroulé avec succès.<br>";
				else
          				echo "<div class=error_msg>Echec du nettoyage des répertoires «home» !</div>";
			}					
			break;
		case 4;
		 	// Vidage de la corbeille
			if ( $phase != 1 && $phase != 2 )
				// Affichage du message de confirmation 
				echo "<div class=error_msg>$msg_confirm</div>";
			elseif ($phase == 1 )
				// Affichage du sablier
				echo "<div align='center'><img src=\"Images/wait.gif\" title=\"Patientez...\" align=\"middle\" border=\"0\">&nbsp;Vidage de la corbeille en cours. Veuillez patienter...</div>";
			elseif ($phase == 2 ) {
				#echo "Le nettoyage de la corbeille s'est déroulé avec succès.<br>";
				$users = search_people_trash ("cn=*");
      				for ($loop=0; $loop<count($users);$loop++) {
			        	$entry="uid=".$users[$loop]["uid"].",".$dn["trash"];
					exec ("$scriptsbinpath/entryDel.pl $entry" ,$AllOutPut,$ReturnValue);
      				}				
				$users = search_people_trash ("cn=*");
				if (count($users) == 0 ) echo "Le nettoyage de la corbeille s'est déroulé avec succès.<br>";
				else echo "<div class=error_msg>Echec du nettoyage de la corbeille !</div>";				
			}
			break;
		case 10;
			// Récupération de comptes orphelins
			// Choix d'un filtre de recherche
			if ( $phase != 1 && $phase != 2 && $phase != 3) {
				$html="<p><u>Recherche des comptes orphelins à transférer</u> :</p>\n"; 
				$html.="<div style='margin-left: 40px'>\n";
				$html.="<form action='ldap_cleaner.php?do=10' method = 'post'>\n";
				$html.="Filtre de recherche&nbsp;";
				$html.="<select name='filter_type'>\n";
				$html.="<option value='contient'>contient</option>\n";
				$html.="<option value='commence'>commence par</option>\n";
				$html.="<option value='finit'>finit par</option>\n";
	      			$html.="</select>\n";
				$html.="<input type='text' name='filtre'>\n";
				$html.="<input type='hidden' name='phase' Value='1'>\n";
				$html.="<input type='submit' Value='Rechercher'>\n";
				$html.="</form></div>\n";
				echo $html;
			} elseif ( $phase == 1 ) {
				// Affichage de la liste des comptes orphelins
				// Interpretation du type de filtre
				if ($filter_type == "contient" ) 
                                    if ($filtre!="*") $filtre="*".$filtre."*";
				if ($filter_type == "commence" ) $filtre=$filtre."*";
				if ($filter_type == "finit" ) $filtre="*".$filtre; 
				// Recherche des utilisateurs répondant au critere
				$users = search_people_trash ("cn=$filtre");
				echo "<div align='center'><img src=\"Images/";
				if (count($users) == 0 ) echo "Poubelle_vide.png";
				else echo "Poubelle_pleine.png";
				echo "\" alt=\"Corbeille\" width=\"51\" height=\"65\" align=\"middle\" border=\"0\">&nbsp;Il y a <STRONG>".count($users)."</STRONG> utilisateur";
				if ( count($users) >= 2 ) echo "s"; 
				echo "&nbsp;dans la corbeille qui répond";
				if ( count($users) >= 2 ) echo "ent"; 
				echo " au «<em>filtre</em>» de recherche.</div>\n";
				// Affichage de la liste des utilisateurs a recuperer
				if ( count($users) > 0) {
					$html="<form action='ldap_cleaner.php?do=10' method = 'post'>\n";
					// Tableau d'affichage des résultats
					draw_table_result ("Catégorie", "Eleve", "Professeur", "Administratif");
      					for ($loop=0; $loop<count($users);$loop++) {
						$html.="<tr><td style='width: 300px;'>".utf8_decode( $users[$loop]["cn"] )."</td>\n";
                                                $NoRecup = false;
                                                # test si on peut récupérer le compte
                                                $attribut[0]="uidnumber";
                                                $tab=get_tab_attribut("people", "uid=*", $attribut);
                                                for($i=0;$i<count($tab);$i++){
                                                    if ( $tab[$i] == $users[$loop]["uidnumber"] ) {
                                                        $NoRecup = true;
                                                        break;
                                                    }  
                                                }
                                                unset($attribut,$tab);
                                                $attribut[0]="uid";
                                                $tab=get_tab_attribut("people", "uid=*", $attribut);
                                                for($i=0;$i<count($tab);$i++){
                                                    if ( $tab[$i] == $users[$loop]["uid"] ) {
                                                        $NoRecup = true;
                                                        break;
                                                    }  
                                                }
                                                if ( $NoRecup ) {
						  $html.="<td colspan='3' style='align: center; width: 150px; font-size:0.7em; font-weight:bold; color:#FDAF4E;'>&nbsp;Ce compte n'est pas récupérable.&nbsp;".msgaide($msg6)."</td>\n";
                                                } else {
						  $html.="<td style='align: center; width: 50px;'><input type='radio' name='cat[$loop]' value='".$users[$loop]["uid"]."@@Eleves'></td>\n";
						  $html.="<td style='width: 50px;'><input type='radio' name='cat[$loop]' value='".$users[$loop]["uid"]."@@Profs'></td>\n";
						  $html.="<td style='width: 50px;'><input type='radio' name='cat[$loop]' value='".$users[$loop]["uid"]."@@Administratifs'></td></tr>\n";
                                                }
      					}
					$html.="</tbody>\n</table>\n";	
					$html.="<input type='hidden' name='phase' Value='2'>\n";
					$html.="<input type='hidden' name='nbr' Value='$loop'>\n";
					$html.="<div style='margin-left: 200px'>\n";
					$html.="<input type='submit' Value='Récupérer'>\n";
					$html.=" <input type='reset' Value='Réinitialisier'>\n";
					$html.="</form></div>\n";	
						
				} else $html = "<div class='alert_msg'>Pas de transfert à effectuer !</div>\n";
				echo $html;	
			} elseif ( $phase == 2 ) {
				// Transfert des comptes de trash -> peoples et positionnement des groupes principaux
				// Transfert des utilisateurs sélectionné dans /tmp/list_recup
				for ($loop=0; $loop<$nbr;$loop++) {
					if ( isset($cat[$loop]) ) {
						$tmp = $cat[$loop];
						exec ("echo $tmp >> /tmp/list_recup");
					}						
				}
				// Affichage du sablier
				echo "<div align='center'><img src=\"Images/wait.gif\" title=\"Patientez...\" align=\"middle\" border=\"0\"> Récupération des comptes orphelins en cours. Veuillez patienter...</div>";
			} elseif ( $phase == 3 ) {
				// Récuperation des utilisateurs sélectionnés
				if ( file_exists("/tmp/list_recup") ) {
					$fd = fopen("/tmp/list_recup", "r");
					draw_table_result ("Récupération dans la catégorie", "Eleve", "Professeur", "Administratif");
					while ( !feof($fd) ) {
						$tmp = fgets($fd, 255);
				        	$trash_member=explode("@@", $tmp);
						// Nettoyage des espaces dans trash_member[1]
						$categorie=trim($trash_member[1]);
						// uid => $trash_member[0]
						// Categorie $trash_member[1]
						if ( $trash_member[0] != "" ) {
							// Lecture des params de l'utilisateur sélectionné dans la trash
							$user = search_people_trash ("uid=$trash_member[0]");
							// Positionnement des constantes "objectclass"
							if ( $sambadomain!="0" ) $user[0]["sambaacctflags"]="[U          ]";
                                                        else $user[0]["acctflags"]="[U          ]";
							$user[0]["objectclass"][0]="top";
							$user[0]["objectclass"][1]="posixAccount";
							$user[0]["objectclass"][2]="shadowAccount";
							$user[0]["objectclass"][3]="person";
							$user[0]["objectclass"][4]="inetOrgPerson";
							if ( $sambadomain!="0" ) $user[0]["objectclass"][5]="sambaSamAccount";
                                                        else  $user[0]["objectclass"][5]="sambaAccount";
							### DEBUG
                                                        if ( $DEBUG=="true" ) {
							 echo "------------------------------------------<br>";
                                                         if ( $sambadomain!="0" ) {
							     echo "sambaacctflags :".$user[0]["sambaacctflags"]."<br>";
							     echo "sambapwdmustchange :".$user[0]["sambapwdmustchange"]."<br>";
							     echo "sambantpassword :".$user[0]["sambantpassword"]."<br>";
							     echo "sambalmpassword :".$user[0]["sambalmpassword"]."<br>";
                                                             echo "sambaSID :".$user[0]["sambasid"]."<br>";
                                                             echo "SambaprimaryGroup".$user[0]["sambaprimarygroupsid"]."<br>";
                                                         } else {
							     echo "acctflags :".$user[0]["acctflags"]."<br>";
							     echo "pwdmustchange :".$user[0]["pwdmustchange"]."<br>";
							     echo "ntpassword :".$user[0]["ntpassword"]."<br>";
							     echo "lmpassword :".$user[0]["lmpassword"]."<br>";
							     echo "userPassword :".$user[0]["userpassword"]."<br>";
							     echo "primarygroupid :".$user[0]["primarygroupid"]."<br>";
							     echo "rid :".$user[0]["rid"]."<br>";
                                                         }
                                                         echo "userPassword :".$user[0]["userpassword"]."<br>";
							 echo "gecos :".$user[0]["gecos"]."<br>";
                                                         echo "employeenumber :".$user[0]["employeenumber"]."<br>";
							 echo "homedirectory :".$user[0]["homedirectory"]."<br>";
							 echo "gidnumber :".$user[0]["gidnumber"]."<br>";
							 echo "uidnumber :".$user[0]["uidnumber"]."<br>";
							 echo "loginshell :".$user[0]["loginshell"]."<br>";			
							 echo "objectclass :".$user[0]["objectclass"][0]."<br>";
							 echo "objectclass :".$user[0]["objectclass"][1]."<br>";
							 echo "objectclass :".$user[0]["objectclass"][2]."<br>";
							 echo "objectclass :".$user[0]["objectclass"][3]."<br>";
							 echo "objectclass :".$user[0]["objectclass"][4]."<br>";
							 echo "objectclass :".$user[0]["objectclass"][5]."<br>";
							 echo "mail :".$user[0]["mail"]."<br>";
							 echo "sn :".$user[0]["sn"]."<br>";
							 echo "givenname :".$user[0]["givenname"]."<br>";
							 echo "cn :".$user[0]["cn"]."<br>";
							 echo "uid :".$user[0]["uid"]."<br>"; 
							 echo "------------------------------------------<br>";
                                                        }
							### FIN DEBUG						
							// Modification de l'entrée dn ou=Trash -> ou=People
	    	 					$ds = @ldap_connect ( $ldap_server, $ldap_port );
      							if ( $ds ) {
	          						$r = @ldap_bind ( $ds, $adminDn, $adminPw ); // Bind en admin
        	  						if ($r) {
										// Ajout dans la branche people
                                                                                
              									if ( @ldap_add ($ds, "uid=".$user[0]["uid"].",".$dn["people"],$user[0] ) ) {
											// Suppression de la branche Trash
											@ldap_delete ($ds, "uid=".$user[0]["uid"].",".$dn["trash"] );
											// Ajout au groupe principal
											@exec ("$scriptsbinpath/groupAddUser.pl $trash_member[0] $categorie");
										        $recup=true;
										} else $recup=false;
									}
							}
							@ldap_close ( $ds );
							// Affichage des utilisateurs récupérés
							$html="<tr><td style='width: 300px;'>";
							if ( $recup )  $html.="<a href='../Annu/people.php?uid=".$user[0]["uid"]."'>";
							$html.=utf8_decode( $user[0]["cn"] );
							if ( $recup ) $html.="</a>";
							$html.="</td>\n";
							if ( $recup ) {
								$html.="<td style='text-align: center; width: 50px;'>";
								if ( $categorie == "Eleves" ) $html.="<b>X</b>"; else $html.="&nbsp;";
								$html.="</td>\n";
								$html.="<td style='text-align: center; width: 50px;'>";
								if ( $categorie == "Profs" ) $html.="<b>X</b>"; else $html.="&nbsp;";
								$html.="</td>\n";
								$html.="<td style='text-align: center; width: 50px;'>";
								if ( $categorie == "Administratifs" ) $html.="<b>X</b>"; else $html.="&nbsp;";
								$html.="</td></tr>\n";
							} else $html.="<td colspan='3' style='color: #ff8f00; text-align: center; width: 50px;'>Compte irrécupérable !</td></tr>\n";
							echo $html;			
						}				
					}
					fclose($fd);
					unlink ("/tmp/list_recup");
					echo "</tbody>\n</table>\n";
				} else  echo "<div class=error_msg>Vous n'avez pas sélectionné d'utilisateur(s) à récupérer!</div>";
			}		
		break; // Fin case
                
	}
} else echo "Vous n'avez pas les droits nécessaires pour cette action...";
echo "</div>";	
include ("../lcs/includes/pieds_de_page.inc.php");
?>
