<?php

/* $Id$ */
/* Dernière modification : 16/06/2007 */

//================================================
// Fonction de génération de mot de passe récupérée sur TotallyPHP
// Aucune mention de licence pour ce script...

/*
 * The letter l (lowercase L) and the number 1
 * have been removed, as they can be mistaken
 * for each other.
*/

function createRandomPassword($nb_chars) {
	$chars = "abcdefghijkmnopqrstuvwxyz023456789";
	srand((double)microtime()*1000000);
	$i = 0;
	$pass = '' ;

	//while ($i <= 7) {
	//while ($i <= 5) {
	while ($i <= $nb_chars) {
		$num = rand() % 33;
		$tmp = substr($chars, $num, 1);
		$pass = $pass . $tmp;
		$i++;
	}

	return $pass;
}
//================================================

function date_et_heure() {
	$instant = getdate();
	$annee = $instant['year'];
	$mois = sprintf("%02d",$instant['mon']);
	$jour = sprintf("%02d",$instant['mday']);
	$heure = sprintf("%02d",$instant['hours']);
	$minute = sprintf("%02d",$instant['minutes']);
	$seconde = sprintf("%02d",$instant['seconds']);

	$retour="$jour/$mois/$annee $heure:$minute:$seconde";

	return $retour;
}

function lireSSMTP() {
	$chemin_ssmtp_conf="/etc/ssmtp/ssmtp.conf";

	$tabssmtp=array();

	$fich=fopen($chemin_ssmtp_conf,"r");
	if(!$fich){
		return false;
	}
	else{
		while(!feof($fich)){
			$ligne=fgets($fich,4096);
			if(strstr($ligne,"root=")){
				unset($tabtmp);
				$tabtmp=explode('=',$ligne);
				$tabssmtp["root"]=trim($tabtmp[1]);
			}
			elseif(strstr($ligne,"mailhub=")){
				unset($tabtmp);
				$tabtmp=explode('=',$ligne);
				$tabssmtp["mailhub"]=trim($tabtmp[1]);
			}
			elseif(strstr($ligne,"rewriteDomain=")){
				unset($tabtmp);
				$tabtmp=explode('=',$ligne);
				$tabssmtp["rewriteDomain"]=trim($tabtmp[1]);
			}
		}
		fclose($fich);

		return $tabssmtp;
	}
}

function my_echo($texte){
	global $echo_file, $dest_mode;

	$destination=$dest_mode;

	if((!file_exists($echo_file))||($echo_file=="")){
		$destination="";
	}

	switch($destination){
		case "file":
			$fich=fopen($echo_file,"a+");
			fwrite($fich,"$texte");
			fclose($fich);
			break;
		default:
			echo "$texte";
			break;
	}
}

function remplace_accents($chaine){
	//$retour=strtr(ereg_replace("¼","OE",ereg_replace("½","oe",$chaine)),"ÀÄÂÉÈÊËÎÏÔÖÙÛÜÇçàäâéèêëîïôöùûü","AAAEEEEIIOOUUUCcaaaeeeeiioouuu");
	$retour=strtr(ereg_replace("Æ","AE",ereg_replace("æ","ae",ereg_replace("¼","OE",ereg_replace("½","oe","$chaine"))))," 'ÂÄÀÁÃÄÅÇÊËÈÉÎÏÌÍÑÔÖÒÓÕ¦ÛÜÙÚİ¾´áàâäãåçéèêëîïìíñôöğòóõ¨ûüùúıÿ¸","__AAAAAAACEEEEIIIINOOOOOSUUUUYYZaaaaaaceeeeiiiinoooooosuuuuyyz");
	return $retour;
}

function get_infos_admin_ldap(){
	//global $dn;
	global $ldap_base_dn;

	$adminLdap=array();

	// Etablir la connexion au serveur et la sélection de la base?

	$sql="SELECT value FROM params WHERE name='adminRdn'";
	$res1=mysql_query($sql);
	if(mysql_num_rows($res1)==1){
		$lig_tmp=mysql_fetch_object($res1);
		$adminLdap["adminDn"]=$lig_tmp->value.",".$ldap_base_dn;
	}

	$sql="SELECT value FROM params WHERE name='adminPw'";
	$res2=mysql_query($sql);
	if(mysql_num_rows($res2)==1){
		$lig_tmp=mysql_fetch_object($res2);
		$adminLdap["adminPw"]=$lig_tmp->value;
	}

	return $adminLdap;
}



