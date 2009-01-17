#!/usr/bin/perl

use CGI qw(:standard :html3);
use Encode qw(encode decode);
use Text::Unaccent;
use Net::LDAP;
use Net::Domain;
use CGI::Cookie;
use DBI;

$domain = Net::Domain::hostdomain();
$hostname = Net::Domain::hostfqdn();

require '/etc/LcSeConfig.ph';

# Pour stoker les UID transmis par le fichier f_uid.txt
%Admin_UID = ();

# D�finition des formats DBF
# --------------------------
%format = (
	   'f_ele'  => 'A5A25A25xA8A1x163A5',
	   'f_men'  => 'x6A5x3A8x16A10',
	   'f_div'  => 'xA5A20x7A10',
	   'f_gro'  => 'xA8A20',
	   'f_eag'  => 'A5A8',
	   'f_tmt'  => 'x6A5x20A40',
	   'f_wind' => 'xA10x16A20A15x20A8x2A1',
		 'a_ind'  => 'xA10x16A20A15x20A8x2A1'
	  );
%verif =  (
	   'f_ele'  => '4802,598',
	   'f_men'  => '546,88',
	   'f_div'  => '322,45',
	   'f_gro'  => '290,38',
	   'f_eag'  => '98,14',
	   'f_tmt'  => '290,77',
	   'f_wind' => '1058,306',
		 'a_ind'  => '706,251'
	  );

# D�finition des formats TXT
# --------------------------
%formatTxt = (
	   'f_ele'  => '6',
	   'f_men'  => '3',
	   'f_div'  => '3',
	   'f_wind' => '5'
	  );

# Variable SambaSID
$lcs_ldap = Net::LDAP->new("$slapdIp");
$lcs_ldap->bind(
  dn       => $adminDn,
  password => $adminPw,
  version  => '3'
);

$res = $lcs_ldap->search(base     => $baseDn,
                         scope    => 'one',
                         filter   => 'objectClass=sambaDomain'
	   );
$domainSid = '';
if (($res->entries)[0]) {
  $domainSid = (($res->entries)[0])->get_value('sambaSID');
}

# Fonction d'�criture de l'ent�te HTML
# ====================================
sub entete {
  $handle = shift(@_);
  print $handle header(-type=>'text/html') if $handle eq 'STDOUT';
  print $handle
   start_html(-title  => 'Importation de donn�es dans l\'annuaire LDAP',
	       -author => 'olivier.le_monnier@tice.ac-caen.fr',
	       -style  => {-src=>"/$webDir/style/style.css"}),
    h1("<a style=\"color: #404044; text-decoration: none\" href=\"/$webDir/result.$pid.html\">Importation</a> de donn�es dans l\'annuaire LDAP"),
    hr();
}

# Fonction d'�criture du pied de page HTML
# ========================================
sub pdp {
  $handle = shift(@_);
  print $handle
    "<hr>\n",
    "<div style=\"font-size: xx-small; text-align: right\">\n",
    "<address><a href=\"mailto:LcsDevTeam\@tice.ac-caen.fr\">&lt; LcsDevTeam\@tice.ac-caen.fr &gt;</a></address>\n",
    "Mis � jour : le 5 juillet 2004",
    "</div>\n",
    end_html();
}

sub dbf2txt {
  my $fichier = shift;
  open ( FICHTMP ,"</tmp/ApacheCgi.temp");
  seek FICHTMP, 4, 0;
  # S�paration ent�te/corps
  read( FICHTMP, $header, 8);
  @param = unpack 'LSS', $header;
  ($nb_enreg, $h_size, $l_enreg) = @param;
  # V�rification des tailles de l'ent�te et d'un enregistrement
  # -----------------------------------------------------------
  if ($verif{$fichier} ne "$h_size".","."$l_enreg") {
    $res = "$h_size,$l_enreg,$verif{$fichier}";
  } else {
    $res = 0;
    # �criture du fichier nettoy�
    # ---------------------------
    open DATA, ">/tmp/$fichier.temp";
    seek FICHTMP, $h_size+1, 0;
    while ( $nb_enreg ) {
      read( FICHTMP, $record, $l_enreg);
      #print "<tt>$format{$fichier}</tt> : $record<br>\n";
      $data = join '|', (unpack $format{$fichier}, $record);
      print DATA encode("utf8", decode("cp850", $data)), "\n";
      $nb_enreg--;
    }
  }
  close DATA;
  close FICHTMP;
  unlink '/tmp/ApacheCgi.temp';
  # Renvoie 0 ou les valeurs compar�es
  # ----------------------------------
  return $res;
}

