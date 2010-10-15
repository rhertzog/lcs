/*
 * AUTHOR: Pedro Lineu Orso                      orso@penguintech.com.br
 *                                                            1998, 2005
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

void getdata(char *, FILE *);
void datashow(char *);
void getlog();
void header();

char dat[128];
char tim[128];
char typ[128];
char ouser[MAXLEN]="";
char ourl[MAXLEN]="";

void
realtime(int argc, char *argv[])
{

   getlog();

}

void getlog()
{
   FILE *tmp, *fp, *fp_usr;
   char template1[255]="/var/tmp/sargtpl1.XXXXXX";
   char template2[255]="/var/tmp/sargtpl2.XXXXXX";
   char cmd[512];
   char buf[512];
   int  fd1,fd2,nreg;

   if(UserTabFile[0] != '\0') {
      if(debug) {
         sprintf(msg,"%s: %s",text[86],UserTabFile);
         debuga(msg);
      }
      if((fp_usr=fopen(UserTabFile,"r"))==NULL) {
        fprintf(stderr, "SARG: (realtime) %s: %s - %s\n",text[45],UserTabFile,strerror(errno));
        exit(1);
      }
      nreg = lseek(fileno(fp_usr), 0, SEEK_END);
      lseek(fileno(fp_usr), 0, 0);
      if((userfile=(char *) malloc(nreg+100))==NULL){
         fprintf(stderr, "SARG ERROR: %s",text[87]);
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

   fd1 = mkstemp(template1);
   fd2 = mkstemp(template2);

   if((fd1 == -1 ) || ((tmp = fdopen (fd1, "w+" )) == NULL)  ) {    /* failure, bail out */
       fprintf(stderr, "SARG: (realtime) mkstemp error - %s\n",strerror(errno));
       exit(1);
   }

   sprintf(cmd,"tail -%d %s",realtime_access_log_lines,AccessLog);
   fp = popen(cmd, "r");
   while(fgets(buf,sizeof(buf),fp) != NULL )
      getdata(buf,tmp);
   pclose(fp);
   fclose(tmp);

   sprintf(cmd,"sort -k 4,4 -k 5,5 -o %s %s",template2,template1);
   system(cmd);
   unlink(template1);
   datashow(template2);
}

void getdata(char *rec, FILE *ftmp)
{
   time_t tt;
   struct tm *t;

   getword3(dat,rec,' ');
   getword3(warea,rec,' ');
   while(strcmp(warea,"") == 0 && strlen(rec) > 0)
      getword3(warea,rec,' ');
   getword3(ip,rec,' ');
   getword3(warea,rec,' ');
   getword3(warea,rec,' ');
   getword3(typ,rec,' ');
   if(strncmp(typ,"CONNECT",7) == 0) {
      getword3(url,rec,' ');
      getword3(user,rec,' ');
    }else {
      getword3(url,rec,'/');
      getword3(url,rec,'/');
      getword3(url,rec,'/');
      getword3(user,rec,' ');
      getword3(user,rec,' ');
   }

   if(strncmp(user,"-",1) == 0 && strcmp(RealtimeUnauthRec,"ignore") == 0)
      return;

   tt=atoi(dat);
   t=localtime(&tt);
   if(strncmp(DateFormat,"u",1) == 0)
      strftime(tbuf, 127, "%Y-%m-%d %H:%M", t);
   else if(strncmp(DateFormat,"e",1) == 0)
      strftime(tbuf, 127, "%d-%m-%Y %H:%M", t);

   sprintf(warea,"%s %s %s %s %s\n",tbuf,ip,user,url,typ);
   fputs(warea,ftmp);
}

void datashow(char *tmp)
{
   FILE *fin;
   char buf[MAXLEN];

   if((fin=fopen(tmp,"r"))==NULL) {
      fprintf(stderr, "SARG: (realtime) open error %s - %s\n",tmp,strerror(errno));
      exit(1);
   }

   header();

   while(fgets(buf, MAXLEN, fin)) {
      buf[strlen(buf)-1]='\0';
      getword3(dat,buf,' ');
      getword3(tim,buf,' ');
      getword3(ip,buf,' ');
      getword3(user,buf,' ');
      if(strlen(dat) < 3 || strlen(user) < 1) continue;
      getword3(url,buf,' ');
      getword3(typ,buf,' ');
      if(strstr(RealtimeTypes,typ) == 0)
         continue;

      if(strcmp(ouser,user) == 0 && strcmp(ourl,url) == 0)
         continue;

      strcpy(u2,user);
      if(strcmp(Ip2Name,"yes") == 0)
         ip2name(u2);
      if(UserTabFile[0] != '\0') {
         sprintf(warea,":%s:",u2);
         if((str=(char *) strstr(userfile,warea)) != (char *) NULL ) {
            z1=0;
            str2=(char *) strstr(str+1,":");
            str2++;
            bzero(name, MAXLEN);
            while(str2[z1] != ':') {
               name[z1]=str2[z1];
               z1++;
            }
         } else strcpy(name,u2);
      } else strcpy(name,u2);

      if(dotinuser && strstr(name,"_")) {
         str2=(char *)subs(name,"_",".");
         strcpy(name,str2);
      }

      printf("<tr><td class=\"data\">%s %s</td><td class=\"data3\">%s</td><td class=\"data3\">%s</td><td class=\"data3\">%s</td><td class=\"data2\"><a href=\"http://%s\">%s</td></tr>\n",dat,tim,ip,name,typ,url,url);
      strcpy(ouser,user);
      strcpy(ourl,url);
   }

   puts("</table>\n</html>\n");
   fclose(fin);
   unlink(tmp);
   fflush(NULL);
   
}

void header()
{
   /* LCS */  
   puts("<?php\n");
   puts("\n");
   puts("include \"/var/www/lcs/includes/headerauth.inc.php\";\n");
   puts("include \"/var/www/Annu/includes/ldap.inc.php\";\n");
   puts("include \"/var/www/Annu/includes/ihm.inc.php\";\n");
   puts("\n");
   puts("list ($idpers,$login)= isauth();\n");
   puts("if ($idpers == \"0\") header(\"Location:$urlauth\");\n");
   puts("?>\n");

   puts("<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\"");
   puts(" \"http://www.w3.org/TR/html4/loose.dtd\">\n");
   puts("<html>\n");
   puts("<head>\n");
   if(realtime_refresh)
      printf("  <meta http-equiv=refresh content=\"%d\" url=\"sarg-php/sarg-realtime.php\"; charset=\"%s\">\n",realtime_refresh,CharSet);
   else
      printf("  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=%s\">\n",CharSet);
   css(stdout);
   puts("</head>\n");
   printf(buf,"<body style=\"font-family:%s;font-size:%s;background-color:%s;background-image:url(%s)\">\n",FontFace,TitleFontSize,BgColor,BgImage);
   puts("<center><table cellpadding=\"1\" cellspacing=\"1\">\n");
   printf("<tr><th class=\"title2\" colspan=\"10\">SARG %s</th></tr>\n",text[134]);
   printf("<tr><th class=\"text\" colspan=\"10\">%s: %d s</th></tr>\n",text[136],realtime_refresh);
   printf("<tr><th class=\"header3\">%s</th><th class=\"header3\">%s</th><th class=\"header3\">%s</th><th class=\"header3\">%s</th><th class=\"header\">%s</th></tr>\n",text[110],text[111],text[98],text[135],text[91]);
}
