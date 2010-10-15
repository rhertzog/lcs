ALTER TABLE users add column change_paswd_at_login int(4);
ALTER TABLE users add column login_failed int(4);
ALTER TABLE users add column passwd_last_changed datetime;
ALTER TABLE users add column expire_account varchar(80);
ALTER TABLE users add column user_auth char(2);
ALTER TABLE users add column logintonewrec int(4);
ALTER TABLE users add column groupadmin int(4);
ALTER TABLE users add column user_offset char(4);
alter table users add column useradmin int(4);
ALTER TABLE users add column viewlogs int(4);
ALTER TABLE users add column viewreports int(4);


CREATE TABLE trackoldpasswd (
  id  int(4) NOT NULL auto_increment,
  userid int(4) NOT NULL default '0',
  password varchar(50) NOT NULL default '',
  PRIMARY KEY  (id)
) TYPE=MyISAM;

CREATE TABLE favorites (
  userid int(4) NOT NULL default '0',
  folder_id int(4) NOT NULL default '1'
) TYPE=MyISAM;

CREATE TABLE advanced_acl (
  group_id int(4) default NULL,
  user_id  int(4) default NULL,
  file_id int(4) default NULL,
  folder_id int(4) default NULL,
  owlread int(4) default '0',
  owlwrite int(4) default '0',
  owlviewlog int(4) default '0',
  owldelete int(4) default '0',
  owlcopy int(4) default '0',
  owlmove int(4) default '0',
  owlproperties int(4) default '0',
  owlupdate int(4) default '0',
  owlcomment int(4) default '0',
  owlcheckin int(4) default '0',
  owlemail int(4) default '0',
  owlrelsearch int(4) default '0',
  owlsetacl int(4) default '0',
  owlmonitor int(4) default '0',
  KEY groupid_index (group_id),
  KEY userid_index (user_id),
  KEY fileid_index (file_id),
  KEY folderid_index (folder_id)
) TYPE=MyISAM;

INSERT INTO advanced_acl VALUES (NULL,0,NULL,1,1,1,0,0,0,0,0,0,0,0,0,0,0,0);

ALTER TABLE prefs add column thumb_disp_status int(4);
ALTER TABLE prefs add column thumb_disp_doc_num  int(4);
ALTER TABLE prefs add column thumb_disp_image_info  int(4);
ALTER TABLE prefs add column thumb_disp_version  int(4);
ALTER TABLE prefs add column thumb_disp_size  int(4);
ALTER TABLE prefs add column thumb_disp_posted  int(4);
ALTER TABLE prefs add column thumb_disp_updated  int(4);
ALTER TABLE prefs add column thumb_disp_modified  int(4);
ALTER TABLE prefs add column thumb_disp_action  int(4);
ALTER TABLE prefs add column thumb_disp_held  int(4);
ALTER TABLE prefs add column self_create_homedir  int(4);
ALTER TABLE prefs add column self_captcha  int(4);
ALTER TABLE prefs add column info_panel_wide  int(4);
ALTER TABLE prefs add column track_favorites  int(4);
ALTER TABLE prefs add column expand_disp_updated int(4) default NULL;
ALTER TABLE prefs add column collapse_disp_updated int(4) default NULL;
ALTER TABLE prefs add column expand_search_disp_updated int(4) default NULL;
ALTER TABLE prefs add column collapse_search_disp_updated int(4) default NULL;
ALTER TABLE prefs add column expand_search_disp_posted int(4) default NULL;
ALTER TABLE prefs add column collapse_search_disp_posted int(4) default NULL;

UPDATE prefs SET thumb_disp_status='1', thumb_disp_doc_num ='1', thumb_disp_image_info ='1', thumb_disp_version ='1', thumb_disp_size ='1', thumb_disp_posted ='1', thumb_disp_modified ='1', thumb_disp_action ='1', thumb_disp_held ='1'; 

ALTER TABLE prefs add column thumbnails_tool_path varchar(255);
ALTER TABLE prefs add column thumbnails_video_tool_path varchar(255);
ALTER TABLE prefs add column thumbnails_video_tool_opt varchar(255);

UPDATE prefs SET thumbnails_tool_path='/usr/bin/convert', thumbnails_video_tool_path='/usr/local/bin/mplayer', thumbnails_video_tool_opt=' -vo png -ss 0:05 -frames 2 -nosound -really-quiet';

ALTER TABLE prefs add column thumbnails int(4);
ALTER TABLE prefs add column thumbnails_small_width int(4);
ALTER TABLE prefs add column thumbnails_med_width int(4);
ALTER TABLE prefs add column thumbnails_large_width int(4);
ALTER TABLE prefs add column thumbnail_view_columns int(4);
ALTER TABLE prefs add column rtftotext_path varchar(250);

UPDATE prefs SET thumbnails='1', thumbnails_small_width='25', thumbnails_med_width='50', thumbnails_large_width='100', thumbnail_view_columns='4', rtftotext_path='/usr/local/bin/unrtf';


ALTER TABLE prefs add column min_pass_length int(4);
ALTER TABLE prefs add column min_username_length int(4);
ALTER TABLE prefs add column min_pass_numeric int(4);
ALTER TABLE prefs add column min_pass_special int(4);
ALTER TABLE prefs add column enable_lock_account int(4);
ALTER TABLE prefs add column lock_account_bad_password int(4);
ALTER TABLE prefs add column track_user_passwords int(4);
ALTER TABLE prefs add column change_password_every int(4);
alter table prefs add column collapse_search_disp_doc_num int(4);
alter table prefs add column expand_search_disp_doc_num int(4);

UPDATE prefs SET min_pass_length='8', min_username_length='6', min_pass_numeric='1', min_pass_special='1', enable_lock_account='1', lock_account_bad_password='4', track_user_passwords='10', change_password_every='10', collapse_search_disp_doc_num ='0', expand_search_disp_doc_num='0', display_password_override = '0'; 

alter table prefs add column filedescreq int(4);
alter table prefs add column folderdescreq int(4);
alter table prefs add column show_user_info int(4);

UPDATE prefs SET filedescreq='0', folderdescreq='0', show_user_info='1';

alter table prefs add column colps_search_disp_doc_fields int(4);
alter table prefs add column expand_search_disp_doc_fields int(4);
alter table prefs add column collapse_disp_doc_fields int(4);
alter table prefs add column expand_disp_doc_fields int(4);

alter table docfields add column show_in_list int(4);

ALTER TABLE `prefs` CHANGE `collapse_search_disp_folder_path` `colps_search_disp_fld_path` int(4);

create INDEX acl_folderid ON advanced_acl (folder_id);
create INDEX acl_fileid ON advanced_acl (file_id);
create INDEX acl_userid ON advanced_acl (user_id);
create INDEX files_filetype ON files (url);
create index acl_groupid_index on advanced_acl (group_id);

alter table membergroup add column groupadmin int(4) DEFAULT NULL;
alter table membergroup change column groupid groupid int(4) DEFAULT NULL;
ALTER TABLE `prefs` ADD COLUMN `allow_custpopup` INT(4) AFTER `allow_popup`;
ALTER TABLE `docfields` ADD COLUMN `show_desc` INT(4) NOT NULL DEFAULT 0 AFTER `searchable`;


