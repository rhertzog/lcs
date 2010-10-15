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

int blue;
int white;
int lavender;
int darkblue;
int dimgray;
int goldenrod;
int goldenrod2;
int gray;
int silver;
int black;
int x1;
char *font1 = SYSCONFDIR"/fonts/FreeSans.ttf";
char s[15];

#if defined(HAVE_GD) && defined(HAVE_ICONV_H) && defined(gdFTEX_Unicode)
#include <iconv.h>
#define SARGgdImageStringFT I18NgdImageStringFT

char * I18NgdImageStringFT (gdImage * im, int *brect, int fg, char *fontlist, 
                         double ptsize, double angle, int x, int y, char *string)
{
 iconv_t localtoutf;
 char *sstring, *str, *sstr, *retval;
 size_t slen, sslen;

 slen = strlen(string) + 1; // We must include string termination character
 sslen = slen * 2;          // We assume that UTF8 maximum 2 times large than local
 sstring = (char *)malloc(sslen);

 str = (char *) string;
 sstr = (char *) sstring;

 localtoutf = iconv_open ("UTF-8", CharSet);
 iconv (localtoutf, (ICONV_CONST char **)&str, &slen, &sstr, &sslen); 
 iconv_close (localtoutf);

 retval = gdImageStringFTEx (im, brect, fg, fontlist, ptsize, angle, x, y, sstring, gdFTEX_Unicode);
 free(sstring);

 return retval;
}
#else
#define SARGgdImageStringFT gdImageStringFT
#endif

