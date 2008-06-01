#!/usr/bin/perl

# Rechercher le compte
# Verifier ou se trouve le compte
# Le remettre en place ou le recreer le cas echeant
# L'affecter a un groupe principal le cas echeant

use LcSe;

# Connexion LDAP
# ==============
$lcs_ldap = Net::LDAP->new("$slapdIp");
$lcs_ldap->bind(
                dn       => $adminDn,
                password => $adminPw,
                version  => '3'
               );

foreach $possibleUid ('wetab', 'webmaster.etab', 'etabw') {
	print "Recheche $possibleUid :\n";
	print "\tdans l'annuaire...\n";
	$res = $lcs_ldap->search(
		base     => "$baseDn",
		scope    => 'sub',
		filter   => "uid=$possibleUid"
	);
	if (($res->entries)[0]) {
		$dn = (($res->entries)[0])->dn;
	}
	print "\tdans le repertoire /home...\n";
	if (-d "/home/$possibleUid") {
		$homeUid = $possibleUid;
		print "\t\tHOME $possibleUid trouve !!!\n";
	}
}
if ( $dn =~ /People/ or $dn =~ /Trash/ ) {
	print "Trouve dans l'annuaire : $dn\n";
	if ($dn =~ /People/) {
		print "\tCompte en place dans la branche People, ajout au groupe Profs.\n";
		system("/usr/share/lcs/sbin/groupAddEntry.pl $dn $profsDn > /dev/null 2>&1");
	} elsif ( $dn =~ /Trash/ ) {
	       print "\tDestruction du compte en place dans la branche Trash.\n";
               system ("/usr/bin/ldapdelete -x -D cn=admin,$baseDn -w$adminPw \"$dn\"");
	       print "\tRecreation du compte.\n";
               system('/usr/share/lcs/sbin/userAdd.pl Webmaster Etab WeB 00000000 M Profs');
	       # Recherche du nouvel uid pour le cas ou l'uidPolicy a changé 
	       # entre la recréation et le passage a la poubelle
               $newuid=`/usr/bin/ldapsearch -xLL cn='Webmaster Etab' | grep uid: | cut -d \" \" -f 2`;
	       chomp ($newuid);
	       die "Erreur lors la recherche de l'uid nouvellement créé.\n" if $newuid eq '';
	       if ( $homeUid ) {
		 if ( $homeUid ne $newuid ) {  
	           system("/bin/chown -R $newuid /home/$homeUid; /bin/mv /home/$homeUid /home/$newuid");
		 } else { system("/bin/chown -R $homeUid /home/$homeUid"); }
	       }   
	}    
    
} else {
        # Le compte n'est pas dans l'annuaire, recréation
	print "Introuvable : Creation du compte.\n";
	system('/usr/share/lcs/sbin/userAdd.pl Webmaster Etab WeB 00000000 M Profs');
        $newuid=`/usr/bin/ldapsearch -xLL cn='Webmaster Etab' | grep uid: | cut -d \" \" -f 2`;
        chomp ($newuid);
        die "Erreur lors la recherche de l'uid nouvellement créé.\n" if $newuid eq '';
	if ( $homeUid ) {
	  if ( $homeUid ne $newuid ) {  
	    system("/bin/chown -R $newuid /home/$homeUid; /bin/mv /home/$homeUid /home/$newuid");
	  } else { system("/bin/chown -R $homeUid /home/$homeUid"); }
	}
}