function test_creation_trash(){
	global $ldap_server, $ldap_port, $dn, $ldap_base_dn;
	global $error;
	$error="";

	// Paramètres
	// Aucun

	// Tableau retourné
	$tab=array();

	fich_debug("======================\n");
	fich_debug("test_creation_trash:\n");

	$ds=@ldap_connect($ldap_server,$ldap_port);
	if($ds){
		$r=@ldap_bind($ds);// Bind anonyme
		if($r){
			$attribut=array("ou","objectClass");

			// A REVOIR... LE TEST MERDOUILLE... IL A L'AIR DE RETOURNER vrai même si ou=Trash n'existe pas

			$result=ldap_search($ds,$ldap_base_dn,"ou=Trash",$attribut);
			fich_debug("ldap_search($ds,\"$ldap_base_dn\",\"ou=Trash\",$attribut)\n");
			//echo "<p>ldap_search($ds,$dn[$branche],\"$filtre\",$attribut);</p>";
			if($result){
				fich_debug("La branche Trash existe.\n");
				@ldap_free_result($result);
			}
			else{
				fich_debug("La branche Trash n'existe pas.\n");

				// On va la créér.
				unset($attributs);
				$attributs=array();
				$attributs["ou"]="Trash";
				$attributs["objectClass"]="organizationalUnit";

				//$r=@ldap_bind($ds);// Bind anonyme
				$adminLdap=get_infos_admin_ldap();
				$r=@ldap_bind($ds,$adminLdap["adminDn"],$adminLdap["adminPw"]); // Bind admin LDAP
				if($r){
					$dn_entree="ou=Trash,".$ldap_base_dn;
					fich_debug("Création de la branche: ");
					$result=ldap_add($ds,"$dn_entree",$attributs);
					if(!$result){
						$error="Echec d'ajout de l'entree ou=Trash";
						fich_debug("ECHEC\n");
						fich_debug("\$error=$error\n");
					}
					else{
						fich_debug("SUCCES\n");
					}
					@ldap_free_result($result);
				}
				else{
					$error=gettext("Echec du bind admin LDAP");
					fich_debug("\$error=$error\n");
				}
			}
		}
		else{
			$error=gettext("Echec du bind anonyme");
			fich_debug("\$error=$error\n");
		}
		@ldap_close($ds);
	}
	else{
		$error=gettext("Erreur de connection au serveur LDAP");
		fich_debug("\$error=$error\n");
	}

	if($error!=""){
		echo "error=$error<br />\n";
	}
}



function add_entry ($entree, $branche, $attributs){
	global $ldap_server, $ldap_port, $dn;
	global $error;
	$error="";

	// Paramètres:
	/*
		$entree: uid=toto
		$branche: people, groups,... ou rights
		$attributs: tableau associatif des attributs
	*/

	$ds=@ldap_connect($ldap_server,$ldap_port);
	if($ds){
		//$r=@ldap_bind($ds);// Bind anonyme
		$adminLdap=get_infos_admin_ldap();
		$r=@ldap_bind($ds,$adminLdap["adminDn"],$adminLdap["adminPw"]); // Bind admin LDAP
		if($r){
			$dn_entree="$entree,".$dn["$branche"];
			$result=ldap_add($ds,"$dn_entree",$attributs);
			if(!$result){
				$error="Echec d'ajout de l'entree $entree";
			}
			@ldap_free_result($result);
		}
		else{
			$error=gettext("Echec du bind admin LDAP");
		}
		@ldap_close($ds);
	}
	else{
		$error=gettext("Erreur de connection au serveur LDAP");
	}

	if($error==""){
		return true;
	}
	else{
		//echo "<p>$error</p>";
		return false;
	}
}


function del_entry ($entree, $branche){
	global $ldap_server, $ldap_port, $dn;
	global $error;
	$error="";

	// Paramètres:
	/*
		$entree: uid=toto
		$branche: people, groups,... ou rights
	*/

	$ds=@ldap_connect($ldap_server,$ldap_port);
	if($ds){
		//$r=@ldap_bind($ds);// Bind anonyme
		$adminLdap=get_infos_admin_ldap();
		$r=@ldap_bind($ds,$adminLdap["adminDn"],$adminLdap["adminPw"]); // Bind admin LDAP
		if($r){
			$result=ldap_delete($ds,"$entree,".$dn["$branche"]);
			if(!$result){
				$error="Echec de la suppression de l'entree $entree";
			}
			@ldap_free_result($result);
		}
		else{
			$error=gettext("Echec du bind admin LDAP");
		}
		@ldap_close($ds);
	}
	else{
		$error=gettext("Erreur de connection au serveur LDAP");
	}

	if($error==""){
		return true;
	}
	else{
		//echo "<p>$error</p>";
		return false;
	}
}




function modify_entry ($entree, $branche, $attributs){
	global $ldap_server, $ldap_port, $dn;
	global $error;
	$error="";

	// Je ne suis pas sûr d'avoir bien saisi le fonctionnement de la fonction ldap_modify() de PHP
	// Du coup, je lui ai préféré les fonctions ldap_mod_add(), ldap_mod_del() et ldap_mod_replace() utilisées dans ma fonction modify_attribut()

	// Paramètres:
	/*
		$entree: uid=toto
		$branche: people, groups,... ou rights
		$attributs: tableau associatif des attributs
	*/

	$ds=@ldap_connect($ldap_server,$ldap_port);
	if($ds){
		//$r=@ldap_bind($ds);// Bind anonyme
		$adminLdap=get_infos_admin_ldap();
		$r=@ldap_bind($ds,$adminLdap["adminDn"],$adminLdap["adminPw"]);// Bind admin LDAP
		if($r){
			$result=ldap_modify($ds,"$entree,".$dn["$branche"],$attributs);
			if(!$result){
				$error="Echec d'ajout de l'entree $entree";
			}
			@ldap_free_result($result);
		}
		else{
			$error=gettext("Echec du bind anonyme");
		}
		@ldap_close($ds);
	}
	else{
		$error=gettext("Erreur de connection au serveur LDAP");
	}

	if($error==""){
		return true;
	}
	else{
		return false;
	}
}



