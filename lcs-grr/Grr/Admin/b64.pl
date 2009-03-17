#!/usr/bin/perl -w
#
# decode-base64

use MIME::Base64;
use Unicode::String qw(latin1 utf8);

die("Erreur d'argument.\n") if ($#ARGV != 0);
$cn = shift @ARGV;

my ($res) = decode_base64($cn);
$fullname = utf8($res)->latin1;
print "$fullname\n";

