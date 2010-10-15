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

void make_index()
{

   FILE *fp_ou, *fp_ou2, *fp_ou3, *fp_tmp, *fp_tmp2, *fp_tmp3;
   DIR *dirp, *dirp2, *dirp3;
   struct dirent *direntp;
   struct dirent *direntp2;
   struct dirent *direntp3;
   char html[MAXLEN];
   char wdir[MAXLEN];
   char wdir_tmp[MAXLEN];
   char wdir_tmp2[MAXLEN];
   char wdir_tmp3[MAXLEN];
   char newname[512];
   char month[4];
   char period[80];
   char data[80];
   char tuser[20];
   char tbytes[20];
   char media[20];
   char ftime[128];
   char day[4], mon[4], year[6], hour[10];
   char h[3], m[3], s[3];

   if(LastLog[0] != '\0') mklastlog(outdir,debug);

   sprintf(wdir,"%sindex.php",outdir);
   sprintf(wdir_tmp,"%sindex.unsort",outdir);
   sprintf(wdir_tmp2,"%sindex.sort",outdir);
   strcpy(hbc1,"class=\"header\"");

   if(strcmp(Index,"no") == 0) {
      if(access(wdir, R_OK) == 0) unlink(wdir);
      return;
   }

   if(debug) debuga(text[53]);

  // Root dir
   dirp = opendir(outdir);
   while ((direntp = readdir( dirp )) != NULL) {
      if(strcmp(IndexTree,"date") == 0) {
         if(!isdigit(direntp->d_name[0]) && !isdigit(direntp->d_name[1])) continue;
         if(strlen(direntp->d_name) > 4) {
            bzero(y1,5);
            bzero(y2,5);
            bzero(m1,4);
            bzero(m2,4);
            bzero(d1,3);
            bzero(d2,3);
            if(strcmp(df,"u") == 0) {
               strncpy(y1,direntp->d_name,4);
               strncpy(m1,direntp->d_name+4,3);
               strncpy(d1,direntp->d_name+7,2);
               strncpy(y2,direntp->d_name+10,4);
               strncpy(m2,direntp->d_name+14,3);
               strncpy(d2,direntp->d_name+17,2);
           } else if(strcmp(df,"e") == 0) {
               strncpy(y1,direntp->d_name+5,4);
               strncpy(m1,direntp->d_name+2,3);
               strncpy(d1,direntp->d_name,2);
               strncpy(y2,direntp->d_name+15,4);
               strncpy(m2,direntp->d_name+12,3);
               strncpy(d2,direntp->d_name+10,2);
            }
            conv_month(m1);
            conv_month(m2);
         }
         sprintf(val1,"%s%s",outdir,y1);
         if(access(val1, R_OK) != 0) mkdir(val1,0755);
         if(strcmp(m1,m2) != 0) sprintf(val2,"%s/%s-%s",val1,m1,m2);
         else sprintf(val2,"%s/%s",val1,m1);
         if(access(val2, R_OK) != 0) mkdir(val2,0755);
         if(strcmp(d1,d2) != 0) sprintf(val3,"%s/%s-%s",val2,d1,d2);
         else sprintf(val3,"%s/%s",val2,d1);
         sprintf(val4,"%s%s",outdir,direntp->d_name);
         rename(val4,val3);
         sprintf(val5,"%s/images",val2);
         if(access(val5, R_OK) != 0) {
            sprintf(val5,"ln -s %simages %s/images",outdir,val2);
            system(val5);
         }
      } else {
         if(!isdigit(direntp->d_name[0]) && !isdigit(direntp->d_name[1])) continue;
         if(strlen(direntp->d_name) == 4) {
            strcpy(y1,direntp->d_name);
            sprintf(val1,"%s%s",outdir,direntp->d_name);
            dirp2 = opendir(val1);
            while ((direntp2 = readdir( dirp2 )) != NULL) {
               if(!isdigit(direntp2->d_name[0]) && !isdigit(direntp2->d_name[1])) continue;
               sprintf(val2,"%s/%s",val1,direntp2->d_name);
               dirp3 = opendir(val2);
               while ((direntp3 = readdir( dirp3 )) != NULL) {
                  if(!isdigit(direntp3->d_name[0]) && !isdigit(direntp3->d_name[1])) continue;
                  bzero(newname,512);
                  strcpy(warea,direntp2->d_name);
                  if(strstr(warea,"-") != 0) {
                     getword(m1,warea,'-');
                     strcpy(m2,warea);
                     conv_month_name(m1);
                     conv_month_name(m2);
                  } else {
                     strcpy(m1,warea);
                     conv_month_name(m1);
                     strcpy(m2,m1);
                  }
                  strcpy(warea,direntp3->d_name);
                  if(strstr(warea,"-") != 0) {
                     getword(d1,warea,'-');
                     strcpy(d2,warea);
                  } else {
                     strcpy(d1,warea);
                     strcpy(d2,warea);
                  }
                  if(strcmp(df,"u") == 0) sprintf(val4,"%s%s%s%s-%s%s%s",outdir,y1,m1,d1,y1,m2,d2);
                  else if(strcmp(df,"e") == 0) sprintf(val4,"%s%s%s%s-%s%s%s",outdir,d1,m1,y1,d2,m2,y1);
                  sprintf(val5,"%s%s/%s/%s",outdir,y1,direntp2->d_name,direntp3->d_name);
                  if(rename(val5,val4)) {
                     fprintf(stderr, "SARG: (index) rename error - %s\n",strerror(errno));
                     exit(1);
                  }
               }
               (void)rewinddir( dirp3 );
               (void)closedir( dirp3 );
            }
            (void)rewinddir( dirp2 );
            (void)closedir( dirp2 );
         }
//         sprintf(cmd,"rm -rf %s%s\n",outdir,direntp->d_name);
//         system(cmd);
      }
   }
   (void)rewinddir( dirp );
   (void)closedir( dirp );

   if((fp_tmp=fopen(wdir_tmp,"w"))==NULL) {
      fprintf(stderr, "SARG: (index) %s: %s\n",text[45],wdir_tmp);
      exit(1);
   }

   dirp = opendir(outdir);
   while ((direntp = readdir( dirp )) != NULL) {
      if(strcmp(IndexTree,"date") == 0) {
         if(strlen(direntp->d_name) > 4 || !isdigit(direntp->d_name[0]) && !isdigit(direntp->d_name[1])) continue;
         fprintf(fp_tmp,"%s %s\n",direntp->d_name,get_size(outdir,direntp->d_name));
         continue;
      } else {
         if(strstr(direntp->d_name,"-") == 0) continue;
         bzero(newname, 512);
         if(strcmp(df,"u") == 0) {
            strncat(newname,direntp->d_name,4);
            strncpy(month,direntp->d_name+4,3);
         } else {
            strncat(newname,direntp->d_name+5,4);
            strncpy(month,direntp->d_name+2,3);
         }
         month[3]='\0';
         conv_month(month);
         strcat(newname,month);
         if(strcmp(df,"u") == 0) strncat(newname,direntp->d_name+7,2);
         else strncat(newname,direntp->d_name,2);
         obtdate(outdir,direntp->d_name,data);
         obtuser(outdir,direntp->d_name,tuser);
         obttotal(outdir,direntp->d_name,tbytes,tuser,media);
         strcpy(html,data);
         getword(mon,html,' ');
         getword(mon,html,' ');
         getword(day,html,' ');
         getword(hour,html,' ');
         getword(year,html,' ');
         getword(year,html,' ');
         strcpy(html,hour);
         getword(h,html,':');
         getword(m,html,':');
         strcpy(s,html);
         buildymd(day,mon,year,ftime);
         fprintf(fp_tmp,"%s%s%s%s;%s;%s;%s;%s;%s;%s\n",ftime, h, m, s, direntp->d_name, data, tuser, tbytes, media,newname);
         continue;
      }
   }

   if(fp_tmp) fclose(fp_tmp);
   if(strcmp(IndexTree,"file") == 0) {
      (void)rewinddir( dirp );
      (void)closedir( dirp );
   }

   if(strcmp(IndexTree,"date") == 0) {
      if(strcmp(IndexSortOrder,"A") == 0) sprintf(warea,"sort -k 1,1 '%s' -o '%s'", wdir_tmp, wdir_tmp2);
      else sprintf(warea,"sort -r -k 1,1 '%s' -o '%s'", wdir_tmp, wdir_tmp2);
      system(warea);
      unlink(wdir_tmp);
      if((fp_tmp=fopen(wdir_tmp2,"r"))==NULL) {
         fprintf(stderr, "SARG: (index) %s: %s\n",text[45],wdir_tmp2);
         exit(1);
      }
      if((fp_ou=fopen(wdir,"w"))==NULL) {
         fprintf(stderr, "SARG: (index) %s: %s\n",text[45],wdir);
         exit(1);
      }
      write_html_header(fp_ou, ".");
      fprintf(fp_ou,"<tr><th %s>%s</th><th %s>%s</th></tr>\n",hbc1,text[130],hbc1,text[132]);
      while(fgets(wwork1,MAXLEN,fp_tmp)!=NULL) {
         getword(tmp4,wwork1,' ');
         fprintf(fp_ou,"<!-- %s -->\n",tmp4);
         fprintf(fp_ou,"<tr><td class=\"data2\"><a href=\"%s\">%s</a></td><td class=\"data2\">%s</td></tr>\n",tmp4,tmp4,wwork1);
         sprintf(tmp2,"%s%s",outdir,tmp4);
         sprintf(tmp3,"%s%s/index.unsort",outdir,tmp4);
         // Year dir
         if((fp_ou2=fopen(tmp3,"w"))==NULL) {
            fprintf(stderr, "SARG: (index) %s: %s\n",text[45],tmp3);
            exit(1);
         }
         dirp2 = opendir(tmp2);
         while ((direntp2 = readdir( dirp2 )) != NULL) {
            if(!isdigit(direntp2->d_name[0]) && !isdigit(direntp2->d_name[1])) continue;
            fprintf(fp_ou2,"%s\n",direntp2->d_name);
         }
         if(fp_ou2) fclose(fp_ou2);
         (void)rewinddir(dirp2);
         (void)closedir(dirp2);
         sprintf(wdir_tmp3,"%s%s/index.sort",outdir,tmp4);
         if(strcmp(IndexSortOrder,"A") == 0) sprintf(csort,"sort -n '%s' -o '%s'", tmp3, wdir_tmp3);
         else sprintf(csort,"sort -n -r '%s' -o '%s'", tmp3, wdir_tmp3);
         system(csort);
         unlink(tmp3);
         if((fp_tmp2=fopen(wdir_tmp3,"r"))==NULL) {
            fprintf(stderr, "SARG: (index) %s: %s\n",text[45],wdir_tmp3);
            exit(1);
         }
         sprintf(tmp3,"%s%s/index.php",outdir,tmp4);
         if((fp_ou2=fopen(tmp3,"w"))==NULL) {
            fprintf(stderr, "SARG: (index) %s: %s\n",text[45],wdir);
            exit(1);
         }
         write_html_header(fp_ou2,"..");
         fprintf(fp_ou2,"<tr><th %s>%s/%s</th></tr>\n",hbc1,text[130],text[131]);
         while(fgets(wwork1,MAXLEN,fp_tmp2)!=NULL) {
            wwork1[strlen(wwork1)-1]='\0';        
            strcpy(tmp5,wwork1);
            if(strstr(tmp5,"-") != 0) {
               getword(warea,tmp5,'-');
               name_month(warea);
               sprintf(tmp6,"%s-",warea);
               name_month(tmp5);
               sprintf(nmonth,"%s%s",tmp6,tmp5);
            } else {
               strcpy(nmonth,tmp5);
               name_month(nmonth);
            }
            fprintf(fp_ou2,"<tr><td class=\"data2\"><a href=\"%s\">%s %s</a></td></tr>\n",wwork1,tmp4,nmonth);
         
            sprintf(val1,"%s%s/%s",outdir,tmp4,wwork1);
            sprintf(tmp5,"%s%s/%s/index.unsort",outdir,tmp4,wwork1);
            if((fp_ou3=fopen(tmp5,"w"))==NULL) {
               fprintf(stderr, "SARG: (index) %s: %s\n",text[45],tmp5);
               exit(1);
            }
            // month dir
            dirp3 = opendir(val1);
            while ((direntp3 = readdir( dirp3 )) != NULL) {
               if(!isdigit(direntp3->d_name[0]) && !isdigit(direntp3->d_name[1])) continue;
               fprintf(fp_ou3,"%s\n",direntp3->d_name);
            }
            if(fp_ou3) fclose(fp_ou3);
            (void)rewinddir(dirp3);
            (void)closedir(dirp3);
            unlink(wdir_tmp3);
            sprintf(tmp6,"%s%s/%s/index.sort",outdir,tmp4,wwork1);
            if(strcmp(IndexSortOrder,"A") == 0) sprintf(csort,"sort -n '%s' -o '%s'", tmp5, tmp6);
            else sprintf(csort,"sort -n -r '%s' -o '%s'", tmp5, tmp6);
            system(csort);
            unlink(tmp5);
            sprintf(val2,"%s%s/%s/index.php",outdir,tmp4,wwork1);
            sprintf(val3,"%s/%s",tmp4,wwork1);
            unlink(val2);
            if((fp_ou3=fopen(val2,"w"))==NULL) {
               fprintf(stderr, "SARG: (index) %s: %s\n",text[45],val2);
               exit(1);
            }
            if((fp_tmp3=fopen(tmp6,"r"))==NULL) {
               fprintf(stderr, "SARG: (index) %s: %s\n",text[45],tmp6);
               exit(1);
            }
            write_html_header(fp_ou3,"../..");
            fprintf(fp_ou3,"<tr><th %s>%s/%s/%s</th></tr>\n",hbc1,text[130],text[131],text[127]);
            while(fgets(warea,MAXLEN,fp_tmp3)!=NULL) {
               warea[strlen(warea)-1]='\0';
               fprintf(fp_ou3,"<tr><td class=\"data2\"><a href=\"%s\">%s %s %s</a></td></tr>\n",warea,tmp4,nmonth,warea);
            }
            if(fp_tmp3) fclose(fp_tmp3);
            if(fp_ou3) fclose(fp_ou3);
            write_html_trailer(fp_ou3);
            unlink(tmp6);
         }
         write_html_trailer(fp_ou2);
         if(fp_ou2) fclose(fp_ou2);
      }
      (void)rewinddir(dirp);
      (void)closedir(dirp);
      write_html_trailer(fp_ou);
      if(fp_ou) fclose(fp_ou);
      if(fp_tmp) fclose(fp_tmp);
      unlink(tmp6);
      unlink(wdir_tmp2);
   } else {
      if(strcmp(IndexSortOrder,"A") == 0) sprintf(warea,"sort -t';' -k 7,7 -k 1,1 '%s' -o '%s'", wdir_tmp, wdir_tmp2);
      else sprintf(warea,"sort -r -t';' -k 7,7 -k 1,1 '%s' -o '%s'", wdir_tmp, wdir_tmp2);
      system(warea);
      unlink(wdir_tmp);
      if((fp_ou=fopen(wdir,"w"))==NULL) {
         fprintf(stderr, "SARG: (index) %s: %s\n",text[45],wdir);
         exit(1);
      }
      write_html_header(fp_ou,".");
      fprintf(fp_ou,"<tr><th %s>%s</th><th %s>%s</th><th %s>%s</th><th %s>%s</th><th %s>%s</th></tr>\n",hbc1,text[101],hbc1,text[102],hbc1,text[103],hbc1,text[93],hbc1,text[96]);
      if((fp_tmp2=fopen(wdir_tmp2,"r"))==NULL) {
         fprintf(stderr, "SARG: (index) %s: %s\n",text[45],wdir_tmp2);
         exit(1);
      }
      while(fgets(buf,MAXLEN,fp_tmp2)!=NULL) {
         getword(period,buf,';');
         getword(period,buf,';');
         getword(data,buf,';');
         getword(tuser,buf,';');
         getword(tbytes,buf,';');
         getword(media,buf,';');
         fprintf(fp_ou,"<tr><td class=\"data2\"><a href='%s/%s'>%s</a></td><td class=\"data2\">%s</td><td class=\"data\">%s</td><td class=\"data\">%s</td><td class=\"data\">%s</td></tr>\n",period,ReplaceIndex,period,data,tuser,tbytes,media);
      }
      if(fp_tmp2) fclose(fp_tmp2);
      unlink(wdir_tmp2);
   }
 
   fputs("</table></center>",fp_ou);

   zdate(ftime, DateFormat);

   show_info(fp_ou);

   fputs("</body>\n</html>\n",fp_ou);
}
