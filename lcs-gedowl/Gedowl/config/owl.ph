<?php
/*
 *  File: owl.php
 * Author: Steve Bourgeois <owl@bozzit.com>
 * Project Founder: Chris Vincent <cvincent@project802.net>
 *
 * Copyright (c) 1999-2006 The Owl Project Team
*/

// Some urls
// owl_root_url below should not contain http://servername/intranet, but
// just the URL from the root of the web server.

//modif1 misterphi
$default->owl_root_url		= "/Plugins/Gedowl";
//eom1
$default->owl_graphics_url	= $default->owl_root_url . "/graphics";

// Ensure that the system_ButtonStyle you choose is a style that exists in 
// all locale
$default->system_ButtonStyle	= "rsdx_blue1";
//$default->system_ButtonStyle	= "Blue";

// Directory where owl is located
// this is the full physical path to where
// Owl was installed

$default->owl_fs_root		= dirname(dirname(__FILE__));
$default->owl_LangDir		= $default->owl_fs_root . "/locale";

// Directory where The Documents Directory is On Disc
// This path should not include the Documents directory name
// only the path leading to it.

// NOTE: There should be a directory writeable by the web server
//       called Documents in $default->owl_FileDir.  If you want to make 
//	 that a different name you need to rename this directory to what 
//	 ever you want then change the name of the Documents folder in 
//	 the folders table. Using mysql or pgsql update statment.
//	 Check the README FILE


// ***************************************************
// Use File System BEGIN
// ***************************************************

// Use the file system of the database to store the
// files uploaded.
// $default->owl_use_fs            = true;		// This stores uploaded files to the Hard Drive
// $default->owl_use_fs            = false;		// This stores uploaded files to a table in the database
// Note that temporary files are created to gzip files
// so set to something that is valid, and is writable by the web server
// For Example: $default->owl_FileDir           =  "/tmp/OWLDB";
// 
// NOTE: This feature is only functional with Mysql
// I don't plan on fixing this unless there is a big demand
// For this feature and Postgres.
// 

$default->owl_use_fs            = true;

//  set to 1 to compress the data in the database 
//  when using $default->owl_use_fs = false this compresses the data 
//  before storing to the database

//$default->owl_compressed_database = 1;

// ***************************************************
// Use File System END
// ***************************************************

//****************************************************
// Pick your language system default language
// now each user can pick his language
// if they are allowed by the admin to change their
// preferences.
//****************************************************
// b5
// Brazilian
// Bulgarian
// Chinese
// CVS
// Czech
// Danish
// Deutsch
// Dutch
// English
// Francais
// Hungarian
// Italian
// Norwegian
// Polish
// Portuguese
// Russian
// Spanish
// 
//modif2 misterphi
$default->owl_lang		= "French";
//eom2
$default->owl_notify_link       = "http://URLLCS". $default->owl_root_url . "/";


// Table Prefix
$default->owl_table_prefix = "";
//$default->owl_table_prefix = "owl_";


// Table with user info
$default->owl_users_table		= $default->owl_table_prefix . "users";

// Table with group memebership for users 
$default->owl_users_grpmem_table	= $default->owl_table_prefix . "membergroup";
$default->owl_sessions_table 		= $default->owl_table_prefix . "active_sessions";

// Table with file info
$default->owl_files_table		= $default->owl_table_prefix . "files";

// Table with folders info
$default->owl_folders_table		= $default->owl_table_prefix . "folders";

// Table with group info
$default->owl_groups_table		= $default->owl_table_prefix . "groups";

// Table with mime info
$default->owl_mime_table		= $default->owl_table_prefix . "mimes";

// Table with html attributes
$default->owl_html_table		= $default->owl_table_prefix . "html";

// Table with html attributes
$default->owl_prefs_table		= $default->owl_table_prefix . "prefs";

// Table with file data info
$default->owl_files_data_table  	= $default->owl_table_prefix . "filedata";

// Table with files that are monitored
$default->owl_monitored_file_table  	= $default->owl_table_prefix . "monitored_file";

// Table with folders that are monitored
$default->owl_monitored_folder_table  	= $default->owl_table_prefix . "monitored_folder";

// Table with all logging
$default->owl_log_table  		= $default->owl_table_prefix . "owl_log";
 
