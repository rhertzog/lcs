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

void authfail_report()
{

   FILE *fp_in = NULL, *fp_ou = NULL;
      
   char url[MAXLEN];
   char html2[MAXLEN];
   char authfail_in[MAXLEN];
   char per[MAXLEN];
   char report[MAXLEN];
   char period[100];
   char ip[MAXLEN];
   char oip[MAXLEN];
   char user[MAXLEN];
   char ouser[MAXLEN];
   char ouser2[MAXLEN];
   char data[15];
   char hora[15];
   char *str;
   int  z=0;
   int  count=0;

   if(strlen(DataFile) > 0) return;

   ouser[0]='\0';

   sprintf(tmp4,"%s/sarg/authfail.log.unsort",TempDir);
   
   if(!authfail_count) {
      unlink(tmp4);
      return;
   }

   sprintf(authfail_in,"%s/authfail.log",TempDir);
   sprintf(per,"%s/period",dirname);
   sprintf(report,"%s/authfail.php",dirname);

   sprintf(csort,"sort -b -T %s -k 3,3 -k 5,5 -o '%s' '%s'", TempDir, authfail_in, tmp4);
   system(csort);
   unlink(tmp4);

   if ((fp_in = fopen(per, "r")) == 0) {
      fprintf(stderr, "SARG: (authfail) %s: %s\n",text[45],per);
      exit(1);
   }

   fgets(period,sizeof(period),fp_in);
   fclose(fp_in);

   if((fp_in=fopen(authfail_in,"r"))==NULL) {
     fprintf(stderr, "SARG: (authfail) %s: %s\n",text[45],authfail_in);
     exit(1);
   }

   if((fp_ou=fopen(report,"w"))==NULL) {
     fprintf(stderr, "SARG: (authfail) %s: %s\n",text[45],report);
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
   sprintf(url,"<tr><th class=\"header3\">%s</th></tr>\n",text[117]);
   fputs(url,fp_ou);
   fputs("</table></center>\n",fp_ou);

   fputs("<center><table cellpadding=0 cellspacing=2>\n",fp_ou);
   fputs("<tr><td></td></tr>\n",fp_ou);
   fputs("<tr><td></td></tr>\n",fp_ou);
   fputs("<tr><td></td></tr>\n",fp_ou);
   sprintf(url,"<tr><th class=\"header\">%s</th><th class=\"header\">%s</th><th class=\"header\">%s</th><th class=\"header\">%s</th></tr>\n",text[98],text[111],text[110],text[91]);
   fputs(url,fp_ou);

   while(fgets(buf,sizeof(buf),fp_in)!=NULL) {
      getword(data,buf,' ');
      getword(hora,buf,' ');
      getword(user,buf,' ');
      getword(ip,buf,' ');
      getword(url,buf,' ');

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

     if(AuthfailReportLimit) {
        if(strcmp(ouser2,name) == 0) {
            count++;
         } else {
            count=1;
            strcpy(ouser2,name);
         }
         if(count >= AuthfailReportLimit)
            continue;
      }

      sprintf(html2,"<tr><td class=\"data2\">%s</td><td class=\"data2\">%s</td><td class=\"data2\">%s-%s</td><td class=\"data2\">%s<a href=\"%s\">%s</a></td></th>\n",name,ip,data,hora,BlockImage,url,url);
      fputs(html2,fp_ou);
   }

   fputs("</table>\n",fp_ou);

   show_info(fp_ou);

   fputs("</body>\n</html>\n",fp_ou);

   fclose(fp_in);
   fclose(fp_ou);

   unlink(authfail_in);

   return;
}
