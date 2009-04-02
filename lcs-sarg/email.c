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

int geramail(const char *dirname, int debug, const char *outdir, int userip, char *email, char *TempDir)
{

   FILE *fp_in, *fp_top1, *fp_top2, *fp_top3;
   long long int ttnbytes=0, ttnacc=0, tnacc=0;
   long long int tnbytes=0, ttnelap=0, tnelap=0;
   float perc=0.00;
   float perc2=0.00;
   int posicao=0;
   char olduser[MAXLEN], csort[MAXLEN], period[MAXLEN], arqper[MAXLEN];
   char wger[MAXLEN], top1[MAXLEN], top2[MAXLEN], top3[MAXLEN], user[MAXLEN], nacc[20], nbytes[20], url[1024], preg[MAXLEN], tusr[MAXLEN];
   char ip[MAXLEN], hora[9], data[11], elap[15], user2[MAXLEN], wperc[8], wperc2[8];
   char strip1[MAXLEN], strip2[MAXLEN], strip3[MAXLEN], strip4[MAXLEN], strip5[MAXLEN], strip6[MAXLEN], strip7[MAXLEN];
   char incac[16], oucac[16];
   int totuser=0;
   time_t t;
   struct tm *local;

   strcpy(wger,dirname);
   strcpy(top1,dirname);
   strcpy(top2,dirname);
   strcpy(top3,dirname);
   strcpy(tusr,dirname);
   strcat(wger,"/general");
   strcat(top1,"/top");
   strcat(top2,"/top.tmp");
   strcat(top3,"/report");
   strcat(tusr,"/users");

   if((fp_in=fopen(wger,"r"))==NULL) {
      fprintf(stderr, "SARG: (email) %s: %s\n",text[45],wger);
      exit(1);
   }

   if((fp_top1=fopen(top1,"w"))==NULL) {
      fprintf(stderr, "SARG: (email) %s: %s\n",text[45],top1);
      exit(1);
   }

   if((fp_top2=fopen(top2,"w"))==NULL) {
      fprintf(stderr, "SARG: (email) %s: %s\n",text[45],top2);
      exit(1);
   }

   fscanf(fp_in,"%s",user);
   fscanf(fp_in,"%s",nacc);
   fscanf(fp_in,"%s",nbytes);
   fscanf(fp_in,"%s",url);
   fscanf(fp_in,"%s",ip);
   fscanf(fp_in,"%s",hora);
   fscanf(fp_in,"%s",data);
   fscanf(fp_in,"%s",elap);
   fscanf(fp_in,"%s",incac);
   fscanf(fp_in,"%s",oucac);

   strcpy(olduser,user);
   totuser=1;

   while(!feof(fp_in))
   {

      if(strcmp(olduser,user) != 0)
      {
         if(strcmp(user,"TOTAL") != 0)
            totuser++;

#if defined(__FreeBSD__)
   sprintf(preg,"%s %15qu %15qu %15qu\n",olduser,tnbytes,tnacc,tnelap);
#elif defined(__alpha) || __ALPHA
   sprintf(preg,"%s %15ld %15ld %15ld\n",olduser,tnbytes,tnacc,tnelap);
#else
   sprintf(preg,"%s %15lld %15lld %15lld\n",olduser,tnbytes,tnacc,tnelap);
#endif
         fputs(preg,fp_top2);
	 strcpy(olduser,user);
         ttnbytes=ttnbytes+tnbytes;
	 ttnacc=ttnacc+tnacc;
	 ttnelap=ttnelap+tnelap;
         tnbytes=0;
         tnacc=0;
         tnelap=0;
      }

      tnbytes=tnbytes+atol(nbytes);
      tnacc=tnacc+atol(nacc);
      tnelap=tnelap+atol(elap);

      fscanf(fp_in,"%s",user);
      fscanf(fp_in,"%s",nacc);
      fscanf(fp_in,"%s",nbytes);
      fscanf(fp_in,"%s",url);
      fscanf(fp_in,"%s",ip);
      fscanf(fp_in,"%s",hora);
      fscanf(fp_in,"%s",data);
      fscanf(fp_in,"%s",elap);
      fscanf(fp_in,"%s",incac);
      fscanf(fp_in,"%s",oucac);

      if(strcmp(user,"TOTAL") == 0)
         continue;
   }
#if defined(__FreeBSD__)
   sprintf(preg,"%s %15qu %15qu %15qu\n",olduser,tnbytes,tnacc,tnelap);
#elif defined(__alpha) || __ALPHA
   sprintf(preg,"%s %15ld %15ld %15ld\n",olduser,tnbytes,tnacc,tnelap);
#else
   sprintf(preg,"%s %15lld %15lld %15lld\n",olduser,tnbytes,tnacc,tnelap);
#endif
   fputs(preg,fp_top2);
   ttnbytes=ttnbytes+tnbytes;
   ttnacc=ttnacc+tnacc;
   ttnelap=ttnelap+tnelap;

#if defined(__FreeBSD__)
   sprintf(preg,"TOTAL %15qu %15qu %15qu\n",ttnbytes,ttnacc,ttnelap);
#elif defined(__alpha) || __ALPHA
   sprintf(preg,"TOTAL %15ld %15ld %15ld\n",ttnbytes,ttnacc,ttnelap);
#else
   sprintf(preg,"TOTAL %15lld %15lld %15lld\n",ttnbytes,ttnacc,ttnelap);
#endif

   fclose(fp_in);
   fclose(fp_top2);

   sprintf(csort,"sort -n -T %s -r -k 2,2 -o '%s' '%s'", TempDir, top1, top2);
   system(csort);

   unlink(top2);

   if((fp_top1=fopen(top1,"a"))==NULL) {
      fprintf(stderr, "SARG: (email) %s: %s\n",text[45],top1);
      exit(1);
   }
   fputs(preg,fp_top1);
   fclose(fp_top1);

   if((fp_top1=fopen(top1,"r"))==NULL) {
      fprintf(stderr, "SARG: (email) %s: %s\n",text[45],top1);
      exit(1);
   }

   if((fp_top3=fopen(top3,"w"))==NULL) {
      fprintf(stderr, "SARG: (email) %s: %s\n",text[45],top3);
      exit(1);
   }

 /*
 * Obtem o period
 */

   strcpy(arqper,dirname);
   strcat(arqper,"/period");

   if ((fp_in = fopen(arqper, "r")) == 0){
      fprintf(stderr, "SARG: (email) %s: %s\n",text[45],arqper);
      exit(1);
   }

   fgets(period,sizeof(period),fp_in);
   fclose(fp_in);

   sprintf(strip1,"%s",text[88]);
   strip_latin(strip1);
   sprintf(preg,"%s\n",strip1);
   fputs(preg,fp_top3);

   sprintf(strip1,"%s",text[97]);
   strip_latin(strip1);
   sprintf(preg,"%s\n",strip1);
   fputs(preg,fp_top3);

   sprintf(strip1,"%s",text[89]);
   strip_latin(strip1);
   sprintf(preg,"%s %s\n\n",strip1,period);
   fputs(preg,fp_top3);

   sprintf(strip1,"%s",text[100]);
   strip_latin(strip1);
   sprintf(strip2,"%s",text[98]);
   strip_latin(strip2);
   sprintf(strip3,"%s",text[92]);
   strip_latin(strip3);
   sprintf(strip4,"%s",text[93]);
   strip_latin(strip4);
   sprintf(strip5,"%s",text[94]);
   strip_latin(strip5);
   sprintf(strip6,"%s",text[95]);
   strip_latin(strip6);
   sprintf(strip7,"%s",text[99]);
   strip_latin(strip7);

   sprintf(preg,"%-7s %-20s %-8s %-15s %%%-6s %-10s %-10s %%%-7s\n------- -------------------- -------- --------------- ------- ---------- ---------- -------\n",strip1,strip2,strip3,strip4,strip4,strip5,strip6,strip7);
   fputs(preg,fp_top3);

   fscanf(fp_top1,"%s",user);
   fscanf(fp_top1,"%s",nbytes);
   fscanf(fp_top1,"%s",nacc);
   fscanf(fp_top1,"%s",elap);

   while(!feof(fp_top1))
   {

      if(strstr(user,"_") != 0)
         fixip(user);

      strcpy(user2,user);

      tnbytes=atol(nbytes);

      if(tnbytes) {
         perc=ttnbytes / 100;
         perc=tnbytes / perc;
      } else perc = 0;

      if(atol(elap)) {
         perc2=ttnelap / 100;
         perc2=atol(elap) / perc2;
      } else perc2 = 0;

      posicao++;
      tnelap=atol(elap);

      sprintf(wperc,"%3.2f%%",perc);
      sprintf(wperc2,"%3.2f%%",perc2);

#if defined(__FreeBSD__)
      sprintf(preg,"%7d %20s %8s %15s %7s %10s %10qu %7s%%\n",posicao,user2,nacc,fixnum(tnbytes,1),wperc,buildtime(tnelap),tnelap,wperc2);
#elif defined(__alpha) || __ALPHA
      sprintf(preg,"%7d %20s %8s %15s %7s %10s %10ld %7s%%\n",posicao,user2,nacc,fixnum(tnbytes,1),wperc,buildtime(tnelap),tnelap,wperc2);
#else
      sprintf(preg,"%7d %20s %8s %15s %7s %10s %10lld %7s\n",posicao,user2,nacc,fixnum(tnbytes,1),wperc,buildtime(tnelap),tnelap,wperc2);
#endif

      if(strstr(user,"TOTAL") != 0){
         sprintf(preg,"------- -------------------- -------- --------------- ------- ---------- ---------- -------\n");
         fputs(preg,fp_top3);
#if defined(__FreeBSD__)
         sprintf(preg,"%-7s %20s %8qu %15s %8s %9s %10qu\n",text[107]," ",ttnacc,fixnum(ttnbytes,1)," ",buildtime(ttnelap),ttnelap);
#elif defined(__alpha) || __ALPHA
         sprintf(preg,"%-7s %20s %8ld %15s %8s %9s %10ld\n",text[107]," ",ttnacc,fixnum(ttnbytes,1)," ",buildtime(ttnelap),ttnelap);
#else
         sprintf(preg,"%-7s %20s %8lld %15s %8s %9s %10lld\n",text[107]," ",ttnacc,fixnum(ttnbytes,1)," ",buildtime(ttnelap),ttnelap);
#endif
      }

      fputs(preg,fp_top3);

      fscanf(fp_top1,"%s",user);
      fscanf(fp_top1,"%s",nbytes);
      fscanf(fp_top1,"%s",nacc);
      fscanf(fp_top1,"%s",elap);
   }

   if(ttnbytes) {
      tnbytes=ttnbytes / totuser;
   } else tnbytes=0;

   sprintf(strip1,"%s",text[96]);
   strip_latin(strip1);
#if defined(__FreeBSD__)
   sprintf(preg,"%-7s %20s %8qu %15s %8s %9s %10qu\n",strip1," ",ttnacc/totuser,fixnum(tnbytes,1)," ",buildtime(ttnelap/totuser),ttnelap/totuser);
#elif defined(__alpha) || __ALPHA
   sprintf(preg,"%-7s %20s %8ld %15s %8s %9s %10ld\n",strip1," ",ttnacc/totuser,fixnum(tnbytes,1)," ",buildtime(ttnelap/totuser),ttnelap/totuser);
#else
   sprintf(preg,"%-7s %20s %8lld %15s %8s %9s %10lld\n",strip1," ",ttnacc/totuser,fixnum(tnbytes,1)," ",buildtime(ttnelap/totuser),ttnelap/totuser);
#endif
   fputs(preg,fp_top3);

   fclose(fp_top1);
   unlink(top1);

   t = time(NULL);
   local = localtime(&t);
   sprintf(preg, "\n%s\n", asctime(local));
   fputs(preg,fp_top3);

   fclose(fp_top3);

   if(strcmp(email,"stdout") == 0) {
      if((fp_top3=fopen(top3,"r"))==NULL) {
         fprintf(stderr, "SARG: (email) %s: %s\n",text[45],top3);
         exit(1);
      }

      while(fgets(buf,sizeof(buf),fp_top3)!=NULL)
         printf("%s",buf);
    } else {
      sprintf(buf,"%s -s 'SARG %s, %s' %s <%s",MailUtility,text[55],asctime(local),email,top3);
      system(buf);
   }
 
   sprintf(csort,"rm -r %s/sarg_tmp",TempDir);
   system(csort);

   return (0);
}