sub txtVerif {
  my $fichier = shift;
  open ( FICHTMP ,"</tmp/ApacheCgi.temp");
  open DATA, ">/tmp/$fichier.temp";
  $res = 0;
  while (<FICHTMP>) {
    $format = split /\|/, $_;
    $_ = encode("utf8", decode("cp1252", $_));
    if ($format ne $formatTxt{$fichier}) {
      close DATA;
      unlink "/tmp/$fichier.temp";
      $res = 1;
      last;
    }
    print DATA;
  }
  # Renvoie 0 ou 1
  # --------------
  close DATA;
  close FICHTMP;
  unlink '/tmp/ApacheCgi.temp';
  return $res;
}

sub mkUid {

  my ( $prenom, $nom ) = @_;

  # G�n�ration de l'UID suivant la politique
  # d�finie dans le fichier de conf commun

  if (($uidPolicy == 0) || ($uidPolicy == 1)) {
    $uid = $prenom . "." . $nom;
  } elsif (($uidPolicy == 2) || ($uidPolicy == 3)) {
    $prenom =~ /^(\w)/;
    $uid = $1 . $nom;
  } elsif ($uidPolicy == 4) {
    $uid = $nom;
    if (length($nom) > 6) {
      $uid =~ /^(.{7})/;
      $uid = $1;
    }
    $prenom =~ /^(\w)/;
    $uid .= $1;
  }
  if ((($uidPolicy == 1) || ($uidPolicy == 2)) && (length($uid) > 19)) {
    $uid =~ /^(.{19})/;
    $uid = $1;
  } elsif (($uidPolicy == 3) && (length($uid) > 8)) {
    $uid =~ /^(.{8})/;
    $uid = $1;
  }
  return(lc($uid));
}

sub sambaAttrs {

  use Crypt::SmbHash;

  my ( $uidNumber, $gid, $password ) = @_;
  $rid = 2 * $uidNumber + 1000;
  $pgrid = 2 * $gid + 1001;
  ( $lmPassword, $ntPassword ) = ntlmgen $password;
  return ( $rid, $pgrid, $lmPassword, $ntPassword );

}

sub getFirstFreeUid {
		my $FFuidNumber = 1000; # n� � partir duquel la recherche est lanc�e
		my $increment = 1024; # doit etre une puissance de 2
		if (defined(getpwuid($FFuidNumber))) {
				do {
						$FFuidNumber += $increment;
				} while (defined(getpwuid($FFuidNumber)));
				
				$increment = int($increment / 2); 
				$FFuidNumber -= $increment;
				do {
						$increment = int($increment / 2); 
						if (defined(getpwuid($FFuidNumber))) {
								$FFuidNumber += $increment;
						} else {
								$FFuidNumber -= $increment;
						}
				} while $increment > 1;
				# la boucle suivante est normalement ex�cut�e au plus une fois
				while (defined(getpwuid($FFuidNumber))) {
						$FFuidNumber++;
				}
		}
		return $FFuidNumber;
}

sub getFirstFreeGid {
		my $FFgidNumber = 2000; # n� � partir duquel la recherche est lanc�e
		my $increment = 1024; # doit etre une puissance de 2
		if (defined(getgrgid($FFgidNumber))) {
				do {
						$FFgidNumber += $increment;
				} while (defined(getgrgid($FFgidNumber)));
				
				$increment = int($increment / 2); 
				$FFgidNumber -= $increment;
				do {
						$increment = int($increment / 2); 
						if (defined(getgrgid($FFgidNumber))) {
								$FFgidNumber += $increment;
						} else {
								$FFgidNumber -= $increment;
						}
				} while $increment > 1;
				# la boucle suivante est normalement ex�cut�e au plus une fois
				while (defined(getgrgid($FFgidNumber))) {
						$FFgidNumber++;
				}
		}
		
		return $FFgidNumber;
}

