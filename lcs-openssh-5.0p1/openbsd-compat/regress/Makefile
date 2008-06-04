# $Id: Makefile.in,v 1.4 2006/08/19 09:12:14 dtucker Exp $

sysconfdir=/etc/ssh
piddir=/var/run
srcdir=.
top_srcdir=../..


CC=i486-linux-gnu-gcc
LD=i486-linux-gnu-gcc
CFLAGS=-Wall -g -O2 -Wall -Wpointer-arith -Wuninitialized -Wsign-compare -Wno-pointer-sign -fstack-protector-all -std=gnu99 
CPPFLAGS=-I. -I.. -I$(srcdir) -I$(srcdir)/..  -DHAVE_CONFIG_H
EXEEXT=
LIBCOMPAT=../libopenbsd-compat.a
LIBS=-lresolv -lcrypto -lutil -lz -lnsl  -lcrypt
LDFLAGS=-Wl,-z,defs -fstack-protector-all $(LIBCOMPAT)

TESTPROGS=closefromtest$(EXEEXT) snprintftest$(EXEEXT) strduptest$(EXEEXT) \
	strtonumtest$(EXEEXT)

all:	t-exec ${OTHERTESTS}

%$(EXEEXT):	%.c
	$(CC) $(CFLAGS) $(CPPFLAGS) $(LDFLAGS) -o $@ $< $(LIBCOMPAT) $(LIBS)

t-exec:	$(TESTPROGS)
	@echo running compat regress tests
	@for TEST in ""$?; do \
		echo "run test $${TEST}" ... 1>&2; \
		./$${TEST}$(EXEEXT) || exit $$? ; \
	done
	@echo finished compat regress tests

clean:
	rm -f *.o *.a core $(TESTPROGS) valid.out

distclean: clean
	rm -f Makefile *~
