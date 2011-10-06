<?php
/**
 * contact.php
 * Formulaire d'envoi de mail
 * Ce script fait partie de l'application GRR
 * Derni�re modification : $Date: 2009-09-29 18:02:56 $
 * @author    Laurent Delineau <laurent.delineau@ac-poitiers.fr>
 * @copyright Copyright 2003-2008 Laurent Delineau
 * @link      http://www.gnu.org/licenses/licenses.html
 * @package   root
 * @version   $Id: contact.php,v 1.4 2009-09-29 18:02:56 grr Exp $
 * @filesource
 *
 * This file is part of GRR.
 *
 * GRR is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * GRR is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with GRR; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

include_once('include/connect.inc.php');
include_once('include/config.inc.php');
include_once('include/functions.inc.php');
include_once('include/'.$dbsys.'.inc.php');
include_once('include/misc.inc.php');
include_once('include/mrbs_sql.inc.php');

$grr_script_name = 'contact.php';

// Settings
require_once('include/settings.inc.php');

//Chargement des valeurs de la table settingS
if (!loadSettings())
    die("Erreur chargement settings");

// Session related functions
require_once('include/session.inc.php');

// Param�tres langage
include_once('include/language.inc.php');

// Resume session
$fin_session = 'n';
if (!grr_resumeSession())
    $fin_session = 'y';

if (($fin_session == 'y') and (getSettingValue("authentification_obli")==1)) {
    header("Location: ./logout.php?auto=1&url=$url");
    die();
};

if ((getSettingValue("authentification_obli")==0) and (getUserName()=='')) {
    $type_session = "no_session";
}
else
{
  $type_session = "with_session";
}

echo begin_page(get_vocab("mrbs").get_vocab("deux_points").getSettingValue("company"));
echo "<div class=\"page_sans_col_gauche\">";


$cible = isset($_POST["cible"]) ? $_POST["cible"] : (isset($_GET["cible"]) ? $_GET["cible"] : '');
$cible = htmlentities($cible); // s�curit�
$type_cible = isset($_POST["type_cible"]) ? $_POST["type_cible"] : (isset($_GET["type_cible"]) ? $_GET["type_cible"] : '');
if ($type_cible!='identifiant:non') $type_cible='';  // s�curit�
$action = isset($_POST["action"]) ? $_POST["action"] : '';
$corps_message = isset($_POST["message"]) ? $_POST["message"] : 'Contenu du message : ';
$email_reponse = isset($_POST["email_reponse"]) ? $_POST["email_reponse"] : '';
$error_subject = 'n';
if (isset($_POST["objet_message"])) {
  $objet_message = trim($_POST["objet_message"]);
  if ($objet_message == '')
    $error_subject = 'y';
}

$casier = isset($_POST["casier"]) ? $_POST["casier"] : '';

if ($error_subject == 'y') {
  $action='';
}

echo "<h1>".get_vocab("Envoi d_un courriel")."</h1>";

switch($action)
{
//envoi du message
case "envoi":
  $destinataire = "";
  if ($type_cible=="identifiant:non") {
      if ($cible=="contact_administrateur") {
        $destinataire = getSettingValue("webmaster_email");
      } else if ($cible=="contact_support") {
        $destinataire = getSettingValue("technical_support_email");
      }
  } else {
    // Il s'agit d'un utilisateur de la base
        $destinataire = grr_sql_query1("SELECT email FROM ".TABLE_PREFIX."_utilisateurs WHERE login = '".protect_data_sql($cible)."'");
        if ($destinataire==-1)
            $destinataire="";
  }
  if ($destinataire=="") {
        echo "<h1 class=\"avertissement\">L'envoi de messages est impossible car l'adresse email du destinataire n'a pas �t� renseign�e.</h1>";
        include "include/trailer.inc.php";
        exit;
  }

	//N.B. pour peaufiner, mettre un script de v�rification de l'adresse email et du contenu du message !
	$message="";
	if (($fin_session == 'n') and (getUserName()!='')) {
	    $message .= "Nom et pr�nom du demandeur : ".affiche_nom_prenom_email(getUserName(),"","nomail")."\n";
      $user_email = grr_sql_query1("select email from ".TABLE_PREFIX."_utilisateurs where login='".getUserName()."'");
      if (($user_email != "") and ($user_email != -1))
    	    $message .= "Email du demandeur : ".$user_email."\n";
	    $message .= $vocab["statut"].preg_replace("/&nbsp;/", " ",$vocab["deux_points"]).$_SESSION['statut']."\n";
	}
	$message .= $vocab["company"].preg_replace("/&nbsp;/", " ",$vocab["deux_points"]).removeMailUnicode(getSettingValue("company"))."\n";
	$message .= $vocab["email"].preg_replace("/&nbsp;/", " ",$vocab["deux_points"]).$email_reponse."\n";
	$message.="\n".$corps_message."\n";
  $sujet = $vocab["subject_mail1"]." - ".$objet_message;

  require_once ("./include/mail.inc.php");
  $m= new my_phpmailer();
  $m->SetLanguage("fr","./phpmailer/language/");
  setlocale(LC_ALL,$locale);
  $tab_destinataire = explode(';',preg_replace("/ /", "",$destinataire));
  foreach($tab_destinataire as $item_email){
      $m->AddAddress($item_email);
  }
  $m->Subject = $sujet;
  $m->Body = $message;
  $m->AddReplyTo( $email_reponse );
  if(!$m->Send()) {
      $message_erreur .= $m->ErrorInfo;
      echo $message_erreur;
  } else {
		echo "<p style=\"text-align: center\">Votre message a �t� envoy� !</p>";
	}
	break;

default://formulaire d'envoi
	echo "<table cellpadding='5'>";
	if (($fin_session == 'n') and (getUserName()!='')) {
	     echo "<tr><td>".get_vocab("Message poste par").get_vocab("deux_points")."</td><td><b>".affiche_nom_prenom_email(getUserName(),"",$type="nomail")."</b></td></tr>\n";
	}
	echo "<tr><td>".get_vocab("webmaster_name").get_vocab("deux_points")."</td><td><b>".getSettingValue("webmaster_name")."</b></td></tr>\n";

	echo "<tr><td>".get_vocab("company").get_vocab("deux_points")."</td><td><b>".getSettingValue("company")."</b></td></tr>\n";

  echo "<tr><td colspan=\"2\">".get_vocab("Redigez votre message ci-dessous").get_vocab("deux_points")."</td></tr>\n";
	echo "</table>\n";
	echo "<form action=\"contact.php\" method=\"post\" id=\"doc\">\n";
	echo "<div>\n";
	echo "<input type=\"hidden\" name=\"action\" value=\"envoi\" />\n";
  if ($cible != '')
	    echo "<input type=\"hidden\" name=\"cible\" value=\"".$cible."\" />\n";
  if ($type_cible != '')
	    echo "<input type=\"hidden\" name=\"type_cible\" value=\"".$type_cible."\" />\n";

	echo get_vocab("Objet du message").get_vocab("deux_points");
	echo "<br /><input type=\"text\" name=\"objet_message\" id=\"objet_message\" size=\"40\" maxlength=\"256\" value=''/>\n";

	echo "<textarea name=\"message\" cols=\"50\" rows=\"5\">".$corps_message."</textarea><br />";

	echo get_vocab("E-mail pour la reponse").get_vocab("deux_points");
	echo "<input type=\"text\" name=\"email_reponse\" id=\"email_reponse\" size=\"40\" maxlength=\"256\" ";
	if ($email_reponse != '') {
      echo "value='".$email_reponse."' ";
	} else if (($fin_session == 'n') and (getUserName()!='')) {
      $user_email = grr_sql_query1("select email from ".TABLE_PREFIX."_utilisateurs where login='".getUserName()."'");
      if (($user_email != "") and ($user_email != -1))
          echo "value='".$user_email."' ";
	}
	echo "/>\n";
	echo "<br />\n";

	echo "<p style=\"text-align:center;\">";
	echo "<input type='button' value='".get_vocab("submit")."' onclick='verif_et_valide_envoi();' />\n";
	echo "</p>\n";
  echo "</div>\n";
	echo "</form>\n";

	echo "<script type='text/javascript'>
	function verif_et_valide_envoi() {
	 if(document.getElementById('objet_message')) {
			objet=document.getElementById('objet_message').value;
			if(objet=='') {
  				alert('Vous n\\'avez pas saisi d\\'objet au message. Ce champ est obligatoire.');
  				exit();
  		}

   }
	 if(document.getElementById('email_reponse')) {
			email=document.getElementById('email_reponse').value;
			if(email=='') {
				confirmation=confirm('Vous n\\'avez pas saisi d\\'adresse courriel/email.\\nVous ne pourrez pas recevoir de r�ponse par courrier �lectronique.\\nSouhaitez-vous n�anmoins poster le message?');
				if(confirmation) {
					document.getElementById('doc').submit();
				}
			}
			else {
				var verif = /^[a-zA-Z0-9_.-]+@[a-zA-Z0-9.-]{2,}[.][a-zA-Z]{2,3}$/
				if (verif.exec(email) == null) {
					confirmation=confirm('L\\'adresse courriel/email saisie ne semble pas valide.\\nVeuillez contr�ler la saisie et confirmer votre envoi si l\\'adresse est correcte.\\nSouhaitez-vous n�anmoins poster le message?');

					if(confirmation) {
						document.getElementById('doc').submit();
					}
				}
				else {
					document.getElementById('doc').submit();
				}
			}
		}
		else {
			document.getElementById('doc').submit();
		}
	}
</script>\n";

	break;
}
echo "</div>";
include_once('include/trailer.inc.php');
?>