#!/usr/bin/perl

require '/etc/LcSeConfig.ph';

die("Erreur d'argument.\n") if ($#ARGV != 1);
($uid, $cn) = @ARGV;
$uidDn = "uid=$uid,$peopleDn";
$cnDn  = "cn=$cn,$droitsDn";

$res = system("/usr/share/lcs/sbin/rightDelEntry.pl $uidDn $cnDn");

exit O;