void *bar(long long int *n)
{     
#ifdef HAVE_GD
   gdPoint points[4];
   int brect[8];
   int val=0, x;
   long long int lim, num;
   char v[15];

   static char limits[400][12]={"0","500","1000","2000","3000","4000",
	"5000","6000","7000","8000","9000","10000",
	"20000","30000","40000","50000","70000","90000",
	"110000","130000","150000","170000","190000","210000",
	"230000","250000","280000","310000","340000","370000",
	"400000","430000","450000","460000","490000","500000",
	"550000","600000","650000","700000","750000","800000",
	"850000","900000","950000","1000000","1100000","1200000",
	"1300000","1400000","1500000","1600000","1700000","1800000",
	"1900000","2000000","2100000","2200000","2300000","2400000",
	"2500000","2600000","2700000","2800000","2900000","3000000",
	"3100000","3200000","3300000","3400000","3500000","3600000",
	"3700000","3800000","3900000","4000000","4100000","4200000",
	"4300000","4400000","4500000","4600000","4700000","4800000",
	"4900000","5000000","5100000","5200000","5300000","5400000",
	"5500000","5600000","5700000","5800000","5900000","6000000",
	"6100000","6200000","6300000","6400000","6500000","6600000",
	"6700000","6800000","6900000","7000000","7100000","7200000",
	"7300000","7400000","7500000","7600000","7700000","7800000",
	"7900000","8000000","8100000","8200000","8300000","8400000",
	"8500000","8600000","8700000","8800000","8900000","9000000",
	"9100000","9200000","9300000","9400000","9500000","9600000",
	"9700000","9800000","9900000","10000000","10500000","11000000",
	"11500000","12000000","12500000","13000000","13500000","14000000",
	"14500000","15000000","15500000","16000000","16500000","17000000",
	"17500000","18000000","18500000","19000000","19500000","20000000",
	"21000000","22000000","23000000","24000000","25000000","26000000",
	"27000000","28000000","29000000","30000000","31000000","32000000",
	"33000000","34000000","35000000","36000000","37000000","38000000",
	"39000000","40000000","41000000","42000000","43000000","44000000",
	"45000000","46000000","47000000","48000000","49000000","50000000",
	"51000000","52000000","53000000","54000000","55000000","56000000",
	"57000000","58000000","59000000","60000000","61000000","62000000",
	"63000000","64000000","65000000","66000000","67000000","68000000",
	"69000000","70000000","71000000","72000000","73000000","74000000",
	"75000000","76000000","77000000","78000000","79000000","80000000",
	"81000000","82000000","83000000","84000000","85000000","86000000",
	"87000000","88000000","89000000","90000000","91000000","92000000",
	"93000000","94000000","95000000","96000000","97000000","98000000",
	"99000000","100000000","110000000","120000000","130000000","140000000",
	"150000000","160000000","170000000","180000000","190000000","200000000",
	"210000000","220000000","230000000","240000000","250000000","260000000",
	"270000000","280000000","290000000","300000000","310000000","320000000",
	"330000000","340000000","350000000","360000000","370000000","380000000",
	"390000000","400000000","410000000","420000000","430000000","440000000",
	"450000000","460000000","470000000","480000000","490000000","500000000",
	"510000000","520000000","530000000","540000000","550000000","560000000",
	"570000000","580000000","590000000","600000000","610000000","620000000",
	"630000000","640000000","650000000","660000000","670000000","680000000",
	"690000000","700000000","710000000","720000000","730000000","740000000",
	"750000000","760000000","770000000","780000000","790000000","800000000",
	"810000000","820000000","830000000","840000000","850000000","860000000",
	"870000000","880000000","890000000","900000000","910000000","920000000",
	"930000000","940000000","950000000","960000000","970000000","980000000",
	"990000000","1000000000","1100000000","1200000000","1300000000","1400000000",
	"1500000000","1600000000","1700000000","1800000000","1900000000","2000000000",
	"2100000000","2200000000","2300000000","2400000000","2500000000","2600000000",
	"2700000000","2800000000","2900000000","3000000000","3100000000","3200000000",
	"3300000000","3400000000","3500000000","3600000000","3700000000","3800000000",
	"3900000000","4000000000","4100000000","4200000000","4300000000","4400000000",
	"4500000000","4600000000","4700000000","4800000000","4900000000","5000000000"};

   if(access(font1, R_OK) != 0) {
     fprintf(stderr, "SARG: (grepday) Fontname: %s not found.\n",font1);
     exit(1);
   }

   if(strcmp(GraphDaysBytesBarColor,"orange") == 0) {
      color1 = gdImageColorAllocate(im, 255, 233, 142);
      color2 = gdImageColorAllocate(im, 220, 163, 72);
      color3 = gdImageColorAllocate(im, 255, 198, 107);
   }
   if(strcmp(GraphDaysBytesBarColor,"blue") == 0) {
      color1 = gdImageColorAllocate(im, 62, 80, 167);
      color2 = gdImageColorAllocate(im, 40, 51, 101);
      color3 = gdImageColorAllocate(im, 57, 73, 150);
   }
   if(strcmp(GraphDaysBytesBarColor,"green") == 0) {
      color1 = gdImageColorAllocate(im,120,166,129);
      color2 = gdImageColorAllocate(im,84,113,82);
      color3 = gdImageColorAllocate(im,158,223,167);
   }
   if(strcmp(GraphDaysBytesBarColor,"yellow") == 0) {
      color1 = gdImageColorAllocate(im,185,185,10);
      color2 = gdImageColorAllocate(im,111,111,10);
      color3 = gdImageColorAllocate(im,166,166,10);
   }
   if(strcmp(GraphDaysBytesBarColor,"brown") == 0) {
      color1 = gdImageColorAllocate(im,97,45,27);
      color2 = gdImageColorAllocate(im,60,30,20);
      color3 = gdImageColorAllocate(im,88,41,26);
   }
   if(strcmp(GraphDaysBytesBarColor,"red")  == 0){
      color1 = gdImageColorAllocate(im,185,10,10);
      color2 = gdImageColorAllocate(im,111,10,10);
      color3 = gdImageColorAllocate(im,166,10,10);
   }

   blue = gdImageColorAllocate(im, 0, 0, 255);
   white = gdImageColorAllocate(im, 255, 255, 255);
   dimgray = gdImageColorAllocate(im, 105, 105, 105);  
   goldenrod = gdImageColorAllocate(im, 234, 234, 174);
   goldenrod2 = gdImageColorAllocate(im, 207, 181, 59);

   num = n;
   for(x=0; x<=366; x++) {
      lim = my_atoll(limits[x]);
      if(lim >= num) {
         val = 425 - x;
         break;
      }
   }
   if(x>366) val = 55;

   gdImageFilledRectangle(im, x1, val, x1+11, 425, color3);

   points[0].x = x1+7;
   points[0].y = val-5;
   points[1].x = x1;
   points[1].y = val;
   points[2].x = x1+11;
   points[2].y = val;
   points[3].x = x1+17;
   points[3].y = val-5;
   gdImageFilledPolygon(im, points, 4, color1);

   gdImageLine(im, x1+8, val-2, x1+8, val-10, dimgray);  
   gdImageFilledRectangle(im, x1-2, val-20, x1+18, val-10, goldenrod);
   gdImageRectangle(im, x1-2, val-20, x1+18, val-10, goldenrod2);

   snprintf(v,6,"%s",fixnum(num,0));

   SARGgdImageStringFT(im,&brect[0],black,font1,6,0.0,x1-1,val-12,v);
   
   points[0].x = x1+17;
   points[0].y = val-5;
   points[1].x = x1+11;
   points[1].y = val;
   points[2].x = x1+11;
   points[2].y = 426;
   points[3].x = x1+17;
   points[3].y = 420;
   gdImageFilledPolygon(im, points, 4, color2);

#endif
   return;
}

