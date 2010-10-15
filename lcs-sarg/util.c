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

// #define LEGACY_MY_ATOLL
// #define LEGACY_TESTVALIDUSERCHAR

#include "include/conf.h"

static char mtab1[12][4]={"Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"};
static char mtab2[12][3]={"01","02","03","04","05","06","07","08","09","10","11","12"};

/*void fgetword(char *word, char *line, int stop)
{
    //VARIANT N1
    int x;
    
    for (x=0; line[x] && (line[x] != stop); x++) word[x] = line[x];
    word[x] = '\0';

    //VARIANT N2
    char *tchar;
    int difflen;
    
    tchar = strchr(line, stop);
    if (tchar == NULL) strcpy(word, line);
    else
    {
	difflen = tchar - line;
	strncpy(word, line, difflen);
	word[difflen] = '\0';
    }
}*/

void getword(char *word, char *line, int stop)
{
  int x = 0, y = 0;
  int limit=10000;
  char wline[MAXLEN];

  //strcpy(wline,line);

  if(strlen(line) < 3) {
     word[0]='\0';
     return;
  }

  for(x=0; line[x] && (line[x] != stop ) && x<limit; x++) word[x] = line[x];
  if(x == limit) {
    printf("SARG: getword loop detected.\n");
    //printf("SARG: Record=\"%s\"\n",wline);
    printf("SARG: searching for \'x%x\'\n",stop);
    printf("SARG: Maybe you have a broken record or garbage in your access.log file.\n");
    //word[0]='\0';
    exit(1);
  }

  word[x] = '\0';
  
  if (line[x]) ++x;

  while((line[y++] = line[x++]));
}

char *  getword2(char *word, char *line, int stop)
{
  int x = 0;
  int limit=MAXLEN;

  if(strlen(line) < 3) {
     word[0]='\0';
     return( line ) ;
  }

  // printf( "IN Buffer <%s>\n" , line ) ;
  for(x=0;((line[x]) && (line[x] != stop && limit ));x++ , limit-- ) word[x] = line[x];
  if(  ! limit) {
        printf("SARG: getword2 loop detected.\n");
        printf("SARG: Buffer=\"%s\"\n",line);
        printf("SARG: searching for \'x%x\'\n",stop);
        printf("SARG: Maybe you have a broken record or garbage in your access.log file.\n");
        exit(1);
  }

  word[x] = '\0';
  // printf( "Value <%s>\n" , word ) ;
  // printf( "OUT Buffer <%s>\n" , line+x+1 ) ;
  return( line + x +1) ;
}

void getword3(char *word, char *line, int stop)
{
  int x = 0, y = 0;

  for(x=0;(line[x] && (line[x] != stop ));x++) word[x] = line[x];
  word[x] = '\0';
  if(line[x]) ++x;
  while((line[y++] = line[x++]));
}


#ifdef LEGACY_MY_ATOLL

// BMG (bguillory@email.com)
// 3 August 1999
long long int my_atoll (const char *nptr)
#define MAXLLL 30 //maximum number of digits in long long (a guess)
{
  int offset=0, x;
  long long int returnval=0;
  char one_digit[2];

  one_digit[1]='\0';

  // Soak up all the white space
  while (isspace(nptr[offset])) {
    offset++;
  } //while

  //For each character left to right
  //change the character to a single digit
  //multiply what we had before by 10 and add the new digit
  for(x=offset; x<=MAXLLL+offset && isdigit(nptr[x]); x++) {
    sprintf(one_digit, "%c", nptr[x]); //I don't know how else to do this
    returnval = (returnval * 10) + atoi(one_digit);
  } //for

  return returnval;

} //my_atoll

#else

#define MAXLLL 30 //maximum number of digits in long long (a guess)
long long int my_atoll (const char *nptr)
{
  long long int returnval=0;
  char * t = nptr ;
  int max_digits = MAXLLL ;

  // Soak up all the white space
  while (isspace( *t )) {
    t++;
  } //while

  //For each character left to right
  //change the character to a single digit
  //multiply what we had before by 10 and add the new digit

  for( ; --max_digits && isdigit( *t ) ; t++ )
  {
     returnval = ( returnval * 10 ) + ( *t - '0' ) ;
  }

  return returnval;

} //my_atoll

