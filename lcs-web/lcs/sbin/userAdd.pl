#!/usr/bin/perl

use Net::Domain;
use Unicode::String qw(latin1 utf8);
use LcSe;

require '/etc/LcSeConfig.ph';

die("Erreur d'argument.\n") if ($#ARGV < 5);
$prenom      =     shift @ARGV;
$categorie   =       pop @ARGV;
$sexe        =       pop @ARGV;
$date        =       pop @ARGV;
$password    =       pop @ARGV;
$nom         = join ' ', @ARGV;

$i = 0;

$prenom = latin1($prenom)->utf8;
$nom = latin1($nom)->utf8;

$lcs_ldap = Net::LDAP->new("$ldap_server");
$lcs_ldap->bind(dn       => "$adminDn",
								password => "$adminPw");

# Gid lcs-users
$gid = getgrnam('lcs-users');

# Fonction attributs GEP -> LDAP (posixAccount)
$res1 = processGepUser('undef', $nom, $prenom, $date, $sexe, $password);
if ($res1 =~ /<tt><strong>(.*)<\/strong>/) {
		$uid = $1;
		$res = 0xffff & system("/usr/share/lcs/sbin/groupAddEntry.pl uid=$uid,$peopleDn cn=$categorie,$groupsDn > /dev/null 2>&1");
		die("Erreur lors de l'ajo�t de l'utilisateur au groupe $categorie.\n") if $res != 0;
}

exit $uid;
