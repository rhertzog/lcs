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

void css(FILE *fp_css)
{
   FILE *fp_in;

   if(strlen(ExternalCSSFile) > 0) {
      if((fp_in=fopen(ExternalCSSFile,"r"))==NULL) {
         fprintf(stderr, "SARG: (css) %s: %s\n",text[45],ExternalCSSFile);
         exit(1);
      }
      fputs("<style>\n",fp_css);
      while(fgets(buf,MAXLEN,fp_in)!=NULL) 
         fputs(buf,fp_css);
      fclose(fp_in);
      fputs("</style>\n",fp_css);
      return;
   }

   fprintf(fp_css,"<style>\n.logo {font-family:Verdana,Tahoma,Arial;font-size:11px;color:%s;}\n",LogoTextColor);
   fprintf(fp_css,".body {font-family:%s;color:%s;background-color:%s;}\n",FontFace,TxColor,BgColor);
   fprintf(fp_css,".info {font-family:%s;font-size:9px;}\n", FontFace);
   fprintf(fp_css,".info a:link,a:visited {font-family:%s;color:#0000FF;font-size:9px;text-decoration:none;}\n", FontFace);
   fprintf(fp_css,".title {font-family:%s;font-size:%s;color:%s;background-color:%s;}\n",FontFace,TitleFontSize,TiColor,BgColor);
   fprintf(fp_css,".title2 {font-family:%s;font-size:%s;color:%s;background-color:%s;text-align:left;}\n",FontFace,TitleFontSize,TiColor,BgColor);
   fprintf(fp_css,".title3 {font-family:%s;font-size:%s;color:%s;background-color:%s;text-align:right;}\n",FontFace,TitleFontSize,TiColor,BgColor);
   fprintf(fp_css,".header {font-family:%s;font-size:%s;color:%s;background-color:%s;text-align:left;border-right:1px solid #666666;border-bottom:1px solid #666666;}\n",FontFace,HeaderFontSize,HeaderColor,HeaderBgColor);
   fprintf(fp_css,".header2 {font-family:%s;font-size:%s;color:%s;background-color:%s;text-align:right;border-right:1px solid #666666;border-bottom:1px solid #666666;}\n",FontFace,HeaderFontSize,HeaderColor,HeaderBgColor);
   fprintf(fp_css,".header3 {font-family:%s;font-size:%s;color:%s;background-color:%s;text-align:center;border-right:1px solid #666666;border-bottom:1px solid #666666;}\n",FontFace,HeaderFontSize,HeaderColor,HeaderBgColor);
   fprintf(fp_css,".text {font-family:%s;color:%s;font-size:%s;}\n", FontFace, TxColor, FontSize);
   fprintf(fp_css,".data {font-family:%s;color:%s;font-size:%s;background-color:%s;text-align:right;border-right:1px solid #6A5ACD;border-bottom:1px solid #6A5ACD;}\n", FontFace, TxColor, FontSize, TxBgColor);
   fprintf(fp_css,".data a:link,a:visited {font-family:%s;color:#0000FF;font-size:%s;background-color:%s;text-align:right;text-decoration:none;}\n", FontFace, FontSize, TxBgColor);
   fprintf(fp_css,".data2 {font-family:%s;color:%s;font-size:%s;background-color:%s;border-right:1px solid #6A5ACD;border-bottom:1px solid #6A5ACD;}\n", FontFace, TxColor, FontSize, TxBgColor);
   fprintf(fp_css,".data2 a:link,a:visited {font-family:%s;color:#0000FF;font-size:%s;background-color:%s;text-decoration:none;}\n", FontFace, FontSize, TxBgColor);
   fprintf(fp_css,".data3 {font-family:%s;color:%s;font-size:%s;text-align:center;background-color:%s;border-right:1px solid #6A5ACD;border-bottom:1px solid #6A5ACD;}\n", FontFace, TxColor, FontSize, TxBgColor);
   fprintf(fp_css,".data3 a:link,a:visited {font-family:%s;color:#0000FF;font-size:%s;text-align:center;background-color:%s;text-decoration:none;}\n", FontFace, FontSize, TxBgColor);
   fprintf(fp_css,".text {font-family:%s;color:%s;font-size:%s;text-align:right;}\n", FontFace, TxColor, FontSize, TxBgColor);
   fprintf(fp_css,".link {font-family:%s;font-size:%s;color:#0000FF;}\n", FontFace, FontSize);
   fprintf(fp_css,".link a:link,a:visited {font-family:%s;font-size:%s;color:#0000FF;text-decoration:none;}\n</style>\n", FontFace, FontSize);
}
