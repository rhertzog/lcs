ALTER TABLE users add column change_paswd_at_login int4;
ALTER TABLE users add column login_failed int4;
ALTER TABLE users add column passwd_last_changed timestamp;
ALTER TABLE users add column expire_account varchar(80);
ALTER TABLE users add column user_auth char(2);
ALTER TABLE users add column logintonewrec int4;
ALTER TABLE users add column groupadmin int4;
ALTER TABLE users add column user_offset char(4);
alter table users add column useradmin int4;
ALTER TABLE users add column viewlogs int4;
ALTER TABLE users add column viewreports int4;

CREATE TABLE trackoldpasswd (
  id serial,
  userid int4,
  password varchar(50) NOT NULL default '',
  primary key  (id)
);

CREATE TABLE favorites (
  userid int4 NOT NULL default '0',
  folder_id int4 NOT NULL default '1'
); 

CREATE TABLE advanced_acl (
  group_id int4 default NULL,
  user_id  int4 default NULL,
  file_id int4 default NULL,
  folder_id int4 default NULL,
  owlread int4 default '0',
  owlwrite int4 default '0',
  owlviewlog int4 default '0',
  owldelete int4 default '0',
  owlcopy int4 default '0',
  owlmove int4 default '0',
  owlproperties int4 default '0',
  owlupdate int4 default '0',
  owlcomment int4 default '0',
  owlcheckin int4 default '0',
  owlemail int4 default '0',
  owlrelsearch int4 default '0',
  owlsetacl int4 default '0',
  owlmonitor int4 default '0'
);

INSERT INTO advanced_acl VALUES (NULL,0,NULL,1,1,1,0,0,0,0,0,0,0,0,0,0,0,0);

ALTER TABLE prefs add column thumb_disp_status int4;
ALTER TABLE prefs add column thumb_disp_doc_num  int4;
ALTER TABLE prefs add column thumb_disp_image_info  int4;
ALTER TABLE prefs add column thumb_disp_version  int4;
ALTER TABLE prefs add column thumb_disp_size  int4;
ALTER TABLE prefs add column thumb_disp_posted  int4;
ALTER TABLE prefs add column thumb_disp_updated  int4;
ALTER TABLE prefs add column thumb_disp_modified  int4;
ALTER TABLE prefs add column thumb_disp_action  int4;
ALTER TABLE prefs add column thumb_disp_held  int4;

UPDATE prefs SET thumb_disp_status='1', thumb_disp_doc_num ='1', thumb_disp_image_info ='1', thumb_disp_version ='1', thumb_disp_size ='1', thumb_disp_posted ='1', thumb_disp_modified ='1', thumb_disp_action ='1', thumb_disp_held ='1', thumb_disp_updated = '0';
                                                                                                                                                                                                                                                                 
ALTER TABLE prefs add column thumbnails_tool_path varchar(255);
ALTER TABLE prefs add column thumbnails_video_tool_path varchar(255);
ALTER TABLE prefs add column thumbnails_video_tool_opt varchar(255);
                                                                                                                                                                                                                                                                 
UPDATE prefs SET thumbnails_tool_path='/usr/bin/convert', thumbnails_video_tool_path='/usr/local/bin/mplayer', thumbnails_video_tool_opt=' -vo png -ss 0:05 -frames 2 -nosound -really-quiet';

ALTER TABLE prefs add column thumbnails int4;
ALTER TABLE prefs add column thumbnails_small_width int4;
ALTER TABLE prefs add column thumbnails_med_width int4;
ALTER TABLE prefs add column thumbnails_large_width int4;
ALTER TABLE prefs add column thumbnail_view_columns int4;
ALTER TABLE prefs add column rtftotext_path varchar(250);
                                                                                                                                                                                                  
UPDATE prefs SET thumbnails='1', thumbnails_small_width='25', thumbnails_med_width='50', thumbnails_large_width='100', thumbnail_view_columns='4', rtftotext_path='/usr/local/bin/unrtf';

ALTER TABLE prefs add column min_pass_length int4;
ALTER TABLE prefs add column min_username_length int4;
ALTER TABLE prefs add column min_pass_numeric int4;
ALTER TABLE prefs add column min_pass_special int4;
ALTER TABLE prefs add column enable_lock_account int4;
ALTER TABLE prefs add column lock_account_bad_password int4;
ALTER TABLE prefs add column track_user_passwords int4;
ALTER TABLE prefs add column change_password_every int4;
alter table prefs add column collapse_search_disp_doc_num int4;
alter table prefs add column expand_search_disp_doc_num int4;

UPDATE prefs SET min_pass_length='8', min_username_length='6', min_pass_numeric='1', min_pass_special='1', enable_lock_account='1', lock_account_bad_password='4', track_user_passwords='10', change_password_every='10', collapse_search_disp_doc_num ='0', expand_search_disp_doc_num='0';

alter table prefs add column filedescreq int4;
alter table prefs add column folderdescreq int4;
alter table prefs add column show_user_info int4;

alter table prefs add column colps_search_disp_doc_fields int4;
alter table prefs add column expand_search_disp_doc_fields int4;
alter table prefs add column collapse_disp_doc_fields int4;
alter table prefs add column expand_disp_doc_fields int4;

alter table docfields add column show_in_list int4;

ALTER TABLE prefs RENAME COLUMN collapse_search_disp_folder_path TO colps_search_disp_fld_path;

UPDATE prefs SET filedescreq='0', folderdescreq='0', show_user_info='1';

create index acl_groupid_index on advanced_acl (group_id);
create index acl_userid_index on advanced_acl (user_id);
create index acl_fileid_index on advanced_acl (file_id);
create index acl_folderid_index on advanced_acl (folder_id);

create table temp as select * from membergroup;
drop table membergroup;
CREATE TABLE membergroup (
   userid int4 not null,
   groupid int4,
   groupadmin int4);
INSERT INTO membergroup select * from temp;
drop table temp;
ALTER TABLE prefs ADD COLUMN allow_custpopup int4;
ALTER TABLE docfields ADD COLUMN show_desc int4 NOT NULL;
ALTER TABLE prefs add column self_create_homedir  int4;
ALTER TABLE prefs add column self_captcha  int4;
ALTER TABLE prefs add column info_panel_wide  int4;
ALTER TABLE prefs add column track_favorites  int4;
ALTER TABLE prefs add column expand_disp_updated int4 default NULL;
ALTER TABLE prefs add column collapse_disp_updated int4 default NULL;
ALTER TABLE prefs add column expand_search_disp_updated int4 default NULL;
ALTER TABLE prefs add column collapse_search_disp_updated int4 default NULL;