#endif


void my_mkdir(char *name)
{
   char w0[255];
   char w1[255];
   char w2[255];

   if(strncmp(name,".",1) == 0 || strncmp(name,"/",1) != 0) {
      fprintf(stderr,"SARG: Ivalid path (%s). Please, use absolute paths only.\n",name);
      fprintf(stderr,"SARG: process aborted.\n");
      exit(1);
   }

   strcpy(w0,name);
   strcpy(w2,"/");
   getword(w1,w0,'/');
   while(strstr(w0,"/") != 0) {
      getword(w1,w0,'/');
      strcat(w2,w1);
      if(access(w2, R_OK) != 0) {
         if(mkdir(w2,0755)) {
            fprintf(stderr,"SARG: mkdir %s %s\n",w2,strerror(errno));
            fprintf(stderr,"SARG: process aborted.\n");
            exit(1);
         }
      }
      strcat(w2,"/");
   }
   strcat(w2,w0);
   if(access(w2, R_OK) != 0) {
      if(mkdir(w2,0755)) {
         fprintf(stderr,"SARG: mkdir %s %s\n",w2,strerror(errno));
         fprintf(stderr,"SARG: process aborted.\n");
         exit(1);
      }
   }
}


void my_lltoa(unsigned long long int n, char s[], int len)
{
  int i = 0;
  int x = 0;
  char ww[50];
  do {
    s[i++] = (n % 10) + '0';
  } while ((n /= 10) > 0);
  s[i] = '\0';
  {
    int c,i,j;
    for (i = 0, j = strlen(s)-1; i<j; i++, j--)
      {
        c = s[i];
        s[i] = s[j];
        s[j] = c;
      }
  }
  
  if(len) {
     bzero(ww,sizeof(ww));
     i=len-strlen(s)-1;
     for(x=0; x<=i; x++)
        ww[x]='0';
     i=strlen(s);
     strncat(ww,s,i>sizeof(ww)?sizeof(ww):i);
     strcpy(s,ww);
  }
    
}


void builddia(char *dia, char *mes, char *ano, char *df, char *wdata)
{
   char ndia[11];
   char nmes[3];
   int  x;

   if(strlen(dia) < 1) return;

   ndia[0]='\0';
   nmes[0]='\0';

   for(x=0; x<12; x++) {
      if(strcmp(mtab1[x],mes) == 0) {
         strncpy(nmes,mtab2[x],sizeof(nmes)-1);
         nmes[sizeof(nmes)-1]=0;
         break;
      }
   }

   snprintf(wdata,9,"%s%s%s",ano,nmes,dia);

   if(strncmp(df,"u",1) != 0)
      snprintf(ndia,sizeof(ndia),"%s/%s/%s",dia,nmes,ano);
    else
      snprintf(ndia,sizeof(ndia),"%s/%s/%s",nmes,dia,ano);

   strcpy(dia,ndia);

}


void buildymd(char *dia, char *mes, char *ano, char *wdata)
{
   char nmes[3];
   int  x;

   nmes[0]='\0';

   for(x=0; x<12; x++) {
      if(strcmp(mtab1[x],mes) == 0)
         strcpy(nmes,mtab2[x]);
   }

   sprintf(wdata,"%s%s%s",ano,nmes,dia);

}


void conv_month(char *month)
{
   int  x;

   for(x=0; x<12; x++) {
      if(strcmp(mtab1[x],month) == 0)
         strcpy(month,mtab2[x]);
   }

}


void conv_month_name(char *month)
{
   int x;

   for(x=0; x<12; x++) {
      if(strcmp(mtab2[x],month) == 0)
         strcpy(month,mtab1[x]);
   }
}


void name_month(char *month)
{
   int  x, z=atoi(month)-1;
   char m[255];
   char w[20];

   strcpy(m,text[133]);

   for(x=0; x<z; x++)
      getword(w,m,',');
   getword(month,m,',');
}


