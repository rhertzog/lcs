CREATE TABLE active_sessions (
        sessid character varying(32),
        usid character varying(25),
        lastused bigint,
        ip character varying(16),
        currentdb integer,
        primary key (sessid)
);

CREATE TABLE membergroup (
        userid integer not null,
        groupid integer default null,
        groupadmin integer default NULL

);

CREATE TABLE favorites (
  userid integer NOT NULL default '0',
  folder_id integer NOT NULL default '1'
);

CREATE TABLE folders (
        id serial,
        name character varying(255) not null,
        parent integer not null,
        description text,
        security character varying(5) not null,
        groupid integer not null,
        creatorid integer not null,
 	password character varying(50) NOT NULL default '',
 	smodified timestamp without time zone ,
        primary key (id)
);

create UNIQUE INDEX folderid_index ON folders (id);

CREATE TABLE files (
        id serial,
        name character varying(80) not null,
        filename character varying(255) not null,
        f_size bigint not null,
        creatorid integer not null,
        parent integer not null,
        created timestamp not null,
        description text not null,
        metadata text not null,
        security integer not null,
        groupid integer not null,
        smodified timestamp not null,
        checked_out integer not null default 0,
        major_revision integer not null default 0,
        minor_revision integer not null default 1,
        url integer not null default 0,
        password character varying(50) NOT NULL default '',
  	doctype integer default 0,
  	updatorid integer default 1,
  	linkedto integer default 0,
  	approved integer default 0,
        primary key (id)

);
create UNIQUE INDEX fileid_index ON files (id);


CREATE TABLE comments (
        id serial,
        fid integer not null,
        userid integer,
        comment_date timestamp not null,
        comments text not null,
        primary key (id)
);

CREATE TABLE news (
        id serial,
        gid integer not null,
        news_title character varying(255) not null,
        news_date timestamp not null,
        news text not null,
        news_end_date timestamp not null,
        primary key (id)
);

CREATE TABLE users (
        id serial,
        groupid character varying(10) not null,
        username character varying(20) not null,
        name character varying(50) not null,
        password character varying(50) not null,
	quota_max bigint not null,
	quota_current bigint not null,
        email character varying(255),
        notify int,
	attachfile int,
	disabled int, 
	noprefaccess int,
	language character varying(15),
        maxsessions integer not null,	
        lastlogin timestamp not null,
        curlogin timestamp not null,
	lastnews integer ,
        newsadmin int not null,
	comment_notify integer,
        buttonstyle character varying(255),
	homedir integer,
	firstdir integer,
        email_tool integer,
        change_paswd_at_login integer,
        login_failed integer,
        passwd_last_changed timestamp without time zone,
        expire_account character varying(80),
        user_auth character(2),
        logintonewrec integer,
        groupadmin integer,
        user_offset character varying(4) default NULL,
        useradmin integer,
        viewlogs integer,
        viewreports integer,
        primary key (id)
);

INSERT INTO users VALUES (1,'0','admin','Administrator','21232f297a57a5a743894a0e4a801fc3',0,230648,'bozz',0,0,0,0,'English',0,'2005-11-14 06:18:50','2005-11-15 18:56:06',8,0,1,'rsdx_blue1',1,1,1,0,0,'2005-04-10 22:28:40','','',0,0,'',0, 0, 0);
INSERT INTO users VALUES (2,'1','guest','Anonymous','823f67f159b22b4c9a6a96999d1dea57',0,0,'',0,0,0,1,'English',19,'2004-11-10 05:02:42','2005-10-23 08:22:16',0,0,0,'rsdx_blue1',1,1,0,0,0,'2005-10-23 08:22:16','','0',0,0,'',0, 0, 0);

SELECT pg_catalog.setval ('users_id_seq', 3, true);

CREATE TABLE html (
        id serial,
        table_expand_width    character varying(15),
        table_collapse_width  character varying(15),
	body_background      character varying(255),
	owl_logo      character varying(255),
        body_textcolor        character varying(15),
        body_link             character varying(15),
        body_vlink            character varying(15)
);

INSERT INTO html VALUES (1,'90%','50%','','owl_logo1.gif','#000000','#000000','#000000');



