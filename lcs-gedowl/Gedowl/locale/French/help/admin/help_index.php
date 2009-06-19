<?php

require_once("../../../../config/owl.php");
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
<img src="../../../../graphics/<? echo $default->sButtonStyle ?>/ui_misc/owl_logo1.gif" border="0" alt="Owl Logo"></img><br /></center>
<hr></hr>

<!-- Help Begins Here -->

<h1> Menu Admin </h1>

<P>Le plugin GEDOWL a &eacute;t&eacute; enti&egrave;rement pr&eacute;
-configur&eacute; et peut &ecirc;tre utilis&eacute; en l'&eacute;tat.</P>
<P>Cette interface vous sera utile :</P>
<UL>
	<LI><P>pour importer les utilisateurs et les groupes &agrave; partir
	de l'annuaire LDAP</P>
</UL>
<UL>
	<LI><P>si vous devez changer les donn&eacute;es d'un utilisateur
	(quota, etc...) ou d'un groupe.</P>
	<LI><P>consulter les logs, les stats, etc ..</P>
</UL>
<P>Pour ajouter un (des) utilisateur()s, il est pr&eacute;f&eacute;rable
de l'(es) ajouter dans l'annuaire du LCS et de faire une mise &agrave;
jour de la base GEDOWL (admin--&gt;Importer les utilisateurs--&gt;Mise
&agrave; jour). En proc&eacute;dant ainsi les nouveaux utilisateurs
&laquo;&nbsp;h&eacute;riteront des bons droits&nbsp;&raquo; en
fonction de leur statut : Administratifs, Profs o&ugrave; &eacute;l&egrave;ves.
</P>
<P>De m&ecirc;me pour supprimer un utilisateur, il suffit de
l'enlever de l'annuaire du LCS. Il est INUTILE de l'enlever de la
base GEDOWL, car sans login LCS, il ne pourra plus acc&eacute;der &agrave;
GEDOWL</P>
<P><BR><BR>

<!-- Help Ends Here -->

<?php

      fPrintButtonSpace(12, 1);
                                                                                                                                                                                                    
      if ($default->show_prefs == 2 or $default->show_prefs == 3)
      {
         fPrintPrefs("infobar2");
      }
      print("</td></tr></table>\n");
      include("../../../lib/footer.inc");
?>
