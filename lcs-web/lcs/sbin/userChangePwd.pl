#!/usr/bin/perl

use Net::LDAP;
use Crypt::SmbHash;

require '/etc/LcSeConfig.ph';

die("Erreur d'argument.\n") if ($#ARGV != 1);
($uid, $password) = @ARGV;
$dn = "uid=$uid,$peopleDn";
# Génération du mot de passe crypté
$salt  = chr (rand(75) + 48);
$salt .= chr (rand(75) + 48);
$crypt = crypt $password, $salt;

( $lmPassword, $ntPassword ) = ntlmgen $password;

$ldap = Net::LDAP->new(
		       "$slapdIp",
		       port    => "$slapdPort",
		       debug   => "$slapdDebug",
		       timeout => "$slapdTimeout",
		       version => "$slapdVersion"
		      );
$ldap->bind(
	    $adminDn,
	    password => $adminPw
	   );

$res = $ldap->search(
	base     => $baseDn,
        scope    => 'one',
        filter   => 'objectClass=sambaDomain'
);

$domainSid = '';

if (($res->entries)[0]) {
  $domainSid = (($res->entries)[0])->get_value('sambaSID');
}


if ($domainSid ne '') { 
	$res = $ldap->modify(
		$dn,
		replace => {
			userPassword    => "{crypt}$crypt",
	                sambaLMPassword => "$lmPassword",
	                sambaNTPassword => "$ntPassword",
		}
	);
} else {
	$res = $ldap->modify(
		$dn,
		replace => {
			userPassword => "{crypt}$crypt",
			#ntPassword   => $ntPassword,
			#lmPassword   => $lmPassword
		}
	);
}

$res->code && die("Erreur LDAP : " . $res->code . " => " . $res->error . ".\n");

mysqlDbPwUpdate($uid, $password);

exit 0;

sub mysqlDbPwUpdate {

  ($uid, $password)= @_;

  # Création du nom de la base données.
  $db_name = $uid;
  $db_name =~ s/-//g;
  $db_name =~ s/_//g;
  $db_name =~ s/\.//g;
  $db_name .= "_db";
  # Écriture des requêtes SQL de création de l'utilisateur et de sa base
  open  SQL, ">/tmp/UserSql.temp";
  print SQL
    "connect mysql;\n",
    "update user set Password=password('$password') where User='$uid';\n";
  close SQL;
  system("mysql -u $mysqlServerUsername -p$mysqlServerPw < /tmp/UserSql.temp");
  system("mysqladmin -u $mysqlServerUsername -p$mysqlServerPw flush-privileges");

  # Nettoyage
  system("rm /tmp/UserSql.temp");

  return 0;

}
