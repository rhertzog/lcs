--- uncomment the following lines if you want to blow away an existing DB
--- drop table owl_admin.ACTIVE_SESSIONS;
--- drop table owl_admin.COMMENTS;
--- drop table owl_admin.FILEDATA;
--- drop table owl_admin.FILES;
--- drop table owl_admin.FOLDERS;
--- drop table owl_admin.GROUPS;
--- drop table owl_admin.HTML;
--- drop table owl_admin.MEMBERGROUP;
--- drop table owl_admin.METAKEYWORDS;
--- drop table owl_admin.MIMES;
--- drop table owl_admin.MONITORED_FILE;
--- drop table owl_admin.MONITORED_FOLDER;
--- drop table owl_admin.NEWS;
--- drop table owl_admin.OWL_LOG;
--- drop table owl_admin.PREFS;
--- drop table owl_admin.SEARCHIDX;
--- drop table owl_admin.USERS;
--- drop table owl_admin.WORDIDX;
--- drop table owl_admin.PEERREVIEW;
--- drop table owl_admin.DOCFIELDSLABEL;
--- drop table owl_admin.DOCTYPE;
--- drop table owl_admin.DOCFIELDS;
--- drop table owl_admin.DOCFIELDVALUES;

commit;

DROP SEQUENCE DOCTYPE_ID_SEQ;

commit;

CREATE SEQUENCE DOCTYPE_ID_SEQ
  START WITH 1
  NOMAXVALUE
  MINVALUE 1
  NOCYCLE
  NOCACHE
  NOORDER;


DROP SEQUENCE DOCFIELDS_ID_SEQ;

commit;

CREATE SEQUENCE DOCFIELDS_ID_SEQ
  START WITH 1
  NOMAXVALUE
  MINVALUE 1
  NOCYCLE
  NOCACHE
  NOORDER;


DROP SEQUENCE DOCFIELDVALUES_ID_SEQ;

commit;

CREATE SEQUENCE DOCFIELDVALUES_ID_SEQ
  START WITH 1
  NOMAXVALUE
  MINVALUE 1
  NOCYCLE
  NOCACHE
  NOORDER;



DROP SEQUENCE COMMENTS_ID_SEQ;

commit;

CREATE SEQUENCE COMMENTS_ID_SEQ
  START WITH 1
  NOMAXVALUE
  MINVALUE 1
  NOCYCLE
  NOCACHE
  NOORDER;


DROP SEQUENCE FILES_ID_SEQ;

commit;

CREATE SEQUENCE FILES_ID_SEQ
  START WITH 1
  NOMAXVALUE
  MINVALUE 1
  NOCYCLE
  NOCACHE
  NOORDER;


DROP SEQUENCE FOLDERS_ID_SEQ;

commit;

CREATE SEQUENCE FOLDERS_ID_SEQ
  START WITH 1
  NOMAXVALUE
  MINVALUE 1
  NOCYCLE
  NOCACHE
  NOORDER;


DROP SEQUENCE GROUPS_ID_SEQ;

commit;

CREATE SEQUENCE GROUPS_ID_SEQ
  START WITH 10
  NOMAXVALUE
  MINVALUE 1
  NOCYCLE
  NOCACHE
  NOORDER;


DROP SEQUENCE HTML_ID_SEQ;

commit;

CREATE SEQUENCE HTML_ID_SEQ
  START WITH 1
  NOMAXVALUE
  MINVALUE 1
  NOCYCLE
  NOCACHE
  NOORDER;


DROP SEQUENCE MONITORED_FILE_ID_SEQ;

commit;

CREATE SEQUENCE MONITORED_FILE_ID_SEQ
  START WITH 1
  NOMAXVALUE
  MINVALUE 1
  NOCYCLE
  NOCACHE
  NOORDER;


DROP SEQUENCE MONITORED_FOLDER_ID_SEQ;

commit;

CREATE SEQUENCE MONITORED_FOLDER_ID_SEQ
  START WITH 1
  NOMAXVALUE
  MINVALUE 1
  NOCYCLE
  NOCACHE
  NOORDER;


DROP SEQUENCE NEWS_ID_SEQ;

commit;

CREATE SEQUENCE NEWS_ID_SEQ
  START WITH 1
  NOMAXVALUE
  MINVALUE 1
  NOCYCLE
  NOCACHE
  NOORDER;


DROP SEQUENCE OWL_LOG_ID_SEQ;

commit;

CREATE SEQUENCE OWL_LOG_ID_SEQ
  START WITH 1
  NOMAXVALUE
  MINVALUE 1
  NOCYCLE
  NOCACHE
  NOORDER;


DROP SEQUENCE PREFS_ID_SEQ;

commit;

CREATE SEQUENCE PREFS_ID_SEQ
  START WITH 1
  NOMAXVALUE
  MINVALUE 1
  NOCYCLE
  NOCACHE
  NOORDER;


DROP SEQUENCE USERS_ID_SEQ;

commit;

CREATE SEQUENCE USERS_ID_SEQ
  START WITH 10
  NOMAXVALUE
  MINVALUE 1
  NOCYCLE
  NOCACHE
  NOORDER;



