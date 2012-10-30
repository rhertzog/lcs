<?php
/* =============================================
   Projet LCS : Linux Communication Server
   Redirection des mails
   Annu/mod_mail.php
   [LCS CoreTeam]
   Equipe Tice academie de Caen
   30/10/2012
   ============================================= */
  include "../lcs/includes/headerauth.inc.php";
  include "includes/ldap.inc.php";
  include "includes/ihm.inc.php";
  include ("../lcs/includes/jlcipher.inc.php");
  $query="SELECT value from applis where name='squirrelmail' or name='roundcube' ";
  $result=mysql_query($query);
  if ($result) 
	{
          if ( mysql_num_rows($result) !=0 ) {
          $r=mysql_fetch_object($result);
          $test_squir=$r->value;
          }
          else $test_squir="0";
          }
          else $test_squir="0";
  mysql_free_result($result);
         
  list ($idpers,$login)= isauth();
  if (($idpers == "0") ) header("Location:$urlauth");
  if ( (ldap_get_right("Mail_can_redir",$login)=="N") || $test_squir=="0") header("Location:index.php");
  $cmd="hostname -d";
  exec($cmd,$hn,$retour);
  $hostn= $hn[0];
  if (is_admin("Lcs_is_admin",$login) == "Y" )
  	{
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
		if ($_POST['choix']=="oui" && $_POST['adr_mail']!="")
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
				$datte=date("Y:m:d H:i:s");
				if ($contenu=="aucune") 
					{
					$message ='<P><B>Les mails ne sont plus redirig&#233;s.</B>';
					}
				else
					{
					$message.= '<P><B>La boite a &#233;t&#233; redirig&#233;e.</B> <br> - Un mail de confirmation  a &#233;t&#233; envoy&#233; <br>';
					}
					$cmd = "INSERT INTO redirmail (faitpar,pour,vers,copie,date,remote_ip) VALUES ('$login','$log2','$contenu', '{$_POST['choix']}','$datte', '{$_SERVER['REMOTE_ADDR']}');";
			
					if(!mysql_query($cmd))  $message.="Erreur insertion base de donn&#233;es  ";
				  
//envoi mail de confirmation			
			
							//destinataire
							$mailTo2=$log2;
							//Le sujet
							$mailSubject = "Redirection de mails";
							//Le  message
							if ($contenu=="aucune")
							$mailBody ="Message automatique ( Ne pas repondre ! ): \n \n Les mails a destination de ".$log2."@".$hostn." ne sont  plus rediriges ";
							else
							$mailBody ="Message automatique ( Ne pas repondre ! ): \n \n Les mails a destination de ".$log2."@".$hostn." sont desormais renvoyes vers l adresse : ".$_POST['adr_mail'];
							//l'expéditeur
							$mailHeaders = "From: LCS";
							//envoi mail
							 //mb_send_mail($mailTo, $mailSubject, $mailBody, $mailHeaders);
							 mb_send_mail($mailTo2, $mailSubject, $mailBody, $mailHeaders);
					
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
	<? if ($adresse!="")
	echo "<p>Pour annuler la redirection, vider le champs ci-dessous et valider.</p>";?>	
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
  mysql_close();
  include ("../lcs/includes/pieds_de_page.inc.php");
?>
