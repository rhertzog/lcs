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

int topuser()
{

   FILE *fp_in = NULL, *fp_ou = NULL, *fp_top1 = NULL, *fp_top2 = NULL, *fp_top3 = NULL;
   long long int ttnbytes=0, ttnacc=0, tnacc=0;
   long long int tnbytes=0, ttnelap=0, tnelap=0;
   long long int tnincache=0, tnoucache=0, ttnincache=0, ttnoucache=0;
   float perc=0.00;
   float perc2=0.00;
   float inperc=0.00, ouperc=0.00;
   int posicao=0;
   char olduser[MAXLEN], csort[MAXLEN], period[MAXLEN], arqper[MAXLEN];
   char wger[MAXLEN], top1[MAXLEN], top2[MAXLEN], top3[MAXLEN];
   char user[MAXLEN], nacc[20], nbytes[20], preg[MAXLEN], tusr[MAXLEN];
   char ip[MAXLEN], time[30], date[30], elap[30], incac[30], oucac[30];
   char ipantes[MAXLEN], nameantes[MAXLEN];
   char sfield[10]="2,2";
   char order[255]="-r";
   char wheader[512]="";
   int  totuser=0;
   int  topcount=0;
   char *s;

   ipantes[0]='\0';
   nameantes[0]='\0';

   strcpy(wger,dirname);
   strcpy(top1,dirname);
   strcpy(top2,dirname);
   strcpy(top3,dirname);
   strcpy(tusr,dirname);
   strcat(wger,"/general");
   strcat(top1,"/top");
   strcat(top2,"/top.tmp");
   strcat(tusr,"/users");
   strcat(top3,"/index.php");

   if((fp_in=fopen(wger,"r"))==NULL) {
    fprintf(stderr, "SARG: (topuser) %s: %s\n",text[45],wger);
    exit(1);
   }

   if((fp_top2=fopen(top2,"w"))==NULL) {
    fprintf(stderr, "SARG: (topuser) %s: %s\n",text[45],top2);
    exit(1);
   }

   fscanf(fp_in,"%s%s%s%s%s%s%s%s%s%s",user,nacc,nbytes,url,ip,time,date,elap,incac,oucac);

   strcpy(olduser,user);
   totuser=1;

   while(!feof(fp_in)) {
      if(strcmp(olduser,user) != 0) {
         if(strcmp(user,"TOTAL") != 0)
            totuser++;

         my_lltoa(tnbytes,val1,15);
         my_lltoa(tnacc,val2,15);
         my_lltoa(tnelap,val3,15);
         my_lltoa(tnincache,val4,15);
         my_lltoa(tnoucache,val5,15);
         sprintf(preg,"%s %s %s %s %s %s\n",olduser,val1,val2,val3,val4,val5);
         fputs(preg,fp_top2);

	 strcpy(olduser,user);
         ttnbytes+=tnbytes;
	 ttnacc+=tnacc;
	 ttnelap+=tnelap;
         ttnincache+=tnincache;
         ttnoucache+=tnoucache;
         tnbytes=0;
         tnacc=0;
         tnelap=0; 
         tnincache=0;
         tnoucache=0;
      }

      tnbytes+=my_atoll(nbytes);
      tnacc+=my_atoll(nacc);
      tnelap+=my_atoll(elap);
      tnincache+=my_atoll(incac);
      tnoucache+=my_atoll(oucac);

      fscanf(fp_in,"%s%s%s%s%s%s%s%s%s%s",user,nacc,nbytes,url,ip,time,date,elap,incac,oucac);

      if(strcmp(user,"TOTAL") == 0)
         continue;
   }

   my_lltoa(tnbytes,val1,15);
   my_lltoa(tnacc,val2,15);
   my_lltoa(tnelap,val3,15);
   my_lltoa(tnincache,val4,15);
   my_lltoa(tnoucache,val5,15);
   sprintf(preg,"%s %s %s %s %s %s\n",olduser,val1,val2,val3,val4,val5);
   fputs(preg,fp_top2);

   ttnbytes+=tnbytes;
   ttnacc+=tnacc;
   ttnelap+=tnelap;
   ttnincache+=tnincache;
   ttnoucache+=tnoucache;

   my_lltoa(ttnbytes,val1,15);
   my_lltoa(ttnacc,val2,15);
   my_lltoa(ttnelap,val3,15);
   my_lltoa(ttnincache,val4,15);
   my_lltoa(ttnoucache,val5,15);
   sprintf(preg,"TOTAL %s %s %s %s %s\n",val1,val2,val3,val4,val5);
   if (fp_in) fclose(fp_in);
   if (fp_top2) fclose(fp_top2);

   strup(TopuserSortField);
   strlow(TopuserSortOrder);

   if(strcmp(TopuserSortField,"USER") == 0)
      strcpy(sfield,"1,1");

   if(strcmp(TopuserSortField,"CONNECT") == 0)
      strcpy(sfield,"3,3");

   if(strcmp(TopuserSortField,"TIME") == 0)
      strcpy(sfield,"4,4");

   if(strcmp(TopuserSortOrder,"normal") == 0)
      order[0]='\0';

   sprintf(csort,"sort -n -T %s %s -k %s -o '%s' '%s'", TempDir, order, sfield, top1, top2);
   system(csort);

   unlink(top2);

   if((fp_top1=fopen(top1,"a"))==NULL) {
    fprintf(stderr, "SARG: (topuser) %s: %s\n",text[45],top1);
    exit(1);
   }
   fputs(preg,fp_top1);
   fclose(fp_top1);

   if((fp_top1=fopen(top1,"r"))==NULL) {
      fprintf(stderr, "SARG: (topuser) %s: %s\n",text[45],top1);
      exit(1);
   }

   if((fp_top3=fopen(top3,"w"))==NULL) {
      fprintf(stderr, "SARG: (topuser) %s: %s\n",text[45],top3);
      exit(1);
   }

 /*
 * get period
 */

   strcpy(arqper,dirname);
   strcat(arqper,"/period");

   if ((fp_in = fopen(arqper, "r")) == 0) {
      fprintf(stderr, "SARG: (topuser) %s: %s\n",text[45],arqper);
      exit(1);
   }

   fgets(period,sizeof(period),fp_in);
   fclose(fp_in);
      /* LCS */
      fputs("<?php\n",fp_top3);
      fputs("\n",fp_top3);
      fputs("include \"/var/www/lcs/includes/headerauth.inc.php\";\n",fp_top3);
      fputs("include \"/var/www/Annu/includes/ldap.inc.php\";\n",fp_top3);
      fputs("include \"/var/www/Annu/includes/ihm.inc.php\";\n",fp_top3);
      fputs("\n",fp_top3);
      fputs("list ($idpers,$login)= isauth();\n",fp_top3);
      fputs("if ($idpers == \"0\") header(\"Location:$urlauth\");\n",fp_top3);
      fputs("?>\n",fp_top3);

   fprintf(fp_top3, "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">\n<html>\n<head>\n  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=%s\">\n",CharSet);
   fputs("</head>\n",fp_top3);
   css(fp_top3);
   fprintf(fp_top3,"<body class=\"body\">");
   if(strlen(LogoImage) > 0) fprintf(fp_top3, "<center><table cellpadding=\"0\" cellspacing=\"0\">\n<tr><th class=\"logo\"><img src='%s' border=0 align=absmiddle width=%s height=%s>&nbsp;%s</th></tr>\n<tr><td height=\"5\"></td></tr>\n</table>\n",LogoImage,Width,Height,LogoText);

   if(strcmp(IndexTree,"date") == 0)
      show_sarg(fp_top3, "../../..");
   else
      show_sarg(fp_top3, "..");
   fputs("<center><table cellpadding=\"0\" cellspacing=\"0\">\n",fp_top3);
   fprintf(fp_top3,"<tr><th align=\"center\" class=\"title\">%s</th></tr>\n",Title);

   fprintf(fp_top3,"<tr><td class=\"header3\">%s: %s</td></tr>\n",text[89],period);
   strcat(wheader,(char *)text[104]);
   strcat(wheader,": ");
   strcat(wheader,TopuserSortField);
   strcat(wheader,", ");
   strcat(wheader,TopuserSortOrder);
   fprintf(fp_top3,"<tr><td class=\"header3\">%s</td></tr>\n",wheader);
   fprintf(fp_top3,"<tr><th class=\"header3\">%s</th></tr>\n",text[137]);

   fputs("</table></center>\n",fp_top3);
   fputs("<center><table cellpadding=\"1\" cellspacing=\"2\">\n",fp_top3);
   fputs("<tr><td><br></td><td></td></tr>\n",fp_top3);

   if(strstr(ReportType,"topsites") != 0 && strcmp(Privacy,"yes") != 0) fprintf(fp_top3,"<tr><td class=\"link\" colspan=11><a href=\"topsites.php\"><font class=\"link\">%s</font></a><font class=\"text\"></font></td></tr>\n",text[119]);
   if(strstr(ReportType,"sites_users") != 0 && strcmp(Privacy,"yes") != 0) fprintf(fp_top3,"<tr><td class=\"link\" colspan=11><a href=\"siteuser.php\"><font class=\"link\">%s</font></a><font class=\"text\"></font></td></tr>\n",text[85]);
   if(dansguardian_count) fprintf(fp_top3,"<tr><td class=\"link\" colspan=11><a href=\"dansguardian.php\"><font class=\"link\">%s</font></a><font class=\"text\"></font></td></tr>\n",text[128]);
   if(squidguard_count) fprintf(fp_top3,"<tr><td class=\"link\" colspan=11><a href=\"squidguard.php\"><font class=\"link\">%s</font></a><font class=\"text\"></font></td></tr>\n",text[122]);
   if (strstr(ReportType,"downloads") != 0 && download_count && strcmp(Privacy,"yes") != 0) fprintf(fp_top3,"<tr><td class=\"link\" colspan=11><a href=\"download.php\"><font class=\"link\">%s</font></a><font class=\"text\"></font></td></tr>\n",text[125]);
   if (strstr(ReportType,"denied") != 0 && denied_count && strcmp(Privacy,"yes") != 0) fprintf(fp_top3,"<tr><td class=\"link\" colspan=11><a href=\"denied.php\"><font class=\"link\">%s</font></a><font class=\"text\"></font></td></tr>\n",text[118]);
   if (strstr(ReportType,"auth_failures") != 0 && authfail_count && strcmp(Privacy,"yes") != 0) fprintf(fp_top3,"<tr><td class=\"link\" colspan=11><a href=\"authfail.php\"><font class=\"link\">%s</font></a><font class=\"text\"></font></td></tr>\n",text[117]);
   if(smartfilter) fprintf(fp_top3,"<tr><td class=\"link\" colspan=11><a href=\"smartfilter.php\"><font class=\"link\">%s</font></a><font class=\"text\"></font></td></tr>\n",text[116]);
   fputs("<tr><td></td></tr>\n",fp_top3);

   if (strstr(ReportType,"topuser") == 0) {
      fputs("</body>\n</html>\n",fp_top3);
      if (fp_top3) fclose (fp_top3);
      return;
   }
   strcpy(val1,text[100]);
   strcpy(val2,text[98]);
   strcpy(val3,text[92]);
   strcpy(val4,text[93]);
   sprintf(val11,"%%%s",text[93]);
   sprintf(val5,"%s-%s-%s",text[113],text[114],text[112]);
   strcpy(val6,text[94]);
   strcpy(val7,text[95]);
   sprintf(val8,"%%%s",text[99]);
   strcpy(val9,"colspan=2");
   bzero(val10, 255);
 
   strcpy(hbc1,"class=\"header\"");
   strcpy(hbc2,"class=\"header\"");
   strcpy(hbc3,"class=\"header\"");
   strcpy(hbc4,"class=\"header\"");
   strcpy(hbc5,"class=\"header3\"");
   strcpy(hbc6,"class=\"header\"");
   strcpy(hbc7,"class=\"header\"");
   strcpy(hbc8,"class=\"header\"");
   strcpy(hbc9,"class=\"header\"");
   strcpy(hbc10,"class=\"header\"");

   if(strstr(TopUserFields,"NUM") == 0) {
      bzero(val1, 255);
      bzero(hbc1, 30);
   }
   if(strstr(TopUserFields,"USERID") == 0) {
      bzero(val2, 255);
      bzero(hbc2, 30);
   }
   if(strstr(TopUserFields,"CONNECT") == 0) {
      bzero(val3, 255);
      bzero(hbc3, 30);
   }
   if(strstr(TopUserFields,"BYTES") == 0) {
      bzero(val4, 255);
      bzero(hbc4, 30);
   }
   if(strstr(TopUserFields,"SETYB") == 0) {
      bzero(val11, 255);
      bzero(hbc9, 30);
   }
   if(strstr(TopUserFields,"IN-CACHE-OUT") == 0) {
      bzero(val5, 255);
      bzero(hbc5, 30);
      bzero(val9, 255);
      strcpy(val10,"<td></td>");
   }
   if(strstr(TopUserFields,"USED_TIME") == 0) {
      bzero(val6, 255);
      bzero(hbc6, 30);
   }
   if(strstr(TopUserFields,"MILISEC") == 0) { 
      bzero(val7, 255);
      bzero(hbc7, 30);
   }
   if(strstr(TopUserFields,"%TIME") == 0) {
      bzero(val8, 255);
      bzero(hbc8, 30);
   }

   fprintf(fp_top3,"<tr><th %s>%s</th><th %s></th><th %s>%s</th><th %s>%s</th><th %s>%s</th><th %s>%s</th><th %s %s>%s%s</th><th %s>%s</th><th %s>%s</th><th %s>%s</th></tr>\n",hbc1,val1,hbc1,hbc2,val2,hbc3,val3,hbc4,val4,hbc9,val11,hbc5,val9,val5,val10,hbc6,val6,hbc7,val7,hbc8,val8);

   fscanf(fp_top1,"%s%s%s%s%s%s",user,nbytes,nacc,elap,incac,oucac);

   while(!feof(fp_top1)) {
      if(atoi(TopUsersNum) > 0 && topcount >= atoi(TopUsersNum)) goto final;
      strcpy(user2,user);
      tnbytes=my_atoll(nbytes);

      if(tnbytes) {
         perc=tnbytes * 100;
         perc=perc / ttnbytes;
      } else perc = 0;

      if(atol(elap)) {
         perc2=atol(elap);
         perc2=((perc2 * 100) / ttnelap);
      } else perc2 = 0;

      if(atol(incac)) {
         inperc=atol(incac);
         inperc=((inperc * 100) / tnbytes);
      } else inperc = 0;

      if(atol(oucac)) {
         ouperc=atol(oucac);
         ouperc=((ouperc * 100) / tnbytes);
      } else ouperc = 0;

      strcpy(href," ");
      strcpy(href2," ");
      strcpy(href3," ");

      if(strcmp(user,"TOTAL") != 0){
         sprintf(href,"<a href='%s/",user);
         sprintf(href2,"<a href='%s/d",user);
         sprintf(href3,"<a href='%s/graph_day.png'>",user);
         strcat(href,user);
         strcat(href2,user);
         strcat(href,".php'>");
         strcat(href2,".php'>");
      }

      if(strcmp(Graphs,"yes") != 0)
         bzero(href3,MAXLEN);

      posicao++;

      tnelap=my_atoll(elap);

      if(userip) {
         fixip(user2);
         if(strcmp(Ip2Name,"yes") == 0) {
            if(strcmp(user2,ipantes) != 0) {
               strcpy(ipantes,user2);
               ip2name(user2);
               strcpy(nameantes,user2);
            } else strcpy(user2,nameantes);
         }
      }
    
      if(strstr(ReportType,"date_time") != 0) {
         sprintf(ltext110,"%s",text[110]);
         if(ltext110){
            for(s=ltext110; *s; ++s)
               *s=tolower(*s);
         }
      } else {
         bzero(href2, MAXLEN);
         bzero(ltext110, 50);
         sprintf(val1,"%s/d%s.php",dirname,user);
	 unlink(val1);
      }

//      if(UserTabFile[0] != '\0' && strstr(user2,".") != 0) {
      if(UserTabFile[0] != '\0') {
         sprintf(warea,":%s:",user2);
         if((str=(char *) strstr(userfile,warea)) != (char *) NULL ) {
            z1=0;
            str2=(char *) strstr(str+1,":");
            str2++;
            bzero(name, MAXLEN);
            while(str2[z1] != ':') {
               name[z1]=str2[z1];
               z1++;
            }
         } else strcpy(name,user2);
      } else strcpy(name,user2);

      if((strcmp(Ip2Name,"yes") == 0) && 
        ((str=(char *) strstr(name, ".")) != (char *) NULL) && 
	((str=(char *) strstr(str+1, ".")) != (char *) NULL))
               ip2name(name);

      twork=my_atoll(nacc);
      my_lltoa(twork,nacc,0);
      sprintf(wwork1,"%s",fixnum(twork,1));
      sprintf(wwork2,"%s",fixnum(tnbytes,1));
      sprintf(wwork3,"%s",fixnum(tnelap,1));

      sprintf(val1,"%d",posicao);
#ifdef HAVE_GD
      sprintf(val2,"%s<img src=\"%s/graph.png\" border=\"0\" title=\"%s\"></a>&nbsp;%s<img src=\"%s/datetime.png\" border=\"0\" title=\"%s %s\">\n",href3,ImageFile,text[126],href2,ImageFile,ltext110,text[55]);
#else
      sprintf(val2,"%s<img src=\"%s/datetime.png\" border=\"0\" title=\"%s\">\n",href2,ImageFile,ltext110);
#endif

      sprintf(val3,"%3.2f%%",perc);
      sprintf(val4,"%3.2f%%",inperc);
      sprintf(val5,"%3.2f%%",ouperc);
      sprintf(val6,"%s",buildtime(tnelap));
      sprintf(val7,"%3.2f%%",perc2);

      strcpy(hbc1,"class=\"data\"");
      strcpy(hbc2,"class=\"data2\"");
      strcpy(hbc3,"class=\"data\"");
      strcpy(hbc4,"class=\"data\"");
      strcpy(hbc5,"class=\"data\"");
      strcpy(hbc6,"class=\"data\"");
      strcpy(hbc7,"class=\"data\"");
      strcpy(hbc8,"class=\"data\"");
      strcpy(hbc9,"class=\"data\"");
      strcpy(hbc10,"class=\"data\"");

      if(strstr(TopUserFields,"NUM") == 0) {
         bzero(val1, 255);
         bzero(hbc1, 30);
      }
      if(strstr(TopUserFields,"USERID") == 0) {
         bzero(val2, 255);
         bzero(hbc2, 30);
      }
      if(strstr(TopUserFields,"CONNECT") == 0) {
         bzero(wwork1, 255);
         bzero(hbc3, 30);
      }
      if(strstr(TopUserFields,"BYTES") == 0) { 
         bzero(wwork2, 255);
         bzero(hbc4, 30);
      }
      if(strstr(TopUserFields,"SETYB") == 0) {
         bzero(val3, 255);
         bzero(hbc5, 30);
      }

      if(strstr(TopUserFields,"IN-CACHE-OUT") == 0) {
         bzero(val4, 255);
         bzero(hbc6, 30);
      }
      if(strstr(TopUserFields,"IN-CACHE-OUT") == 0) {
         bzero(val5, 255);
         bzero(hbc7, 30);
      }
      if(strstr(TopUserFields,"USED_TIME") == 0) {
         bzero(val6, 255);
         bzero(hbc8, 30);
      }
      if(strstr(TopUserFields,"MILISEC") == 0) {
         bzero(wwork3, 255);
         bzero(hbc9, 30);
      }
      if(strstr(TopUserFields,"%TIME") == 0) {
         bzero(val7, 255);
         bzero(hbc10, 30);
      }

      if(strstr(ReportType,"users_sites") == 0)
         href[0]='\0';

      if(dotinuser && strstr(name,"_")) {
         str2=(char *)subs(name,"_",".");
         strcpy(name,str2);
         free(str2);
      }

      sprintf(preg,"<tr><td %s>%s</td><td %s>%s</td><td %s>%s%s</td><td %s>%s</td><td %s>%s</td><td %s>%s</td><td %s>%s</td><td %s>%s</td><td %s>%s</td><td %s>%s<td %s>%s</td></tr>\n",hbc1,val1,hbc2,val2,hbc2,href,name,hbc3,wwork1,hbc4,wwork2,hbc5,val3,hbc6,val4,hbc7,val5,hbc8,val6,hbc9,wwork3,hbc10,val7);

      if(strstr(user,"TOTAL") != 0) {

         if(atol(incac)) {
            inperc=ttnbytes / 100;
            inperc=atol(incac) / inperc;
         } else inperc = 0;

         if(atol(oucac)) {
            ouperc=ttnbytes / 100;
            ouperc=atol(oucac) / ouperc;
         } else ouperc = 0;

	 sprintf(wwork1,"%s",fixnum(ttnacc,1));
	 sprintf(wwork2,"%s",fixnum(ttnbytes,1));
	 sprintf(wwork3,"%s",fixnum(ttnelap,1));

         strcpy(hbc1,"class=\"header2\"");
         strcpy(hbc2,"class=\"header2\"");
         strcpy(hbc3,"class=\"header2\"");
         strcpy(hbc4,"class=\"header2\"");
         strcpy(hbc5,"class=\"header2\"");
         strcpy(hbc6,"class=\"header2\""); 
         strcpy(hbc7,"class=\"header2\"");
         strcpy(hbc8,"class=\"header2\"");
         strcpy(hbc9,"class=\"header2\"");
         strcpy(hbc10,"class=\"header\"");

         sprintf(val4,"%3.2f%%",inperc);
         sprintf(val5,"%3.2f%%",ouperc);
         sprintf(val6,"%s",buildtime(ttnelap));
         sprintf(val7,"%3.2f%%",perc2);

         if(strstr(TopUserFields,"CONNECT") == 0) {
            bzero(wwork1, 255);
            bzero(hbc1, 30);
         }
         if(strstr(TopUserFields,"BYTES") == 0) {
            bzero(wwork2, 255);
            bzero(hbc2, 30);
         }
         if(strstr(TopUserFields,"IN-CACHE-OUT") == 0) {
            bzero(val4, 255);
            bzero(val5, 255);
            bzero(hbc3, 30);
            bzero(hbc4, 30);
         }
         if(strstr(TopUserFields,"USED_TIME") == 0) {
            bzero(val6, 255);
            bzero(hbc5, 30);
         }
         if(strstr(TopUserFields,"MILISEC") == 0) {
            bzero(wwork3, 255);
            bzero(hbc6, 30);
         }

         if(strstr(ReportType,"date_time") != 0) {
	    if(strstr(TopUserFields,"TOTAL") != 0)
               sprintf(preg,"<tr><td></td><td></td><th %s>%s</th><th %s>%s</th><th %s>%15s</th><td></td><th %s>%s</th><th %s>%s</th><th %s>%s</th><th %s>%s</th></tr>\n",hbc10,text[107],hbc1,wwork1,hbc2,wwork2,hbc3,val4,hbc4,val5,hbc5,val6,hbc6,wwork3);
	 } else if(strstr(TopUserFields,"TOTAL") != 0)
            sprintf(preg,"<tr><td></td><td></td><th %s>%s</th><th %s>%s</th><th %s>%15s</th><td></td><th %s>%s</th><th %s>%s</th><th %s>%s</th><th %s>%s</th></tr>\n",hbc10,text[107],hbc1,wwork1,hbc2,wwork2,hbc3,val4,hbc4,val5,hbc5,val6,hbc6,wwork3);
      }

      fputs(preg,fp_top3);

      topcount++;

      fscanf(fp_top1,"%s%s%s%s%s%s",user,nbytes,nacc,elap,incac,oucac);
   }

   if(ttnbytes) tnbytes=ttnbytes / totuser;
   else tnbytes=0;

   twork=ttnacc/totuser;
   twork2=ttnelap/totuser;
   sprintf(wwork1,"%s",fixnum(twork,1));
   sprintf(wwork2,"%s",fixnum(tnbytes,1));
   sprintf(wwork3,"%s",fixnum(twork2,1));

   if(strstr(TopUserFields,"CONNECT") == 0) {
      bzero(wwork1, 255);
      bzero(hbc1, 30);
   }
   if(strstr(TopUserFields,"BYTES") == 0) {
      bzero(wwork2, 255);
      bzero(hbc2, 30);
   }
   if(strstr(TopUserFields,"IN-CACHE-OUT") == 0) {
      bzero(val4, 255);
      bzero(val5, 255);
      bzero(hbc3, 30);
      bzero(hbc4, 30);
   }
   if(strstr(TopUserFields,"USED_TIME") == 0) {
      bzero(val6, 255);
      bzero(hbc5, 30);
   }
   if(strstr(TopUserFields,"MILISEC") == 0) {
      bzero(wwork3, 255);
      bzero(hbc6, 30);
   }

   if((strstr(ReportType,"date_time") != 0 && strstr(TopUserFields,"AVERAGE") != 0)) fprintf(fp_top3,"<tr><td></td><th></th><th %s>%s</th><th %s>%s</th><th %s>%15s</th><td></td><td></td><td></td><th %s>%s</th><th %s>%s</th></tr>\n",hbc10,text[96],hbc1,wwork1,hbc2,wwork2,hbc3,buildtime(ttnelap/totuser),hbc4,wwork3);
   else if(strstr(TopUserFields,"AVERAGE") != 0) fprintf(fp_top3,"<tr><td></td><th></th><td></td><th %s>%s</th><th %s>%s</th><th %s>%15s</th><td></td><td></td><td></td><th %s>%s</th><th %s>%s</th></tr>\n",hbc10,text[96],hbc1,wwork1,hbc2,wwork2,hbc3,buildtime(ttnelap/totuser),hbc4,wwork3);

   if(strlen(UserAgentLog) > 0) {
      fputs("<tr><td></td></tr>\n",fp_top3);
      fputs("<tr><td></td></tr>\n",fp_top3);
      fputs("<td align=\"left\" colspan=8><font size=-1><a href='useragent.php'>Useragent</a> Report</td>\n",fp_top3);
   }

   fputs("</table></center>",fp_top3);

   show_info(fp_top3);

final:
   fclose(fp_top1);
   unlink(top1);

   if((fp_ou=fopen(tusr,"w"))==NULL) {
      fprintf(stderr, "SARG: (topuser) %s: %s\n",text[45],tusr);
      exit(1);
   }

   fprintf(fp_ou,"%d\n",totuser);

   fputs("</body>\n</html>\n",fp_top3);
   fclose(fp_top3);
   fclose(fp_ou);

   return;
}
