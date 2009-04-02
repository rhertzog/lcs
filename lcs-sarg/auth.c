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

void htaccess(char *name)
{
   FILE *fp_auth;

   if(strncmp(UserAuthentication,"yes",3) !=0 )
      return;

   sprintf(warea,"%s/%s/.htaccess",dirname,name);
   if((fp_auth=fopen(warea,"w"))==NULL) {
      fprintf(stderr, "SARG: (auth) %s: %s\n",text[45],warea);
      exit(1);
   }

   strcpy(warea,Require);
   getword(buf,warea,'%');
   fputs("AuthUserFile ",fp_auth);
   fputs(AuthUserFile,fp_auth);
   fputs("\n",fp_auth);
   fputs("AuthName ",fp_auth);
   if(strstr(AuthName,"\"") == 0)
      fputs("\"",fp_auth);
   fputs(AuthName,fp_auth);
   if(strstr(AuthName,"\"") == 0)
      fputs("\"",fp_auth);
   fputs("\n",fp_auth);
   fputs("AuthType ",fp_auth);
   fputs(AuthType,fp_auth);
   fputs("\n<Limit GET POST>\n",fp_auth);
   fputs(buf,fp_auth);
   fputs(name,fp_auth);
   fputs("\n</LIMIT>\n",fp_auth);

   fclose(fp_auth);

   return;
}
