<?php
/*

  File: French/language.inc
  Author: Bozz
  Email: bozz@bozzit.com
  Date: 2001/10/16

  Owl: Copyright Chris Vincent <cvincent@project802.net>

  You should have received a copy of the GNU Public
  License along with this package; if not, write to the
  Free Software Foundation, Inc., 59 Temple Place - Suite 330,
  Boston, MA 02111-1307, USA.

  Updated by Roublev on 2003/06/10
  Email: roublev@free.fr

  Updated and corrected in "French of France" by philplug on 2004/08/31 (corrected hyphenations, Canadian idioms turned into French idioms, etc.)
  Email: ppeltier@bateaubois.com

  Updated in "French of France" by jefd999 on 2005-06-14 fof 0.8 beta

  Updated by PCombet <pcombet@adosis.com> on 2005-12-29

  Corrected by Tanguy <twins29@users.sourceforge.net> on 2006-06-20

*/
//********************************************************
// Date Formating BEGIN
//********************************************************

// If config/owl.php   $default->generic_date_format is set USE That one if not
// use the one in this file
$VER="xxx";
if (isset($default->generic_date_format) && trim($default->generic_date_format) != "") {

        $owl_lang->localized_date_format = $default->generic_date_format;
} else {
        $owl_lang->localized_date_format = "d.m.Y \&agrave; \\ H:i";
}

// Character Encoding
// default: "UTF-8"
$owl_lang->charset = "iso-8859-1";

// Language Code
// example: "en"
$owl_lang->lang_code = "fr";

//********************************************************
// Date Formating BEGIN
//********************************************************

//********************************************************
// Error Messages BEGIN
//********************************************************

$owl_lang->err_adding_comments          = "Vous n'avez pas la permission n&eacute;cessaire pour ajouter un commentaire &agrave; ce fichier";
$owl_lang->err_backup_file              = "Erreur lors de la sauvegarde d'un fichier existant";
$owl_lang->err_backup_folder_create     = "Erreur lors de la cr&eacute;ation du dossier de sauvegarde";
$owl_lang->err_bad_url                  = "Entrez une URL valide SVP";
$owl_lang->err_bad_version_1            = "Owl Intranet a besoin d'une version minimum de PHP ";
$owl_lang->err_bad_version_2            = "Votre serveur a";
$owl_lang->err_bad_version_3            = "L'administrateur du syst&egrave;me doit installer une nouvelle version de PHP";
$owl_lang->err_cant_del_doc_type        = "Suppression actuellement impossible";
$owl_lang->err_captcha_auth		= "Authentification CAPTCHA echou&eacute;e";
$owl_lang->err_comment_empty            = "Commentaire obligatoire";
$owl_lang->err_database                 = "Erreur de base de donn&eacute;es";
$owl_lang->err_doc_field_req            = "Manque le champ :";
$owl_lang->err_doc_type_name_req        = "Nom du document requis <br />";
$owl_lang->err_email                    = "L'e-mail n'a pu &ecirc;tre envoy&eacute;";
$owl_lang->err_email_required           = "Le champ adresse e-mail est requis";
$owl_lang->err_fexist1                  = "<b>Fichiers d&eacute;j&agrave; existants :</b>";
$owl_lang->err_fexist2                  = "Il y a d&eacute;j&agrave; un fichier nomm&eacute; <i>";
$owl_lang->err_fexist3                  = "</i> dans ce dossier.";
$owl_lang->err_field_label_req          = "Le label du champ est requis <br />";
$owl_lang->err_field_name_req           = "Le nom du champ est requis <br />";
$owl_lang->err_field_size_req           = "La taille du champ est requise <br />";
$owl_lang->err_file_exists              = "Le fichier que vous essayez d'ajouter existe d&eacute;j&agrave;";
$owl_lang->err_file_indexing            = "Impossible de cr&eacute;er le fichier temporaire 'TEXT'";
$owl_lang->err_file_lock                = "Vous ne pouvez pas verrouiller ce fichier, il est d&eacute;j&agrave; verrouill&eacute; par ";
$owl_lang->err_file_not_exist           = "Fichier inexistant sur le syst&egrave;me de fichier";
$owl_lang->err_file_too_big             = "php.ini upload_max_filesize d&eacute;pass&eacute;e et/ou<br />Taille maximale autoris&eacute;e dans Owl d&eacute;pass&eacute;e";
$owl_lang->err_file_update              = "La mise &agrave; jour d'un fichier n&eacute;cessite le m&ecirc;me nom de fichier";
$owl_lang->err_fileexists               = "D&eacute;placement annul&eacute;<br />Un fichier avec le m&ecirc;me nom existe d&eacute;j&agrave;.";
$owl_lang->err_filemove_exist           = "Le nom de fichier existe d&eacute;j&agrave; (renommer)";
$owl_lang->err_folder_create            = "Erreur dans la cr&eacute;ation du dossier";
$owl_lang->err_folder_exist             = "Dossier d&eacute;j&agrave; existant";
$owl_lang->err_folderexists             = "Impossible de renommer<br />Un dossier avec le m&ecirc;me nom existe d&eacute;j&agrave;.";
$owl_lang->err_forbidden_file		= "Type de fichier interdit";
$owl_lang->err_general                  = "Une erreur s'est produite";
$owl_lang->err_group_delete             = "Ce groupe a: <font color=\"red\">%d</font> Fichier(s),  <font color=\"red\">%d</font> R&eacute;pertoire(s), <font color=\"red\">%d</font> Utilisateur(s) avec ce groupe primaire , <font color=\"red\">%d</font> Utilisateur(s) qui sont membre de ce groupe";
$owl_lang->err_group_exists             = "Groupe d&eacute;j&agrave; existant";
$owl_lang->err_gzip_not_found           = "gzip n'a pu &ecirc;tre trouv&eacute; dans ";
$owl_lang->err_login                    = "Vous devez &ecirc;tre connect&eacute; pour faire cette action";
$owl_lang->err_movecancel               = "D&eacute;placement annul&eacute;<br />";
$owl_lang->err_nameempty                = "Le nom du dossier ne peut pas &ecirc;tre vide";
$owl_lang->err_news_required            = "ERREUR : Un titre de news et un contenu sont requis";
$owl_lang->err_no_access                = "Vous n'avez pas les droits n&eacute;cessaires sur les fichiers s&eacute;lectionn&eacute;s";
$owl_lang->err_no_access_info           = "Raisons possibles : <br />1) Vous n'avez coch&eacute; aucun fichier ou dossier<br />2) Les op&eacute;rations multiples ne sont pas utilisables sur les dossiers<br />3) Vous n'avez pas acc&egrave;s aux fichiers ou dossiers s&eacute;lectionn&eacute;s.";
$owl_lang->err_noemail                  = "Vous n'&ecirc;tes pas autoris&eacute; &agrave; envoyer ce fichier par e-mail";
$owl_lang->err_nofileaccess             = "Vous n'avez pas acc&egrave;s &agrave; ce fichier";
$owl_lang->err_nofiledelete             = "Vous n'&ecirc;tes pas autoris&eacute; &agrave; supprimer ce fichier";
$owl_lang->err_nofilemod                = "Vous n'&ecirc;tes pas autoris&eacute; &agrave; modifier ce fichier";
$owl_lang->err_nofolderaccess           = "Vous n'avez pas acc&egrave;s &agrave; ce dossier";
$owl_lang->err_nofolderdelete           = "Vous n'&ecirc;tes pas autoris&eacute; &agrave; supprimer ce dossier";
$owl_lang->err_nofoldermod              = "Vous n'&ecirc;tes pas autoris&eacute; &agrave; modifier ce dossier";
$owl_lang->err_nosubfolder              = "Vous n'&ecirc;tes pas autoris&eacute; &agrave; cr&eacute;er des sous-dossiers";
$owl_lang->err_not_zip                  = "Archive ZIP invalide";
$owl_lang->err_note_title               = "Le titre est un champ requis";
$owl_lang->err_note_title_exists        = "Une note du m&ecirc;me nom existe d&eacute;j&agrave;";
$owl_lang->err_noupload                 = "Vous n'&ecirc;tes pas autoris&eacute; &agrave; charger un fichier dans ce dossier";
$owl_lang->err_pass_missmatch           = "Les mots de passe ne correspondent pas";
$owl_lang->err_pass_wrong               = "L'ancien mot de passe est incorrect";
$owl_lang->err_please_select_bulk       = "Cochez au moins un fichier ou un dossier";
$owl_lang->err_pod2html_not_found       = "ERREUR : l'outil pod2html n'a pas &eacutet&eacute trouv&eacute :";
$owl_lang->err_quota                    = "Vous n'avez pas l'espace disque suffisant pour ins&eacute;rer ce fichier : ";
$owl_lang->err_quota_avail              = " disponible.";
$owl_lang->err_quota_exceed             = "Vous avez d&eacute;pass&eacute; votre quota";
$owl_lang->err_quota_needed             = " n&eacute;cessaire ";
$owl_lang->err_req                      = "ERREUR : Tous les champs sont requis";
$owl_lang->err_root_delete              = "Le r&eacute;pertoire racine ne peut pas &ecirc;tre d&eacute;truit";
$owl_lang->err_select_reviewer          = "Merci de s&eacute;lectionner au moins une personne pour approuver votre document";
$owl_lang->err_sess_notvalid            = "L'identifiant de session n'est pas valide";
$owl_lang->err_sess_write               = "Erreur d'&eacute;criture dans la base de donn&eacute;e";
$owl_lang->err_specialfoldername        = "'backup' est un nom r&eacute;serv&eacute; au programme, veuillez choisir un autre nom";
$owl_lang->err_sql                      = "SQL error";
$owl_lang->err_tar_not_found            = "tar n'a pu &ecirc;tre trouv&eacute; dans ";
$owl_lang->err_trash_can_empty          = "La corbeille est vide";
$owl_lang->err_unauth_area              = "Vous n'&ecirc;tes pas autoris&eacute; &agrave; utiliser cet espace";
$owl_lang->err_unauthorized             = "Non autoris&eacute;";
$owl_lang->err_update_file_lock         = "Impossible de mettre &agrave; jour ce fichier, il est verouill&eacute; par";
$owl_lang->err_upload                   = "Erreur de chargement";
$owl_lang->err_user_exists              = "Utilisateur d&eacute;j&agrave; existant";


//********************************************************
// Error Messages END
//********************************************************