CREATE TABLE prefs (
    id serial NOT NULL,
    email_from character varying(80),
    email_fromname character varying(80),
    email_replyto character varying(80),
    email_server character varying(80),
    email_subject character varying(60),
    lookathd character varying(15),
    lookathddel integer,
    def_file_security integer,
    def_file_group_owner integer,
    def_file_owner integer,
    def_file_title character varying(40),
    def_file_meta character varying(40),
    def_fold_security integer,
    def_fold_group_owner integer,
    def_fold_owner integer,
    max_filesize integer,
    tmpdir character varying(255),
    timeout integer,
    expand integer,
    version_control integer,
    restrict_view integer,
    hide_backup integer,
    dbdump_path character varying(80),
    gzip_path character varying(80),
    tar_path character varying(80),
    unzip_path character varying(80),
    pod2html_path character varying(80),
    pdftotext_path character varying(80),
    wordtotext_path character varying(80),
    file_perm integer,
    folder_perm integer,
    logging integer,
    log_file integer,
    log_login integer,
    log_rec_per_page integer,
    rec_per_page integer,
    self_reg integer,
    self_reg_quota integer,
    self_reg_notify integer,
    self_reg_attachfile integer,
    self_reg_disabled integer,
    self_reg_noprefacces integer,
    self_reg_maxsessions integer,
    self_reg_group integer,
    anon_ro integer,
    anon_user integer,
    file_admin_group integer,
    forgot_pass integer,
    collect_trash integer,
    trash_can_location character varying(80),
    allow_popup integer,
    allow_custpopup integer,
    status_bar_location integer,
    remember_me integer,
    cookie_timeout integer,
    use_smtp integer,
    use_smtp_auth integer,
    smtp_passwd character varying(40),
    search_bar integer,
    bulk_buttons integer,
    action_buttons integer,
    folder_tools integer,
    pref_bar integer,
    smtp_auth_login character varying(50),
    expand_disp_status integer,
    expand_disp_doc_num integer,
    expand_disp_doc_type integer,
    expand_disp_title integer,
    expand_disp_version integer,
    expand_disp_file integer,
    expand_disp_size integer,
    expand_disp_posted integer,
    expand_disp_modified integer,
    expand_disp_action integer,
    expand_disp_held integer,
    collapse_disp_status integer,
    collapse_disp_doc_num integer,
    collapse_disp_doc_type integer,
    collapse_disp_title integer,
    collapse_disp_version integer,
    collapse_disp_file integer,
    collapse_disp_size integer,
    collapse_disp_posted integer,
    collapse_disp_modified integer,
    collapse_disp_action integer,
    collapse_disp_held integer,
    expand_search_disp_score integer,
    expand_search_disp_folder_path integer,
    expand_search_disp_doc_type integer,
    expand_search_disp_file integer,
    expand_search_disp_size integer,
    expand_search_disp_posted integer,
    expand_search_disp_modified integer,
    expand_search_disp_action integer,
    collapse_search_disp_score integer,
    colps_search_disp_fld_path integer,
    collapse_search_disp_doc_type integer,
    collapse_search_disp_file integer,
    collapse_search_disp_size integer,
    collapse_search_disp_posted integer,
    collapse_search_disp_modified integer,
    collapse_search_disp_action integer,
    hide_folder_doc_count integer,
    old_action_icons integer,
    search_result_folders integer,
    restore_file_prefix character varying(50),
    major_revision integer,
    minor_revision integer,
    doc_id_prefix character varying(10),
    doc_id_num_digits integer,
    view_doc_in_new_window integer,
    admin_login_to_browse_page integer,
    save_keywords_to_db integer,
    self_reg_homedir integer,
    self_reg_firstdir integer,
    virus_path character varying(80),
    peer_review integer,
    peer_opt integer,
    folder_size integer,
    download_folder_zip integer,
    display_password_override integer,
    thumb_disp_status integer,
    thumb_disp_doc_num integer,
    thumb_disp_image_info integer,
    thumb_disp_version integer,
    thumb_disp_size integer,
    thumb_disp_posted integer,
    thumb_disp_modified integer,
    thumb_disp_action integer,
    thumb_disp_held integer,
    thumbnails_tool_path character varying(255),
    thumbnails_video_tool_path character varying(255),
    thumbnails_video_tool_opt character varying(255),
    thumbnails integer,
    thumbnails_small_width integer,
    thumbnails_med_width integer,
    thumbnails_large_width integer,
    thumbnail_view_columns integer,
    rtftotext_path character varying(250),
    min_pass_length integer,
    min_username_length integer,
    min_pass_numeric integer,
    min_pass_special integer,
    enable_lock_account integer,
    lock_account_bad_password integer,
    track_user_passwords integer,
    change_password_every integer,
    folderdescreq integer,
    show_user_info integer,
    filedescreq integer,
    collapse_search_disp_doc_num integer,
    expand_search_disp_doc_num integer,
    colps_search_disp_doc_fields integer,
    expand_search_disp_doc_fields integer,
    collapse_disp_doc_fields integer,
    expand_disp_doc_fields integer,
    self_create_homedir integer,
    self_captcha integer,
    info_panel_wide integer,
    track_favorites integer,
    expand_disp_updated integer,
    collapse_disp_updated integer,
    expand_search_disp_updated integer,
    collapse_search_disp_updated integer,
    thumb_disp_updated integer
);