CREATE TABLE ACTIVE_SESSIONS
(
  SESSID    VARCHAR2(32)                   NOT NULL,
  USID      VARCHAR2(25),
  LASTUSED  NUMBER(10),
  IP        VARCHAR2(16),
  CURRENTDB NUMBER(4)
)
PCTUSED    40
PCTFREE    10
INITRANS   1
MAXTRANS   255
LOGGING
NOCACHE
NOPARALLEL;


CREATE TABLE COMMENTS
(
  ID            NUMBER(4)                       NOT NULL,
  FID           NUMBER(4)                       NOT NULL,
  USERID        NUMBER(4),
  COMMENT_DATE  DATE                            NOT NULL,
  COMMENTS      VARCHAR2(2048)  DEFAULT 'no comments recorded'     NOT NULL
)
LOGGING
NOCACHE
NOPARALLEL;


CREATE TABLE FILEDATA
(
  ID          NUMBER(4)                         NOT NULL,
  COMPRESSED  NUMBER(4)                         NOT NULL,
  DATA        BLOB
)
LOGGING
  LOB (DATA) STORE AS
      ( 
        ENABLE      STORAGE IN ROW
        CHUNK       8192
        PCTVERSION  10
        NOCACHE
      )
NOCACHE
NOPARALLEL;


CREATE TABLE FILES
(
  ID              NUMBER(4)                     NOT NULL,
  NAME            VARCHAR2(80)      DEFAULT 'Untitled'     NOT NULL,
  FILENAME        VARCHAR2(255)            NOT NULL,
  F_SIZE          NUMBER(19)                    NOT NULL,
  CREATORID       NUMBER(4)                     NOT NULL,
  PARENT          NUMBER(4)                     NOT NULL,
  CREATED         DATE                          NOT NULL,
  DESCRIPTION     VARCHAR2(2048),
  METADATA        VARCHAR2(2048),
  SECURITY        NUMBER(4)                     NOT NULL,
  GROUPID         NUMBER(4)                     NOT NULL,
  SMODIFIED       DATE                          NOT NULL,
  CHECKED_OUT     NUMBER(4)                     NOT NULL,
  MAJOR_REVISION  NUMBER(4)                     NOT NULL,
  MINOR_REVISION  NUMBER(4)                     NOT NULL,
  URL             NUMBER(4),
  PASSWORD        VARCHAR2(50),
  DOCTYPE         NUMBER(4),
  UPDATORID       NUMBER(4),
  linkedto 	  NUMBER(4),
  approved 	  NUMBER(4)
)
LOGGING
NOCACHE
NOPARALLEL;


CREATE TABLE FOLDERS
(
  ID           NUMBER(4)                        NOT NULL,
  NAME         VARCHAR2(255)   DEFAULT 'Untitled Folder'         NOT NULL,
  PARENT       NUMBER(4)                        NOT NULL,
  DESCRIPTION  VARCHAR2(2048),
  SECURITY     VARCHAR2(5)                 NOT NULL,
  GROUPID      NUMBER(4)                        NOT NULL,
  CREATORID    NUMBER(4)                        NOT NULL,
  PASSWORD        VARCHAR2(50),
  smodified	DATE
)
LOGGING 
NOCACHE
NOPARALLEL;


CREATE TABLE GROUPS
(
  ID    NUMBER(4)                               NOT NULL,
  NAME  VARCHAR2(30)                       NOT NULL
)
LOGGING 
NOCACHE
NOPARALLEL;


CREATE TABLE HTML
(
  ID                    NUMBER(4)               NOT NULL,
  TABLE_EXPAND_WIDTH    VARCHAR2(15),
  TABLE_COLLAPSE_WIDTH  VARCHAR2(15),
  --- MAIN_HEADER_BGCOLOR   VARCHAR2(15),
  --- BODY_BGCOLOR          VARCHAR2(15),
  BODY_BACKGROUND       VARCHAR2(255),
  OWL_LOGO              VARCHAR2(255),
  BODY_TEXTCOLOR        VARCHAR2(15),
  BODY_LINK             VARCHAR2(15),
  BODY_VLINK            VARCHAR2(15)
)
LOGGING 
NOCACHE
NOPARALLEL;


CREATE TABLE MEMBERGROUP
(
  USERID   NUMBER(4)                            NOT NULL,
  GROUPID  NUMBER(4) DEFAULT NULL,
  GROUPADMIN NUMBER(4)                  
)
LOGGING 
NOCACHE
NOPARALLEL;

CREATE TABLE METAKEYWORDS
(
  KEYWORD_ID   	NUMBER(4)                            NOT NULL,
  KEYWORD_TEXT	VARCHAR2(255)                          NOT NULL
)
LOGGING
NOCACHE
NOPARALLEL;


CREATE TABLE MIMES
(
  FILETYPE  VARCHAR2(10)                   NOT NULL,
  MIMETYPE  VARCHAR2(50)                   NOT NULL
)
LOGGING
NOCACHE
NOPARALLEL;


CREATE TABLE MONITORED_FILE
(
  ID      NUMBER(4)                             NOT NULL,
  USERID  NUMBER(4)                             NOT NULL,
  FID     NUMBER(4)                             NOT NULL
)
LOGGING 
NOCACHE
NOPARALLEL;