$owl_lang->btn_add_field				= "Nouveau champ";
$owl_lang->btn_add_file					= "Nouveau fichier";
$owl_lang->btn_add_folder				= "Nouveau dossier";
$owl_lang->btn_add_news					= "Ajouter une News";
$owl_lang->btn_add_note					= "Nouvelle note";
$owl_lang->btn_add_url					= "Nouvelle URL";
$owl_lang->btn_add_zip					= "Nouvelle archive";
$owl_lang->btn_admin					= "Admin";
$owl_lang->btn_admin_groups				= "Nouveau groupe";
$owl_lang->btn_admin_news				= "News";
$owl_lang->btn_admin_users				= "Nouvel utilisateur";
$owl_lang->btn_back					= "Pr&eacute;c&eacute;dent";
$owl_lang->btn_browse					= "Explorateur";
$owl_lang->btn_bulk_checkout				= "D&eacute;verrouiller";
$owl_lang->btn_bulk_delete				= "Supprimer";
$owl_lang->btn_bulk_download				= "T&eacute;l&eacute;charger";
$owl_lang->btn_bulk_email				= "E-Mail";
$owl_lang->btn_bulk_move				= "D&eacute;placer";
$owl_lang->btn_cancel					= "Annuler";
$owl_lang->btn_clickhere				= "Cliquez ici";
$owl_lang->btn_collapse_view				= "Affichage simplifi&eacute;";
$owl_lang->btn_create					= "Cr&eacute;er";
$owl_lang->btn_deldoctype				= "Effacer le type de document";
$owl_lang->btn_download_bulk_package			= "Pour t&eacute;l&eacute;charger le package";
$owl_lang->btn_edit_group				= "Editer le groupe";
$owl_lang->btn_edit_user				= "Editer l'utilisateur";
$owl_lang->btn_expand_view				= "Affichage d&eacute;taill&eacute;";
$owl_lang->btn_export					= "Exporter";
$owl_lang->btn_go_bottom				= "Bas de page";
$owl_lang->btn_go_top					= "Haut de page";
$owl_lang->btn_help					= "Aide";
$owl_lang->btn_import_users				= "Importer<br/>les utilisateurs";
$owl_lang->btn_login					= "Login";
$owl_lang->btn_logout					= "D&eacute;connexion";
$owl_lang->btn_mail_tool				= "E-Mail";
$owl_lang->btn_register					= "S'enregistrer";
$owl_lang->btn_reset					= "R&eacute;initialiser";
$owl_lang->btn_send_email				= "Envoyer un E-Mail";
$owl_lang->btn_submit					= "Valider";
$owl_lang->btn_gen_pdf					= "G&eacute;n&eacute;rer un PDF";
$owl_lang->btn_default_view 				= "Affichage Standard"; 
$owl_lang->btn_thumb_view 				= "Vignettes"; 
$owl_lang->btn_import					= "Importer";
$owl_lang->btn_maj					= "Mettre &agrave; jour";

//********************************************************
// Image Alternate popup Messages BEGIN
//********************************************************

$owl_lang->alt_gen_pdf          		= "G&eacute;n&eacute;rer la version PDF du fichier ci-dessus ";
$owl_lang->alt_add_comments     		= "Ajouter un commentaire &agrave; ce fichier";
$owl_lang->alt_updated          		= "Le document a &eacute;t&eacute; mis &agrave; jour depuis votre derni&egrave;re visite";
$owl_lang->alt_new              		= "Le document a &eacute;t&eacute; cr&eacute;&eacute; depuis votre derni&egrave;re visite";
$owl_lang->alt_add_field        		= "Ajouter le champ ci-dessus";
$owl_lang->alt_add_group        		= "Cr&eacute;er le groupe";
$owl_lang->alt_add_news         		= "Ajouter une news";
$owl_lang->alt_add_user         		= "Cr&eacute;er un utilisateur";
$owl_lang->alt_admin_flush      		= "Vider les sessions expir&eacute;es";
$owl_lang->alt_admin_groups     		= "Cr&eacute;er un nouveau groupe";
$owl_lang->alt_admin_users      		= "Cr&eacute;er un nouvel utilisateur";
$owl_lang->alt_approve_file     		= "Approuver ce document";
$owl_lang->alt_back             		= "Pr&eacute;c&eacute;dent";
$owl_lang->alt_btn_add_file     		= "Charger un nouveau fichier";
$owl_lang->alt_btn_add_folder   		= "Cr&eacute;er un nouveau dossier";
$owl_lang->alt_btn_add_note     		= "Ajouter une note";
$owl_lang->alt_btn_add_url      		= "Cr&eacute;er un nouvel URL";
$owl_lang->alt_btn_add_zip           		= "T&eacute;l&eacute;charger un fichier ZIP &agrave; extraire";
$owl_lang->alt_btn_admin			= "Administration de OWL";
$owl_lang->alt_btn_browse			= "Retour &agrave; l'explorateur";
$owl_lang->alt_btn_bulk_checkout		= "D&eacute;verrouiller les fichiers";
$owl_lang->alt_btn_bulk_delete			= "Supprimer les fichiers";
$owl_lang->alt_btn_bulk_download		= "T&eacute;l&eacute;chargement multiple de fichiers";
$owl_lang->alt_btn_bulk_email			= "Envoyer les fichiers par e-mail";
$owl_lang->alt_btn_bulk_move			= "D&eacute;placement de fichiers et dossiers";
$owl_lang->alt_btn_clickhere			= "Cliquez ici";
$owl_lang->alt_btn_collapse_view		= "Affichage simplifi&eacute;";
$owl_lang->alt_btn_expand_view			= "Affichage d&eacute;taill&eacute;";
$owl_lang->alt_btn_help				= "Aide en ligne";
$owl_lang->alt_btn_login			= "Se connecter &agrave; OWL";
$owl_lang->alt_btn_logout			= "Se d&eacute;connecter de OWL";
$owl_lang->alt_btn_mail_tool			= "Outil de messagerie e-mail";
$owl_lang->alt_btn_trash_admin			= "Administration de la poubelle";
$owl_lang->alt_cancel				= "Annuler cette action";
$owl_lang->alt_cancel_move			= "Annuler le d&eacute;placement";
$owl_lang->alt_change				= "Mettre &agrave; jour l'enregistrement";
$owl_lang->alt_comments				= "Commentaires existants pour ce fichier";
$owl_lang->alt_copy_file			= "Copier le fichier";
$owl_lang->alt_copy_folder			= "Copier le dossier";
$owl_lang->alt_del_all				= "Vider la corbeille";
$owl_lang->alt_edt_comments     		= "Edit This Comment";
$owl_lang->alt_del_comments			= "Supprimer le commentaire";
$owl_lang->alt_del_field			= "Supprimer ce champ";
$owl_lang->alt_del_file				= "Supprimer le fichier (O/N)";
$owl_lang->alt_del_folder			= "Supprimer le dossier (O/N)";
$owl_lang->alt_del_group			= "Supprimer le groupe";
$owl_lang->alt_del_news				= "Effacer cette news";
$owl_lang->alt_del_selected			= "Supprimer les fichiers s&eacute;lectionn&eacute;s";
$owl_lang->alt_del_this_folder			= "Supprimer le dossier &#8211; ";
$owl_lang->alt_del_user				= "Supprimer cet utilisateur";
$owl_lang->alt_deldoctype			= "Supprimer le type de document courant";
$owl_lang->alt_edit_field			= "Editer cette d&eacute;finition de champ";
$owl_lang->alt_edit_file_inline			= "Editer le texte du fichier";
$owl_lang->alt_edit_group			= "Editer le groupe s&eacute;lectionn&eacute;";
$owl_lang->alt_edit_news			= "Editer les news";
$owl_lang->alt_edit_user        		= "Editer l'utilisateur s&eacute;lectionn&eacute;";
$owl_lang->alt_email            		= "Envoyer ce fichier";
$owl_lang->alt_email_reminder   		= "E-mail de rappel";
$owl_lang->alt_email_selected   		= "Envoyer tous les fichiers s&eacute;lectionn&eacute;s"; 
$owl_lang->alt_get_file         		= "T&eacute;l&eacute;charger le fichier";
$owl_lang->alt_get_folder       		= "T&eacute;l&eacute;charger tout le contenu du dossier";
$owl_lang->alt_go_bottom        		= "Bas de la page";
$owl_lang->alt_go_top           		= "Haut de la page";
$owl_lang->alt_have_news        		= "News &agrave; lire";
$owl_lang->alt_have_no_news     		= "Aucune(s) news &agrave; lire";
$owl_lang->alt_home_folder      		= "Retour au r&eacute;pertoire racine ";
$owl_lang->alt_link_file        		= "Lier ce fichier &agrave; un autre dossier";
$owl_lang->alt_lock_file        		= "Verrouill&eacute;/D&eacute;verrouill&eacute; ";
$owl_lang->alt_log_clear        		= "Supprimer les fichiers de logs (O/N)";
$owl_lang->alt_log_file         		= "Fichier de logs";
$owl_lang->alt_log_next         		= "Enregistrements suivants";
$owl_lang->alt_log_prev         		= "Enregistrements pr&eacute;c&eacute;dents";
$owl_lang->alt_log_top          		= "Aller au premier enregistrement";
$owl_lang->alt_mod_file         		= "Modifier les propri&eacute;t&eacute;s du fichier";
$owl_lang->alt_mod_folder       		= "Modifier les propri&eacute;t&eacute;s du dossier";
$owl_lang->alt_monitor          		= "Surveiller ce fichier";
$owl_lang->alt_monitor_folder   		= "Surveiller ce dossier";
$owl_lang->alt_monitored        		= "Arr&ecirc;ter la surveillance du fichier";
$owl_lang->alt_monitored_folder 		= "Arr&ecirc;ter la surveillance du dossier";
$owl_lang->alt_move_file        		= "D&eacute;placer le fichier";
$owl_lang->alt_move_folder      		= "D&eacute;placer le dossier";
$owl_lang->alt_move_selected    		= "D&eacute;placer tous les fichiers s&eacute;lectionn&eacute;s"; 
$owl_lang->alt_my_approval      		= "Mes document(s) &agrave; approuver";
$owl_lang->alt_new_doctype      		= "Cr&eacute;er un nouveau type de document";
$owl_lang->alt_news_close       		= "Fermer la fen&ecirc;tre de news";
$owl_lang->alt_news_next        		= "Afficher la news suivante";
$owl_lang->alt_news_prev        		= "Afficher la news pr&eacute;c&eacute;dente";
$owl_lang->alt_news_print       		= "Imprimer cette news";
$owl_lang->alt_pending_approval 		= "Document(s) en attente d'approbation";
$owl_lang->alt_play_file        		= "Jouer le fichier m&eacute;dia";
$owl_lang->alt_publish_file     		= "Rendre ce document public";
$owl_lang->alt_recycle          		= "Votre corbeille est vide";
$owl_lang->alt_recycle_disable  		= "Corbeille<br/>d&eacute;sactiv&eacute;e";
$owl_lang->alt_recycle_not_found		= "Emplacement de la corbeille introuvable";
$owl_lang->alt_refresh_filter   		= "Activer les filtres / Rafra&icirc;chir les r&eacute;sultats";
$owl_lang->alt_register         		= "Inscription";
$owl_lang->alt_reject_file      		= "Rejeter ce document";
$owl_lang->alt_related				= "Trouver les documents en rapport";
$owl_lang->alt_reset_form			= "Remettre &agrave; z&eacute;ro";
$owl_lang->alt_rest_selected    		= "Restaurer les fichiers s&eacute;lectionn&eacute;s";
$owl_lang->alt_search           		= "Lancer la recherche";
$owl_lang->alt_send_email       		= "Envoyer l'e-mail";
$owl_lang->alt_sendfile         		= "Envoyer le fichier";
$owl_lang->alt_set_initial_dir  		= "Fixer le r&eacute;pertoire racine ici";
$owl_lang->alt_site_map         		= "Plan de navigation";
$owl_lang->alt_submit           		= "Valider";
$owl_lang->alt_toggle_check_box 		= "S&eacute;lectionner les documents souhait&eacute;s";
$owl_lang->alt_tool_status_green		= "Outil activ&eacute;";
$owl_lang->alt_tool_status_red  		= "Le chemin est incorrect et/ou n'est pas executable";
$owl_lang->alt_unlock_file      		= "D&eacute;verrouiller";
$owl_lang->alt_upd_field        		= "Mettre ce champ &agrave; jour";
$owl_lang->alt_upd_file         		= "Mise &agrave; jour du fichier";
$owl_lang->alt_view_comments    	= "Ajouter / Voir les commentaires du fichier";
$owl_lang->alt_view_file        		= "Voir le fichier";
$owl_lang->alt_btn_thumb_view 		= "Passer en vignettes"; 
$owl_lang->alt_import			= "Importer";
$owl_lang->alt_maj				= "Mettre &agrave; jour";

