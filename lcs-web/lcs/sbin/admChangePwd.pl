#!/usr/bin/perl

use Net::LDAP;
use Crypt::SmbHash;

require '/etc/LcSeConfig.ph';

die("Erreur d'argument.\n") if ($#ARGV != 1);
($OLD, $NEW) = @ARGV;


# Génération du mot de passe crypté
$salt  = chr (rand(75) + 48);
$salt .= chr (rand(75) + 48);
$crypt = crypt $NEW, $salt;

$ldap = Net::LDAP->new(
		       "$slapdIp",
		       port    => "$slapdPort",
		       debug   => "$slapdDebug",
		       timeout => "$slapdTimeout",
		       version => "$slapdVersion"
		      );

$ldap->bind(
	    $adminDn,
	    password => $OLD
	   );

$res = $ldap->modify(
	$adminDn,
	replace => { userPassword => "{crypt}$crypt"}
);

$res->code && die("Erreur LDAP : " . $res->code . " => " . $res->error . ".\n");
exit 0;