void fixper(char *tbuf, char *period, char *duntil)
{

   char warea[50];
   char dia[5], mes[5], ano[5];
   int  x;

   warea[0]='\0';

   strncpy(dia,duntil+6,2);
   dia[2]='\0';
   strncpy(mes,duntil+4,2);
   mes[2]='\0';
   strncpy(ano,duntil,4);
   ano[4]='\0';

   for(x=0; x<12; x++) {
      if(strcmp(mtab2[x],mes) == 0)
         strcpy(mes,mtab1[x]);
   }

   if(strcmp(df,"e") == 0)
      sprintf(warea,"%s%s%s",dia,mes,ano);
   if(strcmp(df,"u") == 0)
      sprintf(warea,"%s%s%s",ano,mes,dia);

   strcat(period,warea);
}


void debuga(char *msg)
{
  fprintf(stderr, "SARG: %s\n",msg);

}


void debugaz(char *head, char *msg)
{
  fprintf(stderr, "SARG: (util) %s=%s\n",head, msg);

}


void fixip(char *ip)
{
   char n1[MAXLEN], n2[MAXLEN], n3[MAXLEN];
   char wip[MAXLEN];
   char sep[2]=".";
   int iflag=0;

   strcpy(wip,ip);

   if(strstr(ip,".") != 0) {
      strcpy(sep,"_");
      iflag++;
   }

   if(iflag) {
      getword(n1,wip,'.');
      getword(n2,wip,'.');
      getword(n3,wip,'.');
   } else {
      getword(n1,wip,'_');
      getword(n2,wip,'_');
      getword(n3,wip,'_');
   }
   ip[0]='\0';
   sprintf(ip,"%s%s%s%s%s%s%s",n1,sep,n2,sep,n3,sep,wip);

}


char *fixnum(long long int value, int n)
#define MAXIMO 1024
{
   char num[MAXIMO];
   char buf[MAXIMO * 2];
   char *pbuf;
   char ret[MAXIMO * 2];
   char *pret;
   register int i, j, k;
   static char abbrev[30];
      
   my_lltoa(value, num, 0);

   if(strcmp(DisplayedValues,"abbreviation") == 0) {
      if(strlen(num) <= 3)
         sprintf(abbrev,"%s",num);
      if(strlen(num) == 4 || strlen(num) == 7 || strlen(num) == 10 || strlen(num) == 13) {
         snprintf(abbrev,2,"%s",num);
         strncat(abbrev,".",1);
         strncat(abbrev,num+1,2);
         if(!n) return(abbrev);
         if(strlen(num) == 4)
            strncat(abbrev,"K",1);
         else if(strlen(num) == 7)
            strncat(abbrev,"M",1);
         else if(strlen(num) == 10)
            strncat(abbrev,"G",1);
         else if(strlen(num) == 13)
            strncat(abbrev,"T",1);
      }
      if(strlen(num) == 5 || strlen(num) == 8 || strlen(num) == 11 || strlen(num) == 14) {
         snprintf(abbrev,3,"%s",num);
         strncat(abbrev,".",1);
         strncat(abbrev,num+2,2);
         if(!n) return(abbrev);
         if(strlen(num) == 5)
            strncat(abbrev,"K",1);
         else if(strlen(num) == 8)
            strncat(abbrev,"M",1);
         else if(strlen(num) == 11)
            strncat(abbrev,"G",1);
         else if(strlen(num) == 14)
            strncat(abbrev,"T",1);
      }
      if(strlen(num) == 6 || strlen(num) == 9 || strlen(num) == 12 || strlen(num) == 15) {
         snprintf(abbrev,4,"%s",num);
         strncat(abbrev,".",1);
         strncat(abbrev,num+3,2);
         if(!n) return(abbrev);
         if(strlen(num) == 6)
            strncat(abbrev,"K",1);
         else if(strlen(num) == 9)
            strncat(abbrev,"M",1);
         else if(strlen(num) == 12)
            strncat(abbrev,"G",1);
         else if(strlen(num) == 15)
            strncat(abbrev,"T",1);
      }

      return(abbrev);
   }

   bzero(buf, MAXIMO*2);

   pbuf = buf;
   pret = ret;
   k = 0;

   for ( i = strlen(num) - 1, j = 0 ; i > -1; i--) {
      if ( k == 2 && i != 0 )  {
         k = 0;
         pbuf[j++] = num[i];
         if(strcmp(UseComma,"yes") == 0)
            pbuf[j++] = ',';
         else pbuf[j++] = '.';
         continue;
      }
      pbuf[j] = num[i];
      j++;
      k++;
   }

   pret[0]='\0';

   for ( i = strlen(pbuf) - 1, j = 0 ; i > -1; i--, j++)
      pret[j] = pbuf[i];

      pret[j] = '\0';

      return pret;
}