//********************************************************
// Image Alternate popup Messages END
//********************************************************


//********************************************************
// Owl Logging messages BEGIN
//********************************************************

$owl_lang->log_admin_changed_group 		= "Groupe chang&eacute;: ";
$owl_lang->log_admin_changed_user 		= "Utilisateur chang&eacute;: ";
$owl_lang->log_admin_created_group 		= "Groupe cr&eacute;&eacute;: ";
$owl_lang->log_admin_created_user 		= "Utilisateur cr&eacute;&eacute;: ";
$owl_lang->log_admin_del_group 			= "Groupe supprim&eacute;: ";
$owl_lang->log_admin_del_user 			= "Utilisateur supprim&eacute;: ";
$owl_lang->log_admin_disabled 			= "Utilisateur d&eacute;sactiv&eacute;: ";
$owl_lang->log_admin_email_restore 		= "Fichiers envoy&eacute;s:";
$owl_lang->log_admin_of  			= "de";
$owl_lang->log_admin_to  			= "a";
$owl_lang->log_admin_trash_delfile		= "Fichier supprim&eacute;: ";
$owl_lang->log_admin_trash_delfold 		= "R&eacute;pertoire supprim&eacute;: ";
$owl_lang->log_admin_trash_empty 		= "Poubelle vid&eacute;e";
$owl_lang->log_admin_trash_restore 		= "Restaur&eacute;: ";
$owl_lang->log_del_det          		= "Tout le contenu a &eacute;t&eacute; supprim&eacute;";
$owl_lang->log_detail           		= "Aucuns d&eacute;tails suppl&eacute;mentaires";
$owl_lang->log_file_to          		= "A : ";
$owl_lang->log_filter_all       		= "TOUS";
$owl_lang->log_login_det        		= "Utilisateur : ";
$owl_lang->log_login_expired    		= "Utilisateur expir&eacute;: ";
$owl_lang->log_login_too_many_attempts 		= "D&eacute;sactiv&eacute; (trop de connexions &eacute;chou&eacute;es: )";
$owl_lang->log_readhd_feature_add  		= "Lecture automatique des fichiers sur le disque dur";
$log_file_actions[1]            		= "CONNEXION";
$log_file_actions[2]            		= "ECHEC DE LA CONNEXION";
$log_file_actions[3]            		= "DECONNEXION";
$log_file_actions[4]            		= "FICHIER SUPPRIM&#201;";
$log_file_actions[5]            		= "FICHIER CHARG&#201;";
$log_file_actions[6]            		= "FICHIER MIS A JOUR";
$log_file_actions[7]            		= "FICHIER T&#201;L&#201;CHARG&#201;";
$log_file_actions[8]            		= "FICHIER CHANG&#201;";
$log_file_actions[9]            		= "FICHIER VERROUILL&#201;";
$log_file_actions[10]           		= "FICHIER D&#201;VERROUILL&#201;";
$log_file_actions[11]           		= "FICHIER ENVOY&#201;";
$log_file_actions[12]           		= "FICHIER D&#201;PLAC&#201;";
$log_file_actions[13]           		= "DOSSIER CR&#201;E";
$log_file_actions[14]           		= "DOSSIER SUPPRIM&#201;";
$log_file_actions[15]           		= "DOSSIER MODIFI&#201;";
$log_file_actions[16]           		= "DOSSIER D&#201;PLAC&#201;";
$log_file_actions[17]           		= "MOT DE PASSE OUBLI&#201;";
$log_file_actions[18]           		= "INSCRIPTION";
$log_file_actions[19]           		= "FICHIER VU";
$log_file_actions[20]           		= "FICHIER VIRUSS&#201;";
$log_file_actions[21]           		= "FICHIER COPI&#201;";
$log_file_actions[22]           		= "DOSSIER COPIE";
$log_file_actions[23]           		= "FICHIER LIE";
$log_file_actions[24]           		= "ADMIN UTILISATEUR";
$log_file_actions[25]           		= "POUBELLE";
$log_file_actions[26]           		= "FICHIER ACL";
$log_file_actions[27]           		= "REPERTOIRE ACL";

//********************************************************
// Owl Logging messages END
//********************************************************


//********************************************************
// index.php  BEGIN
//********************************************************

$owl_lang->admin_login_date  			= "L'utilisateur Admin s'est connect&eacute; le: ";
$owl_lang->admin_login_from  			= "L'utilisateur Admin s'est connect&eacute; depuis le: ";
$owl_lang->admin_login_subject  		= "L'UTILISATEUR ADMIN EST CONNECTE";
$owl_lang->anonymous         			= "(Acc&egrave;s Anonyme)";
$owl_lang->engine            			= "Plugin GEDOWL ".$VER." bas&eacute; sur ";
$owl_lang->forgot_pass       			= "Vous avez oubli&eacute; votre mot de passe ?";
$owl_lang->like_register     			= "Voulez-vous vous inscrire ?";
$owl_lang->login             			= "Connexion";
$owl_lang->logindisabled     			= "Ce compte a &eacute;t&eacute; suspendu, veuillez contacter l'administrateur";
$owl_lang->loginfail         			= "Erreur de connexion";
$owl_lang->password          			= "Mot de passe";
$owl_lang->repository_list      		= "Dossier :";
$owl_lang->successlogout                      	= "Vous &ecirc;tes d&eacute;connect&eacute;";
$owl_lang->toomanysessions                    	= "Nombre maximum de sessions atteint pour l'utilisateur, contactez l'administrateur";
$owl_lang->username          			= "Utilisateur";
$owl_lang->forgot_username          		= "Utilisateur ou Email";
$owl_lang->version           			= "Version";

//********************************************************
// index.php  END
//********************************************************


//********************************************************
// browse.php  BEGIN
//********************************************************

$owl_lang->actions                      = "Actions";
$owl_lang->current_db                   = "Dossier :";
$owl_lang->current_folder 		= "Dossier en cours :";
$owl_lang->del_all                      = "Vider la corbeille";
$owl_lang->del_selected                 = "Effacer";
$owl_lang->disk_quota                   = "Capacit&eacute; du disque";
$owl_lang->doc_number 			= "N&deg;Doc";
$owl_lang->email_selected               = "Envoyer par e-mail";
$owl_lang->favorite 			= "Favoris";
$owl_lang->favorite_add 		= "Ajouter";
$owl_lang->favorite_delete 		= "Supprimer";
$owl_lang->favorite_go 			= "Allez vers";
$owl_lang->file                         = "Fichier";
$owl_lang->files_in_my_group_title 	= "Fichiers dans mon(mes) groupe(s)";
$owl_lang->folder_count_file            = "Fichier(s)";
$owl_lang->folder_count_folder          = "Dossier(s)";
$owl_lang->folder_count_note            = "Note(s)";
$owl_lang->folder_count_pre             = "Ce dossier contient";
$owl_lang->folder_count_url             = "URL(s)";
$owl_lang->folder_policy                = "Droits du r&eacute;pertoire courant :";
$owl_lang->held                         = "D&eacute;tenu par"; // for version control
//modif1 misterphi
$owl_lang->last_logged                  = "Connexion pr&eacute;c&eacute;dente :";
//eom
$owl_lang->menu_file_action             = "Actions disponibles";
$owl_lang->menu_folder_action           = "Actions disponibles";
$owl_lang->menu_url_action              = "Actions disponibles";
$owl_lang->modified                     = "Derni&egrave;re modification";
$owl_lang->move_selected                = "D&eacute;placer";
$owl_lang->my_files_title		= "Mes fichiers";
$owl_lang->my_monitored_title		= "Mon(Mes) fichier(s)/r&eacute;pertoires(s) surveill&eacute;(s)";
$owl_lang->my_special_access_title	= "Liste de mes acc&egrave;s sp&eacute;ciaux";
$owl_lang->new_file                     = "Nouveau!";
$owl_lang->no_description               = "Aucune description";
$owl_lang->page                         = "Page :";
$owl_lang->panel_file_info              = "Informations";
$owl_lang->postedby                     = "Envoy&eacute; par";
$owl_lang->reallydelete                 = "Voulez-vous vraiment supprimer ";
$owl_lang->reallydelete_logs            = "Souhaitez-vous vraiment vider le fichier de log";
$owl_lang->reallydelete_selected        = "Souhaitez-vous vraiment effacer les fichiers s&eacute;lectionn&eacute;s";
$owl_lang->reallyrestore_selected       = "Voulez-vous vraiment restaurer la s&eacute;lection";
$owl_lang->rest_selected                = "Restaurer la selection";
$owl_lang->score                        = "Point";
$owl_lang->size                         = "Taille";;
$owl_lang->updated_by                   = "Mis &agrave; jour par ";
$owl_lang->special_access_count_alt	= "Acc&egrave;s sp&eacute;cial a mon (mes) fichier(s)/r&eacute;pertoire(s)";
$owl_lang->special_access_count		= "Acc&egrave;s sp&eacute;cial :";
$owl_lang->title                        = "Titre";
$owl_lang->tot_files                    = "Total :";
$owl_lang->tot_monitored		= "Surveill&eacute;(s) :";
$owl_lang->tot_my_checked_out           = "V&eacute;rouill&eacute;(s) :";
$owl_lang->tot_my_files                 = "Mes fichiers :";
$owl_lang->tot_my_group                 = "Groupe(s) :";
$owl_lang->tot_new_files                = "Nouveaut&eacute;s :";
$owl_lang->tot_updated_files            = "Mises &agrave; jour :";
$owl_lang->updated_file                 = "Mis &agrave; jour!";
$owl_lang->user                         = "Utilisateur";
$owl_lang->ver                          = "Ver."; // for version control
//modif2 misterphi
$owl_lang->past_number                  = "Nombre de jours :";
//eom

//********************************************************
// browse.php  END
//********************************************************


//********************************************************
// modify.php  BEGIN
//********************************************************

$owl_lang->adding_comments      		= "Ajouter / Voir les commentaires : ";
$owl_lang->addingfile           		= "Ajout d'un fichier &agrave; ";
$owl_lang->addingfolder         		= "Ajout d'un dossier &agrave; ";
$owl_lang->attach_file          		= "Attacher un fichier";
$owl_lang->change               		= "Changer";
$owl_lang->comments      				= "Commentaires";
$owl_lang->comments_added       		= "Commentaires ajout&eacute;s par : ";
$owl_lang->create               		= "Cr&eacute;er";
$owl_lang->description          		= "Description";
$owl_lang->disp_mp3             		= "MP3&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Chanson :<br />Album :<br />Bitrate&nbsp;-&nbsp;Samplerate :<br />Dur&eacute;e :<br />Genre :<br />Commentaires :";
$owl_lang->email_body           		= "Message";
$owl_lang->email_cc             		= "Copie";
$owl_lang->email_reply_to       		= "Adresse de r&eacute;ponse";
$owl_lang->email_subject        		= "Objet";
$owl_lang->email_to             		= "Destinataire";
$owl_lang->emailing             		= "E-Mail";
$owl_lang->keywords             		= "Mots Cl&eacute;s";
$owl_lang->modify_url           		= "URL";
$owl_lang->modifying            		= "Modification ";
$owl_lang->name                 		= "Nom";
$owl_lang->none_selected        		= "AUCUN";
$owl_lang->note_content         		= "Contenu de la note";
$owl_lang->notify_link          		= "Lien externe &agrave; OWL";
$owl_lang->ownergroup           		= "Groupe Propri&eacute;taire";
$owl_lang->ownership            		= "Propri&eacute;taire";
$owl_lang->permissions          		= "Permissions";
$owl_lang->pick_select          		= "&#8212; S&eacute;lectionnez un Utilisateur Owl &#8212;";
$owl_lang->policy               		= "Droits";
$owl_lang->post_comment					= "Ajouter un commentaire";
$owl_lang->prop_permissions     		= "Propager les Permissions aux sous-r&eacute;pertoires ?";
$owl_lang->prop_perms_files     		= "Droits &agrave; donner aux fichiers propag&eacute;s :";
$owl_lang->sendfile             		= "Envoi fichier";
$owl_lang->sendthisfile         		= "Envoyer ce fichier";
$owl_lang->sendthisurl          		= "Envoyer cette URL";
$owl_lang->updating             		= "Mise &agrave; jour";
$owl_lang->verdescription       		= "Description<br />des changements";
$owl_lang->vermajor             		= "R&eacute;vision majeure";
$owl_lang->verminor             		= "R&eacute;vision mineure";
$owl_lang->vertype              		= "R&eacute;vision";  // for modify.php when uploading
$owl_lang->save_keyword                         = "Save Keyword?";