// Table with all user comments
$default->owl_comment_table  		= $default->owl_table_prefix . "comments";
 
// Table with all news
$default->owl_news_table  		= $default->owl_table_prefix . "news";

// Search Tables
$default->owl_wordidx  			= $default->owl_table_prefix . "wordidx";
$default->owl_searchidx 		= $default->owl_table_prefix . "searchidx";

// Custom Document Fields Tables
$default->owl_docfields_table		= $default->owl_table_prefix . "docfields";
$default->owl_docfieldslabel_table	= $default->owl_table_prefix . "docfieldslabel";
$default->owl_doctype_table          	= $default->owl_table_prefix . "doctype";
$default->owl_docfieldvalues_table	= $default->owl_table_prefix . "docfieldvalues";

// Custom Document Fields Tables
$default->owl_keyword_table		= $default->owl_table_prefix . "metakeywords";

// Custom Document Fields Tables
$default->owl_peerreview_table		= $default->owl_table_prefix . "peerreview";

// Custom Document Fields Tables
$default->owl_trackpasswd_table		= $default->owl_table_prefix . "trackoldpasswd";

// Custom Document Fields Tables
$default->owl_advanced_acl_table	= $default->owl_table_prefix . "advanced_acl";

// Custom Document Fields Tables
$default->owl_user_favorites		= $default->owl_table_prefix . "favorites";

//**********************************************
// Global Date Format BEGIN
// -------------------------------------
//
// If you want one date format for all the language files
// set the variable bellow to the date patern of your
// Choice.   If you require a different pattern for 
// different lanugages, edit each language file
// and set your pattern in the Date Format Section of 
// each file
//
//
// Examples of Valid patterns:
//$default->generic_date_format 	= "Y-m-d"; 			// 2003-03-07
//$default->generic_date_format 	= "Y-m-d H:i:s";		// 2003-03-13 16:46:24
//$default->generic_date_format 	= "r";				// Thu, 13 Mar 2003 16:46:24 -0500
//$default->generic_date_format 	= "d-M-Y h:i:s a";		// 13-Mar-2003 04:46:24 pm
//$default->generic_date_format 	= "Y-m-d\\<\B\R\\>H:i:s";	// 2003-03-13<br />16:46:24
//$default->generic_date_format         = "Y-M-d\\<\B\R\\>H:i ";  	// 2003-Mar-09<br>12:29 
//$default->generic_date_format         = "d-m-y\\<\B\R\\>H:i ";  	// 27-10-02<br>10:58
//$default->generic_date_format         = "D-M-Y\\<\B\R\\>H:i ";  	// Sun-Oct-2002<br>10:58 
//
// For more options check the php documentation:
// http://www.php.net/manual/en/function.date.php
//**********************************************

//modif3
$default->generic_date_format 	= "d-m-Y H:i";
//eom3


//**********************************************
// Global Date Format END
//**********************************************

//**********************************************
// LookATHD Feature Filter Section BEGIN
// -------------------------------------
//
// Uncomment the 2 lines following this section
// to exclude files that have db or txt for 
// an extension
// 
// You can add as many extention as you need.
// and files with the extensions listed below
// are not added to the LookAtHD feature
//
//**********************************************


//$default->lookHD_ommit_ext[] = "DS_Store";
//$default->lookHD_ommit_ext[] = "txt";


// to exclude Folders 
// Carefull as this applies to that foldername in any directory

$default->lookHD_ommit_directory[] = "CVS";

//**********************************************
// LookATHD Feature Filter Section END
//**********************************************

//**********************************************
// OMMIT FILES Section BEGIN
//**********************************************

//$default->upload_ommit_ext[] = "pdf";
//$default->upload_ommit_ext[] = "exe";

//**********************************************
// OMMIT FILES Section END
//**********************************************

//**********************************************
// LookATHD Feature Filter Section END
//**********************************************

// Change this to reflect the database you are using
// Mysql 
 require_once("$default->owl_fs_root/phplib/db_mysql.inc");
// Oracle  
//require_once("$default->owl_fs_root/phplib/db_oci8.inc");
// PostgreSQL  
//require_once("$default->owl_fs_root/phplib/db_pgsql.inc");


//**********************************************
// Database info BEGIN
//**********************************************

$default->owl_default_db = 0;    // This indicates what database should be selected by Default  when multiple repositories are defined

