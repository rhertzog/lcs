<?php
/* =============================================
   Projet LCS : Linux Communication Server
   Redirection des mails
   Annu/mod_mail.php
   [LCS CoreTeam]
   Equipe Tice academie de Caen
   23/10/2009
   ============================================= */
  include "../lcs/includes/headerauth.inc.php";
  include "includes/ldap.inc.php";
  include "includes/ihm.inc.php";
  include ("../lcs/includes/jlcipher.inc.php");
  list ($idpers,$login)= isauth();
  if (($idpers == "0") || is_eleve($login) ) header("Location:$urlauth");
  $cmd="hostname -d";
  exec($cmd,$hn,$retour);
  $hostn= $hn[0];
  if (is_admin("Lcs_is_admin",$login) == "Y" ) {
	if (isset($_GET[uid])) $log2=$_GET[uid];
	elseif (isset($_POST[uid])) $log2=$_POST[uid];
	else $log2=$login;
	}
	else 
	$log2=$login;
	//traitement du formulaire
if ((isset($_POST['Valider']))&& (isset($_POST['adr_mail'])) )
	{
		if ($_POST['adr_mail']!="") $contenu=$_POST['adr_mail']; else $contenu="aucune";
		if ($_POST['choix']=="yes" && $_POST['adr_mail']!="")
		$contenu2= $log2."@".$hostn;
				
		$fichier_script_sudo = "/usr/share/lcs/scripts/execution_script_plugin.sh";
		$script="'/usr/share/lcs/scripts/redir.sh ".$contenu." ". $log2. " ".$contenu2."'";
		$cmd = "/usr/bin/sudo -u root " . $fichier_script_sudo . " " . $script;
		exec($cmd,$lignes_retournees,$ret_val);
		if ($ret_val!=0) $message= '<div class="error_msg"> L\'op&#233;ration a &#233;chou&#233;</div>';
		else 
		{
		exec ("/usr/bin/sudo /usr/share/lcs/scripts/chacces.sh 660 ".$log2.":lcs-users"." /home/".$log2."/.forward",$rien, $retour);
		if 	($retour==0)
			{
			$nom_file="/home/admin/Documents/mailredir.txt";
			$datte=date("d:m:Y H:i");
			$fichier=fopen($nom_file,"a");
			if ($contenu=="aucune") 
				{
				$message ='<P><B>Les mails ne sont plus redirig&#233;s.</B>';
				fputs($fichier, $datte . " Annulation redirection ". $log2." \n ");
				fclose($fichier);
				}
			else 
				{
				fputs($fichier, $datte . " - ". $log2. " vers ".$_POST['adr_mail']." \n ");
				fclose($fichier);
				$message= '<P><B>La boite a &#233;t&#233; redirig&#233;e.</B> <br> - Un mail de confirmation  a &#233;t&#233; envoy&#233; <br>';
				//destinataire
						$mailTo2=$log2;
						//Le sujet
						$mailSubject = "Redirection de mails";
						//Le  message
						$mailBody ="Message automatique ( Ne pas répondre ! ): \n \n Les mails en ".$log2."@".$hostn." sont d&#233;sormais renvoy&#233;s vers  ".$_POST['adr_mail'];
						//l'expéditeur
						$mailHeaders = "From: LCS";
						//envoi mail
						 //mail($mailTo, $mailSubject, $mailBody, $mailHeaders);
						 mail($mailTo2, $mailSubject, $mailBody, $mailHeaders);
				}
			}
			else $message='<div class="error_msg">La redirection a &#233;chou&#233 ! </div>';
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
                
    ?>
<script type="text/javascript">
function writediv(texte)
     {
     document.getElementById('bouton').innerHTML = texte;
     }

function test_email (my_email) {
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
      <p>Rediriger les mails en <span class="important"><?echo $log2.'@'.$hostn;?> 
		</span> vers une boite personnelle</p>
	<? if ($adresse!="")
	echo "<p>Pour annuler la redirection, vider le champs ci-dessous et valider.</p>";?>	
      <form name = "auth" action="mod_mail.php" method="post" ">
        <table border="0">
          <tr>
            <td> - Adresse de redirection 
            <? if ($adresse!="") echo " actuelle ";?>:
             <input type="text" name="adr_mail" value="<?echo $adresse;?>" size="40" onKeyUp="test_email(this.value)" ></td>
          </tr>
          <tr>
          <td> - Faut il conserver dans la boite du Lcs, une copie des mails redirig&#233;s ?</B></td>
          <td>
          <P>
          <input type="radio" name="choix" value="yes"
          <? if (($copie!="" && $adresse!="") || $adresse =="") echo "checked";?>
          > OUI
		  <input type="radio" name="choix" value="no" 
		  <? if ($copie=="" && $adresse!="")  echo "checked";?>
		  > NON
		  <input type="hidden" name="uid" value= "<? echo $log2; ?>">
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
       
      
  include ("../lcs/includes/pieds_de_page.inc.php");
?>