//********************************************************
// modify.php  END
//********************************************************


//********************************************************
// register.php  BEGIN
//********************************************************

$owl_lang->captcha_typein        		= "Saisissez les 5 caract&egrave;res";
$owl_lang->change_pass_title	      		= "CHANGER LE MOT DE PASSE";
$owl_lang->email         			= "E-mail";
$owl_lang->err_cant_reuse_passwords   		= "Vous ne pouvez r&eacute;utiliser des mots de passe d&eacute;j&agrave; utilis&eacute;s auparavant";
$owl_lang->err_new_confirm_different  		= "Le mot de passe et sa confirmation ne coincident pas <br />";
$owl_lang->err_old_pass_ver_failed    		= "Votre ancien Mot de passe est incorrect <br />";
$owl_lang->err_pass_restriction_1     		= "Votre Mot de Passe doit contenir un minimum de '$default->min_pass_length' caract&egrave;res <br />";
$owl_lang->err_pass_restriction_2     		= ", contenir au moins '$default->min_pass_numeric' chiffres [0-9] <br />";
$owl_lang->err_pass_restriction_3     		= "et au moins '$default->min_pass_special' caract&egrave;res sp&eacute;ciaux [ ~!@#$%^&*()-=+_|{}][\";:<>.,?/\\ ]<br />";
$owl_lang->err_username_not_long_enough 	= "Votre nom d'utilisateur '%s' est de %d caract&egrave;res, et doit &ecirc;tre d'au moins %d caract&egrave;res.";
$owl_lang->full_name     			= "Nom ";
$owl_lang->pass_change_email_1        		= "Votre Mot de Passe n'a pas &eacute;t&eacute; r&eacute;initialis&eacute; car vous n'utilisez pas l'authentification Owl";
$owl_lang->pass_change_email_2        		= "Le type de votre authentification est: ";
$owl_lang->pass_change_email_3       		= "Veuillez contacter votre administrateur systeme pour plus d'informations";
$owl_lang->register      			= "Enregistrement de l'utilisateur";
$owl_lang->self_failed   			= "Echec";
$owl_lang->self_passwd   			= "Mot de passe envoy&eacute; &agrave; :";
$owl_lang->self_reg_bod  			= "Votre mot de passe Owl est :";
$owl_lang->self_reg_rset 			= "Votre ancien mot de passe a &eacute;t&eacute; chang&eacute; pour :";
$owl_lang->self_reg_subj 			= "Votre compte Owl";
$owl_lang->send_pass     			= "Envoyer le mot de passe";
$owl_lang->submit        			= "Enregistrement";
$owl_lang->thank_you_1   			= "Merci";
$owl_lang->thank_you_2   			= "Un e-mail vous sera envoy&eacute; <br /> avec un nouveau mot de passe pour <br /><b>l'utilisateur :</b>";
$owl_lang->thank_you_3          		= "Your account needs to be enabled by the administrator before you can log in.";


//********************************************************
// register.php  END
//********************************************************


//********************************************************
// move.php  BEGIN
//********************************************************

$owl_lang->cancel_button     			= "Annuler";
$owl_lang->copy_file				= "Copier un fichier";
$owl_lang->copy_folder				= "Copier un dossier";
$owl_lang->moving            			= "D&eacute;placer";
$owl_lang->moving_file				= "D&eacute;placer un fichier";
$owl_lang->moving_folder			= "D&eacute;placer un dossier";
$owl_lang->select            			= "Veuillez s&eacute;lectionner un nouveau dossier :";

//********************************************************
// move.php  END
//********************************************************


//********************************************************
// log.php  BEGIN
//********************************************************

$owl_lang->diff_from				= "Diff From";
$owl_lang->diff_to				= "Diff To";
$owl_lang->viewlog          			= "Visualiser le log de ";

//********************************************************
// log.php  END
//********************************************************


//********************************************************
// prefs.php  BEGIN
//********************************************************

$owl_lang->comment_notif			= "Commentez la notification";
$owl_lang->confpassword 			= "Confirmez le nouveau mot de passe";
$owl_lang->email_tool   			= "Outil E-mail";
$owl_lang->group        			= "Groupe primaire";
$owl_lang->groupmember  			= "Groupe membre";
$owl_lang->last_modified			= "Date de modification";
$owl_lang->newpassword  			= "Nouveau mot de passe";
$owl_lang->newsadmin    			= "Administrateur de news";
$owl_lang->not_member   			= "Vous n'&ecirc;tes membre d'aucun autre groupe";
$owl_lang->notification 			= "Recevoir des notifications";
$owl_lang->oldpassword  			= "Ancien mot de passe";
$owl_lang->preference   			= "Pr&eacute;f&eacute;rences";
$owl_lang->saved        			= "Modifications sauvegard&eacute;es";
$owl_lang->status_no    			= "NON";
$owl_lang->status_yes   			= "OUI";
$owl_lang->useradmin				= "Utilisateur Administrateur";
$owl_lang->userlang     			= "Langue";
$owl_lang->viewlogs     			= "Peut voir les logs syst&egrave;me";
$owl_lang->viewreports  			= "Peut voir les rapports syst&egrave;me";

//********************************************************
// prefs.php  END
//********************************************************


//********************************************************
// search.php  BEGIN
//********************************************************

$owl_lang->elapsed_time         		= "Temps pass&eacute;";
$owl_lang->query_empty          		= "Le champ de recherche est vide";
$owl_lang->related_query_empty  		= "Aucune m&eacute;ta-donn&eacute;e associ&eacute;e au document pour effectuer une recherche des documents en rapport";
$owl_lang->search               		= "Recherche";
$owl_lang->search_all_word      		= "Tous les mots";
$owl_lang->search_any_word      		= "Au moins un mot";
$owl_lang->search_currentfolder 		= "Recherche dans le dossier courant";
$owl_lang->search_entire_phrase 		= "Expression exacte";
$owl_lang->search_for_files     		= "Recherche de fichiers";
$owl_lang->search_for_folders   		= "Recherche de dossiers";
$owl_lang->search_match         		= "Type de recherche :";
$owl_lang->search_results_for   		= "R&eacute;sultats de recherche pour";
$owl_lang->search_score         		= "Fichiers trouv&eacute;s";
//modif misterphi
$owl_lang->search_winthindocs   		= "Recherche dans les documents index&eacute;s (txt,pdf,...)";//text, pdf et MS-Word)";
//eom
//********************************************************
// search.php  END
//********************************************************


//********************************************************
// view.php  BEGIN
//********************************************************

$owl_lang->viewing           			= "Visualiser ";

//********************************************************
// view.php  END
//********************************************************


//********************************************************
// admin/index.php  BEGIN
//********************************************************

$owl_lang->add                          = "Ajouter";
$owl_lang->edit_user                    = "***** Modification de l'utilisateur  *****";
$owl_lang->edit_group                   = "***** Modification du groupe *****";
$owl_lang->enter_new_user               = "***** Ajout de nouvel utilisateur *****";
$owl_lang->enter_new_group              = "***** Ajout de nouveau groupe *****";
$owl_lang->info_panel_width             = "Afficher le panneau d'informations &eacute;tendues ";
$owl_lang->allow_track_favorite         = "Authoriser l'utilisateur &agrave; tracer les favoris ";
$owl_lang->admin_login_to_browse_page   = "Apr&egrave;s identification, l'admin acc&egrave;de directement aux documents";
$owl_lang->alt_backup			= "Sauvegarde de la base de donn&eacute;es SQL";
$owl_lang->alt_btn_change_passwd        = "Changer votre ancien Mot de Passe";
$owl_lang->alt_btn_cln_history          = "Purger le documents de plus de (jours): ";
$owl_lang->alt_btn_log_viewer           = "Visionneuse du log syst&egrave;me Owl";
$owl_lang->alt_btn_report_viewer        = "Visionneuse de rapports Owl";
$owl_lang->alt_doctype_admin		= "Gestion des types de documents";
$owl_lang->alt_html_prefs		= "Pr&eacute;f&eacute;rences HTML";
$owl_lang->alt_initial_load		= "Chargement de la base SQL depuis le r&eacute;pertoire";
$owl_lang->alt_log_viewer		= "Visualisation des logs";
$owl_lang->alt_news_admin		= "Administration des news";
$owl_lang->alt_site_features		= "Modification de la configuration du site";
$owl_lang->alt_statistics_viewer	= "Visualisation des statistiques";
$owl_lang->alt_trashcan			= "Visualiser la corbeille";
$owl_lang->alt_users_groups		= "Administrer les utilisateurs et les groupes";
$owl_lang->anonymous_account            = "Compte d'acc&egrave;s anonyme :";
$owl_lang->btn_backup			= "Sauvegarde<br />base SQL";
$owl_lang->btn_change_passwd            = "Changer Mot de Passe";
$owl_lang->btn_cln_history              = "Purger l'historique";
$owl_lang->btn_doctype_admin		= "Types de<br />documents";
$owl_lang->btn_html_prefs		= "Pr&eacute;f&eacute;rences<br />HTML";
$owl_lang->btn_initial_load		= "Chargement<br />initial";
$owl_lang->btn_log_viewer               = "Logs du syst&egrave;me ";
$owl_lang->btn_log_viewer		= "Visualiser<BR/>les logs";
$owl_lang->btn_news_admin		= "Administration<br />des news";
$owl_lang->btn_report_viewer            = "Rapports";
$owl_lang->btn_site_features		= "Configuration<br />du site";
$owl_lang->btn_statistics_viewer	= "Visualiser<BR/>les stats";
$owl_lang->btn_trashcan			= "Corbeille";
$owl_lang->btn_users_groups		= "Utilisateurs<br />et groupes";
$owl_lang->buttonstyle                  = "Style des boutons";
$owl_lang->confirm_cln_history          = "Etes-vous sur de vouloir purger les document de plus de (jours): ";
$owl_lang->confirm_initial_load		= "*** ATTENTION *** Ne le faire que lorsque la base est vide";
$owl_lang->deletegroup                  = "Supprimer un groupe";
$owl_lang->deleteuser                   = "Supprimer un utilisateur";
$owl_lang->disable_recs_per_page        = "Activer l'affichage r&eacute;sum&eacute; d&eacute;sactivera l'affichage des enregistrements par page";
$owl_lang->disableuser                  = "D&eacute;sactiver l'utilisateur";
$owl_lang->display_password_override    = "Afficher la r&eacute;&eacute;criture du mot de passe";
$owl_lang->doc_id_num_digits            = "Nombre de chiffres affich&eacute;s pour le N&deg; de document";
$owl_lang->doc_id_prefix                = "Pr&eacute;fixe de N&deg; de document";
$owl_lang->docicon_column               = "Ic&ocirc;ne";
$owl_lang->download_folder_zip          = "T&eacute;l&eacute;charger les dossier au format ZIP (tar.gz par d&eacute;fault)";
$owl_lang->file_admin_group             = "Groupe admin pour fichier/explorateur :";
$owl_lang->flushed                      = "Table des sessions vid&eacute;e, l'utilisateur peut &agrave; nouveau se connecter.<br />";
$owl_lang->groups                       = "Groupes";
$owl_lang->hide_bulk_buttons            = "Masquer les boutons d'actions group&eacute;s :";
$owl_lang->historical_purge             = "Fichiers historiques supprim&eacute;s";
$owl_lang->save_keywords_to_db          = "Autorise les utilisateurs &agrave; sauvegarder les mots-cl&eacute;s pour les r&eacute;utiliser";
$owl_lang->status_column                = "Statut";
$owl_lang->vermajor_initial             = "Revision majeure initiale";
$owl_lang->verminor_initial             = "Revision mineure initiale";
$owl_lang->view_doc_in_new_window       = "Visualisation des documents dans une nouvelle fen&ecirc;tre";
//modif3 misterphi
$owl_lang->btn_admin_user_group	= "Gestion des groupes";
//eom
// Edit HTML Properties Messages
$owl_lang->ht_bd_bg_image               = "Chemin de l'image d'arri&egrave;re-plan :";
$owl_lang->ht_bd_lnk_cl                 = "Couleur des liens :";
$owl_lang->ht_bd_txt_cl                 = "Couleur du texte :";
$owl_lang->ht_bd_vlnk_cl                = "Couleur des liens visit&eacute;s :";
$owl_lang->ht_collapse_width            = "Largeur de la vue r&eacute;duite :";
$owl_lang->ht_expand_width              = "Largeur de la vue &eacute;tendue :";
$owl_lang->ht_tbl_border_sz             = "Taille de la bordure du Tableau :";
$owl_lang->ht_tbl_cell_bg_al            = "Couleur d'arri&egrave;re-plan des cellules 2 :";
$owl_lang->ht_tbl_cell_bg_cl            = "Couleur d'arri&egrave;re-plan des cellules :";
$owl_lang->ht_tbl_hd_bg                 = "Arri&egrave;re-plan de l'ent&ecirc;te :";

