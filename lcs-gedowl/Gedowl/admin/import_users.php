<?php

/**
 * import_users.php
 * 
 * Author: Steve Bourgeois <owl@bozzit.com>
 * Project Founder: Chris Vincent <cvincent@project802.net>
 *
 * Copyright (c) 1999-2005 The Owl Project Team
 * Licensed under the GNU GPL. For full terms see the file COPYING.
 */

require_once(dirname(dirname(__FILE__)) . "/config/owl.php");
require_once($default->owl_fs_root . "/lib/Net_CheckIP/CheckIP.php");
require_once($default->owl_fs_root . "/lib/disp.lib.php");
require_once($default->owl_fs_root . "/lib/owl.lib.php");
require_once($default->owl_fs_root . "/lib/security.lib.php");
include_once($default->owl_fs_root . "/lib/header.inc");
include_once($default->owl_fs_root . "/lib/userheader.inc");

if (!fIsAdmin(true))
{
   die("$owl_lang->err_unauthorized");
} 

if (!empty($userfile))
{
   $userfile = uploadCompat("userfile");
}

print("<center>");
print("<table class=\"border1\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"$default->table_expand_width\"><tr><td align=\"left\" valign=\"top\" width=\"100%\">\n");
fPrintButtonSpace(12, 1);
print("<br />\n");
print("<table class=\"border2\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\"><tr><td align=\"left\" valign=\"top\" width=\"100%\">\n");

if ($default->show_prefs == 1 or $default->show_prefs == 3)
{
   fPrintPrefs("infobar1", "top");
}

fPrintButtonSpace(12, 1);
print("<br />\n");

fPrintAdminPanel("importusers");

