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

void dansguardian_report()
{

   FILE *fp_in = NULL, *fp_ou = NULL;
      
   char url[MAXLEN];
   char html[MAXLEN];
   char html2[MAXLEN];
   char dansguardian_in[MAXLEN];
   char dansguardian_ou[MAXLEN];
   char per[MAXLEN];
   char report[MAXLEN];
   char period[100];
   char ip[MAXLEN];
   char rule[255];
   char oip[MAXLEN];
   char user[MAXLEN];
   char ouser[MAXLEN];
   char date[15];
   char date2[15];
   char hour[15];
   char ftime[128];
   char *str;
   int  z=0;
   int  count=0;

   ouser[0]='\0';

   sprintf(dansguardian_in,"%s/dansguardian.log",tmp);
   if(!dansguardian_count) {
      unlink(dansguardian_in);
      return;
   }

   sprintf(per,"%s/period",dirname);
   sprintf(report,"%s/dansguardian.php",dirname);

   if ((fp_in = fopen(per, "r")) == 0) {
      fprintf(stderr, "SARG: (dansguardian_report) %s: %s\n",text[45],per);
      exit(1);
   }

   fgets(period,sizeof(period),fp_in);
   fclose(fp_in);

   if((fp_in=fopen(dansguardian_in,"r"))==NULL) {
     fprintf(stderr, "SARG: (dansguardian_report) %s: %s\n",text[8],dansguardian_in);
     exit(1);
   }

   if((fp_ou=fopen(report,"w"))==NULL) {
     fprintf(stderr, "SARG: (dansguardian_report) %s: %s\n",text[8],report);
     exit(1);
   }
      /* LCS */
      fputs("<?php\n",fp_ou);
      fputs("\n",fp_ou);
      fputs("include \"/var/www/lcs/includes/headerauth.inc.php\";\n",fp_ou);
      fputs("include \"/var/www/Annu/includes/ldap.inc.php\";\n",fp_ou);
      fputs("include \"/var/www/Annu/includes/ihm.inc.php\";\n",fp_ou);
      fputs("\n",fp_ou);
      fputs("list ($idpers,$login)= isauth();\n",fp_ou);
      fputs("if ($idpers == \"0\") header(\"Location:$urlauth\");\n",fp_ou);
      fputs("?>\n",fp_ou);

   fprintf(fp_ou, "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">\n<html>\n<head>\n  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=%s\">\n",CharSet);
   css(fp_ou);
   fputs("</head>\n",fp_ou);
   if(strlen(FontFace) > 0) fprintf(fp_ou,"<font face=%s>\n",FontFace);
   fprintf(fp_ou,"<body bgcolor=%s text=%s background='%s'>\n",BgColor,TxColor,BgImage);
   if(strlen(LogoImage) > 0) fprintf(fp_ou, "<center><table cellpadding=\"0\" cellspacing=\"0\">\n<tr><th class=\"logo\"><img src='%s' border=0 align=absmiddle width=%s height=%s>&nbsp;%s</th></tr>\n<tr><td height=\"5\"></td></tr>\n</table>\n",LogoImage,Width,Height,LogoText);
   
   if(strcmp(IndexTree,"date") == 0)
      show_sarg(fp_ou, "../../..");
   else
      show_sarg(fp_ou,"..");
   fputs("<center><table cellpadding=0 cellspacing=0>\n",fp_ou);
   sprintf(url,"<tr><th class=\"title\">%s</th></tr>\n",Title);
   fputs(url,fp_ou);

   sprintf(url,"<tr><td class=\"header\">%s: %s</td></tr>\n",text[89],period);
   fputs(url,fp_ou);
   sprintf(url,"<tr><th class=\"header3\">%s</th></tr>\n",text[128]);
   fputs(url,fp_ou);
   fputs("</table></center>\n",fp_ou);

   fputs("<center><table cellpadding=1 cellspacing=2>\n",fp_ou);
   fputs("<tr><td></td></tr>\n",fp_ou);
   sprintf(url,"<tr><th class=\"header\">%s</th><th class=\"header\">%s</th><th class=\"header\">%s</th><th class=\"header\">%s</th><th class=\"header\">%s</th></tr>\n",text[98],text[111],text[110],text[91],text[129]);
   fputs(url,fp_ou);

   while(fgets(buf,sizeof(buf),fp_in)!=NULL) {
      getword(user,buf,' ');
      getword(date2,buf,' ');
      getword(hour,buf,' ');
      getword(ip,buf,' ');
      getword(url,buf,' ');
      getword(rule,buf,'\n');

      if(strcmp(UserIp,"yes") == 0)
           strcpy(user,ip);

      bzero(date, 15);
      if(strncmp(df,"u",1) != 0) {
         strncpy(date,date2+6,2);
         strcat(date,"/");
         strncat(date,date2+4,2);
         strcat(date,"/");
         strncat(date,date2,4);
      } else {
         strncpy(date,date2+4,2);
         strcat(date,"/");
         strncat(date,date2+6,2);
         strcat(date,"/");
         strncat(date,date2,4);
      }

      if((str=(char *) strstr(user, "_")) != (char *) NULL ) {
         if((str=(char *) strstr(str+1, "_")) != (char *) NULL )
            fixip(user);
      }

      if(strcmp(Ip2Name,"yes") == 0) 
         ip2name(ip);

      if(!z) {
         strcpy(ouser,user);
         strcpy(oip,ip);
         z++;
      } else {
         if(strcmp(ouser,user) == 0)
            user[0]='\0';
         if(user[0] != '\0')
            strcpy(ouser,user);
         if(strcmp(oip,ip) == 0) 
            ip[0]='\0';
         if(ip[0] != '\0')
            strcpy(oip,ip);
      }

      if(UserTabFile[0] != '\0') {
         sprintf(warea,":%s:",user);
         if((str=(char *) strstr(userfile,warea)) != (char *) NULL ) {
            z1=0;
            str2=(char *) strstr(str+1,":");
            str2++;
            bzero(name, MAXLEN);
            while(str2[z1] != ':') {
               name[z1]=str2[z1];
               z1++;
            }
         } else strcpy(name,user);
      } else strcpy(name,user);

      if(dotinuser && strstr(name,"_")) {
         str2=(char *)subs(name,"_",".");
         strcpy(name,str2);
      }

     if(DansGuardianReportLimit) {
         if(strcmp(ouser2,name) == 0) {
            count++;
         } else {
            count=1;
            strcpy(ouser2,name);
         }
         if(count >= DansGuardianReportLimit)
            continue;
      }

      sprintf(html2,"<tr><td class=\"data2\" nospaw>%s</td><td class=\"data2\" nospaw>%s</td><td class=\"data2\" nospaw>%s-%s</td><td class=\"data2\" nospaw><a href=\"http://%s\">%s</a></td><td class=\"data2\" nospaw>%s</td></tr>\n",name,ip,date,hour,url,url,rule);
      fputs(html2,fp_ou);
   }

   fputs("</table>\n",fp_ou);

   show_info(fp_ou);
   
   fputs("</body>\n</html>\n",fp_ou);

   fclose(fp_in);
   fclose(fp_ou);

   unlink(dansguardian_in);

   return;
}
