<?php
include_once 'includes/init.php';
include_once 'includes/xcal.php';
include_once 'lcs_functions.php';
$message="<H3> <u>Compte-rendu d'importation du " .date ("j-m-Y") ." &#224; ". date("H:i:s")."</u></H3>";
$my_errormsg="";
$messdebug="";
function ecrit_fichier() {
global $message;
$nom_file="./includes/lcs_cr_import.html";
if ($fichier=fopen($nom_file,"w+")) {
	fputs($fichier, $message);
	fclose($fichier);
	}
else {
	echo "Erreur fichier compte-rendu";
	exit;
	}
}
ecrit_fichier();
set_time_limit(0);

//=======
 
//========================
function unzip($file, $path='', $effacer_zip=false)
{/*Méthode qui permet de décompresser un fichier zip $file dans un répertoire de destination $path
  et qui retourne un tableau contenant la liste des fichiers extraits
  Si $effacer_zip est égal à true, on efface le fichier zip d'origine $file*/
	
	$tab_liste_fichiers = array(); //Initialisation

	$zip = zip_open($file);

	if ($zip)
	{
		while ($zip_entry = zip_read($zip)) //Pour chaque fichier contenu dans le fichier zip
		{
			if (zip_entry_filesize($zip_entry) > 0)
			{
				$complete_path = $path.dirname(zip_entry_name($zip_entry));

				/*On supprime les éventuels caractères spéciaux et majuscules*/
				$nom_fichier = zip_entry_name($zip_entry);
				$nom_fichier = strtr($nom_fichier,"ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ","AAAAAAaaaaaaOOOOOOooooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn");
				$nom_fichier = strtolower($nom_fichier);
				$nom_fichier = ereg_replace("'|[[:blank:]]",'_',$nom_fichier);

				/*On ajoute le nom du fichier dans le tableau*/
				array_push($tab_liste_fichiers,$nom_fichier);

				$complete_name = $path.$nom_fichier; //Nom et chemin de destination

				if(!file_exists($complete_path))
				{
					$tmp = '';
					foreach(explode('/',$complete_path) AS $k)
					{
						$tmp .= $k.'/';

						if(!file_exists($tmp))
						{ mkdir($tmp, 0755); }
					}
				}

				/*On extrait le fichier*/
				if (zip_entry_open($zip, $zip_entry, "r"))
				{
					$fd = fopen($complete_name, 'w');

					fwrite($fd, zip_entry_read($zip_entry, zip_entry_filesize($zip_entry)));

					fclose($fd);
					zip_entry_close($zip_entry);
				}
			}
		}

		zip_close($zip);

		/*On efface éventuellement le fichier zip d'origine*/
		if ($effacer_zip == true)
		unlink($file);
	}

	return $tab_liste_fichiers;
}
//========================
 $userlist = user_get_users ();
//========================
function import_edt($file) {
global  $count_con, $count_suc, $error_num, $numDeleted,$ALLOW_CONFLICT,$message; 
//if ( $file['size'] > 0 ) {
$data=array();
$doOverwrite = 'true';
$data = parse_ical ( "/tmp/arch_agend/".$file );
$type = 'ical';

if ( ! empty ( $data )  ) {
  	$message.= date("H:i:s")." - <b>Traitement du fichier ".$file."</b><br/>";
  	ecrit_fichier();
    lcs_import_data ( $data, true, $type );
    $message.= '  ' . translate ( 'Events successfully imported' ) . ': ' . $count_suc
     . '<br /><br /> '
    /*. translate ( 'Events from prior import marked as deleted' ) . ': '
     . $numDeleted . '<br />
    ' . ( empty ( $ALLOW_CONFLICTS )
      ? translate ( 'Conflicting events' ) . ': ' . $count_con . '<br />
    ' : '' ) . translate ( 'Errors' ) . ': ' . $error_num . '<br /><br />'*/
    ;
   
    	
  }
ecrit_fichier();
}
//========================
if(isset($_REQUEST['test']))
{	

if ($_REQUEST['test']==7)
	{
	$liste = array();
	$liste = unzip('/tmp/archi.zip','/tmp/arch_agend/','true');
	$message.= 'Le fichier zip contenait '.count($liste).' fichier(s) :<br /><br />';
	ecrit_fichier();
	foreach ($liste as $nom_fichier)
		{
		$nom_users=explode('.',$nom_fichier);
  		$nom_user=$nom_users[0];
  		$pb=true;
  		for ( $i = 0, $cnt = count ( $userlist ); $i < $cnt; $i++ ) {
			if ( strtolower($userlist[$i]['cal_lastname'].'_'.$userlist[$i]['cal_firstname']) == $nom_user) {
				$calUser=$userlist[$i]['cal_login'];
				$login=$calUser;
				$pb=false;
				import_edt($nom_fichier);
				
				break;
				
			}
			 
   		}
   		unlink('/tmp/arch_agend/'.$nom_fichier);
   		if ($pb) $my_errormsg.= "- Pas de correspondance pour le fichier <b>".$nom_fichier."</b><br />";
	}
	$message.= "<b><u>Fin de l'importation</u> : ".date("H:i:s")."</b>";
	if ($my_errormsg!="") $message.= "<p> <b>Erreurs </b>: </p><p>" . $my_errormsg."</p>";
	if ($messdebug !="") $message.= "Debug <p>".$messdebug. "</p>";
	ecrit_fichier();
	
}    
else echo "Failed"; 
}

?>