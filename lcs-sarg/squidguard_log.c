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

void read_log(char *wentp, FILE *fp_ou)
{
   FILE *fp_in = NULL;
   char bufbsf[255];
   char leks[5], sep[2], res[MAXLEN];
   char mon[10], hour[15];
   char list[MAXLEN];
   char wdata[127];
   int  idata=0;

   if(debug) {
      strcpy(buf,text[7]);
      getword(urly,buf,' ');
      getword(href,buf,' ');
      sprintf(msg,"%s squidGuard %s: %s",urly,buf,wentp);
      debuga(msg);
   }
   
   if ((fp_in=fopen(wentp,"r"))==NULL) {
      fprintf(stderr, "SARG: (squidguard) %s: %s\n",text[8],wentp);
      exit(1);
   }
   
   while (fgets(buf,sizeof(buf),fp_in) != NULL) {
      if(strlen(SquidGuardLogFormat) > 0) {
         strcpy(bufbsf,SquidGuardLogFormat);
         leks[0]='\0';
         getword(leks,bufbsf,'#');
         while(strcmp(leks,"end") != 0) {
            getword(leks,bufbsf,'#');
            getword(sep,bufbsf,'#');
            if(strcmp(leks,"end") != 0) {
               getword(res,buf,sep[0]);
               if(strcmp(leks,"year") == 0)
                  strcpy(year,res);
               else if(strcmp(leks,"year") == 0)
                  strcpy(year,res);
               else if(strcmp(leks,"mon") == 0)
                  strcpy(mon,res);
               else if(strcmp(leks,"day") == 0)
                  strcpy(day,res);
               else if(strcmp(leks,"hour") == 0)
                  strcpy(hour,res);
               else if(strcmp(leks,"list") == 0)
                  strcpy(list,res);
               else if(strcmp(leks,"ip") == 0)
                  strcpy(ip,res);
               else if(strcmp(leks,"user") == 0)
                  strcpy(user,res);
               else if(strcmp(leks,"url") == 0)
                  strcpy(url,res);
            }
         }
      } else {
         getword(year,buf,'-');
         getword(mon,buf,'-');
         getword(day,buf,' ');
         getword(hour,buf,' ');
         getword(list,buf,'/');
         getword(list,buf,'/');
         getword(tmp5,buf,'/');
         getword(tmp5,buf,'/');
         getword(url,buf,'/');
         getword(ip,buf,' ');
         getword(ip,buf,'/');
         getword(user,buf,' ');
         getword(user,buf,' ');
      }

      sprintf(warea,"%s%s%s",year,mon,day);
      sprintf(wdata,"%s%s%s",year,mon,day);
      idata = atoi(wdata);

      if(strcmp(SquidguardIgnoreDate,"on") == 0) {
         if(idata < dfrom && idata > duntil)
            continue;
      }

      if (strcmp(user,"-") == 0) {
         strcpy(user,ip);
         bzero(ip, 30);
      }
      sprintf(tmp6,"%s %s%s%s %s %s %s %s\n",user,year,mon,day,hour,ip,url,list);
      fputs(tmp6, fp_ou);
      squidguard_count++;
   }
   if (fp_in) fclose(fp_in);
   return;
}


int squidguard_log()
{

   FILE *fp_ou = NULL, *fp_guard = NULL;
   char guard_in[MAXLEN];
   char guard_ou[MAXLEN];
   char logdir[MAXLEN];
   char year[10], day[10], mon[10];
   char user[MAXLEN];
   char ip[30];
   int  x, y;

   if(strlen(SquidGuardConf) < 1 && strlen(SquidGuardLogAlternate) < 1)
     return;

   if (strlen(SquidGuardLogAlternate) > 0)
      SquidGuardConf[0]='\0';

   sprintf(guard_in,"%s/squidguard.unsort",tmp);
   sprintf(guard_ou,"%s/squidguard.log",tmp);
   if((fp_ou=fopen(guard_in,"a"))==NULL) {
      fprintf(stderr, "SARG: (squidguard) %s: %s\n",text[8],guard_in);
      exit(1);
   }

   bzero(day, 3);
   bzero(mon, 4);
   bzero(year, 5);

   if(strcmp(SquidguardIgnoreDate,"on") == 0) {
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
   }

   if(strlen(SquidGuardConf) > 0) {
      if(access(SquidGuardConf, R_OK) != 0) {
         sprintf(msg,"Cannot open squidGuard config file: %s",SquidGuardConf);
         debuga(msg);
         exit(1);
      }

      if((fp_guard=fopen(SquidGuardConf,"r"))==NULL) {
         fprintf(stderr, "SARG: (squidguard) %s: %s\n",text[8],SquidGuardConf);
         exit(1);
      }
   
      while(fgets(buf,sizeof(buf),fp_guard)!=NULL) {
         if(strstr(buf,"\n") != 0)
            buf[strlen(buf)-1]='\0';
         if(strstr(buf,"logdir ") != 0) {
            getword(logdir,buf,' ');
            getword(logdir,buf,' ');
         }
         if((str=(char *) strstr(buf, "log")) != (char *) NULL )  {
            str=str+3;
            str2[0]='\0';
            y=0;
            for (x=0; x<=strlen(str); x++) {
               if (str[x] != ' ' && str[x] != '\t') {
                  str2[y] = str[x];
                  y++;
               }
            }
            if(strchr(str2,' ') != 0) {
               getword(warea,str2,' ');
               strcpy(str2,warea);
            }
            if(strchr(str2,'#') != 0) {
               getword(warea,str2,'#');
               strcpy(str2,warea);
            }
            sprintf(wentp,"%s/%s",logdir,str2);
            read_log(wentp,fp_ou);
         }
      }
   } else {
      sprintf(wentp,"%s",SquidGuardLogAlternate);
      read_log(wentp,fp_ou);
   }

   if (fp_guard) fclose(fp_guard);
   if (fp_ou) fclose(fp_ou);

   if(debug) {
      sprintf(msg,"%s: %s",text[54],guard_ou);
      debuga(msg);
   }

   sprintf(tmp6,"sort -k 1,1 -k 2,2 -k 4,4 '%s' -o '%s'",guard_in, guard_ou);
   system(tmp6);

   unlink(guard_in);
   return;
}