INSERT INTO prefs VALUES (1,'owl@yourdomain.com','OWL','owl@yourdomain.com','localhost','[OWL] : AUTOMATED MAIL','false',1,0,0,1,'<font color=red>No Info</font>','not in',0,0,1,151200000,'/tmp',9000,1,1,0,1,'/usr/bin/mysqldump','/usr/bin/gzip','/bin/tar','/usr/bin/unzip','','/usr/bin/pdftotext','/usr/local/bin/antiword',0,0,0,1,1,25,0,0,0,0,0,0,0,0,1,0,2,2,0,0,'',1,1,1,0,30,0,0,'',1,1,1,1,1,'',1,0,1,1,1,1,1,1,1,1,1,1,0,1,1,1,1,1,0,0,0,1,1,1,1,1,1,1,1,1,1,1,1,1,1,0,0,0,1,0,1,0,1,0,'ABC-',3,0,1,1,1,1,'',1,1,1,1,0,1,1,1,1,1,1,1,1,1,'/usr/bin/convert','/usr/local/bin/mplayer',' -vo png -ss 0:05 -frames 2 -nosound -really-quiet',1,25,50,100,4,'/usr/local/bin/unrtf',8,2,0,0,0,4,10,90,0,0,0,1,0,0,1,0,0,0,0,1,1,0,0,0,0,0);


CREATE TABLE monitored_file (
        id serial,
        userid integer not null,
        fid integer not null,
        primary key (id)
);

CREATE TABLE monitored_folder (
        id serial,
        userid integer not null,
        fid integer not null,
        primary key (id)
);




CREATE TABLE groups (
        id serial,
        name character varying(30) not null,
        primary key (id)
);

CREATE TABLE filedata (
        id serial,
        compressed integer not null default 0,
        data bytea ,
        primary key (id)
);

CREATE TABLE owl_log (
        id serial,
        userid integer,
        filename character varying(255),
        parent integer,
        action character varying(40), 
        details text,
        ip character varying(16),
        agent character varying(255),
        logdate timestamp not null,
        type character varying(20),
        primary key (id)
);

create table wordidx (
        wordid integer,
        word character varying(128) not null
);

create UNIQUE INDEX word_index ON wordidx (word);

create table searchidx (
        wordid integer,
        owlfileid integer
);

create INDEX search_fileid ON searchidx (owlfileid);

CREATE TABLE mimes (
        filetype character varying(10) not null primary key,
        mimetype character varying(50) not null
);

INSERT INTO folders (name,parent,security,groupid,creatorid,description, smodified, password) VALUES ('Documents', 0, 51, 0, 0, '', '2004-10-17 08:11:50', '');

INSERT INTO groups (name) VALUES ('Administrators');
INSERT INTO groups (name) VALUES ('Anonymous');
INSERT INTO groups (name) VALUES ('File Admin');


UPDATE GROUPS SET id = 0 WHERE name = 'Administrators';
UPDATE GROUPS SET id = 1 WHERE name = 'Anonymous';
UPDATE GROUPS SET id = 2 WHERE name = 'File Admin';