CREATE TABLE MONITORED_FOLDER
(
  ID      NUMBER(4)                             NOT NULL,
  USERID  NUMBER(4)                             NOT NULL,
  FID     NUMBER(4)                             NOT NULL
)
LOGGING
NOCACHE
NOPARALLEL;


CREATE TABLE NEWS
(
  ID             NUMBER(4)                      NOT NULL,
  GID            NUMBER(4)                      NOT NULL,
  NEWS_TITLE     VARCHAR2(255)     DEFAULT 'Untitled'   NOT NULL,
  NEWS_DATE      DATE                           NOT NULL,
  NEWS           VARCHAR2(4000)   DEFAULT 'No news text was recorded for this item'  NOT NULL,
  NEWS_END_DATE  DATE             DEFAULT ADD_MONTHS(SYSDATE,1)     NOT NULL
)
LOGGING
NOCACHE
NOPARALLEL;


CREATE TABLE OWL_LOG
(
  ID        NUMBER(4)                           NOT NULL,
  USERID    NUMBER(4),
  FILENAME  VARCHAR2(255),
  PARENT    NUMBER(4),
  ACTION    VARCHAR2(40),
  DETAILS   VARCHAR2(4000),
  IP        VARCHAR2(16),
  AGENT     VARCHAR2(255),
  LOGDATE   DATE                                NOT NULL,
  TYPE      VARCHAR2(20)
)
LOGGING 
NOCACHE
NOPARALLEL;


CREATE TABLE PREFS
(
  ID                    NUMBER(4)               NOT NULL,
  EMAIL_FROM            VARCHAR2(80),
  EMAIL_FROMNAME        VARCHAR2(80),
  EMAIL_REPLYTO         VARCHAR2(80),
  EMAIL_SERVER          VARCHAR2(80),
  EMAIL_SUBJECT         VARCHAR2(60),
  LOOKATHD              VARCHAR2(15),
  LOOKATHDDEL           NUMBER(4),
  DEF_FILE_SECURITY     NUMBER(4),
  DEF_FILE_GROUP_OWNER  NUMBER(4),
  DEF_FILE_OWNER        NUMBER(4),
  DEF_FILE_TITLE        VARCHAR2(40),
  DEF_FILE_META         VARCHAR2(40),
  DEF_FOLD_SECURITY     NUMBER(4),
  DEF_FOLD_GROUP_OWNER  NUMBER(4),
  DEF_FOLD_OWNER        NUMBER(4),
  MAX_FILESIZE          NUMBER(15),
  TMPDIR                VARCHAR2(255),
  TIMEOUT               NUMBER(4),
  EXPAND                NUMBER(4),
  VERSION_CONTROL       NUMBER(4),
  RESTRICT_VIEW         NUMBER(4),
  HIDE_BACKUP           NUMBER(4),
  DBDUMP_PATH           VARCHAR2(80),
  GZIP_PATH             VARCHAR2(80),
  TAR_PATH              VARCHAR2(80),
  UNZIP_PATH            VARCHAR2(80),
  POD2HTML_PATH         VARCHAR2(80),
  PDFTOTEXT_PATH        VARCHAR2(80),
  WORDTOTEXT_PATH        VARCHAR2(80),
  FILE_PERM             NUMBER(4),
  FOLDER_PERM           NUMBER(4),
  LOGGING               NUMBER(4),
  LOG_FILE              NUMBER(4),
  LOG_LOGIN             NUMBER(4),
  LOG_REC_PER_PAGE      NUMBER(4),
  REC_PER_PAGE          NUMBER(4),
  SELF_REG              NUMBER(4),
  SELF_REG_QUOTA        NUMBER(4),
  SELF_REG_NOTIFY       NUMBER(4),
  SELF_REG_ATTACHFILE   NUMBER(4),
  SELF_REG_DISABLED     NUMBER(4),
  SELF_REG_NOPREFACCES  NUMBER(4),
  SELF_REG_MAXSESSIONS  NUMBER(4),
  SELF_REG_GROUP        NUMBER(4),
  ANON_RO               NUMBER(4),
  ANON_USER             NUMBER(4),
  FILE_ADMIN_GROUP      NUMBER(4),
  FORGOT_PASS           NUMBER(4),
  COLLECT_TRASH         NUMBER(4),
  TRASH_CAN_LOCATION    VARCHAR2(80),
  ALLOW_POPUP           NUMBER(4),
  ALLOW_CUSTPOPUP       NUMBER(4),
  STATUS_BAR_LOCATION   NUMBER(4),
  --- HIDE_BULK             NUMBER(4),
  REMEMBER_ME           NUMBER(4),
  COOKIE_TIMEOUT        NUMBER(4),
  USE_SMTP              NUMBER(4),
  USE_SMTP_AUTH         NUMBER(4),
  SMTP_PASSWD           VARCHAR2(40),
  SEARCH_BAR            NUMBER(4),
  BULK_BUTTONS          NUMBER(4),
  ACTION_BUTTONS        NUMBER(4),
  FOLDER_TOOLS          NUMBER(4),
  PREF_BAR              NUMBER(4),
  smtp_auth_login 	VARCHAR2(50),
  expand_disp_status 	NUMBER(4),
  expand_disp_doc_num 	NUMBER(4),
  expand_disp_doc_type 	NUMBER(4),
  expand_disp_title 	NUMBER(4),
  expand_disp_version 	NUMBER(4),
  expand_disp_file 	NUMBER(4),
  expand_disp_size 	NUMBER(4),
  expand_disp_posted 	NUMBER(4),
  expand_disp_modified 	NUMBER(4),
  expand_disp_action 	NUMBER(4),
  expand_disp_held 	NUMBER(4),
  collapse_disp_status 	NUMBER(4),
  collapse_disp_doc_num NUMBER(4),
  collapse_disp_doc_type NUMBER(4),
  collapse_disp_title 	NUMBER(4),
  collapse_disp_version NUMBER(4),
  collapse_disp_file 	NUMBER(4),
  collapse_disp_size 	NUMBER(4),
  collapse_disp_posted 	NUMBER(4),
  collapse_disp_modified NUMBER(4),
  collapse_disp_action 	NUMBER(4),
  collapse_disp_held 	NUMBER(4),
  expand_search_disp_score 	NUMBER(4),
  expand_search_disp_folder_path NUMBER(4),
  expand_search_disp_doc_type 	NUMBER(4),
  expand_search_disp_file 	NUMBER(4),
  expand_search_disp_size 	NUMBER(4),
  expand_search_disp_posted 	NUMBER(4),
  expand_search_disp_modified 	NUMBER(4),
  expand_search_disp_action 	NUMBER(4),
  collapse_search_disp_score 	NUMBER(4),
  colps_search_disp_fld_path 	NUMBER(4),
  collapse_search_disp_doc_type 	NUMBER(4),
  collapse_search_disp_file 	NUMBER(4),
  collapse_search_disp_size 	NUMBER(4),
  collapse_search_disp_posted 	NUMBER(4),
  collapse_search_disp_modified NUMBER(4),
  collapse_search_disp_action 	NUMBER(4),
  hide_folder_doc_count 	NUMBER(4),
  old_action_icons 	NUMBER(4),
  search_result_folders NUMBER(4),
  restore_file_prefix 	VARCHAR2(50),
  major_revision 	NUMBER(4),
  minor_revision 	NUMBER(4),
  doc_id_prefix 	VARCHAR2(10),
  doc_id_num_digits 	NUMBER(4),
  view_doc_in_new_window NUMBER(4),
  admin_login_to_browse_page 	NUMBER(4),
  save_keywords_to_db 	NUMBER(4),
  self_reg_homedir 	NUMBER(4),
  self_reg_firstdir 	NUMBER(4),
  virus_path 		VARCHAR2(80),
  peer_review 		NUMBER(4),
  peer_opt 		NUMBER(4),
  folder_size 		NUMBER(4),
  download_folder_zip 	NUMBER(4),
  display_password_override NUMBER(4),
  self_create_homedir  NUMBER(4),
  self_captcha  NUMBER(4),
  info_panel_wide NUMBER(4),
  track_favorites NUMBER(4)
)
LOGGING 
NOCACHE
NOPARALLEL;


