#!/usr/bin/perl

# $Id$
use Crypt::SmbHash;

$password = $ARGV[0];
if ( !$password ) {
                print "Not enough arguments\n";
                print "Usage: $0 password\n";
                exit 1;
}

ntlmgen $password, $lm, $nt;

#return "$lm $nt";
print "$lm $nt";
