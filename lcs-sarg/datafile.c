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

void saverecs(char *dirname, char *user, long long int nacc, char *url, long long int nbytes, char *ip, char *hora, char *dia, long long int nelap, long long int incache, long long int oucache);
void grava_SmartFilter(char *dirname, char *user, char *ip, char *data, char *hora, char *url, char *smart);

void data_file(char *tmp)
{

   FILE *fp_in;

   char accdia[11], acchora[9], accuser[MAXLEN], accip[MAXLEN], accurl[MAXLEN], accbytes[12], accelap[10];
   char oldaccdia[11], oldacchora[9], oldaccip[MAXLEN];
   char dirname[MAXLEN], wdirname[MAXLEN], oldurl[MAXLEN], oldaccuser[MAXLEN];
   char olduser[MAXLEN], oldmsg[50], acccode[50], oldaccelap[10], oldacccode[50];
   char ipantes[MAXLEN], nameantes[MAXLEN]; 
   char accsmart[MAXLEN];
   char Msg[MAXLEN];
   char wcrc[50];
   char crc2[50];
   char wdname[MAXLEN];
   char wname2[MAXLEN];
   DIR *dirp;
   struct dirent *direntp;
   long long int nbytes=0; 
   long long int nelap=0; 
   long long int nacc=0;
   long long int rtotal=0;
   long long int incache=0;
   long long int oucache=0;

   ipantes[0]='\0';
   nameantes[0]='\0';

   olduser[0]='\0';
   strncat(tmp,"/sarg",5);

   dirp = opendir(tmp);
   while ( (direntp = readdir( dirp )) != NULL ) {
      if(strstr(direntp->d_name,".log") == 0)
         continue;
      sprintf(tmp3,"%s/%s",tmp,direntp->d_name);

      if((fp_in=fopen(tmp3,"r"))==NULL){
         fprintf(stderr, "SARG: (datafile) %s: %s\n",text[45],tmp);
         exit(1);
      }
      strcpy(wdname,direntp->d_name);
      strip_prefix:
      getword(wname2,wdname,'.');
      strcat(user,wname2);

      ttopen=0;
      while(fgets(buf,sizeof(buf),fp_in)!=NULL) {
         getword(accdia,buf,' ');
         getword(acchora,buf,' ');
         getword(accuser,buf,' ');
         getword(accip,buf,' ');
         getword(accurl,buf,' ');
         getword(accbytes,buf,' ');
         getword(acccode,buf,' ');
         getword(accelap,buf,' ');
         getword(accsmart,buf,' ');
         getword(accsmart,buf,'"');
   
         if(strcmp(Ip2Name,"yes") == 0) {
            if(strcmp(accip,ipantes) != 0) {
               strcpy(ipantes,accip);
               ip2name(accip);
               strcpy(nameantes,accip);
            } 
	    else strcpy(accip,nameantes);
         }
   
         if(!rtotal){
            strcpy(oldurl,accurl);
            strcpy(oldacccode,acccode);
            strcpy(oldaccelap,accelap);
            strcpy(oldaccuser,accuser);
            strcpy(oldaccip,accip);
            strcpy(oldaccdia,accdia);
            strcpy(oldacchora,acchora);
            rtotal++;
         }
   
         if(strcmp(oldurl,accurl) != 0 || strcmp(oldaccuser,accuser) != 0){
            strcpy(oldmsg,"OK");
            if(strstr(oldacccode,"DENIED") != 0) strcpy(oldmsg,text[46]);
            strcpy(wdirname,dirname);
            gravatmp(oldaccuser,wdirname,oldurl,nacc,nbytes,oldmsg,nelap,indexonly,incache,oucache);
            strcpy(wdirname,dirname);
            saverecs(wdirname,oldaccuser,nacc,oldurl,nbytes,oldaccip,oldacchora,oldaccdia,nelap,incache,oucache);
            nacc=0;
            nbytes=0;
            nelap=0;
            incache=0;
            oucache=0;
            if(strcmp(oldaccuser,accuser) != 0) ind2=0;
         }

         nacc++;
         nbytes+=my_atoll(accbytes);
         nelap+=my_atoll(accelap);
   
         strcpy(wcrc,acccode);
         getword(crc2,wcrc,'/');

         if(strstr(crc2,"MISS") != 0) oucache+=my_atoll(accbytes);
         else incache+=my_atoll(accbytes);
   
         strcpy(oldurl,accurl);
         strcpy(oldaccuser,accuser);
         strcpy(oldacccode,acccode);
         strcpy(oldaccelap,accelap);
         strcpy(oldaccip,accip);
         strcpy(oldaccdia,accdia);
         strcpy(oldacchora,acchora);
      }
   
      fclose(fp_in);
}
   
   (void)closedir( dirp );
   if(debug) {
      sprintf(Msg,"Datafile %s successfully",DataFile);
      debuga(Msg);
   }
}

void saverecs(char *dirname, char *user, long long int nacc, char *url, long long int nbytes, char *ip, char *hora, char *dia, long long int nelap, long long int incache, long long int oucache)
{

   FILE *fp_ou;
   char reg[MAXLEN];

   if((fp_ou=fopen(DataFile,"a"))==NULL){
      fprintf(stderr, "SARG: (datafile) %s: %s\n",text[45],DataFile);
      exit(1);
   }

   my_lltoa(nacc,val1,0);
   my_lltoa(nbytes,val2,0);
   my_lltoa(nelap,val3,0);
   my_lltoa(incache,val4,0);
   my_lltoa(oucache,val5,0);

   if(strstr(DataFileFields,"user") != 0) {
      strcpy(reg,user);
      strncat(reg,DataFileDelimiter,1);
   }
   if(strstr(DataFileFields,"date") != 0) {
      strncat(reg,dia,strlen(dia));
      strncat(reg,DataFileDelimiter,1);
   }
   if(strstr(DataFileFields,"time") != 0) {
      strncat(reg,hora,strlen(hora));
      strncat(reg,DataFileDelimiter,1);
   }
   if(strstr(DataFileFields,"url") != 0) {
      strcpy(name,url);
      if (strcmp(DataFileUrl,"ip") == 0) name2ip(name);
      strncat(reg,name,strlen(name));
      strncat(reg,DataFileDelimiter,1);
   }
   if(strstr(DataFileFields,"connect") != 0) {
      strncat(reg,val1,strlen(val1));
      strncat(reg,DataFileDelimiter,1);
   }
   if(strstr(DataFileFields,"bytes") != 0) {
      strncat(reg,val2,strlen(val2));
      strncat(reg,DataFileDelimiter,1);
   }
   if(strstr(DataFileFields,"in_cache") != 0) {
      strncat(reg,val4,strlen(val4));
      strncat(reg,DataFileDelimiter,1);
   }
   if(strstr(DataFileFields,"out_cache") != 0) {
      strncat(reg,val5,strlen(val5));
      strncat(reg,DataFileDelimiter,1);
   }
   if(strstr(DataFileFields,"elapsed") != 0) {
      strncat(reg,val3,strlen(val3));
      strncat(reg,DataFileDelimiter,1);
   }

   reg[strlen(reg)-1]='\n';
   fputs(reg,fp_ou);

   fclose(fp_ou);
}