function modify_attribut ($entree, $branche, $attributs, $mode){
	global $ldap_server, $ldap_port, $dn;
	global $error;
	$error="";

	// Paramètres:
	/*
		$entree: uid=toto
		$branche: people, groups,... ou rights
		$attribut: tableau associatif des attributs à modifier
		$mode: add replace ou del

		// Pour del aussi, il faut fournir la bonne valeur de l'attribut pour que cela fonctionne
		// On peut ajouter, modifier, supprimer plusieurs attributs à la fois.
	*/

	$ds=@ldap_connect($ldap_server,$ldap_port);
	if($ds){
		//$r=@ldap_bind($ds);// Bind anonyme
		$adminLdap=get_infos_admin_ldap();
		$r=@ldap_bind($ds,$adminLdap["adminDn"],$adminLdap["adminPw"]);// Bind admin LDAP
		if($r){
			switch($mode){
				case "add":
					$result=ldap_mod_add($ds,"$entree,".$dn["$branche"],$attributs);
					break;
				case "del":
					$result=ldap_mod_del($ds,"$entree,".$dn["$branche"],$attributs);
					break;
				case "replace":
					$result=ldap_mod_replace($ds,"$entree,".$dn["$branche"],$attributs);
					break;
			}
			if(!$result){
				$error="Echec d'ajout de la modification $mode sur $entree";
			}
			@ldap_free_result($result);
		}
		else{
			$error=gettext("Echec du bind anonyme");
		}
		@ldap_close($ds);
	}
	else{
		$error=gettext("Erreur de connection au serveur LDAP");
	}

	if($error==""){
		return true;
	}
	else{
		return false;
	}
}


/*
function crob_init() {
	// Récupération de variables dans la base MySQL se3db
	//global $domainsid,$uidPolicy;
        global $defaultgid,$domain,$defaultshell,$domainsid;

	$domainsid="";
	$sql="select value from params where name='domainsid';";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)==1){
		$lig_tmp=mysql_fetch_object($res);
		$domainsid=$lig_tmp->value;
	} else {
            // Cas d'un LCS ou sambaSID n'est pas dans la table params
            unset($retval);
            exec ("ldapsearch -x -LLL  objectClass=sambaDomain | grep sambaSID | cut -d ' ' -f 2",$retval);
            $domainsid = $retval[0];
            // Si il n'y a pas de sambaSID dans l'annuaire, on fixe une valeur factice
            // Il faudra appliquer un correct SID lors de l'installation d'un se3
            if (!isset($domainsid)) $domainsid ="S-0-0-00-0000000000-000000000-0000000000";
        }

	$uidPolicy="";
	$sql="select value from params where name='uidPolicy';";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)==1){
		$lig_tmp=mysql_fetch_object($res);
		$uidPolicy=$lig_tmp->value;
	}

	$defaultgid="";
	$sql="select value from params where name='defaultgid';";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)==1){
		$lig_tmp=mysql_fetch_object($res);
		$defaultgid=$lig_tmp->value;
	} else {
            // Cas d'un LCS ou defaultgid n'est pas dans la table params
            exec ("getent group lcs-users | cut -d ':' -f 3", $retval);
            $defaultgid= $retval[0];
        }

	$domain="";
	$sql="select value from params where name='domain';";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)==1){
		$lig_tmp=mysql_fetch_object($res);
		$domain=$lig_tmp->value;
	}

	$defaultshell="";
	$sql="select value from params where name='defaultshell';";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)==1){
		$lig_tmp=mysql_fetch_object($res);
		$defaultshell=$lig_tmp->value;
	}
}
*/


function fich_debug($texte){
	// Passer la variable ci-dessous à 1 pour activer l'écriture d'infos de débuggage dans /tmp/debug_se3lcs.txt
	// Il conviendra aussi d'ajouter des appels fich_debug($texte) là où vous en avez besoin;o).
	$debug=0;

	if($debug==1){
		$fich=fopen("/tmp/debug_se3lcs.txt","a+");
		fwrite($fich,$texte);
		fclose($fich);
	}
}


