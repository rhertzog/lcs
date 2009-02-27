#!/usr/bin/perl

require '/etc/LcSeConfig.ph';

die("Erreur d'argument.\n") if ($#ARGV != 0);
$cn = shift @ARGV;
$dn = "cn=$cn,$groupsDn";

$res = system("/usr/share/lcs/sbin/entryDel.pl $dn");

# Generate samba's configuration (group's shares)
`sudo /usr/share/lcs/scripts/execution_script_plugin.sh /usr/share/lcs/sbin/lcs-smb-config &>/dev/null &` 
	if(-x "/usr/share/lcs/sbin/lcs-smb-config");

exit 0;
