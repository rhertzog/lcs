#!/usr/bin/perl

require '/etc/LcSeConfig.ph';

die("Erreur d'argument.\n") if ($#ARGV != 0);
$uid = shift @ARGV;

$dn = 'uid=' . $uid . ',' . $peopleDn;
#$uid =~ /^(\w*)\.(\w*)$/;
$dbName = $uid;
$dbName =~ s/\.//;
$dbName =~ s/\-//;
$dbName =~ s/_//;
$dbName .= '_db';

die if $uid eq '';

$res = system("/usr/share/lcs/sbin/entryDel.pl $dn");

# Suppression de la base de données
open SQL, ">/tmp/UserSql.tmp";
print SQL
  "use mysql;\n",
  "delete from db   where user='$uid';\n",
  "delete from user where user='$uid';\n",
  "use lcs_db;\n",
  "delete from redirmail where pour='$uid';\n";
system("mysqladmin -f -u$mysqlServerUsername -p$mysqlServerPw drop $dbName");
system("mysql -f -u$mysqlServerUsername -p$mysqlServerPw < /tmp/UserSql.tmp");

# Suppression du home perso...
system("rm -r /home/$uid");

# Nettoyage
system("rm /tmp/UserSql.tmp");

exit O;
