#!/usr/bin/perl

require '/etc/LcSeConfig.ph';

die("Erreur d'argument.\n") if ($#ARGV != 1);
($uid, $cn) = @ARGV;
$uidDn = "uid=$uid,$peopleDn";
$cnDn  = "cn=$cn,$groupsDn";

$res = system("/usr/share/lcs/sbin/groupDelEntry.pl $uidDn $cnDn");

exit O;
