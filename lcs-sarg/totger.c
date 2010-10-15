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

int totalger(const char *dirname, int debug, const char *outdir)

{


   FILE *fp_in, *fp_ou;
   long long int tnacc=0;
   long long int tnbytes=0;
   long long int telap=0;
   long long int tincache=0, toucache=0;
   char wger[MAXLEN], user[MAXLEN], nacc[10], nbytes[10], url[1024];
   char ip[MAXLEN], hora[9], data[11], elap[15];
   char incac[15], oucac[15];

   strcpy(wger,dirname);
   strcat(wger,"/general");

   if((fp_in=fopen(wger,"r"))==NULL) {
      fprintf(stderr, "SARG: (totger) %s: %s\n",text[45],wger);
      exit(1);
   }

   fscanf(fp_in,"%s%s%s%s%s%s%s%s%s%s",user,nacc,nbytes,url,ip,hora,data,elap,incac,oucac);

   while(!feof(fp_in))
   {
      tnacc+=my_atoll(nacc);
      tnbytes+=my_atoll(nbytes);
      telap+=my_atoll(elap);
      tincache+=my_atoll(incac);
      toucache+=my_atoll(oucac);

      fscanf(fp_in,"%s%s%s%s%s%s%s%s%s%s",user,nacc,nbytes,url,ip,hora,data,elap,incac,oucac);
   }

   fclose(fp_in);

   strcpy(wger,dirname);
   strcat(wger,"/general");

   if((fp_ou=fopen(wger,"a"))==NULL) {
    fprintf(stderr, "SARG: (totger) %s: %s\n",text[45],wger);
    exit(1);
   }

   url[0]='\0';

   my_lltoa(tnacc,val1,15);
   my_lltoa(tnbytes,val2,15);
   my_lltoa(telap,val3,15);
   my_lltoa(tincache,val4,15);
   my_lltoa(toucache,val5,15);
   sprintf(url,"TOTAL %s %s %s %s %s\n",val1,val2,val3,val4,val5);
   fputs(url,fp_ou);
   fclose(fp_ou);

   return (0);
}
