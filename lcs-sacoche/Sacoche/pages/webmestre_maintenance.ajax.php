<?php
/**
 * @version $Id$
 * @author Thomas Crespin <thomas.crespin@sesamath.net>
 * @copyright Thomas Crespin 2010
 * 
 * ****************************************************************************************************
 * SACoche <http://sacoche.sesamath.net> - Suivi d'Acquisitions de Compétences
 * © Thomas Crespin pour Sésamath <http://www.sesamath.net> - Tous droits réservés.
 * Logiciel placé sous la licence libre GPL 3 <http://www.rodage.org/gpl-3.0.fr.html>.
 * ****************************************************************************************************
 * 
 * Ce fichier est une partie de SACoche.
 * 
 * SACoche est un logiciel libre ; vous pouvez le redistribuer ou le modifier suivant les termes 
 * de la “GNU General Public License” telle que publiée par la Free Software Foundation :
 * soit la version 3 de cette licence, soit (à votre gré) toute version ultérieure.
 * 
 * SACoche est distribué dans l’espoir qu’il vous sera utile, mais SANS AUCUNE GARANTIE :
 * sans même la garantie implicite de COMMERCIALISABILITÉ ni d’ADÉQUATION À UN OBJECTIF PARTICULIER.
 * Consultez la Licence Générale Publique GNU pour plus de détails.
 * 
 * Vous devriez avoir reçu une copie de la Licence Générale Publique GNU avec SACoche ;
 * si ce n’est pas le cas, consultez : <http://www.gnu.org/licenses/>.
 * 
 */

if(!defined('SACoche')) {exit('Ce fichier ne peut être appelé directement !');}
if($_SESSION['SESAMATH_ID']==ID_DEMO) {exit('Action désactivée pour la démo...');}

$action = (isset($_POST['f_action'])) ? clean_texte($_POST['f_action']) : '';
$motif  = (isset($_POST['f_motif']))  ? clean_texte($_POST['f_motif'])  : '';

//	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*
// Bloquer ou débloquer l'application
//	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*

if($action=='debloquer')
{
	ajouter_log_PHP( $log_objet='Maintenance' , $log_contenu='Application accessible.' , $log_fichier=__FILE__ , $log_ligne=__LINE__ , $only_sesamath=false );
	debloquer_application($_SESSION['USER_PROFIL'],'0');
	exit('<label class="valide">Application accessible.</label>');
}

if($action=='bloquer')
{
	ajouter_log_PHP( $log_objet='Maintenance' , $log_contenu='Application fermée.' , $log_fichier=__FILE__ , $log_ligne=__LINE__ , $only_sesamath=false );
	bloquer_application($_SESSION['USER_PROFIL'],'0',$motif);
	exit('<label class="erreur">Application fermée : '.html($motif).'</label>');
}

//	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*
// Mise à jour automatique des fichiers
//	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*

$dossier_import  = './__tmp/import';
$fichier_import  = $dossier_import.'/telechargement.zip';
$dossier_dezip   = $dossier_import.'/SACoche';
$dossier_install = '.';

//
// 1. Récupération de l'archive <em>ZIP</em>...
//
if($action=='maj_etape1')
{
	$contenu_zip = url_get_contents(SERVEUR_TELECHARGEMENT,$tab_post=false,$timeout=29);
	if(substr($contenu_zip,0,6)=='Erreur')
	{
		exit(']¤['.'pb'.']¤['.$contenu_zip);
	}
	Ecrire_Fichier($fichier_import,$contenu_zip);
	exit(']¤['.'ok'.']¤['."Decompression de l'archive&hellip;");
}

//
// 2. Decompression de l'archive...
//
if($action=='maj_etape2')
{
	if(is_dir($dossier_dezip))
	{
		Supprimer_Dossier($dossier_dezip);
	}
	// Dezipper dans le dossier temporaire
	$code_erreur = unzip( $fichier_import , $dossier_import , TRUE /*use_ZipArchive*/ );
	if($code_erreur)
	{
		require('./_inc/tableau_zip_error.php');
		exit(']¤['.'pb'.']¤['.'Fichiers impossibles à extraire ('.$code_erreur.$tab_zip_error[$code_erreur].') !');
	}
	exit(']¤['.'ok'.']¤['."Analyse des fichiers et recensement des dossiers&hellip;");
}

