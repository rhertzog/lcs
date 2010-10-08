#!/usr/bin/perl

use strict;
use File::Basename;
use File::Copy;


#############################################################
# Script de mise � jour de BCDIWEB  sur un serveur Linux    #
# Alexis Abram� - CRDP de Poitou-Charentes - novembre 2007  #
#############################################################

#A lancer en tant que SuperUtilisateur (su ou root)
my $sourcearchori=`pwd`;
chomp $sourcearchori;
my $version_cgi="Bcdi Web v. 2.0";
my $archivebcdiweb="BcdiWebLinux.tar.gz";
my $emplacement_installation_cgi="/usr/bcdiserv/progweb";
my $chemin_apacheconf="/etc/httpd/conf/httpd.conf";
my $user_web;
my $group_web;
my $group_bcdi;
my $alias_bcdi;
my $fichier_mdl="bcdiweb_init.mdl";

#structure pour stocker les infos trouv�es dans les fichiers ini existants 
my $rbases=[
	     {
		 'nom'=>"",
	     }
	    ];
#structure pour stocker les questions pos�es au lancement du script
my $rconfig=[
	     {
		 'message'=>"Emplacement du fichier $archivebcdiweb :",
		 'chemin'=>\$sourcearchori,
		 'creation'=>0,
	     },
	     {
		 'message'=>"Emplacement d'installation du cgi :",
		 'chemin'=>\$emplacement_installation_cgi,
		 'creation'=>1,
	     },
	     {
		 'message'=>"Nom et emplacement du fichier de conf d'Apache :",
		 'chemin'=>\$chemin_apacheconf,
		 'creation'=>0,
	     },
	     ];
#hachage des param�tres d'une base pour lesquels une question sera pos�e

my %conseil_ini=(
		 "nom"=>"Nom de cette base.\nCe mot qui servira � l'appel d'une base dans l'URL sera compos� de caract�res non accentu�s uniquement sans espace.",
		 "DONNEES"=>"\nChemin UNIX d'acc�s � cette base.",
		 "ELECTRE"=>"\nActivation de l'acc�s aux objets Electre (couvertures, sommaires, biographies...).\nAttention : l'abonnement � MemoElectre ou MemoElectre plus est n�cessaire.",
		 "ORGANISME"=>"\nNom de l'�tablissement abonn� � Bcdi tel qu'indiqu� sur la licence.",
		 "CODE"=>"\nCode d'acc�s de l'abonn� Bcdi tel qu'indiqu� sur la licence.",
		 "BASEBIBLIO"=>"\nBase bibliographique (base sans exemplaires).",
		 "COMPTE"=>"\nAffichage de l'onglet Compte dans les �crans de recherche.",
		 "RESERVATIONS"=>"\nPossibilit� d'effectuer les r�servations � distance.",
		 "NOM_BASE"=>"\nIntitul� de votre base dans les �crans de recherche.",
		 "MARGUERITE"=>"\nAffichage de la marguerite (codage en couleur de la cote Dewey) dans les r�sultats de recherche.",
		 "MODELES"=>"\nChoix du r�pertoire des mod�les. Choix autoris�s :\n1 : mod�les pour le coll�ge,\n2 : mod�les pour le lyc�e,\n3 : mod�les pour utilisateurs adultes.",
		 #"EMAIL"=>"\nAdresse mail du responsable du site.",#non g�r�
		 #"AFFDICO"=>"\nActivation de la recherche avec Dico (recherche plein-texte am�lior�e).",#non g�r�
		 );

my $option_script=shift;
if ($option_script eq "-f"){
    &menu;
}
else{
    &verif_root;
}

sub verif_root{
##############################
# Fonction pour tester l'UID #
##############################
my $whoami=`whoami`;
chomp $whoami;
    if ($whoami ne "root"){
	print "Vous �tes $whoami.\nCette installation doit �tre lanc�e par l'utilisateur 'root'.\nVous pouvez forcer l'installation sans v�rification de l'utilisateur avec l'option -f\n";
	exit 1; 
    }
    else {	
	&menu;
	}
}

