#!/usr/bin/perl

use Net::LDAP;
use Crypt::SmbHash;
use Encode;

require '/etc/LcSeConfig.ph';

if (defined($ad_auth_delegation) and $ad_auth_delegation eq "true") {
    usage() if ($#ARGV < 2);
} else {
    usage() if ($#ARGV < 1);
}

my ($uid, $password, $oldpass) = @ARGV;
my $dn = "uid=$uid,$peopleDn";
# Génération du mot de passe crypté
my $salt  = chr (rand(75) + 48);
$salt .= chr (rand(75) + 48);
my $crypt = crypt $password, $salt;

my ($lmPassword, $ntPassword) = ntlmgen $password;

my $ldap = Net::LDAP->new(
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

my $res = $ldap->search(
	base     => $baseDn,
        scope    => 'one',
        filter   => 'objectClass=sambaDomain'
);

my $domainSid = '';

if ($res->entry(0)) {
	$domainSid = $res->entry(0)->get_value('sambaSID');
}

my $sasl_auth = '';
# Verify if authentication happens through SASL
$res  = $ldap->search(
	base => $peopleDn,
	filter => "uid=$uid",
	attrs => ['userPassword'],
);
if ($res->entry(0) and $res->entry(0)->get_value('userPassword') =~ /^{sasl}/) {
	$sasl_auth = 1;
}

if (defined($ad_auth_delegation) and $ad_auth_delegation eq "true" and $sasl_auth) {
	## Update password in active directory
	# Connect to AD server
	my $ad_ldap = Net::LDAP->new($ad_server, timeout => $slapdTimeout,
				     scheme => "ldaps");
	die "Can't connect to Active Directory $ad_server\n" unless $ad_ldap;
	my $mesg = $ad_ldap->bind($ad_bind_dn, password => $ad_bind_pw,
				  version => 3);
	check_ldap_success($mesg, "binding on the AD with system account");

	# Identify the account whose password must be changed
	my $search = $ad_ldap->search(base => $ad_base_dn, scope => "sub",
				      filter => "sAMAccountName=$uid",
				      attrs => []);
	my $entry = $search->shift_entry();
	die "No user matching sAMAccountName=$uid found in Active Directory.\n"
	    unless defined $entry;
	my $user_dn = $entry->dn();

	# Modify password on Active Directory
	if ($oldpass) { # Login with the user account if we can
		$mesg = $ad_ldap->bind($user_dn, password => $oldpass,
				       version => 3);
		check_ldap_success($mesg, "binding on the AD as the user");
	}

	$mesg = $ad_ldap->modify($user_dn,
		replace => {
			unicodePwd => adPasswd($password)
		}
	);
	check_ldap_success($mesg, "resetting password on AD") unless $oldpass;
	if ($mesg->code) {
	    # Password reset failed, try an update (must confirm old pass)
	    $mesg = $ad_ldap->modify($user_dn,
		    changes => [
			    delete => [unicodePwd => adPasswd($oldpass)],
			    add => [unicodePwd => adPasswd($password)]
		    ]
	    );
	    check_ldap_success($mesg, "updating password on AD");
	}

	# Modify password on LCS to record the delegation to SASL
	$res = $ldap->modify(
		$dn,
		replace => {
			userPassword => "{sasl}$uid"
		}
	);
	check_ldap_success($res, "updating userPasswd (sasl delegation)");

} else {
	# Update password in LCS LDAP server
	$res = $ldap->modify(
		$dn,
		replace => {
			userPassword => "{crypt}$crypt"
		}
	);
	check_ldap_success($res, "updating userPasswd (crypted password)");
}

if ($domainSid ne '') {
	$res = $ldap->modify(
		$dn,
		replace => {
	                sambaLMPassword => "$lmPassword",
	                sambaNTPassword => "$ntPassword",
		}
	);
	check_ldap_success($res, "updating sambaLMPassword/sambaNTPassword");
}

mysqlDbPwUpdate($uid, $password);

exit 0;

sub adPasswd($) {
    my ($passwd) = @_;
    return Encode::encode("UTF-16LE", qq("$passwd"));
}

sub check_ldap_success {
    my ($res, $opdesc) = @_;
    if ($res->code) {
	print STDERR "LDAP operation failed: $opdesc\n";
	die("LDAP ERROR: " . $res->code . " => " . $res->error . ".\n");
    }
}

sub usage {
    print STDERR "Usage: \n";
    printf STDERR "%s <uid> <new-password> <old-password>\n", $ARGV[0];
    exit 1;
}

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