sub incrementUidInit {
  while (@data = getpwent()) {
    $increment{$data[0]} = 1;
  }
  return %increment;
}

sub processGepUser {

  my ( $uniqueNumber, $nom, $prenom, $date, $sexe, $password ) = @_;
  if ($password eq 'undef') { $password = $date }
  ( $uid, $cn, $givenName, $pseudo, $sn, $crypt, $gecos ) = gep2posixAccount( $prenom, $nom, $password, $date, $sexe );
  # S'il existe un UID issu du fichier f_uid on le prend
  $uid = $Admin_UID{$uniqueNumber} || $uid;
  # Recherche EMPLOYEENUMBER correspondant dans l'annuaire
  ### Correctif leb 1 Dec. 2006
  $res = $lcs_ldap->search(base     => "$peopleDn",
			   scope    => 'one',
                           filter   => "(employeeNumber=$uniqueNumber)");
  #### Fin Correctif leb 
  warn $res->error if $res->code;
  if (($res->entries)[0] and $uniqueNumber ne 'undef') {
    # S'il existe : actualisation des entr�es CN et SN et employeeNumber
    $uid = (($res->entries)[0])->get_value('uid');
    updateUserCSn();
    $cn = encode('latin1', decode('utf8', $cn));
    return ("<tr><td>Entr�e <strong>$cn :</strong></td><td>compte $uniqueNumber d�j� pr�sent dans l'annuaire : <tt><strong>$uid</strong></tt>.</td></tr>\n");
  } else {
    $id = 1;
  DOUBLONS: while (1) {
      # Recherche d'un uid correspondant dans l'annuaire
      $res = $lcs_ldap->search(base     => "$peopleDn",
															 scope    => 'one',
															 filter   => "uid=$uid");
      warn $res->error if $res->code;
      if (($res->entries)[0]) {
					# S'il existe : v�rification de la pr�sence de l'EMPLOYEENUMBER
					$employeeNumber = (($res->entries)[0])->get_value('employeeNumber');
					if (! $employeeNumber and $uniqueNumber ne 'undef') {
							# En l'absence : Comparaison des CN
							$cnLdap = unac_string('utf8', (($res->entries)[0])->get_value('cn'));
							$cnTemp = unac_string('utf8', $cn);
							if ($cnLdap eq $cnTemp) {
									$ldapUid = (($res->entries)[0])->get_value('uid');
									updateUserEntry();
									$cn = encode('latin1', decode('utf8', $cn));
									return ("<tr><td>Entr�e <strong>$cn :</strong></td><td>Mise � jour du 'num�ro unique', compte <tt><strong>$ldapUid</strong></tt>.</td></tr>\n");
							} else {
									# traitement des d�chets...
									$cn = encode('latin1', decode('utf8', $cn));
									return ("<tr><td>Entr�e <strong>$cn :</strong></td><td>Risque de conflits, entr�e non trait�e</td></tr>\n");
							}
					} else {
							# Sinon Gestion des doublons
							if ($uid =~ /^(.*)${id}$/) {
									$uid = $1 . ++$id;
							} else {
									chop($uid) if ((length($uid)>= 8) && ($uidPolicy == 4));
									$uid .= ++$id;
							}
							next DOUBLONS;
					}
      } else {
	# Cr�ation de l'entr�e
	addUserEntry($uid, $password);
	$cn = encode('latin1', decode('utf8', $cn));
        my $ValRetour =  "<tr><td>Entr�e <strong>$cn :</strong></td><td>Cr�ation du compte <tt><strong>$uid</strong></tt>";
        $ValRetour .= " fourni par f_uid" if $Admin_UID{$uniqueNumber} ;
        $ValRetour .= "</td></tr>\n";
        return ($ValRetour);
	last DOUBLONS;
      }
    }
  }
}

sub updateUserCSn {

  $res = $lcs_ldap->modify( "uid=$uid,$peopleDn",
			    replace => {
				       cn             => $cn,
				       sn             => $sn,
				       employeeNumber => $uniqueNumber
				      } );
  warn $res->error if $res->code;

}

sub updateUserEntry {

  $res = $lcs_ldap->modify( "uid=$uid,$peopleDn",
			    add => {
				    employeeNumber => $uniqueNumber
				   } );
  warn $res->error if $res->code;

}