//
// 3. Analyse des fichiers et recensement des dossiers... (après initialisation de la session temporaire)
//
if($action=='maj_etape3')
{
	$_SESSION['tmp'] = array();
	Analyser_Dossier( $dossier_install , strlen($dossier_install) , 'avant' );
	Analyser_Dossier( $dossier_dezip   , strlen($dossier_dezip)   , 'apres' );
	exit(']¤['.'ok'.']¤['."Analyse et répercussion des modifications&hellip;");
}

//
// 4. Analyse et répercussion des modifications... (tout en bloquant l'appli)
//
if($action=='maj_etape4')
{
	$thead = '<tr><td colspan="2">Mise à jour automatique du '.date('d/m/Y H:i:s').'</td></tr>';
	$tbody = '';
	// Bloquer l'application
	ajouter_log_PHP( $log_objet='Mise à jour des fichiers' , $log_contenu='Application fermée.' , $log_fichier=__FILE__ , $log_ligne=__LINE__ , $only_sesamath=false );
	bloquer_application($_SESSION['USER_PROFIL'],'0','Mise à jour des fichiers en cours.');
	// Dossiers : ordre croissant pour commencer par ceux les moins imbriqués : obligatoire pour l'ajout, et pour la suppression on teste si pas déjà supprimé.
	ksort($_SESSION['tmp']['dossier']);
	foreach($_SESSION['tmp']['dossier'] as $dossier => $tab)
	{
		if( (isset($tab['avant'])) && (isset($tab['apres'])) )
		{
			// Dossier inchangé (cas le plus fréquent donc testé en premier).
		}
		elseif(!isset($tab['avant']))
		{
			// Dossier à ajouter
			$tbody .= '<tr><td class="v">Dossier ajouté</td><td>'.$dossier.'</td></tr>';
			if( !Creer_Dossier($dossier_install.$dossier) )
			{
				ajouter_log_PHP( $log_objet='Mise à jour des fichiers' , $log_contenu='Application accessible.' , $log_fichier=__FILE__ , $log_ligne=__LINE__ , $only_sesamath=false );
				debloquer_application($_SESSION['USER_PROFIL'],'0');
				exit(']¤['.'pb'.']¤['."Dossier ".$dossier." non créé ou inaccessible en écriture !");
			}
		}
		elseif(!isset($tab['apres'])) // (forcément)
		{
			// Dossier à supprimer
			$tbody .= '<tr><td class="r">Dossier supprimé</td><td>'.$dossier.'</td></tr>';
			if(is_dir($dossier_install.$dossier))
			{
				Supprimer_Dossier($dossier_install.$dossier);
			}
		}
	}
	// Fichiers : ordre décroissant pour avoir VERSION.txt en dernier (majuscules avant dans la table ASCII).
	krsort($_SESSION['tmp']['fichier']);
	foreach($_SESSION['tmp']['fichier'] as $fichier => $tab)
	{
		if( (isset($tab['avant'])) && (isset($tab['apres'])) )
		{
			if( ($tab['avant']!=$tab['apres']) && ($fichier!='/.htaccess') )
			{
				// Fichier changé => maj (si le .htaccess a été changé, c'est sans doute volontaire, ne pas y toucher)
				if( !copy( $dossier_dezip.$fichier , $dossier_install.$fichier ) )
				{
					ajouter_log_PHP( $log_objet='Mise à jour des fichiers' , $log_contenu='Application accessible.' , $log_fichier=__FILE__ , $log_ligne=__LINE__ , $only_sesamath=false );
					debloquer_application($_SESSION['USER_PROFIL'],'0');
					exit(']¤['.'pb'.']¤['."Erreur lors de l'écriture du fichier ".$fichier." !");
				}
				$tbody .= '<tr><td class="b">Fichier modifié</td><td>'.$fichier.'</td></tr>';
			}
		}
		elseif( (!isset($tab['avant'])) && ($fichier!='/.htaccess') )
		{
			// Fichier à ajouter (si le .htaccess n'y est pas, c'est sans doute volontaire, ne pas l'y remettre)
			if( !copy( $dossier_dezip.$fichier , $dossier_install.$fichier ) )
			{
				ajouter_log_PHP( $log_objet='Mise à jour des fichiers' , $log_contenu='Application accessible.' , $log_fichier=__FILE__ , $log_ligne=__LINE__ , $only_sesamath=false );
				debloquer_application($_SESSION['USER_PROFIL'],'0');
				exit(']¤['.'pb'.']¤['."Erreur lors de l'écriture du fichier ".$fichier." !");
			}
			$tbody .= '<tr><td class="v">Fichier ajouté</td><td>'.$fichier.'</td></tr>';
		}
		elseif(!isset($tab['apres'])) // (forcément)
		{
			// Fichier à supprimer
			if(is_file($dossier_install.$fichier))
			{
				unlink($dossier_install.$fichier);
			}
			$tbody .= '<tr><td class="r">Fichier supprimé</td><td>'.$fichier.'</td></tr>';
		}
	}
	// Débloquer l'application
	ajouter_log_PHP( $log_objet='Mise à jour des fichiers' , $log_contenu='Application accessible.' , $log_fichier=__FILE__ , $log_ligne=__LINE__ , $only_sesamath=false );
	debloquer_application($_SESSION['USER_PROFIL'],'0');
	// Enregistrement du rapport
	$fichier_chemin  = './__tmp/export/rapport_maj.html';
	$fichier_contenu = '<!DOCTYPE html><html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><style type="text/css">body{font-family:monospace;font-size:8pt}table{border-collapse:collapse}thead{background:#CCC;font-weight:bold;text-align:center}td{border:solid 1px;padding:2px;white-space:nowrap}.v{color:green}.r{color:red}.b{color:blue}</style></head><body><table><thead>'.$thead.'</thead><tbody>'.$tbody.'</tbody></table></body></html>';
	Ecrire_Fichier($fichier_chemin,$fichier_contenu);
	exit(']¤['.'ok'.']¤['.'Rapport des modifications apportées et nettoyage&hellip;');
}

