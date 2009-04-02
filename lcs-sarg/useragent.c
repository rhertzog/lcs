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

void useragent()
{

   FILE *fp_in = NULL, *fp_ou = NULL, *fp_ht = NULL;
   char tmp[MAXLEN], tmp2[MAXLEN];
   char ip[MAXLEN], data[255], agent[255], user[255];
   char ipantes[MAXLEN], nameantes[MAXLEN];
   char tagent[7];
   char csort[120];
   char msg[255];
   char ftime[128];
   char user_old[255]="$#%0a3bc6";
   char agent_old[255]="$#%0a3bc6";
   char html[255];
   char hfile[MAXLEN];
   char idate[MAXLEN], fdate[MAXLEN];
   int  agentot=0, agentot2=0, agentdif=0, cont=0;
   float perc=0;
   unsigned long totregsl=0;

   ip[0]='\0';
   data[0]='\0';
   agent[0]='\0';
   user[0]='\0';
   user_old[0]='\0';
   agent_old[0]='\0';
   ipantes[0]='\0';
   nameantes[0]='\0';

   sprintf(hfile,"%s/%s/useragent.html", outdir,period);

   sprintf(tmp,"%s/squagent.unsort",TempDir);
   sprintf(tmp2,"%s/squagent.log",TempDir);

   if((fp_in=fopen(UserAgentLog,"r"))==NULL) {
      fprintf(stderr, "SARG: (useragent) %s: %s\n",text[45],UserAgentLog);
      exit(1);
   }

   if((fp_ou=fopen(tmp,"w"))==NULL) {
      fprintf(stderr, "SARG: (email) %s: %s\n",text[45],tmp);
      exit(1);
   }

   if(debug) {
      sprintf(msg,"%s: %s",text[66],UserAgentLog);
      debuga(msg);
   }

   while(fgets(buf,sizeof(buf),fp_in)!=NULL) {
      totregsl++;
      getword(ip,buf,' ');
      getword(data,buf,'[');
      getword(data,buf,' ');
      if(totregsl == 1)
         strcpy(idate,data);
      getword(agent,buf,'"');
      getword(agent,buf,'"');

      if(strlen(buf)) {
         getword(user,buf,' ');
         getword(user,buf,'\n');
      }

      if(user[0] == '-')
         strcpy(user,ip);
      if(strlen(user) == 0)
         strcpy(user,ip);

      sprintf(buf,"%s\\%s\\%s\\%s\\\n",ip,data,agent,user);
      fputs(buf,fp_ou);
      user[0]='\0';
   }

   strcpy(fdate,data);

   if(debug) {
      sprintf(msg, "   %s: %ld",text[10],totregsl);
      debuga(msg);
   }

   fclose(fp_in);
   fclose(fp_ou);

   if (fp_ht) {
      fclose(fp_ht);
   }

   if(debug) {
      sprintf(msg,"%s: %s",text[54],tmp2);
      debuga(msg);
   }

   sprintf(csort,"sort -n -t '\\' -k 4,4 -k 3,3 -k 2,2 -k 1,1 -o '%s' '%s'",tmp2,tmp);
   system(csort);

   unlink(tmp);

   if((fp_in=fopen(tmp2,"r"))==NULL) {
      fprintf(stderr, "SARG: (useragent) %s: %s\n",text[45],tmp2);
      exit(1);
   }

   if((fp_ht=fopen(hfile,"w"))==NULL) {
      fprintf(stderr, "SARG: (useragent) %s: %s\n",text[45],hfile);
      exit(1);
   }
 
   if(debug)
      debuga(text[72]);
      /* LCS */
      fputs("<?php\n",fp_ht);
      fputs("\n",fp_ht);
      fputs("include \"/var/www/lcs/includes/headerauth.inc.php\";\n",fp_ht);
      fputs("include \"/var/www/Annu/includes/ldap.inc.php\";\n",fp_ht);
      fputs("include \"/var/www/Annu/includes/ihm.inc.php\";\n",fp_ht);
      fputs("\n",fp_ht);
      fputs("list ($idpers,$login)= isauth();\n",fp_ht);
      fputs("if ($idpers == \"0\") header(\"Location:$urlauth\");\n",fp_ht);
      fputs("?>\n",fp_ht);
      fputs("<!--useragent-->\n",fp_ht);
   fprintf(fp_ht, "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">\n<html>\n<head>\n  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=%s\">\n",CharSet);
   fputs("</head>\n",fp_ht);
   if(strlen(FontFace) > 0) fprintf(fp_ht,"<font face=%s>\n",FontFace);
   fprintf(fp_ht,"<body bgcolor=%s text=%s background='%s'>\n",BgColor,TxColor,BgImage);
   if(strlen(LogoImage) > 0) fprintf(fp_ht, "<center><table cellpadding=\"0\" cellspacing=\"0\">\n<tr><th class=\"logo\"><img src='%s' border=0 align=absmiddle width=%s height=%s>&nbsp;%s</th></tr>\n<tr><td height=\"5\"></td></tr>\n</table>\n",LogoImage,Width,Height,LogoText);

   if(strcmp(IndexTree,"date") == 0)
      show_sarg(fp_ht, "../../..");
   else
      show_sarg(fp_ht,"..");
   fputs("<center><table cellpadding=0 cellspacing=0>\n",fp_ht);
   sprintf(html,"<tr><th align=center colspan=2><b><font color=%s size=+1>%s</font></b></th></tr>\n",TiColor,text[105]);
   fputs(html,fp_ht);

   fputs("<tr><td></td></tr><tr><td></td></tr>",fp_ht);
   fputs("</table></center>\n",fp_ht);

   fputs("<center><table cellpadding=0 cellspacing=0>\n",fp_ht);

   sprintf(html,"<tr><td align=right bgcolor=%s><font size=%s>%s:</font><td align=left bgcolor=%s><font size=%s>%s - %s</font></td></td></tr>\n",HeaderBgColor,FontSize,text[89],TxBgColor,FontSize,idate,fdate);
   fputs(html,fp_ht);

   fputs("</table></center>\n",fp_ht);
   fputs("<center><table cellpadding=0 cellspacing=0>\n",fp_ht);
   fputs("<tr><td></td><td></td></tr>",fp_ht);

   sprintf(html,"<tr><th align=left bgcolor=%s><font size=%s color=%s>%s</font></th><th bgcolor=%s align=left><font size=%s color=%s>%s</font></th></tr>\n",HeaderBgColor,FontSize,HeaderColor,text[98],HeaderBgColor,FontSize,HeaderColor,text[106]);
   fputs(html,fp_ou);

   while(fgets(buf,sizeof(buf),fp_in)!=NULL) {
      getword(ip,buf,'\\');

      if(strcmp(Ip2Name,"yes") == 0) {
         if(strcmp(ip,ipantes) != 0) {
            strcpy(ipantes,ip);
            ip2name(ip);
            strcpy(nameantes,ip);
         } else strcpy(ip,nameantes);
      }

      getword(data,buf,'\\');
      getword(agent,buf,'\\');
      getword(user,buf,'\\');

      if(strcmp(user,user_old) != 0) {
         sprintf(html,"<tr><td align=left bgcolor=%s><font size=%s>%s</td><td align=left bgcolor=%s><font size=%s>%s</td></tr>\n",TxBgColor,FontSize,user,TxBgColor,FontSize,agent);
         fputs(html,fp_ht);
         strcpy(user_old,user);
         strcpy(agent_old,agent);
      } else {
         if(strcmp(agent,agent_old) != 0) {
            sprintf(html,"<tr><td></td><td align=left bgcolor=%s><font size=%s>%s</td></tr>\n",TxBgColor,FontSize,agent);
            fputs(html,fp_ht);
            strcpy(agent_old,agent);
         }
      }
   }
  
   fputs("</table>\n",fp_ht);
   fclose(fp_in);
   fclose(fp_ou);

   sprintf(csort,"sort -n -t '\\' -k 3,3 -o '%s' '%s'",tmp,tmp2);
   system(csort);

   unlink(tmp2);

   if((fp_in=fopen(tmp,"r"))==NULL) {
      fprintf(stderr, "SARG: (useragent) %s: %s\n",text[45],tmp);
      exit(1);
   }

   if((fp_ou=fopen(tmp2,"w"))==NULL) {
      fprintf(stderr, "SARG: (useragent) %s: %s\n",text[45],tmp2);
      exit(1);
   }

   agent_old[0]='\0';

   while(fgets(buf,sizeof(buf),fp_in)!=NULL) {
      getword(ip,buf,'\\');
      getword(data,buf,'\\');
      getword(agent,buf,'\\');

      if(!cont) {
         cont++;
         strcpy(agent_old,agent);
      }
     
      agentot++;

      if(strcmp(agent,agent_old) != 0) {
         agentdif++;
         sprintf(html,"%06d %s\n",agentot,agent_old);
         fputs(html,fp_ou);
         strcpy(agent_old,agent);
         agentot2+=agentot;
         agentot=1;
      }
   }
   agentdif++;
   sprintf(html,"%06d %s\n",agentot,agent);
   fputs(html,fp_ou);
   agentot2+=agentot;

   fclose(fp_in);
   fclose(fp_ou);

   unlink(tmp);

   sprintf(csort,"sort -n -r -k 1,1 -o '%s' '%s'",tmp,tmp2);
   system(csort);

   unlink(tmp2);

   if((fp_in=fopen(tmp,"r"))==NULL) {
      fprintf(stderr, "SARG: (useragent) %s: %s\n",text[45],tmp);
      exit(1);
   }

   if((fp_ht=fopen(hfile,"a"))==NULL) {
      fprintf(stderr, "SARG: (useragent) %s: %s\n",text[45],hfile);
      exit(1);
   }

   fputs("<br><br>\n",fp_ht);

   fputs("<center><table cellpadding=0 cellspacing=0>\n",fp_ht);
   sprintf(html,"<tr><th align=left bgcolor=%s><font size=%s color=%s>%s</font></th><th bgcolor=%s align=left><font size=%s color=%s>%s</font></th><th align=center bgcolor=%s><font size=%s color=%s>%%</font></th></tr>\n",HeaderBgColor,FontSize,HeaderColor,text[106],HeaderBgColor,FontSize,HeaderColor,text[107],HeaderBgColor,FontSize,HeaderColor);
   fputs(html,fp_ht);

   while(fgets(buf,sizeof(buf),fp_in)!=NULL) {
      buf[strlen(buf)-1]='\0';
      getword(tagent,buf,' ');
      perc=atoi(tagent) * 100;
      perc=perc / agentot2;

      sprintf(html,"<tr><td align=left bgcolor=%s><font size=%s>%s</td><td align=right bgcolor=%s><font size=%s>%d</td><td align=right bgcolor=%s><font size=%s>%3.2f</td></tr>\n",TxBgColor,FontSize,buf,TxBgColor,FontSize,atoi(tagent),TxBgColor,FontSize,perc);
      fputs(html,fp_ht);
   }

   fputs("</table></html>\n",fp_ht);
   if(strcmp(ShowSargInfo,"yes") == 0) {
      zdate(ftime, DateFormat);
      sprintf(html,"<br><br><center><font size=-2>%s <a href='%s'>%s-%s</a> %s %s</font></center>\n",text[108],URL,PGM,VERSION,text[109],ftime);
      fputs(html,fp_ht);
   }

   fputs("</table>\n</body>\n</html>\n",fp_ht);
   fclose(fp_in);
   fclose(fp_ht);

   unlink(tmp); 

   return;
      
}