// Edit Prefs Properties Messages
$owl_lang->anonymous_access             = "Acc&egrave;s anonyme r&eacute;gl&eacute;";
$owl_lang->auth_http                    = "Authentification HTTP (.htaccess)";
$owl_lang->auth_ldap                    = "Authentication LDAP";
$owl_lang->auth_owl                     = "Authentication Owl";
$owl_lang->auth_pop3                    = "Authentication Pop3";
$owl_lang->file_desc_required           = "Description de fichier n&eacute;cessaire";
$owl_lang->folder_desc_required         = "Description de r&eacute;pertoire n&eacute;cessaire";
$owl_lang->header_group_admin           = "Administration des groupes";
$owl_lang->header_sitefeatures_settings = "Configuration du site";
$owl_lang->header_user_admin            = "Administration des utilisateurs";
$owl_lang->hide_folder_doc_count        = "Masquer l'affichage du nombre de types de fichiers";
$owl_lang->hide_folder_size             = "Masquer l'affichage de la taille des dossiers";
$owl_lang->hide_panel                   = "Masquer";
$owl_lang->home_dir                     = "Dossier de base de l'utilisateur:";
$owl_lang->html_title			= "Pr&eacute;f&eacute;rences HTML";
$owl_lang->initial_dir                  = "Dossier de base pour l'utilisateur";
$owl_lang->maxsessions                  = "Nombre max. de sessions";
$owl_lang->noprefaccess                 = "D&eacute;sactiver les pr&eacute;f&eacute;rences";
$owl_lang->old_action_icons             = "Utiliser les ic&ocirc;nes d'action pre Owl 0.80 (sans menus d&eacute;roulants)";
$owl_lang->owl_anon_download            = "T&eacute;l&eacute;chargement seulement";
$owl_lang->owl_anon_full                = "Tous les acc&egrave;s";
$owl_lang->owl_anon_ro                  = "Les acc&egrave;s anonymes sont en lecture seule :";
$owl_lang->owl_dbdump_path              = "Chemin de l'outil de sauvegarde de la base :";
$owl_lang->owl_def_file_group_owner     = "Groupe propri&eacute;taire par d&eacute;faut :";
$owl_lang->owl_def_file_meta            = "M&eacute;ta-donn&eacute;e de fichier par d&eacute;faut :";
$owl_lang->owl_def_file_owner           = "Propri&eacute;taire de fichier par d&eacute;faut :";
$owl_lang->owl_def_file_perm            = "Attributs de cr&eacute;ation de fichier par d&eacute;faut :";
$owl_lang->owl_def_file_security        = "S&eacute;curit&eacute; de fichier par d&eacute;faut :";
$owl_lang->owl_def_file_title           = "Nom de fichier par d&eacute;faut (Aucun si m&ecirc;me nom) :";
$owl_lang->owl_def_fold_group_owner     = "Groupe propri&eacute;taire de dossier par d&eacute;faut :";
$owl_lang->owl_def_fold_owner           = "Propri&eacute;taire de dossier par d&eacute;faut :";
$owl_lang->owl_def_fold_sec             = "S&eacute;curit&eacute; de dossier par d&eacute;faut :";
$owl_lang->owl_def_folder_perm          = "Attributs de cr&eacute;ation de dossier par d&eacute;faut :";
$owl_lang->owl_default_tmpdir           = "Emplacement du r&eacute;pertoire 'Temp' : ";
$owl_lang->owl_email_from               = "E-Mail de l'exp&eacute;diteur / Connexion SMTP :";
$owl_lang->owl_email_fromname           = "Nom de l'exp&eacute;diteur :";
$owl_lang->owl_email_replyto            = "E-Mail de r&eacute;ponse :";
$owl_lang->owl_email_server             = "Serveur SMTP :";
$owl_lang->owl_email_smtp               = "Utiliser l'envoi par SMTP";
$owl_lang->owl_email_smtp_auth          = "Utiliser l'identification par SMTP";
$owl_lang->owl_email_smtp_auth_login    = "SMTP Auth Login :";
$owl_lang->owl_email_smtp_auth_passwd   = "Mot de passe SMTP";
$owl_lang->owl_email_subject_pref       = "Pr&eacute;fixe d'objet :";
$owl_lang->owl_fogotpass                = "Afficher le lien 'Mot de passe oubli&eacute;' :";
$owl_lang->owl_group_user               = "Utilisateurs membres de ce groupe";
$owl_lang->owl_gzip_path                = "Chemin vers gzip :";
$owl_lang->owl_hidebackup               = "Cacher le dossier des sauvegardes :";
$owl_lang->owl_log_file                 = "Loguer les activit&eacute;s de fichiers :";
$owl_lang->owl_log_login_act            = "Loguer les activit&eacute;s de connexion/d&eacute;connexion :";
$owl_lang->owl_log_rec_page             = "Nombre d'enregistrements par page de Log :";
$owl_lang->owl_logging                  = "Autorisez la gestion des logs ";
$owl_lang->owl_logo                     = "Logo de la page de connexion ([Langue]/graphics) :";
$owl_lang->owl_lookAtHD                 = "D&eacute;sactiver la surveillance du disque dur ";
$owl_lang->owl_lookAtHDDel              = "Retirer les fichiers supprim&eacute;s de la BD :";
$owl_lang->owl_max_filesize             = "Taille maximale pour l'upload d'un fichier ";
$owl_lang->owl_owl_expand               = "Affichage par d&eacute;faut de l'explorateur (d&eacute;taill&eacute;) ";
$owl_lang->owl_owl_timeout              = "Dur&eacute;e max. d'une session ";
$owl_lang->owl_pdftotext_path           = "Maintenir l'index de recherche pour <br />les fichiers PDF et textes <br />&#8211; Laisser vide pour d&eacute;sactiver :";
$owl_lang->owl_pod2html_path            = "Perl 5.8.0 ou plus r&eacute;cent requis<br />Chemin vers pod2html :";
$owl_lang->owl_restrict_view            = "Autoriser l'affichage r&eacute;sum&eacute; ";
$owl_lang->owl_self_reg                 = "Autorisez l'inscription automatique des utilisateurs ";
$owl_lang->owl_self_reg_hd              = "INSCRIPTION AUTOMATIQUE DES UTILISATEURS";
$owl_lang->owl_tar_path                 = "Chemin vers tar :";
$owl_lang->owl_title_browser            = "PARAM&#200;TRES DE L'EXPLORATEUR OWL";
$owl_lang->owl_title_custom             = "PERSONNALISER LES COLONNES DE L'EXPLORATEUR";
$owl_lang->owl_title_custom_search      = "PERSONNALISER LES RESULTATS DE RECHERCHE";
$owl_lang->owl_title_defaults           = "ATTRIBUTS PAR D&#201;FAUT";
$owl_lang->owl_title_email              = "PARAMETRES E-MAIL DE OWL";
$owl_lang->owl_title_HD                 = "PARAMETRES DE SURVEILLANCE DU DISQUE DUR POUR LES NOUVEAUX FICHIERS";
$owl_lang->owl_title_logging            = "CONFIGURATION DES LOGS";
$owl_lang->owl_title_other              = "CONFIGURATION G&#201;N&#201;RALE";
$owl_lang->owl_title_tools              = "OUTILS DE SAUVEGARDE DE LA BASE";
$owl_lang->owl_unzip_path               = "Chemin vers unzip :";
$owl_lang->owl_version_control          = "Autoriser le contr&ocirc;le de version ";
$owl_lang->owl_wordtotext_path          = "les fichier MS-WORD <br />&#8211;  Laisser vide pour d&eacute;sactiver :";
$owl_lang->pass_account_lockout      	= "Nombre de tentatives de d&eacute;connexion:";
$owl_lang->pass_change_every            = "Forcer le changement de Mot Passe tous les (en Jours):";
$owl_lang->pass_enable_account_lockout  = "Activer la d&eacute;connexion de compte:";
$owl_lang->pass_min_passwd_length       = "Longueur minimum des Mots de Passe utilisateurs:";
$owl_lang->pass_min_passwd_numeric      = "Nombre minimum de chiffres:";
$owl_lang->pass_min_passwd_special      = "Nombre minimum de caract&egrave;res sp&eacute;ciaux:";
$owl_lang->pass_min_username_length     = "Longueur minimum des Identifiants utilisateurs:";
$owl_lang->pass_track_old_passwords   	= "Conserver le nombre de Mot de Passes anterieurs";
$owl_lang->quota                        = "Capacit&eacute; 0=D&eacute;sactiv&eacute;";
$owl_lang->reallydeluser                = "Cet utilisateur poss&egrave;de %numfiles% Fichier(s) et %numfolders% Dossier(s). Ce(s) Fichier(s) et ce(s) Dossier(s) deviendront orphelins. Etes-vous toujours d'accord pour supprimer cet utilisateur ?";
$owl_lang->recs_per_page                = "Affiche le nombre d'enregistrement par Page (0= non actif) :";
$owl_lang->recycle_enabled              = "Activer la corbeille ";
$owl_lang->recycle_location             = "Emplacement de la Corbeille :";
$owl_lang->recycle_title                = "PARAM&#200;TRES DE LA CORBEILLE";
$owl_lang->remember_me                  = "Affiche le lien 'Souvenez-vous de moi'";
$owl_lang->remember_me_checkbox         = "Souvenez-vous de moi";
$owl_lang->remember_me_title            = "PARAMETRES DE LOGIN PERSISTANT";
$owl_lang->remember_timeout             = "Dur&eacute;e de vie du cookie en jours";
$owl_lang->restore_file_prefix          = "Pr&eacute;fixe pour les fichiers restaur&eacute;s";
$owl_lang->rtftotext_path               = "Chemin de l'utilitaire RTF vers Texte";
$owl_lang->search_result_folders        = "Inclure les dossiers dans les r&eacute;sultats de recherche";
$owl_lang->show_action_title            = "Afficher les boutons d'actions :";
$owl_lang->show_bulk_title              = "Afficher les boutons d'action en nombre :";
$owl_lang->show_folder_title            = "Afficher les outils dossier :";
$owl_lang->show_panel                   = "Afficher";
$owl_lang->show_panel_title             = "Afficher le panneau de statut :";
$owl_lang->show_pref_title              = "Afficher la barre de Pref/d&eacute;connexion :";
$owl_lang->show_search_title            = "Afficher la barre de recherche :";
$owl_lang->show_user_info           	= "Les utilisateurs affich&eacute;s sont consultables";
$owl_lang->status_bar_both              = "Les deux";
$owl_lang->status_bar_bottom            = "En bas";
$owl_lang->status_bar_location          = "Afficher la barre d'&eacute;tat :";
$owl_lang->status_bar_not               = "Ne pas afficher";
$owl_lang->status_bar_top               = "En haut";
$owl_lang->cust_popup                   = "Personnaliser les champs pr&eacute;sent dans les popup";
$owl_lang->cust_headpopup     		= "CHAMPS DU DOCUMENT SPECIFIQUE";
$owl_lang->use_popup                    = "Utilser les 'popup' pour afficher les news ";
$owl_lang->user_admin              	= "Administrateur d'Utilisateur/Groupe";
$owl_lang->user_auth_type               = "Type de l'authentification:";
$owl_lang->user_change_pass_next_login  = "Changer le mot de Passe &agrave; la prochaine Connexion:";
$owl_lang->user_expires                 = "Le compte expire le:";
$owl_lang->user_gmt_offset              = "R&eacute;glage GMT de l'utilisateur";
$owl_lang->user_group_admin             = "Administrateur de fichier pour le groupe primaire:";
$owl_lang->user_login_to_newrecords     = "A la connexion, aller &agrave; la page des nouveaux documents:";
$owl_lang->users                        = "Utilisateurs";
$owl_lang->users_group_admin		= "Utilisateurs qui sont administrateurs de groupe";
$owl_lang->users_in_primary_group       = "Utilisateurs qui ont ce groupe comme PRIMAIRE";
$owl_lang->create_user_homedir 		= "Create User HomeDir:";
$owl_lang->use_captcha 			= "Use CAPTCHA:";
$owl_lang->created_here 		= "<br /> (CREATED HERE IF create homedir checked)";
$owl_lang->virus_path                   = "Chemin vers l'anti-virus<br />Example : '/usr/bin/clamscan --quiet' <br />&#8211; Laissez vide pour d&eacute;sactiver :";
$owl_lang->time_offsets[0][0] 		= "-12";
$owl_lang->time_offsets[0][1] 		= "(GMT-12:00) Eniwetok, Kwajalein";
$owl_lang->time_offsets[1][0] 		= "-11";
$owl_lang->time_offsets[1][1] 		= "(GMT-11:00) Midway Island, Samoa";
$owl_lang->time_offsets[2][0] 		= "-10";
$owl_lang->time_offsets[2][1] 		= "(GMT-10:00) Hawaii";
$owl_lang->time_offsets[3][0] 		= "-9";
$owl_lang->time_offsets[3][1] 		= "(GMT-9:00) Alaska";
$owl_lang->time_offsets[4][0] 		= "-8";
$owl_lang->time_offsets[4][1] 		= "(GMT-8:00) Pacific Time (US & Canada)";
$owl_lang->time_offsets[5][0] 		= "-7";
$owl_lang->time_offsets[5][1] 		= "(GMT-7:00) Mountain Time (US & Canada)";
$owl_lang->time_offsets[6][0] 		= "-6";
$owl_lang->time_offsets[6][1] 		= "(GMT-6:00) Central Time (US & Canada), Mexico City";
$owl_lang->time_offsets[7][0] 		= "-5";
$owl_lang->time_offsets[7][1] 		= "(GMT-5:00) Eastern Time (US & Canada), Bogota, Lima, Quito";
$owl_lang->time_offsets[8][0] 		= "-4";
$owl_lang->time_offsets[8][1] 		= "(GMT-4:00) Atlantic Time (Canada), Caracas, La Paz";
$owl_lang->time_offsets[9][0] 		= "-3.5";
$owl_lang->time_offsets[9][1] 		= "(GMT-3:30) Newfoundland";
$owl_lang->time_offsets[10][0] 		= "-3";
$owl_lang->time_offsets[10][1] 		= "(GMT-3:00) Brazil, Buenos Aires, Georgetown";
$owl_lang->time_offsets[11][0] 		= "-2";
$owl_lang->time_offsets[11][1] 		= "(GMT-2:00) Mid-Atlantic";
$owl_lang->time_offsets[12][0] 		= "-1";
$owl_lang->time_offsets[12][1] 		= "(GMT-1:00) Azores, Cape Verde Islands";
$owl_lang->time_offsets[13][0] 		= "0";
$owl_lang->time_offsets[13][1] 		= "(GMT) Western Europe Time, London, Lisbon, Casablanca, Monrovia";
$owl_lang->time_offsets[14][0] 		= "1";
$owl_lang->time_offsets[14][1] 		= "(GMT+1:00) Central Europe Time, Brussels, Copenhagen, Madrid, Paris";
$owl_lang->time_offsets[15][0] 		= "2";
$owl_lang->time_offsets[15][1] 		= "(GMT+2:00) Eastern Europe Time, Kaliningrad, South Africa";
$owl_lang->time_offsets[16][0] 		= "3";
$owl_lang->time_offsets[16][1] 		= "(GMT+3:00) Baghdad, Kuwait, Riyadh, Moscow, St. Petersburg, Volgograd, Nairobi";
$owl_lang->time_offsets[17][0] 		= "3.5";
$owl_lang->time_offsets[17][1] 		= "(GMT+3:30) Tehran";
$owl_lang->time_offsets[18][0] 		= "4";
$owl_lang->time_offsets[18][1] 		= "(GMT+4:00) Abu Dhabi, Muscat, Baku, Tbilisi";
$owl_lang->time_offsets[19][0] 		= "4.5";
$owl_lang->time_offsets[19][1] 		= "(GMT+4:30) Kabul";
$owl_lang->time_offsets[20][0] 		= "5";
$owl_lang->time_offsets[20][1] 		= "(GMT+5:00) Ekaterinburg, Islamabad, Karachi, Tashkent";
$owl_lang->time_offsets[21][0] 		= "5.5";
$owl_lang->time_offsets[21][1] 		= "(GMT+5:30) Bombay, Calcutta, Madras, New Delhi";
$owl_lang->time_offsets[22][0] 		= "6";
$owl_lang->time_offsets[22][1] 		= "(GMT+6:00) Almaty, Dhaka, Colombo";
$owl_lang->time_offsets[23][0] 		= "7";
$owl_lang->time_offsets[23][1] 		= "(GMT+7:00) Bangkok, Hanoi, Jakarta";
$owl_lang->time_offsets[24][0] 		= "8";
$owl_lang->time_offsets[24][1] 		= "(GMT+8:00) Beijing, Perth, Singapore, Hong Kong, Chongqing, Urumqi, Taipei";
$owl_lang->time_offsets[25][0] 		= "9";
$owl_lang->time_offsets[25][1] 		= "(GMT+9:00) Tokyo, Seoul, Osaka, Sapporo, Yakutsk";
$owl_lang->time_offsets[26][0] 		= "9.5";
$owl_lang->time_offsets[26][1] 		= "(GMT+9:30) Adelaide, Darwin";
$owl_lang->time_offsets[27][0] 		= "10";
$owl_lang->time_offsets[27][1] 		= "(GMT+10:00) East Australian Standard, Guam, Papua New Guinea, Vladivostok";
$owl_lang->time_offsets[28][0] 		= "11";
$owl_lang->time_offsets[28][1] 		= "(GMT+11:00) Magadan, Solomon Islands, New Caledonia";
$owl_lang->time_offsets[29][0] 		= "12";
$owl_lang->time_offsets[29][1] 		= "(GMT+12:00) Auckland, Wellington, Fiji, Kamchatka, Marshall Island";