//
// 5. Nettoyage...
//
if($action=='maj_etape5')
{
	unset($_SESSION['tmp']);
	Supprimer_Dossier($dossier_dezip);
	exit(']¤['.'ok'.']¤['.VERSION_PROG);
}

//	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*
// Vérification des fichiers
//	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*

$dossier_import  = './__tmp/import';
$fichier_import  = $dossier_import.'/verification.zip';
$dossier_dezip   = $dossier_import.'/SACoche';
$dossier_install = '.';

//
// 1. Récupération de l'archive <em>ZIP</em>...
//
if($action=='verif_etape1')
{
	$tab_post = array();
	$tab_post['verification'] = 1;
	$tab_post['version'] = VERSION_PROG;
	$contenu_zip = url_get_contents(SERVEUR_TELECHARGEMENT,$tab_post,$timeout=29);
	if(substr($contenu_zip,0,6)=='Erreur')
	{
		exit(']¤['.'pb'.']¤['.$contenu_zip);
	}
	Ecrire_Fichier($fichier_import,$contenu_zip);
	exit(']¤['.'ok'.']¤['."Decompression de l'archive&hellip;");
}

//
// 2. Decompression de l'archive...
//
if($action=='verif_etape2')
{
	if(is_dir($dossier_dezip))
	{
		Supprimer_Dossier($dossier_dezip);
	}
	// Dezipper dans le dossier temporaire
	$code_erreur = unzip( $fichier_import , $dossier_import , TRUE /*use_ZipArchive*/ );
	if($code_erreur)
	{
		require('./_inc/tableau_zip_error.php');
		exit(']¤['.'pb'.']¤['.'Fichiers impossibles à extraire ('.$code_erreur.$tab_zip_error[$code_erreur].') !');
	}
	exit(']¤['.'ok'.']¤['."Analyse des fichiers et recensement des dossiers&hellip;");
}

