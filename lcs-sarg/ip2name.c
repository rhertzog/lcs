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

void ip2name(char *ip)
{ 
   u_long addr;
   struct hostent *hp; 
   char **p; 

   if ((int)(addr = inet_addr(ip)) == -1)
      return;

   hp = gethostbyaddr((char *)&addr, sizeof (addr), AF_INET); 
   if (hp == NULL) 
      return;

   for (p = hp->h_addr_list; *p != 0; p++) { 
      struct in_addr in; 

      (void) memcpy(&in.s_addr, *p, sizeof (in.s_addr));         
      (void) sprintf(ip,"%s", hp->h_name); 
   } 

   return;
} 

void name2ip(char *name)
{ 
   struct in_addr ia;
   struct hostent *hp;
   char   work[MAXLEN];
   char   n1[4];
   char   n2[4];
   char   n3[4];
   char   n4[4];

   if(strstr(name,":") > 0) {
      getword(work,name,':');
      strcpy(name,work);
   }

   if((hp=gethostbyname(name))==NULL)
      return;
   else {
      memcpy(&ia.s_addr,hp->h_addr_list[0],sizeof(ia.s_addr));
      ia.s_addr=ntohl(ia.s_addr);
      sprintf(name,"%s",inet_ntoa(ia));
      getword(n4,name,'.');
      getword(n3,name,'.');
      getword(n2,name,'.');
      strcpy(n1,name);
      sprintf(name,"%s.%s.%s.%s",n1,n2,n3,n4);

   }

   return;
} 