function creer_uid($nom,$prenom){
	global $uidPolicy;
	global $ldap_server, $ldap_port, $dn;
	global $error;
	$error="";

	fich_debug("======================\n");
	fich_debug("creer_uid:\n");
	fich_debug("\$nom=$nom\n");
	fich_debug("\$prenom=$prenom\n");

	fich_debug("\$uidPolicy=$uidPolicy\n");
	fich_debug("\$ldap_server=$ldap_server\n");
	fich_debug("\$ldap_port=$ldap_port\n");
	fich_debug("\$error=$error\n");
	fich_debug("\$dn=$dn\n");

/*
	# Il faudrait améliorer la fonction pour gérer les "Le goff Martin" qui devraient donner "Le_goff-Martin"
	# Actuellement, on passe tous les espaces à _
*/

	// Récupération de l'uidPolicy (et du sid)
	//crob_init(); Ne sert à rien !!!
	//echo "<p>\$uidPolicy=$uidPolicy</p>";

	// Filtrer certains caractères:
	//nom=$(echo "$nom" | tr " àâäéèêëîïôöùûü" "-aaaeeeeiioouuu" | sed -e "s/'//g")
	//$nom=strtolower(strtr("$nom"," 'àâäéèêëîïôöùûüçÇÂÄÊËÎÏÔÖÙÛÜ","__aaaeeeeiioouuucCAAEEIIOOUUU"));
	//$prenom=strtolower(strtr("$prenom"," 'àâäéèêëîïôöùûüçÇÂÄÊËÎÏÔÖÙÛÜ","__aaaeeeeiioouuucCAAEEIIOOUUU"));
	$nom=strtolower(strtr(ereg_replace("Æ","AE",ereg_replace("æ","ae",ereg_replace("¼","OE",ereg_replace("½","oe","$nom"))))," 'ÂÄÀÁÃÄÅÇÊËÈÉÎÏÌÍÑÔÖÒÓÕ¦ÛÜÙÚİ¾´áàâäãåçéèêëîïìíñôöğòóõ¨ûüùúıÿ¸","__AAAAAAACEEEEIIIINOOOOOSUUUUYYZaaaaaaceeeeiiiinoooooosuuuuyyz"));
	$prenom=strtolower(strtr(ereg_replace("Æ","AE",ereg_replace("æ","ae",ereg_replace("¼","OE",ereg_replace("½","oe","$prenom"))))," 'ÂÄÀÁÃÄÅÇÊËÈÉÎÏÌÍÑÔÖÒÓÕ¦ÛÜÙÚİ¾´áàâäãåçéèêëîïìíñôöğòóõ¨ûüùúıÿ¸","__AAAAAAACEEEEIIIINOOOOOSUUUUYYZaaaaaaceeeeiiiinoooooosuuuuyyz"));

	fich_debug("Après filtrage...\n");
	fich_debug("\$nom=$nom\n");
	fich_debug("\$prenom=$prenom\n");

	//ÂÄÀÁÃÄÅÇÊËÈÉÎÏÌÍÑÔÖÒÓÕ¦ÛÜÙÚİ¾´
	//AAAAAAACEEEEIIIINOOOOOSUUUUYYZ
	//áàâäãåçéèêëîïìíñôöğòóõ¨ûüùúıÿ¸
	//aaaaaaceeeeiiiinoooooosuuuuyyz

	/*
	# Valeurs de l'uidPolicy
	#	0: prenom.nom
	#	1: prenom.nom tronqué à 19
	#	2: pnom tronqué à 19
	#	3: pnom tronqué à 8
	#	4: nomp tronqué à 8
	#	5: nomprenom tronqué à 18
	*/

	switch($uidPolicy){
		case 0:
			$uid=$prenom.".".$nom;
			break;
		case 1:
			$uid=$prenom.".".$nom;
			$uid=substr($uid,0,19);
			break;
		case 2:
			$ini_prenom=substr($prenom,0,1);
			$uid=$ini_prenom.$nom;
			$uid=substr($uid,0,19);
			break;
		case 3:
			$ini_prenom=substr($prenom,0,1);
			$uid=$ini_prenom.$nom;
			$uid=substr($uid,0,8);
			break;
		case 4:
			$debut_nom=substr($nom,0,7);
			$ini_prenom=substr($prenom,0,1);
			$uid=$debut_nom.$ini_prenom;
			break;
		case 5:
			$uid=$nom.$prenom;
			$uid=substr($uid,0,18);
			break;
		default:
			$ERREUR="oui";
	}

	fich_debug("\$uid=$uid\n");
	fich_debug("\$ERREUR=$ERREUR\n");

	// Pour faire disparaitre les caractères spéciaux restants:
	$uid=ereg_replace("[^a-z_.-]","",$uid);

	fich_debug("Après filtrage...\n");
	fich_debug("\$uid=$uid\n");

	$test_caract1=substr($uid,0,1);
	if(strlen(ereg_replace("[a-z]","",$test_caract1))!=0){
		$error="Le premier caractère de l'uid n'est pas une lettre.";
	}
	else{
		// Début de l'uid... pour les doublons...
		$prefuid=substr($uid,0,strlen($uid)-1);
		$prefuid2=substr($uid,0,strlen($uid)-2);
		// Ou renseigner un uid_initial ou uid_souche
		$uid_souche=$uid;



		$ok_uid="non";

		$attr=array("uid");

		$ds=@ldap_connect($ldap_server,$ldap_port);
		if($ds){
			$r=@ldap_bind($ds);// Bind anonyme
			//$adminLdap=get_infos_admin_ldap();
			//$r=@ldap_bind($ds,$adminLdap["adminDn"],$adminLdap["adminPw"]);// Bind admin LDAP
			if($r){
				$cpt=2;
				//while($ok_uid=="non"){
				//while(($ok_uid=="non")&&($cpt<10)){
				while(($ok_uid=="non")&&($cpt<100)){
					$result=ldap_search($ds,$dn["people"],"uid=$uid*",$attr);
					if ($result) {
						$info=@ldap_get_entries($ds,$result);
						if($info){
							$ok_uid="oui";
							for($i=0;$i<$info["count"];$i++){
								//echo "<p>";
								// En principe, il n'y a qu'un uid par entrée...
								for($loop=0;$loop<$info[$i]["uid"]["count"]; $loop++) {
									//echo "\$info[$i][\"uid\"][$loop]=".$info[$i]["uid"][$loop]."<br />\n";
									if($info[$i]["uid"][$loop]==$uid){
										$ok_uid="non";
										//$uid=substr($uid,0,strlen($uid)-1).$cpt;
										//$uid=substr($uid,0,strlen($uid)-strlen($cpt)).$cpt;
										//$uid=$prefuid.$cpt;
										$uid=substr($uid_souche,0,strlen($uid_souche)-strlen($cpt)).$cpt;
										fich_debug("Doublons... \$uid=$uid\n");
										$cpt++;
									}
								}
								//echo "</p>\n";
							}
						}
					}
					else{
						$error="Echec de la lecture des entrées...";
						fich_debug("\$error=$error\n");
					}
					@ldap_free_result($result);
				}
			}
			else{
				$error=gettext("Echec du bind anonyme");
				fich_debug("\$error=$error\n");
			}
			@ldap_close($ds);
		}
		else{
			$error=gettext("Erreur de connection au serveur LDAP");
			fich_debug("\$error=$error\n");
		}
	}

	if($error!=""){
		echo "error=$error<br />\n";
		fich_debug("\$error=$error\n");
		return false;
	}
	//elseif($cpt>=10){
		//$error="Il y a au moins 10 uid en doublon...<br />On en est à $uid<br />Etes-vous sûr qu'il n'y a pas des personnes qui ont quitté l'établissement?";
	elseif($cpt>=100){
		$error="Il y a au moins 100 uid en doublon...<br />On en est à $uid<br />Etes-vous sûr qu'il n'y a pas des personnes qui ont quitté l'établissement?";
		echo "error=$error<br />\n";
		fich_debug("\$error=$error\n");
		return false;
	}
	else{
		// Retourner $uid
		return $uid;
	}
}



