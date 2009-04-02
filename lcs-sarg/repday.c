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

extern numlist hours;

void report_day(char *user, int *iprel, int *ipuser)
{

   FILE *fp_in, *fp_ou;

   char data[20];
   char odata[20];
   char hora[20];
   char elap[20];
   char oelap[20];
   char html[8000];
   char arqout[MAXLEN];
   char wdirname[MAXLEN];
   char wuser[255];
   char c[ 24 ][20];
   int  count=0;
   int  ihora=0;
   long long int v[ 24 ] = { 0L, 0L, 0L, 0L, 0L, 0L, 0L, 0L, 0L, 0L, 0L, 0L, 
			     0L, 0L, 0L, 0L, 0L, 0L, 0L, 0L, 0L, 0L, 0L, 0L };
   long long int t[ 24 ] = { 0L, 0L, 0L, 0L, 0L, 0L, 0L, 0L, 0L, 0L, 0L, 0L, 
			     0L, 0L, 0L, 0L, 0L, 0L, 0L, 0L, 0L, 0L, 0L, 0L };
   long long int tt=0, ttt=0;
   int i;

   sprintf(arqout,"%s/%s/d%s.php",dirname,user,user);
   sprintf(wdirname,"%s/%s.day",tmp,user);

   if(access(wdirname, R_OK) != 0)
      return;

   if((fp_in=fopen(wdirname,"r"))==NULL) {
     fprintf(stderr, "SARG: (repday) %s: %s\n",text[8],wdirname);
     exit(1);
   }

   if((fp_ou=fopen(arqout,"w"))==NULL) {
     fprintf(stderr, "SARG: (repday) %s: %s\n",text[8],arqout);
     exit(1);
   }

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
      show_sarg(fp_ou, "../../../..");
   else
      show_sarg(fp_ou, "../..");

   fputs("<center><table cellpadding=0 cellspacing=0>\n",fp_ou);
   sprintf(html,"<tr><th class=\"title\" colspan=2>%s</th></tr>\n",Title);
   fputs(html,fp_ou);
   
   fputs("</table></center>\n",fp_ou);
   fputs("<center><table cellpadding=0 cellspacing=0>\n",fp_ou);

   sprintf(html,"<tr><td class=\"header3\" colspan=\"2\">%s:&nbsp;%s</td></tr>\n",text[89],period);
   fputs(html,fp_ou);

   strcpy(wuser,user);
   if(strstr(wuser,"_") != 0)
      fixip(wuser);

   if(strcmp(Ip2Name,"yes") == 0)
      if((str=(char *) strstr(name, ".")) != (char *) NULL) {
         if((str=(char *) strstr(str+1, ".")) != (char *) NULL)
            ip2name(wuser);
   }

   if(UserTabFile[0] != '\0') {
      sprintf(warea,":%s:",wuser);
      if((str=(char *) strstr(userfile,warea)) != (char *) NULL ) {
         z1=0;
         str2=(char *) strstr(str+1,":");
         str2++;
         bzero(name, MAXLEN);
         while(str2[z1] != ':') {
            name[z1]=str2[z1];
            z1++;
         }
      } else strcpy(name,wuser);
   } else strcpy(name,user);

   if(dotinuser && strstr(name,"_")) {
      str2=(char *)subs(name,"_",".");
      strcpy(name,str2);
   }
     
   sprintf(html,"<tr><th class=\"header3\" colspan=\"2\">%s:&nbsp;%s</th></tr>\n",text[90],name);
   fputs(html,fp_ou);

   fputs("<tr><td></td></tr><tr><td></td></tr>\n",fp_ou);
   fputs("<tr><td></td></tr><tr><td></td></tr></table>\n",fp_ou);

   fputs("<table cellpadding=0 cellspacing=2>\n", fp_ou);

   fputs( "<tr><td border=0></td>\n", fp_ou );

   if(strcmp(datetimeby,"bytes") == 0)
     strcpy( html, text[93] );
   else
     strcpy( html, "H:M:S" );

   for( i = 0; i < hours.len; i++ )
     fprintf( fp_ou,
	"<td class=\"header3\">%02dH<br>%s</td>\n", hours.list[ i ], html );
   fprintf( fp_ou,
     "<td class=\"header3\">%s<br>%s</td></tr>\n", text[107], html );

   while(fgets(buf,sizeof(buf),fp_in)!=NULL) {
      getword(data,buf,' ');
      if(!count) {
         strcpy(odata,data);
         count++;
      }

      getword(hora,buf,' ');
      getword(elap,buf,' ');
      elap[strlen(elap)-1]='\0';

      if(strcmp(data,odata) != 0) {
         strcpy(oelap,elap);

	 for( i = 0; i < hours.len; i++ )
	   sprintf(c[ hours.list[ i ] ],"%s",fixtime(v[ hours.list[ i ] ]));

	 for( i = 8; i <= 17; i++ )
	   if(strcmp(c[ hours.list[ i ] ],"00:00:00") == 0)
		bzero(c[ hours.list[ i ] ],12);

         fprintf( fp_ou,
       "<tr><td class=\"data\">%s</td>\n", odata );
	 for( i = 0; i < hours.len; i++ )
	   fprintf( fp_ou,
	     "<td class=\"data\">%s</td>\n", c[ hours.list[ i ] ] );
	 fprintf( fp_ou,
	   "<td class=\"data\">%s</td>\n", fixtime(tt) );
        
         tt=0;
	 for( i = 0; i < hours.len; i++ ) v[ hours.list[ i ] ]=0;
         strcpy(odata,data);
         strcpy(elap,oelap);
      }

      ihora=atoi(hora);

      v[ ihora ]+=my_atoll(elap);
      tt+=my_atoll(elap);
      t[ ihora ]+=my_atoll(elap);
      ttt+=my_atoll(elap);

   }

   for( i = 0; i < hours.len; i++ )
     sprintf(c[ hours.list[ i ] ],"%s",fixtime(v[ hours.list[ i ] ]));

   for( i = 0; i < hours.len; i++ )
     if(strcmp(c[ hours.list[ i ] ],"00:00:00") == 0) bzero(c[ hours.list[ i ] ],12);
 
   fprintf( fp_ou,
     "<tr><td class=\"data\">%s</td>\n", data );
   for( i = 0; i < hours.len; i++ )
     fprintf( fp_ou,
       "<td class=\"data\">%s</td>\n", c[ hours.list[ i ] ] );
   fprintf( fp_ou,
     "<td class=\"data\">%s</td></tr>\n", fixtime(tt) );

   for( i = 0; i < hours.len; i++ )
     sprintf(c[ hours.list[ i ] ],"%s",fixtime(t[ hours.list[ i ] ]));

   fprintf( fp_ou,
     "<tr><td class=\"header\">%s</td>\n", text[107] );
   for( i = 0; i < hours.len; i++ )
     fprintf( fp_ou,
       "<td class=\"header2\">%s</td>\n", c[ hours.list[ i ] ] );
   fprintf( fp_ou,
     "<td class=\"header2\">%s</td></tr>\n", fixtime(ttt) );

   fputs("</body>\n</html>\n",fp_ou);

   show_info(fp_ou);

   fclose(fp_in);
   fclose(fp_ou);

   return;
}