// First Database Information

$default->owl_db_id[0]           = "0";
//modif4 misterphi
$default->owl_db_user[0]           = "gedowl_user";
$default->owl_db_pass[0]           = "#PASS#";
$default->owl_db_host[0]           = "localhost";
$default->owl_db_name[0]           = "gedowl_plug";
//eom4
$default->owl_db_display_name[0]   = "Intranet";
$default->owl_db_ldapserver[0]     = "your.ldap.server.address";
$default->owl_db_ldapdomain[0]     = "";
//$default->owl_db_FileDir[0]        =  "/var/www/html/owl-0.90";
$default->owl_db_FileDir[0]        =  $default->owl_fs_root;
$default->peer_auto_publish[0] 	   = "false";

// Second Database

//$default->owl_db_id[1]           = "1";
//$default->owl_db_user[1]           = "root";
//$default->owl_db_pass[1]           = "";
//$default->owl_db_host[1]           = "localhost";
//$default->owl_db_name[1]           = "test";
//$default->owl_db_display_name[1]   = "Testing";
//$default->owl_db_ldapserver[1]     = "your.ldap.server.address";
//$default->owl_db_ldapdomain[1]     = "";
//$default->owl_db_FileDir[1]           =  $default->owl_fs_root;
//$default->peer_auto_publish[1] = "false";


// Third Database and so on and so on....

//**********************************************
// Database info END
//**********************************************

// This is to display the version information in the footer

$default->version = "Owl 0.95 ";
$default->site_title = "Owl Intranet ";
$default->phpversion = "4.3.10";

$default->debug = false;

// BEGIN Drop Down Menu Order

$default->FolderMenuOrder = array(
'folder_view', 
'folder_delete', 
'folder_edit', 
'folder_copy', 
'folder_move', 
'folder_monitor', 
'folder_download', 
'folder_acl',
'folder_thumb'
);
$default->FileMenuOrder = array(
'file_log',
'file_delete',
'file_edit',
'file_acl',
'file_link',
'file_copy',
'file_move',
'file_update',
'file_download',
'file_comment',
'file_lock',
'file_email',
'file_monitor',
'file_inline_edit',
'file_find',
'file_thumb',
'file_view'
);

// END Drop Down Menu Order

// BEGIN WORDIDX exlusion List
$default->words_to_exclude_from_wordidx[] = "the";
$default->words_to_exclude_from_wordidx[] = "a";
$default->words_to_exclude_from_wordidx[] = "is";
$default->words_to_exclude_from_wordidx[] = "on";
$default->words_to_exclude_from_wordidx[] = "or";
$default->words_to_exclude_from_wordidx[] = "he";
$default->words_to_exclude_from_wordidx[] = "she";
$default->words_to_exclude_from_wordidx[] = "his";
$default->words_to_exclude_from_wordidx[] = "her";
// END WORDIDX

// This is for adding a view icon to file types
// that are not currently supported by Owl
// DO NOT ADD FILE Types that already have
// a view icon (the magnifying glass) Or you will endup with 2 of them


$default->view_other_file_type_inline[] = "Your-Extension-without-the-dot-here";


$default->edit_text_files_inline[] = "txt";
$default->edit_text_files_inline[] = "php";
$default->edit_text_files_inline[] = "tpl";
$default->edit_text_files_inline[] = "sql";
$default->edit_text_files_inline[] = "html";
$default->edit_text_files_inline[] = "sh";



$default->list_of_chars_to_remove_from_wordidx = "{}\"?$()/\\&*.;:,";

$default->list_of_valid_chars_in_file_names = "-A-Za-z0-9._[:space:]ÀàÁáÂâÃãÄäÅåÆæÇçÈèÉéÊêËëÌìÍíÎîÐðÏïÑñÒòÓóÔôÕõÖö×÷ØøÙùÚúÛûÜüÝýßÞþÿ()@#$\{}+,&;";

// if you want index your documents on add Archive change this to 1
// This was removed as with large ZIP files with allot of indexable files
// it would cause the Script to time out and/or run out of resources.
// Run admin/tools/bigindex.pl instead.
$default->index_files_on_archive_add = 0;

$default->default_sort_column = "name"; // Values are: name -- major_minor_revision -- filename -- f_size -- creatorid -- smodified -- sortchecked_out
$default->default_sort_order = "ASC";  // Values are ASC OR DESC

