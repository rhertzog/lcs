#!/usr/bin/perl

require '/etc/LcSeConfig.ph';

die("Erreur d'argument.\n") if ($#ARGV != 0);
$cn = shift @ARGV;
$dn = "cn=$cn,$groupsDn";

$res = system("/usr/share/lcs/sbin/entryDel.pl $dn");

exit O;