//********************************************************
// admin/index.php  END
//********************************************************


//********************************************************
// admin/log.php  BEGIN
//********************************************************

$owl_lang->owl_log_filter       = "Filtre";
$owl_lang->owl_log_hd_action    = "Action";
$owl_lang->owl_log_hd_agent     = "Agent";
$owl_lang->owl_log_hd_dt_tm     = "Date/Heure";
$owl_lang->owl_log_hd_dtls      = "D&eacute;tails";
$owl_lang->owl_log_hd_file      = "Fichier";
$owl_lang->owl_log_hd_fld_path  = "Chemin de dossier";
$owl_lang->owl_log_hd_ip        = "Adresse IP";
$owl_lang->owl_log_hd_user      = "Utilisateur";
$owl_lang->owl_log_hide         = "Cacher : ";
$owl_lang->owl_log_no_rec       = " AUCUN ENREGISTREMENT TROUV&#201; ";
$owl_lang->owl_log_viewer       = "Visionneuse de LOG";

//********************************************************
// admin/log.php  END
//********************************************************


//********************************************************
// admin/recycle.php  BEGIN
//********************************************************

$owl_lang->empty_trash          = "Vider la corbeille";
$owl_lang->really_empty_trash   = "Voulez-vous vraiment vider la corbeille?";
$owl_lang->recycle_bin_admin    = "Administration de la corbeille";
$owl_lang->trash_email_body  	= "Les fichiers demand&eacute;s sont attach&eacute;s.";
$owl_lang->trash_email_subject  = "FICHIERS RECUPERES QUE VOUS AVEZ DEMANDE";

//********************************************************
// admin/recycle.php  END
//********************************************************


//********************************************************
// lib/owl.lib.php  BEGIN
//********************************************************