CREATE TABLE SEARCHIDX
(
  WORDID     NUMBER(4)                          DEFAULT NULL,
  OWLFILEID  NUMBER(4)                          DEFAULT NULL
)
LOGGING 
NOCACHE
NOPARALLEL;


CREATE TABLE USERS
(
  ID              NUMBER(4)                     NOT NULL,
  GROUPID         VARCHAR2(10)             NOT NULL,
  USERNAME        VARCHAR2(20)   DEFAULT 'Unnamed'    NOT NULL,
  NAME            VARCHAR2(50)    DEFAULT 'Unnamed'   NOT NULL,
  PASSWORD        VARCHAR2(50)     DEFAULT ' '        NOT NULL,
  QUOTA_MAX       NUMBER(16)                    NOT NULL,
  QUOTA_CURRENT   NUMBER(16)                    NOT NULL,
  EMAIL           VARCHAR2(255),
  NOTIFY          NUMBER(4),
  ATTACHFILE      NUMBER(4),
  DISABLED        NUMBER(4),
  NOPREFACCESS    NUMBER(4),
  LANGUAGE        VARCHAR2(15),
  MAXSESSIONS     NUMBER(4),
  LASTLOGIN       DATE                          NOT NULL,
  CURLOGIN        DATE                          NOT NULL,
  LASTNEWS        NUMBER(4),
  NEWSADMIN       NUMBER(4),
  COMMENT_NOTIFY  NUMBER(4),
  buttonstyle	  VARCHAR2(255),
  HOMEDIR         NUMBER(4),
  FIRSTDIR        NUMBER(4),
  EMAIL_TOOL      NUMBER(4)
)
LOGGING
NOCACHE
NOPARALLEL;

INSERT INTO users 
	(id,groupid,username,name,password,quota_max,quota_current,notify,
	attachfile,disabled,noprefaccess,maxsessions,lastlogin,curlogin,
	newsadmin, comment_notify, lastnews
	) VALUES (
	1, 0, 'admin', 'Administrator', 'admin', '0', '0', '0','0','0', '0', 
	'0', SYSDATE, SYSDATE, '0', '0','0'
	);

INSERT INTO users 
	(id,groupid,username,name,password,quota_max,quota_current,notify,
	attachfile,disabled,noprefaccess,maxsessions,lastlogin,curlogin,
	newsadmin, comment_notify, lastnews
	) VALUES (
	2, 1, 'guest', 'Anonymous', 'guest', '0', '0', '0', '0','1', '1', '19', 
	SYSDATE, SYSDATE, '0', '0','0'
	);

