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

void ccharset()
{
   if(strcmp(CharSet,"Latin2") == 0) strcpy(CharSet,"ISO-8859-2");
   else if(strcmp(CharSet,"Latin3") == 0) strcpy(CharSet,"ISO-8859-3");
   else if(strcmp(CharSet,"Latin4") == 0) strcpy(CharSet,"ISO-8859-4");
   else if(strcmp(CharSet,"Cyrillic") == 0) strcpy(CharSet,"ISO-8859-5");
   else if(strcmp(CharSet,"Arabic") == 0) strcpy(CharSet,"ISO-8859-6");
   else if(strcmp(CharSet,"Greek") == 0) strcpy(CharSet,"ISO-8859-7");
   else if(strcmp(CharSet,"Hebrew") == 0) strcpy(CharSet,"ISO-8859-8");
   else if(strcmp(CharSet,"Latin5") == 0) strcpy(CharSet,"ISO-8859-9");
   else if(strcmp(CharSet,"Latin6") == 0) strcpy(CharSet,"ISO-8859-10");
   else if(strcmp(CharSet,"Windows-1251") == 0) strcpy(CharSet,"Windows-1251");
   else if(strcmp(CharSet,"Koi8-r") == 0) strcpy(CharSet,"KOI8-R");
   return;
}
