/*
 * AUTHOR: Pedro Lineu Orso                      orso@penguintech.com.br
 *                                                            1998, 2005
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

void htmlrel()
{
   DIR *dirp;
   FILE *fp_in, *fp_ou, *fp_ip, *fp_ip2, *fp_usr, *fp_tt;

   struct dirent *direntp;
   long long int nnbytes=0, unbytes=0, tnbytes=0, totbytes=0, totbytes2=0;
   long long int totelap=0, totelap2=0, nnelap=0, unelap=0, tnelap=0;
   long long int incache=0, oucache=0, tnincache=0, tnoucache=0, twork=0, twork2=0;
   char arqin[MAXLEN], arqou[MAXLEN], arqper[MAXLEN], arqip[MAXLEN];
   char nacc[20], nbytes[20], url[1024], purl[1024], tmsg[50], tmsg2[50], nelap[20], csort[MAXLEN];
   char period[MAXLEN], usuario[MAXLEN], wusuario[MAXLEN], u2[MAXLEN], duser[MAXLEN];
   char userbytes[20], userelap[20], userurl[1024], userhora[9], userdia[9];
   char user_ip[MAXLEN], olduserip[MAXLEN], tmp2[MAXLEN], tmp3[MAXLEN], incac[20], oucac[20];
   char denied_report[255], name2[MAXLEN];
   char ttd1[3], ttd2[3], ttd3[5], ttt1[3], ttt2[3], ttt3[3];
   char *str;
   char ftime[128], warea[MAXLEN];
   char wtemp[MAXLEN], totuser[8];
   long long int tnacc=0, ttnacc=0, unacc=0;
   float perc=0, perc2=0, ouperc=0, inperc=0;
   char *s;
   int  x, count;

   if(indexonly) return;
   if(strstr(ReportType,"users_sites") == 0) return;

   strcpy(tmp2,TempDir);
   strcat(tmp2,"/sargtmp.unsort");

   strcpy(tmp3,TempDir);
   strcat(tmp3,"/sargtmp.log");

   strcpy(arqper,dirname);
   strcat(arqper,"/period");

   if ((fp_in = fopen(arqper, "r")) == 0){
      fprintf(stderr, "SARG: (html1) %s: %s\n",text[45],arqper);
      exit(1);
   }

   fgets(period,sizeof(period),fp_in);
   fclose(fp_in);

   strcpy(arqper,dirname);
   strcat(arqper,"/general");

   if ((fp_in = fopen(arqper, "r")) == 0){
      fprintf(stderr, "SARG: (html2) %s: %s\n",text[45],arqper);
      exit(1);
   }

   while(fgets(buf,sizeof(buf),fp_in)!=NULL) {
      if(strstr(buf,"TOTAL") == 0) {
         getword(wtemp,buf,' ');
         ttnacc+=my_atoll(buf);
         getword(wtemp,buf,' ');
         getword(wtemp,buf,' ');
         totbytes+=my_atoll(wtemp);
         getword(wtemp,buf,' ');
         getword(wtemp,buf,' ');
         getword(wtemp,buf,' ');
         getword(wtemp,buf,' ');
         getword(wtemp,buf,' ');
         totelap+=my_atoll(wtemp);
      }
   }

   fclose(fp_in);

   dirp = opendir(tmp);
   while ( (direntp = readdir( dirp )) != NULL ) {
      if(strstr(direntp->d_name,".txt") == 0)
         continue;

      count=1;
      strcpy(usuario,direntp->d_name);
      wusuario[0]='\0';
   
      striptxt:
      getword(warea,usuario,'.');
      strcpy(denied_report,warea);
      strcat(wusuario,warea);
   
      if(strcmp(usuario,"txt") !=0) {
         strcat(wusuario,".");
         goto striptxt;
      }
 
      sprintf(warea,"%s/%s",dirname,wusuario);
      mkdir(warea,0755);

      report_day(wusuario, iprel, userip);
      greport_day(wusuario, iprel, userip);

      strcpy(usuario,wusuario);
      strcpy(arqin,tmp);
      strcat(arqin,"/");
      strcpy(arqou,dirname);
      strcat(arqou,"/");
      strcat(arqou,usuario);
      strcat(arqou,"/");
      strcat(arqou,usuario);
      strcat(arqou,".php");
      strcpy(duser,arqin);
      strcat(duser,"denied_");
      strcat(arqin,direntp->d_name);
 
      if((str=(char *) strstr(denied_report, "_")) != (char *) NULL ) {
         if((str=(char *) strstr(str+1, "_")) != (char *) NULL )
            fixip(denied_report);
      }
      strcat(duser,denied_report);
      strcat(duser,".php");
      if(access(duser, R_OK) != 0)
         denied_report[0]='\0';
   
      if ((fp_in = fopen(arqin, "r")) == 0){
         fprintf(stderr, "SARG: (html3) %s: %s\n",text[45],arqin);
         exit(1);
      }
   
      while(fgets(buf,sizeof(buf),fp_in)!=NULL) {
         getword(wtemp,buf,' ');
         tnacc+=my_atoll(wtemp);
         getword(wtemp,buf,' ');
         tnbytes+=my_atoll(wtemp);
         getword(wtemp,buf,' ');
         getword(wtemp,buf,' ');
         getword(wtemp,buf,' ');
         tnelap+=my_atoll(wtemp);
         getword(wtemp,buf,' ');
         tnincache+=my_atoll(wtemp);
         getword(wtemp,buf,' ');
         tnoucache+=my_atoll(wtemp);
      }

      fclose(fp_in);
   
      if ((fp_in = fopen(arqin, "r")) == 0){
         fprintf(stderr, "SARG: (html4) %s: %s\n",text[45],arqin);
         exit(1);
      }
   
      if ((fp_ou = fopen(arqou, "w")) == 0){
         fprintf(stderr, "SARG: (html5) %s: %s\n",text[45],arqou);
         exit(1);
      }
      /* LCS */
      fputs("<?php\n",fp_ou);
      fputs("\n",fp_ou);
      fputs("include \"/var/www/lcs/includes/headerauth.inc.php\";\n",fp_ou);
      fputs("include \"/var/www/Annu/includes/ldap.inc.php\";\n",fp_ou);
      fputs("include \"/var/www/Annu/includes/ihm.inc.php\";\n",fp_ou);
      fputs("\n",fp_ou);
      fputs("list ($idpers,$login)= isauth();\n",fp_ou);
      fputs("if ($idpers == \"0\") header(\"Location:$urlauth\");\n",fp_ou);
      fputs("?>\n",fp_ou);

      fputs("<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\"",fp_ou);
      fputs(" \"http://www.w3.org/TR/html4/loose.dtd\">\n",fp_ou);
      fputs("<html>\n",fp_ou);
      fputs("<head>\n",fp_ou);

      sprintf(html,"  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=%s\">\n",CharSet);
      fputs(html,fp_ou);
      css(fp_ou);
      fputs("</head>\n",fp_ou);

      sprintf(buf,"<body style=\"font-family:%s;font-size:%s;background-color:%s; \
           background-image:url(%s)\">\n",FontFace,TitleFontSize,BgColor,BgImage);
      fputs(buf,fp_ou);

   if(strlen(LogoImage) > 0) {
      fputs("<center><table cellpadding=\"0\" cellspacing=\"0\">\n",fp_ou);
      sprintf(html,"<tr><th class=\"logo\"><img src='%s' border=0 align=absmiddle width=%s height=%s>&nbsp;%s</th></tr>\n",LogoImage,Width,Height,LogoText);
      fputs(html,fp_ou);
      fputs("<tr><td height=\"5\"></td></tr>\n",fp_ou);
      fputs("</table>\n",fp_ou);
   }

      if(strcmp(IndexTree,"date") == 0)
         show_sarg(fp_ou, "../../../..");
      else
         show_sarg(fp_ou, "../..");

      fputs("<center><table cellpadding=\"0\" cellspacing=\"0\">\n",fp_ou);
      sprintf(html,"<tr><th class=\"title\" colspan=\"2\">%s</th></tr>\n",Title);
      fputs(html,fp_ou);

      strcpy(u2,usuario);
      if(userip){
         strcpy(u2,usuario);
         fixip(u2);
      }
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

      strcpy(name2,name);
      if(dotinuser && strstr(name2,"_")) {
         str2=(char *)subs(name2,"_",".");
         strcpy(name2,str2);
      }
   
      sprintf(html,"<tr><td class=\"header\" colspan=\"2\">%s:&nbsp;%s</td></tr>\n",text[89],period);
      fputs(html,fp_ou);
      sprintf(html,"<tr><td class=\"header\" colspan=\"2\">%s:&nbsp;%s</td></tr>\n",text[90],name2);
      fputs(html,fp_ou);
      sprintf(html,"<tr><td class=\"header\" colspan=\"2\">%s:&nbsp;%s, %s</td></tr>\n",text[104],UserSortField,UserSortOrder);
      fputs(html,fp_ou);
      sprintf(html,"<tr><td class=\"header3\" colspan=2>%s %s</td></tr>\n",text[32],text[55]);
      fputs(html,fp_ou);
      fputs("<tr><td></td></tr>\n",fp_ou);
 
      fputs("</table></center>\n",fp_ou);
      fputs("<center><table cellpadding=\"2\" cellspacing=\"1\">\n",fp_ou);
   
      if(strlen(denied_report) > 0) {
         sprintf(html,"<tr><td class=\"header\" colspan=11><a href=\"denied_%s.php\">%s</a> %s</td></tr>\n",denied_report,text[116],text[55]);
         fputs(html,fp_ou);
      }
   
      strcpy(val3,text[92]);
      strcpy(val4,text[93]);
      sprintf(val11,"%%%s",text[93]);
      sprintf(val5,"%s-%s-%s",text[113],text[114],text[112]);
      strcpy(val6,text[94]);
      strcpy(val7,text[95]);
      sprintf(val8,"%%%s",text[99]);
      strcpy(val9,"colspan=2");
      bzero(val10, 255);
      
      if(strstr(UserReportFields,"CONNECT") == 0)
         bzero(val3, 255);
      if(strstr(UserReportFields,"BYTES") == 0)
         bzero(val4, 255);
      if(strstr(UserReportFields,"SETYB") == 0)
         bzero(val11, 255);
      if(strstr(UserReportFields,"IN-CACHE-OUT") == 0) {
         bzero(val5, 255);
         bzero(val9, 255);
         strcpy(val10,"<td></td>");
      }
      if(strstr(UserReportFields,"USED_TIME") == 0)
         bzero(val6, 255);
      if(strstr(UserReportFields,"MILISEC") == 0)
         bzero(val7, 255);
      if(strstr(UserReportFields,"%TIME") == 0)
         bzero(val8, 255);
   
      sprintf(html,"<tr><th></th><th class=\"header\">%s</th><th class=\"header\">%s</th><th class=\"header\">%s</th><th class=\"header\">%s</th><th class=\"header3\" %s>%s</th><th class=\"header\">%s</th><th class=\"header\">%s</th><th class=\"header\">%s</th></tr>\n",text[91],val3,val4,val11,val9,val5,val6,val7,val8);
      fputs(html,fp_ou);
  
      if(debug) {
         if(userip) {
            strcpy(u2,usuario);
            fixip(u2);
            sprintf(msg, "%s: %s",text[61],u2);
         } else
            sprintf(msg, "%s: %s",text[61],usuario);
            debuga(msg);
      }

      fscanf(fp_in,"%s",nacc);
      fscanf(fp_in,"%s",nbytes);
      fscanf(fp_in,"%s",url);
      fscanf(fp_in,"%s",tmsg);
      fscanf(fp_in,"%s",nelap);
      fscanf(fp_in,"%s",incac);
      fscanf(fp_in,"%s",oucac);

      while(!feof(fp_in)) {
         if(strncmp(tmsg,"OK",2) != 0)
            sprintf(tmsg,"<td class=\"data\">%s</td>",text[46]);
         else bzero(tmsg, 50);

         nnbytes=my_atoll(nbytes);
         nnelap=my_atoll(nelap);
         incache=my_atoll(incac);
         oucache=my_atoll(oucac);
   
         if(nnbytes) {
            perc=nnbytes * 100;
            perc=perc / tnbytes;
         } else {
            perc=0;
            ouperc=0;
         }

         if(nnelap) {
            perc2=nnelap * 100;
            perc2=perc2 / tnelap;
         } else perc2=0;
 
         if(incache) {
            inperc=incache * 100;
            inperc=inperc / nnbytes;
         } else inperc=0;
   
         if(oucache) {
            ouperc=oucache * 100;
            ouperc=ouperc / nnbytes;
         } else ouperc=0;
   
         twork=my_atoll(nacc);
         sprintf(wwork1,"%s",fixnum(twork,1));
         sprintf(wwork2,"%s",fixnum(nnbytes,1));
         sprintf(wwork3,"%s",fixnum(nnelap,1));

         if(strcmp(LongUrl,"yes") != 0) {
            getword(warea,url,'/');
            sprintf(url,"%s",warea);
            strcpy(urly,url);
         } else {
            strcpy(urly,url);
            url_module(url, module);
            getword(warea,url,'/');
            sprintf(url,"%s...%s",warea,module);
         }

         if(strcmp(Privacy,"yes") == 0)
            sprintf(httplink,"<font color=%s><href=http://%s>%s",       \
                    PrivacyStringColor,PrivacyString,PrivacyString);
          else
             strcpy(tmp6,"../../images");
             if(strcmp(IndexTree,"date") == 0)
                sprintf(tmp6,"../%s",ImageFile);
             if(strlen(BlockIt) > 0)
                sprintf(httplink,"<a href=\"%s%s?url=%s\"><img src=\"%s/sarg-squidguard-block.png\" border=\"0\"></a><a href=http://%s>&nbsp;%s</a>",wwwDocumentRoot,BlockIt,urly,tmp6,urly,urly);
             else
                sprintf(httplink,"<a href=http://%s title=\"%s\">%s</a>",urly,urly,url);

         if(strstr(ReportType,"site_user_time_date") != 0) {
            sprintf(ltext110,"%s",text[110]);
            if(ltext110){
               for(s=ltext110; *s; ++s)
                  *s=tolower(*s);
            }
            strcpy(siteind,urly);
            str=siteind;
            for(z1=0; z1<strlen(str); z1++) {
	       if(str[z1]=='?' || str[z1]=='-' || str[z1]=='.' || str[z1]==':' || str[z1]=='/' || str[z1]=='\\')
                  str[z1]='_';
            }
            sprintf(href2,"<a href=\"tt%s-%s.php\"><img src=\"%s/datetime.png\" border=\"0\" title=\"%s %s\"></a>",usuario,siteind,tmp6,ltext110,text[55]);
         } else {
            bzero(href2, MAXLEN);
            bzero(ltext110, 50);
         } 

         sprintf(val2,"%s",href2);
         sprintf(val3,"%3.2f%%",perc);
         sprintf(val4,"%3.2f%%",inperc);
         sprintf(val5,"%3.2f%%",ouperc);
         sprintf(val6,"%s",buildtime(nnelap));
         sprintf(val7,"%3.2f%%",perc2);
      
         if(strstr(UserReportFields,"CONNECT") == 0) {
            bzero(wwork1, 255);
            bzero(hbc1, 30);
         }
         if(strstr(UserReportFields,"BYTES") == 0) {
            bzero(wwork2, 255);
            bzero(hbc2, 30);
         }
         if(strstr(UserReportFields,"MILISEC") == 0) {
            bzero(wwork3, 255);
            bzero(hbc3, 30);
         }
         if(strstr(UserReportFields,"SETYB") == 0) {
            bzero(val3, 255);
            bzero(hbc4, 30);
         }
         if(strstr(UserReportFields,"IN-CACHE-OUT") == 0) {
            bzero(val4, 255);
            bzero(val5, 255);
            bzero(hbc5, 30);
            bzero(hbc6, 30);
         }
         if(strstr(UserReportFields,"USED_TIME") == 0) {
            bzero(val6, 255);
            bzero(hbc7, 30);
         }
         if(strstr(UserReportFields,"%TIME") == 0) {
            bzero(val7, 255);
            bzero(hbc8, 30);
         }
         if(strncmp(tmsg," ",1) == 0)
            bzero(hbc9, 30);

         sprintf(html,"<tr><td class=\"data\">%s</td><td class=\"data3\">%s</td><td class=\"data\">%s</td><td class=\"data\">%s</td><td class=\"data\">%s</td><td class=\"data\">%s</td><td class=\"data\">%s</td><td class=\"data\">%s</td><td class=\"data\">%s</td><td class=\"data\">%s</td>%s</tr>\n",val2,httplink,wwork1,wwork2,val3,val4,val5,val6,wwork3,val7,tmsg);
   
         if(UserReportLimit) {
            if(count <= UserReportLimit) {
               fputs(html,fp_ou);
               count++;
            }
         } else fputs(html,fp_ou);

         if(iprel) {
            strcpy(arqip,tmp);
            strcat(arqip,"/");
            strcat(arqip,usuario);
            strcat(arqip,".ip");
   
            if ((fp_ip = fopen(arqip, "r")) == 0){
               fprintf(stderr, "SARG: (html6) %s: %s\n",text[45],arqip);
               exit(1);
            }
   
            if ((fp_ip2 = fopen(tmp2, "a")) == 0){
               fprintf(stderr, "SARG: (html7) %s: %s\n",text[45],tmp2);
               exit(1);
            }
   
            while(fgets(buf,sizeof(buf),fp_ip)!=NULL) {
               if(strstr(buf,url) != 0)
                  fputs(buf,fp_ip2);
            }
   
            fclose(fp_ip);
            fclose(fp_ip2);

            sprintf(csort,"sort -n -T %s -k 1,1 -k 5,5 -o '%s' '%s'",TempDir,tmp3,tmp2);
            system(csort);
   
            if ((fp_ip = fopen(tmp3, "r")) == 0) {
               fprintf(stderr, "SARG: (html8) %s: %s\n",text[45],tmp3);
               exit(1);
            }

            fscanf(fp_ip,"%s",user_ip);
            fscanf(fp_ip,"%s",userurl);
            fscanf(fp_ip,"%s",userdia);
            fscanf(fp_ip,"%s",userhora);
            fscanf(fp_ip,"%s",userbytes);
            fscanf(fp_ip,"%s",userelap);
   
            strcpy(olduserip,user_ip);
   
            while(!feof(fp_ip)) {
               if(strcmp(user_ip,olduserip) != 0) {
                  my_lltoa(unelap,val2,0);
                  sprintf(wwork1,"%s",fixnum(unbytes,1));
                  sprintf(html,"<tr><td></td><td class=\"data\">%s</td><td></td><td class=\"data\">%s</td><td></td><td></td><td></td><td class=\"data\">%s</td><td class=\"data\">%s</td></tr>\n",olduserip,wwork1,buildtime(unelap),val2);
                  fputs(html,fp_ou);
  
                  strcpy(olduserip,user_ip);
                  unacc=0;
                  unbytes=0;
                  unelap=0;
               }
   
               unbytes=unbytes+my_atoll(userbytes);
               unelap=unelap+my_atoll(userelap);
   
               fscanf(fp_ip,"%s",user_ip);
               fscanf(fp_ip,"%s",userurl);
               fscanf(fp_ip,"%s",userdia);
               fscanf(fp_ip,"%s",userhora);
               fscanf(fp_ip,"%s",userbytes);
               fscanf(fp_ip,"%s",userelap);
   
            }

            fclose(fp_ip);

            unlink(tmp2);
            unlink(tmp3);

            my_lltoa(unelap,val3,0);
            sprintf(wwork1,"%s",fixnum(unbytes,1));
            sprintf(html,"<tr><td></td><td class=\"data\">%s</td><td></td><td class=\"data\">%s</td><td></td><td></td><td></td><td class=\"data\">%s</td><td class=\"data\">%s</font></td></tr>\n",olduserip,wwork1,buildtime(unelap),val3);
            fputs(html,fp_ou);
         }

         unacc=0;
         unbytes=0;
         unelap=0;

         fscanf(fp_in,"%s",nacc);
         fscanf(fp_in,"%s",nbytes);
         fscanf(fp_in,"%s",url);
         fscanf(fp_in,"%s",tmsg);
         fscanf(fp_in,"%s",nelap);
         fscanf(fp_in,"%s",incac);
         fscanf(fp_in,"%s",oucac);
//         fscanf(fp_in,"%s",datestimes);

      }

      if(iprel)
         unlink(arqip);
      unlink(arqin);

      if(tnbytes) {
         perc=totbytes / 100;
         perc=tnbytes / perc;
      } else perc=0;
   
      if(tnelap) {
         perc2=totelap / 100;
         perc2=tnelap / perc2;
      } else perc2=0;
   
      if(tnoucache) {
         ouperc=tnoucache * 100;
         ouperc=ouperc / tnbytes;
      } else ouperc=0;
   
      if(tnincache) {
         inperc=tnincache * 100;
         inperc=inperc / tnbytes;
      } else inperc=0;
   
      sprintf(wwork1,"%s",fixnum(tnacc,1));
      sprintf(wwork2,"%s",fixnum(tnbytes,1));
      sprintf(wwork3,"%s",fixnum(tnelap,1));

      sprintf(val2,"%s%s",href2,ltext110);
      sprintf(val3,"%3.2f%%",perc);
      sprintf(val4,"%3.2f%%",inperc);
      sprintf(val5,"%3.2f%%",ouperc);
      sprintf(val6,"%s",buildtime(tnelap));
      sprintf(val7,"%3.2f%%",perc2);

      strcpy(hbc1,"class=\"header2\"");
      strcpy(hbc2,"class=\"header2\"");
      strcpy(hbc3,"class=\"header2\"");
      strcpy(hbc4,"class=\"header2\"");
      strcpy(hbc5,"class=\"header2\"");
      strcpy(hbc6,"class=\"header2\"");
      strcpy(hbc7,"class=\"header2\"");
      strcpy(hbc8,"class=\"header2\"");
      strcpy(hbc9,"class=\"header\"");

      if(strstr(UserReportFields,"CONNECT") == 0) {
         bzero(wwork1, 255);
         bzero(hbc1, 30);
      }
      if(strstr(UserReportFields,"BYTES") == 0) {
         bzero(wwork2, 255);
         bzero(hbc2, 30);
      }
      if(strstr(UserReportFields,"MILISEC") == 0) {
         bzero(wwork3, 255);
         bzero(hbc3, 30);
      }
      if(strstr(UserReportFields,"SETYB") == 0) {
         bzero(val3, 255);
         bzero(hbc4, 30);
      }
      if(strstr(UserReportFields,"IN-CACHE-OUT") == 0) {
         bzero(val4, 255);
         bzero(hbc5, 30);
      }
      if(strstr(UserReportFields,"IN-CACHE-OUT") == 0) {
         bzero(val5, 255);
         bzero(hbc6, 30);
      }
      if(strstr(UserReportFields,"USED_TIME") == 0) {
         bzero(val6, 255);
         bzero(hbc7, 30);
      }
      if(strstr(UserReportFields,"%TIME") == 0) {
         bzero(val7, 255);
         bzero(hbc8, 30);
      }

      if(strstr(UserReportFields,"TOTAL") != 0) {
         sprintf(html,"<tr><th></th><th %s>%s</th><th %s>%s</th><th %s>%s</th><th %s>%s</th><th %s>%s</th><th %s>%s</th><th %s>%s</th><th %s>%s</font></th><th %s>%s</font></th></tr>\n",hbc9,text[107],hbc1,wwork1,hbc2,wwork2,hbc4,val3,hbc5,val4,hbc6,val5,hbc7,val6,hbc3,wwork3,hbc8,val7);
         fputs(html,fp_ou);
      }

      fclose(fp_in);
  
      if(atoi(PerUserLimit) > 0) {
         if(tnbytes > (atoi(PerUserLimit)*1000000)) {
            limit_flag=0;
            if(access(PerUserLimitFile, R_OK) == 0) {
               if((fp_usr = fopen(PerUserLimitFile, "r")) == 0) {
                  fprintf(stderr, "SARG: (html9) %s: %s\n",text[45],PerUserLimitFile);
                  exit(1);
               }
               while(fgets(tmp6,sizeof(tmp6),fp_usr)!=NULL) {
                  if(strstr(tmp6,"\n") != 0)
                     tmp6[strlen(tmp6)-1]='\0';
                  if(strcmp(tmp6,u2) == 0) {
                     limit_flag=1;
                     break;
                  }
               }
               fclose(fp_usr);
            }
  
            if(!limit_flag) {
               if((fp_usr = fopen(PerUserLimitFile, "a")) == 0) {
                  fprintf(stderr, "SARG: (html10) %s: %s\n",text[45],PerUserLimitFile);
                  exit(1);
               }
               sprintf(html,"%s\n",u2);
               fputs(html,fp_usr);
               fclose(fp_usr);
    
               if(debug) {
                  sprintf(msg, "%s %s %s (%s MB). %s %s",text[32],u2,text[74],PerUserLimit,text[75],PerUserLimitFile);
                      debuga(msg);
               }
            }
         }
      }

      strcpy(arqper,dirname);
      strcat(arqper,"/users");
 
      if ((fp_in = fopen(arqper, "r")) == 0){
         fprintf(stderr, "SARG: (html11) %s: %s\n",text[45],arqper);
         exit(1);
      }

      fgets(totuser,8,fp_in);
      fclose(fp_in);
 
      totbytes2=totbytes/my_atoll(totuser);
      totelap2=totelap/my_atoll(totuser);
 
      if(totbytes2) {
         perc = totbytes / 100;
         perc = totbytes2 / perc;
      } else perc=0;
   
      if(totelap2) {
         perc2 = totelap / 100;
         perc2 = totelap2 / perc2;
      } else perc2=0;
   
      twork2=my_atoll(totuser);
      twork=ttnacc/twork2;
  
      sprintf(wwork1,"%s",fixnum(twork,1));
      sprintf(wwork2,"%s",fixnum(totbytes2,1));
      sprintf(wwork3,"%s",fixnum(totelap2,1));

      sprintf(val6,"%s",buildtime(totelap2));
      sprintf(val7,"%3.2f%%",perc2); 
   
      strcpy(hbc1,"class=\"header2\"");
      strcpy(hbc2,"class=\"header2\"");
      strcpy(hbc3,"class=\"header2\"");
      strcpy(hbc4,"class=\"header2\"");
      strcpy(hbc5,"class=\"header2\"");
      strcpy(hbc6,"class=\"header\"");

      if(strstr(UserReportFields,"CONNECT") == 0) {
         bzero(wwork1, 255);
         bzero(hbc1, 30);
      }
      if(strstr(UserReportFields,"BYTES") == 0) {
         bzero(wwork2, 255);
         bzero(hbc2, 30);
      }
      if(strstr(UserReportFields,"MILISEC") == 0) {
         bzero(wwork3, 255);
         bzero(hbc3, 30);
      }
      if(strstr(UserReportFields,"USED_TIME") == 0) { 
         bzero(val6, 255);
         bzero(hbc4, 30);
      }
      if(strstr(UserReportFields,"%TIME") == 0) {
         bzero(val7, 255);
         bzero(hbc5, 30);
      }
   
      if(strstr(UserReportFields,"AVERAGE") != 0) {
         sprintf(html,"<tr><th></th><th %s>%s</th><th %s>%s</th><th %s>%s</th><th></th><th></th><th></th><th %s>%s</th><th %s>%s</font></th><th %s>%s</th></tr>\n",hbc6,text[96],hbc1,wwork1,hbc2,wwork2,hbc4,val6,hbc3,wwork3,hbc5,val7);
         fputs(html,fp_ou);
      }

      tnacc=0;
      tnbytes=0;
      tnelap=0;
      tnincache=0;
      tnoucache=0;

      fputs("</center></table>\n",fp_ou);

      show_info(fp_ou);

      fputs("</body>\n</html>\n",fp_ou);

      fclose(fp_ou);

      htaccess(wusuario);

   }

   (void)rewinddir(dirp);
   (void)closedir(dirp);

   return;
}
