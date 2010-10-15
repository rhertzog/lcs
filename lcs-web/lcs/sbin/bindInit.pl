#!/usr/bin/perl

open MAILNAME, '</etc/mailname';
while (<MAILNAME>) {
	chomp($domain = $_);
}
close MAILNAME;

open INTERFACES, '</etc/network/interfaces';
while (<INTERFACES>) {
	if (/address\s(\d+\.\d+\.\d+)\.(\d+)$/) {
		$localnet = $1;
		$LcsHost = $2;
	}
	if (/gateway\s\d+\.\d+\.\d+\.(\d+)$/) {
		$GwHost = $1;
	}
}
close INTERFACES;

open DB,  ">/etc/bind/localnet.db";
open REV, ">/etc/bind/localnet.rev";

print DB
	";\n",
	"\$TTL\t604800\n",
	"@\tIN\tSOA\t$domain. admin.$domain. (\n",
	"\t\t\t\t1\t; Serial\n",
	"\t\t\t\t604800\t; Refresh\n",
	"\t\t\t\t86400\t; Retry\n",
	"\t\t\t\t2419200\t; Expire\n",
	"\t\t\t\t604800 )\t; Negative Cache TTL\n",
	";\n",
	"@\tIN\tNS\tLcs.$domain.\n";

print REV
	";\n",
	"\$TTL\t604800\n",
	"@\tIN\tSOA\t$domain. admin.$domain. (\n",
	"\t\t\t\t1\t; Serial\n",
	"\t\t\t\t604800\t; Refresh\n",
	"\t\t\t\t86400\t; Retry\n",
	"\t\t\t\t2419200\t; Expire\n",
	"\t\t\t\t604800 )\t; Negative Cache TTL\n",
	";\n",
	"@\tIN\tNS\tLcs.$domain.\n";

foreach $ip (1 .. 255) {
	if ($ip == $LcsHost) {
	print DB  "Lcs\tIN\tA\t$localnet.$ip\n";
	print REV "$ip\tIN\tPTR\tLcs.$domain.\n";
	next;
	}
	if ($ip == $GwHost) {
	print DB  "routeur\tIN\tA\t$localnet.$ip\n";
	print REV "$ip\tIN\tPTR\trouteur.$domain.\n";
	next;
	}
	print DB  "machine$ip\tIN\tA\t$localnet.$ip\n";
	print REV "$ip\tIN\tPTR\tmachine$ip.$domain.\n";
}
close DB; close REV;

$localnet =~ /(\d+)\.(\d+)\.(\d+)/;
$reverse  = $3 . '.' . $2 . '.' . $1 . '.in-addr.arpa';

open NAMEDCONFLOCAL, '>/etc/bind/named.conf.local';
print NAMEDCONFLOCAL
	"zone \"$domain\" {\n",
	"\ttype master;\n",
	"\tfile \"/etc/bind/localnet.db\";\n",
	"};\n",
	"zone \"$reverse\" {\n",
	"\ttype master;\n",
	"\tfile \"/etc/bind/localnet.rev\";\n",
	"};\n";
close NAMEDCONFLOCAL;

open NAMEDCONF, '/etc/bind/named.conf';
while (<NAMEDCONF>) {
  if (/CONFIG LCS/) {
    $found = 1;
    last;
  }
}
close NAMEDCONF;

unless ($found) {
  open NAMEDCONF, '>>/etc/bind/named.conf';
  print NAMEDCONF "include \"/etc/bind/named.conf.local\";";
  close NAMEDCONF;
}

system('/etc/init.d/bind reload > /dev/null 2>&1');