INSERT INTO groups VALUES (0, 'Administrators');

UPDATE groups SET id = 0 WHERE name = 'Administrators';
INSERT INTO groups VALUES (1, 'Anonymous');
INSERT INTO groups VALUES (2, 'File Admin');

--- INSERT INTO folders VALUES (1, 'Documents', 0, ' ', 50, 0, 0, ' ');
INSERT INTO folders VALUES (1,'Documents',0,'','51',0,1,'',SYSDATE);



CREATE TABLE WORDIDX
(
  WORDID  NUMBER(4)                             DEFAULT NULL,
  WORD    VARCHAR2(128)                    NOT NULL
)
LOGGING
NOCACHE
NOPARALLEL;

CREATE TABLE peerreview 
(
  reviewer_id 	NUMBER(4),
  file_id 	NUMBER(4),
  status 	NUMBER(4)
);


CREATE UNIQUE INDEX PK_SEARCHIDX ON SEARCHIDX
(OWLFILEID)
LOGGING
NOPARALLEL;


CREATE UNIQUE INDEX PK_WORDIDX ON WORDIDX
(WORD)
LOGGING
NOPARALLEL;


CREATE OR REPLACE TRIGGER COMMENTS_ID_TRIGGER
BEFORE INSERT
ON COMMENTS
REFERENCING NEW AS NEW OLD AS OLD
FOR EACH ROW
BEGIN
  SELECT COMMENTS_ID_SEQ.NEXTVAL INTO :NEW.id FROM DUAL;
END;
/
SHOW ERRORS;



CREATE OR REPLACE TRIGGER FILES_ID_TRIGGER
  BEFORE INSERT ON FILES
  REFERENCING NEW AS NEW OLD AS OLD
  FOR EACH ROW
BEGIN
  SELECT files_ID_SEQ.NEXTVAL INTO :NEW.id FROM DUAL;
END;
/
SHOW ERRORS;



CREATE OR REPLACE TRIGGER FOLDERS_ID_TRIGGER
  BEFORE INSERT ON FOLDERS
  REFERENCING NEW AS NEW OLD AS OLD
  FOR EACH ROW
BEGIN
  SELECT folders_ID_SEQ.NEXTVAL INTO :NEW.id FROM DUAL;
END;
/
SHOW ERRORS;



CREATE OR REPLACE TRIGGER GROUPS_ID_TRIGGER
  BEFORE INSERT ON GROUPS
  REFERENCING NEW AS NEW OLD AS OLD
  FOR EACH ROW
BEGIN
  SELECT groups_ID_SEQ.NEXTVAL INTO :NEW.id FROM DUAL;
END;
/
SHOW ERRORS;



CREATE OR REPLACE TRIGGER HTML_ID_TRIGGER
  BEFORE INSERT ON HTML
  REFERENCING NEW AS NEW OLD AS OLD
  FOR EACH ROW
BEGIN
  SELECT html_ID_SEQ.NEXTVAL INTO :NEW.id FROM DUAL;
END;
/
SHOW ERRORS;



CREATE OR REPLACE TRIGGER MONITORED_FILE_ID_TRIGGER
  BEFORE INSERT ON MONITORED_FILE
  REFERENCING NEW AS NEW OLD AS OLD
  FOR EACH ROW
BEGIN
  SELECT monitored_file_ID_SEQ.NEXTVAL INTO :NEW.id FROM DUAL;
END;
/
SHOW ERRORS;



CREATE OR REPLACE TRIGGER MONITORED_FOLDER_ID_TRIGGER
  BEFORE INSERT ON MONITORED_FOLDER
  REFERENCING NEW AS NEW OLD AS OLD
  FOR EACH ROW
BEGIN
  SELECT monitored_folder_ID_SEQ.NEXTVAL INTO :NEW.id FROM DUAL;
END;
/
SHOW ERRORS;



CREATE OR REPLACE TRIGGER NEWS_ID_TRIGGER
  BEFORE INSERT ON NEWS
  REFERENCING NEW AS NEW OLD AS OLD
  FOR EACH ROW
BEGIN
  SELECT news_ID_SEQ.NEXTVAL INTO :NEW.id FROM DUAL;
END;
/
SHOW ERRORS;



CREATE OR REPLACE TRIGGER OWL_LOG_ID_TRIGGER
  BEFORE INSERT ON OWL_LOG
  REFERENCING NEW AS NEW OLD AS OLD
  FOR EACH ROW
BEGIN
  SELECT owl_log_ID_SEQ.NEXTVAL INTO :NEW.id FROM DUAL;
END;
/
SHOW ERRORS;



CREATE OR REPLACE TRIGGER PREFS_ID_TRIGGER
  BEFORE INSERT ON PREFS
  REFERENCING NEW AS NEW OLD AS OLD
  FOR EACH ROW
BEGIN
  SELECT prefs_ID_SEQ.NEXTVAL INTO :NEW.id FROM DUAL;
END;
/
SHOW ERRORS;



CREATE OR REPLACE TRIGGER USERS_ID_TRIGGER
  BEFORE INSERT ON USERS
  REFERENCING NEW AS NEW OLD AS OLD
  FOR EACH ROW