function verif_employeeNumber($employeeNumber){
	global $ldap_server, $ldap_port, $dn;
	global $error;
	$error="";
	// Tester si l'employeeNumber est dans l'annuaire ou non...

	//$attribut=array("uid","employeenumber");
	//$attribut=array("employeenumber");
	$attribut=array("uid");
	$tab=get_tab_attribut("people","employeenumber=$employeeNumber",$attribut);

	/*
	echo "count($tab)=".count($tab)."<br />\n";
	for($i=0;$i<count($tab);$i++){
		echo "tab[$i]=$tab[$i]<br />\n";
	}
	*/

	if(count($tab)>0){return $tab;}else{return false;}
}

function verif_nom_prenom_sans_employeeNumber($nom,$prenom){
	global $ldap_server, $ldap_port, $dn;
	global $error;
	$error="";
	// Tester si un uid existe ou non dans l'annuaire pour $nom et $prenom sans employeeNumber...
	// ... ce qui correspondrait à un compte créé à la main.

	$attribut=array("uid");
	$tab1=array();
	//$tab1=get_tab_attribut("people","cn='$prenom $nom'",$attribut);
 	$tab1=get_tab_attribut("people","cn=$prenom $nom",$attribut);

	//echo "<p>error=$error</p>";

	$trouve=0;
	if(count($tab1)>0){
		//echo "<p>count(\$tab1)>0</p>";
		for($i=0;$i<count($tab1);$i++){
			$attribut=array("employeenumber");
			$tab2=get_tab_attribut("people","uid=$tab1[$i]",$attribut);
			if(count($tab2)==0){
				//echo "<p>count(\$tab2)==0</p>";
				$trouve++;
				$uid=$tab1[$i];
				//echo "<p>uid=$uid</p>";
			}
		}

		// On ne cherche à traiter que le cas d'une seule correspondance.
		// S'il y en a plus, on ne pourra pas identifier...
		if($trouve==1){
			return $uid;
		}
		else{
			return false;
		}
	}
	else{
		return false;
	}
}


function get_tab_attribut($branche, $filtre, $attribut){
	global $ldap_server, $ldap_port, $dn;
	global $error;
	$error="";

	// Paramètres
	// $attribut doit être un tableau d'une seule valeur.
	// Ex.: $attribut[0]="uidNumber";

	// Tableau retourné
	$tab=array();

	fich_debug("======================\n");
	fich_debug("get_tab_attribut:\n");

	$ds=@ldap_connect($ldap_server,$ldap_port);
	if($ds){
		$r=@ldap_bind($ds);// Bind anonyme
		if($r){
			$result=ldap_search($ds,$dn[$branche],"$filtre",$attribut);
			fich_debug("ldap_search($ds,".$dn[$branche].",\"$filtre\",$attribut)\n");
			//echo "<p>ldap_search($ds,$dn[$branche],\"$filtre\",$attribut);</p>";
			if ($result){
				//echo "\$result=$result<br />";
				$info=@ldap_get_entries($ds,$result);
				if($info){
					fich_debug("\$info[\"count\"]=".$info["count"]."\n");
					//echo "<br />".$info["count"]."<br />";
					for($i=0;$i<$info["count"];$i++){
						fich_debug("\$info[$i][$attribut[0]][\"count\"]=".$info[$i][$attribut[0]]["count"]."\n");
						for($loop=0;$loop<$info[$i][$attribut[0]]["count"]; $loop++) {
							$tab[]=$info[$i][$attribut[0]][$loop];
							fich_debug("\$tab[]=".$info[$i][$attribut[0]][$loop]."\n");
						}
					}
					rsort($tab);
				}
				else{
					fich_debug("\$info vide... @ldap_get_entries($ds,$result) n'a rien donné.\n");
				}
			}
			else{
				$error="Echec de la lecture des entrées: ldap_search($ds,".$dn[$branche].",\"$filtre\",$attribut)";
				fich_debug("\$error=$error\n");
			}
			@ldap_free_result($result);

		}
		else{
			$error=gettext("Echec du bind anonyme");
			fich_debug("\$error=$error\n");
		}
		@ldap_close($ds);
	}
	else{
		$error=gettext("Erreur de connection au serveur LDAP");
		fich_debug("\$error=$error\n");
	}

	if($error!=""){
		echo "error=$error<br />\n";
	}

	return $tab;
}