void greport_day(char *user, int *iprel, int *ipuser)
{
   FILE *fp_in, *pngout;
   int x, y;
   int brect[8];
   char wdirname[MAXLEN];
   char graph[MAXLEN];
   char wuser[255];
   char csort[255];
   char data[20];
   int  count=0;
   char oday[20];
   char day[20];
   char bytes[20];
   long long int tot;
   time_t t;
   struct tm *local;
#ifdef HAVE_GD

   if(strcmp(Graphs,"yes") != 0) {
      unlink(wdirname);
      return;
   }

   im = gdImageCreate(720, 480);

   lavender = gdImageColorAllocate(im, 230, 230, 250);  
   white = gdImageColorAllocate(im, 255, 255, 255);  
   gray = gdImageColorAllocate(im, 192, 192, 192);  
   silver = gdImageColorAllocate(im, 211, 211, 211);  
   black = gdImageColorAllocate(im, 0, 0, 0);  
   blue = gdImageColorAllocate(im, 35, 35, 227);  
   dimgray = gdImageColorAllocate(im, 105, 105, 105);  
   darkblue = gdImageColorAllocate(im, 0, 0, 139);

   gdImageRectangle(im, 0, 0, 719, 479, dimgray);
   gdImageFilledRectangle(im, 60, 60, 700, 420, silver);

   points[0].x = 50;
   points[0].y = 65;
   points[1].x = 50;
   points[1].y = 425;
   points[2].x = 60;
   points[2].y = 420;
   points[3].x = 60;
   points[3].y = 60;
   gdImageFilledPolygon(im, points, 4, gray);

   points[0].x = 60;
   points[0].y = 420;
   points[1].x = 50;
   points[1].y = 425;
   points[2].x = 690;
   points[2].y = 425;
   points[3].x = 700;
   points[3].y = 420;
   gdImageFilledPolygon(im, points, 4, gray);
  
   gdImageLine(im, 50, 65, 50, 430, black);  
   gdImageLine(im, 45, 425, 690, 425, black);  
   gdImageLine(im, 50, 425, 60, 420, black);  
   gdImageLine(im, 60, 420, 60, 60, black);  
   gdImageLine(im, 700, 60, 700, 420, black);  
   gdImageLine(im, 690, 425, 700, 420, black);  

   for(x=415; x>=65; x=x-10) {
      gdImageLine(im, 50, x, 60, x-5, dimgray);  
      gdImageLine(im, 47, x, 50, x, dimgray);  
   }

   for(x=60; x<=420; x=x+10)
      gdImageLine(im, 60, x, 700, x, dimgray);  
  
   gdImageLine(im, 60, 420, 700, 420, black);  

   for(x=70; x<=680; x=x+20)
      gdImageLine(im, x, 425, x, 428, dimgray);  

   y=65;
   for(x=1; x<=31; x++) {
      sprintf(s,"%02d",x);
      SARGgdImageStringFT(im,&brect[0],dimgray,font1,7,0.0,y,437,s);
      y=y+20;
   }

   t = time(NULL);
   local = localtime(&t);
   if(strcmp(DateFormat,"u") == 0)
      strftime(ftime, 127, "%b/%d/%Y %H:%M", local);
   if(strcmp(DateFormat,"e") == 0)
      strftime(ftime, 127, "%d/%b/%Y-%H:%M", local);

   if(dotinuser && strstr(name,"_")) {
      str2=(char *)subs(name,"_",".");
      strcpy(name,str2);
   }

   SARGgdImageStringFT(im,&brect[0],darkblue,font1,7,0.0,620,470,ftime);
   if(strcmp(ShowSargInfo,"yes") == 0) SARGgdImageStringFT(im,&brect[0],darkblue,font1,10,0.0,257,15,"SARG, ");
   SARGgdImageStringFT(im,&brect[0],darkblue,font1,10,0.0,300,15,Title);
   sprintf(warea,"%s: %s",text[89],period);
   SARGgdImageStringFT(im,&brect[0],darkblue,font1,9,0.0,300,27,warea);
   sprintf(warea,"%s: %s",text[90],name);
   SARGgdImageStringFT(im,&brect[0],darkblue,font1,9,0.0,300,38,warea);
   SARGgdImageStringFT(im,&brect[0],dimgray,font1,7,0.0,23,418,"  50K");
   SARGgdImageStringFT(im,&brect[0],dimgray,font1,7,0.0,23,408,"250K");
   SARGgdImageStringFT(im,&brect[0],dimgray,font1,7,0.0,23,398,"500K");
   SARGgdImageStringFT(im,&brect[0],dimgray,font1,7,0.0,23,388,"   1M");
   SARGgdImageStringFT(im,&brect[0],dimgray,font1,7,0.0,23,378,"   2M");
   SARGgdImageStringFT(im,&brect[0],dimgray,font1,7,0.0,23,368,"   3M");
   SARGgdImageStringFT(im,&brect[0],dimgray,font1,7,0.0,23,358,"   4M");
   SARGgdImageStringFT(im,&brect[0],dimgray,font1,7,0.0,23,348,"   5M");
   SARGgdImageStringFT(im,&brect[0],dimgray,font1,7,0.0,23,338,"   6M");
   SARGgdImageStringFT(im,&brect[0],dimgray,font1,7,0.0,23,328,"   7M");
   SARGgdImageStringFT(im,&brect[0],dimgray,font1,7,0.0,23,318,"   8M");
   SARGgdImageStringFT(im,&brect[0],dimgray,font1,7,0.0,23,308,"   9M");
   SARGgdImageStringFT(im,&brect[0],dimgray,font1,7,0.0,23,298," 10M");
   SARGgdImageStringFT(im,&brect[0],dimgray,font1,7,0.0,23,288," 15M");
   SARGgdImageStringFT(im,&brect[0],dimgray,font1,7,0.0,23,278," 20M");
   SARGgdImageStringFT(im,&brect[0],dimgray,font1,7,0.0,23,268," 30M");
   SARGgdImageStringFT(im,&brect[0],dimgray,font1,7,0.0,23,258," 40M");
   SARGgdImageStringFT(im,&brect[0],dimgray,font1,7,0.0,23,248," 50M");
   SARGgdImageStringFT(im,&brect[0],dimgray,font1,7,0.0,23,238," 60M");
   SARGgdImageStringFT(im,&brect[0],dimgray,font1,7,0.0,23,228," 70M");
   SARGgdImageStringFT(im,&brect[0],dimgray,font1,7,0.0,23,218," 80M");
   SARGgdImageStringFT(im,&brect[0],dimgray,font1,7,0.0,23,208," 90M");
   SARGgdImageStringFT(im,&brect[0],dimgray,font1,7,0.0,23,198,"100M");
   SARGgdImageStringFT(im,&brect[0],dimgray,font1,7,0.0,23,188,"200M");
   SARGgdImageStringFT(im,&brect[0],dimgray,font1,7,0.0,23,178,"300M");
   SARGgdImageStringFT(im,&brect[0],dimgray,font1,7,0.0,23,168,"400M");
   SARGgdImageStringFT(im,&brect[0],dimgray,font1,7,0.0,23,158,"500M");
   SARGgdImageStringFT(im,&brect[0],dimgray,font1,7,0.0,23,148,"600M");
   SARGgdImageStringFT(im,&brect[0],dimgray,font1,7,0.0,23,138,"700M");
   SARGgdImageStringFT(im,&brect[0],dimgray,font1,7,0.0,23,128,"800M");
   SARGgdImageStringFT(im,&brect[0],dimgray,font1,7,0.0,23,118,"900M");
   SARGgdImageStringFT(im,&brect[0],dimgray,font1,7,0.0,23,108,"   1G");
   SARGgdImageStringFT(im,&brect[0],dimgray,font1,7,0.0,23, 98,"   2G");
   SARGgdImageStringFT(im,&brect[0],dimgray,font1,7,0.0,23, 88,"   3G");
   SARGgdImageStringFT(im,&brect[0],dimgray,font1,7,0.0,23, 78,"   4G");
   SARGgdImageStringFT(im,&brect[0],dimgray,font1,7,0.0,23, 68,"   5G");
   SARGgdImageStringFT(im,&brect[0],black,font1,10,3.14/2,20,248,text[93]);
   SARGgdImageStringFT(im,&brect[0],black,font1,10,0.0,330,460,text[127]);
 
   sprintf(graph,"%s/%s/graph_day.png",dirname,user);
   sprintf(wdirname,"%s/%s.day",tmp,user);
   sprintf(tmp5,"%s/%s.graph",tmp,user);

   if(access(wdirname, R_OK) != 0)
      return;

   sprintf(csort,"sort -t'/' -k 2,2 -o '%s' '%s'",tmp5,wdirname);
   if(strcmp(DateFormat,"e") == 0)
      sprintf(csort,"sort -t'/' -k 1,1 -o '%s' '%s'",tmp5,wdirname);

   system(csort);

   if((fp_in=fopen(tmp5,"r"))==NULL) {
     fprintf(stderr, "SARG: (grepday) %s: %s\n",text[8],tmp5);
     exit(1);
   }

   if((pngout=fopen(graph,"wb"))==NULL) {
     fprintf(stderr, "SARG: (grepday) %s: %s\n",text[8],graph);
     exit(1);
   }

   strcpy(wuser,user);
   if(strstr(wuser,"_") != 0)
      fixip(wuser);

   if(strcmp(Ip2Name,"yes") == 0)
      if((str=(char *) strstr(name, ".")) != (char *) NULL) {
         if((str=(char *) strstr(str+1, ".")) != (char *) NULL)
            ip2name(wuser);
   }

   if(UserTabFile[0] != '\0') {
      sprintf(warea,":%s:",wuser);
      if((str=(char *) strstr(userfile,warea)) != (char *) NULL ) {
         z1=0;
         str2=(char *) strstr(str+1,":");
         str2++;
         bzero(name, MAXLEN);
         while(str2[z1] != ':') {
            name[z1]=str2[z1];
            z1++;
         }
      } else strcpy(name,wuser);
   } else strcpy(name,user);

   while(fgets(buf,sizeof(buf),fp_in)!=NULL) {
      getword(data,buf,' ');
      getword(day,data,'/');
      if(strcmp(DateFormat,"u") == 0)
         getword(day,data,'/');
      if(!count) {
         strcpy(oday,day);
         count++;
      }
      getword(bytes,buf,' ');
      getword(bytes,buf,' ');
      bytes[strlen(bytes)-1]='\0';

      if(strcmp(oday,day) != 0) {
         strcpy(warea,oday);
         x1 = 44 +(atoi(oday) * 20);
         bar(tot);
         strcpy(oday,day);
         tot=0;
      } else
         tot=tot+my_atoll(bytes);
   }

   if(tot) {
      x1 = 44 +(atoi(day) * 20);
      bar(tot);
   }

   gdImagePng(im, pngout);
   fclose(pngout);
   gdImageDestroy(im);

   fclose(fp_in);
   unlink(wdirname);
   unlink(tmp5);
 
#endif  
   return;
}