INSERT INTO mimes VALUES ('ai', 'application/postscript');
INSERT INTO mimes VALUES ('aif', 'audio/x-aiff');
INSERT INTO mimes VALUES ('aifc', 'audio/x-aiff');
INSERT INTO mimes VALUES ('aiff', 'audio/x-aiff');
INSERT INTO mimes VALUES ('asc', 'text/plain');
INSERT INTO mimes VALUES ('au', 'audio/basic');
INSERT INTO mimes VALUES ('avi', 'video/x-msvideo');
INSERT INTO mimes VALUES ('bcpio', 'application/x-bcpio');
INSERT INTO mimes VALUES ('bin', 'application/octet-stream');
INSERT INTO mimes VALUES ('bmp', 'image/bmp');
INSERT INTO mimes VALUES ('cdf', 'application/x-netcdf');
INSERT INTO mimes VALUES ('class', 'application/octet-stream');
INSERT INTO mimes VALUES ('cpio', 'application/x-cpio');
INSERT INTO mimes VALUES ('cpt', 'application/mac-compactpro');
INSERT INTO mimes VALUES ('csh', 'application/x-csh');
INSERT INTO mimes VALUES ('css', 'text/css');
INSERT INTO mimes VALUES ('dcr', 'application/x-director');
INSERT INTO mimes VALUES ('dir', 'application/x-director');
INSERT INTO mimes VALUES ('dms', 'application/octet-stream');
INSERT INTO mimes VALUES ('doc', 'application/msword');
INSERT INTO mimes VALUES ('dvi', 'application/x-dvi');
INSERT INTO mimes VALUES ('dxr', 'application/x-director');
INSERT INTO mimes VALUES ('eps', 'application/postscript');
INSERT INTO mimes VALUES ('etx', 'text/x-setext');
INSERT INTO mimes VALUES ('exe', 'application/octet-stream');
INSERT INTO mimes VALUES ('ez', 'application/andrew-inset');
INSERT INTO mimes VALUES ('gif', 'image/gif');
INSERT INTO mimes VALUES ('gtar', 'application/x-gtar');
INSERT INTO mimes VALUES ('hdf', 'application/x-hdf');
INSERT INTO mimes VALUES ('hqx', 'application/mac-binhex40');
INSERT INTO mimes VALUES ('htm', 'text/html');
INSERT INTO mimes VALUES ('html', 'text/html');
INSERT INTO mimes VALUES ('ice', 'x-conference/x-cooltalk');
INSERT INTO mimes VALUES ('ief', 'image/ief');
INSERT INTO mimes VALUES ('iges', 'model/iges');
INSERT INTO mimes VALUES ('igs', 'model/iges');
INSERT INTO mimes VALUES ('jpe', 'image/jpeg');
INSERT INTO mimes VALUES ('jpeg', 'image/jpeg');
INSERT INTO mimes VALUES ('jpg', 'image/jpeg');
INSERT INTO mimes VALUES ('js', 'application/x-javascript');
INSERT INTO mimes VALUES ('kar', 'audio/midi');
INSERT INTO mimes VALUES ('latex', 'application/x-latex');
INSERT INTO mimes VALUES ('lha', 'application/octet-stream');
INSERT INTO mimes VALUES ('lzh', 'application/octet-stream');
INSERT INTO mimes VALUES ('man', 'application/x-troff-man');
INSERT INTO mimes VALUES ('me', 'application/x-troff-me');
INSERT INTO mimes VALUES ('mesh', 'model/mesh');
INSERT INTO mimes VALUES ('mid', 'audio/midi');
INSERT INTO mimes VALUES ('midi', 'audio/midi');
INSERT INTO mimes VALUES ('mif', 'application/vnd.mif');
INSERT INTO mimes VALUES ('mov', 'video/quicktime');
INSERT INTO mimes VALUES ('movie', 'video/x-sgi-movie');
INSERT INTO mimes VALUES ('mp2', 'audio/mpeg');
INSERT INTO mimes VALUES ('mp3', 'audio/mpeg');
INSERT INTO mimes VALUES ('mpe', 'video/mpeg');
INSERT INTO mimes VALUES ('mpeg', 'video/mpeg');
INSERT INTO mimes VALUES ('mpg', 'video/mpeg');
INSERT INTO mimes VALUES ('mpga', 'audio/mpeg');
INSERT INTO mimes VALUES ('ms', 'application/x-troff-ms');
INSERT INTO mimes VALUES ('msh', 'model/mesh');
INSERT INTO mimes VALUES ('nc', 'application/x-netcdf');
INSERT INTO mimes VALUES ('oda', 'application/oda');
INSERT INTO mimes VALUES ('pbm', 'image/x-portable-bitmap');
INSERT INTO mimes VALUES ('pdb', 'chemical/x-pdb');
INSERT INTO mimes VALUES ('pdf', 'application/pdf');
INSERT INTO mimes VALUES ('pgm', 'image/x-portable-graymap');
INSERT INTO mimes VALUES ('pgn', 'application/x-chess-pgn');
INSERT INTO mimes VALUES ('png', 'image/png');
INSERT INTO mimes VALUES ('pnm', 'image/x-portable-anymap');
INSERT INTO mimes VALUES ('ppm', 'image/x-portable-pixmap');
INSERT INTO mimes VALUES ('ppt', 'application/vnd.ms-powerpoint');
INSERT INTO mimes VALUES ('ps', 'application/postscript');
INSERT INTO mimes VALUES ('qt', 'video/quicktime');
INSERT INTO mimes VALUES ('ra', 'audio/x-realaudio');
INSERT INTO mimes VALUES ('ram', 'audio/x-pn-realaudio');
INSERT INTO mimes VALUES ('ras', 'image/x-cmu-raster');
INSERT INTO mimes VALUES ('rgb', 'image/x-rgb');
INSERT INTO mimes VALUES ('rm', 'audio/x-pn-realaudio');
INSERT INTO mimes VALUES ('roff', 'application/x-troff');
INSERT INTO mimes VALUES ('rpm', 'audio/x-pn-realaudio-plugin');
INSERT INTO mimes VALUES ('rtf', 'text/rtf');
INSERT INTO mimes VALUES ('rtx', 'text/richtext');
INSERT INTO mimes VALUES ('sgm', 'text/sgml');
INSERT INTO mimes VALUES ('sgml', 'text/sgml');
INSERT INTO mimes VALUES ('sh', 'application/x-sh');
INSERT INTO mimes VALUES ('shar', 'application/x-shar');
INSERT INTO mimes VALUES ('silo', 'model/mesh');
INSERT INTO mimes VALUES ('sit', 'application/x-stuffit');
INSERT INTO mimes VALUES ('skd', 'application/x-koan');
INSERT INTO mimes VALUES ('skm', 'application/x-koan');
INSERT INTO mimes VALUES ('skp', 'application/x-koan');
INSERT INTO mimes VALUES ('skt', 'application/x-koan');
INSERT INTO mimes VALUES ('smi', 'application/smil');
INSERT INTO mimes VALUES ('smil', 'application/smil');
INSERT INTO mimes VALUES ('snd', 'audio/basic');
INSERT INTO mimes VALUES ('spl', 'application/x-futuresplash');
INSERT INTO mimes VALUES ('src', 'application/x-wais-source');
INSERT INTO mimes VALUES ('sv4cpio', 'application/x-sv4cpio');
INSERT INTO mimes VALUES ('sv4crc', 'application/x-sv4crc');
INSERT INTO mimes VALUES ('swf', 'application/x-shockwave-flash');
INSERT INTO mimes VALUES ('t', 'application/x-troff');
INSERT INTO mimes VALUES ('tar', 'application/x-tar');
INSERT INTO mimes VALUES ('tcl', 'application/x-tcl');
INSERT INTO mimes VALUES ('tex', 'application/x-tex');
INSERT INTO mimes VALUES ('texi', 'application/x-texinfo');
INSERT INTO mimes VALUES ('texinfo', 'application/x-texinfo');
INSERT INTO mimes VALUES ('tif', 'image/tiff');
INSERT INTO mimes VALUES ('tiff', 'image/tiff');
INSERT INTO mimes VALUES ('tr', 'application/x-troff');
INSERT INTO mimes VALUES ('tsv', 'text/tab-separated-values');
INSERT INTO mimes VALUES ('txt', 'text/plain');
INSERT INTO mimes VALUES ('ustar', 'application/x-ustar');
INSERT INTO mimes VALUES ('vcd', 'application/x-cdlink');
INSERT INTO mimes VALUES ('vrml', 'model/vrml');
INSERT INTO mimes VALUES ('wav', 'audio/x-wav');
INSERT INTO mimes VALUES ('wrl', 'model/vrml');
INSERT INTO mimes VALUES ('xbm', 'image/x-xbitmap');
INSERT INTO mimes VALUES ('xls', 'application/vnd.ms-excel');
INSERT INTO mimes VALUES ('xml', 'text/xml');
INSERT INTO mimes VALUES ('xpm', 'image/x-xpixmap');
INSERT INTO mimes VALUES ('xwd', 'image/x-xwindowdump');
INSERT INTO mimes VALUES ('xyz', 'chemical/x-pdb');
INSERT INTO mimes VALUES ('zip', 'application/zip');
INSERT INTO mimes VALUES ('gz', 'application/x-gzip');
INSERT INTO mimes VALUES ('tgz', 'application/x-gzip');
INSERT INTO mimes VALUES ('sxw','application/vnd.sun.xml.writer');
INSERT INTO mimes VALUES ('stw','application/vnd.sun.xml.writer.template');
INSERT INTO mimes VALUES ('sxg','application/vnd.sun.xml.writer.global');
INSERT INTO mimes VALUES ('sxc','application/vnd.sun.xml.calc');
INSERT INTO mimes VALUES ('stc','application/vnd.sun.xml.calc.template');
INSERT INTO mimes VALUES ('sxi','application/vnd.sun.xml.impress');
INSERT INTO mimes VALUES ('sti','application/vnd.sun.xml.impress.template');
INSERT INTO mimes VALUES ('sxd','application/vnd.sun.xml.draw');
INSERT INTO mimes VALUES ('std','application/vnd.sun.xml.draw.template');
INSERT INTO mimes VALUES ('sxm','application/vnd.sun.xml.math');
INSERT INTO mimes VALUES ('wpd','application/wordperfect');



