
#include "info.h"

#if HAVE_STDIO_H
#include <stdio.h>
#endif
#if HAVE_STDLIB_H
#include <stdlib.h>
#endif
#if HAVE_STRING_H
#include <string.h>
#endif
#if HAVE_STRINGS_H
#include <strings.h>
#endif
#if HAVE_SYS_TIME_H
#include <sys/time.h>
#endif
#if HAVE_TIME_H
#include <time.h>
#endif
#if HAVE_SYS_RESOURCE_H
#include <sys/resource.h>
#endif
#if HAVE_UNISTD_H
#include <unistd.h>
#endif
#if HAVE_SYS_DIRENT_H && !HAVE_DIRENT_H
#include <sys/dirent.h>
#endif
#if HAVE_DIRENT_H
#include <dirent.h>
#endif
#if HAVE_SYS_SOCKET_H
#include <sys/socket.h>
#endif
#if HAVE_NETDB_H
#include <netdb.h>
#endif
#if HAVE_TYPES_H
#include <types.h>
#endif
#if HAVE_NETINET_IN_H
#include <netinet/in.h>
#endif
#if HAVE_ARPA_INET_H
#include <arpa/inet.h>
#endif
#if HAVE_SYS_STAT_H
#include <sys/stat.h>
#endif
#if HAVE_CTYPE_H
#include <ctype.h>
#endif
#if HAVE_ERRNO_H
#include <errno.h>
#endif
#if HAVE_GD_H
#include <gd.h>
#define HAVE_GD
gdImagePtr im;
gdPoint points[4];
#endif
#if HAVE_GDFONTL_H
#include <gdfontl.h>
#endif
#if HAVE_GDFONTT_H
#include <gdfontt.h>
#endif
#if HAVE_GDFONTS_H
#include <gdfonts.h>
#endif
#if HAVE_GDFONTMB_H
#include <gdfontmb.h>
#endif
#if HAVE_GDFONTG_H
#include <gdfontg.h>
#endif

#if HAVE_FOPEN64
#define _FILE_OFFSET_BITS 64
#define MY_FOPEN fopen
#else
#define MY_FOPEN fopen
#endif


#define MAXLEN 20000
long long int my_atoll (const char *nptr);

FILE *fp_tt; 