function get_first_free_uidNumber(){
	global $ldap_server, $ldap_port, $dn;
	global $error;
	$error="";

	// On démarre les uid à 1001, mais admin est en 5000:
	// unattend est en 1000 chez moi... mais cela peut changer avec des établissements dont l'annuaire SE3 date d'avant l'ajout d'unattend
	$first_uidNumber=1000;
	$last_uidNumber=4999;
	//$last_uidNumber=1200;

	unset($attribut);
	$attribut=array();
	$attribut[0]="uidnumber";
	//$tab=array();
	//$tab=get_tab_attribut("people", "uid=*", $attribut);
	$tab1=array();
	$tab1=get_tab_attribut("people", "uid=*", $attribut);
	$tab2=array();
	$tab2=get_tab_attribut("trash", "uid=*", $attribut);
	$tab=array_merge($tab1,$tab2);
	rsort($tab);

	/*
	// Debug:
	echo "count(\$tab)=".count($tab)."<br />";
	for($i=0;$i<count($tab);$i++){
		echo "\$tab[$i]=$tab[$i]<br />";
	}
	*/

	/*
	// Méthode OK, mais on risque la pénurie des uidNumber entre 1000 et 5000
	// à ne pas récupérer des uidNumber d'utilisateurs qui ont quitté l'établissement
	//$last_uidNumber=1473;
	$uidNumber=$last_uidNumber;
	while((!in_array($uidNumber,$tab))&&($uidNumber>$first_uidNumber)){
		$uidNumber--;
		//echo "\$uidNumber=$uidNumber<br />";
	}
	$uidNumber++;
	if(($uidNumber>$last_uidNumber)||(in_array($uidNumber,$tab))){
		$error="Il n'y a plus de plus grand uidNumber libre en dessous de $last_uidNumber";
		echo "error=$error<br />";
		return false;
	}
	else{
		echo "<p><b>\$uidNumber=$uidNumber</b></p>";
		return $uidNumber;
	}
	*/


	//TEST: $last_uidNumber=1200;
	// Ou: on recherche le plus petit uidNumber dispo entre $first_uidNumber et $last_uidNumber
	$uidNumber=$first_uidNumber;
	while((in_array($uidNumber,$tab))&&($uidNumber<$last_uidNumber)){
		$uidNumber++;
	}
	//echo "<p><b>\$uidNumber=$uidNumber</b></p>";

	if(($uidNumber==$last_uidNumber)&&(in_array($uidNumber,$tab))){
		$error="Il n'y a plus d'uidNumber libre";
		//echo "error=$error<br />";
		return false;
	}
	else{
		return $uidNumber;
	}

	/*
	// Ou: On mixe les deux méthodes:
	// C'EST UNE FAUSSE SOLUTION:
	// Quand tout va être rempli la première fois, on va commencer à récupérer des uidNumber par le haut dès qu'un uidNumber va se libérer et on va ré-affecter des uidNumber utilisés récemment.
	$uidNumber=$last_uidNumber;
	while((!in_array($uidNumber,$tab))&&($uidNumber>$first_uidNumber)){
		$uidNumber--;
		//echo "\$uidNumber=$uidNumber<br />";
	}
	$uidNumber++;
	if(($uidNumber>$last_uidNumber)||(in_array($uidNumber,$tab))){
		// On commence à réaffecter des uidNumber libres par le bas
		$uidNumber=$first_uidNumber;
		while((in_array($uidNumber,$tab))&&($uidNumber<$last_uidNumber)){
			$uidNumber++;
		}

		if(($uidNumber==$last_uidNumber)&&(in_array($uidNumber,$tab))){
			$error="Il n'y a plus d'uidNumber libre";
			//echo "error=$error<br />";
			return false;
		}
		else{
			return $uidNumber;
		}
	}
	else{
		//echo "<p><b>\$uidNumber=$uidNumber</b></p>";
		return $uidNumber;
	}
	*/
}




function get_first_free_gidNumber(){
	global $ldap_server, $ldap_port, $dn;
	global $error;
	$error="";

	/*
	# Quelques groupes:
	# 5000:admins
	# 5001:Eleves
	# 5002:Profs
	# 5003:Administratifs
	# 1560:overfill
	# 1000:lcs-users
	# 998:machines
	*/

	$first_gidNumber=2000;
	$last_gidNumber=4999;
	//$last_gidNumber=2010;

	unset($attribut);
	$attribut=array();
	$attribut[0]="gidnumber";

	$tab1=array();
	$tab1=get_tab_attribut("people", "uid=*", $attribut);

	$tab=array();
	for($i=0;$i<count($tab1);$i++){
		//echo "\$tab1[$i]=$tab1[$i]<br />";
		$tab[]=$tab1[$i];
	}

	//echo "<hr />";

	$tab2=array();
	$tab2=get_tab_attribut("groups", "cn=*", $attribut);

	for($i=0;$i<count($tab2);$i++){
		//echo "\$tab2[$i]=$tab2[$i]<br />";
		if(!in_array($tab2[$i],$tab)){
			$tab[]=$tab2[$i];
		}
	}
	rsort($tab);

	/*
	// Debug:
	echo "count(\$tab)=".count($tab)."<br />";
	for($i=0;$i<count($tab);$i++){
		echo "\$tab[$i]=$tab[$i]<br />";
	}
	*/

	// On recherche le plus petit gidNumber dispo entre $first_gidNumber et $last_gidNumber
	$gidNumber=$first_gidNumber;
	while((in_array($gidNumber,$tab))&&($gidNumber<$last_gidNumber)){
		$gidNumber++;
	}
	//echo "<p><b>\$gidNumber=$gidNumber</b></p>";

	if(($gidNumber==$last_gidNumber)&&(in_array($gidNumber,$tab))){
		$error="Il n'y a plus de gidNumber libre";
		//echo "error=$error<br />";
		return false;
	}
	else{
		return $gidNumber;
	}
	// Pour contrôler:
	// ldapsearch -xLLL gidNumber | grep gidNumber | sed -e "s/^gidNumber: //" | sort -n -r | uniq | head
	// ldapsearch -xLLL gidNumber | grep gidNumber | sed -e "s/^gidNumber: //" | sort -n -r | uniq | tail
}