create INDEX parentid_index ON files (parent);
                                                                                                                                                                     
CREATE TABLE docfieldslabel (
  doc_field_id integer NOT NULL default '0',
  field_label character varying(80) NOT NULL default '',
  locale character varying(80) NOT NULL default ''
);

CREATE TABLE doctype (
        doc_type_id serial,
        doc_type_name character varying(255) not null,
        primary key (doc_type_id)
);
                                                                                                                                                                     
INSERT INTO doctype (doc_type_name) values ('Default');
                                                                                                                                                                     
CREATE TABLE docfields (
        id serial,
        doc_type_id integer not null ,
        field_name character varying(80) not null,
        field_position integer not null,
        field_type character varying(80) not null,
        field_values text not null,
        field_size integer not null,
        searchable integer not null,
        show_desc integer not null,
        required integer not null,
  	show_in_list integer default NULL,
        primary key (id)
);

CREATE TABLE docfieldvalues (
        id serial,
        file_id integer not null ,
        field_name character varying(80) not null,
        field_value text not null,
        primary key (id)
);
                                                                                                                                                                     
CREATE TABLE peerreview (
        reviewer_id integer,
        file_id integer,
        status integer
);

CREATE TABLE metakeywords (
        keyword_id serial,
        keyword_text char(255) not null,
        primary key (keyword_id)
);

CREATE TABLE trackoldpasswd (
    id serial NOT NULL,
    userid integer,
    "password" character varying(50) DEFAULT '' NOT NULL,
        primary key (id)
);

CREATE TABLE advanced_acl (
    group_id integer,
    user_id integer,
    file_id integer,
    folder_id integer,
    owlread integer DEFAULT '0',
    owlwrite integer DEFAULT '0',
    owlviewlog integer DEFAULT '0',
    owldelete integer DEFAULT '0',
    owlcopy integer DEFAULT '0',
    owlmove integer DEFAULT '0',
    owlproperties integer DEFAULT '0',
    owlupdate integer DEFAULT '0',
    owlcomment integer DEFAULT '0',
    owlcheckin integer DEFAULT '0',
    owlemail integer DEFAULT '0',
    owlrelsearch integer DEFAULT '0',
    owlsetacl integer DEFAULT '0',
    owlmonitor integer DEFAULT '0'
);

create index acl_groupid_index on advanced_acl (group_id);
create index acl_userid_index on advanced_acl (user_id);
create index acl_fileid_index on advanced_acl (file_id);
create index acl_folderid_index on advanced_acl (folder_id);

