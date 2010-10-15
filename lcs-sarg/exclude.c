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

int vhexclude(char *excludefile, char *url)
{

   char whost[1024];
   char str[strlen(excludefile)];
   char wurl[strlen(url)];
   char wurl2[strlen(url)];

   whost[0]='\0';
   strcpy(str,excludefile);
   strcpy(wurl,url);

   getword(whost,str,' ');
   
   if(strchr(wurl,':') != 0) {
      getword(warea,wurl,':');
      strcpy(wurl,warea);
   }

   while(strcmp(whost,"*END*") != 0) {
      if(strcmp(wurl,whost) == 0)
         return(0);
      if(strchr(whost,'*') != 0) {
         getword(warea,whost,'.');
         getword(warea,wurl,'.');
         if(strcmp(wurl,whost) == 0)
            return(0);
      }
      getword(whost,str,' ');
   }

   return(1);
}


int vuexclude(char *excludeuser, char *user)
{

   char wuser[MAXLEN];

   strcpy(wuser,user);
   strcat(wuser," ");

   if(strstr(excludeuser,wuser) != 0 )
      return(0);

   return(1);
}
