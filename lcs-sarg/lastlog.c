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

void mklastlog()
{

   FILE *fp_in, *fp_ou;
   DIR *dirp;
   struct dirent *direntp;
   char temp[MAXLEN];
   char warea[MAXLEN];
   char ftime[128];
   int  ftot=0;
   time_t t;
   struct tm *local;
   struct stat statb;

   if(strcmp(LastLog,"0") == 0)
      return;

   sprintf(temp,"%slastlog1",outdir);
   if((fp_ou=fopen(temp,"w"))==NULL) {
     fprintf(stderr, "SARG: (lastlog) %s: %s\n",text[9],temp);        
     exit(1);
   }

   dirp = opendir(outdir);
   while ((direntp = readdir( dirp )) != NULL ){
      if(strstr(direntp->d_name,"-") == 0)
         continue;

      sprintf(warea,"%s%s",outdir,direntp->d_name);
      stat(warea,&statb);
      t=statb.st_ctime;
      local = localtime(&t);
      strftime(ftime, 127, "%Y%m%d%H%M%S", local);
      sprintf(buf,"%s %s\n",ftime,direntp->d_name);
      fputs(buf,fp_ou);
      ftot++;
   }

   (void)rewinddir( dirp );
   (void)closedir( dirp );
   fclose(fp_ou);
   
   sprintf(buf,"sort -n -k 1,1 -o '%slastlog' '%s'",outdir,temp);
   system(buf);

   unlink(temp);

   if(ftot<=atoi(LastLog)) {
      sprintf(temp,"%slastlog",outdir);
      if(access(temp, R_OK) == 0)
         unlink(temp);
      return;
   }

   ftot-=atoi(LastLog);

   sprintf(temp,"%slastlog",outdir);
   if((fp_in=fopen(temp,"r"))==NULL) {
     fprintf(stderr, "SARG: (lastlog) %s: %s\n",text[9],temp);        
     exit(1);
   }

   while(fgets(buf,sizeof(buf),fp_in)!=NULL) {
      getword(warea,buf,' ');
      buf[strlen(buf)-1]='\0';
     
      if(ftot) {
         if(debug) {
            sprintf(msg,"%s: %s",text[81],buf);
            debuga(msg);
         }
//         sprintf(temp,"%s%s",outdir,buf);
         sprintf(temp,"rm -r %s%s",outdir,buf);
         system(temp);
         unlink(temp);
         ftot--;
      }
   }

   fclose(fp_in);
   sprintf(temp,"%slastlog",outdir);
   unlink(temp);
 
   return;
}