sub addUserEntry {

  ($uid, $password) = @_;

  my $uidNumber = getFirstFreeUid($uidNumber);

  my ( $rid, $pgrid, $lmPassword, $ntPassword ) = sambaAttrs( $uidNumber, $gid, $password );

  @entry = (
	    'uid',            $uid,
	    'cn',             $cn,
	    'givenName',      $givenName,
	    'sn',             $sn,
            'initials',        $pseudo,
	    'mail',           "$uid\@$domain",
	    'objectClass',    'top',
	    'objectClass',    'posixAccount',
	    'objectClass',    'shadowAccount',
	    'objectClass',    'person',
	    'objectClass',    'inetOrgPerson',
	    'loginShell',     $loginShell,
	    'uidNumber',      $uidNumber,
	    'gidNumber',      $gid,
	    'homeDirectory',  "/home/$uid",
	    'userPassword',   "{crypt}$crypt",
	    'gecos',          $gecos,
	   );
  if ($domainSid ne '') {
    push @entry, (
	      'objectClass',    'sambaSamAccount',
	      'sambaSID',      "$domainSid-$rid",
	      'sambaPrimaryGroupSID', "$domainSid-$pgrid",
	      'sambaLMPassword',     $lmPassword,
	      'sambaNTPassword',     $ntPassword,
	      'sambaPwdMustChange',  '2147483648',
	      'sambaAcctFlags',      '[U          ]'
	   );
  } else {
    $domainSid="S-0-0-00-0000000000-000000000-0000000000";
    push @entry, (
#	      'objectClass',    'sambaAccount',
#	      'rid',            $rid,
#	      'primaryGroupId', $pgrid,
#	      'lmPassword',     $lmPassword,
#	      'ntPassword',     $ntPassword,
#	      'PwdMustChange',  '2147483648',
#	      'AcctFlags',      '[U          ]'
	      'objectClass',    'sambaSamAccount',
	      'sambaSID',      "$domainSid-$rid",
	      'sambaPrimaryGroupSID', "$domainSid-$pgrid",
	      'sambaLMPassword',     $lmPassword,
	      'sambaNTPassword',     $ntPassword,
	      'sambaPwdMustChange',  '2147483648',
	      'sambaAcctFlags',      '[U          ]'
	     );
  }

  push @entry, ('employeeNumber', $uniqueNumber) if $uniqueNumber;

  $res = $lcs_ldap->add( "uid=$uid,$peopleDn",
			 attrs => \@entry );
  warn $res->error if $res->code;

}

sub normalize {
  $toNormalize = shift;
  $howMuch = shift;
  if ($toNormalize =~ /\s/ and length($toNormalize) > $howMuch) {
    my @elements = split / /, $toNormalize;
    $normalized = '';
    foreach $element (@elements) {
      if (length($element) > $howMuch) {
	$element = lc($element);
	$element = ucfirst($element);
      }
      $normalized .= "$element ";
    }
    chop $normalized;
    return $normalized
  } elsif (length($toNormalize)> $howMuch) {
    $normalized = lc($toNormalize);
    $normalized = ucfirst($normalized);
  }
}

sub isAdmin {

  $isAdmin = 'N';
  # Identification de l'utilisateur
  # ===============================
  # R�cup�ration du cookie
  %cookies = fetch CGI::Cookie;
   if (exists $cookies{'LCSAuth'} ) { $session = $cookies{'LCSAuth'}->value; }
  # Connexion MySql
  $lcs_db = DBI->connect('DBI:mysql:lcs_db', $mysqlServerUsername, $mysqlServerPw);
  $requete = $lcs_db->prepare("select idpers from sessions where (sess = '$session')");
  $requete->execute();
  $id = $requete->fetchrow_array;
  $requete = $lcs_db->prepare("select login from personne where (id = $id)");
  $requete->execute();
  $login = $requete->fetchrow_array;
  $requete->finish;
  $lcs_db->disconnect;
  $lcs_ldap = Net::LDAP->new("$ldap_server");
  $lcs_ldap->bind();
#  $res = $lcs_ldap->search(base     => "$baseDn",
#			   scope    => 'sub',
#			   attrs    => 'gecos',
#			   filter   => "(uid=$login)");
#  foreach $entry ($res->entries) {
#    @gecos = $entry->get_value('gecos');
#  }
#  $isAdmin = (split /,/,$gecos[0])[2];
  # Validation
  $admindn = 'uid=' . $login .",". $peopleDn;
  @attrs = ('cn');
  $lcs_ldap = Net::LDAP->new("$ldap_server");
  $lcs_ldap->bind(dn       => "$adminDn",
                  password => "$adminPw");
  $res = $lcs_ldap->search(base     => "cn=Annu_is_admin,$droitsDn",
                           scope    => 'subtree',
                           attrs    => \@attrs,
                           filter   => "(member=$admindn)");
  foreach $entry ($res->entries) {
    @cn  = $entry->get('cn');
  }
  if ($cn[0] eq 'Annu_is_admin') {
     $isAdmin = "Y";
  }
  $lcs_ldap->unbind();
  return $isAdmin;

}

