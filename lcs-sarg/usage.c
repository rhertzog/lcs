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

void usage(char *prog)
{
  fprintf(stderr, "%s: %s [%s...]\n", prog,text[39],text[40]);
  fprintf(stderr, "%5s-a %s\n"," ",text[23]);
  fprintf(stderr, "%5s-b %s\n"," ",text[71]);
  fprintf(stderr, "%5s-c %s\n"," ",text[69]);
  fprintf(stderr, "%5s-d %s dd/mm/yyyy-dd/mm/yyyy\n"," ",text[24]);
  fprintf(stderr, "%5s-e %s (%s)\n"," ",text[41],text[42]);
  fprintf(stderr, "%5s-f %s (%s/sarg.conf)\n"," ",text[70],SYSCONFDIR);
  fprintf(stderr, "%5s-g %s [e=%s -> dd/mm/yy, u=%s -> mm/dd/yy]\n"," ",text[25],text[26],text[27]);
  fprintf(stderr, "%5s-h Help (this...)\n"," ");
  fprintf(stderr, "%5s-i %s\n"," ",text[43]);
  fprintf(stderr, "%5s-l %s\n"," ",text[37]);
  fprintf(stderr, "%5s-n %s\n"," ",text[65]);
  fprintf(stderr, "%5s-o %s\n"," ",text[38]);
  fprintf(stderr, "%5s-p %s (%s)\n"," ",text[29],text[44]);
  fprintf(stderr, "%5s-s %s [Eg. www.microsoft.com, www.netscape.com]\n"," ",text[30]);
  fprintf(stderr, "%5s-t %s [HH, HH:MM]\n"," ",text[31]);
  fprintf(stderr, "%5s-u %s\n"," ",text[32]);
  fprintf(stderr, "%5s-w %s\n"," ",text[34]);
  fprintf(stderr, "%5s-x %s\n"," ",text[36]);
  fprintf(stderr, "%5s-z %s\n"," ",text[35]);
  fprintf(stderr, "%5s-convert %s\n"," ",text[76]);
  fprintf(stderr, "%5s-split %s\n"," ",text[77]);
  fprintf(stderr, "\n\t%s-%s %s Pedro Lineu Orso -    pedro.orso@gmail.com\n",PGM,VERSION,text[78]);
  fprintf(stderr, "\thttp://sarg.sourceforge.net\n");
  fprintf(stderr, "\n\tPease donate to the sarg project:");
  fprintf(stderr, "\n\t\thttp://sarg.sourceforge.net/donations.php\n\n");

  return;
}