//
// 3. Analyse des fichiers et recensement des dossiers... (après initialisation de la session temporaire)
//
if($action=='verif_etape3')
{
	$_SESSION['tmp'] = array();
	Analyser_Dossier( $dossier_install , strlen($dossier_install) , 'avant' );
	Analyser_Dossier( $dossier_dezip   , strlen($dossier_dezip)   , 'apres' , FALSE );
	exit(']¤['.'ok'.']¤['."Comparaison des données&hellip;");
}

//
// 4. Comparaison des données...
//
if($action=='verif_etape4')
{
	$thead = '<tr><td colspan="2">Vérification du '.date('d/m/Y H:i:s').'</td></tr>';
	$tbody_ok = '';
	$tbody_pb = '';
	// Dossiers : ordre croissant pour commencer par ceux les moins imbriqués : obligatoire pour l'ajout, et pour la suppression on teste si pas déjà supprimé.
	ksort($_SESSION['tmp']['dossier']);
	foreach($_SESSION['tmp']['dossier'] as $dossier => $tab)
	{
		if( (isset($tab['avant'])) && (isset($tab['apres'])) )
		{
			// Dossier inchangé (cas le plus fréquent donc testé en premier).
			$tbody_ok .= '<tr><td class="v">Dossier présent</td><td>'.$dossier.'</td></tr>';
		}
		elseif(!isset($tab['avant']))
		{
			// Dossier manquant
			$tbody_pb .= '<tr><td class="r">Dossier manquant</td><td>'.$dossier.'</td></tr>';
		}
		elseif(!isset($tab['apres'])) // (forcément)
		{
			// Dossier en trop
			$tbody_pb .= '<tr><td class="r">Dossier en trop</td><td>'.$dossier.'</td></tr>';
		}
	}
	// Fichiers : ordre décroissant pour avoir VERSION.txt en dernier (majuscules avant dans la table ASCII).
	krsort($_SESSION['tmp']['fichier']);
	foreach($_SESSION['tmp']['fichier'] as $fichier => $tab)
	{
		if( (isset($tab['avant'])) && (isset($tab['apres'])) )
		{
			if( ($tab['avant']==$tab['apres']) || ($fichier=='/.htaccess') )
			{
				// Fichier identique (si le .htaccess a été changé, c'est sans doute volontaire, ne pas y toucher)
				$tbody_ok .= '<tr><td class="v">Fichier identique</td><td>'.$fichier.'</td></tr>';
			}
			else
			{
				// Fichier différent
				$tbody_pb .= '<tr><td class="r">Fichier différent</td><td>'.$fichier.'</td></tr>';
			}
		}
		elseif( (!isset($tab['avant'])) && ($fichier!='/.htaccess') )
		{
			// Fichier manquant
			$tbody_pb .= '<tr><td class="r">Fichier manquant</td><td>'.$fichier.'</td></tr>';
		}
		elseif(!isset($tab['apres'])) // (forcément)
		{
			$tbody_pb .= '<tr><td class="r">Fichier en trop</td><td>'.$fichier.'</td></tr>';
		}
	}
	// Enregistrement du rapport
	$fichier_chemin  = './__tmp/export/rapport_verif.html';
	$fichier_contenu = '<!DOCTYPE html><html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><style type="text/css">body{font-family:monospace;font-size:8pt}table{border-collapse:collapse}thead{background:#CCC;font-weight:bold;text-align:center}td{border:solid 1px;padding:2px;white-space:nowrap}.v{color:green}.r{color:red}.b{color:blue}</style></head><body><table><thead>'.$thead.'</thead><tbody>'.$tbody_pb.$tbody_ok.'</tbody></table></body></html>';
	Ecrire_Fichier($fichier_chemin,$fichier_contenu);
	exit(']¤['.'ok'.']¤['.'Rapport des modifications apportées et nettoyage&hellip;');
}

//
// 5. Nettoyage...
//
if($action=='verif_etape5')
{
	unset($_SESSION['tmp']);
	Supprimer_Dossier($dossier_dezip);
	exit(']¤['.'ok'.']¤['.VERSION_PROG);
}

//	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*
// On ne devrait pas en arriver là...
//	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*

exit('Erreur avec les données transmises !');

?>