void buildhref(char * href)
{
   char whref[MAXLEN];

   if(strcmp(href,"./") == 0){
      href[0]='\0';
      strcat(href,"<a href='");
      return;
   }

   href[strlen(href)-1]='\0';
   sprintf(whref,"%s",strrchr(href,'/'));

   strcpy(href,"<a href='");
   strcat(href,whref);
   strcat(href,"/");

   return;

}


char *buildtime(long long int elap)
{

   int num = elap / 1000;
   int hor = 0;
   int min = 0;
   int sec = 0;
   static char buf[12];

   buf[0]='\0';

   hor=num / 3600;
   min=(num % 3600) / 60;
   sec=num % 60;
   sprintf(buf,"%02d:%02d:%02d",hor,min,sec);

   return(buf);

}


void obtdate(char *dirname, char *name, char *data)
{

   FILE *fp_in;
   char wdir[MAXLEN];

   sprintf(wdir,"%s%s/date",dirname,name);
   if ((fp_in = fopen(wdir, "r")) == 0) {
      data[0]='\0';
      return;
   }

   fgets(data,80,fp_in);
   fclose(fp_in);
   data[strlen(data)-1]='\0';

   return;

}


void obtuser(char *dirname, char *name, char *tuser)
{

   FILE *fp_in;
   char wdir[MAXLEN];

   sprintf(wdir,"%s%s/users",dirname,name);
   if((fp_in=fopen(wdir,"r"))==NULL){    
      tuser[0]='\0';
      return;
   }

   fgets(tuser,20,fp_in);
   tuser[strlen(tuser)-1]='\0';
   fclose(fp_in);

   return;

}


void obttotal(char *dirname, char *name, char *tbytes, char *tuser, char *media)
{

   FILE *fp_in;
   char wdir[MAXLEN];
   long long int med=0;
   long long int wtuser=0;

   sprintf(wdir,"%s%s/general",dirname,name);

   if ((fp_in = fopen(wdir, "r")) == 0) {
      tbytes=0;
      return;
   }

   while(fgets(buf,sizeof(buf),fp_in)!=NULL) {
      getword(warea,buf,' ');
      if(strcmp(warea,"TOTAL") != 0)
         continue;
      getword(warea,buf,' ');
      getword(warea,buf,' ');
      twork=my_atoll(warea);
      sprintf(tbytes,"%s",fixnum(twork,1));
   }
   fclose(fp_in);

   if(tuser[0] == '\0') {
      wtuser=0;
      sprintf(media,"%s","0");
      return;
   }
   
   wtuser=my_atoll(tuser);
   med=my_atoll(warea) / wtuser;
   sprintf(media,"%s",fixnum(med,1));

   return;

}


//void gperiod(char *dirname, const char *period)
void gperiod()
{
 
   FILE *fp_ou;
 
   char wdirname[MAXLEN];

   strcpy(wdirname,dirname);
   strcat(wdirname,"/");
   strcat(wdirname,"period");

   if((fp_ou=fopen(wdirname,"w"))==NULL){
      fprintf(stderr, "SARG: (report) %s: %s\n",text[45],wdirname);
      exit(1);
   }
 
   fputs(period,fp_ou);
   fclose(fp_ou);

   if(debug)
      debuga((char *)text[50]);
 
   return;
 
}

