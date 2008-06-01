#!/usr/bin/perl

require '/etc/LcSeConfig.ph';

die("Erreur d'argument.\n") if ($#ARGV != 1);

($uid, $password)= @ARGV;
 
system("mysql -e \"SET PASSWORD FOR \'$uid\'@\'localhost\' = PASSWORD(\'$password\');\" -u $mysqlServerUsername -p$mysqlServerPw");

exit 0;
