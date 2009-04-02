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

void topsites()
{

   FILE *fp_in, *fp_ou;
      
   char url[MAXLEN];
   char ourl[MAXLEN];
   char nacc[20];
   char nbytes[20];
   char ntime[20];
   char ntemp[20];
   char ttnacc[20];
   char ttnbytes[20];
   char ttntime[20];
   char csort[255];
   char general[MAXLEN];
   char general2[MAXLEN];
   char general3[MAXLEN];
   char per[MAXLEN];
   char sites[MAXLEN];
   char report[MAXLEN];
   char period[100];
   char sortf[10];
   char sortt[10];
   long long int tnacc=0;
   long long int tnbytes=0;
   long long int tntime=0;
   long long int twork1=0, twork2=0, twork3=0;
   int regs=0;

   if(strcmp(Privacy,"yes") == 0)
      return;

   sprintf(general,"%s/general",dirname);
   sprintf(sites,"%s/sites",dirname);
   sprintf(general2,"%s/general2",dirname);
   sprintf(general3,"%s/general3",dirname);
   sprintf(per,"%s/period",dirname);

   if (strstr(ReportType,"topusers") == 0)
      sprintf(report,"%s/index.php",dirname);
   else
      sprintf(report,"%s/topsites.php",dirname);

   if ((fp_in = fopen(per, "r")) == 0) {
      fprintf(stderr, "SARG: (topuser) %s: %s\n",text[45],per);
      exit(1);
   }

   fgets(period,sizeof(period),fp_in);
   fclose(fp_in);

   sprintf(csort,"sort -k 4,4 -o '%s' '%s'",general2,general);
   system(csort);

   if((fp_in=fopen(general2,"r"))==NULL) {
     fprintf(stderr, "SARG: (topsite) %s: %s\n",text[8],general2);
     exit(1);
   }

   if((fp_ou=fopen(general3,"w"))==NULL) {
     fprintf(stderr, "SARG: (topsite) %s: %s\n",text[8],general3);
     exit(1);
   }

   while(fgets(buf,sizeof(buf),fp_in)!=NULL) {
      getword(url,buf,' ');
      if(strcmp(url,"TOTAL") == 0) {
         getword(ttnacc,buf,' ');
         getword(ttnbytes,buf,' ');
         getword(ttntime,buf,' ');
         continue;
      }
      getword(nacc,buf,' ');
      getword(nbytes,buf,' ');
      getword(url,buf,' ');
      getword(ntemp,buf,' ');
      getword(ntemp,buf,' ');
      getword(ntemp,buf,' ');
      getword(ntime,buf,' ');

      if(!regs) {
         strcpy(ourl,url);
         regs++;
      }

      if(strcmp(url,ourl) != 0) {
         my_lltoa(tnacc,val1,15);
         my_lltoa(tnbytes,val2,15);
         my_lltoa(tntime,val3,15);
         sprintf(buf,"%s %s %s %s\n",val1,val2,val3,ourl);
         fputs(buf, fp_ou);
         strcpy(ourl,url);
         tnacc=0;
         tnbytes=0;
         tntime=0;
      }

      tnacc+=my_atoll(nacc);
      tnbytes+=my_atoll(nbytes);
      tntime+=my_atoll(ntime);
   }

   my_lltoa(tnacc,val1,15);
   my_lltoa(tnbytes,val2,15);
   my_lltoa(tntime,val3,15);
   sprintf(buf,"%s %s %s %s\n",val1,val2,val3,ourl);
   fputs(buf, fp_ou);

   fclose(fp_in);
   fclose(fp_ou);
   unlink(general2);

   strlow(TopsitesSortField);
   strlow(TopsitesSortType);

   if(strcmp(TopsitesSortField,"connect") == 0)
      strcpy(sortf,"1,1");
   if(strcmp(TopsitesSortField,"bytes") == 0)
      strcpy(sortf,"2,2");
   if(strcmp(TopsitesSortType,"a") == 0)
      strcpy(sortt," ");
   if(strcmp(TopsitesSortType,"d") == 0)
      strcpy(sortt,"-r");

   sprintf(csort,"sort %s -k %s -o '%s' '%s'",sortt,sortf,sites,general3);
   system(csort);

   unlink(general2);
   unlink(general3);

   if((fp_in=fopen(sites,"r"))==NULL) {
     fprintf(stderr, "SARG: (topsite) %s: %s\n",text[8],sites);
     exit(1);
   }

   if((fp_ou=fopen(report,"w"))==NULL) {
     fprintf(stderr, "SARG: (topsite) %s: %s\n",text[8],report);
     exit(1);
   }

   regs=0;
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
   fprintf(fp_ou,"<body bgcolor=%s text=%s background='%s'>\n",BgColor,TxColor,BgImage);
   if(strlen(LogoImage) > 0) fprintf(fp_ou, "<center><table cellpadding=\"0\" cellspacing=\"0\">\n<tr><th class=\"logo\"><img src='%s' border=0 align=absmiddle width=%s height=%s>&nbsp;%s</th></tr>\n<tr><td height=\"5\"></td></tr>\n</table>\n",LogoImage,Width,Height,LogoText);

   if(strcmp(IndexTree,"date") == 0)
      show_sarg(fp_ou,"../../..");
   else
      show_sarg(fp_ou, "..");

   fputs("<center><table cellpadding=\"0\" cellspacing=\"0\">\n",fp_ou);
   sprintf(url,"<tr><th class=\"title\">%s</th></tr>\n",Title);
   fputs(url,fp_ou);

   sprintf(url,"<tr><td class=\"header3\">%s: %s</td></tr>\n",text[89],period);
   fputs(url,fp_ou);
   sprintf(url,"<tr><th class=\"header3\">%s %s %s</th></tr>\n",text[83],TopSitesNum,text[84]);
   fputs(url,fp_ou);
   fputs("</table></center>\n",fp_ou);

   fputs("<center><table cellpadding=\"1\" cellspacing=\"2\">\n",fp_ou);
   fputs("<tr><td></td></tr>\n",fp_ou);
   sprintf(url,"<tr><th class=\"header\">%s</th><th class=\"header\">%s</th> \
		<th class=\"header\">%s</th><th class=\"header\">%s</th> \
		<th class=\"header\">%s</th></tr>\n", \
		text[100],text[91],text[92],text[93],text[99]);
   fputs(url,fp_ou);

   regs=1;

   while(fgets(buf,sizeof(buf),fp_in)!=NULL) {
      if(regs>atoi(TopSitesNum))
         break;
      getword(nacc,buf,' ');
      getword(nbytes,buf,' ');
      getword(ntime,buf,' ');
      getword(url,buf,' ');

      twork1=my_atoll(nacc);
      twork2=my_atoll(nbytes);
      twork3=my_atoll(ntime);

      sprintf(wwork1,"%s",fixnum(twork1,1));
      sprintf(wwork2,"%s",fixnum(twork2,1));
      sprintf(wwork3,"%s",fixnum(twork3,1));

      if(strlen(BlockIt) > 0)
         sprintf(BlockImage,"<a href=\"%s%s?url=%s\"><img src=\"../images/sarg-squidguard-block.png\" border=\"0\"></a>&nbsp;",wwwDocumentRoot,BlockIt,url);
      else BlockImage[0]='\0';


      sprintf(ourl,"<tr><td class=\"data\">%d</td><td class=\"data2\">%s<a href=\"http://%s\"><font class=\"link\">%s</font></td><td class=\"data\">%s</td><td class=\"data\">%s</td><td class=\"data\">%s</td></tr>\n",regs,BlockImage,url,url,wwork1,wwork2,wwork3);
      fputs(ourl,fp_ou);
      regs++;
   }


   fputs("</table></center>\n",fp_ou);

   show_info(fp_ou);

   fputs("</body>\n</html>\n",fp_ou);
   
   fclose(fp_in);
   fclose(fp_ou);

   return;

}