void vrfydir(char *dir, char *per1, char *addr, char *site, char *us, char *form)
{
   FILE *img_in, *img_ou;
   int  num=1, count=0;
   int  c;
   char wdir[MAXLEN];
   char per2[MAXLEN];
   char dirname2[MAXLEN];
   char images[512];
   DIR *dirp;
   struct dirent *direntp;

   if(strcmp(IndexTree,"date") == 0) {
      bzero(y1,5);
      bzero(y2,5);
      bzero(d1,3);
      bzero(d2,3);
      bzero(m1,4);
      bzero(m2,4);
      if(strncmp(df,"u",1) == 0) {
         strncpy(y1,period,4);
         strncpy(y2,period+10,4);
         strncpy(m1,period+4,3);
         strncpy(m2,period+14,3);
         strncpy(d1,period+7,2);
         strncpy(d2,period+17,2);
      } else if(strncmp(df,"e",1) == 0) {
         strncpy(d1,period+0,2);
         strncpy(d2,period+10,2);
         strncpy(m1,period+2,3);
         strncpy(m2,period+12,3);
         strncpy(y1,period+5,4);
         strncpy(y2,period+15,4);
      }
      conv_month(m1);
      conv_month(m2);

      sprintf(wdir,"%s%s",outdir,y1);
      if(strcmp(y1,y2) != 0) {
         strncat(wdir,"-",1);
         strncat(wdir,y2,strlen(y2));
      }
      if(access(wdir, R_OK) != 0)
         my_mkdir(wdir);
   
      strncat(wdir,"/",1);
      strncat(wdir,m1,strlen(m1));
      if(strcmp(m1,m2) != 0) {
         strncat(wdir,"-",1);
         strncat(wdir,m2,strlen(m2));
      }
      if(access(wdir, R_OK) != 0)
         my_mkdir(wdir);
   
      strncat(wdir,"/",1);
      strncat(wdir,d1,strlen(d1));
      if(strcmp(d1,d2) != 0) {
         strncat(wdir,"-",1);
         strncat(wdir,d2,strlen(d2));
      }
   } else
      sprintf(wdir,"%s",dir);

   if(strlen(us) > 0) {
      strcat(wdir,"-");
      strcat(wdir,us);
   }
   if(strlen(addr) > 0) {
      strcat(wdir,"-");
      strcat(wdir,addr);
   }
   if(strlen(site) > 0) {
      strcat(wdir,"-");
      strcat(wdir,site);
   }

   if(strcmp(dirname,wdir) != 0)
      strcpy(dirname,wdir);

   if(strcmp(IndexTree,"date") != 0) {
      strcpy(dirname2,dirname);
      if(strcmp(OverwriteReport,"no") == 0) {
         while(num) {
            if(access(wdir,R_OK) == 0) {
               sprintf(wdir,"%s.%d",dirname,num);
               sprintf(per2,"%s.%d",per1,num);
               num++;
               count++;
            } else
               break;
         }

         if(count > 0) {
            if(debug)
               fprintf(stderr, "SARG: %s: %s %s %s\n",text[51],dirname2,text[52],wdir);
            rename(dirname2,wdir);
         }
      } else {
         if(access(dir,R_OK) == 0) {
            sprintf(csort,"rm -r %s",dir);
            system(csort);
         }
      }
      my_mkdir(dirname);
   } else {
      strcpy(dirname2,wdir);
      if(strcmp(OverwriteReport,"no") == 0) {
         while(num) {
            if(access(wdir,R_OK) == 0) {
               sprintf(wdir,"%s.%d",dirname2,num);
               sprintf(per2,"%s.%d",per1,num);
               num++;
               count++;
            } else
               break;
         }
   
         if(count > 0) {
            if(debug)
               fprintf(stderr, "SARG: %s: %s %s %s\n",text[51],dirname2,text[52],wdir);
            rename(dirname2,wdir);
            strcpy(dirname2,wdir);
         }
      } else {
         if(access(wdir,R_OK) == 0) {
            sprintf(csort,"rm -r %s",wdir);
            system(csort);
         }
      }
   
      if(access(wdir, R_OK) != 0)
         my_mkdir(wdir);
   }

   strcpy(dirname2,wdir);
   sprintf(images,"%simages",outdir);
   mkdir(images,0755);

   sprintf(wdir,"date >%s/%s",dirname,"date");
   system(wdir);

   sprintf(per2,"%s/images",SYSCONFDIR);

   dirp = opendir(per2);
   if(dirp==NULL) {
      fprintf(stderr, "SARG: (util) %s %s: %s\n","Can't open directory", per2,strerror(errno));
      return;
   }
   while ((direntp = readdir( dirp )) != NULL ){
      if(strncmp(direntp->d_name,".",1) == 0)
         continue;
      sprintf(val10,"%s/%s",per2,direntp->d_name);
      sprintf(val11,"%s/%s",images,direntp->d_name);
      img_in = fopen(val10, "rb");
      if(img_in!=NULL) {
         img_ou = fopen(val11, "wb");
         if(img_ou!=NULL) {
            while (c!=EOF) {
               c = fgetc(img_in);
               if(c==EOF) break;
               fputc(c,img_ou);
            }
            c=0;
            fclose(img_ou);
         } else
            fprintf(stderr,"SARG: (util): %s %s: %s\n", text[45]?text[45]:"Can't open/create file", val11, strerror(errno));
      } else
         fprintf(stderr,"SARG: (util): %s %s: %s\n", text[45]?text[45]:"Can't open file", val10, strerror(errno));

      fclose(img_in);
   }
   (void) rewinddir(dirp);
   (void) closedir(dirp);

   return;


}