sub menu{
###########################
# Fonction menu d'accueil #
###########################
    system("clear");
    my $menu=<<"FIN_MENU";
                   *************************************************
                   *      Param�trage d\'une base de donn�es       *
                   *        pour BCDIWEB v. 2.0 sous Linux         *
                   *************************************************

  


    1. Param�trage d\'une base
    2. Quitter



Probl�mes d\'affichage ?
Ce programme utilise le jeu de caract�res ISO-8859-15. R�glez votre 
console en cons�quence.
La plupart des questions pos�es sont accompagn�es d\'une valeur par
d�faut (chemin d\'installation par ex.) ou des valeurs possibles de 
la r�ponse (O/n pour oui ou non par ex.). Dans ce dernier cas la
valeur par d�faut est en majuscules.
Pour s�lectionner la valeur par d�faut propos�e dans la question, 
appuyez simplement sur Entr�e. 

FIN_MENU

print $menu;
    my %reponse_menu=(
		      '1'=> \&creation_ini,
                      '2'=> sub {print "Bye !\n";exit 0;},
		      );

  QUESTION:
    print "Votre choix : ";
    my $reponse_utilisateur=<>;
    chomp $reponse_utilisateur;
    if (exists $reponse_menu{$reponse_utilisateur}){
	$reponse_menu{$reponse_utilisateur}->();
    }
    else{
	print "R�ponse incorrecte !\n";
	goto QUESTION;
    }
}

sub creation_ini{
############################################################
# Fonction d'installation du cgi avec r�cup�ration         #
# du param�trage d'une ancien cgi Bcdi                     #
############################################################
    &chemin_installation("1");
    &parametrage_nouvelle_base;
    &reglage_droits("$emplacement_installation_cgi");
    &info_web();
}

sub chemin_installation{
    system("clear");
     print << "DEBUT_PARAM";
#####################################################
# D�claration des param�tres d'installation du cgi  #
#####################################################

DEBUT_PARAM
    my $fin_option=shift;
    my $debut_option=1;
    
    for ($debut_option..$fin_option){
EMPLACEMENT_INSTALLATION:
	print "$rconfig->[$_]->{message}\n(par d�faut : ${$rconfig->[$_]->{chemin}})\n";
	my $chemin_reponse=<>;
	chomp $chemin_reponse;
	if ($chemin_reponse){
	    ${$rconfig->[$_]->{chemin}}=$chemin_reponse;
	}
	my $testemplacement=stat (${$rconfig->[$_]->{chemin}});
	if (! $testemplacement){
	    if ($rconfig->[$_]->{creation}){
	      #QUESTION_CREATION_EMPLACEMENT:
		print "Le r�pertoire n'existe pas. Voulez-vous le cr�er ? (o/n)\n";
		my $reponse_utilisateur="";
		while ( ! $reponse_utilisateur ){
		    $reponse_utilisateur=<>;
		    chomp $reponse_utilisateur;
		    my %reponse_configuration=(
					       'o'=> sub {
						   my $chemin=shift;
						   my $creation_chemin=mkdir $chemin,0770;
						   if ($creation_chemin){
						       print "L'emplacement $chemin a �t� cr�� !\n";
						   }
						   else {
						       print "La cr�ation de l'emplacement $chemin a �chou� !\n($!)\n";
						       goto EMPLACEMENT_INSTALLATION;
						   }
					       },
					       'n'=> sub { goto EMPLACEMENT_INSTALLATION;
						       },
					       );
		    if (exists $reponse_configuration{$reponse_utilisateur}){
			$reponse_configuration{$reponse_utilisateur}->(${$rconfig->[$_]->{chemin}});
		    }
		    else{
			print "R�ponse incorrecte !\n";
			$reponse_utilisateur="";
		    }
		}
	    }
	    else{
		print "L'emplacement indiqu� n'est pas correct !\n\n";
		goto EMPLACEMENT_INSTALLATION;
	    }
	}
	print "\n";
    }
#r�cup�ration du groupe et de l'utilisateur web :
    my @stat_progweb=stat (${$rconfig->[1]->{chemin}});
    $user_web=$stat_progweb[4];
    $group_web=$stat_progweb[5];
#test de compatibilit� :$emplacement_installation_cgi
    if ( -e "$emplacement_installation_cgi/bcdi3web.cgi"){
	print "Le r�pertoire $emplacement_installation_cgi semble contenir le cgi Bcdi 3 Web :\nMise � jour impossible.\nVeuillez proc�der � une installation compl�te de $version_cgi.\n";
	exit 0;
    }
}

sub parametrage_nouvelle_base{
    system('clear');
     print << "DEBUT_PARAM";
#####################################################
# Param�trage d'une nouvelle base                   #
#####################################################

DEBUT_PARAM
my %repertoire_modeles=(
			"1"=>"$emplacement_installation_cgi/modcol",
			"2"=>"$emplacement_installation_cgi/modlyc",
			"3"=>"$emplacement_installation_cgi/modspe",
			);

#Question Nom de la base : pour cr�er le fichier d'ini 
######################################################
    print "$conseil_ini{nom}\n";
    my $nom_base;
    print "Votre choix :\n";
    $nom_base=<>;
    chomp($nom_base);
    while (! verif_nom_base($nom_base)){
	print "R�ponse incorrecte !\nVotre choix :\n";
	$nom_base=<>;
	chomp ($nom_base);
    }
    while (! verif_absence_fichier_ini($nom_base)){
	print "Le fichier ". "bcdiweb_"."$nom_base".".ini "."existe d�j� !\nAutre choix :\n";
	$nom_base=<>;
	chomp ($nom_base);
    }
    my $fichier_ini="bcdiweb_"."$nom_base".".ini";
    open (INI,">$emplacement_installation_cgi/$fichier_ini") or die "Impossible de cr�er $fichier_ini : $!\n";
    open (MDL,"<$emplacement_installation_cgi/$fichier_mdl") or die "Impossible d'ouvrir le fichier $fichier_mdl : $!\n";
    my $repertoire_modeles="";
  while (my $ligne=<MDL>){
        if ($ligne=~m/=/){
	    chomp ($ligne);
	  my $sauve_ligne=$ligne;
	  $ligne=~s/^;//;
	  my @param=split /=/, $ligne;
# Les questions : affichage conditionnel des consignes : 
########################################################
	  if (exists ($conseil_ini{$param[0]})){
	      print "$conseil_ini{$param[0]}\n";
	      my $reponse;
	      my $reponse_defaut;
	      print "Votre choix :";
	      if ($param[0] eq "MARGUERITE" || $param[0] eq "ELECTRE" || $param[0] eq "COMPTE" || $param[0] eq "RESERVATIONS" || $param[0] eq "AFFDICO" ){
		  print " (O/n)";
		  $reponse_defaut="o";
	      }
	      if ( $param[0] eq "BASEBIBLIO" ){
		  print " (o/N)";
		  $reponse_defaut="n";
	      }
	      print "\n";
#Les r�ponses : analyse et �criture :
#####################################
	      $reponse=<>;
	      chomp ($reponse);
	      if (! $reponse){
		  if ( $reponse_defaut ){
		      $reponse=$reponse_defaut;
		  }
	      }
	      if ($param[0] eq "DONNEES" ){
		  while (! &verif_chemin_base($reponse)){
		      print "Chemin incorrect !\nVotre choix :\n";
		      $reponse=<>;
		      chomp ($reponse);
		  }
		  if (&ajouter_droits($reponse)){
		      print "Ajout des droits d'�criture pour le groupe sur le r�pertoire $reponse\n";
		  }
		  else{
		      print "Impossible d'ajouter les droits d'�criture pour le groupe sur le r�pertoire $ligne\n";
		  }
		  my @stat_donnees=stat ($reponse);
		  $group_bcdi=$stat_donnees[5];
		  $ligne=&unix2dos("$param[0]=$reponse\n");
		  print INI "$ligne";
		  next;
	      }
	      elsif ($param[0] eq "MODELES" ){
		  if (exists ($repertoire_modeles{$reponse})){
		      $reponse=$repertoire_modeles{$reponse};
		  }
		  while (! &verif_chemin_modeles($reponse)){
		      print "Choix incorrect !\nVotre choix :\n";
		      $reponse=<>;
		      chomp ($reponse);
		      if (exists ($repertoire_modeles{$reponse})){
			  $reponse=$repertoire_modeles{$reponse};
		      }
		      else{
			  $reponse="";
		      }
		  }
		  $ligne=&unix2dos("$param[0]=$reponse\n");
		  $repertoire_modeles=$reponse;
		  print INI "$ligne";
		  next;
	      }
	      elsif ( $param[0] eq "MARGUERITE" || $param[0] eq "BASEBIBLIO" || $param[0] eq "COMPTE" || $param[0] eq "RESERVATIONS" || $param[0] eq "AFFDICO" ){
		  
		  while (! &verif_ouinon($reponse)){
		      print "R�ponse incorrecte (o/n) !\nVotre choix :\n";
		      $reponse=<>;
		      chomp ($reponse);
		  }
		  if ($reponse eq "o"  || $reponse eq "O"){
		      $reponse="OUI";
		  }
		  else{
		      $reponse="NON";
		  }
		  $ligne=&unix2dos("$param[0]=$reponse\n");
		  print INI "$ligne";
		  next;

	      }
	      elsif ( $param[0] eq "ELECTRE" ){
		  while (! &verif_commentaire($reponse)){
		      print "R�ponse incorrecte (o/n) !\nVotre choix :\n";
		      $reponse=<>;
		      chomp ($reponse);
		  }
		  if ( $reponse eq "n" || $reponse eq "N" ){
		     $ligne=";".$sauve_ligne;
		     $ligne=&unix2dos("$ligne\n");
		     print INI "$ligne";
		     next;
		  }
		  else{
		      $param[1]="$emplacement_installation_cgi"."/electre";
		      if ( ! -e $param[1] ){
			  mkdir ($param[1],0770) or die "Impossible de cr�er $param[1] : $:!\n";
		      }
		      $ligne="$param[0]"."="."$param[1]";
		      $ligne=&unix2dos("$ligne\n");
		      print INI "$ligne";
		      next;
		  }
	      }
	      elsif ( $param[0] eq "EMAIL" ){
		  while (! &verif_mail($reponse)){
		      print "R�ponse incorrecte !\nVotre choix :\n";
		      $reponse=<>;
		      chomp ($reponse);
		  }
		  
		  $ligne=&unix2dos("$param[0]=$reponse\n");
		  print INI "$ligne";
		  next;

	      }
	      elsif ( $param[0] eq "CODE" ){
		  while (! &verif_code($reponse)){
		      print "R�ponse incorrecte (8 lettres majuscules) !\nVotre choix :\n";
		      $reponse=<>;
		      chomp ($reponse);
		  }
		  
		  $ligne=&unix2dos("$param[0]=$reponse\n");
		  print INI "$ligne";
		  next;

	      }
	      else{
		  while (! $reponse){
		      print "R�ponse vide !\nVotre choix :\n";
		      $reponse=<>;
		      chomp ($reponse);
		  }
		  $ligne=&unix2dos("$param[0]=$reponse\n");
		  print INI "$ligne";
		  next;
	      }
	  }
# Il n'existe pas de conseil concernant ces options : pas de question pos�e 
###########################################################################
	  else{
	      if ($param[0] eq "TRAVAIL"){
		  if ($param[1]){
		      if ( ! -e $param[1] ){
			  mkdir ($param[1],0770) or die "Impossible de cr�er $param[1] : $:!\n";
		      }
		  }
		  else{
		      my $copie_nom_base=lc($nom_base);
		      $param[1]="$emplacement_installation_cgi/trav"."$copie_nom_base";
		      if ( ! -e $param[1] ){
			  mkdir ($param[1],0770) or die "Impossible de cr�er $param[1] : $:!\n";
		      }
		  }
		  $ligne=&unix2dos("$param[0]=$param[1]\n");
		  print INI "$ligne";
		  next;
	      }
	      elsif ( $param[0] eq "REP_MODELES" ){
		  if ($repertoire_modeles){
		      $repertoire_modeles=basename($repertoire_modeles);
		  }		  
		  $ligne=&unix2dos("$param[0]=../$repertoire_modeles/\n");
		  print INI "$ligne";
		  next;
	      }
	      elsif ($param[0] eq "CACHE"){
		  if ($param[1]){
		      if ($param[1]=~m/^oui$/i){
			  $ligne=&unix2dos("$param[0]=$param[1]\n");
			  print INI "$ligne";
			  next;
		      }
		      elsif ( ! -e $param[1] ){
			  mkdir ($param[1],0770) or die "Impossible de cr�er $param[1] : $:!\n";
		      }
		  }
		  else{
		      my $copie_nom_base=lc($nom_base);
		      $param[1]="$emplacement_installation_cgi"."/cache";
		    #  $param[1]="$emplacement_installation_cgi/trav"."$copie_nom_base"."/cache";
		      if ( ! -e $param[1] ){
			  mkdir ($param[1],0770) or die "Impossible de cr�er $param[1] : $:!\n";
		      }
		  }
		  $ligne=&unix2dos("$param[0]=$param[1]\n");
		  print INI "$ligne";
		  next;
	      }
	      else{
		  $sauve_ligne=&unix2dos("$sauve_ligne\n");
		  print INI "$sauve_ligne";
		  next;
	      }
	  }
      }
	else{
	    $ligne=&unix2dos("$ligne");
	    print INI "$ligne";
	    next;
	}
  }
    close MDL;
    close INI;
    print "La base $nom_base a �t� param�tr�e avec succ�s !\n\n";
&question_nouvelle_base ();
}

sub question_nouvelle_base {
print "\nVoulez-vous effectuer le param�trage d'une nouvelle base ? (o/n)\n";
    my $reponse_utilisateur="";
    while (! $reponse_utilisateur ){
	$reponse_utilisateur=<>;
	chomp $reponse_utilisateur;
	my %reponse_parametres=(
				'o'=> \&parametrage_nouvelle_base,
				'n'=> sub {system("clear");},
				
				);
	if (exists $reponse_parametres{$reponse_utilisateur}){
	    $reponse_parametres{$reponse_utilisateur}->();
	}
	else{
	    print "R�ponse incorrecte !\n";
	    $reponse_utilisateur="";
	}
    }


}
sub verif_nom_base{
    my $nom_base=shift;
    chomp ($nom_base);
#si le nom est vide :
    if (! $nom_base){
	return 0;
    }
#si le nom contient autre chose qu'un caract�re alphanum�rique ou un _ :
    elsif ($nom_base=~m/[\W_]/){
	return 0;
    }
    else{
	return 1;
    }
}
sub verif_chemin_base {
    my $chemin=shift;
    if(stat("$chemin/NOTICES.DAT")){
	return 1;
    }
    else{
	return 0;
    }
}
sub verif_chemin_modeles {
    my $chemin=shift;
    if(stat("$chemin")){
	return 1;
    }
    else{
	return 0;
    }
}

sub verif_ouinon {
    my $reponse=shift;
    if($reponse=~m/^[oOnN]{1}$/){
	return 1;
    }
    else{
	return 0;
    }
}

sub verif_commentaire {
    my $reponse=shift;
    if($reponse=~m/^[oOnN]{1}$/){
	return 1;
    }
    else{
	return 0;
    }
}

sub verif_code {
    my $reponse=shift;
    if($reponse=~m/^[A-Z]{8}$/){
	return 1;
    }
    else{
	return 0;
    }
}
sub dos2unix {
    my $ligne=shift;
    $ligne=~s/\r\n/\n/g;
    return $ligne;
}
sub unix2dos {
    my $ligne=shift;
    $ligne=~s/\n/\r\n/g;
    return $ligne;
}
sub verif_absence_fichier_ini{
    my $base=shift;
    my $fichier_ini="bcdiweb_"."$base".".ini";
    if (-e "$emplacement_installation_cgi/$fichier_ini"){
	return 0;
    }
    else{
	return 1;
    }
}
sub ajouter_droits{
    my $repertoire=shift;
    my $test_droits=system ("chmod -R g+rwx $repertoire");
    if ($test_droits){
	print "Probl�me pendant le changement des droits sur le r�pertoire $repertoire :\npour permettre � Bcdi Web d'acc�der � la base, vous devrez donner les droits de lecture et d'�criture pour le groupe sur ce r�pertoire et son contenu.\n";
	return 0;
    }else{
	return 1;
    } 

}
sub verif_mail {
    my $mail=shift;
    if ( $mail=~m/@/){
	return 1;
    }
    else{
	return 0;
    }
}
sub reglage_droits {
    my $chemin=shift;
    print << "DEBUT_REGLAGES_DROITS";
########################
# R�glage des droits   #
########################

DEBUT_REGLAGES_DROITS

    my $test_droits=system ("chown -R $user_web.$group_web $chemin >/dev/null 2>&1");
    if ($test_droits){
	print "Probl�me pendant le r�glage des droits sur le r�pertoire $chemin (code de retour Unix : $test_droits) : $!\n";
	exit 0;
    }
    else{
	print "R�glage des droits sur $chemin : Ok !\n\n";
    }
}
sub info_web {
    my $option_info=shift;
    chomp ($option_info);
    my $nom_base="";
    $alias_bcdi=~s/\///g;
    opendir(PROGWEB,$emplacement_installation_cgi) or die "Impossible d'ouvrir le r�pertoire progweb : $!\n";
    my $compteur_base=0;
    my @base=();
    while (defined( my $nom_fichier = readdir(PROGWEB))){
	if ($nom_fichier=~m/\.ini$/){
	    my $nom_base=$nom_fichier;
	    $nom_base=~s/^bcdiweb_//i;
	    $nom_base=~s/\.ini$//i;
	    $base[$compteur_base]="$nom_base";
	    $compteur_base++;
	}
	else{
	    next;
	}
    }
    close (PROGWEB);
    if (! $option_info ){
	
	print << "FIN_INFO";
	
############################################
# Infos sur l\'utilisation du cgi           #
############################################
	
Installation termin�e !
Pour interroger une de vos bases, dans la barre d\'url
d\'un navigateur, tapez :

FIN_INFO

	}
    elsif ( $option_info == "1" ){
	print << "FIN_INFO";
	
############################################
# Infos sur l\'utilisation du cgi           #
############################################
	
Installation termin�e !

Vous devez � pr�sent attribuer le r�pertoire ci-dessous
� l\'utilisateur web et � son groupe. Par exemple :
chown -R apache.apache $emplacement_installation_cgi

Vous devez aussi ajouter l\'utilisateur web au groupe du propri�taire
des fichiers de vos bases (gestbcdi, normalement). Pour cela, �ditez
le fichier /etc/group et ajouter � la fin de la ligne concernant le groupe
gestbcdi, l\'utilisateur web. La ligne ci-dessous, par exemple, ajoute l\'utilisateur
apache au groupe gestbcdi :
gestbcdi:x:501:apache

Puis vous devrez d�clarer le cgi dans le fichier de configuration
d\'Apache, en ajoutant � la fin de ce fichier (souvent httpd.conf)
la ligne :
include $emplacement_installation_cgi/confhttp/

Enfin, vous devrez relancer Apache (ou forcer la relecture du fichier
de conf).

Pour interroger une de vos bases, dans la barre d\'url
d\'un navigateur, tapez :

FIN_INFO

} 
    elsif ( $option_info == "2" ){
	print << "FIN_INFO";
	
############################################
# Infos sur l\'utilisation du cgi           #
############################################
	
Installation termin�e !
Le fichier de conf d\'Apache a �t� sauvegard� sous le nom :
$emplacement_installation_cgi/sauvehttpd.conf

Pour interroger une de vos bases, dans la barre d\'url
d\'un navigateur, tapez :

FIN_INFO

}
    else {
	die "Option incorrecte : $option_info !\n";
    }
    foreach my $base (@base){
	print "http://adresse_serveur/$alias_bcdi/bcdiweb.cgi/$base\n";
    }
}    
