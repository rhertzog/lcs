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

int dansguardian_log()
{

   FILE *fp_in = NULL, *fp_ou = NULL, *fp_guard = NULL;
   char guard_in[MAXLEN];
   char guard_ou[MAXLEN];
   char loglocation[MAXLEN] = "/var/log/dansguardian/access.log";
   char year[10], mon[10], day[10];
   char hour[15];
   char user[MAXLEN], code1[255], code2[255];
   char ip[30];
   char wdata[127];
   int  idata=0;
   int  x, y;

   bzero(day, 3);
   bzero(mon, 4);
   bzero(year, 5);

   if(strcmp(df,"e") == 0) {
      strncpy(day,period,2);
      strncpy(mon,period+2,3);
      strncpy(year,period+5,4);
      conv_month(mon);
      sprintf(warea,"%s%s%s",year,mon,day);
      dfrom=atoi(warea);
      strncpy(day,period+10,2);
      strncpy(mon,period+12,3);
      strncpy(year,period+15,4);
      conv_month(mon);
      sprintf(warea,"%s%s%s",year,mon,day);
      duntil=atoi(warea);
   } else {
      strncpy(day,period+7,2);
      strncpy(mon,period+4,3);
      strncpy(year,period,4);
      conv_month(mon);
      sprintf(warea,"%s%s%s",year,mon,day);
      dfrom=atoi(warea);
      strncpy(day,period+17,2);
      strncpy(mon,period+14,3);
      strncpy(year,period+10,4);
      conv_month(mon);
      sprintf(warea,"%s%s%s",year,mon,day);
      duntil=atoi(warea);
   }

   sprintf(guard_in,"%s/dansguardian.unsort",tmp);
   sprintf(guard_ou,"%s/dansguardian.log",tmp);

   if(access(DansGuardianConf, R_OK) != 0) {
      sprintf(msg,"Cannot open DansGuardian config file: %s",DansGuardianConf);
      debuga(msg);
      exit(1);
   }

   if((fp_guard=fopen(DansGuardianConf,"r"))==NULL) {
      fprintf(stderr, "SARG: (dansguardian) %s: %s\n",text[8],DansGuardianConf);
      exit(1);
   }

   if((fp_ou=fopen(guard_in,"a"))==NULL) {
      fprintf(stderr, "SARG: (dansguardian) %s: %s\n",text[8],guard_in);
      exit(1);
   }

   while(fgets(buf,sizeof(buf),fp_guard)!=NULL) {
      if(strstr(buf,"\n") != 0)
         buf[strlen(buf)-1]='\0';
      if(strncmp(buf,"#",1) == 0)
         continue;
      if(strstr(buf,"loglocation ") != 0) {
         getword(loglocation,buf,'\'');
         getword(loglocation,buf,'\'');
         break;
      }
   }

   if(debug) {
      strcpy(buf,text[7]);
      getword(urly,buf,' ');
      getword(href,buf,' ');
      sprintf(msg,"%s DansGuardian %s: %s",urly,buf,loglocation);
      debuga(msg);
   }
   
   if((fp_in=fopen(loglocation,"r"))==NULL) {
      fprintf(stderr, "SARG: (dansguardian) %s: %s\n",text[8],loglocation);
      exit(1);
   }
 
   while(fgets(buf,sizeof(buf),fp_in) != NULL) {
      if(strstr(buf," *DENIED* ") == 0)
         continue;
      getword(year,buf,'.');
      getword(mon,buf,'.');
      getword(day,buf,' ');
      getword(hour,buf,' ');
      getword(user,buf,' ');
      getword(ip,buf,' ');
      getword(url,buf,'/');
      getword(url,buf,'/');
      getword(url,buf,' ');
      getword(code1,buf,' ');
      getword(code1,buf,' ');
      getword(code2,buf,' ');
      sprintf(wdata,"%s%02d%s",year,atoi(mon),day);
      idata = atoi(wdata);

      if(strcmp(DansguardianIgnoreDate,"on") == 0) {
         if(idata < dfrom && idata > duntil)
            continue;
      }

      if (strcmp(user,"-") == 0) {
         strcpy(user,ip);
         bzero(ip, 30);
      }
      sprintf(tmp6,"%s %d %s %s %s %s %s\n",user,idata,hour,ip,url,code1,code2);
      fputs(tmp6, fp_ou);
      dansguardian_count++;
   }

   if(fp_in) fclose(fp_in);
   if(fp_guard) fclose(fp_guard);
   if(fp_ou) fclose(fp_ou);

   if(debug) {
      sprintf(msg,"%s: %s",text[54],guard_ou);
      debuga(msg);
   }

   sprintf(tmp6,"sort -k 1,1 -k 2,2 -k 4,4 '%s' -o '%s'",guard_in, guard_ou);
   system(tmp6);
   unlink(guard_in);
   return;
}
