<?php
/* =============================================
   Projet LCS-SE3
   Consultation/ Gestion de l'annuaire LDAP
   Equipe Tice academie de Caen
   Distribue selon les termes de la licence GPL
   Derniere modification : 23/05/2014
   ============================================= */
include "includes/check-token.php";
if (!check_acces()) exit;

$login=$_SESSION['login'];
include "../lcs/includes/headerauth.inc.php";
include "includes/ldap.inc.php";
include "includes/ihm.inc.php";
include ("../lcs/includes/jlcipher.inc.php");

if (count($_GET)>0 || count($_POST)>0 ) {
      //configuration objet
      include ("../lcs/includes/htmlpurifier/library/HTMLPurifier.auto.php");
      $config = HTMLPurifier_Config::createDefault();
      $purifier = new HTMLPurifier($config);
if (count($_POST)>0) {
      if ($_POST['adr_mail']!="") {
         $adr_destination= (filter_var($_POST['adr_mail'], FILTER_VALIDATE_EMAIL)) ? $adr_destination=$purifier->purify($_POST['adr_mail']) : "";
         }
      else $adr_destination="aucune";
       if ($_POST['choix']) $choix=$purifier->purify($_POST['choix']);
}
       //les $Get sont purifiees plus loin
}

$message=$adr_locale="";

//test la validite du webmail
$query="SELECT value from applis where name='squirrelmail' or name='roundcube' ";
$result=mysqli_query($GLOBALS["___mysqli_ston"], $query);
if ($result)
    {
    if ( mysqli_num_rows($result) !=0 ) {
        $r=mysqli_fetch_object($result);
        $test_squir=$r->value;
        }
    else $test_squir="0";
    }
    else $test_squir="0";
