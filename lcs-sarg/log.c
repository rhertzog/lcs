/*
 * AUTHOR: Pedro Lineu Orso                         pedro.orso@gmail.com
 *                                                            1998, 2006
 * SARG Squid Analysis Report Generator      http://sarg.sourceforge.net
 *
 * SARG donations:
 *      please look at http://sarg.sourceforge.net/donations.php
 * ---------------------------------------------------------------------
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111, USA.
 *
 */

#include "include/conf.h"

#define LEGACY_WRITE_USER
#define LEGACY_WRITE_DOWNLOAD
#define REPORT_EVERY_X_LINES 5000


char *userfile;
char *excludefile;
char *excludeuser;

char sz_Last_User[ MAXLEN ] = { 0 } ;
int bool_ShowReadStatistics ;

numlist weekdays = { { 0, 1, 2, 3, 4, 5, 6 }, 7 };
numlist hours = { { 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12,
             13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23 }, 24 };

void getusers(char *pwdfile, int debug);
void gethexclude(char *hexfile, int debug);
void getuexclude(char *uexfile, int debug);
void ttx(char *user);
int compar( const void *, const void * );

#define _FILE_OFFSET_BITS 64

int main(argc,argv)
   int argc;
   char *argv[];
{

   FILE *fp_in = NULL, *fp_ou = NULL, *fp_denied, *fp_usr, *fp_authfail, *fp_log;

   char sz_Download_Unsort[ 20000 ] ;
   FILE * fp_Download_Unsort = NULL ;
   FILE * fp_Write_User = NULL ;

   extern int optind;
   extern int optopt;
   extern char *optarg;

   char w[MAXLEN];
   char data[255]; 
   char elap[255];
   char none[255];
   char ip[MAXLEN];
   char msg[MAXLEN];
   char tam[255];
   char fun[255];
   char wuser[MAXLEN]; 
   char code[MAXLEN];
   char code2[MAXLEN];
   char smartfilter[MAXLEN];
   char dia[128]; 
   char wdata[128];
   char mes[30];
   char ano[30];
   char hora[30];
   char wtemp[MAXLEN];
   char wtemp2[255];
   char date[255];
   char arq[255];
   char arq_log[255];
   char warq[255][255];
   char hm[15], hmf[15], hmr[15], whm[15];
   int  chm=0;
   char uagent[MAXLEN];
   char hexclude[MAXLEN];
   char csort[MAXLEN]; 
   char tbuf[128];
   char tbuf2[128];
   char zip[20];
   char *str;
   char bufz[MAXLEN];
   char bufy[MAXLEN];
   int  common; 
   int  common_log=0;
   int  squid_log=0;
   int  ch;
   int  x, l;
   int  errflg=0;
   int  puser=0;
   int  fhost=0;
   int  dns=0;
   int  fuser=0;
   int  idata=0;
   int  narq=0;
   int  iarq=0;
   int  exstring=0;
   int  i0=0,i1=0,i2=0,i3=0,i4=0,i5=0,i6=0,i7=0,i8=0;
   long totregsl=0; 
   long totregsg=0;
   long totregsx=0;
   long totper=0;
   long int  max_elapsed=0;
   time_t tt;
   struct tm *t;
   unsigned long nreg=0;
   off_t recs1=0;
   unsigned long recs2=0;
   struct rlimit rl;
   int OutputNonZero = REPORT_EVERY_X_LINES ;

   BgImage[0]='\0';
   LogoImage[0]='\0';
   LogoText[0]='\0';
   PasswdFile[0]='\0';
   OutputEmail[0]='\0';
   Ip2Name[0]='\0';
   UserAgentLog[0]='\0';
   ExcludeHosts[0]='\0';
   ExcludeUsers[0]='\0';
   ConfigFile[0]='\0';
   code[0]='\0';
   LastLog[0]='\0';
   UserIp[0]='\0';
   ReportType[0]='\0';
   UserTabFile[0]='\0';
   BlockIt[0]='\0';
   ExternalCSSFile[0]='\0';
   SquidGuardLogFormat[0]='\0';
   SquidGuardLogAlternate[0]='\0';

   strcpy(AccessLog,"/usr/local/squid/var/logs/access.log");
   sprintf(ExcludeCodes,"%s/exclude_codes",SYSCONFDIR);
   strcpy(GraphDaysBytesBarColor,"orange");
   strcpy(BgColor,"#ffffff");
   strcpy(TxColor,"#000000");
   strcpy(TxBgColor,"lavender");
   strcpy(TiColor,"darkblue");
   strcpy(Width,"80");
   strcpy(Height,"45");
   strcpy(LogoTextColor,"#000000");
   strcpy(HeaderColor,"darkblue");
   strcpy(HeaderBgColor,"#dddddd");
   strcpy(LogoTextColor,"#006699");
   strcpy(FontSize,"9px");
   strcpy(TempDir,"/tmp");
   strcpy(OutputDir,"/var/www/html/squid-reports");
   strcpy(Ip2Name,"no");
   strcpy(DateFormat,"u");
   strcpy(OverwriteReport,"no");
   strcpy(RemoveTempFiles,"yes");
   strcpy(ReplaceIndex,"index.php");
   strcpy(Index,"yes");
   strcpy(RecordsWithoutUser,"ip");
   strcpy(UseComma,"no");
   strcpy(MailUtility,"mailx");
   strcpy(TopSitesNum,"100");
   strcpy(UserIp,"no");
   strcpy(TopuserSortField,"BYTES");
   strcpy(UserSortField,"BYTES");
   strcpy(TopuserSortOrder,"reverse");
   strcpy(UserSortOrder,"reverse");
   strcpy(TopsitesSortField,"CONNECT");
   strcpy(TopsitesSortType,"D");
   strcpy(LongUrl,"no");
   strcpy(language,"English");
   strcpy(FontFace,"Verdana,Tahoma,Arial");
   strcpy(datetimeby,"bytes");
   strcpy(CharSet,"ISO-8859-1");
   strcpy(Privacy,"no");
   strcpy(PrivacyString,"***.***.***.***");
   strcpy(PrivacyStringColor,"blue");
   strcpy(SuccessfulMsg,"yes");
   strcpy(TopUserFields,"NUM DATE_TIME USERID CONNECT BYTES %BYTES IN-CACHE-OUT USED_TIME MILISEC %TIME TOTAL AVERAGE");
   strcpy(UserReportFields,"CONNECT BYTES %BYTES IN-CACHE-OUT USED_TIME MILISEC %TIME TOTAL AVERAGE");
   strcpy(DataFileDelimiter,";");
   strcpy(DataFileFields,"user;date;time;url;connect;bytes;in_cache;out_cache;elapsed");
   strcpy(SiteUserTimeDateType,"table");
   strcpy(ShowReadStatistics,"yes");
   strcpy(IndexSortOrder,"D");
   strcpy(ShowSargInfo,"yes");
   strcpy(ShowSargLogo,"yes");
   strcpy(ParsedOutputLog,"no");
   strcpy(ParsedOutputLogCompress,"/bin/gzip");
   strcpy(DisplayedValues,"abbreviation");
   strcpy(HeaderFontSize,"9px");
   strcpy(TitleFontSize,"11px");
   strcpy(AuthUserFile,"/usr/local/sarg/passwd");
   strcpy(AuthName,"SARG, Restricted Access");
   strcpy(AuthType,"basic");
   strcpy(Require,"require user admin %u");
   strcpy(DownloadSuffix,"7z,ace,arj,avi,bat,bin,bz2,bzip,cab,com,cpio,dll,doc,dot,exe,gz,iso,lha,lzh,mdb,mov,mp3,mpeg,mpg,mso,nrg,ogg,ppt,rar,rtf,shs,src,sys,tar,tgz,vcd,vob,wma,wmv,zip");
   strcpy(Graphs,"yes");
   strcpy(Ulimit,"20000");
   strcpy(NtlmUserFormat,"domainname+username");
   strcpy(IndexTree,"file");
   strcpy(RealtimeTypes,"GET,PUT,CONNECT");
   strcpy(RealtimeUnauthRec,"ignore");
   strcpy(SquidguardIgnoreDate,"off");
   strcpy(DansguardianIgnoreDate,"off");
   strcpy(DataFileUrl,"ip");
   strcpy(MaxElapsed,"28800000");
   strcpy(BytesInSitesUsersReport,"no");

   dia[0]='\0';
   mes[0]='\0';
   ano[0]='\0';
   hora[0]='\0';
   tmp[0]='\0';
   tmp2[0]='\0';
   tmp3[0]='\0';
   wtemp[0]='\0';
   wtemp2[0]='\0';
   us[0]='\0';
   date[0]='\0';
   df[0]='\0';
   uagent[0]='\0';
   hexclude[0]='\0';
   addr[0]='\0';
   hm[0]='\0';
   hmf[0]='\0';
   site[0]='\0';
   outdir[0]='\0';
   elap[0]='\0';
   email[0]='\0';
   zip[0]='\0';
   UserInvalidChar[0]='\0';
   DataFile[0]='\0';
   SquidGuardConf[0]='\0';
   DansGuardianConf[0]='\0';

   excode=0;
   denied_count=0;
   download_count=0;
   authfail_count=0;
   dansguardian_count=0;
   squidguard_count=0;
   DeniedReportLimit=10;
   AuthfailReportLimit=10;
   DansGuardianReportLimit=10;
   SquidGuardReportLimit=10;
   DownloadReportLimit=50;
   UserReportLimit=0;
   debug=0;
   debugz=0;
   debugm=0;
   iprel=0;
   userip=0;
   color1=0;
   color2=0;
   color3=0;
   sarglog=0;
   isalog=0;
   dotinuser=0;
   realt=0;
   realtime_refresh=3;
   realtime_access_log_lines=1000;
   cost=0.01;
   nocost=50000000;

   bzero(IncludeUsers, MAXLEN);
   bzero(ExcludeString, MAXLEN);

   for(x=0; x<=254; x++) 
      warq[x][0]='\0';

   language_load(language);
   strcpy(Title,text[88]);

   while((ch = getopt(argc, argv, "a:b:c:d:e:f:g:u:l:L:o:s:t:w:hijmnprvxyz")) != -1){
      switch(ch)
      {
         case 'a':
            strcpy(addr,optarg);
            break;
         case 'b':
            strcpy(uagent,optarg);
            break;
         case 'c':
            strcpy(hexclude,optarg);
            break;
         case 'd':
            strcpy(date,optarg);
            strcpy(cduntil,optarg);
            getword(cdfrom,cduntil,'-');
            date_from(date, cdfrom, cduntil);
            dfrom=atoi(cdfrom);
            duntil=atoi(cduntil);
            break;
         case 'e':
            strcpy(email,optarg);
            break;
         case 'f':
            strcpy(ConfigFile,optarg);
            break;
         case 'g':
            strcpy(df,optarg);
            break;
   	 case 'h':
  	    usage(argv[0]);
            exit(0);
 	    break;
         case 'i':
            iprel++;
            break;
         case 'l':
            strcpy(warq[narq],optarg);
            narq++;
            break;
         case 'L':
            strcpy(SquidGuardLogAlternate,optarg);
            break;
         case 'm':
            debugm++;
            break;
         case 'n':
            dns++;
            break;
         case 'o':
            strcpy(outdir,optarg);
            break;
         case 'p':
            userip++;
            break;
         case 'r':
            realt++;
            break;
         case 's':
            strcpy(site,optarg);
            break;
         case 't':
            if(strstr(optarg,"-") == 0) {
              strcpy(hm,optarg);
              strcpy(hmf,optarg);
            } else {
               getword(hm,optarg,'-');
               strcpy(hmf,optarg);
            }
            if(strlen(hm) > 5) {
	       printf("SARG: time period must be MM or MM:SS. Exit.\n");
	       exit(1);
            }
            bzero(whm,15);
            if(strstr(hm,":") != 0) {
               getword(warea,hm,':');
               sprintf(whm,"%s%s",warea,hm);
               strcpy(hm,whm);
            }
            bzero(whm,15);
            if(strstr(hmf,":") != 0) {
               getword(warea,hmf,':');
               sprintf(whm,"%s%s",warea,hmf);
               strcpy(hmf,whm);
            }
            break;
         case 'u':
            strcpy(us,optarg);
            break;
         case 'v':
            version();
            break;
         case 'w':
            strcpy(tmp,optarg);
            break;
         case 'x':
            debug++;
            break;
         case 'y':
            langcode++;
            break;
         case 'z':
            debugz++;
            break;
         case ':':
	    fprintf(stderr, "Option -%c require an argument\n",optopt);
	    errflg++;
	    break;
         case '?':
            usage(argv[0]);
            exit(1);
            break;
      }

   }

   if (errflg) {
      usage(argv[0]);
      exit(2);
   }

   if(debug) debuga("Init");

   if(ConfigFile[0] == '\0') sprintf(ConfigFile,"%s/sarg.conf",SYSCONFDIR);
    else if(access(ConfigFile, R_OK) != 0) {
            sprintf(msg,"Cannot open config file: %s - %s",ConfigFile,strerror(errno));
            debuga(msg);
            exit(1);
         }
    
   if(access(ConfigFile, R_OK) == 0)
      getconf(debugm, ConfigFile, AccessLog, debug, BgColor, TxColor, TxBgColor, TiColor, LogoImage, LogoText, LogoTextColor, Width, Height, Title, BgImage, FontFace, HeaderColor, HeaderBgColor, FontSize, PasswdFile, TempDir, OutputDir, OutputEmail, Ip2Name, TopuserSortField, UserSortField, TopuserSortOrder, UserSortOrder, UserAgentLog, ExcludeHosts, DateFormat, ExcludeUsers, PerUserLimitFile, PerUserLimit, UserIp, MaxElapsed);
   
   if(realt) {
      realtime();
      exit(0);
   }

   if(strcmp(IndexTree,"file") == 0)
      strcpy(ImageFile,"../images");
   else
      strcpy(ImageFile,"../../../images");

   dataonly=0;
   if(DataFile[0] != '\0');
      dataonly++;
   
   str2=(char *)subs(TopUserFields,"%BYTES","SETYB");
   strcpy(TopUserFields,str2);
   
   str2=(char *)subs(UserReportFields,"%BYTES","SETYB");
   strcpy(UserReportFields,str2);

   if(!narq) {
      strcpy(warq[0],AccessLog);
      narq++;
   }

   if(strcmp(hexclude,"onvert") == 0 && strcmp(site,"plit") != 0) {
      convlog(warq[0], df, dfrom, duntil);
      exit(0);
   }
   
   if(strcmp(site,"plit") == 0) {
      splitlog(warq[0], df, dfrom, duntil, hexclude);
      exit(0);
   }

   if(ExcludeCodes[0] != '\0') {
      if((excludecode=(char *) malloc(1024))==NULL) {
         fprintf(stderr, "SARG: %s (1024):\n",text[59]);
         exit(1);
      }
      bzero(excludecode,1024);
      load_excludecodes();
   }

   if(access(PasswdFile, R_OK) == 0) {
      getusers(PasswdFile,debug);
      puser++;
   }

   if(hexclude[0] == '\0')
      strcpy(hexclude,ExcludeHosts);
   if(strlen(hexclude) > 0) {
      if(access(hexclude, R_OK) != 0) {
         sprintf(msg,"Cannot open exclude_hosts file: %s - %s",hexclude,strerror(errno));
         debuga(msg);
         exit(1);
      } else {
         gethexclude(hexclude,debug);
         fhost++;
      }
   }

   if(ReportType[0] == '\0')
      strcpy(ReportType,"topusers topsites users_sites sites_users date_time denied auth_failures site_user_time_date downloads");

   if(access(ExcludeUsers, R_OK) == 0) {
      getuexclude(ExcludeUsers,debug);
      fuser++;
   }

   indexonly=0;
   if(fuser) {
      if(strstr(excludeuser,"indexonly") != 0)
         indexonly++;
   }
   if(strcmp(ExcludeUsers,"indexonly") == 0) indexonly++;
   if(strcmp(Index,"only") == 0) indexonly++;

   if(dns) strcpy(Ip2Name,"yes");

   if(strcmp(UserIp,"yes") == 0) userip++;

   if(strlen(MaxElapsed)>1) max_elapsed=atol(MaxElapsed);

   if(strlen(outdir)<1) strcpy(outdir,OutputDir);
   strcat(outdir,"/");


   if(arq[0] == '\0') strcpy(arq,AccessLog);

   if(uagent[0] == '\0') strcpy(uagent,UserAgentLog);

   if(tmp[0] == '\0') strcpy(tmp,TempDir);
   else strcpy(TempDir,tmp);

   if(df[0] == '\0') strcpy(df,DateFormat);
   else strcpy(DateFormat,df);

   if(df[0] == '\0') {
      strcpy(df,"u");
      strcpy(DateFormat,"u");
   }

   if(strlen(email)<1 && strlen(OutputEmail)>0) strcpy(email,OutputEmail);

   strcpy(tmp2,tmp);

   if(strlen(email) > 0) {
      sprintf(wtemp2,"%s/sarg_tmp",tmp2);
      my_mkdir(wtemp2);
      strcat(tmp2,"/sarg_tmp");
      strcpy(outdir,tmp2);
      strcat(outdir,"/");
    }

   strcat(tmp2,"/sarg.log");

   sprintf(warea,"%s/sarg",tmp);
   if(access(warea, R_OK) == 0) {
      sprintf(tmp3,"rm -rf %s",warea);
      system(tmp3);
   }
   
   sprintf(tmp3,"%s/sarg",tmp);
   my_mkdir(tmp3);
   strcpy(tmp4,tmp3);
   strcpy(tmp5,tmp3);
   strcpy(tmp6,tmp3);
   strcat(tmp4,"/denied.log.unsort");
   strcat(tmp5,"/denied.log");
   strcat(tmp6,"/authfail.log.unsort");

   if(debug) {
      fprintf(stderr, "SARG: %s:\nSARG:\n",text[22]);
      fprintf(stderr, "SARG: %35s (-a) = %s\n",text[23],addr);
      fprintf(stderr, "SARG: %35s (-b) = %s\n",text[71],uagent);
      fprintf(stderr, "SARG: %35s (-c) = %s\n",text[69],hexclude);
      fprintf(stderr, "SARG: %35s (-d) = %s\n",text[24],date);
      fprintf(stderr, "SARG: %35s (-e) = %s\n",text[41],email);
      fprintf(stderr, "SARG: %35s (-f) = %s\n",text[70],ConfigFile);
      if(strcmp(df,"e") == 0)
         fprintf(stderr, "SARG: %35s (-g) = %s (dd/mm/yyyy)\n",text[25],text[26]);
      if(strcmp(df,"u") == 0)
         fprintf(stderr, "SARG: %35s (-g) = %s (mm/dd/yyyy)\n",text[25],text[27]);
      if(strcmp(df,"w") == 0)
         fprintf(stderr, "SARG: %35s (-g) = %s (yyyy/ww)\n",text[25],text[85]);
      if(iprel)
         fprintf(stderr, "SARG: %35s (-i) = %s\n",text[28],text[1]);
       else
         fprintf(stderr, "SARG: %35s (-i) = %s\n",text[28],text[2]);
      fprintf(stderr, "SARG: %35s (-l) = %s\n",text[37],arq);
      if(strcmp(Ip2Name,"yes") == 0)
         fprintf(stderr, "SARG: %35s (-n) = %s\n",text[65],text[1]);
       else
         fprintf(stderr, "SARG: %35s (-n) = %s\n",text[65],text[2]);
      fprintf(stderr, "SARG: %35s (-o) = %s\n",text[38],outdir);
      if(strcmp(UserIp,"yes") == 0)
         fprintf(stderr, "SARG: %35s (-p) = %s\n",text[29],text[1]);
       else
         fprintf(stderr, "SARG: %35s (-p) = %s\n",text[29],text[2]);
      fprintf(stderr, "SARG: %35s (-s) = %s\n",text[30],site);
      fprintf(stderr, "SARG: %35s (-t) = %s\n",text[31],hm);
      fprintf(stderr, "SARG: %35s (-u) = %s\n",text[32],us);
      fprintf(stderr, "SARG: %35s (-w) = %s\n",text[34],tmp);
      if(debug)
         fprintf(stderr, "SARG: %35s (-x) = %s\n",text[35],text[1]);
       else
         fprintf(stderr, "SARG: %35s (-x) = %s\n",text[35],text[2]);
      if(debugz)
         fprintf(stderr, "SARG: %35s (-z) = %s\n",text[36],text[1]);
       else
         fprintf(stderr, "SARG: %35s (-z) = %s\n",text[36],text[2]);
      fprintf(stderr, "SARG:\n");
   }

   if(debugm) {
      printf("%s:\nSARG:\n",text[22]);
      printf("%35s (-a) = %s\n",text[23],addr);
      printf("%35s (-b) = %s\n",text[71],uagent);
      printf("%35s (-c) = %s\n",text[69],hexclude);
      printf("%35s (-d) = %s\n",text[24],date);
      printf("%35s (-e) = %s\n",text[41],email);
      printf("%35s (-f) = %s\n",text[70],ConfigFile);
      if(strcmp(df,"e") == 0)
         printf("%35s (-g) = %s (dd/mm/yyyy)\n",text[25],text[26]);
      if(strcmp(df,"u") == 0)
         printf("%35s (-g) = %s (mm/dd/yyyy)\n",text[25],text[27]);
      if(strcmp(df,"w") == 0)
         printf("%35s (-g) = %s (yyyy/ww)\n",text[25],text[85]);
      if(iprel)
         printf("%35s (-i) = %s\n",text[28],text[1]);
       else
         printf("%35s (-i) = %s\n",text[28],text[2]);
      printf("%35s (-l) = %s\n",text[37],arq);
      if(strcmp(Ip2Name,"yes") == 0)
         printf("%35s (-n) = %s\n",text[65],text[1]);
       else
         printf("%35s (-n) = %s\n",text[65],text[2]);
      printf("%35s (-o) = %s\n",text[38],outdir);
      if(strcmp(UserIp,"yes") == 0)
         printf("%35s (-p) = %s\n",text[29],text[1]);
       else
         printf("%35s (-p) = %s\n",text[29],text[2]);
      printf("%35s (-s) = %s\n",text[30],site);
      printf("%35s (-t) = %s\n",text[31],hm);
      printf("%35s (-u) = %s\n",text[32],us);
      printf("%35s (-w) = %s\n",text[34],tmp);
      if(debug)
         printf("%35s (-x) = %s\n",text[35],text[1]);
       else
         printf("%35s (-x) = %s\n",text[35],text[2]);
      if(debugz)
         printf("%35s (-z) = %s\n",text[36],text[1]);
       else
         printf("%35s (-z) = %s\n",text[36],text[2]);
      printf("sarg %s: %s\n",text[73],VERSION);
      printf("Language=%s\n\n",text[3]);
   }

   if(debug){
      sprintf(msg,"sarg %s: %s",text[73],VERSION);
      debuga(msg);
   }
#if defined(RLIMIT_NOFILE)
   getrlimit (RLIMIT_NOFILE, &rl);
#else #if defined(RLIMIT_OFILE)
   getrlimit (RLIMIT_OFILE, &rl);
#endif
   l1 = rl.rlim_cur;
   l2 = rl.rlim_max;

   rl.rlim_cur = atol(Ulimit);
   rl.rlim_max = atol(Ulimit);
#if defined(RLIMIT_NOFILE)
   if(setrlimit (RLIMIT_NOFILE, &rl) == -1) {
#else #if defined(RLIMIT_OFILE)
   if(setrlimit (RLIMIT_OFILE, &rl) == -1) {
#endif
      sprintf(msg,"setrlimit error - %s\n",strerror(errno));
      debuga(msg);
   }

   if(debug) {
      sprintf(msg,"Maximum file descriptor: cur=%ld max=%ld, changed to cur=%ld max=%ld",l1,l2,rl.rlim_cur,rl.rlim_max);
      debuga(msg);
   }

   if(UserTabFile[0] != '\0') {
      if(debug) {
         sprintf(msg,"%s: %s",text[86],UserTabFile);
         debuga(msg);
      }
      if((fp_usr=fopen(UserTabFile,"r"))==NULL) {
        fprintf(stderr, "%s: (log) %s: %s - %s\n",argv[0],text[45],UserTabFile,strerror(errno));
        exit(1);
      }
      nreg = lseek(fileno(fp_usr), 0, SEEK_END);
      lseek(fileno(fp_usr), 0, 0);
      if((userfile=(char *) malloc(nreg+100))==NULL){
         fprintf(stderr, "%s ERROR: %s",argv[0],text[87]);
         exit(1);
      }
      bzero(userfile,nreg+100);
      strncat(userfile,":",1);
      z1=0;
      z2=1;
      while(fgets(buf,MAXLEN,fp_usr)!=NULL){
        buf[strlen(buf)-1]='\0';
        if(strstr(buf,"\r") != 0) buf[strlen(buf)-1]='\0';
        getword(bufy,buf,' ');
        for(z1=0; z1<=strlen(bufy); z1++) {
           userfile[z2]=bufy[z1];
           z2++;
        }
        strncat(userfile,":",1);
        for(z1=0; z1<=strlen(buf); z1++) {
           userfile[z2]=buf[z1];
           z2++;
        }
        strncat(userfile,":",1);
      }
      fclose(fp_usr);
   }

   sprintf ( sz_Download_Unsort , "%s/sarg/download.unsort", tmp);
   bool_ShowReadStatistics = ( strcmp(ShowReadStatistics,"yes") == 0 ) ;

   while(narq--) {
      strcpy(arq,warq[iarq]);
      iarq++;  

   strcpy(arqtt,arq);
   decomp(arq,zip,tmp);
   if(debug) {
      sprintf(msg, "%s: %s",text[7],arq);
      debuga(msg);
   }

#if defined(HAVE_FOPEN64)
   if((fp_in=(long)fopen64(arq,"r"))==NULL) {
#else
   if((fp_in=fopen(arq,"r"))==NULL) {
#endif
     fprintf(stderr, "%s: (log) %s: %s - %s\n",argv[0],text[8],arq,strerror(errno));
     exit(1);
   }
   fgets(bufz,sizeof(bufz),fp_in);
   if(!isalog && strncmp(bufz,"#Software: Mic",14) == 0) isalog++;

   if(strncmp(bufz,"*** SARG Log ***",16) == 0) {
      getword(val2,arqtt,'-');
      getword(val2,arqtt,'_');
      getword(val3,arqtt,'-');
      getword(val3,arqtt,'_');
      sprintf(period,"%s-%s",val2,val3);
      sarglog=1;
   } else lseek(fileno(fp_in), 0, 0);

   if(strcmp(ParsedOutputLog, "no") != 0 && !sarglog) {
      if(access(ParsedOutputLog,R_OK) != 0) {
         sprintf(csort,"%s",ParsedOutputLog);
         my_mkdir(csort);
      }
      sprintf(arq_log,"%s/sarg_temp.log",ParsedOutputLog);
      if((fp_log=fopen(arq_log,"w"))==NULL) {
        fprintf(stderr, "%s: (log) %s: %s - %s\n",argv[0],text[8],arq_log,strerror(errno));
        exit(1);
      }
      fputs("*** SARG Log ***\n",fp_log);
   }

   if(strstr(ReportType,"denied") != 0) {
      if((fp_denied=fopen(tmp4,"w"))==NULL) {
         fprintf(stderr, "%s: (log) %s: %s - %s\n",argv[0],text[45],tmp4,strerror(errno));
         exit(1);
      }
   }

   if(DataFile[0]=='\0') {
      if(strstr(ReportType,"denied") != 0 || strstr(ReportType,"auth_failures") != 0) {
         if((fp_authfail=fopen(tmp6,"w"))==NULL) {
            fprintf(stderr, "%s: (log) %s: %s - %s\n",argv[0],text[45],tmp6,strerror(errno));
            exit(1);
         }
      }
   }

   // pre-Read the file only if I have to show stats
   if(bool_ShowReadStatistics) {
      rewind(fp_in);
      recs1=0;
      recs2=0;
      while( fgets(bufz,sizeof(bufz),fp_in) != NULL ) recs1++;
      rewind(fp_in);

      printf("SARG: Records in file: %d, reading: %3.2f%%\r",recs1,(float) 0);
      fflush( stdout ) ;
   }
   
   while(fgets(bufz,sizeof(bufz),fp_in)!=NULL) {
	recs2++;
	if( bool_ShowReadStatistics && ! --OutputNonZero) {
           perc = recs2 * 100 ;
           perc = perc / recs1 ;
   	   printf("SARG: Records in file: %d, reading: %3.2f%%\r",recs1,perc);
           fflush (stdout);
           OutputNonZero = REPORT_EVERY_X_LINES ;
        }
        if(strlen(bufz) > MAXLEN-1) continue;
        if(!bufz[0]) continue;
        if(strstr(bufz,"HTTP/0.0") != 0) continue;
        if(strstr(bufz,"logfile turned over") != 0) continue;
        if(bufz[0] == ' ') continue;
        if(strlen(bufz) < 58) continue;

        // Record only hours usage which is required
        tt = (time_t) strtoul( bufz, NULL, 10 );
        t = localtime( &tt );

        if( bsearch( &( t -> tm_wday ), weekdays.list, weekdays.len,
                                        sizeof( int ), compar ) == NULL )
          continue;

        if( bsearch( &( t -> tm_hour ), hours.list, hours.len,
                                        sizeof( int ), compar ) == NULL )
          continue;

        // exclude_string
        exstring=0;
        if(strlen(ExcludeString) > 0) {
           strcpy(warea,bufz);
           strcpy(html,ExcludeString);
           while(strstr(html,":") != 0) {
              getword(val1,html,':');
              if((str=(char *) strstr(warea,val1)) != (char *) NULL )
                 exstring++;
           }
           if((str=(char *) strstr(warea,html)) != (char *) NULL )
                 exstring++;
        }
        if(exstring) continue;

        strcpy(bufy,bufz);
        if ((str = strchr(bufz, '\n')) != NULL)
           *str = '\0';          /* strip \n */       

        totregsl++;
        common=0;
        if(debugm)
       	   printf("BUF=%s\n",bufz);

        if(!sarglog && !isalog) {
           getword(data,bufz,' ');
           if((str=(char *) strstr(data, ".")) != (char *) NULL ) {
              if((str=(char *) strstr(str+1, ".")) != (char *) NULL ) {
                 strcpy(ip,data);
       	         strcpy(elap,"0");
	         getword(none,bufz,' ');
	         getword(user,bufz,' ');
	         getword(data,bufz,']');
	         getword(fun,bufz,'"');
	         getword(fun,bufz,' ');
	         getword(url,bufz,' ');
                 getword(code2,bufz,' ');
                 getword(code2,bufz,' ');
                 getword(tam,bufz,' ');
                 if((str=(char *) strstr(bufz, " ")) != (char *) NULL )
                    getword(code,bufz,' ');
                 else strcpy(code,bufz);

                 if ((str = strchr(code, ':')) != NULL)
                    *str = '/'; 

	         if(strcmp(tam,"\0") == 0)
	            strcpy(tam,"0");

	         common++;
	         common_log=1;
	      }
	   }

	   if(!common) {
	      getword(elap,bufz,' ');
	      while(strcmp(elap,"") == 0 && strlen(bufz) > 0)
	         getword(elap,bufz,' ');
              if(strlen(elap) < 1) continue;
	      getword(ip,bufz,' ');
	      getword(code,bufz,' ');
	      getword(tam,bufz,' ');
	      getword(fun,bufz,' ');
	      getword(url,bufz,' ');
              while (strstr(bufz,"%20") != 0) {
                 getword(warea,bufz,' ');
                 strcat(url,warea);
              }
	      getword(user,bufz,' ');
	      squid_log=1;
	   }
        } else if(!isalog) {
	   getword(data,bufz,' ');
	   getword(hora,bufz,' ');
	   getword(user,bufz,' ');
	   getword(ip,bufz,' ');
	   getword(url,bufz,' ');
	   getword(tam,bufz,' ');
	   getword(code,bufz,' ');
	   getword(elap,bufz,' ');
	   getword(smartfilter,bufz,' ');
        } else if(isalog) {
           if(!i0) {
              getword(val1,bufz,' ');
              while(strstr(bufz,"\t") != 0) {
                 getword(val1,bufz,'\t');
                 i0++;
                 if(strcmp(val1,"c-ip") == 0) i1=i0;
                 if(strcmp(val1,"cs-username") == 0) i2=i0;
                 if(strcmp(val1,"date") == 0) i3=i0;
                 if(strcmp(val1,"time") == 0) i4=i0;
                 if(strcmp(val1,"time-taken") == 0) i5=i0;
                 if(strcmp(val1,"sc-bytes") == 0) i6=i0;
                 if(strcmp(val1,"cs-uri") == 0) i7=i0;
                 if(strcmp(val1,"sc-status") == 0) i8=i0;
              }
           }
           fgets(bufz,sizeof(bufz),fp_in);
           strcpy(val1,bufz);
           for(x=0; x<=i1-1; x++) getword3(ip,val1,'\t');
           strcpy(val1,bufz);
           for(x=0; x<=i2-1; x++) getword3(user,val1,'\t');
           strcpy(val1,bufz);
           for(x=0; x<=i3-1; x++) getword3(data,val1,'\t');
           strcpy(val1,bufz);
           for(x=0; x<=i4-1; x++) getword3(hora,val1,'\t');
           strcpy(val1,bufz);
           for(x=0; x<=i5-1; x++) getword3(elap,val1,'\t');
           strcpy(val1,bufz);
           for(x=0; x<=i6-1; x++) getword3(tam,val1,'\t');
           strcpy(val1,bufz);
           for(x=0; x<=i7-1; x++) getword3(url,val1,'\t');
           strcpy(val1,bufz);
           for(x=0; x<=i8-1; x++) getword3(code,val1,'\t');
           
           if(strcmp(code,"401") == 0 || strcmp(code,"403") == 0 || strcmp(code,"407") == 0) {
              sprintf(val1,"DENIED/%s",code);
              strcpy(code,val1);
           }
           getword(ano,data,'-');
           getword(mes,data,'-');
           strcpy(dia,data);
           conv_month_name(mes);
           sprintf(data," %s/%s/%s:%s",dia,mes,ano,hora);
        }

        if(strlen(user) > 150) {
           totregsx++;
           continue;
        }

	// include_users
	if(strlen(IncludeUsers) > 0) {
           sprintf(val1,":%s:",user);
           if((str=(char *) strstr(IncludeUsers,val1)) == (char *) NULL )
              continue;
	}

        if(excode) {
           if(vercode(code)) {
              totregsx++;
              continue;
           }
        }

        if(testvaliduserchar(user))
	   continue;

        while(strstr(user,"%5c") != 0 || strstr(user,"%20") != 0) {
           getword(w,user,'%');
           strcpy(wuser,user+2);
           sprintf(user,"%s.%s",w,wuser);
        }

	str=user;
	for(z1=0; z1<strlen(str); z1++) {
	   if(isalnum(str[z1]) || ispunct(str[z1])) {
	   if(str[z1]=='.') dotinuser++;
	   if(str[z1]=='?' || str[z1]=='.' || str[z1]==':' || str[z1]=='/' || str[z1]=='\\')
	      str[z1]='_';
	   }
	}

        strlow(user);
        if(strncmp(NtlmUserFormat,"user",4) == 0) {
           if(strstr(user,"_") != 0)
              getword(warea,user,'_');  
           if(strstr(user,"+") != 0)
              getword(warea,user,'+');  
        }

        if(strstr(ReportType,"denied") != 0)
           strcpy(urly,url);

        if(strlen(DownloadSuffix)) {
           suffix[0]='\0';
           download_flag=0;
           if(strncmp(url+strlen(url)-4,".",1) == 0)
              strcpy(suffix,url+strlen(url)-3);
           else strcpy(suffix,url+strlen(url)-4);
           if(strstr(DownloadSuffix,suffix) != 0) {
              strcpy(download_url,url);
              download_flag=1;
              download_count++;
           }
        }
         
        if (strchr(url,'/')) {
           getword(w,url,'/');
           getword(w,url,'/');
          if (!strchr(url,'/')) {
              totregsx++;
              continue;
          }
        }

        if(strcmp(LongUrl,"no") == 0) {
           getword(w,url,'/');
           strcpy(url,w);
           if(strlen(url) > 512 && strstr(url,"%") != 0) {
              getword(w,url,'%');
              strcpy(url,w);
           }
        }

        if(!sarglog) {
           if(!common && !isalog) {
              tt=atoi(data);
              t=localtime(&tt);

              strftime(tbuf2, 127, "%H%M", t);
              if(strncmp(df,"u",1) == 0)
	         strftime(tbuf, 127, "%Y%b%d", t);
              if(strncmp(df,"e",1) == 0)
	         strftime(tbuf, 127, "%d%b%Y", t);
              if(strncmp(df,"w",1) == 0) {
                 strcpy(IndexTree,"file");
	         strftime(tbuf, 127, "%Y.%U", t);
              }

              strftime(dia, 127, "%d/%m/%Y", t);
              strftime(wdata, 127, "%Y%m%d", t);
          
              idata=atoi(wdata);

              if(strncmp(df,"u",1)==0)
                 strftime(dia, 127, "%m/%d/%Y", t);
              sprintf(hora,"%02d:%02d:%02d",t->tm_hour,t->tm_min,t->tm_sec);
	    } else {
	      strcpy(wtemp,data+1);
	      getword(data,wtemp,':');
	      getword(hora,wtemp,' ');
	      getword(dia,data,'/');
	      getword(mes,data,'/');
	      getword(ano,data,'/');

              if(strcmp(df,"u") == 0)
	         sprintf(tbuf,"%s%s%s",ano,mes,dia);
              if(strcmp(df,"e") == 0)
	         sprintf(tbuf,"%s%s%s",dia,mes,ano);
	      builddia(dia,mes,ano,df,wdata);
              idata=atoi(wdata);
	   }
        } else {
	   getword(mes,data,'/');
	   getword(dia,data,'/');
	   strcpy(ano,data);
        }

        if(debugm)
           printf("DATE=%s IDATA=%d DFROM=%d DUNTIL=%d\n",date,idata,dfrom,duntil);

        l=1;
        if(strlen(us)>0){
  	   if(strcmp(user,us)==0)
	      l=1;else l=0;
 	}
           
        if(l){
           if(strlen(addr)>0){
  	      if(strcmp(addr,ip)==0)
	         l=1;else l=0;
 	   }
           if(fhost) {
//              l=vhexclude(excludefile,ip);
              l=vhexclude(excludefile,url);
              if(!l)
	         totregsx++;
           }
        }

        if(l){
           if(strlen(date) > 0){
              if(idata >= dfrom && idata <= duntil)
	         l=1;else l=0;
 	   }
        }
        if(l){
           if(strlen(hm)>0) {
              strcpy(whm,hora);
              bzero(hmr,15);
              chm++;
              while(chm) {
                 getword(warea,whm,':');
                 strncat(hmr,warea,2);
                 chm--;
              }
              strncat(hmr,whm,2);

  	      if(atoi(hmr) >= atoi(hm) && atoi(hmr) <= atoi(hmf))
	         l=1;else l=0;
 	   }
        }
        if(l){
           if(strlen(site)>0){
  	      if(strstr(url,site)!=0)
	         l=1;else l=0;
 	   }
        }

        if(userip)
           strcpy(user,ip);

        if(strcmp(user,"-") == 0 || strcmp(user," ") == 0 || strcmp(user,"") == 0) {
           if(strcmp(RecordsWithoutUser,"ip") == 0)
	      strcpy(user,ip);
           if(strcmp(RecordsWithoutUser,"ignore") == 0)
	      continue;
           if(strcmp(RecordsWithoutUser,"everybody") == 0)
	      strcpy(user,"everybody");
        }

        if(dotinuser) {
           str2=(char *)subs(user,"_",".");
           strcpy(user,str2);
           dotinuser=0;
        }

        if(puser) {
           sprintf(wuser,":%s:",user);
           if(strstr(userfile, wuser) == 0)
              continue;
        }
        
        if(l) {
           if(fuser) {
              l=vuexclude(excludeuser,user);
              if(!l)
	         totregsx++;
           }
        }

        if(l) {
           if(userip)
              fixip(user);
        }

        if(l&&max_elapsed) {
           if(atol(elap)>max_elapsed) {
              elap[0]='0';
              elap[1]='\0';
	    }
        }

        if(l) {
	   if(strcmp(user,"-") !=0 && strlen(url) > 0 && strcmp(user," ") !=0 && strcmp(user,"") !=0 && strcmp(user,":") !=0){
              if((str=(char *) strstr(bufz, "[SmartFilter:")) != (char *) NULL ) {
                 str[strlen(str)-1]='\0';
                 sprintf(smartfilter,"\"%s\"",str+1);
              } else sprintf(smartfilter,"\"\"");

              sprintf(bufz, "%s %s %s %s %s %s %s %s %s\n",dia,hora,user,ip,url,tam,code,elap,smartfilter);

#ifdef LEGACY_WRITE_USER
              sprintf(tmp3,"%s/sarg/%s.unsort",tmp,user);
#if defined(HAVE_FOPEN64)
              if((fp_ou=fopen64(tmp3,"a"))==NULL) {
#else
              if((fp_ou=fopen(tmp3,"a"))==NULL) {
#endif
                fprintf(stderr, "%s: (zzzlog) %s: %s - %s\n",argv[0],text[9],tmp3,strerror(errno));
                exit(1);
              }
              fputs(bufz,fp_ou);

#else
                  if ( strcmp ( user , sz_Last_User ) != 0 ) {
                        if ( fp_Write_User )
                                fclose( fp_Write_User ) ;
                        sprintf (tmp3, "%s/sarg/%s.unsort", tmp, user);

#if defined(HAVE_FOPEN64)
                        if ((fp_Write_User = fopen64 (tmp3, "a")) == NULL) {
#else
                        if ((fp_Write_User = fopen (tmp3, "a")) == NULL) {
#endif
                              fprintf (stderr, "%s: (log) %s: %s - %s\n", argv[0], text[9], tmp3, strerror(errno));
                                exit (1);
                        }
                        strcpy( sz_Last_User , user ) ;
                  }
                  fputs (bufz, fp_Write_User);
#endif

              if(strcmp(ParsedOutputLog, "no") != 0 && !sarglog)
                 fputs(bufz,fp_log);

#ifdef LEGACY_WRITE_USER
              fclose(fp_ou);
#endif
	      totregsg++;

              if(download_flag) {
                 sprintf(bufz, "%s %s %s %s %s\n",dia,hora,user,ip,download_url);

#ifdef LEGACY_WRITE_DOWNLOAD
                 sprintf(tmp3,"%s/sarg/download.unsort",tmp);
#if defined(HAVE_FOPEN64)
                 if((fp_ou=fopen64(tmp3,"a"))==NULL) {
#else
                 if((fp_ou=fopen(tmp3,"a"))==NULL) {
#endif
                    fprintf(stderr, "%s: (log) %s: %s - %s\n",argv[0],text[9],tmp3,strerror(errno));
                    exit(1);
                 }
                 fputs(bufz,fp_ou);
                 fclose(fp_ou);
#else
                 if ( ! fp_Download_Unsort ) {
#if defined(HAVE_FOPEN64)
                      if ((fp_Download_Unsort = fopen64 ( sz_Download_Unsort, "a")) == NULL) {
#else
                      if ((fp_Download_Unsort = fopen (sz_Download_Unsort, "a")) == NULL) {
#endif
                          fprintf (stderr, "%s: (log) %s: %s - %s\n", argv[0], text[9], tmp3, strerror(errno));
                          exit (1);
                      }
                 }
                 fputs (bufz, fp_Download_Unsort);
#endif
              }

              if(strstr(ReportType,"denied") != 0 || strstr(ReportType,"auth_failures") != 0) {
                 if(strstr(code,"DENIED/403") != 0) {
                    sprintf(bufz, "%s %s %s %s %s\n",dia,hora,user,ip,urly);
                    fputs(bufz,fp_denied);
                    denied_count++;
                 }
                 if(strstr(code,"DENIED/401") != 0 || strstr(code,"DENIED/407") != 0) {
                    sprintf(bufz, "%s %s %s %s %s\n",dia,hora,user,ip,urly);
                    if(fp_authfail)
                       fputs(bufz,fp_authfail);
                    authfail_count++;
                 }
              }
       
              if(!totper && !sarglog){
	         totper++;
                 sprintf(period,"%s-",tbuf);
                 sprintf(per_hour,"%s-",tbuf2);
                 if(strlen(date)>0)
                    fixper(tbuf, period, cduntil);
                 if(debugz){
                    debugaz("tbuf",tbuf);
                    debugaz("period",period);
                 }
	      }
           }

           if(debugm){
              printf("IP=\t%s\n",ip);
              printf("USER=\t%s\n",user);
              printf("ELAP=\t%s\n",elap);
              printf("DATE=\t%s\n",dia);
              printf("TIME=\t%s\n",hora);
              printf("FUNC=\t%s\n",fun);
              printf("URL=\t%s\n",url);
              printf("CODE=\t%s\n",code);
              printf("LEN=\t%s\n",tam);
	   }
        }
      }
      if( bool_ShowReadStatistics )
        printf("SARG: Records in file: %d, reading: %3.2f%%\n",recs1, (float) 100 );
   }

   if ( fp_Download_Unsort ) 
     fclose (fp_Download_Unsort);

   if (fp_Write_User) 
     fclose (fp_Write_User);

   if(debug) {
      sprintf(msg, "   %s: %ld, %s: %ld, %s: %ld",text[10],totregsl,text[11],totregsg,text[68],totregsx);
      debuga(msg);
  
      if((common_log) && (squid_log))
         debuga(text[12]);
  
      if((common_log) && (!squid_log)) 
         debuga(text[13]);
 
      if((!common_log) && (squid_log))
         debuga(text[14]);

      if(sarglog)
         debuga(text[124]);
 
      if((!common_log) && (!squid_log) && (!sarglog) && (!isalog)) {
         if(!totregsg) {
            fprintf(stderr, "SARG: %s\n",text[16]);
            fprintf(stderr, "SARG: %s\n",text[21]);
	 } else fprintf(stderr, "SARG: %s\n",text[15]);
         bzero(msg,sizeof(msg));
         fclose(fp_in);
//         fclose(fp_ou);
         if(fp_denied)
            fclose(fp_denied);
         if(fp_authfail)
            fclose(fp_authfail);
         if(tmp4)
            unlink(tmp4);
         if(tmp6)
            unlink(tmp6);
         unlink(tmp3);
         exit(0);
      }
   }
 
   if(!totregsg){
      fprintf(stderr, "SARG: %s\n",text[16]);
      fprintf(stderr, "SARG: %s\n",text[21]);
      fclose(fp_in);
//      fclose(fp_ou);
      if(fp_denied)
         fclose(fp_denied);
      if(fp_authfail)
         fclose(fp_authfail);
      exit(0);
   }

   if(date[0] == '\0' && !sarglog) {
      strcat(period,tbuf);
      strcat(per_hour,tbuf2);
   }

   if(debugz){
      debugaz("data",dia);
      debugaz("tbuf",tbuf);
      debugaz("period",period);
   }

   if(debug){
     sprintf(msg, "%s: %s",text[17],period);
     debuga(msg);
   }

   fclose(fp_in);
//   fclose(fp_ou);
   if(fp_denied)
      fclose(fp_denied);
   if(fp_authfail)
      fclose(fp_authfail);

   if(strcmp(ParsedOutputLog, "no") != 0 && !sarglog) {
      fclose(fp_log); 
      strcpy(val1,period);
      getword(val2,val1,'-');
      getword(val3,per_hour,'-');
      sprintf(val4,"%s/sarg-%s_%s-%s_%s.log",ParsedOutputLog,val2,val3,val1,per_hour);
      rename(arq_log,val4);
      strcpy(arq_log,val4);

      if(strcmp(ParsedOutputLogCompress,"nocompress") != 0) {
         sprintf(val1,"%s %s",ParsedOutputLogCompress,arq_log);
         system(val1);
      }

      if(debug) {
         sprintf(msg,"%s %s",text[123],arq_log);
         debuga(msg);
      }
   }

   if(strstr(ReportType,"denied") != 0) {
      if(debug) {
         sprintf(msg,"%s %s",text[54],tmp4);
         debuga(msg);
      }
      sprintf(csort,"sort -T %s -k 3,3 -k 5,5 -o '%s' '%s'",tmp,tmp5,tmp4);
      system(csort);
      unlink(tmp4);
   }
 
   sort_users_log(tmp, debug);

   report_gen:
   if(strlen(DataFile) > 0)
      data_file(tmp);
   else
      gerarel();

   unlink(tmp2);
   if(strstr(ReportType,"denied") != 0)
      unlink(tmp5);
 
   if((strlen(zip) > 0 && strcmp(zip,"zcat") !=0)) {
      recomp(arq, zip); }
//   else  unlink(arq);

   if(debug)
      debuga(text[21]);

   sprintf(csort,"rm -rf %s",tmp);
   system(csort);

   if(excludecode)
      free(excludecode);
   if(userfile)
      free(userfile);
   if(excludefile)
      free(excludefile);
   if(excludeuser)
      free(excludeuser);

   exit(0);

}


void getusers(char *pwdfile, int debug)
{

   FILE *fp_usr;
   char buf[255];
   char Msg[255];
   char user[255];
   unsigned long int nreg=0;

   if(debug) {
      sprintf(Msg,"%s: %s",text[60],pwdfile);
      debuga(Msg);
   }

   if ((fp_usr = fopen(pwdfile, "r")) == NULL) {
      fprintf(stderr, "SARG: (getusers) %s: %s - %s\n",text[45],pwdfile,strerror(errno));
      exit(1);
   }

   nreg = lseek(fileno(fp_usr), (off_t)0, SEEK_END);
   nreg = nreg+5000; 
   lseek(fileno(fp_usr), (off_t)0, 0);

   if((userfile=(char *) malloc(nreg))==NULL){
      fprintf(stderr, "SARG: %s (%ld):\n",text[59],nreg);
      exit(1);
   }

   bzero(userfile,nreg);
   sprintf(userfile,":");

   while(fgets(buf,255,fp_usr)!=NULL){
     getword(user,buf,':');
     strncat(userfile,user,strlen(user));
     strncat(userfile,":",1);
   }

   fclose(fp_usr);

   return;
}


void gethexclude(char *hexfile, int debug)
{

   FILE *fp_ex;
   char buf[255];
   char Msg[255];
   unsigned long int nreg=0;

   if(debug) {
      sprintf(Msg,"%s: %s",text[67],hexfile);
      debuga(Msg);
   }

   if ((fp_ex = fopen(hexfile, "r")) == NULL) {
      fprintf(stderr, "SARG: (gethexclude) %s: %s - %s\n",text[45],hexfile,strerror(errno));
      exit(1);
   }

   nreg = lseek(fileno(fp_ex), (off_t)0, SEEK_END);
   lseek(fileno(fp_ex), (off_t)0, 0);

   if((excludefile=(char *) malloc(nreg+11))==NULL){
      fprintf(stderr, "SARG: %s (%ld):\n",text[59],nreg);
      exit(1);
   }

   bzero(excludefile,nreg+11);

   while(fgets(buf,255,fp_ex)!=NULL){
     if(strstr(buf,"#") != 0)
        continue;
     buf[strlen(buf)-1]='\0';
     strcat(excludefile,buf);
     strcat(excludefile," ");
   }

   strcat(excludefile,"*END* ");

   fclose(fp_ex);

   return;
}


void getuexclude(char *uexfile, int debug)
{

   FILE *fp_ex;
   char buf[255];
   char Msg[255];
   unsigned long int nreg=0;

   if(debug) {
      sprintf(Msg,"%s: %s",text[67],uexfile);
      debuga(Msg);
   }

   if ((fp_ex = fopen(uexfile, "r")) == NULL) {
      fprintf(stderr, "SARG: (gethexclude) %s: %s - %s\n",text[45],uexfile,strerror(errno));
      exit(1);
   }

   nreg = lseek(fileno(fp_ex), (off_t)0, SEEK_END);
   lseek(fileno(fp_ex), (off_t)0, 0);

   if((excludeuser=(char *) malloc(nreg+11))==NULL){
      fprintf(stderr, "SARG: %s (%ld):\n",text[59],nreg);
      exit(1);
   }

   bzero(excludeuser,nreg+11);

   while(fgets(buf,255,fp_ex)!=NULL){
     if(strstr(buf,"#") != 0)
        continue;
     buf[strlen(buf)-1]='\0';
     strcat(excludeuser,buf);
     strcat(excludeuser," ");
   }

   strcat(excludeuser,"*END* ");

   fclose(fp_ex);

   return;
}