/*
function add_user($uid,$nom,$prenom,$sexe,$naissance,$password,$employeeNumber){
	// Récupérer le gidNumber par défaut -> lcs-users (1000) ou slis (600)
	global $defaultgid,$domain,$defaultshell,$domainsid,$uidPolicy;

	fich_debug("================\n");
	fich_debug("add_user:\n");
	fich_debug("\$defaultgid=$defaultgid\n");
	fich_debug("\$domain=$domain\n");
	fich_debug("\$defaultshell=$defaultshell\n");
	fich_debug("\$domainsid=$domainsid\n");
	fich_debug("\$uidPolicy=$uidPolicy\n");

	global $pathscripts;
	fich_debug("\$pathscripts=$pathscripts\n");


	// crob_init(); Ne sert a rien !!!!
	$nom=ereg_replace("[^a-z_-]","",strtolower(strtr(ereg_replace("Æ","AE",ereg_replace("æ","ae",ereg_replace("¼","OE",ereg_replace("½","oe","$nom"))))," 'ÂÄÀÁÃÄÅÇÊËÈÉÎÏÌÍÑÔÖÒÓÕ¦ÛÜÙÚİ¾´áàâäãåçéèêëîïìíñôöğòóõ¨ûüùúıÿ¸","__AAAAAAACEEEEIIIINOOOOOSUUUUYYZaaaaaaceeeeiiiinoooooosuuuuyyz")));
	$prenom=ereg_replace("[^a-z_-]","",strtolower(strtr(ereg_replace("Æ","AE",ereg_replace("æ","ae",ereg_replace("¼","OE",ereg_replace("½","oe","$prenom"))))," 'ÂÄÀÁÃÄÅÇÊËÈÉÎÏÌÍÑÔÖÒÓÕ¦ÛÜÙÚİ¾´áàâäãåçéèêëîïìíñôöğòóõ¨ûüùúıÿ¸","__AAAAAAACEEEEIIIINOOOOOSUUUUYYZaaaaaaceeeeiiiinoooooosuuuuyyz")));

	$nom=ucfirst(strtolower($nom));
	$prenom=ucfirst(strtolower($prenom));

	fich_debug("\$nom=$nom\n");
	fich_debug("\$prenom=$prenom\n");


	// Récupérer un uidNumber:
	//$uidNumber=get_first_free_uidNumber();
	if(!get_first_free_uidNumber()){return false;exit();}
	$uidNumber=get_first_free_uidNumber();
	$rid=2*$uidNumber+1000;
	$pgrid=2*$defaultgid+1001;

	fich_debug("\$uidNumber=$uidNumber\n");


	// Faut-il interdire les espaces dans le password? les apostrophes?
	// Comment le script ntlmpass.pl prend-il le paramètre? sans les apostrophes?

	$ntlmpass=explode(" ",exec("$pathscripts/ntlmpass.pl '$password'"));

	$sambaLMPassword=$ntlmpass[0];
	$sambaNTPassword=$ntlmpass[1];
	$userPassword=exec("$pathscripts/unixPassword.pl '$password'");

	$attribut=array();
	$attribut["uid"]="$uid";
	$attribut["cn"]="$prenom $nom";

	$attribut["givenName"]=strtolower($prenom).strtoupper(substr($nom,0,1));

	$attribut["sn"]="$nom";

	$attribut["mail"]="$uid@$domain";
	$attribut["objectClass"]="top";

	// Comme la clé est toujours objectClass, cela pose un problème: un seul attribut objectClass est ajouté (le dernier défini)
	//$attribut["objectClass"]="posixAccount";
	//$attribut["objectClass"]="shadowAccount";
	//$attribut["objectClass"]="person";
	//$attribut["objectClass"]="inetOrgPerson";
	//$attribut["objectClass"]="sambaSamAccount";

	$attribut["loginShell"]="$defaultshell";
	$attribut["uidNumber"]="$uidNumber";

	$attribut["gidNumber"]="$defaultgid";

	$attribut["homeDirectory"]="/home/$uid";
	$attribut["gecos"]="$prenom $nom,$naissance,$sexe,N";

	$attribut["sambaSID"]="$domainsid-$rid";
        $attribut["sambaPrimaryGroupSID"]="$domainsid-$pgrid";

	$attribut["sambaPwdMustChange"]="2147483647";
	$attribut["sambaAcctFlags"]="[U          ]";
	$attribut["sambaLMPassword"]="$sambaLMPassword";
	$attribut["sambaNTPassword"]="$sambaNTPassword";
	$attribut["userPassword"]="{crypt}$userPassword";

	// IL faut aussi l'employeeNumber
	if("$employeeNumber"!=""){
		$attribut["employeeNumber"]="$employeeNumber";
	}

	$result=add_entry("uid=$uid","people",$attribut);
	if($result){
		// Reste à ajouter les autres attributs objectClass
		unset($attribut);
		$attribut=array();
		$attribut["objectClass"]="posixAccount";
		if(modify_attribut("uid=$uid","people", $attribut, "add")){
			unset($attribut);
			$attribut=array();
			$attribut["objectClass"]="shadowAccount";
			if(modify_attribut("uid=$uid","people", $attribut, "add")){
				unset($attribut);
				$attribut=array();
				$attribut["objectClass"]="person";
				if(modify_attribut("uid=$uid","people", $attribut, "add")){
					unset($attribut);
					$attribut=array();
					$attribut["objectClass"]="inetOrgPerson";
					if(modify_attribut("uid=$uid","people", $attribut, "add")){
						unset($attribut);
						$attribut=array();
						$attribut["objectClass"]="sambaSamAccount";
						if(modify_attribut("uid=$uid","people", $attribut, "add"))  return true;
						else return false;
					} else return false;
				} else return false;
			} else return false;
		} else return false;
	} else return false;
}
*/