char outdir[MAXLEN];
char dirname[MAXLEN];
char buf[MAXLEN];
char url[MAXLEN];
char urly[MAXLEN];
char user[MAXLEN];
char period[MAXLEN];
char msg[1024];
char per_hour[128];
char tmp[MAXLEN];
char tmp2[MAXLEN];
char tmp3[MAXLEN];
char tmp4[MAXLEN];
char tmp5[MAXLEN];
char tmp6[MAXLEN];
char parse_out[MAXLEN];
char arqtt[MAXLEN];
char html[MAXLEN];
char datestimes[MAXLEN];
char ConfigFile[MAXLEN];
char href[MAXLEN];
char href2[MAXLEN];
char href3[MAXLEN];
char df[20];
char day[3], month[4], year[5];
char nmonth[30];
char y1[5], y2[5];
char d1[3], d2[3];
char m1[4], m2[4];
char ltext110[50];
char cdfrom[30];
char cduntil[30];
char LastLog[5];
char RemoveTempFiles[4];
char ReplaceIndex[256];
char Index[20];
char OverwriteReport[4];
char u2[255];
char RecordsWithoutUser[20];
char UseComma[4];
char MailUtility[6];
char TopSitesNum[20];
char TopUsersNum[20];
char ExcludeCodes[256];
char TopsitesSortField[15];
char TopsitesSortType[20];
char ReportType[255];
char UserTabFile[255];
char warea[MAXLEN];
char name[MAXLEN];
char LongUrl[20];
char Ip2Name[20];
char language[255];
char bufy[MAXLEN];
char AccessLog[MAXLEN];
char Title[MAXLEN];
char BgColor[MAXLEN];
char BgImage[MAXLEN];
char TxColor[MAXLEN];
char TxBgColor[MAXLEN];
char TiColor[MAXLEN];
char LogoImage[MAXLEN];
char LogoText[MAXLEN];
char LogoTextColor[MAXLEN];
char Width[MAXLEN];
char Height[MAXLEN];
char FontFace[MAXLEN];
char HeaderColor[MAXLEN];
char HeaderBgColor[MAXLEN];
char FontSize[MAXLEN];
char PasswdFile[MAXLEN];
char TempDir[MAXLEN];
char OutputDir[MAXLEN];
char OutputEmail[MAXLEN];
char TopuserSortField[30];
char UserSortField[30];
char TopuserSortOrder[10];
char UserSortOrder[10];
char UserAgentLog[255];
char module[255];
char ExcludeHosts[255];
char ExcludeUsers[255];
char DateFormat[2];
char PerUserLimitFile[255];
char PerUserLimit[20];
char UserIp[5];
char MaxElapsed[255];
char datetimeby[10];
char csort[255];
char CharSet[255];
char UserInvalidChar[255];
char Graphs[5];
char GraphDaysBytesBarColor[255];
char Privacy[10];
char PrivacyString[255];
char PrivacyStringColor[30];
char IncludeUsers[MAXLEN];
char ExcludeString[MAXLEN];
char SuccessfulMsg[5];
char TopUserFields[255];
char UserReportFields[255];
char DataFile[MAXLEN];
char DataFileDelimiter[3];
char DataFileFields[MAXLEN];
char DataFileUrl[20];
char SiteUserTimeDateType[10];
char ShowReadStatistics[5];
char IndexSortOrder[5];
char DansGuardianConf[MAXLEN];
char DansguardianIgnoreDate[10];
char SquidGuardConf[MAXLEN];
char SquidGuarddbHome[255];
char SquidGuardLogFormat[MAXLEN];
char SquidGuardLogAlternate[MAXLEN];
char SquidguardIgnoreDate[10];
char ShowSargInfo[5];
char BytesInSitesUsersReport[10];
char ShowSargLogo[5];
char ParsedOutputLog[MAXLEN];
char ParsedOutputLogCompress[255];
char DisplayedValues[20];
char HeaderFontSize[4];
char TitleFontSize[5];
char wwwDocumentRoot[MAXLEN];
char ExternalCSSFile[MAXLEN];
char BlockIt[255];
char BlockImage[512];
char NtlmUserFormat[30];
char hbc1[30];
char hbc2[255];
char hbc3[30];
char hbc4[30];
char hbc5[30];
char hbc6[30];
char hbc7[30];
char hbc8[30];
char hbc9[30];
char hbc10[30];
char IndexTree[10];
char UserAuthentication[10];
char AuthUserFile[255];
char AuthName[512];
char AuthType[255];
char Require[512];
char DownloadSuffix[MAXLEN];
char *excludecode;
char *userfile;
char *str;
char *str2;
char text[200][255];
char val1[MAXLEN];
char val2[MAXLEN];
char val3[MAXLEN];
char val4[MAXLEN];
char val5[MAXLEN];
char val6[MAXLEN];
char val7[MAXLEN];
char val8[MAXLEN];
char val9[MAXLEN];
char val10[MAXLEN];
char val11[MAXLEN];
char wwork1[MAXLEN];
char wwork2[MAXLEN];
char wwork3[MAXLEN];
char ftime[128];
char mask[MAXLEN];
char httplink[MAXLEN];
char html_old[MAXLEN];
char siteind[MAXLEN];
char site[MAXLEN];
char us[50];
char email[MAXLEN];
char test[1];
char ouser2[255];
char user2[MAXLEN];
char wentp[512];
char addr[MAXLEN];
char suffix[10];
char download_url[MAXLEN];
char Ulimit[6];
char RealtimeTypes[1024];
char cmd[255];
char ImageFile[255];
char tbuf[128];
char ip[25];
char RealtimeUnauthRec[15];

int  excode;
int  idate;
int  smartfilter;
int  denied_count;
int  download_count;
int  authfail_count;
int  dansguardian_count;
int  squidguard_count;
int  limit_flag;
int  color1;
int  color2;
int  color3;
int  z1, z2, z3;
int  ttopen;
int  ind2;
int  sarglog;
int  isalog;
int  dfrom; 
int  duntil;
int  dataonly;
int  indexonly;
int  iprel;
int  userip;
int  langcode;
int  debug;
int  debugz;
int  debugm;
int  AuthfailReportLimit;
int  DeniedReportLimit;
int  DownloadReportLimit;
int  SiteUsersReportLimit;
int  DansGuardianReportLimit;
int  SquidGuardReportLimit;
int  UserReportLimit;
int  download_flag;
int  dotinuser;
int  realtime_refresh;
int  realtime_access_log_lines;
int  realt;
int  x, y;
int  rc;
long l1, l2;
float perc;

long long int twork;
long long int twork2;
long long int nocost;
float cost;

typedef struct
{ int list[ 24 ];
  int len;
} numlist;

DIR *dirp;
struct dirent *direntp;

int getnumlist( char *, numlist *, const int, const int );