$owl_lang->donothing                            = "Ne rien changer &#8211; laisser permissions en l'&eacute;tat";
$owl_lang->everyoneread                         = "Lecture/T&eacute;l&eacute;chargement par tous";
$owl_lang->everyoneread_ad                      = "Lecture/T&eacute;l&eacute;chargement par tous";
$owl_lang->everyonewrite                        = "Lecture/Ecriture/T&eacute;l&eacute;chargement par tous";
$owl_lang->everyonewrite_ad                     = "Lecture/Ecriture/T&eacute;l&eacute;chargement par tous";
$owl_lang->everyonewrite_ad_nod                 = "Lecture/Ecriture/T&eacute;l&eacute;chargement par tous, SANS SUPPRESSION";
$owl_lang->everyonewrite_nod                    = "Lecture/Ecriture/T&eacute;l&eacute;chargement par tous, SANS SUPPRESSION";
$owl_lang->file_size_byte                       = "b";
$owl_lang->file_size_gigabyte                   = "go";
$owl_lang->file_size_kilobyte                   = "ko";
$owl_lang->file_size_megabyte                   = "mo";
$owl_lang->geveryoneread                        = "Lecture du dossier par tous";
$owl_lang->geveryoneread_ad                     = "Lecture du dossier par tous";
$owl_lang->geveryonewrite                       = "T&eacute;l&eacute;chargement et suppression du dossier par tous";
$owl_lang->geveryonewrite_ad                    = "T&eacute;l&eacute;chargement et suppression du dossier par tous";
$owl_lang->geveryonewrite_ad_nod                = "T&eacute;l&eacute;chargement du dossier par tous";
$owl_lang->geveryonewrite_nod                   = "T&eacute;l&eacute;chargement par tous";
$owl_lang->ggroupread                           = "Lecture du dossier par votre groupe";
$owl_lang->ggroupread_ad                        = "Lecture du dossier par le groupe s&eacute;lectionn&eacute;";
$owl_lang->ggroupwrite                          = "T&eacute;l&eacute;chargement et suppression du dossier par votre groupe";
$owl_lang->ggroupwrite_ad                       = "T&eacute;l&eacute;chargement et suppression du dossier par le groupe s&eacute;lectionn&eacute;";
$owl_lang->ggroupwrite_ad_nod                   = "T&eacute;l&eacute;chargement du dossier par le groupe s&eacute;lectionn&eacute;";
$owl_lang->ggroupwrite_nod                      = "T&eacute;l&eacute;chargement du dossier par votre groupe";
$owl_lang->ggroupwrite_worldread                = "Votre groupe peut ajouter des fichiers et effacer ce r&eacute;pertoire & tout le monde peut voir le contenu";
$owl_lang->ggroupwrite_worldread_ad             = "Le groupe s&eacute;lectionn&eacute; peut ajouter des fichiers et effacer ce r&eacute;pertoire & tout le monde peut voir le contenu";
$owl_lang->ggroupwrite_worldread_ad_nod         = "Le groupe s&eacute;lectionn&eacute; peut ajouter des fichiers & tout le monde peut voir le contenu, SANS SUPPRESSION";
$owl_lang->ggroupwrite_worldread_nod            = "Votre groupe peut ajouter des fichiers & tout le monde peut voir le contenu, SANS SUPPRESSION";
$owl_lang->gonlyyou                             = "T&eacute;l&eacute;chargement et suppression du dossier par vous seul";
$owl_lang->gonlyyou_ad                          = "T&eacute;l&eacute;chargement et suppression du dossier par vous seul";
$owl_lang->groupread                            = "Lecture/T&eacute;l&eacute;chargement par votre groupe";
$owl_lang->groupread_ad                         = "Lecture/T&eacute;l&eacute;chargement par le groupe s&eacute;lectionn&eacute;";
$owl_lang->groupwrite                           = "Lecture/Ecriture/T&eacute;l&eacute;chargement par votre groupe";
$owl_lang->groupwrite_ad                        = "Lecture/Ecriture/T&eacute;l&eacute;chargement par le groupe s&eacute;lectionn&eacute;";
$owl_lang->groupwrite_ad_nod                    = "Lecture/Ecriture/T&eacute;l&eacute;chargement par le groupe s&eacute;lectionn&eacute;, SANS SUPPRESSION";
$owl_lang->groupwrite_nod                       = "Lecture/Ecriture/T&eacute;l&eacute;chargement par votre groupe, SANS SUPPRESSION";
$owl_lang->groupwrite_worldread                 = "Votre groupe peut lire/&eacute;crire/t&eacute;l&eacute;charger & tout le monde peut lire";
$owl_lang->groupwrite_worldread_ad              = "Le groupe s&eacute;lectionn&eacute; peut lire/&eacute;crire/t&eacute;l&eacute;charger & tout le monde peut lire";
$owl_lang->groupwrite_worldread_ad_nod          = "Le groupe s&eacute;lectionn&eacute; peut lire/&eacute;crire/t&eacute;l&eacute;charger (SANS SUPPRIMER) & tout le monde peut lire";
$owl_lang->groupwrite_worldread_nod             = "Votre groupe peut lire/&eacute;crire/t&eacute;l&eacute;charger (SANS SUPPRIMER) & tout le monde peut lire";
$owl_lang->invalidsess                          = "Index de session invalide, connectez vous depuis la page d'accueil<br />";
$owl_lang->notif_comment_1                      = "comment&eacute; le";
$owl_lang->notif_comment_2                      = "Commentaire :";
$owl_lang->notif_msg                            = "CECI EST UN MESSAGE AUTOMATIQUE<br /><br />Veuillez trouver ci-joint, le fichier ajout&eacute; ou mis &agrave; jour<br /><br />";
$owl_lang->notif_msg_alt                        = "CECI EST UN MESSAGE AUTOMATIQUE\n\nVeuillez trouver ci-joint, le fichier ajout&eacute; ou mis &agrave; jour\n\n";
$owl_lang->notif_msg_alt_link           	= "CECI EST UN MESSAGE AUTOMATIQUE\n\nVeuillez trouver ci-joint,  un lien vers un nouveau fichier ou un fichier mis &agrave; jour\n\n";
$owl_lang->notif_msg_link               	= "CECI EST UN MESSAGE AUTOMATIQUE<br /><br />Veuillez trouver ci-joint,  un lien vers un nouveau fichier ou un fichier mis &agrave; jour<br /><br />";
$owl_lang->notif_salutation                     = "Cher";
$owl_lang->notif_subject_comment                = "Un fichier OWL Intranet -- Commentaire Ajout&eacute;";
$owl_lang->notif_subject_monitor                = "Un fichier surveill&eacute; a &eacute;t&eacute; modifi&eacute;";
$owl_lang->notif_subject_new                    = "Ajout d'un nouveau fichier sur le serveur";
$owl_lang->notif_subject_upd                    = "Mise &agrave; jour d'un fichier sur le serveur";
$owl_lang->notif_subject_file_deleted           = "FILE DELETED FROM REPOSITORY";
$owl_lang->notif_subj_new_account               = "A NEW OWL ACCOUNT WAS CREATED FOR YOU";
$owl_lang->notif_user                   	= "Cr&eacute;&eacute;/mis &agrave; jour par :";
$owl_lang->onlyyou                              = "Lecture/Ecriture/T&eacute;l&eacute;chargement par vous seul";
$owl_lang->onlyyou_ad                           = "Lecture/Ecriture/T&eacute;l&eacute;chargement par vous seul";
$owl_lang->orphaned                     	= "&#8211; PAS DE PROPRIETAIRE  &#8211;";
$owl_lang->owl_path                             = "Chemin du fichier : ";
$owl_lang->sessinuse                            = "Acc&egrave;s refus&eacute;, ce compte est en cours d'utilisation<br />";
$owl_lang->sesstimeout                          = "Votre session s'est termin&eacute;e, veuillez vous reconnecter<br />";
$owl_lang->virus_infected               	= "FICHIER INFECTE PAR UN VIRUS";
$owl_lang->virus_return_val             	= "L'anti-virus retourne la valeur :";
$owl_lang->watermark_string             	= "Confidential";
$owl_lang->watermark_source             	= "Owl Repository 0.91";


//********************************************************
// lib/owl.lib.php  END
//********************************************************


//********************************************************
// HREF TITLE messages  BEGIN
//********************************************************

$owl_lang->title_browse_folder          = "Explorer ce dossier";
$owl_lang->title_browse_site            = "Explorer cette URL";
$owl_lang->title_download_view          = "T&eacute;l&eacute;charger / Visualiser ce fichier";
$owl_lang->title_edit_prefs             = "Visualiser / &eacute;diter les pr&eacute;f&eacute;rences d'utilisateur";
$owl_lang->title_return_folder          = "Retourner vers";
$owl_lang->title_sort                   = "Trier par";
$owl_lang->title_view_my                = "Visualiser la liste de mes fichiers";
$owl_lang->title_view_new               = "Voir la liste des nouveaux fichiers depuis ma derni&egrave;re visite";
$owl_lang->title_view_updated           = "Voir la liste des fichiers mis &agrave; jour depuis ma derni&egrave;re visite";

//********************************************************
// HREF TITLE messages  END
//********************************************************


//********************************************************
// admin/news.php and newsread.php BEGIN
//********************************************************

$owl_lang->news_content                 = "Contenu";
$owl_lang->news_hd                      = "News";
$owl_lang->news_hd_audience             = "Public vis&eacute;";
$owl_lang->news_hd_created              = "Cr&eacute;&eacute; le";
$owl_lang->news_hd_expires              = "Expiration le";
$owl_lang->news_heading                 = "Ent&ecirc;te de news";
$owl_lang->news_posted_date             = "News post&eacute;e :";
$owl_lang->news_read                    = "Lue";
$owl_lang->news_title                   = "Administration des news";

//********************************************************
// admin/news.php and newsread.php  END
//********************************************************


//********************************************************
// DEBUG MESSAGES BEGIN
//********************************************************

$owl_lang->debug_doc_not_exists         = "DEBUG : Le dossier documents d'OWL : " .$default->owl_FileDir . "/" . fid_to_name(1) ." n'existe pas.";
$owl_lang->debug_doc_not_writeable      = "DEBUG : Le dossier documents d'OWL : " .$default->owl_FileDir . "/" . fid_to_name(1) ." n'est pas autoris&eacute; en &eacute;criture sur ce serveur.";
$owl_lang->debug_file_not_exist         = "DEBUG : Le fichier requis '$default->owl_FileDir/$filename' n'existe pas.<br /> OR<br />Le chemin sp&eacute;cifi&eacute; par \$default->owl_FileDir in config/owl.php est mauvais.";
$owl_lang->debug_tmp_not_exists         = "DEBUG : Le dossier TEMP de OWL : $default->owl_tmpdir n'existe pas.";
$owl_lang->debug_tmp_not_writeable      = "DEBUG : Le dossier TEMP de OWL : $default->owl_tmpdir n'est pas libre en &eacute;criture.";
$owl_lang->debug_webserver_no_access    = "DEBUG : Le serveur web n'a pas les droits en lecture pour : <br />'$default->owl_FileDir/$filename'. <br />SVP, r&eacute;gler le droit Correspondant et r&eacute;essayez";

//********************************************************
// DEBUG MESSAGES END
//********************************************************


//********************************************************
// admin/stats.php BEGIN
//********************************************************

$owl_lang->owl_stats_viewer     		= "Visionneuse de Statistiques";
$owl_lang->stats_available_reports 		= "Statistiques disponibles";
$owl_lang->stats_files          		= "Nbre de fichiers par utilisateur";
$owl_lang->stats_folders        		= "Nbre de dossiers par utilisateur";
$owl_lang->stats_information   			= "Vous devez activer la gestion des logs de connexion et de fichiers pour pouvoir consulter les statistiques";
$owl_lang->stats_select_report 			= "Selectionnez une statistique";
$owl_lang->stats_top            		= "Top 20 des T&eacute;l&eacute;chargements";
$owl_lang->stats_users          		= "Authentification des utilisateurs (Login / Failure / Logout)";
$owl_lang->stats_users_loggedin 		= "Utilisateurs connect&eacute;s";

//********************************************************
// admin/stats.php END
//********************************************************

//********************************************************
// admin/doctype.php BEGIN
//********************************************************