void strip_latin(char *line)
{
   char buf[255];
   char warea[255];

   while(strstr(line,"&") != 0){
      getword(warea,line,'&');
      strncat(warea,line,1);
      getword(buf,line,';');
      strcat(warea,line);
      strcpy(line,warea);
   }

   return;

}

void zdate(char *ftime, char *DateFormat)
{

   time_t t;
   struct tm *local;

   t = time(NULL);
   local = localtime(&t);
   if(strcmp(DateFormat,"u") == 0)
      strftime(ftime, 127, "%b/%d/%Y %H:%M", local);
   if(strcmp(DateFormat,"e") == 0)
      strftime(ftime, 127, "%d/%b/%Y-%H:%M", local);
   if(strcmp(DateFormat,"w") == 0)
      strftime(ftime, 127, "%V-%H-%M", local);

   return;
}


char *fixtime(long int elap)
{

   int num = elap / 1000;
   int hor = 0;
   int min = 0;
   int sec = 0;
   static char buf[12];

   if(strcmp(datetimeby,"bytes") == 0) {
      sprintf(buf,"%s",fixnum(elap,1));
      return buf;
   }

   buf[0]='\0';

   if(num<1) {
      sprintf(buf,"00:00:%02ld",elap);
      return buf;
   }

   hor=num / 3600;
   min=(num % 3600) / 60;
   sec=num % 60;

   sprintf(buf,"%01d:%02d:%02d",hor,min,sec);

   if(strcmp(buf,"0:00:00") == 0)
      strcpy(buf,"0");

   return buf;

}


void date_from(char *date, char *dfrom, char *duntil)
{

   char diaf[10];
   char mesf[10];
   char anof[10];
   char diau[10];
   char mesu[10];
   char anou[10];
   static char wdate[50];


   strcpy(wdate,date);
   if(strstr(wdate,"-") == 0) {
      strcat(wdate,"-");
      strcat(wdate,date);
      strcpy(date,wdate);
   }

   getword(diaf,wdate,'/');
   getword(mesf,wdate,'/');
   getword(anof,wdate,'-');
   getword(diau,wdate,'/');
   getword(mesu,wdate,'/');
   strcpy(anou,wdate);

   sprintf(dfrom,"%s%s%s",anof,mesf,diaf);
   sprintf(duntil,"%s%s%s",anou,mesu,diau);
   return;
}


char *strlow(char *string)
{
      char *s;

      if (string)
      {
            for (s = string; *s; ++s)
                  *s = tolower(*s);
      }

      return string;
}




char *strup(char *string)
{
      char *s;

      if (string)
      {
            for (s = string; *s; ++s)
                  *s = toupper(*s);
      }

      return string;
}


char *subs(char *str, char *from, char *to)
{
   char *tmp;
   char *ret;
   unsigned int ss, st;

   if(strstr(str,from) == 0)
      return (char *) str;

    ss = strlen(str); st = strlen(to) + 10;

    if((ret=(char *) malloc(ss + st))==NULL)
    {
     fprintf(stderr, "SARG: %s (%d):\n",text[59],ss+st);
     exit(1);
    }

    bzero(ret,ss+st);

    tmp = strstr(str, from);
    if ( tmp == (char *) NULL )
       return (char *) NULL;
    strncpy(ret, str, ss - strlen(tmp));
    strcat(ret, to);
    strcat(ret, (tmp+strlen(from)));
    return (char *) ret;
}


