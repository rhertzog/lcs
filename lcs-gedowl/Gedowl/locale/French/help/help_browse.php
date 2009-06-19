<?php
/*
 * help_browse.php
 *
 * Author: Steve Bourgeois owl@bozzit.com
 *
 * Copyright (c) 1999-2005 The Owl Project Team
 * Licensed under the GNU GPL. For full terms see the file COPYING.
 *
 */

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
  print("<center>");
 
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
<P>
<center>
Ce plugin  de Gestion Electronique de Documents (GED)  bas&eacute; sur OWL (<a href="http://owl.sourceforge.net/">http://owl.sourceforge.net/</a>.) <br>a &eacute;t&eacute; adapt&eacute; et pr&eacute;-configur&eacute; pour une utilisation sur LCS dans un &eacute;tablissement scolaire. <br>Mainteneur du plugin : <A HREF="mailto:philippe.leclerc1@ac-caen.fr?subject=GEDOWL">Philippe Leclerc : Equipe TICE CAEN</A></P>
</center>
<!-- Help Begins Here -->

<h2>Introduction</h2>
Owl est un syst&egrave;me de gestion de documents multi-utilisateurs. Les utilisateurs ont la possibilit&eacute; de d&eacute;poser des documents et de leur assigner des attributs. Les autres utilisateurs sont alors en mesure de localiser les documents, soit en utilisant l'arborescence des dossiers ou en utilisant le moteur de recherche interne.
<br />Un document peut &ecirc;tre n'importe quel type de document &eacute;lectronique ou  fichier . G&eacute;n&eacute;ralement ces documents sont des fichiers de traitement de texte, tableur, ou fichiers PDF, mais  il est possible de d&eacute;poser des fichiers graphiques, audio et la vid&eacute;o. Les utilisateurs du syst&egrave;me Owl disposent de nombreuses options:
<ul><li>Envoi de documents par mail</li><li>Surveillance des r&eacute;pertoires et des fichiers, avec notification des changements par mail </li><li>Utilisation du  Version Control System (VCS)  pour  rechercher les modifications, voir les diff&eacute;rentes versions et dans certains cas afficher les diff&eacute;rences entre les versions des fichiers.</li><li>Ajout de commentaires aux documents</li></ul>

Toutes ces fonctionnalit&eacute;s ne n&eacute;cessitent qu'un navigateur internet et sont donc ind&eacute;pendantes du syt&egrave;me d'exploitation.

<h2>Explorateur de fichiers</h2>
L'explorateur est le principal outil que vous allez utiliser pour naviguer dans  l'arborescence, trouver et utiliser les documents qui ont &eacute;t&eacute; d&eacute;pos&eacute;s dans le syst&egrave;me. Vous pouvez effectuer certaines actions sur les dossiers et les documents tels que  trier , consulter ou t&eacute;l&eacute;charger le document, envoi par mail...

<h2>Structure des r&eacute;pertoires</h2>
Les documents qui sont t&eacute;l&eacute;charg&eacute;s dans le syst&egrave;me Owl sont stock&eacute;s dans des dossiers et chaque dossier peut avoir une s&eacute;rie de sous-dossiers. Ce type de structure est  une arborecence et est g&eacute;n&eacute;ralement utilis&eacute;e pour le stockage et l'organisation des fichiers sur votre ordinateur
 La structure des dossiers est un moyen pratique de regrouper les documents de mani&egrave;re constructive. Par exemple, vous pouvez saisir l'ensemble de vos documents techniques pour un certain nombre de produits. Vous pourriez cr&eacute;er un dossier nomm&eacute; documents techniques et puis une s&eacute;rie de sous-dossiers dans les documents techniques de chaque produit. Pour que cela fonctionne correctement, vous devez choisir des noms de dossiers qui donnent &agrave; l'utilisateur une description suffisamment explicite.

<h3>Barre de titre</h3>

<table  style="width: 100%; text-align: left;" border="0" cellpadding="2" cellspacing="2">
<tr>
 <td><img src="../../../graphics/<? echo $default->sButtonStyle ?>/ui_icons/tg_check.gif" width="13" height="16" border="0" alt=""></img></td>
 <td>S&eacute;lectionner/D&eacute;selectionner les fichiers et dossiers pour diverses op&eacute;rations</td>
</tr>
<tr>
 <td>Status</td>
 <td>Les ic&ocirc;nes suivant peuvent &ecirc;tre affich&eacute;:</td>
</tr>
<tr>
 <td>&nbsp;</td>
 <td><a class="curl1">*</a>
     &nbsp;Le document a &eacute;t&eacute; index&eacute;, des recherches peuvent &ecirc;tre efffectu&eacute;es sur son contenu</td>
</tr>
<tr>
 <td>&nbsp;</td>
 <td><img src="../../../graphics/<? echo $default->sButtonStyle ?>/ui_icons/new.gif" width="13" height="16" border="0" alt=""></img>
     &nbsp;Le document a &eacute;t&eacute; ajout&eacute; depuis votre derni&egrave;re visite</td>
</tr>
<tr>
 <td>&nbsp;</td>
 <td><img src="../../../graphics/<? echo $default->sButtonStyle ?>/ui_icons/updated.gif" width="13" height="16" border="0" alt=""></img>
     &nbsp;Le document a &eacute;t&eacute; mis &agrave; jour depuis votre derni&egrave;re visite</td>
</tr>
<tr><td>&nbsp;</td>
 <td><img src="../../../graphics/<? echo $default->sButtonStyle ?>/icon_action/comment.gif" width="17" height="20" border="0" alt=""></img>
           Le document a &eacute;t&eacute; comment&eacute;</td>
</tr>

<tr>
 <td><?php echo $owl_lang->title ?></td>
 <td>Le titre du document ou du dossier.</td>
</tr>
<tr>
 <td><?php echo $owl_lang->ver  ?></td>
 <td>La version du document ou du dossier. Le nombre avant le point indique des changements importants alors que le nombre apr&egrave;s le point indique de l&eacute;g&egrave;res modifications.</td>
</tr>
<tr>
 <td><?php echo $owl_lang->file ?></td>
 <td>Le nom du fichier ou du dossier.</td>
</tr>
<tr>
 <td><?php echo $owl_lang->size ?></td>
 <td>La taille du fichier ou du dossier.</td>
</tr>
<tr>
 <td><?php echo $owl_lang->postedby ?></td>
 <td>L'utilisateur qui a d&eacute;poser le document.</td>
</tr>
<tr>
 <td><?php echo $owl_lang->modified ?></td>
 <td>La date et l'heure de la derni&egrave;re modification du document.</td>
</tr>
<tr>
 <td><?php echo $owl_lang->held ?></td>
 <td>Indique l'utilisateur qui a verouill&eacute; le document. Un document verrouill&eacute; ne peut plus &ecirc;te modifi&eacute;</td>
</tr>
</table>


<h3><?php echo $owl_lang->actions ?></h3>
 Le nombre d&#39;ic&ocirc;nes d&#39;action, qui sont visibles en pla&ccedil;ant le curseur de la souris sur le nom du fichier ou du dossier, d&eacute;pendra de vos autorisations sur  le dossier ou le document.
<h4>Boutons Action</h4>
<table  style="width: 100%; text-align: left;" border="0" cellpadding="2"
 cellspacing="2">
<tr>
 <td width = "3%"><img src="../../../graphics/<? echo $default->sButtonStyle ?>/icon_action/log.gif" width="17" height="20" border="0" alt=""></img> </td>
 <td><?php echo $owl_lang->alt_log_file ?></td>
</tr>
<tr>
 <td><img src="../../../graphics/<? echo $default->sButtonStyle ?>/icon_action/trash.gif" width="17" height="20" border="0" alt=""></img> </td>
 <td><?php echo $owl_lang->alt_del_file ?></td>
</tr>
<tr>
 <td><img src="../../../graphics/<? echo $default->sButtonStyle ?>/icon_action/edit.gif" width="17" height="20" border="0" alt=""></img> </td>
 <td><?php echo $owl_lang->alt_mod_file ?></td>
</tr>
<tr>
 <td><img src="../../../graphics/<? echo $default->sButtonStyle ?>/icon_action/link.gif" width="17" height="20" border="0" alt=""></img> </td>
 <td><?php echo $owl_lang->alt_link_file ?></td>
</tr>
<tr>
 <td><img src="../../../graphics/<? echo $default->sButtonStyle ?>/icon_action/copy.gif" width="16" height="16" border="0" alt=""></img> </td>
 <td><?php echo $owl_lang->alt_copy_file ?></td>
</tr>
<tr>
 <td><img src="../../../graphics/<? echo $default->sButtonStyle ?>/icon_action/move.gif" width="17" height="20" border="0" alt=""></img> </td>
 <td><?php echo $owl_lang->alt_move_file ?></td>
</tr>
<tr>
 <td><img src="../../../graphics/<? echo $default->sButtonStyle ?>/icon_action/update.gif" width="17" height="20" border="0" alt=""></img> </td>
 <td><?php echo $owl_lang->alt_upd_file ?></td>
</tr>
<tr>
 <td><img src="../../../graphics/<? echo $default->sButtonStyle ?>/icon_action/bin.gif" width="17" height="20" border="0" alt=""></img></td>
 <td><?php echo $owl_lang->alt_get_file ?></td>
</tr>
<tr>
 <td><img src="../../../graphics/<? echo $default->sButtonStyle ?>/icon_action/comment_dis.gif" width="17" height="20" border="0" alt=""></img></td>
 <td><?php echo $owl_lang->alt_add_comments ?></td>
</tr>
<tr>
 <td><img src="../../../graphics/<? echo $default->sButtonStyle ?>/icon_action/lock.gif" width="17" height="17" border="0" alt=""></img></td>
 <td><?php echo $owl_lang->alt_lock_file ?></td>
</tr>
<tr>
<td><img src="../../../graphics/<? echo $default->sButtonStyle ?>/icon_action/unlock.gif" width="16" height="16" border="0" alt=""></img></td>
 <td><?php echo $owl_lang->alt_unlock_file ?></td>
</tr>
<tr>
<td><img src="../../../graphics/<? echo $default->sButtonStyle ?>/icon_action/zip.gif" width="17" height="20" border="0" alt=""></img></td>
<td><?php echo $owl_lang->alt_btn_add_zip ?></td>
</tr>
<tr>
<td><img src="../../../graphics/<? echo $default->sButtonStyle ?>/icon_action/newcomment.gif" width="17" height="20" border="0" alt=""></img></td>
 <td><?php echo $owl_lang->alt_add_comments ?></td>
</tr>
<tr>
 <td><img src="../../../graphics/<? echo $default->sButtonStyle ?>/icon_action/email.gif" width="17" height="20" border="0" alt=""></img></td>
 <td><?php echo $owl_lang->alt_email ?></td>
</tr>
<tr>
 <td><img src="../../../graphics/<? echo $default->sButtonStyle ?>/icon_action/related.gif" width="17" height="20" border="0" alt=""></img></td>
 <td><?php echo $owl_lang->alt_related ?></td>
</tr>
<tr>
 <td><img src="../../../graphics/<? echo $default->sButtonStyle ?>/icon_action/mag.gif" width="17" height="20" border="0" alt=""></img></td>
 <td><?php echo $owl_lang->alt_view_file ?></td>
</tr>
<tr>
<td><img src="../../../graphics/<? echo $default->sButtonStyle ?>/icon_action/play.gif" width="17" height="20" border="0" alt=""></img></td>
 <td><?php echo $owl_lang->alt_play_file ?></td>
</tr>
<tr>
<td><img src="../../../graphics/<? echo $default->sButtonStyle ?>/icon_action/monitor.gif" width="17" height="20" border="0" alt=""></img></td>
 <td><?php echo $owl_lang->alt_monitor ?></td>
</tr>
<tr>
<td><img src="../../../graphics/<? echo $default->sButtonStyle ?>/icon_action/print.gif" width="17" height="20" border="0" alt=""></img></td>
 <td><?php echo $owl_lang->alt_news_print ?></td>
</tr>
</table>

<h4>Boutons divers</h4>
<p>Ces boutons vous permettent de faire des actions avec plusieurs fichiers en m&ecirc;me temps.
</p>
<table style="width: 100%; text-align: left;" border="0" cellpadding="2" cellspacing="2">
    <tr>
      <td>
<table>
<tr>
<td nowrap="nowrap"><?php fPrintSubmitButton($owl_lang->btn_bulk_download, $owl_lang->alt_btn_bulk_download, "reset", "") ?></td>
</tr>
</table>
      </td>
      <td>T&eacute;l&eacute;charger les fichiers s&eacute;lectionn&eacute;s.<br /></td>
    </tr>
 <tr>
      <td>
<table>
<tr>
<td nowrap="nowrap"><?php fPrintSubmitButton($owl_lang->btn_bulk_move, $owl_lang->alt_btn_bulk_move, "reset", "") ?></td>
</tr>
</table>
      </td>
      <td>D&eacute;placer les fichiers s&eacute;lectionn&eacute;s dans un autre dossier.<br /></td>
    </tr>
 <tr>
      <td>
<table>
<tr>
<td nowrap="nowrap"><?php fPrintSubmitButton($owl_lang->btn_bulk_email, $owl_lang->alt_btn_bulk_email, "reset", "") ?></td>
</tr>
</table>
      </td>
      <td>Envoyer les fichiers s&eacute;lectionn&eacute;s par mail.<br />
      </td>
    </tr>
 <tr>
      <td>
<table>
<tr>
<td nowrap="nowrap"><?php fPrintSubmitButton($owl_lang->btn_bulk_delete, $owl_lang->alt_btn_bulk_delete, "reset", "") ?></td>
</tr>
</table>
      </td>
      <td>Supprimer les fichiers s&eacute;lectionn&eacute;s.A noter que vous ne pouvez pas supprimer un dossier.<br />
      </td>
    </tr>
 <tr>
      <td>
<table>
<tr>
<td nowrap="nowrap"><?php fPrintSubmitButton($owl_lang->btn_bulk_checkout, $owl_lang->alt_btn_bulk_checkout, "reset", "") ?></td>
</tr>
</table>
      </td>
      <td>Verrouiller/D&eacute;verrouiller les fichiers s&eacute;lectionn&eacute;s. Seul l'utilisateur qui a verrouill&eacute; un fichier peut le modifier .<br />
      </td>
    </tr>
</table>
<br />

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