BEGIN
  SELECT users_ID_SEQ.NEXTVAL INTO :NEW.id FROM DUAL;
END;
/
SHOW ERRORS;



ALTER TABLE ACTIVE_SESSIONS ADD (
  PRIMARY KEY (SESSID)
    USING INDEX 
);


ALTER TABLE COMMENTS ADD (
  PRIMARY KEY (ID)
    USING INDEX
);


ALTER TABLE FILEDATA ADD (
  PRIMARY KEY (ID)
    USING INDEX 
);


ALTER TABLE FILES ADD (
  PRIMARY KEY (ID)
    USING INDEX 
);


ALTER TABLE FOLDERS ADD (
  PRIMARY KEY (ID)
    USING INDEX 
);


ALTER TABLE GROUPS ADD (
  PRIMARY KEY (ID)
    USING INDEX 
);


ALTER TABLE HTML ADD (
  PRIMARY KEY (ID)
    USING INDEX 
);


ALTER TABLE MIMES ADD (
  PRIMARY KEY (FILETYPE)
    USING INDEX 
);


ALTER TABLE MONITORED_FILE ADD (
  PRIMARY KEY (ID)
    USING INDEX 
);


ALTER TABLE MONITORED_FOLDER ADD (
  PRIMARY KEY (ID)
    USING INDEX 
);


ALTER TABLE NEWS ADD (
  PRIMARY KEY (ID)
    USING INDEX
);


ALTER TABLE OWL_LOG ADD (
  PRIMARY KEY (ID)
    USING INDEX 
);


ALTER TABLE PREFS ADD (
  PRIMARY KEY (ID)
    USING INDEX
);


ALTER TABLE USERS ADD (
  PRIMARY KEY (ID)
    USING INDEX
);



INSERT INTO html VALUES (1,'90%','50%','','owl_logo1.gif','#000000','#000000','#000000');

prompt ********************************************;
prompt * Please enter some default config variables *;
prompt ********************************************;

INSERT INTO prefs VALUES 
	(owl_admin.prefs_id_seq.nextval,'&from_email_address','OWL','&reply_to_email_address','&email_server','[OWL] :','false',1,
	0,0,1,'<font color=red>No Info</font>','not in\r\ndb',50,0,1,5120000,
	'&temporary_directory',30,1,1,0,1,'/usr/bin/mysqldump','/bin/gzip',
	'/bin/tar','/usr/bin/unzip','/usr/local/bin/pod2html','/usr/bin/pdftotext',
	'/usr/local/bin/antiword',4,54,0,1,1,5,0,0,0,0,0,0,0,0,1,1,2,2,1,0,'',1,1,0,30,0,0,'',
	2,0,1,1,3,'',1,1,1,1,1,1,1,1,1,1,1,1,0,1,1,1,1,1,0,0,0,1,1,1,1,1,1,1,1,1,1,1,1,1,1,0,0,
	0,0,0,1,'RESTORED-',1,0,'ABC-',3,0,0,0,1,1,'','0','0','1','0','1','0'
	);


ALTER SESSION SET NLS_DATE_FORMAT='YYYY-MM-DD HH24:MI:SS';


UPDATE users SET language = 'English';
UPDATE users SET password = '21232f297a57a5a743894a0e4a801fc3' WHERE name = 'Administrator';
UPDATE users SET password = '084e0343a0486ff05530df6c705c8bb4' WHERE name = 'Anonymous';
UPDATE users SET homedir = '1'; 
UPDATE users SET firstdir = '1'; 

UPDATE prefs SET pdftotext_path = '/usr/bin/pdftotext';
UPDATE prefs SET pdftotext_path = '/usr/local/bin/antiword';
UPDATE prefs SET pod2html_path = '/usr/local/bin/pod2html';
UPDATE users SET buttonstyle = 'Blue';
UPDATE prefs SET rec_per_page = '0';

