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

void totaliza_day(const char *tmp, char *user, int indexonly)
{

   FILE *fp_in, *fp_ou;
      
   char data[20];
   char hora[20];
   char min[20];
   char elap[20];
   char odata[20];
   char ohora[20];
   char oelap[20];
   char hm[20];
   char ohm[20];
   char csort[255];
   char wdirname[MAXLEN];
   char sortout[MAXLEN];
   char arqout[MAXLEN];
   int  regs=0;
   long long int telap=0;
   long long int tused=0;

   if(indexonly) return;
   if(strstr(ReportType,"users_sites") == 0) return;

   sprintf(wdirname,"%s/%s.htmp",tmp,user);
   sprintf(arqout,"%s/%s.day",tmp,user);
   sprintf(sortout,"%s/%s.sort",tmp,user);

   sprintf(csort,"sort -k 1,1 -k 2,2 -o '%s' '%s'",sortout,wdirname);
   system(csort);

   unlink(wdirname);

   if((fp_in=fopen(sortout,"r"))==NULL) {
     fprintf(stderr, "SARG: (totday) %s: %s\n",text[8],sortout);
     exit(1);
   }

   if((fp_ou=fopen(arqout,"w"))==NULL) {
     fprintf(stderr, "SARG: (totday) %s: %s\n",text[8],arqout);
     exit(1);
   }

   while(fgets(buf,sizeof(buf),fp_in)!=NULL) {
      if(strstr(buf,"\n") != 0)
         buf[strlen(buf)-1]='\0';
         
      getword(data,buf,' ');
      getword(hora,buf,':');
      getword(min,buf,':');
      getword(elap,buf,' ');
      strcpy(elap,buf);
      sprintf(hm,"%s%s",hora,min);

      if(!regs) {
         strcpy(odata,data);
         strcpy(ohora,hora);
         strcpy(oelap,elap);
         strcpy(ohm,hm);
         regs++;
      }

      if(strcmp(hora,ohora) != 0) {
         if(tused > telap)
            tused=telap;

         my_lltoa(telap,val1,15);
         sprintf(buf,"%s %s %s\n",odata,ohora,val1);
         fputs(buf, fp_ou);
         strcpy(odata,data);
         strcpy(ohora,hora);
         strcpy(ohm,hm);
         telap=0;
         tused=0;
      }

      if(strcmp(ohm,hm) != 0) {
         tused+=60000;
         strcpy(ohm,hm);
      }

      telap+=my_atoll(elap);
          
   }

   if(tused > telap)
      tused=telap;

   my_lltoa(telap,val1,15);
   sprintf(buf,"%s %s %s\n",data,hora,val1);
   fputs(buf, fp_ou);

   fclose(fp_in);
   fclose(fp_ou);

   unlink(sortout);

   return;

}
