<?php

require_once("../../../config/owl.php");
require_once("$default->owl_fs_root/lib/disp.lib.php");
require_once("$default->owl_fs_root/lib/owl.lib.php");
require_once("$default->owl_fs_root/lib/security.lib.php");
include_once("$default->owl_fs_root/lib/header.inc");
include_once("$default->owl_fs_root/lib/userheader.inc");


   $urlArgs = array();
   $urlArgs['sess']      = $sess;
   $urlArgs['parent']    = $parent;
   $urlArgs['expand']    = $expand;
   $urlArgs['order']     = $order;
   $urlArgs[$sortorder] = $sortname;

   if(empty($expand))
   {
      $expand = $default->expand;
   }
   
    if ($expand == 1)
   {
      print("<table class='border1' cellspacing='0' cellpadding='0' border='0' width='$default->table_expand_width'><tr><td align='left' valign='top' width='100%'>\n");
   }
   else
   {
      print("<table class='border1' cellspacing='0' cellpadding='0' border='0' width='$default->table_collapse_width'><tr><td align='left' valign='top' width='100%'>\n");
   }
   fPrintButtonSpace(12, 1);
   print("<br />\n");
   print("<table class='border2' cellspacing='0' cellpadding='0' border='0' width='100%'><tr><td align='left' valign='top' width='100%'>\n");
                                                                                                                                                                                          
  if ($default->show_prefs == 1 or $default->show_prefs == 3)
  {
         fPrintPrefs("infobar1", "top");
  }
   fPrintButtonSpace(12, 1);
   print("<br />\n");
?>

<center><h1><?php echo $owl_lang->alt_btn_help ?></h1>
<img src="../../../graphics/<? echo $default->sButtonStyle ?>/ui_misc/owl_logo1.gif" border="0" alt="Owl Logo"></img><br /></center>
<hr></hr>

<!-- Help Begins Here -->
<H1> Selection des groupes/utilisateurs </H1>

<OL>
	<LI><P><B>S&eacute;lection des groupes</B></P>

<UL>
	<LI><P>Les groupes s&eacute;lectionn&eacute;s apparaissent dans la
	liste de droite</P>
	<LI><P>En s&eacute;lectionnant un groupe, vous attribuerez <U>les
	m&ecirc;mes permissions</U> &agrave; tous les membres de ce groupe 
	</P>
	<LI><P>En utilisant la touche CTRL, vous pouvez s&eacute;lectionner
	plusieurs groupes &agrave; d&eacute;placer</P>
	<LI><P>Utiliser les boutons <?print("&nbsp;&nbsp;<input type=\"button\" value=\"-->\"></input>\n");?> , <?print("&nbsp;&nbsp;<input type=\"button\" value=\"<--\"></input>\n");?> pour ajouter ou enlever un groupe
	&agrave; la liste</P>
	<LI><P>Cliquer sur  <?print("<input class=\"fbuttonup1\"   value=\"$owl_lang->acl_set_selected\" >\n"); ?> . Cela mettra &agrave; jour la liste des
	utilisateurs disponibles</P>
	<UL>
		<UL>
			<LI><P>les membres des groupes enlev&eacute;s seront ajout&eacute;s
			&agrave; la liste des utilisateurs</P>
			<LI><P>les membres des groupes ajout&eacute;s seront enlev&eacute;s
			&agrave; la liste des utilisateurs<BR><BR><BR>
			</P>
		</UL>
	</UL>
</UL>

	<LI><P><B>S&eacute;lection des utilisateurs</B></P>

<UL>
	<LI><P>Utiliser cette fonction si vous voulez attribuer des
	permissions &agrave; certains membres d'un groupe</P>
	<LI><P>Utiliser les boutons <?print("&nbsp;&nbsp;<input type=\"button\" value=\"-->\"></input>\n");?> , <?print("&nbsp;&nbsp;<input type=\"button\" value=\"<--\"></input>\n");?> pour ajouter ou enlever un
	utilisateur &agrave; la liste</P>
</UL>
<P></P>

	<LI><P><B>Validation</B></P>

<UL>
	<LI><P>Lorsque la liste des groupes et des utilisateurs vous
	convient, cliquer sur <?print("<input class=\"fbuttonup1\"   value=\"$owl_lang->acl_set_selected\" >\n"); ?>
	</P>
</UL>
	<LI><P><B>Attribution des permissions</B></P>

<UL>
	<LI><P>Apr&egrave;s validation , un tableau de cases &agrave; cocher
	vous permet d'attribuer des permissions &agrave; chaque groupe et
	utilisateur  (case coch&eacute;e=permission attribu&eacute;e)</P>
	<P></P>
</UL>
<P></P>
</OL>


