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
#include "include/defs.h"

void gerarel()
{

   FILE *fp_in;

   char accdia[11], acchora[9], accuser[MAXLEN], accip[MAXLEN], accurl[MAXLEN], accbytes[12], accelap[10];
   char oldaccdia[11], oldacchora[9], oldaccip[MAXLEN], wdir[MAXLEN], per1[MAXLEN];
   char wdirname[MAXLEN], oldurl[MAXLEN], oldaccuser[MAXLEN];
   char olduser[MAXLEN], oldmsg[50], acccode[50], oldaccelap[10], oldacccode[50], user[MAXLEN];
   char ipantes[MAXLEN], nameantes[MAXLEN], wdname[MAXLEN], wname2[MAXLEN]; 
   char accsmart[MAXLEN];
   char wcrc[50];
   char crc2[50];
   long long int nbytes=0; 
   long long int nelap=0; 
   long long int nacc=0;
   long long int rtotal=0;
   long long int incache=0;
   long long int oucache=0;
   char *s;
   DIR *dirp;
   struct dirent *direntp;

   ipantes[0]='\0';
   nameantes[0]='\0';
   smartfilter=0;

   sprintf(dirname, "%s%s", outdir, period);
   sprintf(wdir, "%s%s", outdir, period);
   strcpy(per1,period);
   vrfydir(wdir, per1, addr, site, us, email);

   if(debugz){
      debugaz("dirname",dirname);
      debugaz("wdir",wdir);
   }
 
   strcpy(wdirname,dirname);
   gperiod();

   if(strlen(UserAgentLog) > 0 && email[0] == '\0') useragent();

   olduser[0]='\0';
   strncat(tmp,"/sarg",5);

   dirp = opendir(tmp);
   while ((direntp = readdir( dirp )) != NULL ) {
      if((strstr(direntp->d_name,".log") == 0) ||
         (strncmp(direntp->d_name,"download.log",12) == 0) ||
         (strncmp(direntp->d_name,"denied.log",10) == 0) ||
	 (strncmp(direntp->d_name,"authfail.log.unsort",19) == 0))
         continue;
      sprintf(tmp3,"%s/%s",tmp,direntp->d_name);
      if((fp_in=fopen(tmp3,"r"))==NULL){
         fprintf(stderr, "SARG: (report) %s: %s\n",text[45],tmp);
         exit(1);
      }
    
      strcpy(wdname,direntp->d_name);
      strip_prefix:
      getword(wname2,wdname,'.');
      strcat(user,wname2);
   
      if(strcmp(wdname,"log") !=0) {
         strcat(user,".");
         goto strip_prefix;
      }
 
      strcpy(wdirname,dirname);
      maketmp(user,tmp,debug,indexonly);
      maketmp_hour(user,tmp,indexonly);
   
      ttopen=0;
      bzero(html_old, MAXLEN);
   
      while(fgets(buf,sizeof(buf),fp_in)!=NULL) {
         getword(accdia,buf,' ');
         getword(acchora,buf,' ');
         getword(accuser,buf,' ');
         getword(accip,buf,' ');
         getword(accurl,buf,' ');
         getword(accbytes,buf,' ');
         getword(acccode,buf,' ');
         if(strncmp(acccode,"TCP_DENIED/407",14) == 0) continue;
         getword(accelap,buf,' ');
         getword(accsmart,buf,' ');
         getword(accsmart,buf,'"');
   
         if(strlen(accsmart) > 0) {
            smartfilter++;
            strcpy(wdirname,dirname);
            grava_SmartFilter(wdirname,accuser,accip,accdia,acchora,accurl,accsmart);
         }

         if(strcmp(Ip2Name,"yes") == 0) {
            if(strcmp(accip,ipantes) != 0) {
               strcpy(ipantes,accip);
               ip2name(accip);
               strcpy(nameantes,accip);
            } else strcpy(accip,nameantes);
         }
     
         strcpy(wdirname,dirname);
         gravatmp_hora(wdirname,accuser,accdia,acchora,accelap,accbytes,indexonly);

         if(iprel){
            strcpy(wdirname,dirname);
            gravaporuser(accuser,wdirname,accurl,accip,accdia,acchora,accbytes,accelap,indexonly);
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

         if(site[0] != '\0') {
            if(strcmp(oldaccuser,accuser) != 0){
               strcpy(oldmsg,"OK");
               if(strstr(oldacccode,"DENIED") != 0)
                  sprintf(oldmsg,"%s",text[46]);
               gravatmp(oldaccuser,wdirname,oldurl,nacc,nbytes,oldmsg,nelap,indexonly,incache,oucache);
               gravager(wdirname,oldaccuser,nacc,oldurl,nbytes,oldaccip,oldacchora,oldaccdia,nelap,
				incache,oucache);
               nacc=0;
               nbytes=0;
               nelap=0;
               incache=0;
               oucache=0;
            }
         } else {     
            if(strcmp(oldurl,accurl) != 0 || strcmp(oldaccuser,accuser) != 0){
               strcpy(oldmsg,"OK");
               if(strstr(oldacccode,"DENIED") != 0)
                  sprintf(oldmsg,"%s",text[46]);
               strcpy(wdirname,dirname);
               gravatmp(oldaccuser,wdirname,oldurl,nacc,nbytes,oldmsg,nelap,indexonly,incache,oucache);
               strcpy(wdirname,dirname);
               gravager(wdirname,oldaccuser,nacc,oldurl,nbytes,oldaccip,oldacchora,oldaccdia,nelap,incache,oucache);
               nacc=0;
               nbytes=0;
               nelap=0;
               incache=0;
               oucache=0;
   	    if(strcmp(oldaccuser,accuser) != 0)
   	       ind2=0;
            }
      }
      nacc++;
      nbytes+=my_atoll(accbytes);
      nelap+=my_atoll(accelap);

      if(strstr(ReportType,"site_user_time_date") != 0) {
         if(!ttopen) {
            ind2++;
	    strcpy(siteind,accurl);
	    str=siteind;
	    for(z1=0; z1<strlen(str); z1++) {
	    if(str[z1]=='?' || str[z1]=='-' || str[z1]=='.' || str[z1]==':' || str[z1]=='/' || str[z1]=='\\')
	                     str[z1]='_';
            }
	    sprintf(arqtt,"%s/%s",dirname,accuser);
	    if(access(arqtt, R_OK) != 0)
               my_mkdir(arqtt);
	    sprintf(arqtt,"%s/%s/tt%s-%s.php",dirname,accuser,accuser,siteind);
            if(strlen(arqtt) > 255) {
               strncpy(val7,arqtt,255);
               bzero(arqtt,MAXLEN);
               strcpy(arqtt,val7);
            }            
            if ((fp_tt = fopen(arqtt, "w")) == 0) {
               fprintf(stderr, "SARG: (report) %s: %s\n",text[45],arqtt);
               exit(1);
            }
	    ttopen=1;

   	    if(strcmp(Privacy,"yes") == 0)
               sprintf(httplink,"<font size=%s color=%s><href=http://%s>%s",	\
	    	          FontSize,PrivacyStringColor,PrivacyString,PrivacyString);
	     else
               sprintf(httplink,"<font size=%s><a href=http://%s>%s</A>",FontSize,accurl,accurl);

            sprintf(ltext110,"%s",text[110]);
            if(ltext110){
               for(s=ltext110; *s; ++s)
                  *s=tolower(*s);
            }
      /* LCS */
      fputs("<?php\n",fp_tt);
      fputs("\n",fp_tt);
      fputs("include \"/var/www/lcs/includes/headerauth.inc.php\";\n",fp_tt);
      fputs("include \"/var/www/Annu/includes/ldap.inc.php\";\n",fp_tt);
      fputs("include \"/var/www/Annu/includes/ihm.inc.php\";\n",fp_tt);
      fputs("\n",fp_tt);
      fputs("list ($idpers,$login)= isauth();\n",fp_tt);
      fputs("if ($idpers == \"0\") header(\"Location:$urlauth\");\n",fp_tt);
      fputs("?>\n",fp_tt);
      fputs("<!--report-->\n",fp_tt);
            
	    fprintf(fp_tt, "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">\n<html>\n<head>\n  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=%s\">\n",CharSet);
            css(fp_tt);
            fputs("</head>\n",fp_tt);
            fprintf(fp_tt,"<body bgcolor=%s text=%s background='%s'>\n",BgColor,TxColor,BgImage);
	    if(strlen(LogoImage) > 0) fprintf(fp_tt, "<center><table cellpadding=\"0\" cellspacing=\"0\">\n<tr><th class=\"logo\"><img src='%s' border=0 align=absmiddle width=%s height=%s>&nbsp;%s</th></tr>\n<tr><td height=\"5\"></td></tr>\n</table>\n",LogoImage,Width,Height,LogoText);

            if(strcmp(IndexTree,"date") == 0)
               show_sarg(fp_tt, "../../../..");
            else
               show_sarg(fp_tt, "../..");

            fputs("<center><table cellpadding=0 cellspacing=0>\n",fp_tt);
            fprintf(fp_tt,"<tr><th class=\"title\" colspan=\"2\">%s</th></tr>\n",Title);

            strcpy(u2,user);
            if(strcmp(Ip2Name,"yes") == 0)
               ip2name(u2);
            if(UserTabFile[0] != '\0') {
               sprintf(warea,":%s:",u2);
               if((str=(char *) strstr(userfile,warea)) != (char *) NULL ) {
                  z1=0;
                  str2=(char *) strstr(str+1,":");
                  str2++;
                  bzero(name, MAXLEN);
                  while(str2[z1] != ':') {
                     name[z1]=str2[z1];
                     z1++;
                  }
               } else strcpy(name,u2);
            } else strcpy(name,u2);

            if(dotinuser && strstr(name,"_")) {
               str2=(char *)subs(name,"_",".");
               strcpy(name,str2);
            }

	    fprintf(fp_tt,"<tr><td class=\"header\" colspan=\"2\">%s:&nbsp;%s</td></tr>\n",text[89],period);
	    fprintf(fp_tt,"<tr><td class=\"header\" colspan=\"2\">%s:&nbsp;%s</td></tr>\n",text[90],name);
	    fprintf(fp_tt,"<tr><td class=\"header\" colspan=\"2\">%s:&nbsp;%s, %s</td></tr>\n",text[104],UserSortField,UserSortOrder);
	    fprintf(fp_tt,"<tr><th class=\"header3\" colspan=2>%s</th></tr>\n",text[32]);

            fputs("</table></center>\n",fp_tt);
            fputs("<center><table cellpadding=\"0\" cellspacing=\"2\">\n",fp_tt);
            fputs("<tr><td></td><td></td></tr>",fp_tt);
            bzero(tmp4, MAXLEN);
            strncpy(tmp4,text[110],4);
            fprintf(fp_tt,"<tr><th class=\"header\">%s</th><th class=\"header\">%s</th><th class=\"header\">%s</th></tr>\n",text[91],tmp4,text[110]+5);
	 }

         sprintf(html,"<tr><td class=\"data\">%s</td><td class=\"data\">%s</td><td class=\"data\">%s</td></tr>\n",accurl,accdia,acchora);

	 if(strcmp(html,html_old) != 0)
            fputs(html,fp_tt);
	 strcpy(html_old, html);
      } else bzero(ltext110, 50);

      strcpy(wcrc,acccode);
      getword(crc2,wcrc,'/');

      if(strstr(crc2,"MISS") != 0)
         oucache+=my_atoll(accbytes);
       else incache+=my_atoll(accbytes);

      strcpy(oldurl,accurl);

      if(strcmp(accuser,oldaccuser) != 0) {
         strcpy(wdirname,dirname);
         totaliza_day(tmp,oldaccuser,indexonly);
      }

      strcpy(oldaccuser,accuser);
      strcpy(oldacccode,acccode);
      strcpy(oldaccelap,accelap);
      strcpy(oldaccip,accip);
      strcpy(oldaccdia,accdia);
      strcpy(oldacchora,acchora);

   }
   bzero(user,MAXLEN);
   fclose(fp_in);
   unlink(tmp3);
}

   strcpy(oldmsg,"OK");
   if(strstr(oldacccode,"DENIED") != 0)
      sprintf(oldmsg,"%s",text[46]);
   strcpy(wdirname,dirname);
   gravatmpf(oldaccuser,wdirname,oldurl,nacc,nbytes,oldmsg,nelap,indexonly,incache,oucache);
   strcpy(wdirname,dirname);
   gravager(wdirname,oldaccuser,nacc,oldurl,nbytes,oldaccip,oldacchora,oldaccdia,nelap,incache,oucache);
   strcpy(wdirname,dirname);
   totaliza_day(tmp,oldaccuser,indexonly);

   tmpsort();

   strcpy(wdirname,dirname);
   totalger(wdirname, debug, outdir);

   if(strlen(email) == 0) {
      if(strstr(ReportType,"downloads") != 0) download_report();

      if(strlen(DansGuardianConf) > 0) {
         strcpy(wdirname,dirname);
         dansguardian_log();
      }

      strcpy(wdirname,dirname);
      squidguard_log();

      strcpy(wdirname,dirname);
      topuser();

      if(strstr(ReportType,"topsites") != 0) topsites();

      if(strstr(ReportType,"sites_users") != 0) siteuser();
      gen_denied_report();

      strcpy(wdirname,dirname);
      authfail_report();

      if(smartfilter) smartfilter_report();

      if(strlen(DansGuardianConf) > 0) dansguardian_report();

      squidguard_report();

      if(strstr(ReportType,"users_sites") != 0) htmlrel();

      make_index();

      if(strncmp(SuccessfulMsg,"yes",3) == 0) fprintf(stderr, "SARG: %s %s\n",text[47],dirname);
    } else {
      strcpy(wdirname,dirname);
      geramail(wdirname, debug, outdir, userip, email, TempDir);

      if((strcmp(email,"stdout") != 0) && (strncmp(SuccessfulMsg,"yes",3) == 0))
            fprintf(stderr, "SARG: %s %s\n",text[48],email);
   }

   if(indexonly) {
      strcpy(wdirname,dirname);
      index_only(wdirname, debug);
   }

   removetmp(dirname);

   return;
}


void maketmp(char *user, char *dirname, int debug, int indexonly)
{

   FILE *fp_ou;

   char wdirname[MAXLEN];

   if(indexonly) return;
   if(strstr(ReportType,"users_sites") == 0) return;

   strcpy(wdirname,tmp);
   strcat(wdirname,"/");
   strcat(wdirname,user);

   if(debug){
      sprintf(msg,"%s: %s",text[49],wdirname);
      debuga(msg);
   }

   strcat(wdirname,".utmp");
   if((fp_ou=fopen(wdirname,"w"))==NULL){
      fprintf(stderr, "SARG: (report) %s: %s\n",text[45],wdirname);
      exit(1);
   }

   fclose(fp_ou);
   return;
}


void maketmp_hour(char *user, char *dirname, int indexonly)
{

   FILE *fp_ou;

   char wdirname[MAXLEN];

   if(indexonly) return;
   if(strstr(ReportType,"users_sites") == 0) return;

   strcpy(wdirname,tmp);
   strcat(wdirname,"/");
   strcat(wdirname,user);

   strcat(wdirname,".htmp");
   if((fp_ou=fopen(wdirname,"w"))==NULL){
      fprintf(stderr, "SARG: (report-1) %s: %s - %s\n",text[45],wdirname,strerror(errno));
      exit(1);
   }

   fclose(fp_ou);
   return;
}


void gravatmp(char *oldaccuser, char *dirname, char *oldurl, long long int nacc, long long int nbytes, char *oldmsg, long long int nelap, int indexonly, long long int incache, long long int oucache)
{

   FILE *fp_ou;

   char wdirname[MAXLEN];

   if(indexonly) return;
   if(strstr(ReportType,"users_sites") == 0) return;

   strcpy(wdirname,tmp);
   strcat(wdirname,"/");
   strcat(wdirname,oldaccuser);
   strcat(wdirname,".utmp");

   if((fp_ou=fopen(wdirname,"a"))==NULL){
      fprintf(stderr, "SARG: (report) %s: %s\n",text[45],wdirname);
      exit(1);
   } 

   my_lltoa(nacc,val1,15);
   my_lltoa(nbytes,val2,15);
   my_lltoa(nelap,val3,15);
   my_lltoa(incache,val4,15);
   my_lltoa(oucache,val5,15);
   fprintf(fp_ou,"%s %s %s %s %s %s %s\n",val1,val2,oldurl,oldmsg,val3,val4,val5);

   fclose(fp_ou);
   ttopen=0;

   if(fp_tt) {
      fputs("</table>\n",fp_tt);
      fputs("</body>\n</html>\n",fp_tt);
      fclose(fp_tt);
   }

   return;

}


void gravatmp_hora(char *dirname, char *user, char *data, char *hora, char *elap, char *bytes, int indexonly)
{

   FILE *fp_ou;

   char wdirname[MAXLEN];

   if(indexonly || (strstr(ReportType,"users_sites") == 0)) return;

   strcpy(wdirname,tmp);
   strcat(wdirname,"/");
   strcat(wdirname,user);
   strcat(wdirname,".htmp");

   if((fp_ou=fopen(wdirname,"a"))==NULL){
      fprintf(stderr, "SARG: (report-2) %s: %s - %s\n",text[45],wdirname,strerror(errno));
      exit(1);
   }

   if(strcmp(datetimeby,"bytes") == 0) fprintf(fp_ou,"%s %s %s\n",data,hora,bytes);
   else fprintf(fp_ou,"%s %s %s\n",data,hora,elap);

   fclose(fp_ou);

   return;
}


void gravaporuser(char *user, char *dirname, char *url, char *ip, char *data, char *hora, char *tam, char *elap, int indexonly)
{

   FILE *fp_ou;

   char wdirname[MAXLEN];

   if(indexonly || (strstr(ReportType,"users_sites") == 0)) return;

   strcpy(wdirname,tmp);
   strcat(wdirname,"/");
   strcat(wdirname,user);
   strcat(wdirname,".ip");

   if((fp_ou=fopen(wdirname,"a"))==NULL){
      fprintf(stderr, "SARG: (report) %s: %s\n",text[45],wdirname);
      exit(1);
   } 

   fprintf(fp_ou,"%s %s %s %s %s %s\n",ip,url,data,hora,tam,elap);

   fclose(fp_ou);

   return;

}


void gravatmpf(char *oldaccuser, char *dirname, char *oldurl, long long int nacc, long long int nbytes, char *oldmsg, long long int nelap, int indexonly, long long int incache, long long int oucache)
{

   FILE *fp_ou;

   char wdirname[MAXLEN];

   if(indexonly || (strstr(ReportType,"users_sites") == 0)) return;

   strcpy(wdirname,tmp);
   strcat(wdirname,"/");
   strcat(wdirname,oldaccuser);
   strcat(wdirname,".utmp");

   if((fp_ou=fopen(wdirname,"a"))==NULL){
      fprintf(stderr, "SARG: (report) %s: %s\n",text[45],wdirname);
      exit(1);
   }

   my_lltoa(nacc,val1,15);
   my_lltoa(nbytes,val2,15);
   my_lltoa(nelap,val3,15);
   my_lltoa(incache,val4,15);
   my_lltoa(oucache,val5,15);
   fprintf(fp_ou,"%s %s %s %s %s %s %s\n",val1,val2,oldurl,oldmsg,val3,val4,val5);

   fclose(fp_ou);
   ttopen=0;
   ind2=0;

   if(fp_tt) {
      fputs("</table>\n",fp_tt);
      fputs("</html>\n",fp_tt);
      fclose(fp_tt);
   }

   return;
     
}


void gravager(char *dirname, char *user, long long int nacc, char *url, long long int nbytes, char *ip, char *hora, char *dia, long long int nelap, long long int incache, long long int oucache)
{

   FILE *fp_ou;

   strcat(dirname,"/");
   strcat(dirname,"general");
   
   if((fp_ou=fopen(dirname,"a"))==NULL){
      fprintf(stderr, "SARG: (report) %s: %s\n",text[45],dirname);
      exit(1);
   }

   my_lltoa(nacc,val1,15);
   my_lltoa(nbytes,val2,15);
   my_lltoa(nelap,val3,15);
   my_lltoa(incache,val4,15);
   my_lltoa(oucache,val5,15);
   fprintf(fp_ou,"%s %s %s %s %s %s %s %s %s %s\n",user,val1,val2,url,ip,hora,dia,val3,val4,val5);

   fclose(fp_ou);
   return;

}

void grava_SmartFilter(char *dirname, char *user, char *ip, char *data, char *hora, char *url, char *smart)
{

   FILE *fp_ou;

   char wdirname[MAXLEN];

   sprintf(wdirname,"%s/smartfilter.unsort",dirname);

   if((fp_ou=fopen(wdirname,"a"))==NULL){
      fprintf(stderr, "SARG: (report) %s: %s\n",text[45],wdirname);
      exit(1);
   }

   fprintf(fp_ou,"%s %s %s %s %s %s\n",user,data,hora,ip,url,smart);
   fputs("</body>\n</html>\n",fp_tt);

   fclose(fp_ou);

   return;

}
