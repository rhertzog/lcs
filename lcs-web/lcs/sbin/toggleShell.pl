#!/usr/bin/perl

use Net::LDAP; 
require '/etc/LcSeConfig.ph'; 
die("Erreur d'argument.\n") if ($#ARGV != 1); 
($dnToModify, $shell) = @ARGV;

$lcs_ldap = Net::LDAP->new(
	"$slapdIp",
	port    => "$slapdPort",
	debug   => "$slapdDebug",
	timeout => "$slapdTimeout",
	version => "$slapdVersion"
);
$lcs_ldap->bind(
	dn       => $adminDn,
	password => $adminPw,
	version  => '3'
);
$res = $lcs_ldap->search(
	base     => "$dnToModify",
	filter   => "uid=*"
);
if (($res->entries)[0]) {
	$homeDirectory = (($res->entries)[0])->get_value('homeDirectory')
} else {
	die "L'utilisateur n'existe pas !";
}
$res = $lcs_ldap->modify(
	$dnToModify,
	replace => { 'loginShell' => $shell }
);
if (($shell eq '/bin/bash' || $shell eq '/bin/true') && $homeDirectory =~ /(.*)\/\.\//) {
	$res = $lcs_ldap->modify(
		$dnToModify,
                replace => { 'homeDirectory' => "$1" } 
	);
}
if ($shell eq '/usr/lib/sftp-server' && $homeDirectory !~ /\/\.\//) {
	$res = $lcs_ldap->modify(
		$dnToModify,
		replace => { 'homeDirectory' => "$homeDirectory/./" }
	);
}
