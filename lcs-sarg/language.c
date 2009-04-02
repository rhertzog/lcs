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

void language_load(char *language)
{

   FILE *fp_text;
   int record=0;

   sprintf(warea,"%s/languages/%s",SYSCONFDIR,language);

   if((fp_text=fopen(warea,"r"))==NULL) {
     fprintf(stderr, "SARG: (language) Cannot open language file: %s\n",warea);
     exit(1);
   }

   while(fgets(buf,MAXLEN,fp_text)!=NULL) {
      getword(warea,buf,'"');
      getword(warea,buf,'"');
      strcpy(text[record],warea);

      if(langcode)
      printf("%d %s\n",record,warea);

      record++;
   }

   fclose(fp_text);

   return;
}