<H1> Permissions </H1>
<?
print("<table class=\"margin2\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">\n");
      print("<tr>\n<td align=\"left\" valign=\"top\">");
      print("<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">");
      fPrintSectionHeader("$owl_lang->acl_set_file_permissions");
      print("</table>");
      print("</td></tr></table>");
      print("<table class=\"margin2\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">\n");
      print("<tr>\n<td align=\"left\" valign=\"top\">");
      print("<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">");
      print("<tr>\n");
      //print("<tr>\n<td class=\"title1\" align=\"center\"><b>$owl_lang->acl_heading_file</b></td>\n</tr>\n");
      print("<tr>\n<td class=\"title1\" align=\"center\" width=\"15%\"><a class=\"ltitle1\" >$owl_lang->acl_file_read</a></td>\n<td> : voir, t&eacute;l&eacute;charger le fichier (si l'utilisateur n'a pas cette permission, le fichier sera
invisible dans l'explorateur )</td>\n</tr>\n");
      print("<tr>\n<td class=\"title1\" align=\"center\"><a class=\"ltitle1\" width=\"15%\">$owl_lang->acl_file_update</a></td>\n<td> : modifier le fichier</td>\n</tr>\n");
      print("<tr>\n<td class=\"title1\" align=\"center\"><a class=\"ltitle1\" width=\"15%\">$owl_lang->acl_file_set_acl</a></td>\n<td> : modifier les permissions sur le fichier</td>\n</tr>\n");
      print("<tr>\n<td class=\"title1\" align=\"center\"><a class=\"ltitle1\" width=\"15%\">$owl_lang->acl_file_delete</a></td>\n<td> : supprimer le fichier</td>\n</tr>\n");
      print("<tr>\n<td class=\"title1\" align=\"center\"><a class=\"ltitle1\" width=\"15%\">$owl_lang->acl_file_copy</a></td>\n<td> : copier le fichier</td>\n</tr>\n");
      print("<tr>\n<td class=\"title1\" align=\"center\"><a class=\"ltitle1\" width=\"15%\">$owl_lang->acl_file_move</a></td>\n<td> : d&eacute;placer le fichier</td>\n</tr>\n");
      print("<tr>\n<td class=\"title1\" align=\"center\"><a class=\"ltitle1\" width=\"15%\">$owl_lang->acl_file_modify</a></td>\n<td> : modifier les propri&eacute;t&eacute;s</td>\n</tr>\n");
      print("<tr>\n<td class=\"title1\" align=\"center\"><a class=\"ltitle1\" width=\"15%\">$owl_lang->acl_file_view_log</a></td>\n<td> : voir les diff&eacute;rences entre les diff&eacute;rentes versions d'un fichier</td>\n</tr>\n");
      print("<tr>\n<td class=\"title1\" align=\"center\"><a class=\"ltitle1\" width=\"15%\">$owl_lang->acl_file_comment</a></td>\n<td> : ajouter un commentaire</td>\n</tr>\n");
      print("<tr>\n<td class=\"title1\" align=\"center\"><a class=\"ltitle1\" width=\"15%\">$owl_lang->acl_file_checkin</a></td>\n<td> : verrouiller/d&eacute;verrouiller  le fichier pour
interdire/autoriser les modifications</td>\n</tr>\n");
      print("<tr>\n<td class=\"title1\" align=\"center\"><a class=\"ltitle1\" width=\"15%\">$owl_lang->acl_file_email</a></td>\n<td> : envoyer le fichier par mail  </td>\n</tr>\n");
      print("<tr>\n<td class=\"title1\" align=\"center\"><a class=\"ltitle1\" width=\"15%\">$owl_lang->acl_file_search</a></td>\n<td> : chercher les documents en rapport</td>\n</tr>\n");
      print("<tr>\n<td class=\"title1\" align=\"center\"><a class=\"ltitle1\" width=\"15%\">$owl_lang->acl_file_monitor</a></td>\n<td> : surveiller&nbsp; les modifications du fichier et recevoir les
notifications par mail (l'utilisateur doit ensuite activer la surveillance sur le fichier)</td>\n</tr>\n");
      print("</tr>\n");
print("</table></table><p></p>");
//permissions dossiers 
		print("<table class=\"margin2\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">\n");
      print("<tr>\n<td align=\"left\" valign=\"top\">");
      print("<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">");
      fPrintSectionHeader("$owl_lang->acl_set_folder_permissions");
      print("</table>");
      print("</td></tr></table>");

      print("<table class=\"margin2\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">\n");
      print("<tr>\n<td align=\"left\" valign=\"top\">");
      print("<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">");
      print("<tr>\n");
      //print("<tr>\n<td class=\"title1\" align=\"center\"><b>$owl_lang->acl_heading_folder</b></td>\n");
      print("<tr>\n<td class=\"title1\" align=\"center\"><a class=\"ltitle1\" width=\"15%\">$owl_lang->acl_folder_read</a></td>\n</tr>\n");
      print("<tr>\n<td class=\"title1\" align=\"center\"><a class=\"ltitle1\" width=\"15%\">$owl_lang->acl_folder_write</a></td>\n</tr>\n");
      print("<tr>\n<td class=\"title1\" align=\"center\"><a class=\"ltitle1\" width=\"15%\">$owl_lang->acl_folder_delete</a></td>\n</tr>\n");
      print("<tr>\n<td class=\"title1\" align=\"center\"><a class=\"ltitle1\" width=\"15%\">$owl_lang->acl_folder_copy</a></td>\n<td width=\"84%\"> : les permissions ont la m&ecirc;me signification que celles des fichiers  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>\n</tr>\n");
      print("<tr>\n<td class=\"title1\" align=\"center\"><a class=\"ltitle1\" width=\"15%\">$owl_lang->acl_folder_move</a></td>\n</tr>\n");
      print("<tr>\n<td class=\"title1\" align=\"center\"><a class=\"ltitle1\" width=\"15%\">$owl_lang->acl_folder_modify</a></td>\n</tr>\n");
      print("<tr>\n<td class=\"title1\" align=\"center\"><a class=\"ltitle1\" width=\"15%\">$owl_lang->acl_folder_set_acl</a></td>\n</tr>\n");
      print("<tr>\n<td class=\"title1\" align=\"center\"><a class=\"ltitle1\" width=\"15%\">$owl_lang->acl_folder_monitor</a></td>\n</tr>\n");
      print("<tr>\n</tr>\n");
		print("</table></table><p></p>");

?>

<!-- Help Ends Here -->

<?php

      fPrintButtonSpace(12, 1);
                                                                                                                                                                                                    
      if ($default->show_prefs == 2 or $default->show_prefs == 3)
      {
         fPrintPrefs("infobar2");
      }
      print("</td></tr></table>\n");
      include("../../../lib/footer.inc");
      print("</td></tr></table>\n");
?>
