#!/usr/bin/perl

use LcSe;

($prenom, $nom) = @ARGV;
$uid = mkUid($prenom, $nom);
print "$uid\n";
