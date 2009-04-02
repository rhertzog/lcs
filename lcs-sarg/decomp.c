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

void decomp(char *arq, char *zip, char *tmp)
{

   char cmd[1024];

   if(access(arq, R_OK) != 0) {
      sprintf(cmd,"%s: %s",text[64],arq);
      debuga(cmd);
      exit(1);
   }

   if(strstr(arq,".gz") !=0) {
      sprintf(cmd,"%s: %s > %s/sarg-file.in (zcat)",text[62],arq,tmp);
      debuga(cmd);
      sprintf(cmd,"zcat %s > %s/sarg-file.in",arq,tmp);
      system(cmd);
      strcpy(zip,"zcat");
      sprintf(arq,"%s/sarg-file.in",tmp);
      return;
   }

   if(strstr(arq,".bz2") != 0) {
      sprintf(cmd,"%s: %s > %s/sarg-file.in (bzcat)",text[62],arq,tmp);
      debuga(cmd);
      sprintf(cmd,"bzcat %s > %s/sarg-file.in",arq,tmp);
      system(cmd);
      strcpy(zip,"zcat");
      sprintf(arq,"%s/sarg-file.in",tmp);
      return;
   }

   if(strstr(arq,".Z")) {
      sprintf(cmd,"%s: %s (uncompress)",text[62],arq);
      debuga(cmd);
      sprintf(cmd,"uncompress %s",arq);
      system(cmd);
      arq[strlen(arq)-2]='\0';
      strcpy(zip,"compress");
   }

   return;

}


void recomp(char *arq, char *zip) 
{

   char cmd[1024];

   if(access(arq, R_OK) != 0) {
      sprintf(cmd,"%s: %s",text[64],arq);
      debuga(cmd);
      exit(1);
   }

   sprintf(cmd,"%s: %s",text[63],arq);
   debuga(cmd);

   if(strcmp(zip,"gzip") == 0)
      sprintf(cmd,"%s %s",zip,arq);    

   if(strcmp(zip,"compress") == 0)
      sprintf(cmd,"%s %s",zip,arq);

   system(cmd);
   return;

}
