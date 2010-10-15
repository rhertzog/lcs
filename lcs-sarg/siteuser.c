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

void siteuser()
{

   FILE *fp_in, *fp_ou;
      
   char user[MAXLEN];
   char url[MAXLEN];
   char wuser[MAXLEN];
   char ourl[MAXLEN];
   char nacc[20];
   char nbytes[20];
   char obytes[20];
   char csort[255];
   char general[MAXLEN];
   char general2[MAXLEN];
   char per[MAXLEN];
   char html[MAXLEN];
   char sites[MAXLEN];
   char report[MAXLEN];
   char period[100];
   int regs=0;
   int ucount=0;
   char *users;
   long long int llbytes=0;

   if(strcmp(Privacy,"yes") == 0)
      return;

   sprintf(general,"%s/general",dirname);
   sprintf(sites,"%s/sites",dirname);
   sprintf(general2,"%s/general2",dirname);
   sprintf(per,"%s/period",dirname);
   sprintf(report,"%s/siteuser.php",dirname);

   if ((fp_in = fopen(per, "r")) == 0) {
      fprintf(stderr, "SARG: (topuser) %s: %s\n",text[45],per);
      exit(1);
   }

   fgets(period,sizeof(period),fp_in);
   fclose(fp_in);

   sprintf(csort,"sort -k 4,4 -k 1,1 -o '%s' '%s'",general2,general);
   system(csort);

   if((fp_in=fopen(general2,"r"))==NULL) {
     fprintf(stderr, "SARG: (topsite) %s: %s\n",text[8],general2);
     exit(1);
   }

   if((fp_ou=fopen(report,"w"))==NULL) {
     fprintf(stderr, "SARG: (topsite) %s: %s\n",text[8],report);
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
      show_sarg(fp_ou, "..");

   fputs("<center><table cellpadding=0 cellspacing=0>\n",fp_ou);
   sprintf(url,"<tr><th class=\"title\">%s</th></tr>\n",Title);
   fputs(url,fp_ou);

   sprintf(url,"<tr><td class=\"header3\">%s: %s</td></tr>\n",text[89],period);
   fputs(url,fp_ou);
   sprintf(url,"<tr><th class=\"header3\">%s</th></tr>\n",text[85]);
   fputs(url,fp_ou);
   fputs("</table></center>\n",fp_ou);

   fputs("<center><table cellpadding=0 cellspacing=2>\n",fp_ou);
   fputs("<tr><td></td></tr>\n",fp_ou);
   if(strncmp(strlow(BytesInSitesUsersReport),"yes",3) == 0)
      sprintf(url,"<tr><th class=\"header\">%s</th><th class=\"header\">%s</th><th class=\"header\">%s</th><th class=\"header\">%s</th></tr>\n",text[100],text[91],text[93],text[103]);
   else sprintf(url,"<tr><th class=\"header\">%s</th><th class=\"header\">%s</th><th class=\"header\">%s</th></tr>\n",text[100],text[91],text[103]);
   fputs(url,fp_ou);
  
   user[0]='\0';
   ourl[0]='\0';
   obytes[0]='\0';

   if((users=(char *) malloc(204800))==NULL){
      fprintf(stderr, "SARG: ERROR: %s",text[87]);
      exit(1);
   }
   strcat(users," ");

   while(fgets(buf,sizeof(buf),fp_in)!=NULL) {
      getword(user,buf,' ');
      if(strcmp(user,"TOTAL") == 0)
         continue;
      if(userip)
         fixip(user);

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

      if(strcmp(Ip2Name,"yes") == 0)
         ip2name(user);

      if(strcmp(Ip2Name,"yes") == 0)
         ip2name(user);

      getword(nacc,buf,' ');
      getword(nbytes,buf,' ');
      getword(url,buf,' ');

      if(!regs) {
         strcpy(ourl,url);
         strcpy(obytes,nbytes);
         regs++;
      }
      
      sprintf(wuser," %s ",name);
      if(strstr(users,wuser) == 0 && strcmp(url,ourl) == 0) {
         strcat(users,name);
         strcat(users," ");
         ucount++;
         if(ucount>4) {
            strcat(users,"<br>");
            ucount=0;
         }
      }

      if(SiteUsersReportLimit) {
         if(regs >= SiteUsersReportLimit)
            continue;
      }

      if(strlen(BlockIt) > 0)
         sprintf(BlockImage,"<a href=\"%s%s?url=%s\"><img src=\"../images/sarg-squidguard-block.png\" border=\"0\"></a>&nbsp;",wwwDocumentRoot,BlockIt,ourl);
      else BlockImage[0]='\0';

      if(strcmp(url,ourl) != 0) {
         if(strncmp(strlow(BytesInSitesUsersReport),"yes",3) == 0) {
            llbytes=my_atoll(obytes);
            sprintf(wwork2,"%s",fixnum(llbytes,1));
            sprintf(html,"<tr><td class=\"data\">%d</td><td class=\"data2\">%s<a href=\"http://%s\">%s</td><td class=\"data\">%s</td><td class=\"data2\">%s</td></tr>\n",regs,BlockImage,ourl,ourl,wwork2,users);
         } else sprintf(html,"<tr><td class=\"data\">%d</td><td class=\"data2\">%s<a href=\"http://%s\">%s</td><td class=\"data2\">%s</td></tr>\n",regs,BlockImage,ourl,ourl,users);
         fputs(html,fp_ou);
         regs++;
         ucount=0;
	 strcpy(users,name);
         strcat(users," ");
         strcpy(ourl,url);
         strcpy(obytes,nbytes);
      }
   }

   sprintf(html,"<tr><td class=\"data\">%d</td><td class=\"data2\"><a href=\"http://%s\">%s</td><td class=\"data2\">%s</td></tr>\n",regs,ourl,ourl,users);
   fputs(html,fp_ou);

   unlink(general2);

   fputs("</table></center>\n",fp_ou);

   show_info(fp_ou);

   fputs("</body>\n</html>\n",fp_ou);
   
   fclose(fp_in);
   fclose(fp_ou);

   if(users)
      free(users);

   return;

}