function add_user($uid,$nom,$prenom,$sexe,$naissance,$password,$employeeNumber){
	// Récupérer le gidNumber par défaut -> lcs-users (1000) ou slis (600)
	global $defaultgid,$domain,$defaultshell,$domainsid,$uidPolicy;

	fich_debug("================\n");
	fich_debug("add_user:\n");
	fich_debug("\$defaultgid=$defaultgid\n");
	fich_debug("\$domain=$domain\n");
	fich_debug("\$defaultshell=$defaultshell\n");
	fich_debug("\$domainsid=$domainsid\n");
	fich_debug("\$uidPolicy=$uidPolicy\n");

	global $pathscripts;
	fich_debug("\$pathscripts=$pathscripts\n");


	// crob_init(); Ne sert a rien !!!!
	$nom=ereg_replace("[^a-z_-]","",strtolower(strtr(ereg_replace("Æ","AE",ereg_replace("æ","ae",ereg_replace("¼","OE",ereg_replace("½","oe","$nom"))))," 'ÂÄÀÁÃÄÅÇÊËÈÉÎÏÌÍÑÔÖÒÓÕ¦ÛÜÙÚİ¾´áàâäãåçéèêëîïìíñôöğòóõ¨ûüùúıÿ¸","__AAAAAAACEEEEIIIINOOOOOSUUUUYYZaaaaaaceeeeiiiinoooooosuuuuyyz")));
	$prenom=ereg_replace("[^a-z_-]","",strtolower(strtr(ereg_replace("Æ","AE",ereg_replace("æ","ae",ereg_replace("¼","OE",ereg_replace("½","oe","$prenom"))))," 'ÂÄÀÁÃÄÅÇÊËÈÉÎÏÌÍÑÔÖÒÓÕ¦ÛÜÙÚİ¾´áàâäãåçéèêëîïìíñôöğòóõ¨ûüùúıÿ¸","__AAAAAAACEEEEIIIINOOOOOSUUUUYYZaaaaaaceeeeiiiinoooooosuuuuyyz")));

	$nom=ucfirst(strtolower($nom));
	$prenom=ucfirst(strtolower($prenom));

	fich_debug("\$nom=$nom\n");
	fich_debug("\$prenom=$prenom\n");


	// Récupérer un uidNumber:
	//$uidNumber=get_first_free_uidNumber();
	if(!get_first_free_uidNumber()){return false;exit();}
	$uidNumber=get_first_free_uidNumber();
	$rid=2*$uidNumber+1000;
	$pgrid=2*$defaultgid+1001;

	fich_debug("\$uidNumber=$uidNumber\n");


	// Faut-il interdire les espaces dans le password? les apostrophes?
	// Comment le script ntlmpass.pl prend-il le paramètre? sans les apostrophes?

	$ntlmpass=explode(" ",exec("$pathscripts/ntlmpass.pl '$password'"));

	$sambaLMPassword=$ntlmpass[0];
	$sambaNTPassword=$ntlmpass[1];
	$userPassword=exec("$pathscripts/unixPassword.pl '$password'");

	$attribut=array();
	$attribut["uid"]="$uid";
	$attribut["cn"]="$prenom $nom";

	$attribut["givenName"]=strtolower($prenom).strtoupper(substr($nom,0,1));

	$attribut["sn"]="$nom";

	$attribut["mail"]="$uid@$domain";
	//$attribut["objectClass"]="top";
	/*
	// Comme la clé est toujours objectClass, cela pose un problème: un seul attribut objectClass est ajouté (le dernier défini)
	$attribut["objectClass"]="posixAccount";
	$attribut["objectClass"]="shadowAccount";
	$attribut["objectClass"]="person";
	$attribut["objectClass"]="inetOrgPerson";
	$attribut["objectClass"]="sambaSamAccount";
	*/
	$attribut["objectClass"][0]="top";
	$attribut["objectClass"][1]="posixAccount";
	$attribut["objectClass"][2]="shadowAccount";
	$attribut["objectClass"][3]="person";
	$attribut["objectClass"][4]="inetOrgPerson";
	$attribut["objectClass"][5]="sambaSamAccount";

	$attribut["loginShell"]="$defaultshell";
	$attribut["uidNumber"]="$uidNumber";

	$attribut["gidNumber"]="$defaultgid";

	$attribut["homeDirectory"]="/home/$uid";
	$attribut["gecos"]="$prenom $nom,$naissance,$sexe,N";

	$attribut["sambaSID"]="$domainsid-$rid";
        $attribut["sambaPrimaryGroupSID"]="$domainsid-$pgrid";

	$attribut["sambaPwdMustChange"]="2147483647";
	$attribut["sambaAcctFlags"]="[U          ]";
	$attribut["sambaLMPassword"]="$sambaLMPassword";
	$attribut["sambaNTPassword"]="$sambaNTPassword";
	$attribut["userPassword"]="{crypt}$userPassword";

	// IL faut aussi l'employeeNumber
	if("$employeeNumber"!=""){
		$attribut["employeeNumber"]="$employeeNumber";
	}

	$result=add_entry("uid=$uid","people",$attribut);

	if($result){
		/*
		// Reste à ajouter les autres attributs objectClass
		unset($attribut);
		$attribut=array();
		$attribut["objectClass"]="posixAccount";
		if(modify_attribut("uid=$uid","people", $attribut, "add")){
			unset($attribut);
			$attribut=array();
			$attribut["objectClass"]="shadowAccount";
			if(modify_attribut("uid=$uid","people", $attribut, "add")){
				unset($attribut);
				$attribut=array();
				$attribut["objectClass"]="person";
				if(modify_attribut("uid=$uid","people", $attribut, "add")){
					unset($attribut);
					$attribut=array();
					$attribut["objectClass"]="inetOrgPerson";
					if(modify_attribut("uid=$uid","people", $attribut, "add")){
						unset($attribut);
						$attribut=array();
						$attribut["objectClass"]="sambaSamAccount";
						if(modify_attribut("uid=$uid","people", $attribut, "add"))  return true;
						else return false;
					} else return false;
				} else return false;
			} else return false;
		} else return false;
		*/
		return true;
	} else return false;
}


?>