void removetmp(char *outdir)
{

   FILE *fp_in;
   char warea[256];

   if(strcmp(RemoveTempFiles,"yes") != 0) {
      return;
   } else {
      if(debug) {
         sprintf(msg,"%s: general, period",text[82]);
         debuga(msg);
      }
      sprintf(warea,"%s/general",outdir);
      if((fp_in=fopen(warea,"r"))==NULL){
         fprintf(stderr, "SARG: (removetmp) %s: %s\n",text[45],warea);
         exit(1);
      }
      while(fgets(buf,sizeof(buf),fp_in)!=NULL) {
         if(strncmp(buf,"TOTAL",5) == 0)
            break;
      }
      fclose(fp_in);
      if((fp_in=fopen(warea,"w"))==NULL){
         fprintf(stderr, "SARG: (removetmp) %s: %s\n",text[45],warea);
         exit(1);
      }
      fputs(buf,fp_in);
      fclose(fp_in);
      sprintf(warea,"%s/period",outdir);
      unlink(warea);
   }
  
   return;
}

void load_excludecodes()
{

   FILE *fp_in;
   char data[80];

   if((fp_in=fopen(ExcludeCodes,"r"))==NULL) {
     fprintf(stderr, "SARG: (util) Cannot open file: %s (exclude_codes)\n",ExcludeCodes);
     exit(1);
   }

   while(fgets(data,80,fp_in)!=NULL) {
      data[strlen(data)-1]='\0';
      strcat(excludecode,data);
      strcat(excludecode,";");
      excode++;
   }

   fclose(fp_in);
   return;

}

int vercode(char *code)
{
   char warea[1024];
   char cod[80];
   int z;

   strcpy(warea,excludecode);
   for(z=0; z<=excode-1; z++) {
      getword(cod,warea,';');
      if(strcmp(code,cod) == 0) 
         return 1;
   }
   return 0;
}

void fixnone(char *str)
{
   if(strstr(str,"\n") != 0)
      str[strlen(str)-1]='\0';
   if(strcmp(str,"none") == 0)
      str[0]='\0';

   return;
}

#ifdef LEGACY_TESTVALIDUSERCHAR
int testvaliduserchar(char *user)
{

   int x=0;
   int y=0;

   for (y=0; y<strlen(UserInvalidChar); y++) {
      for (x=0; x<strlen(user); x++) {
         if(user[x] == UserInvalidChar[y])
            return 1;
      }
   }
   return 0;
}
#else
int testvaliduserchar(char *user)
{

   char * p_UserInvalidChar = UserInvalidChar ;
   char * p_user ;

   while( *p_UserInvalidChar ) {
      p_user = user ;
      while ( *p_user ) {
         if( *p_UserInvalidChar == *p_user )
            return 1;
         p_user++ ;
      }
      p_UserInvalidChar++ ;
   }
   return 0;
}
#endif

int compar( const void *a, const void *b )
{ if( *(int *)a > *(int *)b ) return 1;
  if( *(int *)a < *(int *)b ) return -1;
  return 0;
}

int getnumlist( char *buf, numlist *list, const int len, const int maxvalue )
{ int i, j, d, flag, r1, r2;
  char *pbuf, **bp, *strbufs[ 24 ];
  
  bp = strbufs;
  strtok( buf, " \t" );
  for( *bp = strtok( NULL, "," ), list->len = 0; *bp; *bp = strtok( NULL, "," ) )
   { if( ++bp >= &strbufs[ 24 ] )
       break;
     list->len++;
   }
  if( ! list->len )      
     return -1;
  d = 0;
  for( i = 0; i < list->len; i++ )  
   { if( strstr( strbufs[ i ], "-" ) != 0 )
      { pbuf = strbufs[ i ];
        strtok( pbuf, "-" );
        pbuf = strtok( NULL, "\0" );
        r1 = atoi( strbufs[ i ] );
        if( ( r2 = atoi( pbuf ) ) >= maxvalue || r1 >= r2 )
          return -1;
        if( i + d + ( r2 - r1 ) + 1 <= len )
         { for( j = r1; j <= r2; j++ )
             list->list[ i + d++ ] = j;
           d--;
         }
      }
     else
       if( ( list->list[ i + d ] = atoi( strbufs[ i ] ) ) >= maxvalue )
          return 1;
   }
  list->len += d;
  qsort( list->list, list->len, sizeof( int ), compar );
  do
   { flag = 0;
     for( i = 0; i < list->len - 1; i++ )
       if( list->list[ i ] == list->list[ i + 1 ] )
        { for( j = i + 1; j < list->len; j++ )
            list->list[ j - 1 ] = list->list[ j ];
          list->len--;
          flag = 1;
          break;
        }
   } while( flag );
  return 0;
}


