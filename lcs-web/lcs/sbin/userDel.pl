#!/usr/bin/perl
# userDel.pl 22/02/2014
require '/etc/LcSeConfig.ph';

die("ERR argument.\n") if ($#ARGV != 0);
$uid = shift @ARGV;

die ("ERR : Empty login.\n") if $uid eq '';

die ("ERR : No root login.\n") if $uid eq 'root';

die ("ERR : Improper login.\n") if ($uid =~ m/[^a-z0-9._-]/);

$res = `ldapsearch -xLLL uid="$uid" | grep -c "uid:"`;
die ("ERR : Unknow login. \n") if $res == 0;

$dn = 'uid=' . $uid . ',' . $peopleDn;
$res = system("/usr/share/lcs/sbin/entryDel.pl $dn");

# Prepare Database name
$dbName = $uid;
$dbName =~ s/\.//;
$dbName =~ s/\-//;
$dbName =~ s/_//;
$dbName .= '_db';
# Erase database
open SQL, ">/tmp/UserSql.tmp";
print SQL
  "use mysql;\n",
  "delete from db   where user='$uid';\n",
  "delete from user where user='$uid';\n",
  "use lcs_db;\n",
  "delete from redirmail where pour='$uid';\n";
system("mysqladmin -f -u$mysqlServerUsername -p$mysqlServerPw drop $dbName");
system("mysql -f -u$mysqlServerUsername -p$mysqlServerPw < /tmp/UserSql.tmp");

# Remove user home
system("rm -rf /home/$uid");

# Cleaning
system("rm /tmp/UserSql.tmp");

exit O;
