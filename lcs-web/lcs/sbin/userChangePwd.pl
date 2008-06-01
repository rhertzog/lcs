#!/usr/bin/perl

use Net::LDAP;
use Crypt::SmbHash;

require '/etc/LcSeConfig.ph';

die("Erreur d'argument.\n") if ($#ARGV != 1);
($uid, $password) = @ARGV;
$dn = "uid=$uid,$peopleDn";
# G�n�ration du mot de passe crypt�
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

  # Cr�ation du nom de la base donn�es.
  $db_name = $uid;
  $db_name =~ s/-//g;
  $db_name =~ s/_//g;
  $db_name =~ s/\.//g;
  $db_name .= "_db";
  # �criture des requ�tes SQL de cr�ation de l'utilisateur et de sa base
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