INSERT INTO mimes VALUES ('ai','application/postscript');
INSERT INTO mimes VALUES ('aif','audio/x-aiff');
INSERT INTO mimes VALUES ('aifc','audio/x-aiff');
INSERT INTO mimes VALUES ('aiff','audio/x-aiff');
INSERT INTO mimes VALUES ('asc','text/plain');
INSERT INTO mimes VALUES ('au','audio/basic');
INSERT INTO mimes VALUES ('avi','video/x-msvideo');
INSERT INTO mimes VALUES ('bcpio','application/x-bcpio');
INSERT INTO mimes VALUES ('bin','application/octet-stream');
INSERT INTO mimes VALUES ('bmp','image/bmp');
INSERT INTO mimes VALUES ('cdf','application/x-netcdf');
INSERT INTO mimes VALUES ('class','application/octet-stream');
INSERT INTO mimes VALUES ('cpio','application/x-cpio');
INSERT INTO mimes VALUES ('cpt','application/mac-compactpro');
INSERT INTO mimes VALUES ('csh','application/x-csh');
INSERT INTO mimes VALUES ('css','text/css');
INSERT INTO mimes VALUES ('dcr','application/x-director');
INSERT INTO mimes VALUES ('dir','application/x-director');
INSERT INTO mimes VALUES ('dms','application/octet-stream');
INSERT INTO mimes VALUES ('doc','application/msword');
INSERT INTO mimes VALUES ('dvi','application/x-dvi');
INSERT INTO mimes VALUES ('dxr','application/x-director');
INSERT INTO mimes VALUES ('eps','application/postscript');
INSERT INTO mimes VALUES ('etx','text/x-setext');
INSERT INTO mimes VALUES ('exe','application/octet-stream');
INSERT INTO mimes VALUES ('ez','application/andrew-inset');
INSERT INTO mimes VALUES ('gif','image/gif');
INSERT INTO mimes VALUES ('gtar','application/x-gtar');
INSERT INTO mimes VALUES ('hdf','application/x-hdf');
INSERT INTO mimes VALUES ('hqx','application/mac-binhex40');
INSERT INTO mimes VALUES ('htm','text/html');
INSERT INTO mimes VALUES ('html','text/html');
INSERT INTO mimes VALUES ('ice','x-conference/x-cooltalk');
INSERT INTO mimes VALUES ('ief','image/ief');
INSERT INTO mimes VALUES ('iges','model/iges');
INSERT INTO mimes VALUES ('igs','model/iges');
INSERT INTO mimes VALUES ('jpe','image/jpeg');
INSERT INTO mimes VALUES ('jpeg','image/jpeg');
INSERT INTO mimes VALUES ('jpg','image/jpeg');
INSERT INTO mimes VALUES ('js','application/x-javascript');
INSERT INTO mimes VALUES ('kar','audio/midi');
INSERT INTO mimes VALUES ('latex','application/x-latex');
INSERT INTO mimes VALUES ('lha','application/octet-stream');
INSERT INTO mimes VALUES ('lzh','application/octet-stream');
INSERT INTO mimes VALUES ('man','application/x-troff-man');
INSERT INTO mimes VALUES ('me','application/x-troff-me');
INSERT INTO mimes VALUES ('mesh','model/mesh');
INSERT INTO mimes VALUES ('mid','audio/midi');
INSERT INTO mimes VALUES ('midi','audio/midi');
INSERT INTO mimes VALUES ('mif','application/vnd.mif');
INSERT INTO mimes VALUES ('mov','video/quicktime');
INSERT INTO mimes VALUES ('movie','video/x-sgi-movie');
INSERT INTO mimes VALUES ('mp2','audio/mpeg');
INSERT INTO mimes VALUES ('mp3','audio/mpeg');
INSERT INTO mimes VALUES ('mpe','video/mpeg');
INSERT INTO mimes VALUES ('mpeg','video/mpeg');
INSERT INTO mimes VALUES ('mpg','video/mpeg');
INSERT INTO mimes VALUES ('mpga','audio/mpeg');
INSERT INTO mimes VALUES ('ms','application/x-troff-ms');
INSERT INTO mimes VALUES ('msh','model/mesh');
INSERT INTO mimes VALUES ('nc','application/x-netcdf');
INSERT INTO mimes VALUES ('oda','application/oda');
INSERT INTO mimes VALUES ('pbm','image/x-portable-bitmap');
INSERT INTO mimes VALUES ('pdb','chemical/x-pdb');
INSERT INTO mimes VALUES ('pdf','application/pdf');
INSERT INTO mimes VALUES ('pgm','image/x-portable-graymap');
INSERT INTO mimes VALUES ('pgn','application/x-chess-pgn');
INSERT INTO mimes VALUES ('png','image/png');
INSERT INTO mimes VALUES ('pnm','image/x-portable-anymap');
INSERT INTO mimes VALUES ('ppm','image/x-portable-pixmap');
INSERT INTO mimes VALUES ('ppt','application/vnd.ms-powerpoint');
INSERT INTO mimes VALUES ('ps','application/postscript');
INSERT INTO mimes VALUES ('qt','video/quicktime');
INSERT INTO mimes VALUES ('ra','audio/x-realaudio');
INSERT INTO mimes VALUES ('ram','audio/x-pn-realaudio');
INSERT INTO mimes VALUES ('ras','image/x-cmu-raster');
INSERT INTO mimes VALUES ('rgb','image/x-rgb');
INSERT INTO mimes VALUES ('rm','audio/x-pn-realaudio');
INSERT INTO mimes VALUES ('roff','application/x-troff');
INSERT INTO mimes VALUES ('rpm','audio/x-pn-realaudio-plugin');
INSERT INTO mimes VALUES ('rtf','text/rtf');
INSERT INTO mimes VALUES ('rtx','text/richtext');
INSERT INTO mimes VALUES ('sgm','text/sgml');
INSERT INTO mimes VALUES ('sgml','text/sgml');
INSERT INTO mimes VALUES ('sh','application/x-sh');
INSERT INTO mimes VALUES ('shar','application/x-shar');
INSERT INTO mimes VALUES ('silo','model/mesh');
INSERT INTO mimes VALUES ('sit','application/x-stuffit');
INSERT INTO mimes VALUES ('skd','application/x-koan');
INSERT INTO mimes VALUES ('skm','application/x-koan');
INSERT INTO mimes VALUES ('skp','application/x-koan');
INSERT INTO mimes VALUES ('skt','application/x-koan');
INSERT INTO mimes VALUES ('smi','application/smil');
INSERT INTO mimes VALUES ('smil','application/smil');
INSERT INTO mimes VALUES ('snd','audio/basic');
INSERT INTO mimes VALUES ('spl','application/x-futuresplash');
INSERT INTO mimes VALUES ('src','application/x-wais-source');
INSERT INTO mimes VALUES ('sv4cpio','application/x-sv4cpio');
INSERT INTO mimes VALUES ('sv4crc','application/x-sv4crc');
INSERT INTO mimes VALUES ('swf','application/x-shockwave-flash');
INSERT INTO mimes VALUES ('t','application/x-troff');
INSERT INTO mimes VALUES ('tar','application/x-tar');
INSERT INTO mimes VALUES ('tcl','application/x-tcl');
INSERT INTO mimes VALUES ('tex','application/x-tex');
INSERT INTO mimes VALUES ('texi','application/x-texinfo');
INSERT INTO mimes VALUES ('texinfo','application/x-texinfo');
INSERT INTO mimes VALUES ('tif','image/tiff');
INSERT INTO mimes VALUES ('tiff','image/tiff');
INSERT INTO mimes VALUES ('tr','application/x-troff');
INSERT INTO mimes VALUES ('tsv','text/tab-separated-values');
INSERT INTO mimes VALUES ('txt','text/plain');
INSERT INTO mimes VALUES ('ustar','application/x-ustar');
INSERT INTO mimes VALUES ('vcd','application/x-cdlink');
INSERT INTO mimes VALUES ('vrml','model/vrml');
INSERT INTO mimes VALUES ('wav','audio/x-wav');
INSERT INTO mimes VALUES ('wrl','model/vrml');
INSERT INTO mimes VALUES ('xbm','image/x-xbitmap');
INSERT INTO mimes VALUES ('xls','application/vnd.ms-excel');
INSERT INTO mimes VALUES ('xml','text/xml');
INSERT INTO mimes VALUES ('xpm','image/x-xpixmap');
INSERT INTO mimes VALUES ('xwd','image/x-xwindowdump');
INSERT INTO mimes VALUES ('xyz','chemical/x-pdb');
INSERT INTO mimes VALUES ('zip','application/zip');
INSERT INTO mimes VALUES ('gz','application/x-gzip');
INSERT INTO mimes VALUES ('tgz','application/x-gzip');
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

  CREATE TABLE docfieldslabel (
  doc_field_id number(4) NOT NULL,
  field_label varchar2(80) NOT NULL,
  locale varchar2(80) NOT NULL )
  LOGGING 
  NOCACHE
  NOPARALLEL;

  ALTER TABLE DOCFIELDSLABEL ADD (
  PRIMARY KEY (doc_field_id)
  USING INDEX);


  CREATE TABLE doctype (
     doc_type_id number(4) not null,
     doc_type_name varchar2(255) not null,
     primary key (doc_type_id))
     LOGGING 
     NOCACHE
     NOPARALLEL;



   CREATE TABLE docfields (
       id number(4) not null,
       doc_type_id number(4) not null ,
       field_name VARCHAR2(80) not null,
       field_position number(4) not null,
       field_type VARCHAR2(80) not null,
       field_values VARCHAR2(80) not null,
       field_size number(38) not null,
       searchable number(4) not null,
       show_desc number(4) default 0 not null,
       required number(4) not null,
         primary key (id)
     )
   LOGGING 
   NOCACHE
   NOPARALLEL;

 CREATE TABLE docfieldvalues (
         id number(4) not null,
         file_id number(4) not null ,
         field_name varchar2(80) not null,
         field_value varchar2(80) not null,
         primary key (id)
 )
 LOGGING 
 NOCACHE
 NOPARALLEL;


 CREATE OR REPLACE TRIGGER DOCTYPE_ID_TRIGGER
  BEFORE INSERT ON DOCTYPE
  REFERENCING NEW AS NEW OLD AS OLD
  FOR EACH ROW