//modif5 misterphi
$default->charset = "iso-8859-1";
//eom5

//**********************************************************************
// SAFE MODE ISSUES BEGIN
//**********************************************************************
//
// this was added to workaround issues with SAFE MODE TURNED ON.
// check: http://bugs.php.net/bug.php?id=24604

// Sets the Defautl MASK when OWL Creates a directory;
// HERE is the important bit:
/*
[15 Oct 2004 10:28am CEST] paulo dot matos at fct dot unl dot pt

A workaround/solution to this problem on *nix

Assuming that httpd server runs as apache/apache (uid/gid), and php
script is user/group. 

1) php.ini
safe_mode = On
safe_mode_gid = On

2) Create initial data directory, on install phase as:

mkdir /path/to/datadir
chown user.group /path/to/datadir
chmod 2777 /path/to/datadir

3) Create all subdirectories (within php), like:
mkdir(/path/to/datadir/subdir, 02777);

This way all subdirectry will inherit group from initial parent dir and
SAFE_MODE won't complain, since all subdirs
and files will be apache.group.

IMPORTANT NOTE: After any subdirectory creation you shouldn't change
directory permissions, otherwise it will loose
the GID bit and all files/subdirectories created afterwards won't have
group inherited!

*/

$default->directory_mask = 0777;

//$default->directory_mask = 02777;

//
//**********************************************************************
// SAFE MODE ISSUES END
//**********************************************************************

// What authitencation should Owl Use.
// 0 = Old Standard Owl Authentication
// 1 = .htaccess authentication (username must also exists as the Owl users Table)
// 2 = pop3 authentication (username must also exists as the Owl users Table)
// 3 = LDAP authentication (username must also exists as the Owl users Table)

$default->auth = 0;

// Auth 2  POP3
$default->auth_port = "110";
$default->auth_host = "192.168.11.41";

// Auth 3 LDAP

//$default->ldapserver = "lclntn7e.ngco.com";

$default->ldapserverroot = "ou=People,dc=??????,dc=???";
$default->ldapuserattr = "uid"; // whatever holds logon name in your ldap schema
$default->ldapprotocolversion = "3"; // or 2 to match your ldap


// If you are behind a load-balanced proxy, thus the IP
// changes, you get an "session in use" error, because
// active sessions are checked against the tripple (sessid,uid,ip). 
//
// DEFAULT
// true ---> track it as yet, i.e. (sessid,uid,ip)
//
// false --> track it alternate, i.e. (sessid,uid)
$default->active_session_ip = false;

//************************************************************
// NEW OPTIONS
//************************************************************

$default->thumbnails_url = $default->owl_root_url . "/ThumbNails"; // this directory has to be in the webspace
$default->thumbnails_location = $default->owl_fs_root  . "/ThumbNails"; // this directory has to be in the webspace

// Video image types that will be processed with mplayer

$default->thumbnail_video_type[] = "avi";
$default->thumbnail_video_type[] = "mpg";
$default->thumbnail_video_type[] = "mpeg";
$default->thumbnail_video_type[] = "mov";

// Image types that will be processed with convert 

$default->thumbnail_image_type[] = "gif";
$default->thumbnail_image_type[] = "jpg";
$default->thumbnail_image_type[] = "jpeg";
$default->thumbnail_image_type[] = "png";
$default->thumbnail_image_type[] = "tiff";
$default->thumbnail_image_type[] = "tif";
$default->thumbnail_image_type[] = "eps";
$default->thumbnail_image_type[] = "ai";
$default->thumbnail_image_type[] = "pdf";
$default->thumbnail_image_type[] = "doc";



// BEGIN NEW ACL Based Security Model

$default->advanced_security = 1;

// User 0 is equal to EVERYBODY
// If the group and user are left blank "" then the creators primary group is used
// ------------------------------
// begin default folder security.
// the first [x]  indicates which database
// this default security will apply to.
// ------------------------------

//$default->folder_security[0][] = array ( "group_id" => "" , "user_id" => "0", 
//"owlread" => "1", 
//"owlwrite" => "0", 
//"owldelete" => "0", 
//"owlcopy" => "0", 
//"owlmove" => "0", 
//"owlproperties" => "0", 
//"owlsetacl" => "0", 
//"owlmonitor" => "1" );

