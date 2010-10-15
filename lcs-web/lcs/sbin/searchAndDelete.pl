#!/usr/bin/perl

# Version du 27/02/09
#   - Recherche des comptes présents dans ou=People et membre d'aucun groupe.
#   - déplacement vers ou=Trash


use LcSe;
use Crypt::SmbHash;

# Connexion LDAP
# ==============
$lcs_ldap = Net::LDAP->new("$slapdIp");
$lcs_ldap->bind(
		dn       => $adminDn,
		password => $adminPw,
		version  => '3'
	       );

# Recherche de la presence du SID du domaine
# ==========================================
$res = $lcs_ldap->search(
	base     => $baseDn,
        scope    => 'one',
        filter   => 'objectClass=sambaDomain'
);

$domainSid = '';

if (($res->entries)[0]) {
  $domainSid = (($res->entries)[0])->get_value('sambaSID');
}

print "domain sid : $domainSid";

# Création de la poubelle le cas échéant
# ======================================
$trashOuName = 'Trash';
$trashRdn    = "ou=$trashOuName";
$trashDn     = "$trashRdn,$baseDn";
$trashSearch = $lcs_ldap->search(base     => "$baseDn",
				 scope    => 'one',
				 filter   => "$trashRdn");
warn $trashSearch->error if $trashSearch->code;
unless (($trashSearch->entries)[0]) {
  # Création
  # --------
  @trashAttributes = ( 'objectClass', 'organizationalUnit',
		       'ou',          "$trashOuName" );
  $creationTrash = $lcs_ldap->add( "$trashDn",
				   attrs => \@trashAttributes );
  warn $creationTrash->error if $creationTrash->code;
}

# Recherche des utilisateurs concernés et Action
# ==============================================
#
# Recherche de tous les utilisateurs
# ----------------------------------
$peoples = $lcs_ldap->search(base     => "$peopleDn",
			     scope    => 'one',
			     filter   => 'uid=*');
foreach $people ($peoples->entries) {
  $dn  = $people->dn;
  $uid = $people->get_value('uid');
  #next if $uid eq 'admin';
  next if ($uid eq 'admin' or $uid eq 'webmaster.etab' or $uid eq 'wetab' or $uid eq 'spip.manager' or $uid eq 'etabw' or $uid eq 'ldapadm');  
  # Vérification de l'appartenance à des groupes
  # --------------------------------------------
  $memberOfAGroupOfNames = $lcs_ldap->search(base     => "$groupsDn",
					     scope    => 'one',
					     filter   => "member=$dn");
  warn $memberOfAGroupOfNames->error if $memberOfAGroupOfNames->code;
  next if ($memberOfAGroupOfNames->entries)[0];
  $memberOfAPosixGroup   = $lcs_ldap->search(base     => "$groupsDn",
					     scope    => 'one',
					     filter   => "(&(!(cn=overfill))(memberUid=$uid))");
  warn $memberOfAPosixGroup->error if $memberOfAPosixGroup->code;
  next if ($memberOfAPosixGroup->entries)[0];
  # Désactivation du compte SAMBA et déplacement le cas échéant
  # -----------------------------------------------------------
  if ($domainSid ne '') { 
    $sambaDesactiv = $lcs_ldap->modify(
        $dn,
        replace => {
            sambaacctFlags => '[UD         ]',
        }
    );
  } else {
    $sambaDesactiv = $lcs_ldap->modify(
        $dn,
        replace => {
            acctFlags => '[UD         ]',
        }
    );
  }
  warn $sambaDesactiv->error if $sambaDesactiv->code;
  $trashSearch = $lcs_ldap->search(base     => "$trashDn",
				   scope    => 'one',
				   filter   => "uid=$uid");
  if (($trashSearch->entries)[0]) {
    $emptyTrash = $lcs_ldap->delete(($trashSearch->entries)[0]);
    warn $emptyTrash->error if $emptyTrash->code;
  }
  $move2trash = $lcs_ldap->moddn( $dn,
				  newrdn      => "uid=$uid",
				  newsuperior => "$trashDn");
  warn $move2trash->error if $move2trash->code;
}

$lcs_ldap->unbind();

sub emptyTrash {
  # Vidage de la poubelle
  # ---------------------
  $trashSearch = $lcs_ldap->search(base     => "$trashDn",
				   scope    => 'one',
				   filter   => "uid=*");
  foreach $entry ($trashSearch->entries) {
    $emptyTrash = $lcs_ldap->delete($entry);
    warn $emptyTrash->error if $emptyTrash->code;
  }
}
