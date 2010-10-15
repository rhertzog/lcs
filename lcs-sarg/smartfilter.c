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

void smartfilter_report()
{

   FILE *fp_in = NULL, *fp_ou = NULL, *fp_user = NULL;
      
   char url[MAXLEN];
   char html[MAXLEN];
   char html2[MAXLEN];
   char csort[255];
   char smart_in[MAXLEN];
   char smart_ou[MAXLEN];
   char per[MAXLEN];
   char sites[MAXLEN];
   char report[MAXLEN];
   char period[100];
   char ip[MAXLEN];
   char user[MAXLEN];
   char ouser[MAXLEN];
   char data[15];
   char hora[15];
   char smartcat[256];
   char smartheader[15];
   char ftime[128];
   char smartuser[MAXLEN];
   char *str;
   int  fuser=0;

   ouser[0]='\0';

   sprintf(smartheader,"%s",text[116]);
   strup(smartheader);

   sprintf(smart_in,"%s/smartfilter.unsort",dirname);
   sprintf(sites,"%s/sites",dirname);
   sprintf(smart_ou,"%s/smartfilter.log",dirname);
   sprintf(per,"%s/period",dirname);
   sprintf(report,"%s/smartfilter.html",dirname);

   if ((fp_in = fopen(per, "r")) == 0) {
      fprintf(stderr, "SARG: (smartfilter) %s: %s\n",text[45],per);
      exit(1);
   }

   fgets(period,sizeof(period),fp_in);
   fclose(fp_in);

   sprintf(csort,"sort -n -k 1,1 -k 2,2 -k 3,3 -o '%s' '%s'",smart_ou,smart_in);
   system(csort);
   unlink(smart_in);

   if((fp_in=fopen(smart_ou,"r"))==NULL) {
     fprintf(stderr, "SARG: (smartfilter) %s: %s\n",text[8],smart_ou);
     exit(1);
   }

   if((fp_ou=fopen(report,"w"))==NULL) {
     fprintf(stderr, "SARG: (smartfilter) %s: %s\n",text[8],report);
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
   fputs("</head>\n",fp_ou);
   if(strlen(FontFace) > 0) fprintf(fp_ou,"<font face=%s>\n",FontFace);
   fprintf(fp_ou,"<body bgcolor=%s text=%s background='%s'>\n",BgColor,TxColor,BgImage);
   fputs("<center><table cellpadding=0 cellspacing=0>\n",fp_ou);
   if(strlen(LogoImage) > 0) fprintf(fp_ou, "<center><table cellpadding=\"0\" cellspacing=\"0\">\n<tr><th class=\"logo\"><img src='%s' border=0 align=absmiddle width=%s height=%s>&nbsp;%s</th></tr>\n<tr><td height=\"5\"></td></tr>\n</table>\n",LogoImage,Width,Height,LogoText);

   fprintf(fp_ou,"<tr><th align=center><b><font color=%s size=+1>%s</font></b></th></tr>\n",TiColor,Title);
   fprintf(fp_ou,"<tr><td align=center bgcolor=%s><font size=%s>%s: %s</font></td></tr>\n",HeaderBgColor,FontSize,text[89],period);
   fprintf(fp_ou,"<tr><th bgcolor=%s align=center><font size=%s>%s</font></th></tr>\n",HeaderBgColor,FontSize,text[116]);
   fputs("</table></center>\n",fp_ou);

   fputs("<center><table cellpadding=0 cellspacing=2>\n",fp_ou);
   fputs("<tr><td></td></tr>\n",fp_ou);
   fputs("<tr><td></td></tr>\n",fp_ou);
   fputs("<tr><td></td></tr>\n",fp_ou);
   fprintf(fp_ou,"<tr><th bgcolor=%s><font size=%s>%s</font></th><th bgcolor=%s><font size=%s>%s</font></th><th bgcolor=%s><font size=%s>%s</font></th><th bgcolor=%s><font size=%s>%s</font></th><th bgcolor=%s><font size=%s>%s</font></th></tr>\n",HeaderBgColor,FontSize,text[98],HeaderBgColor,FontSize,text[111],HeaderBgColor,FontSize,text[110],HeaderBgColor,FontSize,text[91],HeaderBgColor,FontSize,smartheader);

   while(fgets(buf,sizeof(buf),fp_in)!=NULL) {
      getword(user,buf,' ');
      getword(data,buf,' ');
      getword(hora,buf,' ');
      getword(ip,buf,' ');
      getword(url,buf,' ');
      getword(smartcat,buf,'\n');

      if((str=(char *) strstr(user, "_")) != (char *) NULL ) {
         if((str=(char *) strstr(str+1, "_")) != (char *) NULL )
            fixip(user);
      }

      if(strcmp(ouser,user) != 0) {
         strcpy(ouser,user);
         sprintf(smartuser,"%s/denied_%s.html",dirname,user);
         if(fuser) {
            fuser=0;
            fputs("</table>\n",fp_user);
            if(strcmp(ShowSargInfo,"yes") == 0) {
               zdate(ftime, DateFormat);
               sprintf(html2,"<br><br><center><font size=-2>%s <a href='%s'>%s-%s</a> %s %s</font></center>\n",text[108],URL,PGM,VERSION,text[109],ftime);
               fputs(html2,fp_user);
	    }
            fputs("</body>\n</html>\n",fp_user);
            fclose(fp_user);
         }
         if ((fp_user = fopen(smartuser, "a")) == 0) {
            fprintf(stderr, "SARG: (smartfilter) %s: %s\n",text[45],smartuser);
            exit(1);
         }
         fuser=1;

         fputs("<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\"",fp_ou);
         fputs(" \"http://www.w3.org/TR/html4/loose.dtd\">\n",fp_ou);
         fputs("<html>\n",fp_user);
         fputs("<head>\n",fp_user);
         sprintf(html,"  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=%s\">\n",CharSet);
         fputs(html,fp_user);
         fputs("</head>\n",fp_user);

         if(strlen(FontFace) > 0) {
            sprintf(html2,"<font face=%s>\n",FontFace);
            fputs(url,fp_user);
         }
         sprintf(html2,"<body bgcolor=%s text=%s background='%s'>\n",BgColor,TxColor,BgImage);
         fputs(html2,fp_user);
         fputs("<center><table cellpadding=0 cellspacing=0>\n",fp_user);
         if(strlen(LogoImage) > 0) fprintf(fp_user,"<tr><th align=left><img src='%s' border=0 align=absmiddle width=%s height=%s><font color=%s>%s</font>\n",LogoImage,Width,Height,LogoTextColor,LogoText);
         fprintf(fp_user,"<tr><th align=center><b><font color=%s size=+1>%s</font></b></th></tr>\n",TiColor,Title);
         fprintf(fp_user,"<tr><td align=center bgcolor=%s><font size=%s>%s: %s</font></td></tr>\n",HeaderBgColor,FontSize,text[89],period);
         fprintf(fp_user,"<tr><td align=center bgcolor=%s><font size=%s>%s:</font><font size=%s> %s</font></td></tr>\n",HeaderBgColor,FontSize,text[90],FontSize,user);
         fputs("</table></center>\n",fp_user);
         fputs("<center><table cellpadding=0 cellspacing=2>\n",fp_user);
         fputs("<tr><td></td></tr>\n",fp_user);
         fputs("<tr><td></td></tr>\n",fp_user);
         fputs("<tr><td></td></tr>\n",fp_user);
         sprintf(html2,"<tr><th bgcolor=%s><font size=%s>%s</font></th><th bgcolor=%s><font size=%s>%s</font></th><th bgcolor=%s><font size=%s>%s</font></th><th bgcolor=%s><font size=%s>%s</font></th><th bgcolor=%s><font size=%s>%s</font></th></tr>\n",HeaderBgColor,FontSize,text[98],HeaderBgColor,FontSize,text[111],HeaderBgColor,FontSize,text[110],HeaderBgColor,FontSize,text[91],HeaderBgColor,FontSize,smartheader);
         fputs(html2,fp_user);
      }
      sprintf(html2,"<tr><td bgcolor=%s align=center><font size=%s>%s</font></td><td bgcolor=%s align=center><font size=%s>%s</font></td><td bgcolor=%s align=center><font size=%s>%s-%s</font></td><td bgcolor=%s><font size=%s>%s</font></td><td bgcolor=%s><font size=%s>%s</font></td></th>\n",TxBgColor,FontSize,user,TxBgColor,FontSize,ip,TxBgColor,FontSize,data,hora,TxBgColor,FontSize,url,TxBgColor,FontSize,smartcat);
      fputs(html2,fp_user);

      sprintf(html,"<tr><td bgcolor=%s align=center><font size=%s>%s</font></td><td bgcolor=%s align=center><font size=%s>%s</font></td><td bgcolor=%s align=center><font size=%s>%s-%s</font></td><td bgcolor=%s><font size=%s>%s</font></td><td bgcolor=%s><font size=%s>%s</font></td></th>\n",TxBgColor,FontSize,user,TxBgColor,FontSize,ip,TxBgColor,FontSize,data,hora,TxBgColor,FontSize,url,TxBgColor,FontSize,smartcat);
      fputs(html,fp_ou);
   }

   fputs("</table>\n",fp_ou);

   if(strcmp(ShowSargInfo,"yes") == 0) {
      zdate(ftime, DateFormat);
      sprintf(html,"<br><br><center><font size=-2>%s <a href='%s'>%s-%s</a> %s %s</font></center>\n",text[108],URL,PGM,VERSION,text[109],ftime);
      fputs(html,fp_ou);
   }

   fputs("</body>\n</html>\n",fp_user);

   fclose(fp_ou);
   if(fp_user) {
      fputs("</table>\n",fp_user);
      if(strcmp(ShowSargInfo,"yes") == 0) {
         zdate(ftime, DateFormat);
         sprintf(html2,"<br><br><center><font size=-2>%s <a href='%s'>%s-%s</a> %s %s</font></center>\n",text[108],URL,PGM,VERSION,text[109],ftime);
         fputs(html2,fp_user);
      }
      fputs("</body>\n</html>\n",fp_user);
      fclose(fp_user);
   }

   return;
}