sub annuelle {
  # Pr�paration d'une importation annuelle
  # --------------------------------------
  print RES "<h2>Pr�paration � l'importation annuelle</h2>\n";
  # Connexion LDAP
  $lcs_ldap = Net::LDAP->new("$slapdIp");
  $lcs_ldap->bind(
		  dn       => $adminDn,
		  password => $adminPw,
		  version  => '3'
		 );
  # 1.  Suppression des groupes 'Cours', 'Equipe', 'Classe' et 'Matiere'
  $res = $lcs_ldap->search(base     => "$groupsDn",
			   scope    => 'one',
			   filter   => "(|(cn=Classe_*)(cn=Equipe_*)(cn=Cours_*)(cn=Matiere_*))");
  warn $res->error if $res->code;
  if (($res->entries)[0]) {
    foreach $entry ($res->entries) {
      $cn = $entry->get_value('cn');
      $res = $lcs_ldap->delete("cn=$cn,$groupsDn");
      print RES "Suppression du groupe <tt><strong>$cn</strong></tt>.<br>\n" if $debug > 1;
      warn $res->error if $res->code;
    }
  }
  # 2.  Modification du DN des groupes Eleves, Profs et Administratifs
  $res = $lcs_ldap->search(base     => "$groupsDn",
			   scope    => 'one',
			   filter   => 'cn=Eleves');
  warn $res->error if $res->code;
  $elevesGid = (($res->entries)[0])->get_value('gidNumber');
  $res = $lcs_ldap->moddn( $elevesDn,
			   newrdn => "${elevesRdn}Old" );
  warn $res->error if $res->code;
  $elevesRdn =~ /cn=(.+)$/;
  @elevesEntry = (
		  'cn',          "$1",
		  'objectClass', 'top',
		  'objectClass', 'posixGroup',
		  'gidNumber',   $elevesGid,
		 );
  $res = $lcs_ldap->add( "$elevesDn",
			 attrs => \@elevesEntry );
  warn $res->error if $res->code;
  $res = $lcs_ldap->search(base     => "$groupsDn",
			   scope    => 'one',
			   filter   => 'cn=Profs');
  warn $res->error if $res->code;
  $profsGid = (($res->entries)[0])->get_value('gidNumber');
  $res = $lcs_ldap->moddn( $profsDn,
			   newrdn => "${profsRdn}Old" );
  warn $res->error if $res->code;
  $profsRdn =~ /cn=(.+)$/;
  @profsEntry = (
		 'cn',          "$1",
		 'objectClass', 'top',
		 'objectClass', 'posixGroup',
		 'gidNumber',   $profsGid,
		);
  $res = $lcs_ldap->add( "$profsDn",
			 attrs => \@profsEntry );
  warn $res->error if $res->code;
  $res = $lcs_ldap->search(base     => "$groupsDn",
			   scope    => 'one',
			   filter   => 'cn=Administratifs');
  warn $res->error if $res->code;
  $adminsGid = (($res->entries)[0])->get_value('gidNumber');
  $res = $lcs_ldap->moddn( "cn=Administratifs,$groupsDn",
			   newrdn => 'cn=AdministratifsOld' );
  warn $res->error if $res->code;
  @adminsEntry = (
		  'cn',          'Administratifs',
		  'objectClass', 'top',
		  'objectClass', 'posixGroup',
		  'gidNumber',   $adminsGid,
		 );
  $res = $lcs_ldap->add( "cn=Administratifs,$groupsDn",
                         attrs => \@adminsEntry );
  warn $res->error if $res->code; 
  # 3.  Recopie des member de ElevesOld, ProfsOld et AdministratifsOld dont
  #     l'employeeNumber est vide vers les branches renouvell�es
  $res = $lcs_ldap->search(base     => "$peopleDn",
			   scope    => 'one',
			   filter   => "(!(employeeNumber=*))");
  foreach $entry ($res->entries) {
    $uid = $entry->get_value('uid');
    $res2 = $lcs_ldap->search(base     => "${elevesRdn}Old,$groupsDn",
			      scope    => 'base',
			      filter   => "memberUid=$uid");
    if (($res2->entries)[0]) {
      $res2 = $lcs_ldap->modify( $elevesDn,
				 add => { 'memberUid' => $uid } );
      warn $res2->error if $res->code;
      next;
    } else {
      $res2 = $lcs_ldap->search(base     => "${profsRdn}Old,$groupsDn",
			        scope    => 'base',
 			        filter   => "memberUid=$uid");
      if (($res2->entries)[0]) {
        $res2 = $lcs_ldap->modify( $profsDn,
				   add => { 'memberUid' => $uid } );
        warn $res2->error if $res->code;
	next;
      } else {
        $res2 = $lcs_ldap->search(base     => "cn=AdministratifsOld,$groupsDn",
			          scope    => 'base',
			          filter   => "memberUid=$uid");
        if (($res2->entries)[0]) {
          $res2 = $lcs_ldap->modify( "cn=Administratifs,$groupsDn",
				     add => { 'memberUid' => $uid } );
          warn $res2->error if $res->code;
	}
      }
    }
  }
  # 4.  Suppression des branches OLD
  $res = $lcs_ldap->delete("${profsRdn}Old,$groupsDn");
  $res = $lcs_ldap->delete("${elevesRdn}Old,$groupsDn");
  $res = $lcs_ldap->delete("cn=AdministratifsOld,$groupsDn");
}
sub gep2posixAccount {
  
  my ( $prenom, $nom, $password, $date, $sexe ) = @_;

  @noms = ();
  $sn = '';

  # � Minusculisation � et nettoyage du nom et du pr�nom
  # $nom =~ tr/A-Z/a-z/;
  $nom = lc($nom);
  $nom =~ s/'//; #';
  if ($nom =~ /\s/) {
    @noms = (split / /,$nom);
    $nom1 = $noms[0];
    if (length($noms[0]) < 4) {
      $nom1 .= "_" . $noms[1];
      $separator = ' ';
    } else {
      $separator = '-';
    }
    foreach $nom_partiel (@noms) {
      $sn .= ucfirst($nom_partiel) . $separator;
    }
    chop $sn;
  } else {
    $nom1 = $nom;
    $sn = ucfirst($nom);
  }
  $nom =~ /^(\w)(.*)/;
  $firstletter_nom = $1;
  $firstletter_nom = uc($firstletter_nom);

  $prenom =~ /^(\S*)/;
  $prenom1 = $1;
  $prenom1 = lc($prenom1);
  $prenom1 =~ s/'//; #';
  $prenom1 =~ s/\.//; #';

  $uid = mkUid(unac_string('utf8', ($prenom1)), unac_string('utf8', ($nom1)));

  # G�n�ration du mot de passe crypt�
  $salt  = chr (rand(75) + 48);
  $salt .= chr (rand(75) + 48);
  $crypt = crypt $password, $salt;

  # G�n�ration de cn, givenName et sn
  $sn = unac_string('utf8',$sn);
  $prenom = unac_string('utf8',$prenom1);
  $Uprenom = ucfirst($prenom);
  $cn = "$Uprenom $sn";
  $givenName = $Uprenom;
  $pseudo = $prenom . $firstletter_nom;

  # G�n�ration du gecos
  if ($sexe eq '1') { $sexe = 'M' }
  if ($sexe eq '2') { $sexe = 'F' }

  $gecos = "$cn,$date,$sexe,N";

  @data = ( $uid, $cn, $givenName, $pseudo, $sn, $crypt, $gecos );
  return @data;
}

return 1;