BEGIN
  SELECT doctype_ID_SEQ.NEXTVAL INTO :NEW.doc_type_id FROM DUAL;
END;
/
show errors;

CREATE OR REPLACE TRIGGER DOCFIELDS_ID_TRIGGER
  BEFORE INSERT ON DOCFIELDS
  REFERENCING NEW AS NEW OLD AS OLD
  FOR EACH ROW
BEGIN
  SELECT docfields_ID_SEQ.NEXTVAL INTO :NEW.id FROM DUAL;
END;
/
show errors;

CREATE OR REPLACE TRIGGER DOCFIELDVALUES_ID_TRIGGER
  BEFORE INSERT ON DOCFIELDVALUES
  REFERENCING NEW AS NEW OLD AS OLD
  FOR EACH ROW
BEGIN
  SELECT docfieldvalues_ID_SEQ.NEXTVAL INTO :NEW.id FROM DUAL;
END;
/
show errors;


INSERT INTO doctype (doc_type_name) values ('Default');


prompt ********************************************;
prompt * IT IS SAFE TO IGNORE ANY MESSAGES ABOUT  *;
prompt * NOT BEING ABLE TO DROP A SEQUENCE        *;
prompt * OR SYNONYM THAT DOES NOT EXIST.          *;
prompt *                                          *;
prompt * owl tables have been created. Enjoy!     *;
prompt ********************************************;