/* 
$default->folder_security[0][] = array ( "group_id" => "" , "user_id" => "", 
"owlread" => "1", 
"owlwrite" => "1", 
"owldelete" => "0", 
"owlcopy" => "0", 
"owlmove" => "0", 
"owlproperties" => "1", 
"owlsetacl" => "0", 
"owlmonitor" => "1" ); */

// ------------------------------
// end default folder security.
// ------------------------------

// ------------------------------
// begin default file security.
// the first [x]  indicates which database 
// this default security will apply to.
// ------------------------------

/* $default->file_security[0][] = array ( "group_id" => "" , "user_id" => "", 
"owlread" => "1", 
"owlwrite" => "1", 
"owlviewlog" => "0",
"owldelete" => "0", 
"owlcopy" => "0", 
"owlmove" => "0", 
"owlproperties" => "1", 
"owlupdate" => "0",
"owlcomment" => "0",
"owlcheckin" => "0",
"owlemail" => "0",
"owlrelsearch" => "0",
"owlsetacl" => "0", 
"owlmonitor" => "1" ); */

// ------------------------------
// end default file security.
// ------------------------------

// END NEW ACL Based Security Model

$default->notify_of_admin_login = 0;
$default->notify_of_admin_login_email = "security_manager@yourdomain.com"; 

// When a file is downloaded this will append the Major, Minor version numbers 
// to the downloaded file name
$default->append_doc_version_to_downloaded_files = 0;

$default->machine_time_zone =2;


$default->make_file_indexing_user_selectable = 0;
$default->count_file_folder_special_access = false;


// *************************************************************
// List of Special DROP ZONE Folders
// The idea here is to allow anybody to upload a document to this
// folder, but not retain ownership of the file.
//
// like an FTP Upload Folder
//
// Security Reference
// '0' Everyone can read/download
// '1' Everyone can read/write/download
// '2' The selected group can read/download
// '3' The selected group can read/write/download
// '4' Only you can read/download/write
// '5' The selected group can read/write/download, NO DELETE
// '6' Everyone can read/write/download, NO DELETE
// '7' selected='selected'>The selected group can read/write/download &amp; everyone else can read
// '8' The selected group can read/write/download (NO DELETE) &amp; everyone else can read
//
//  $default->special_folder_defaults[<FOLDER ID HERE>]
// ************************************************************
                                                                                                                                                   
//$default->special_folder_defaults[1] = array ( "creatorid" => "1" , "groupid" => "0",
//"description" => "Default Description",
//"metadata" => "Default metadata",
//"security" => "4");

$default->version_control_backup_dir_name = "backup";

$default->purge_historical_documents_days = 90;

$default->default_doctype = 1;
$default->default_url_doctype = 1;

// On file and folder creation the ACL's of the parent are applied 
// to the new file or folder
$default->inherit_acl_from_parent_folder = true;


// Should be 1 or 0, for whether monitoring a folder 
// also monitors subfolders under it 
$default->owl_monitor_subfolders = 1; 


// Replace all textareas in Owl with Richtext editor
$default->use_wysiwyg_for_textarea = 0;

$default->owl_maintenance_mode = 0;

//$default->pdf_watermark_path = "/usr/bin/pdftk-disabled";
// if you have pdttk version 1.41 or higer installed set this one to 1 else set it to 0

$default->pdf_pdftk_tool_greater_than_1_40 = 1;

$default->pdf_watermark_path = "/usr/bin/pdftk";
$default->pdf_thumb_path = "/usr/bin/pdftoppm";
$default->pdf_custom_watermark_filepath = ""; // LEAVE Empty unless you created your own background pdf file

// this enables a popup window on setacl
// that shows the users that are in the group.

//modif6 misterphi
$default->show_users_in_group = true;
//eom6

// Email notification generates a valid session for users so
// they don't have to login, to turn this feature off and
// force the user to sign on before they get access to the file
// set this value to false

$default->generate_notify_link_session = false;

// when Owl Use FS = False the default was to delete
// files that where imported and stored to the database
// to NOT delete your physical files on import set this
// to FALSE
$default->use_fs_false_remove_files_on_load = true;

// MegaUpload Progress Bar
$default->use_progress_bar = 0;
$default->progress_bar_tmp_dir = "/tmp";

?>