((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);

//redirection si non droit
if ( (ldap_get_right("Mail_can_redir",$login)=="N") || $test_squir=="0") {
      header("Location:index.php");
      exit;
}

if (is_admin("Lcs_is_admin",$login) == "Y" )
    {
    if (isset($_GET['uid'])) $log2=$purifier->purify($_GET['uid']);
    elseif (isset($_POST['uid'])) $log2=$purifier->purify($_POST['uid']);
    else $log2=$login;
    }
    else
    $log2=$login;

//traitement du formulaire
$cmd="hostname -d";
exec($cmd,$hn,$retour);
$hostn= $hn[0];

if ((isset($_POST['Valider']))&& ($adr_destination != "")   && $choix !="" && ( preg_match("#^[A-Za-z0-9._-]{3,19}$#", $log2)))
            {
            if ($choix=="oui" && $adr_destination!="" && $adr_destination!="aucune" ) $adr_locale= $log2."@".$hostn;
            $script="/usr/share/lcs/scripts/redir.sh ".escapeshellarg($adr_destination)." ". escapeshellarg($log2). " ".escapeshellarg($adr_locale);
            $cmd = "/usr/bin/sudo  " . $script;
            exec($cmd,$lignes_retournees,$ret_val);
            if ($ret_val!=0) $message= '<div class="error_msg"> L\'op&#233;ration a &#233;chou&#233;</div>';
            else
                    {
                    $datte=date("Y:m:d H:i:s");
                    if ($adr_destination=="aucune")
                            {
                            $message ='<P><B>Les mails ne sont plus redirig&#233;s.</B>';
                            }
                    else
                            {
                            $message.= '<P><B>La boite a &#233;t&#233; redirig&#233;e.</B> <br> - Un mail de confirmation  a &#233;t&#233; envoy&#233; <br>';
                            }
                    $cmd = "INSERT INTO redirmail (faitpar,pour,vers,copie,date,remote_ip) VALUES ('$login','$log2','$adr_destination', '$choix','$datte', '{$_SERVER['REMOTE_ADDR']}');";

                    if(!mysqli_query($GLOBALS["___mysqli_ston"], $cmd))  $message.="Erreur insertion base de donn&#233;es  ";

                    //envoi mail de confirmation
                    //destinataire
                    $mailTo2=$log2;
                    //Le sujet
                    $mailSubject = "Redirection de mails";
                    //Le  message
                    if ($adr_destination == "aucune")
                    $mailBody ="Message automatique ( Ne pas repondre ! ): \n \n Les mails a destination de ".$log2."@".$hostn." ne sont  plus rediriges ";
                    else
                    $mailBody ="Message automatique ( Ne pas repondre ! ): \n \n Les mails a destination de ".$log2."@".$hostn." sont desormais renvoyes vers l adresse : ".$adr_destination;
                    //l'expéditeur
                    $mailHeaders = "From: LCS";
                    //envoi mail
                     //mb_send_mail($mailTo, $mailSubject, $mailBody, $mailHeaders);
                     mb_send_mail($mailTo2, $mailSubject, $mailBody, $mailHeaders);
                    }
        }

// Si $log2, on récupère les datas de l'utilisateur
if ($log2)
    {
    $adresse="";
    $file_name="/home/".$log2."/.forward";
    if (file_exists($file_name))
            {
            if (!$fp=fopen($file_name,"r"))
                    {
                    echo  "<div class='error_msg'> L\'op&#233;ration a &#233;chou&#233;</div>";
                    }
            else
                    {
                    $redirect=fgetcsv($fp,128);
                    $adresse=$redirect[0];
                    $ligne=fgetcsv($fp,128);
                    $copie=$ligne[0];
                    }
            }
    }

//affichage du formulaire
header_crypto_html("Redirection des mails");
aff_trailer ("5");
if (! preg_match("#^[A-Za-z0-9._-]{3,19}$#", $log2)) {
    echo '<div class="error_msg">Le login n\'est pas conforme</div>';
    exit;
}
?>
<script type="text/javascript">
function writediv(texte)
     {
     document.getElementById('bouton').innerHTML = texte;
     }

function test_emb_send_mail (my_email) {
        var new_string = new String(my_email);
        if ((!new_string.match('^[-_\.0-9a-zA-Z]{1,}@[-_\.0-9a-zA-Z]{1,}[\.][0-9a-zA-Z]{2,}$')) && (my_email!="")) {
                return writediv(' <div class="error_msg"> Entrez une adresse valide</div>');
        }
        else {
                writediv('<input type="submit" name="Valider" value="Valider">');
        }
    }

  </script>
        <h3><u>Redirection des mails</u></h3>
        <p>Rediriger les mails &agrave; destination de  <span class="important"><?echo $log2.'@'.$hostn;?>
        </span> vers une boite personnelle</p>
        <? if ($adresse!="") echo "<p>Pour annuler la redirection, vider le champs ci-dessous et valider.</p>";?>
        <form name = "auth" action="mod_mail.php" method="post" ">
        <table border="0">
          <tr>
            <td> - Adresse de redirection
            <? if ($adresse!="") echo " actuelle ";?>:
             <input type="text" name="adr_mail" value="<?echo $adresse;?>" size="40" onKeyUp="test_emb_send_mail(this.value)" ></td>
          </tr>
          <tr>
          <td> - Faut il conserver dans la boite du Lcs, une copie des mails redirig&#233;s ?</B></td>
          <td>
          <P>
          <input type="radio" name="choix" value="oui"
          <? if (($copie!="" && $adresse!="") || $adresse =="") echo "checked";?>
          > OUI
        <input type="radio" name="choix" value="non"
        <? if ($copie=="" && $adresse!="")  echo "checked";?>
        > NON
        <input type="hidden" name="uid" value= "<? echo $log2; ?>">
        <input name="jeton" type="hidden"  value="<?php echo md5($_SESSION['token'].htmlentities($_SERVER['PHP_SELF'])); ?>" />
        </P>
        </td></tr><tr></tr>
        <td colspan=2 align=center>
        <div id=bouton> <input type="submit" name="Valider" value="Valider"></div>
        </td>
        </tr>
        </table>
      </form>
<?
  if ($message!="") echo $message;
  ((is_null($___mysqli_res = mysqli_close($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);
  include ("../lcs/includes/pieds_de_page.inc.php");
?>