void show_info(FILE *fp_ou)
{
  if(strcmp(ShowSargInfo,"yes") != 0) return;
  zdate(ftime, DateFormat);
  fprintf(fp_ou,"<center><table><tr><td><br><br></td><td class=\"info\">%s <a href='%s'><font class=\"info\">%s-%s</font></a> %s %s</td></tr></table></center>\n",text[108],URL,PGM,VERSION,text[109],ftime);
}

void show_sarg(FILE *fp_ou, char *ind)
{
   if(strcmp(ShowSargLogo,"yes") == 0) fprintf(fp_ou,"<center><table cellpadding=0 cellspacing=0>\n<tr><th class=\"logo\"><a href=\"http://sarg.sourceforge.net\"><img src=\"%s/images/sarg.png\" border=\"0\" align=\"absmiddle\" title=\"SARG, Squid Analysis Report Generator. Logo by Osamu Matsuzaki\"></a>&nbsp;<font class=\"logo\">Squid Analysis Report Generator</font></th></tr>\n<tr><th class=\"title\">&nbsp</th></tr>\n<table>\n",ind);
}

get_size(char *path, char *file)
{
   FILE *fp;
   char response[255];

   sprintf(cmd,"du -skh %s%s",path,file);
   fp = popen(cmd, "r");
   fgets(response, 255, fp);
   getword(val5,response,'\t');
   pclose(fp);

   return (val5);
}


void write_html_header(FILE *fp_ou, char * ind)
{
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
      fputs("<!--util-->\n",fp_ou);
   fprintf(fp_ou, "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">\n<html>\n<head>\n  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=%s\">\n</head>\n",CharSet);
   css(fp_ou);
   fprintf(fp_ou,"<body style=\"font-family:%s;font-size:%s;background-color:%s;background-image:url(%s)\">\n",FontFace,TitleFontSize,BgColor,BgImage);
   if(strlen(LogoImage) > 0) fprintf(fp_ou, "<center><table cellpadding=\"0\" cellspacing=\"0\">\n<tr><th class=\"logo\"><img src='%s' border=0 align=absmiddle width=%s height=%s>&nbsp;%s</th></tr>\n<tr><td height=\"5\"></td></tr>\n</table>\n",LogoImage,Width,Height,LogoText);
   show_sarg(fp_ou, ind);
   fprintf(fp_ou,"<center><table cellpadding=\"0\" cellspacing=\"0\">\n<tr><th class=\"title\">%s</th></tr>\n</table></center>\n<center><table cellpadding=\"1\" cellspacing=\"2\">\n<tr><td></td><td></td></tr>\n",Title);
}


char url_module(char *url, char *w2)
{
   int x, y;
   char w[255];

   bzero(w, 255);
   bzero(w2, 255);
   y=0;
   for(x=strlen(url)-1; x>=0; x--) {
      if(url[x] == '/' || y>255) break;
      w[y]=url[x];
      y++;
   }

   y=0;
   for(x=strlen(w)-1; x>=0; x--) {
      w2[y]=w[x];
      y++;
   }

   return;
}


void write_html_trailer(FILE *fp_ou)
{
   fputs("</table></center>\n",fp_ou);
   zdate(ftime, DateFormat);
   show_info(fp_ou);
   fputs("</body>\n</html>\n",fp_ou);
}

void version()
{
   printf("SARG Version: %s\n",VERSION);
   exit(0);
}
