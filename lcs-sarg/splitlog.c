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

void splitlog(char *arq, char *df, int dfrom, int duntil, char *convert)
{

   FILE *fp_in;
   char buf[MAXLEN];
   char data[30];
   char dia[11];
   char hora[9];
   char wdata[20];
   time_t tt;
   int idata=0;
   struct tm *t;

   if(arq[0] == '\0')
      strcpy(arq,"/usr/local/squid/logs/access.log");

   if((fp_in=fopen(arq,"r"))==NULL) {
      fprintf(stderr, "SARG: (splitlog) %s: %s\n",text[8],arq);
      exit(1);
   }

   while(fgets(buf,sizeof(buf),fp_in)!=NULL) {
      getword(data,buf,' ');
      tt=atoi(data);
      t=localtime(&tt);

      if(dfrom) {
         strftime(wdata, 127, "%Y%m%d", t);
         idata=atoi(wdata);
	 if(idata < dfrom || idata > duntil)
         continue;
      }

      if(strcmp(convert,"onvert") != 0) {
         printf("%s %s",data,buf);
         continue;
      }

      if(strncmp(df,"e",1) == 0)
         strftime(dia, 127, "%d/%m/%Y", t);
       else
         strftime(dia, 127, "%m/%d/%Y", t);

      sprintf(hora,"%02d:%02d:%02d",t->tm_hour,t->tm_min,t->tm_sec);
      printf("%s %s %s",dia,hora,buf);
   }

   fclose(fp_in);
}