$owl_lang->doc_administration   		= "ADMINISTRATION DES TYPES DE DOCUMENTS";
$owl_lang->doc_field_disp_label 		= "NOM DU CHAMPS:";
$owl_lang->doc_field_disp_sep 			= "LABEL SEPARATEUR";
$owl_lang->doc_field_label      		= "Label du champ (affich&eacute;)";
$owl_lang->doc_field_name       		= "Nom du champ associ&eacute;";
$owl_lang->doc_field_pos        		= "Position du champ";
$owl_lang->doc_field_required   		= "Obligatoire ?";
$owl_lang->doc_field_sample			= "&#8211;&nbsp;&nbsp;P&nbsp;R&nbsp;E&nbsp;V&nbsp;I&nbsp;S&nbsp;U&nbsp;A&nbsp;L&nbsp;I&nbsp;S&nbsp;E&nbsp;R&nbsp;&nbsp;&#8211;";
$owl_lang->doc_field_searchable 		= "Inclus dans les recherches?";
$owl_lang->doc_field_popup  			= "Inclus dans la description du popup?";
$owl_lang->doc_field_show_in_browse 		= "Afficher l'explorateur:";
$owl_lang->doc_field_show_in_list 		= "Voir le Champs dans l'Explorateur:";
$owl_lang->doc_field_size       		= "Taille du champ";
$owl_lang->doc_field_type			= "Type de champs:";
$owl_lang->doc_field_values			= "Valeurs du champs s&eacute;par&eacute;es par | :";
$owl_lang->doc_fields 				= "Champs personnalis&eacute;s";
$owl_lang->doc_new_doc_type     		= "&#8212; Ajouter un nouveau type de document &#8212;";
$owl_lang->doc_specific         		= "CHAMPS SPECIFIQUES DU DOCUMENT :";
$owl_lang->document_type        		= "Type de Document";
$owl_lang->document_type_name   		= "Nom du type de Document";

//********************************************************
// admin/doctype.php END
//********************************************************

//********************************************************
// admin/import_users.php  BEGIN
//********************************************************

$owl_lang->group_create         = "Groupe :";
$owl_lang->header_csv_import    = "Import des utilisateurs depuis l'annuaire LDAP du LCS";
$owl_lang->header_csv_maj    = "Mise &agrave; jour des utilisateurs depuis l'annuaire LDAP du LCS";
$owl_lang->import_inserted      = "AJOUTER";
$owl_lang->import_skipped       = "SAUTER";
$owl_lang->invalid_groupid      = "groupid Invalide --> ";
$owl_lang->msg_user_exists      = "Utilisateur existant";
$owl_lang->sendcsvfile          = "T&eacute;l&eacute;charger un fichier CSV";
$owl_lang->user_created_skipped = "Utilisateur :";

//********************************************************
// admin/import_users.php END
//********************************************************

//********************************************************
// DOC PEER Review  BEGIN
//********************************************************

$owl_lang->owl_peer_review      		= "Activer l'approbation de documents :";
$owl_lang->owl_peer_review_opt  		= "L'approbation est optionnelle :";
$owl_lang->owl_title_peer_review		= "APPROBATION DE DOCUMENTS";
$owl_lang->owl_title_peer_review2		= "Approbation de documents";
$owl_lang->peer_approval_title  		= "En attente de mon approbation";
$owl_lang->peer_approved_body   		= "Votre document a &eacute;t&eacute; et approuv&eacute; :";
$owl_lang->peer_file_approved   		= "Fichier approuv&eacute; :";
$owl_lang->peer_file_final      		= "Approbation finale :";
$owl_lang->peer_file_rejected   		= "Fichier rejet&eacute; :";
$owl_lang->peer_file_to_review  		= "Fichier &agrave; approuver :";
$owl_lang->peer_final_approval  		= "[Approbation Finale] : ";
$owl_lang->peer_final_body      		= "Votre document a &eacute;t&eacute; approuv&eacute; par tous les approbateurs sollicit&eacute;s. merci de vous identifier et de les rendre publique";
$owl_lang->peer_final_body_auto 		= "Votre document a &eacute;t&eacute; approuv&eacute; par tous les approbateurs sollicit&eacute;s.<br />Votre document a &eacute;t&eacute; rendu publique.<br />";
$owl_lang->peer_msg_to_reviewer 		= "Message aux approbateurs (incluant tel et e-mail)";
$owl_lang->peer_my_approval     		= "En attente de mon approbation";
$owl_lang->peer_notif_user      		= "Cr&eacute;&eacute;/mis &agrave; jour par ";
$owl_lang->peer_pending_approval		= "Mes demandes d'approbation en cours";
$owl_lang->peer_pending_title   		= "Mes demandes d'approbation en cours";
$owl_lang->peer_reject_reason   		= "Raison du refus";
$owl_lang->peer_rejected_body   		= "Votre document a &eacute;t&eacute; rejet&eacute; :";
$owl_lang->peer_review_body     		= "Votre approbation a &eacute;t&eacute; demand&eacute;e pour le(s) document(s) suivant(s) :";
$owl_lang->peer_reviewer_list   		= "Approbateurs(s)";
$owl_lang->peer_satus           		= "Statut";
$owl_lang->peer_satus_approved  		= "&#8212; A P P R O U V E &#8212;";
$owl_lang->peer_satus_pending   		= "&#8212; E N   C O U R S &#8212;";
$owl_lang->peer_satus_rejected  		= "&#8212; R E J E T E &#8212;";
$owl_lang->peer_subj_approved   		= "Votre document a &eacute;t&eacute; approuv&eacute;";
$owl_lang->peer_subj_rejected   		= "Votre document a &eacute;t&eacute; rejet&eacute;";
$owl_lang->peer_subj_reminder   		= "RAPPEL : Vous avez un fichier &agrave; approuver";
$owl_lang->peer_subj_review     		= "Vous avez un fichier &agrave; approuver";

//********************************************************
// DOC PEER Review  END
//********************************************************


//********************************************************
// setacl.php BEGIN
//********************************************************

$owl_lang->acl_adding_file             = "Ajouter Fichier ACL:";
$owl_lang->acl_adding_folder           = "Ajouter Repertoire ACL:";
$owl_lang->acl_available_groups        = "Groupe(s) disponible(s)";
$owl_lang->acl_available_users         = "Utilisateur(s) disponible(s)";
$owl_lang->acl_edit_file               = "Editer fichier ACL:";
$owl_lang->acl_edit_folder             = "Editer r&eacute;pertoire ACL:";
$owl_lang->acl_file_checkin            = "D&eacute;verrouiller/Verrouiller";
$owl_lang->acl_file_comment            = "Commentaire";
$owl_lang->acl_file_copy               = "Copier";
$owl_lang->acl_file_delete             = "Supprimer";
$owl_lang->acl_file_email              = "E-Mail";
$owl_lang->acl_file_modify             = "Propri&eacute;t&eacute;s";
$owl_lang->acl_file_monitor            = "Moniteur";
$owl_lang->acl_file_move               = "D&eacute;placer";
$owl_lang->acl_file_read               = "Lire";
$owl_lang->acl_file_search             = "Recherche approch&eacute;e";
$owl_lang->acl_file_set_acl            = "Definir ACL";
$owl_lang->acl_file_update             = "Mettre &agrave; jour";
$owl_lang->acl_file_view_log           = "Voir Log";
$owl_lang->acl_folder_copy             = "Copier";
$owl_lang->acl_folder_delete           = "Supprimer";
$owl_lang->acl_folder_modify           = "Modifier Propri&eacute;t&eacute;s";
$owl_lang->acl_folder_monitor          = "Moniteur";
$owl_lang->acl_folder_move             = "Deplacer";
$owl_lang->acl_folder_read             = "Lire (T&eacute;l&eacute;charger/Explorer)";
$owl_lang->acl_folder_set_acl          = "Definir ACL";
$owl_lang->acl_folder_write            = "Ecrire (Cr&eacute;er)";
$owl_lang->acl_heading_file            = "Groupe(s)/Utilisateur(s)";
$owl_lang->acl_heading_files           = "FICHIERS &#8211; SELECTIONNER UTILISATEUR(S) ET GROUPE(S) SOUHAITE(S)";
$owl_lang->acl_heading_folder          = "Groupe(s)/Utilisateur(s)";
$owl_lang->acl_heading_folders         = "REPERTOIRES &#8211; SELECTIONNER UTILISATEUR(S) ET GROUPE(S) SOUHAITE(S)";
$owl_lang->acl_propagate_file          = "D&eacute;finir cette r&egrave;gle de s&eacute;cutit&eacute; pour \"TOUS\" les fichiers:";
$owl_lang->acl_propagate_folders       = "Propager aux sous r&eacute;pertoire(s):";
$owl_lang->acl_selected_groups         = "Groupe(s) s&eacute;l&eacute;ctionn&eacute;(s)";
$owl_lang->acl_selected_users          = "Utilisateur(s) s&eacute;l&eacute;ctionn&eacute;(s)";
$owl_lang->acl_set_acl                 = "D&eacute;finir les r&egrave;gles de s&eacute;curit&eacute;";
$owl_lang->acl_set_acl_now             = "D&eacute;finir les r&egrave;gles de s&eacute;curit&eacute; maintenant:";
$owl_lang->acl_set_file_permissions    = "D&eacute;finir les permissions des Fichiers";
$owl_lang->acl_set_folder_permissions  = "D&eacute;finir les permissions des r&eacute;pertoires";
$owl_lang->alt_set_folder_acl          = "D&eacute;finir la politique de s&eacute;curit&eacute; des dossiers";
$owl_lang->acl_set_selected            = "Valider la s&eacute;l&eacute;ction";
$owl_lang->alt_acl_save_file           = "Sauvegarder les r&egrave;gles de s&eacute;curit&eacute; du document";
$owl_lang->alt_acl_save_folder         = "Sauvegarder les r&egrave;gles de s&eacute;curit&eacute; du r&eacute;pertoire";
$owl_lang->btn_acl_save                = "Sauvegarder";

//********************************************************
// setacl.php END
//********************************************************


//********************************************************
// admin/reports BEGIN
//********************************************************

$owl_lang->report_available_reports         = "Rapports disponibles:";
$owl_lang->report_file_folder               = "Fichier/R&eacute;pertoire";
$owl_lang->report_filter                    = "Filtre:";
$owl_lang->report_filter_since              = "Depuis:";
$owl_lang->report_full_access               = "ACCES TOTAL";
$owl_lang->report_select_report             = "&#8211; S&eacute;l&eacute;ctionner un Rapport &#8211;";
$owl_lang->report_users_disabled_title      = "D&eacute;sactiver l'utilisateur(s)";
$owl_lang->report_users_entitlement         = "Liste d'acc&egrave;s de l'utilisateur";
$owl_lang->report_users_inactive_title      = "Utilisateur(s) inactif depuis";

//********************************************************
// admin/reports END
//********************************************************


//********************************************************
// THUMBNAILS BEGIN
//********************************************************

$owl_lang->alt_no_thumb                    = "Pas de vignette disponible";
$owl_lang->alt_thumb_small                 = "Petite vignette";
$owl_lang->thumb_enable_thumbnails         = "Activer l'affichage/cr&eacute;ation des vignettes:";
$owl_lang->thumb_gen_thumb_tool_path       = "Chemin vers le convertisseur ImageMagick:";
$owl_lang->thumb_gen_thumb_vid_tool_parms  = "Param&egrave;tres Mplayer:";
$owl_lang->thumb_gen_thumb_vid_tool_path   = "Chemin vers Mplayer:";
$owl_lang->thumb_image_info                = "Infos de l'image";
$owl_lang->thumb_image_size                = "Taille de l'image:";
$owl_lang->thumb_large_width               = "Largeur de la vignette (GRANDE) en Pixels:";
$owl_lang->thumb_med_width                 = "Largeur de la vignette (MOYENNE) en Pixels:";
$owl_lang->thumb_number_colums             = "Nombre de colonnes pour l'affichage des vignettes:";
$owl_lang->thumb_re_generate               = "G&eacute;n&eacute;rer la vignette";
$owl_lang->thumb_small_width               = "Largeur de la vignette (PETITE) en Pixels:";
$owl_lang->thumb_title                     = "OPTIONS D'AFFICHAGE/CREATION DE VIGNETTES";
$owl_lang->thumb_title_thumb_view          = "DETAILS DES VIGNETTES";

//********************************************************
// THUMBNAILS END
//********************************************************
?>
