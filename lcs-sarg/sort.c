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

void tmpsort()
{

   DIR *dirp;
   struct dirent *direntp;

   char csort[MAXLEN];
   char arqou[MAXLEN], arqin[MAXLEN], wnome[MAXLEN], wnome2[MAXLEN];
   char field1[10]="2,2";
   char field2[10]="1,1";
   char field3[10]="3,3";
   char order[4]="-r";

   if(indexonly) return;
   if(strstr(ReportType,"users_sites") == 0) return;

   wnome[0]='\0';
   wnome2[0]='\0';

   dirp = opendir(tmp);
   while ((direntp = readdir( dirp )) != NULL ){
      if(strstr(direntp->d_name,".utmp") == 0)
         continue;

      strcpy(wentp,direntp->d_name);
 
      wnome[0]='\0'; 
      striptmp:
      getword(wnome2,wentp,'.');
      strcat(wnome,wnome2);

      if(strcmp(wentp,"utmp") !=0) {
         strcat(wnome,".");
         goto striptmp;
      }

      strcpy(arqou,tmp);
      strcat(arqou,"/");
      strcpy(arqin,arqou);
      strcat(arqou,wnome);
      strcat(arqin,direntp->d_name);

      if(debug) {
         sprintf(msg,"%s: %s",text[54],arqou);
         debuga(msg);
      }

      strup(UserSortField);
      strlow(UserSortOrder);

      if(strcmp(UserSortField,"CONNECT") == 0) {
         strcpy(field1,"1,1");
         strcpy(field2,"2,2");
         strcpy(field3,"3,3");
      }

      if(strcmp(UserSortField,"SITE") == 0) {
         strcpy(field1,"3,3");
         strcpy(field2,"2,2");
         strcpy(field3,"1,1");
      }

      if(strcmp(UserSortField,"TIME") == 0) {
         strcpy(field1,"5,5");
         strcpy(field2,"2,2");
         strcpy(field3,"1,1");
      }

      if(strcmp(UserSortOrder,"normal") == 0)
         order[0]='\0';
        
      strcat(arqou,".txt");
      sprintf(csort,"sort -n -T %s %s -k %s -k %s -k %s -o '%s' '%s'",TempDir,order,field1,field2,field3,arqou,arqin);
      system(csort);
      unlink(arqin);

   }

   (void)rewinddir( dirp );
   (void)closedir( dirp );
   return;
}

void sort_users_log(char *tmp, int debug)
{

   DIR *dirp;
   struct dirent *direntp;
   char csort[MAXLEN];
   char wtmp[MAXLEN];
   char wname2[MAXLEN];
   char wdname[MAXLEN];

   sprintf(wtmp,"%s/sarg",tmp);

   dirp = opendir(wtmp);
   while ( (direntp = readdir( dirp )) != NULL ){
      if(strstr(direntp->d_name,".unsort") == 0)
         continue;
      if(strcmp(direntp->d_name,"denied.log") == 0 || strcmp(direntp->d_name,"authfail.log.unsort") == 0)
         continue;

      strcpy(wdname,direntp->d_name);

      bzero(user, MAXLEN);
      strip_unsort:
      getword(wname2,wdname,'.');
      strcat(user,wname2);

      if(strcmp(wdname,"unsort") !=0) {
         strcat(user,".");
         goto strip_unsort;
      }

      if(debug) {
         sprintf(msg,"%s %s/%s.unsort",text[54],wtmp,user);
         debuga(msg);
      }

      if(strcmp(direntp->d_name,"download.unsort") == 0)
         sprintf(csort,"sort -T %s -k 3,3 -k 1,1 -k 2,2 -k 5,5 -o '%s/%s.log' '%s/%s.unsort'",
					tmp, wtmp, user, wtmp, user);
      else
         sprintf(csort,"sort -T %s -k 5,5 -k 1,1 -k 2,2 -o '%s/%s.log' '%s/%s.unsort'", 
					tmp, wtmp, user, wtmp, user);
      system(csort);
      sprintf(wdname,"%s/%s.unsort",wtmp,user);
      unlink(wdname);
      bzero(user, MAXLEN);

   }
   (void)rewinddir( dirp );
   (void)closedir( dirp );

   return;
}