print("<form  action=\"" . $_SERVER["PHP_SELF"] ."\" method=\"post\">\n");
print("<input type=\"hidden\" name=\"sess\" value=\"$sess\"></input>");
print("<table class=\"margin2\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">\n");
print("<tr>\n");
print("</tr>\n");
fPrintSectionHeader($owl_lang->header_csv_import, "admin2");
print("<tr>\n");
print("<td align=\"left\" valign=\"top\">\n");
print("<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">\n");
print("<tr>\n");

//Import des utilisateurs
if (isset($_POST['Importer']))
	{
	//suppression des documents,des utilisateurs,initialisation de la base de données
	$fichier_script_sudo = "/usr/share/lcs/scripts/execution_script_plugin.sh";
	$script = './init.sh';
	$cmd = "/usr/bin/sudo -H -u root " . $fichier_script_sudo . " " . $script;
	exec($cmd,$lignes_retournees,$ret_val);
		
	
	
	//fichiers nécessaires à l'exploitation de l'API
	$BASEDIR="/var/www";
	//include "../Includes/basedir.inc.php";
	include "$BASEDIR/lcs/includes/headerauth.inc.php";
	include "$BASEDIR/Annu/includes/ldap.inc.php";
	include "$BASEDIR/Annu/includes/ihm.inc.php";  

	$qSQL = new OWL_db;
	$dNowDate = $qSQL->now();
	//initialisation d'un tableau
	$data=array();
	$grp_primaire= array ('Administratif','Prof','classe');
	$mess1="";
	for ($index=0; $index < count($grp_primaire); $index++)
		{
		$filtre=$grp_primaire[$index]."*";
		//recherche des groupes 
		$groups=search_groups('cn='.$filtre);
		if (count($groups))
			{    
			for ($loup=0; $loup < count($groups); $loup++)
			        {
				$grp=$groups[$loup]["cn"];
				//création du groupe
				$qSQL->query("SELECT * FROM $default->owl_groups_table WHERE name = '$grp'");
              			if ($qSQL->num_rows() == 0)
               				{
                 			 if ($qSQL->query("INSERT INTO $default->owl_groups_table (name) VALUES ('$grp')"))
                 			 $mess1.= "<B><u>Groupe $grp cr&eacute;&eacute; :</u></B><br>";
                 			 else 
                 				 {
                 				 $mess1.= "Echec lors de la cr&eacute;ation du groupe $grp <br>";
                 				 continue;
                 				 }
                 			 }
					//récupération de l'id du groupe créé
					$qSQL->query("SELECT * FROM $default->owl_groups_table WHERE name = '$grp'");
					$qSQL->next_record();
					$id_grp=$qSQL->f("id");
					//recherche des membres
					$uids = search_uids ("(cn=".$groups[$loup]["cn"].")", "half");
		  			$people = search_people_groups ($uids,"(sn=*)","cat");
		  			 for ($loop=0; $loop <count($people); $loop++) 
		  			 	{
		      				$uname = $people[$loop]['uid'];
		      				$nom = addslashes($people[$loop]["fullname"]);
		      				//insertion des membres dans la table users	
					       if ($qSQL->query("INSERT INTO $default->owl_users_table (`groupid`, `username`, `name`, `password`, `quota_max`, `quota_current`,
					       `email`,`notify`, `attachfile`, `disabled`, `noprefaccess`, `language`, `maxsessions`, `lastlogin`, `curlogin`, `lastnews`, `newsadmin`,
					       `comment_notify`,`buttonstyle`, `homedir`, `firstdir`, `email_tool`, `change_paswd_at_login`, `login_failed`, `passwd_last_changed`,
					       `expire_account`, `user_auth`,`logintonewrec`, `groupadmin`, `user_offset`, `useradmin`, `viewlogs`, `viewreports`) VALUES 
			       			( '$id_grp', '$uname', '$nom', 'd41d8cd98f00b204e9800998ecf8427e', '20971520', '0', '$uname', '1', '0', '0', '1', 'French', '0', $dNowDate, $dNowDate, 
			       			'0', '0', '1', 'rsdx_blue1', '1', '1', '0', '0', '0', $dNowDate, '', '0', '0', '0', '1', '0', '0', '0')"))
					      $mess1.= " $uname, "; 
					       }//fin d'insertion des membres
				       $mess1.="<BR>";
				       //si c'est une classe, affectation des profs 
				       if ($grp_primaire[$index]=='classe')
				       		{
				       		$mess1.="<b>Equipe</b> : ";			       
				       		//recherche des profs
							$filter2 = ereg_replace("Classe_","Equipe_",$grp);
    						$uids2 = search_uids ("(cn=".$filter2.")", "half");
    						$people2 = search_people_groups ($uids2,"(sn=*)","cat");
   		 					if (count($people2)) 
   		 						{
    							for ($loop=0; $loop < count($people2); $loop++) 
    								{
       								 if ($people2[$loop]["cat"] == "Equipe")
       					 				{       				 
       					 				$loginprof = $people2[$loop]["uid"];
       				 					//récupération de l'id du prof
										$qSQL->query("SELECT * FROM $default->owl_users_table WHERE username = '$loginprof'");
										if ($qSQL->num_rows() > 0)
            								{
											$qSQL->next_record();
											$id_prof=$qSQL->f("id");
											if ($qSQL->query("INSERT INTO $default->owl_users_grpmem_table (userid,groupadmin) VALUES ('$id_prof', '$id_grp')"))
											$mess1 .=$loginprof.", ";
											}
       				 					}
   			 						}
								}
							$mess1.="<BR>";
			      		} //fin d'affectation des profs de l'équipe
					}//fin de traitement d'une classe
				}//fin du traitement d'un groupe primaire
			else  $mess1.= "<h3 class='ko'> Erreur dans l'importation de $filtre<BR></h3>";
			}//fin du traitement des groupes primaires
		echo $mess1;
		$qSQL = new OWL_db;
		$qSQL->query("SELECT * FROM $default->owl_groups_table WHERE 1");
		$loop=0;
		while ($qSQL->next_record())
      		{        	
			$id_groupe[$loop]=$qSQL->f("id");
			$name_group[$loop]=$qSQL->f("name");
			$loop++;
			}
			for ($loop=0; $loop <count($name_group); $loop++) 
		  			 	{
		  			 	$id_gr=$id_groupe[$loop];
		  	if ($id_groupe[$loop]==0)
			$qSQL->query("INSERT INTO $default->owl_advanced_acl_table (group_id, folder_id, owlread, owlwrite, owlviewlog, owldelete, owlcopy, owlmove, 
			owlproperties ,owlupdate, owlcomment, owlcheckin, owlemail, owlrelsearch, owlsetacl, owlmonitor ) 
			VALUES ('$id_gr','1', '1', '1', '0', '1', '1', '1', '1', '0', '0', '0', '0', '0', '1', '1') ");		 	
			elseif (($name_group[$loop] == "Administratifs") || ($name_group[$loop]  == "Profs"))
			 {
			$qSQL->query("INSERT INTO $default->owl_advanced_acl_table (group_id, folder_id, owlread, owlwrite, owlviewlog, owldelete, owlcopy, owlmove,
			 owlproperties ,owlupdate, owlcomment, owlcheckin, owlemail, owlrelsearch, owlsetacl, owlmonitor ) 
			 VALUES ('$id_gr','1', '1', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0')  ");
			$qSQL->query("INSERT INTO $default->owl_advanced_acl_table (group_id, file_id, owlread, owlwrite, owlviewlog, owldelete, owlcopy, owlmove,
			 owlproperties ,owlupdate, owlcomment, owlcheckin, owlemail, owlrelsearch, owlsetacl, owlmonitor ) 
			 VALUES('$id_gr', '1', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0') ");
			}
			elseif (ereg("^Classe", $name_group[$loop])) 
			$qSQL->query("INSERT INTO $default->owl_advanced_acl_table (group_id, folder_id, owlread, owlwrite, owlviewlog, owldelete, owlcopy, owlmove, 
			owlproperties ,owlupdate, owlcomment, owlcheckin, owlemail, owlrelsearch, owlsetacl, owlmonitor ) 
			VALUES ('$id_gr','1', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0') ");
			
		}
		
		$qSQL->query("INSERT INTO $default->owl_files_table (id, name, filename, f_size, creatorid, parent, created, description, metadata, security, groupid, smodified, 
		checked_out, major_revision, minor_revision, url, password, doctype, updatorid, linkedto, approved) VALUES 
		('1', 'Exemple d''utilisation', 'A_LIRE.pdf', '517777', '1', '1', '2008-04-13 00:09:12', '', ' ', '6', '0', '2008-04-13 00:10:59', '1', '1', '0', '0', '', '1', '1', '0', '1')");


		echo "<br><u>Import annuel termin&eacute;</u>";	
		}//fin de l'import
		
				
	else
	{	
print("<td class=\"form1\"><P><FONT COLOR='#dc2300'>Attention</FONT> : L'import annuel</P>
<UL>
	<LI><P>supprime tous les documents</P>
	<LI><P>supprime tous les utilisateurs et  groupes</P>
	<LI><P>importe les utilisateurs et les groupes depuis le LDAP</P>
	<LI><P>affecte les utilisateurs &agrave; leurs groupes</P>
</UL></td>\n");
//print("<td class=\"form1\" width=\"100%\"><input class=\"finput1\" type=\"file\" name=\"userfile\" size=\"24\" maxlength=\"512\"></input></td>\n");
//print("<td class=\"form1\" width=\"100%\">La mise &agrave; jour aligne la base utilisateurs sur celle du LDAP : </td>\n");
/*print("<td class=\"form2\" width=\"100%\">");
fPrintSubmitButton($owl_lang->btn_submit, $owl_lang->alt_submit, "submit");
print("</td>\n");

print("<td class=\"form1\">");
fPrintButtonSpace(1, 1);
print("</td>\n");*/
print("</tr>\n");
print("<tr>\n");
print("<td class=\"form2\" width=\"100%\">");
fPrintSubmitButton($owl_lang->btn_import, $owl_lang->alt_import, "submit","Importer");
fPrintButtonSpace(1, 1);
print("</td>\n");
print("</tr>\n");
}
//############################

fPrintSectionHeader($owl_lang->header_csv_maj, "admin2");
print("<tr>\n");
print("<td align=\"left\" valign=\"top\">\n");
print("<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">\n");
print("<tr>\n");
flush();
//Import des utilisateurs
if (isset($_POST['maj']))
	{
	//fichiers nécessaires à l'exploitation de l'API
	$BASEDIR="/var/www";
	//include "../Includes/basedir.inc.php";
	include "$BASEDIR/lcs/includes/headerauth.inc.php";
	include "$BASEDIR/Annu/includes/ldap.inc.php";
	include "$BASEDIR/Annu/includes/ihm.inc.php";  

	$qSQL = new OWL_db;
	$dNowDate = $qSQL->now();
	//initialisation d'un tableau
	$data=array();
	$grp_primaire= array ('Administratif','Prof','classe');
	echo "<u>D&eacute;but de la mise &agrave; jour :</u> <BR>";
	flush();
	$mess1="";
	for ($index=0; $index < count($grp_primaire); $index++)
		{
		$filtre=$grp_primaire[$index]."*";
		//recherche des groupes 
		$groups=search_groups('cn='.$filtre);
		if (count($groups))
			{    
			for ($loup=0; $loup < count($groups); $loup++)
			        {
					$grp=$groups[$loup]["cn"];
					//récupération de l'id du groupe 
					$qSQL->query("SELECT * FROM $default->owl_groups_table WHERE name = '$grp'");
					$qSQL->next_record();
					$id_grp=$qSQL->f("id");
					//recherche des membres
					$uids = search_uids ("(cn=".$groups[$loup]["cn"].")", "half");
		  			$people = search_people_groups ($uids,"(sn=*)","cat");
		  			 for ($loop=0; $loop <count($people); $loop++) 
		  			 	{
		      				$uname = $people[$loop]['uid'];
		      				$nom = addslashes($people[$loop]["fullname"]);
		      				$qSQL2 = new OWL_db;
		      				$qSQL2->query("SELECT * FROM $default->owl_users_table WHERE username = '$uname'");
							if ($qSQL2->num_rows() == 0)
            				{			
            				//insertion  dans la table users	
					       if ($qSQL->query("INSERT INTO $default->owl_users_table (`groupid`, `username`, `name`, `password`, `quota_max`, `quota_current`,
					       `email`,`notify`, `attachfile`, `disabled`, `noprefaccess`, `language`, `maxsessions`, `lastlogin`, `curlogin`, `lastnews`, `newsadmin`,
					       `comment_notify`,`buttonstyle`, `homedir`, `firstdir`, `email_tool`, `change_paswd_at_login`, `login_failed`, `passwd_last_changed`,
					       `expire_account`, `user_auth`,`logintonewrec`, `groupadmin`, `user_offset`, `useradmin`, `viewlogs`, `viewreports`) VALUES 
			       			( '$id_grp', '$uname', '$nom', 'd41d8cd98f00b204e9800998ecf8427e', '20971520', '0', '', '0', '0', '0', '1', 'French', '0', $dNowDate, $dNowDate, 
			       			'0', '0', '0', 'rsdx_blue1', '1', '1', '0', '0', '0', $dNowDate, '', '0', '0', '0', '1', '0', '0', '0')"))
					      $mess1.= "- Ajout de <b>$nom</b> dans le groupe $grp <BR> "; 
					       }
					       }//fin d'insertion nouvel utilisateur
				       
				       //si prof affectation aux groupes classes 
				       if ($grp_primaire[$index]=='classe')
				       		{
				       					       
				       		//recherche des profs
							$filter2 = ereg_replace("Classe_","Equipe_",$grp);
    						$uids2 = search_uids ("(cn=".$filter2.")", "half");
    						$people2 = search_people_groups ($uids2,"(sn=*)","cat");
   		 					if (count($people2)) 
   		 						{
    							for ($loop=0; $loop < count($people2); $loop++) 
    								{
       								 if ($people2[$loop]["cat"] == "Equipe")
       					 				{       				 
       					 				$loginprof = $people2[$loop]["uid"];
       				 					//récupération de l'id du prof
										$qSQL->query("SELECT * FROM $default->owl_users_table WHERE username = '$loginprof'");
										if ($qSQL->num_rows() > 0)
            								{
											$qSQL->next_record();
											$id_prof=$qSQL->f("id");
											$NOM=$qSQL->f("name");
											$qSQL3 = new OWL_db;
											$qSQL3->query("SELECT * FROM $default->owl_users_grpmem_table WHERE userid = '$id_prof' AND groupadmin ='$id_grp'");
											if ($qSQL3->num_rows() == 0)
            								{
            								if ($qSQL->query("INSERT INTO $default->owl_users_grpmem_table (userid,groupadmin) VALUES ('$id_prof', '$id_grp')"))
											$mess1 .="- Affectation de<b> ".$NOM." </b>&agrave; $filter2 <BR> ";
											}
											}
       				 					}
   			 						}
								}
							
			      		} //fin d'affectation des profs de l'équipe
					}//fin de traitement d'une classe
				}//fin du traitement d'un groupe primaire
			else  $mess1.= "<h3 class='ko'> Erreur dans l'importation de $filtre<BR></h3>";
			}//fin du traitement des groupes primaires
		echo $mess1."<br><u> Mise &agrave; jour termin&eacute;e.</u>";	
		}//fin de la maj

//###############################
else
{
print("<td class=\"form1\">
<UL>
	<LI><P>La mise &agrave; jour ajoute les utilisateurs du LDAP qui ne
	sont pas pr&eacute;sent dans la base et les affecte &agrave; leurs
	groupes.</P>
	<LI><P>La suppression d'un utilisateur &eacute;tant conditionnn&eacute;e
	par l'absence de documents lui appartenant, cette op&eacute;ration
	ne peut &ecirc;tre effectu&eacute;e que manuellement dans  le menu
	Admin.</P>
</UL></td>\n");
print("</tr>\n");
print("<tr>\n");
print("<td class=\"form2\" width=\"100%\">");
fPrintSubmitButton($owl_lang->btn_maj, $owl_lang->alt_maj, "submit", "maj");
fPrintButtonSpace(1, 1);
print("</td>\n");
print("</tr>\n");
}

if (!empty($userfile))
{
   define( 'GROUPID', '0');
   define( 'USERNAME', '1');
   define( 'FULLNAME', '2');
   define( 'PASSWORD', '3');
   define( 'MAXSESSION', '12');
    
   $handle = fopen ($userfile["tmp_name"],"r");
   $qSQL = new OWL_db;
   $dNowDate = $qSQL->now();
   $row = 0;
   $CountLines = 0;
   
   while ($data = fgetcsv ($handle, 5000, ",")) 
   {
      if ($row == 0)
      {
         $row++;
         continue;
      } 
      $CountLines++;
      $PrintLines = $CountLines % 2;
      if ($PrintLines == 0)
      {
         $sTrClass = "file1";
      }
      else
      {
         $sTrClass = "file2";
      }

      $query = "INSERT INTO $default->owl_users_table (login_failed, passwd_last_changed, lastlogin,curlogin,groupid,username,name,password,quota_max,quota_current,email,notify,attachfile,disabled,noprefaccess,language,maxsessions,newsadmin,comment_notify,buttonstyle,homedir,firstdir,email_tool, change_paswd_at_login, expire_account, user_auth, logintonewrec) VALUES ('0', $dNowDate, $dNowDate, $dNowDate, ";
   
      $num = count ($data);
   
      //print "<p> $num fields in line $row: <br>\n";
      $row++;
      $bSkipUser = false;
      for ($c=0; $c < $num; $c++)  
      {
         if ($c > GROUPID )
         {
            $query .= ", ";
         }
   
         if ( $c == GROUPID )
         {
            if (is_numeric($data[$c]))
            {
               $iSaveGroupid = $data[$c];
               $qSQL->query("SELECT * FROM $default->owl_groups_table WHERE id = '$data[$c]'");
               if ($qSQL->num_rows() == 0)
               {
                  $sMessage = $owl_lang->invalid_groupid . "'$data[$c]'";
                  $bSkipUser = true;
               }
            }
            else
            {
               $qSQL->query("SELECT * FROM $default->owl_groups_table WHERE name = '$data[$c]'");
               if ($qSQL->num_rows() == 0)
               {
                  $qSQL->query("INSERT INTO $default->owl_groups_table (name) VALUES ('$data[$c]')");
                  print("<tr>\n");
                  print("<td class=\"$sTrClass\">$owl_lang->group_create</td>\n");
                  print("<td class=\"$sTrClass\" width=\"100%\">$data[$c]</td>\n");
                  print("</tr>\n");
                  $data[$c] = $qSQL->insert_id($default->owl_groups_table, 'id');
                  $CountLines++;
                  $PrintLines = $CountLines % 2;
                  if ($PrintLines == 0)
                  {
                     $sTrClass = "file1";
                  }
                  else
                  {
                     $sTrClass = "file2";
                  }
               }
               else
               {
                  $qSQL->next_record();
                  $data[$c] = $qSQL->f("id");
               }
               $iSaveGroupid = $data[$c];
            }
         }
         elseif ( $c == PASSWORD )
         {
            $data[$c] = md5($data[$c]);
         }
         elseif ( $c == MAXSESSION )
         {
            $data[$c] = $data[$c] - 1;
         }
         elseif ( $c == USERNAME )
         {
            $sUserName = $data[$c];
            $qSQL->query("SELECT * FROM $default->owl_users_table WHERE username = '$sUserName'");
            if ($qSQL->num_rows() > 0)
            {
                  $bSkipUser = true;
                  $sMessage = $owl_lang->msg_user_exists;
            }
         }
         elseif ( $c == FULLNAME )
         {
            $sFullName = $data[$c];
         }
         $newdata = ereg_replace("'","\'",$data[$c]);
         $query .= "'$newdata'";
       }
       $query .= ")";
   
       print("<tr>\n");
       print("<td class=\"$sTrClass\">$owl_lang->user_created_skipped</td>\n");
       if (!$bSkipUser)
       {
          $qSQL->query($query);
          $iUserID = $qSQL->insert_id();
          $qSQL->query("INSERT INTO $default->owl_users_grpmem_table (userid,groupid) VALUES ('$iUserID', '$iSaveGroupid')");
          print("<td class=\"$sTrClass\" width=\"100%\">$sUserName ($sFullName) $owl_lang->import_inserted</td>\n");
       }
       else
       {
          print("<td class=\"$sTrClass\" width=\"100%\">$sUserName ($sFullName) $owl_lang->import_skipped $sMessage</td>\n");
       }
       print("</tr>\n");
    
   }
   fclose ($handle);
   unlink($userfile["tmp_name"]);
}
print("</table>\n");
print("</td></tr></table>\n");
print("</form>\n");

fPrintButtonSpace(12, 1);
if ($default->show_prefs == 2 or $default->show_prefs == 3)
{
   fPrintPrefs("infobar2");
}
print("</td></tr></table>\n");
print("</center>");

include($default->owl_fs_root .  "/lib/footer.inc");

?>
