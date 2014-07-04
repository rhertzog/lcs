#!/usr/bin/perl

use Unicode::String qw(latin1 utf8);

require '/etc/LcSeConfig.ph';

($groupType, $cn, @description) = @ARGV;

die("Erreur d'argument" . ".\n") if ($#ARGV < 2 or ($groupType != 1 and $groupType != 2));

$groupType = 'posixGroup' if $groupType == 1;
$groupType = 'groupOfNames' if $groupType == 2;

$description = join ' ', @description;
$description = latin1($description)->utf8;

$gid = getFirstFreeGid(1000);

@args = (
	 "/usr/share/lcs/sbin/entryAdd.pl",
	 "cn=$cn,$groupsDn",
	 "cn=$cn",
	 "objectClass=top",
	 "objectClass=$groupType",
	 "description=$description",
	);

$optionalArg = "gidNumber=$gid";
push @args, $optionalArg if $groupType eq 'posixGroup';

$res = 0xffff & system @args;
die("Erreur lors de l'ajoût du groupe.\n") if $res != 0;

exit 0;

sub getFirstFreeGid {
  my $firstFreeGid = shift;
  while (defined(getgrgid($firstFreeGid))) {
    $firstFreeGid++;
  }
  return $firstFreeGid;
}
