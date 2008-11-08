<?php
/* =============================================
   Projet LCS : Linux Communication Server
   Consultation de l'annuaire LDAP
   Annu/people.php
   [LCS CoreTeam]
   « jLCF >:> » jean-luc.chretien@tice.ac-caen.fr
   « oluve » olivier.le_monnier@crdp.ac-caen.fr
   Equipe Tice academie de Caen
   Derniere mise a jour : 08/11/2008
   ============================================= */
  include "../lcs/includes/headerauth.inc.php";
  include "includes/ldap.inc.php";
  include "includes/ihm.inc.php";

 
  list ($idpers,$login)= isauth();
  if ($idpers == "0") header("Location:$urlauth");
  header_html();
  aff_trailer ("31");
  if (is_admin("Annu_is_admin",$login)=="Y")
	  {
	  list($user, $groups)=people_get_variables($uid, true);
	  //premiere passe -> affichage du formulaire
	  if (!isset($_POST['delgrp']))
	  {echo "<H2>".$user["fullname"]."</H2>\n";
	  if ($user["description"]) echo "<p>".$user["description"]."</p>";
	  //Affichage des groupes
	  if ( count($groups) ) 
		{
		echo "<U>Membre des groupes</U> :<BR>\n";
		echo '<form action="del_group_user.php?uid='.$uid.'" method="post">';
		for ($loop=0; $loop < count ($groups)  ; $loop++)
			{
			if ( ($groups[$loop]["cn"] != "Profs") && ($groups[$loop]["cn"] != "Eleves") && ($groups[$loop]["cn"] != "Administratifs") )
				{
				echo "<input type='checkbox' name='grp[]' value='".$groups[$loop]["cn"]."' >";
				if ($groups[$loop]["type"]=="posixGroup")
				echo "<font color=\"#1E90FF\"><STRONG>".$groups[$loop]["cn"]."</STRONG> </font>";
				echo "<font size=\"-2\"> ".$groups[$loop]["description"];
				$login1=split ("[\,\]",ldap_dn2ufn($groups[$loop]["owner"]),2);
				if ( $uid == $login1[0] ) echo "<strong><font color=\"#ff8f00\">&nbsp;(professeur principal)</font></strong>";
				echo "</font><BR>\n";
				}
			}
		echo "<h4>Supprimer les groupes d'appartenance s&#233;lectionn&#233;s </h4>";
		echo '<p align ="center"><input type="submit" name="delgrp" value="Lancer la requête"></p></form>';
			
		}
		}//fin du formulaire
		
		else //deuxieme passe ->Traitement du formulaire
			{
			$grps=$_POST['grp'];
	
			// Affichage message d'erreur si aucun groupe selectionne
			if (!count($grps) )
				{
				echo "<div class=error_msg>
					  Vous devez s&#233;lectionner au moins un groupe &#224; supprimer !
					</div>\n";
				}
			else 
				{
				//suppression des groupes
				$useruid=$user['uid'];
				$nomcplt=$user["fullname"];
				echo "Retrait de l'utilisateur <a href='people.php?uid=$uid'>$nomcplt</a> ";
				if (count($grps) > 1  )
					echo "des groupes secondaires</a> :<BR><BR>\n";
					else echo "du groupe secondaire</a><BR><BR>\n";
		
				for ($loop=0; $loop < count($grps); $loop++  ) {
					exec ("$scriptsbinpath/groupDelUser.pl $useruid $grps[$loop] ",$AllOutPut,$ReturnValue);
					echo $grps[$loop]."&nbsp;\t\t";
					if ($ReturnValue == 0 ) 
						echo "<stong><strong>R&#233;ussi</strong></strong><BR>\n";
						else  echo "<font color=\"orange\">Echec</font><BR>\n";}
						
				}
			}
			
	
	}

	else//si pas admin 
	{
    echo "<div class=error_msg>Cette application, n&#233;cessite les droits d'administrateur du serveur LCS !</div>";
	}	
  include ("../lcs/includes/pieds_de_page.inc.php");
?>
